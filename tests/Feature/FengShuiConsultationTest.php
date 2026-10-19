<?php

namespace Tests\Feature;

use Botble\Base\Facades\EmailHandler;
use Botble\Contact\Events\SentContactEvent;
use Botble\Contact\Listeners\SendContactEmailListener;
use Botble\Contact\Models\Contact;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class FengShuiConsultationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Theme::uses('martfury');
        config(['database.connections.consultation_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::setDefaultConnection('consultation_test');

        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('unread');
            $table->text('custom_fields')->nullable();
            $table->timestamps();
        });
        Schema::create('contact_custom_fields', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('type')->default('text');
            $table->string('status')->default('published');
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
        });
        Event::fake([SentContactEvent::class]);
    }

    protected function tearDown(): void
    {
        DB::purge('consultation_test');
        parent::tearDown();
    }

    public function test_consultation_submission_saves_all_details_for_admin(): void
    {
        $data = [
            ...$this->validData(),
            'email' => 'giachu@example.com',
            'content' => 'Tôi cần tư vấn hướng nhà và bố trí bàn làm việc.',
            'birth_time' => '06:45 sáng (Giờ Mão)',
            'lunar_birth_year' => 'Đinh Mão 1987',
            'status' => 'read',
            'custom_fields' => ['Injected' => 'untrusted'],
            'contact_custom_fields' => ['9999' => 'untrusted'],
        ];

        $this->postJson('/contact/consultation', $data)->assertOk()->assertJsonPath('error', false);
        $contact = Contact::query()->sole();
        $this->assertSame($data['content'], $contact->content);
        $this->assertSame($data['email'], $contact->email);
        $this->assertSame('unread', $contact->status->getValue());
        $this->assertSame('Đặt lịch tư vấn: Phong Thủy Nhà Ở', $contact->subject);
        $this->assertSame([
            'Ngày sinh (Dương lịch)' => '12/05/1987',
            'Giờ sinh' => $data['birth_time'],
            'Năm sinh âm lịch' => $data['lunar_birth_year'],
            'Hạng mục tư vấn' => 'Phong Thủy Nhà Ở',
            'Hình thức tham vấn' => 'Luận Giải Online',
        ], $contact->custom_fields);
        Event::assertDispatched(SentContactEvent::class);
    }

    public function test_email_notes_and_birth_time_are_optional(): void
    {
        $this->postJson('/contact/consultation', $this->validData())
            ->assertOk()->assertJsonPath('error', false);

        $contact = Contact::query()->sole();
        $this->assertSame('', $contact->email);
        $this->assertSame('Đăng ký đặt lịch tư vấn phong thủy.', $contact->content);
        $this->assertSame('', $contact->custom_fields['Giờ sinh']);
    }

    public function test_invalid_dates_choices_phone_and_consent_are_rejected(): void
    {
        $this->postJson('/contact/consultation', [
            ...$this->validData(),
            'name' => '',
            'phone' => 'abc',
            'birth_date' => '2099-01-01',
            'email' => 'not-an-email',
            'specialty' => 'unlisted',
            'consultation_method' => 'unlisted',
            'agree_terms_and_policy' => '0',
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'name', 'phone', 'birth_date', 'email', 'specialty', 'consultation_method', 'agree_terms_and_policy',
        ]);
        $this->assertSame(0, Contact::query()->count());
    }

    public function test_normal_contact_form_keeps_existing_validation(): void
    {
        $this->postJson('/contact/send', ['name' => 'Nguyễn Văn Khang'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'content']);
    }

    public function test_new_shortcode_style_renders_form_without_changing_default(): void
    {
        $html = (string) shortcode()->compile('[contact-form style="consultation"][/contact-form]', true);
        $this->assertStringContainsString('feng-shui-contact__surface', $html);
        $this->assertStringContainsString('/contact/consultation', $html);
        $this->assertStringContainsString('name="birth_date"', $html);
        $this->assertSame(3, substr_count($html, 'class="feng-shui-contact__method"'));
        $this->assertStringNotContainsString('Thiên Thành', $html);
        $this->assertStringNotContainsString('120 phút', $html);

        $default = (string) shortcode()->compile('[contact-form][/contact-form]', true);
        $this->assertStringContainsString('ps-contact-form', $default);
        $this->assertStringNotContainsString('feng-shui-contact__surface', $default);
    }

    public function test_confirmation_is_not_sent_without_sender_email(): void
    {
        $contact = new Contact(['name' => 'Nguyễn Văn Khang', 'email' => '']);
        EmailHandler::shouldReceive('setModule')->once()->andReturnSelf();
        EmailHandler::shouldReceive('setVariableValues')->once()->andReturnSelf();
        EmailHandler::shouldReceive('sendUsingTemplate')->once()->with('notice', \Mockery::any(), [])->andReturn(true);

        (new SendContactEmailListener)->handle(new SentContactEvent($contact));
        $this->assertTrue(true);
    }

    private function validData(): array
    {
        return [
            'name' => 'Nguyễn Văn Khang',
            'phone' => '0912345678',
            'birth_date' => '1987-05-12',
            'specialty' => 'home',
            'consultation_method' => 'online',
            'agree_terms_and_policy' => '1',
        ];
    }
}
