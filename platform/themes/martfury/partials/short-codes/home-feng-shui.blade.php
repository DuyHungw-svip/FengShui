@php
    $backgroundImage = $shortcode->background_image
        ? RvMedia::getImageUrl($shortcode->background_image)
        : Theme::asset()->url('img/home-feng-shui-hero.png');

    $buttons = [
        [
            'label' => $shortcode->button_1_label ?: 'Xem Phong Thủy Bản Mệnh',
            'url' => $shortcode->button_1_url ?: '#',
            'icon' => $shortcode->button_1_icon ?: 'ti ti-leaf',
            'class' => 'is-primary',
        ],
        [
            'label' => $shortcode->button_2_label ?: 'Lập Lá Số Tử Vi',
            'url' => $shortcode->button_2_url ?: '#',
            'icon' => $shortcode->button_2_icon ?: 'ti ti-book',
            'class' => 'is-muted',
        ],
        [
            'label' => $shortcode->button_3_label ?: 'Xem Ngày Cát Nhật',
            'url' => $shortcode->button_3_url ?: '#',
            'icon' => $shortcode->button_3_icon ?: 'ti ti-calendar-check',
            'class' => 'is-muted',
        ],
        [
            'label' => $shortcode->button_4_label ?: 'Phong Thủy Nhà Ở',
            'url' => $shortcode->button_4_url ?: '#',
            'icon' => $shortcode->button_4_icon ?: 'ti ti-home',
            'class' => 'is-muted',
        ],
        [
            'label' => $shortcode->button_5_label ?: 'Vật Phẩm Cát Tường',
            'url' => $shortcode->button_5_url ?: '#',
            'icon' => $shortcode->button_5_icon ?: 'ti ti-diamond',
            'class' => 'is-gold',
        ],
    ];

    $notes = array_filter([
        $shortcode->note_1 ?: 'Tra Cứu Chính Tông',
        $shortcode->note_2 ?: '100% Cổ Truyền Đại Việt',
        $shortcode->note_3 ?: 'Khởi Vận Chu Kỳ Vận 9',
    ]);
@endphp

<section class="home-feng-shui" style="--home-feng-shui-bg: url('{{ $backgroundImage }}');">
    <div class="home-feng-shui__shade"></div>

    <div class="home-feng-shui__inner">
        <div class="home-feng-shui__eyebrow">
            {!! BaseHelper::renderIcon('ti ti-compass') !!}
            <span>{!! BaseHelper::clean($shortcode->eyebrow ?: 'Dịch Lý Đông Phương Ứng Dụng • Khai Thông Địa Mạch') !!}</span>
        </div>

        <h1 class="home-feng-shui__title">
            {!! BaseHelper::clean($shortcode->title ?: 'Thuận Thiên Thời') !!}
            <span>{!! BaseHelper::clean($shortcode->highlight_text ?: 'Khởi Sinh Khí') !!}</span>
        </h1>

        <p class="home-feng-shui__subtitle">
            {!! BaseHelper::clean($shortcode->subtitle ?: 'Hệ thống số hóa toàn diện phép phong thủy cổ truyền Đại Việt. Tinh tuyển tri thức Bát Trạch, Tử Vi Đẩu Số và Cửu Tinh Huyền Không định hướng gia vận vững bền.') !!}
        </p>

        <div class="home-feng-shui__actions">
            @foreach ($buttons as $button)
                @if ($button['label'])
                    <a class="home-feng-shui__button {{ $button['class'] }}" href="{{ $button['url'] }}">
                        {!! BaseHelper::renderIcon($button['icon']) !!}
                        <span>{!! BaseHelper::clean($button['label']) !!}</span>
                    </a>
                @endif
            @endforeach
        </div>

        @if ($notes)
            <div class="home-feng-shui__notes">
                @foreach ($notes as $note)
                    <span>{!! BaseHelper::clean($note) !!}</span>
                @endforeach
            </div>
        @endif
    </div>
</section>
