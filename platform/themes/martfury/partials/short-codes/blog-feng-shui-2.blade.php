@php
    $featuredImage = $shortcode->featured_image
        ? RvMedia::getImageUrl($shortcode->featured_image)
        : Theme::asset()->url('img/home-feng-shui-hero.png');

    $defaultImages = [
        1 => Theme::asset()->url('img/blog-feng-shui-bell.png'),
        2 => Theme::asset()->url('img/blog-feng-shui-compass.png'),
        3 => Theme::asset()->url('img/home-feng-shui-hero.png'),
        4 => Theme::asset()->url('img/blog-feng-shui-compass.png'),
    ];

    $articleDefaults = [
        1 => [
            'category' => 'Nhập Trạch - Khởi Lễ',
            'title' => 'Nghi Lễ Tạ Thần Linh & An Vị Gia Tiên',
            'description' => 'Bài văn khấn mộc mạc đúng truyền thống Việt, cùng danh mục ngũ quả, lễ hương đăng và thủ tục an vị để gia đạo khởi đầu yên ổn.',
            'url' => '#',
        ],
        2 => [
            'category' => 'Minh Đường Tụ Thủy',
            'title' => 'Vị Trí Đặt Tiểu Cảnh Nước Chuẩn Phong Thủy',
            'description' => 'Nguyên tắc "Sơn quản nhân đinh, Thủy quản tài lộc" giúp chọn vị trí hồ nước, dòng chảy và ánh sáng để kích hoạt sinh khí vừa đủ.',
            'url' => '#',
        ],
        3 => [
            'category' => 'Công Danh Thuận Buồm',
            'title' => 'Tựa Sơn Nghinh Thủy: Quy Cách Bàn Làm Việc',
            'description' => 'Tránh quay lưng ra cửa, đón nhận ánh sáng tự nhiên và chọn điểm tựa vững chãi để tinh thần ổn định, công việc hanh thông.',
            'url' => '#',
        ],
        4 => [
            'category' => 'Tĩnh Tâm An Cư',
            'title' => 'Thanh Lọc Khí Trường: Cách Sắp Đặt Góc Thiền',
            'description' => 'Chọn góc yên tĩnh, ánh sáng dịu và vật phẩm cân bằng để giảm nhiễu khí, giúp không gian nghỉ ngơi sâu hơn và tinh thần ổn định.',
            'url' => '#',
        ],
    ];

    $legacyDescriptions = [
        1 => 'Bài văn khấn mộc mạc đúng truyền thống Việt, cùng danh mục ngũ quả và thủ tục hỏa lò tụ sinh...',
        2 => 'Nguyên tắc "Sơn quản nhân đinh, Thủy quản tài lộc" và những cung vị tuyệt đối không nên kích hoạt...',
        3 => 'Tránh quay lưng ra cửa, đón nhận ánh sáng tự nhiên và cách chọn điểm tựa vững chãi để minh mẫn...',
        4 => '',
    ];

    $articles = [];

    for ($i = 1; $i <= 4; $i++) {
        $description = $shortcode->{'article_' . $i . '_description'};
        $normalizedDescription = str_replace(['“', '”'], '"', trim((string) $description));
        $normalizedLegacyDescription = str_replace(['“', '”'], '"', $legacyDescriptions[$i]);

        if (! $description || $normalizedDescription === $normalizedLegacyDescription) {
            $description = $articleDefaults[$i]['description'];
        }

        $articles[] = [
            'image' => $shortcode->{'article_' . $i . '_image'}
                ? RvMedia::getImageUrl($shortcode->{'article_' . $i . '_image'})
                : $defaultImages[$i],
            'category' => $shortcode->{'article_' . $i . '_category'} ?: $articleDefaults[$i]['category'],
            'title' => $shortcode->{'article_' . $i . '_title'} ?: $articleDefaults[$i]['title'],
            'description' => $description,
            'url' => $shortcode->{'article_' . $i . '_url'} ?: $articleDefaults[$i]['url'],
        ];
    }
@endphp

<section class="blog-feng-shui-2">
    <div class="blog-feng-shui-2__inner">
        <div class="blog-feng-shui-2__header">
            <span>{!! BaseHelper::clean($shortcode->eyebrow ?: 'Thực Địa Ứng Dụng') !!}</span>
            <h2>{!! BaseHelper::clean($shortcode->title ?: 'Cẩm Nang Dịch Lý Nhà Ở Đương Đại') !!}</h2>
            <p>{!! BaseHelper::clean($shortcode->subtitle ?: 'Giải pháp hóa giải thế sát, khơi thông điểm nghẽn năng lượng, kiến tạo không gian sống dung hòa sinh thái và tâm an.') !!}</p>
        </div>

        <div class="blog-feng-shui-2__layout">
            <article class="blog-feng-shui-2__featured">
                <a class="blog-feng-shui-2__featured-image" href="{{ $shortcode->featured_url ?: '#' }}" style="--blog-feng-shui-2-featured-bg: url('{{ $featuredImage }}');">
                    <span>{!! BaseHelper::clean($shortcode->featured_badge ?: 'Kỳ Thư Tháng Này') !!}</span>
                </a>

                <div class="blog-feng-shui-2__featured-body">
                    <div class="blog-feng-shui-2__meta">
                        {!! BaseHelper::clean($shortcode->featured_meta ?: 'Đại Việt Dịch Học • 8 phút đọc • 12.400 lượt xem') !!}
                    </div>

                    <h3>
                        <a href="{{ $shortcode->featured_url ?: '#' }}">
                            {!! BaseHelper::clean($shortcode->featured_title ?: 'Bố Trí Không Gian Sống Hài Hòa Âm Dương: Hóa Giải Góc Nhọn Xung Sát (Tiễn Xạ)') !!}
                        </a>
                    </h3>

                    <p>
                        {!! BaseHelper::clean($shortcode->featured_description ?: 'Các cạnh nhọn của tủ kệ, dầm ngang bê tông hay hướng nhọn của công trình đối diện trực xung thẳng vào vị trí giường nằm hoặc bàn trà sinh hoạt tạo nên trường khí bất ổn, khiến gia đạo dễ phát sinh nóng giận và hao tán sinh lực.') !!}
                    </p>

                    <a class="blog-feng-shui-2__read-more" href="{{ $shortcode->featured_url ?: '#' }}">
                        <span>{!! BaseHelper::clean($shortcode->featured_action_label ?: 'Đọc giải pháp chi tiết') !!}</span>
                        {!! BaseHelper::renderIcon('ti ti-book') !!}
                    </a>
                </div>
            </article>

            <div class="blog-feng-shui-2__side-list">
                @foreach ($articles as $article)
                    <article class="blog-feng-shui-2__side-card">
                        <a class="blog-feng-shui-2__side-image" href="{{ $article['url'] }}">
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}">
                        </a>

                        <div class="blog-feng-shui-2__side-content">
                            <span>{!! BaseHelper::clean($article['category']) !!}</span>
                            <h3>
                                <a href="{{ $article['url'] }}">
                                    {!! BaseHelper::clean($article['title']) !!}
                                </a>
                            </h3>
                            <p>{!! BaseHelper::clean($article['description']) !!}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
