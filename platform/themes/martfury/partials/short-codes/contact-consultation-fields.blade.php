@php
    $requestClass = \Theme\Martfury\Http\Requests\FengShuiConsultationRequest::class;
@endphp

<div class="feng-shui-contact__grid">
    <div class="feng-shui-contact__field">
        <label for="consultation-name">{!! BaseHelper::renderIcon('ti ti-user') !!} Họ và Tên Gia Chủ <span>*</span></label>
        <input id="consultation-name" name="name" type="text" autocomplete="name" maxlength="40" placeholder="Ví dụ: Nguyễn Văn Khang" value="{{ old('name') }}" required>
    </div>
    <div class="feng-shui-contact__field">
        <label for="consultation-phone">{!! BaseHelper::renderIcon('ti ti-device-mobile') !!} Số Điện Thoại Trực Hệ <span>*</span></label>
        <input id="consultation-phone" name="phone" type="tel" autocomplete="tel" maxlength="30" placeholder="0912 xxx xxx" value="{{ old('phone') }}" required>
    </div>
    <div class="feng-shui-contact__field">
        <label for="consultation-email">{!! BaseHelper::renderIcon('ti ti-mail') !!} Hòm Thư Điện Tử (Email)</label>
        <input id="consultation-email" name="email" type="email" autocomplete="email" maxlength="80" placeholder="Địa chỉ email của bạn" value="{{ old('email') }}">
    </div>
    <div class="feng-shui-contact__field">
        <label for="consultation-birth-date">{!! BaseHelper::renderIcon('ti ti-calendar') !!} Ngày Tháng Năm Sinh (Dương Lịch) <span>*</span></label>
        <input id="consultation-birth-date" name="birth_date" type="date" autocomplete="bday" min="1900-01-01" max="{{ now()->toDateString() }}" value="{{ old('birth_date') }}" required>
    </div>
</div>

<div class="feng-shui-contact__birth-details feng-shui-contact__grid">
    <div class="feng-shui-contact__field">
        <label for="consultation-birth-time">{!! BaseHelper::renderIcon('ti ti-clock') !!} Giờ Sinh Chính Xác (Nếu biết)</label>
        <input id="consultation-birth-time" name="birth_time" type="text" maxlength="100" placeholder="Ví dụ: 06:45 sáng (Giờ Mão)" value="{{ old('birth_time') }}">
    </div>
    <div class="feng-shui-contact__field">
        <label for="consultation-lunar-year">{!! BaseHelper::renderIcon('ti ti-rosette') !!} Năm Sinh Âm Lịch (Nếu có sẵn)</label>
        <input id="consultation-lunar-year" name="lunar_birth_year" type="text" maxlength="100" placeholder="Ví dụ: Giáp Dần 1974, Đinh Mão 1987" value="{{ old('lunar_birth_year') }}">
    </div>
</div>

<div class="feng-shui-contact__field">
    <label for="consultation-specialty">{!! BaseHelper::renderIcon('ti ti-category') !!} Hạng Mục Cần Thỉnh Tư Vấn <span>*</span></label>
    <div class="feng-shui-contact__select">
        <select id="consultation-specialty" name="specialty" required>
            <option value="">-- Quý gia chủ vui lòng chọn chuyên khoa tư vấn --</option>
            @foreach ($requestClass::specialties() as $value => $label)
                <option value="{{ $value }}" @selected(old('specialty') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        {!! BaseHelper::renderIcon('ti ti-chevron-down') !!}
    </div>
</div>

<fieldset class="feng-shui-contact__methods">
    <legend>{!! BaseHelper::renderIcon('ti ti-heart-handshake') !!} Hình Thức Tham Vấn Mong Muốn <span>*</span></legend>
    <div class="feng-shui-contact__method-options">
        @foreach ($requestClass::methods() as $value => [$label, $description])
            <label class="feng-shui-contact__method">
                <input type="radio" name="consultation_method" value="{{ $value }}" @checked(old('consultation_method', 'in-person') === $value) required>
                <span><strong>{{ $label }}</strong><small>{{ $description }}</small></span>
            </label>
        @endforeach
    </div>
</fieldset>

<div class="feng-shui-contact__field feng-shui-contact__question">
    <label for="consultation-content">
        <span class="feng-shui-contact__question-title">{!! BaseHelper::renderIcon('ti ti-pencil-question') !!} Vấn Đề Gia Đạo Hoặc Nguyện Vọng Cần Giải Đáp Cụ Thể</span>
        <small>Không bắt buộc</small>
    </label>
    <textarea id="consultation-content" name="content" rows="5" maxlength="10000" placeholder="Quý gia chủ có thể miêu tả tình hình đất đai, hướng nhà, khó khăn trong kinh doanh, dự định xuất hành hoặc nguyện vọng gia đạo cần được tư vấn...">{{ old('content') }}</textarea>
</div>

<div class="feng-shui-contact__privacy">
    {!! BaseHelper::renderIcon('ti ti-lock') !!}
    <p>Thông tin ngày giờ sinh và nội dung hồ sơ được tiếp nhận để chuẩn bị tư vấn. Chúng tôi sẽ liên hệ với Quý gia chủ để trao đổi và xác nhận lịch hẹn.</p>
</div>

@if (setting('contact_form_show_terms_checkbox', true))
    <label class="feng-shui-contact__consent">
        <input type="checkbox" name="agree_terms_and_policy" value="1" @checked(old('agree_terms_and_policy')) required>
        <span>{{ trans('plugins/contact::contact.agree_terms_privacy') }}</span>
    </label>
@endif
