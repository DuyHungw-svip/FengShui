<?php

namespace Theme\Martfury\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\PhoneNumberRule;
use Botble\Captcha\Facades\Captcha;
use Botble\Contact\Forms\Fronts\ContactForm;
use Botble\Contact\Http\Requests\ContactRequest;
use Illuminate\Validation\Rule;

class FengShuiConsultationRequest extends ContactRequest
{
    public static function specialties(): array
    {
        return [
            'personal' => 'Mệnh & Ngũ Hành',
            'birth-chart' => 'Tử Vi Bát Tự',
            'home' => 'Phong Thủy Nhà Ở',
            'business' => 'Phong Thủy Kinh Doanh',
            'auspicious-date' => 'Ngày Tốt & Nhập Trạch',
            'other' => 'Tư Vấn Khác',
        ];
    }

    public static function methods(): array
    {
        return [
            'in-person' => ['Trực tiếp', 'Trao đổi và tư vấn trực tiếp'],
            'on-site' => ['Khảo Sát Tận Nơi', 'Khảo sát không gian thực tế'],
            'online' => ['Luận Giải Online', 'Trao đổi qua cuộc gọi video'],
        ];
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:40'],
            'phone' => ['required', new PhoneNumberRule],
            'email' => ['nullable', new EmailRule, 'max:80'],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'birth_time' => ['nullable', 'string', 'max:100'],
            'lunar_birth_year' => ['nullable', 'string', 'max:100'],
            'specialty' => ['required', Rule::in(array_keys(static::specialties()))],
            'consultation_method' => ['required', Rule::in(array_keys(static::methods()))],
            'content' => ['nullable', 'string', 'max:10000'],
        ];

        if (setting('contact_form_show_terms_checkbox', true)) {
            $rules['agree_terms_and_policy'] = ['required', 'accepted:1'];
        }

        // Keep the same anti-spam settings as the existing contact form.
        if (is_plugin_active('captcha')) {
            if (Captcha::reCaptchaEnabled() && Captcha::formSetting(ContactForm::class, 'enable_recaptcha')) {
                $rules = [...$rules, ...Captcha::rules()];
            }

            if (Captcha::mathCaptchaEnabled() && Captcha::formSetting(ContactForm::class, 'enable_math_captcha')) {
                $rules = [...$rules, ...Captcha::mathCaptchaRules()];
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            ...parent::attributes(),
            'birth_date' => 'ngày tháng năm sinh',
            'birth_time' => 'giờ sinh',
            'lunar_birth_year' => 'năm sinh âm lịch',
            'specialty' => 'hạng mục tư vấn',
            'consultation_method' => 'hình thức tham vấn',
        ];
    }

    public function messages(): array
    {
        return [
            ...parent::messages(),
            'required' => 'Vui lòng cung cấp :attribute.',
            'email' => 'Vui lòng nhập địa chỉ email hợp lệ.',
            'birth_date.date_format' => 'Vui lòng nhập ngày sinh hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh không được ở tương lai.',
            'birth_date.after_or_equal' => 'Ngày sinh phải từ năm 1900 trở đi.',
            'specialty.in' => 'Vui lòng chọn hạng mục tư vấn trong danh sách.',
            'consultation_method.in' => 'Vui lòng chọn hình thức tham vấn trong danh sách.',
        ];
    }
}
