@php
    $defaultImages = [
        1 => Theme::asset()->url('img/blog-feng-shui-bell.png'),
        4 => Theme::asset()->url('img/blog-feng-shui-compass.png'),
    ];

    $cardDefaults = [
        1 => [
            'label' => 'Hạp Thể Tương Sinh',
            'title' => 'Mệnh & Ngũ Hành',
            'description' => 'Phân định màu sắc tương hợp, chất liệu bổ trợ, xác định hướng Cát - Hung chuẩn xác theo Cung phi chính tông.',
            'action_label' => 'Khám phá ngũ hành',
            'url' => '#',
            'icon' => 'ti ti-sparkles',
        ],
        2 => [
            'kicker' => '12 Cung Chức Vị',
            'label' => 'Đẩu Số Mệnh Bàn',
            'title' => 'Tử Vi Bát Tự',
            'description' => 'An sao lập bàn 12 cung chức vị: Mệnh, Phu, Phúc, Điền, Quan, Nô... Giải mã đại hạn 10 năm và lưu niên cát triệu.',
            'action_label' => 'Lập bản bát tự',
            'url' => '#',
            'icon' => 'ti ti-book',
        ],
        3 => [
            'kicker' => 'Hoàng Đạo Cát Nhật',
            'label' => 'Lịch Trực Khai Hoa',
            'title' => 'Ngày Tốt Vạn Sự',
            'description' => 'Chọn giờ Hoàng đạo, ngày Trực Kiến - Trừ - Mãn cho hỷ sự, động thổ, xuất hành và lễ nghi khánh thành hưng nghiệp.',
            'action_label' => 'Tra ngày cát lợi',
            'url' => '#',
            'icon' => 'ti ti-calendar-check',
        ],
        4 => [
            'label' => 'Bát Trạch Trực Nghiệm',
            'title' => 'Bát Trạch Nhà Ở',
            'description' => 'Bố trí Minh đường, tọa hướng Môn - Táo - Thờ, hóa giải loan đầu hình sát bên ngoài và tụ dưỡng nội khí trường.',
            'action_label' => 'Thẩm định dương trạch',
            'url' => '#',
            'icon' => 'ti ti-compass',
        ],
    ];

    $cards = [];

    for ($i = 1; $i <= 4; $i++) {
        $image = $shortcode->{'card_' . $i . '_image'}
            ? RvMedia::getImageUrl($shortcode->{'card_' . $i . '_image'})
            : ($defaultImages[$i] ?? null);

        $cards[] = [
            'image' => $image,
            'kicker' => $shortcode->{'card_' . $i . '_kicker'} ?: ($cardDefaults[$i]['kicker'] ?? ''),
            'label' => $shortcode->{'card_' . $i . '_label'} ?: $cardDefaults[$i]['label'],
            'title' => $shortcode->{'card_' . $i . '_title'} ?: $cardDefaults[$i]['title'],
            'description' => $shortcode->{'card_' . $i . '_description'} ?: $cardDefaults[$i]['description'],
            'action_label' => $shortcode->{'card_' . $i . '_action_label'} ?: $cardDefaults[$i]['action_label'],
            'url' => $shortcode->{'card_' . $i . '_url'} ?: $cardDefaults[$i]['url'],
            'icon' => $shortcode->{'card_' . $i . '_icon'} ?: $cardDefaults[$i]['icon'],
            'tone' => $i === 3 ? 'is-wine' : 'is-gold',
        ];
    }
@endphp

<section class="blog-feng-shui">
    <div class="blog-feng-shui__inner">
        <div class="blog-feng-shui__header">
            <span>{!! BaseHelper::clean($shortcode->eyebrow ?: 'Thực Chứng Học Thuật') !!}</span>
            <h2>{!! BaseHelper::clean($shortcode->title ?: 'Tứ Pháp Bí Truyền Ứng Dụng') !!}</h2>
        </div>

        <div class="blog-feng-shui__grid">
            @foreach ($cards as $card)
                <article class="blog-feng-shui__card {{ $card['image'] ? 'is-photo' : 'is-icon' }} {{ $card['tone'] }}">
                    <div
                        class="blog-feng-shui__visual"
                        @if ($card['image'])
                            style="--blog-feng-shui-card-bg: url('{{ $card['image'] }}');"
                        @endif
                    >
                        @if ($card['image'])
                            <div class="blog-feng-shui__visual-shade"></div>
                        @endif

                        @if ($card['kicker'])
                            <span class="blog-feng-shui__kicker">{!! BaseHelper::clean($card['kicker']) !!}</span>
                        @endif

                        @unless ($card['image'])
                            <div class="blog-feng-shui__icon">
                                {!! BaseHelper::renderIcon($card['icon']) !!}
                            </div>
                        @endunless

                        <span class="blog-feng-shui__label">{!! BaseHelper::clean($card['label']) !!}</span>
                    </div>

                    <div class="blog-feng-shui__body">
                        <h3>{!! BaseHelper::clean($card['title']) !!}</h3>
                        <p>{!! BaseHelper::clean($card['description']) !!}</p>
                    </div>

                    <a class="blog-feng-shui__action" href="{{ $card['url'] }}">
                        <span>{!! BaseHelper::clean($card['action_label']) !!}</span>
                        {!! BaseHelper::renderIcon('ti ti-arrow-right') !!}
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
