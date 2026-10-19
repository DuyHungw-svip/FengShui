@php
    foreach (array_keys($form->getFields()) as $field) {
        if (! str_starts_with($field, 'form_front_')) {
            $form->remove($field);
        }
    }

    $form
        ->setUrl(route('public.contact.consultation'))
        ->setValidatorClass(\Theme\Martfury\Http\Requests\FengShuiConsultationRequest::class)
        ->setFormOption('class', 'contact-form feng-shui-contact__form')
        ->setFormInputWrapperClass('feng-shui-contact__field')
        ->setFormInputClass('feng-shui-contact__input')
        ->add('before_consultation', \Botble\Base\Forms\Fields\HtmlField::class, [
            'html' => apply_filters('pre_contact_form', null),
        ])
        ->add('consultation_fields', \Botble\Base\Forms\Fields\HtmlField::class, [
            'html' => Theme::partial('short-codes.contact-consultation-fields'),
        ])
        ->add('after_consultation', \Botble\Base\Forms\Fields\HtmlField::class, [
            'html' => apply_filters('after_contact_form', null),
        ])
        ->add('submit', \Botble\Base\Forms\Fields\HtmlField::class, [
            'html' => Theme::partial('short-codes.contact-consultation-submit'),
        ]);
@endphp

<section class="feng-shui-contact">
    <div class="feng-shui-contact__surface">
        <header class="feng-shui-contact__heading">
            <p class="feng-shui-contact__eyebrow">
                {!! BaseHelper::renderIcon('ti ti-writing') !!}
                ĐĂNG KÝ KHẢO SÁT & LẬP HỒ SƠ
            </p>
            <h1>Phiếu Thỉnh Cố Vấn Học Thuật 1-1</h1>
            <p>Kính mong Quý gia chủ cung cấp thông tin chính xác để chúng tôi chuẩn bị hồ sơ và nội dung tư vấn kỹ lưỡng trước buổi trao đổi.</p>
        </header>

        {!! $form->renderForm() !!}
    </div>
</section>
