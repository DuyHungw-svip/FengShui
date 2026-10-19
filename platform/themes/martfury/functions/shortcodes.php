<?php

use Botble\Ads\Facades\AdsManager;
use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Faq\Models\FaqCategory;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;

app()->booted(function (): void {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    add_shortcode('HomeFengShui', __('HomeFengShui'), __('Home Feng Shui hero section'), function (Shortcode $shortcode) {
        return Theme::partial('short-codes.home-feng-shui', compact('shortcode'));
    });

    shortcode()->setAdminConfig('HomeFengShui', function (array $attributes) {
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'background_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Background image'))
            )
            ->add(
                'eyebrow',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Eyebrow'))
                    ->placeholder(__('Eyebrow'))
                    ->defaultValue('Dịch Lý Đông Phương Ứng Dụng • Khai Thông Địa Mạch')
            )
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
                    ->defaultValue('Thuận Thiên Thời')
            )
            ->add(
                'highlight_text',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Highlighted title'))
                    ->placeholder(__('Highlighted title'))
                    ->defaultValue('Khởi Sinh Khí')
            )
            ->add(
                'subtitle',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Subtitle'))
                    ->placeholder(__('Subtitle'))
                    ->rows(3)
                    ->defaultValue('Hệ thống số hóa toàn diện phép phong thủy cổ truyền Đại Việt. Tinh tuyển tri thức Bát Trạch, Tử Vi Đẩu Số và Cửu Tinh Huyền Không định hướng gia vận vững bền.')
            );

        $buttonDefaults = [
            1 => ['label' => 'Xem Phong Thủy Bản Mệnh', 'url' => '#', 'icon' => 'ti ti-leaf'],
            2 => ['label' => 'Lập Lá Số Tử Vi', 'url' => '#', 'icon' => 'ti ti-book'],
            3 => ['label' => 'Xem Ngày Cát Nhật', 'url' => '#', 'icon' => 'ti ti-calendar-check'],
            4 => ['label' => 'Phong Thủy Nhà Ở', 'url' => '#', 'icon' => 'ti ti-home'],
            5 => ['label' => 'Vật Phẩm Cát Tường', 'url' => '#', 'icon' => 'ti ti-diamond'],
        ];

        for ($i = 1; $i <= 5; $i++) {
            $form
                ->add(
                    'button_' . $i . '_label',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Button :number label', ['number' => $i]))
                        ->placeholder(__('Button :number label', ['number' => $i]))
                        ->defaultValue($buttonDefaults[$i]['label'])
                )
                ->add(
                    'button_' . $i . '_url',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Button :number URL', ['number' => $i]))
                        ->placeholder(__('Button :number URL', ['number' => $i]))
                        ->defaultValue($buttonDefaults[$i]['url'])
                )
                ->add(
                    'button_' . $i . '_icon',
                    CoreIconField::class,
                    CoreIconFieldOption::make()
                        ->label(__('Button :number icon', ['number' => $i]))
                        ->defaultValue($buttonDefaults[$i]['icon'])
                );
        }

        $noteDefaults = [
            1 => 'Tra Cứu Chính Tông',
            2 => '100% Cổ Truyền Đại Việt',
            3 => 'Khởi Vận Chu Kỳ Vận 9',
        ];

        for ($i = 1; $i <= 3; $i++) {
            $form->add(
                'note_' . $i,
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Note :number', ['number' => $i]))
                    ->placeholder(__('Note :number', ['number' => $i]))
                    ->defaultValue($noteDefaults[$i])
            );
        }

        return $form;
    });

    shortcode()->ignoreCaches(['BlogFengShui', 'BlogFengShui2']);

    $postChoices = function (): array {
        if (! is_plugin_active('blog')) {
            return ['' => 'Tự động lấy bài mới nhất'];
        }

        return ['' => 'Tự động lấy bài mới nhất']
            + \Botble\Blog\Models\Post::query()->wherePublished()
                ->orderByDesc('created_at')->orderByDesc('id')->pluck('name', 'id')->all();
    };

    add_shortcode('BlogFengShui', __('BlogFengShui'), __('Blog Feng Shui card section'), function (Shortcode $shortcode) {
        $slots = [];
        for ($i = 1; $i <= 4; $i++) {
            $slots[$i] = $shortcode->{'card_' . $i . '_post_id'};
        }
        $posts = theme_get_feng_shui_posts($slots);

        return $posts->isEmpty() ? '' : Theme::partial('short-codes.blog-feng-shui', compact('shortcode', 'posts'));
    });

    shortcode()->setAdminConfig('BlogFengShui', function (array $attributes) use ($postChoices) {
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add('eyebrow', TextField::class, TextFieldOption::make()
                ->label(__('Eyebrow'))->defaultValue('Thực Chứng Học Thuật'))
            ->add('title', TextField::class, TextFieldOption::make()
                ->label(__('Title'))->defaultValue('Tứ Pháp Bí Truyền Ứng Dụng'));

        $choices = $postChoices();
        for ($i = 1; $i <= 4; $i++) {
            $form
                ->add('card_' . $i . '_post_id', SelectField::class, SelectFieldOption::make()
                    ->label('Bài viết ở ô ' . $i)
                    ->choices($choices)->searchable()
                    ->helperText('Chỉ hiển thị bài đã xuất bản. Chỉnh tiêu đề, ảnh và mô tả tại Blog → Bài viết.'))
                ->add('card_' . $i . '_kicker', TextField::class, TextFieldOption::make()
                    ->label(__('Card :number kicker', ['number' => $i])))
                ->add('card_' . $i . '_action_label', TextField::class, TextFieldOption::make()
                    ->label(__('Card :number action label', ['number' => $i]))->defaultValue('Đọc bài viết'))
                ->add('card_' . $i . '_icon', CoreIconField::class, CoreIconFieldOption::make()
                    ->label(__('Card :number icon', ['number' => $i]))->defaultValue('ti ti-book'));
        }

        return $form;
    });

    add_shortcode('BlogFengShui2', __('BlogFengShui2'), __('Blog Feng Shui featured article section'), function (Shortcode $shortcode) {
        $slots = ['featured' => $shortcode->featured_post_id];
        for ($i = 1; $i <= 4; $i++) {
            $slots[$i] = $shortcode->{'article_' . $i . '_post_id'};
        }
        $posts = theme_get_feng_shui_posts($slots);
        $featured = $posts->pull('featured');

        if (! $featured) {
            $featured = $posts->shift();
        }

        return $featured ? Theme::partial('short-codes.blog-feng-shui-2', [
            'shortcode' => $shortcode,
            'featured' => $featured,
            'articles' => $posts,
        ]) : '';
    });

    shortcode()->setAdminConfig('BlogFengShui2', function (array $attributes) use ($postChoices) {
        $choices = $postChoices();
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add('eyebrow', TextField::class, TextFieldOption::make()
                ->label(__('Eyebrow'))->defaultValue('Thực Địa Ứng Dụng'))
            ->add('title', TextField::class, TextFieldOption::make()
                ->label(__('Title'))->defaultValue('Cẩm Nang Dịch Lý Nhà Ở Đương Đại'))
            ->add('subtitle', TextareaField::class, TextareaFieldOption::make()
                ->label(__('Subtitle'))->rows(2)
                ->defaultValue('Giải pháp hóa giải thế sát, khơi thông điểm nghẽn năng lượng, kiến tạo không gian sống dung hòa sinh thái và tâm an.'))
            ->add('featured_post_id', SelectField::class, SelectFieldOption::make()
                ->label('Bài viết nổi bật')->choices($choices)->searchable()
                ->helperText('Chỉ hiển thị bài đã xuất bản. Chỉnh tiêu đề, ảnh và mô tả tại Blog → Bài viết.'))
            ->add('featured_badge', TextField::class, TextFieldOption::make()
                ->label(__('Featured badge'))->defaultValue('Kỳ Thư Tháng Này'))
            ->add('featured_action_label', TextField::class, TextFieldOption::make()
                ->label(__('Featured action label'))->defaultValue('Đọc giải pháp chi tiết'));

        for ($i = 1; $i <= 4; $i++) {
            $form->add('article_' . $i . '_post_id', SelectField::class, SelectFieldOption::make()
                ->label('Bài viết nhỏ ' . $i)->choices($choices)->searchable());
        }

        return $form;
    });

    $blogFengShui3Fields = [
        'eyebrow' => ['Dòng giới thiệu', 'Học Thuật Cổ Dịch • Biện Chứng Thực Địa • Phi Mê Tín'],
        'title' => ['Tiêu đề', 'Hành Trình Gìn Giữ Cổ Học & Kiến Tạo Bình An Gia Đạo'],
        'description' => ['Nội dung giới thiệu', 'Gìn giữ tinh hoa Kinh Dịch, Bát Trạch và tri thức phong thủy truyền thống, chúng tôi đồng hành cùng gia chủ trong việc tìm hiểu không gian sống và vun đắp bình an gia đạo. Mỗi cuộc tham vấn bắt đầu từ sự lắng nghe, đối chiếu thông tin và khảo sát thực tế, hướng đến những giải pháp phù hợp với hoàn cảnh của từng gia đình.'],
        'quote_title' => ['Tiêu đề lời dẫn', 'Đạo Pháp Tự Nhiên - Đức Năng Thắng Số'],
        'quote_description' => ['Nội dung lời dẫn', 'Địa lý thuận thiên ứng nhân; nhà ở an định tạo dưỡng tâm tính. Lấy sự cân bằng, hiểu biết và hành động thiết thực làm nền tảng cho đời sống an hòa.'],
        'primary_label' => ['Nút chính - Nội dung', 'Đặt Lịch Tham Vấn 1-1'],
        'primary_url' => ['Nút chính - Liên kết', '/contact'],
        'secondary_label' => ['Nút phụ - Nội dung', 'Tìm Hiểu Tri Thức Phong Thủy'],
        'secondary_url' => ['Nút phụ - Liên kết', '/blog'],
        'image' => ['Ảnh giới thiệu', ''],
        'image_alt' => ['Mô tả ảnh', 'Tư liệu Dịch lý và địa lý cổ'],
        'image_eyebrow' => ['Dòng nhỏ trên ảnh', 'Tư Liệu Dịch Lý & Địa Lý Cổ'],
        'image_caption' => ['Chú thích trên ảnh', 'Gìn giữ tri thức, ứng dụng vào đời sống'],
        'badge_title' => ['Nhãn nổi - Tiêu đề', 'Tư Vấn Có Trách Nhiệm'],
        'badge_description' => ['Nhãn nổi - Nội dung', 'Lắng nghe và đồng hành cùng gia chủ'],
    ];
    $blogFengShui3Stats = [
        1 => ['label' => 'Nền Tảng', 'value' => 'Kinh Dịch', 'description' => 'Tìm hiểu tri thức cổ học và giá trị truyền thống trong đời sống đương đại.', 'icon' => 'ti ti-writing'],
        2 => ['label' => 'Tham Vấn', 'value' => 'Gia Trạch', 'description' => 'Lắng nghe nhu cầu của gia chủ, xem xét thông tin và bối cảnh từng gia đình.', 'icon' => 'ti ti-folder'],
        3 => ['label' => 'Phương Pháp', 'value' => 'Thực Địa', 'description' => 'Kết hợp nghiên cứu tư liệu với quan sát, khảo sát không gian thực tế.', 'icon' => 'ti ti-school'],
        4 => ['label' => 'Đồng Hành', 'value' => 'Tận Tâm', 'description' => 'Trao đổi rõ ràng, tôn trọng lựa chọn và hoàn cảnh riêng của mỗi gia chủ.', 'icon' => 'ti ti-shield-check'],
    ];

    add_shortcode('BlogFengShui3', __('BlogFengShui3'), 'Giới thiệu cổ học, tham vấn và bốn ô thông tin', function (Shortcode $shortcode) use ($blogFengShui3Fields, $blogFengShui3Stats) {
        $content = [];
        foreach ($blogFengShui3Fields as $field => [$label, $default]) {
            $content[$field] = $shortcode->{$field} ?? $default;
        }
        $stats = [];
        foreach ($blogFengShui3Stats as $number => $defaults) {
            foreach ($defaults as $field => $default) {
                $stats[$number][$field] = $shortcode->{'stat_' . $number . '_' . $field} ?? $default;
            }
        }

        return Theme::partial('short-codes.blog-feng-shui-3', compact('content', 'stats'));
    });

    shortcode()->setAdminConfig('BlogFengShui3', function (array $attributes) use ($blogFengShui3Fields, $blogFengShui3Stats) {
        $form = ShortcodeForm::createFromArray($attributes);

        foreach ($blogFengShui3Fields as $field => [$label, $default]) {
            if ($field === 'image') {
                $form->add($field, MediaImageField::class, MediaImageFieldOption::make()->label($label));
            } elseif (in_array($field, ['description', 'quote_description'], true)) {
                $form->add($field, TextareaField::class, TextareaFieldOption::make()
                    ->label($label)->rows(3)->defaultValue($default));
            } else {
                $form->add($field, TextField::class, TextFieldOption::make()->label($label)->defaultValue($default));
            }
        }

        foreach ($blogFengShui3Stats as $number => $defaults) {
            $prefix = 'stat_' . $number . '_';
            $form
                ->add($prefix . 'icon', CoreIconField::class, CoreIconFieldOption::make()
                    ->label('Ô ' . $number . ' - Biểu tượng')->defaultValue($defaults['icon']))
                ->add($prefix . 'label', TextField::class, TextFieldOption::make()
                    ->label('Ô ' . $number . ' - Nhãn')->defaultValue($defaults['label']))
                ->add($prefix . 'value', TextField::class, TextFieldOption::make()
                    ->label('Ô ' . $number . ' - Thông tin / số liệu')->defaultValue($defaults['value'])
                    ->helperText('Nhập số liệu thực tế của bạn; có thể trình bày 25+ Năm, 18.000+, 100% hoặc 0 Đồng như mẫu.'))
                ->add($prefix . 'description', TextareaField::class, TextareaFieldOption::make()
                    ->label('Ô ' . $number . ' - Mô tả')->rows(2)->defaultValue($defaults['description']));
        }

        return $form;
    });
    shortcode()->ignoreLazyLoading(['BlogFengShui3']);
    shortcode()->ignoreCaches(['BlogFengShui3']);

    $blogFengShui4Fields = [
        'eyebrow' => ['Dòng giới thiệu', 'Không Gian Tiếp Đón'],
        'title' => ['Tiêu đề', 'Trà Thiền Đàm Đạo - Lắng Nghe & Thấu Cảm'],
        'description' => ['Nội dung giới thiệu', 'Một không gian tĩnh tại để cùng nhau trao đổi, lắng nghe và tìm hiểu tri thức phong thủy. Mỗi cuộc trò chuyện bắt đầu từ sự thấu hiểu hoàn cảnh của gia chủ, trong không gian trà thanh tịnh, gần gũi với thiên nhiên và những giá trị cổ học.'],
        'contact_label' => ['Nội dung liên kết liên hệ', 'Liên hệ đặt lịch ghé thăm'],
        'contact_url' => ['Liên kết liên hệ', '/contact'],
    ];
    $blogFengShui4Images = [
        1 => ['image' => '', 'caption' => 'Không Gian Đàm Đạo Trà Thiền', 'alt' => 'Không gian trà và sân vườn', 'fallback' => 'img/home-feng-shui-hero.png'],
        2 => ['image' => '', 'caption' => 'Trà Đạo Tĩnh Tâm', 'alt' => 'Không gian trà đạo tĩnh tâm', 'fallback' => 'img/home-feng-shui-hero.png'],
        3 => ['image' => '', 'caption' => 'Tư Liệu La Bàn Cổ', 'alt' => 'La bàn và tư liệu cổ học', 'fallback' => 'img/blog-feng-shui-compass.png'],
    ];
    $blogFengShui4Features = [
        1 => ['title' => 'Tĩnh Tâm Trước Luận Việc', 'description' => 'Dành thời gian lắng lại, chia sẻ những điều đang quan tâm để cuộc trao đổi được cởi mở và sáng rõ.', 'icon' => 'ti ti-plant-2'],
        2 => ['title' => 'Tư Liệu Cổ Học', 'description' => 'Cùng tìm hiểu sách cổ, la bàn và các tư liệu địa lý, đối chiếu tri thức truyền thống với thực tế.', 'icon' => 'ti ti-book'],
        3 => ['title' => 'Tham Vấn Trực Tiếp', 'description' => 'Trao đổi riêng về hoàn cảnh và nguyện vọng của gia chủ, tôn trọng sự riêng tư trong mỗi cuộc tham vấn.', 'icon' => 'ti ti-user'],
    ];

    add_shortcode('BlogFengShui4', __('BlogFengShui4'), 'Không gian trà thiền, giới thiệu và tham vấn', function (Shortcode $shortcode) use ($blogFengShui4Fields, $blogFengShui4Images, $blogFengShui4Features) {
        $content = [];
        foreach ($blogFengShui4Fields as $field => [$label, $default]) {
            $content[$field] = $shortcode->{$field} ?? $default;
        }
        $pictures = $blogFengShui4Images;
        foreach ($pictures as $number => &$picture) {
            foreach (['image', 'caption', 'alt'] as $field) {
                $picture[$field] = $shortcode->{'picture_' . $number . '_' . $field} ?? $picture[$field];
            }
        }
        unset($picture);

        $features = [];
        foreach ($blogFengShui4Features as $number => $defaults) {
            $feature = [];
            foreach ($defaults as $field => $default) {
                $feature[$field] = $shortcode->{'feature_' . $number . '_' . $field} ?? $default;
            }
            if ($feature['title'] || $feature['description']) {
                $features[] = $feature;
            }
        }

        return Theme::partial('short-codes.blog-feng-shui-4', compact('content', 'pictures', 'features'));
    });

    shortcode()->setAdminConfig('BlogFengShui4', function (array $attributes) use ($blogFengShui4Fields, $blogFengShui4Images, $blogFengShui4Features) {
        $form = ShortcodeForm::createFromArray($attributes);

        foreach ($blogFengShui4Fields as $field => [$label, $default]) {
            if ($field === 'description') {
                $form->add($field, TextareaField::class, TextareaFieldOption::make()
                    ->label($label)->rows(3)->defaultValue($default));
            } else {
                $form->add($field, TextField::class, TextFieldOption::make()->label($label)->defaultValue($default));
            }
        }

        foreach ($blogFengShui4Images as $number => $defaults) {
            $prefix = 'picture_' . $number . '_';
            $form
                ->add($prefix . 'image', MediaImageField::class, MediaImageFieldOption::make()
                    ->label('Ảnh ' . $number . ($number === 1 ? ' - Ảnh lớn bên trái' : ' - Ảnh nhỏ')))
                ->add($prefix . 'caption', TextField::class, TextFieldOption::make()
                    ->label('Ảnh ' . $number . ' - Chú thích')->defaultValue($defaults['caption']))
                ->add($prefix . 'alt', TextField::class, TextFieldOption::make()
                    ->label('Ảnh ' . $number . ' - Mô tả ảnh')->defaultValue($defaults['alt']));
        }

        foreach ($blogFengShui4Features as $number => $defaults) {
            $prefix = 'feature_' . $number . '_';
            $form
                ->add($prefix . 'icon', CoreIconField::class, CoreIconFieldOption::make()
                    ->label('Điểm nổi bật ' . $number . ' - Biểu tượng')->defaultValue($defaults['icon']))
                ->add($prefix . 'title', TextField::class, TextFieldOption::make()
                    ->label('Điểm nổi bật ' . $number . ' - Tiêu đề')->defaultValue($defaults['title']))
                ->add($prefix . 'description', TextareaField::class, TextareaFieldOption::make()
                    ->label('Điểm nổi bật ' . $number . ' - Mô tả')->rows(2)->defaultValue($defaults['description']));
        }

        return $form;
    });
    shortcode()->ignoreLazyLoading(['BlogFengShui4']);
    shortcode()->ignoreCaches(['BlogFengShui4']);

    $cardFengShuiHeader = [
        'eyebrow' => ['Dòng giới thiệu', 'Tôn Chỉ Phụng Sự'],
        'title' => ['Tiêu đề', 'Ba Trụ Cột Vững Bền Của Phong Thủy Chân Chính'],
        'subtitle' => ['Mô tả', 'Chúng tôi định nghĩa phong thủy là sự tương tác hài hòa giữa Thiên Thời (nhịp điệu vũ trụ), Địa Lợi (hình thế môi trường đất đai) và Nhân Hòa (tâm tính và đạo đức gia chủ).'],
    ];
    $cardFengShuiDefaults = [
        1 => [
            'icon' => 'ti ti-compass',
            'title' => 'Minh Bạch Học Thuật',
            'description' => 'Tri thức phong thủy được trình bày rõ ràng, đối chiếu với tư liệu cổ học và hoàn cảnh thực tế của gia chủ. Mỗi nhận định cần có căn cứ và được trao đổi minh bạch, không phán đoán tùy tiện hay dùng sự lo lắng để thúc đẩy những quyết định thiếu cân nhắc.',
            'note' => 'Biện chứng & Tri thức truyền thống',
        ],
        2 => [
            'icon' => 'ti ti-bulb',
            'title' => 'Dĩ Tâm Vi Bản',
            'description' => '"Đức năng thắng số, tâm an vạn sự hưng". Phong thủy hướng đến sự cân bằng giữa ánh sáng, luồng gió và không gian sống. Giá trị bền vững bắt đầu từ tâm thái an hòa, lối sống thiện lành và sự thấu hiểu giữa những người cùng chung mái nhà.',
            'note' => 'Dưỡng tâm • Tích đức • Tự nhiên',
        ],
        3 => [
            'icon' => 'ti ti-lock',
            'title' => 'Bảo Mật Thông Tin',
            'description' => 'Thông tin ngày sinh, bản vẽ mặt bằng và những chia sẻ về gia đình được tiếp nhận để chuẩn bị nội dung tham vấn phù hợp. Sự riêng tư của gia chủ cần được tôn trọng trong quá trình trao đổi, với phạm vi sử dụng thông tin được giải thích rõ ràng.',
            'note' => 'Tôn trọng quyền riêng tư',
        ],
    ];

    add_shortcode('CardFengShui', __('CardFengShui'), 'Ba trụ cột và tôn chỉ phụng sự phong thủy', function (Shortcode $shortcode) use ($cardFengShuiHeader, $cardFengShuiDefaults) {
        $heading = [];
        foreach ($cardFengShuiHeader as $field => [$label, $default]) {
            $heading[$field] = $shortcode->{$field} ?? $default;
        }
        $cards = [];
        foreach ($cardFengShuiDefaults as $number => $defaults) {
            foreach ($defaults as $field => $default) {
                $cards[$number][$field] = $shortcode->{'card_' . $number . '_' . $field} ?? $default;
            }
        }

        return Theme::partial('short-codes.card-feng-shui', compact('heading', 'cards'));
    });

    shortcode()->setAdminConfig('CardFengShui', function (array $attributes) use ($cardFengShuiHeader, $cardFengShuiDefaults) {
        $form = ShortcodeForm::createFromArray($attributes);
        foreach ($cardFengShuiHeader as $field => [$label, $default]) {
            if ($field === 'subtitle') {
                $form->add($field, TextareaField::class, TextareaFieldOption::make()->label($label)->rows(2)->defaultValue($default));
            } else {
                $form->add($field, TextField::class, TextFieldOption::make()->label($label)->defaultValue($default));
            }
        }
        foreach ($cardFengShuiDefaults as $number => $defaults) {
            $prefix = 'card_' . $number . '_';
            $form
                ->add($prefix . 'icon', CoreIconField::class, CoreIconFieldOption::make()
                    ->label('Thẻ ' . $number . ' - Biểu tượng')->defaultValue($defaults['icon']))
                ->add($prefix . 'title', TextField::class, TextFieldOption::make()
                    ->label('Thẻ ' . $number . ' - Tiêu đề')->defaultValue($defaults['title']))
                ->add($prefix . 'description', TextareaField::class, TextareaFieldOption::make()
                    ->label('Thẻ ' . $number . ' - Mô tả')->rows(4)->defaultValue($defaults['description']))
                ->add($prefix . 'note', TextField::class, TextFieldOption::make()
                    ->label('Thẻ ' . $number . ' - Ghi chú cuối thẻ')->defaultValue($defaults['note']));
        }

        return $form;
    });
    shortcode()->ignoreLazyLoading(['CardFengShui']);
    shortcode()->ignoreCaches(['CardFengShui']);

    $productFengShuiDefaults = [
        1 => [
            'image' => 'products/4ba1bf26-d221-427c-819d-7c75d0ccecee.png',
            'badge' => 'Trì Chú Khai Quang',
            'element' => 'Mộc - Thủy Tương Hạp',
            'price' => '3.200.000 đ',
            'title' => 'Vòng Ngọc Cẩm Thạch Tự Nhiên',
            'description' => 'Chế tác từ ngọc nguyên khối khai thác tự nhiên, mang năng lượng ôn hòa giúp điều hòa từ trường cơ thể, tĩnh tâm an thần.',
            'note' => 'Kiểm định bảo ngọc',
        ],
        2 => [
            'image' => 'products/7c733296-6ea1-4625-b578-8583942cf873.png',
            'badge' => 'Chiêu Tài Tiến Bảo',
            'element' => 'Kim Vận Hưng Khởi',
            'price' => '6.800.000 đ',
            'title' => 'Kim Thiềm Thừ Ngậm Tiền Vàng',
            'description' => 'Đồng nguyên chất đúc thủ công thể cát khánh, chuyên an vị tại cung Tài Lộc hoặc quầy thu ngân để trấn giữ tài nguyên không tán.',
            'note' => 'An vị điểm chỉ',
        ],
        3 => [
            'image' => '',
            'badge' => 'Trí Tuệ - Đỗ Đạt',
            'element' => 'Văn Tinh Tụ Khí',
            'price' => '2.450.000 đ',
            'title' => 'Tháp Văn Xương Cửu Tầng Đồng',
            'description' => 'Biểu trưng của đỗ đạt khoa bảng và công danh rộng mở. Khuyến nghị đặt tại bàn làm việc hoặc phương vị Văn Xương tinh.',
            'note' => 'Trợ học đăng khoa',
        ],
    ];

    add_shortcode('ProductFengShui', __('ProductFengShui'), __('Vật phẩm phong thủy - Bảo Vật Hộ Thân Trợ Mệnh'), function (Shortcode $shortcode) use ($productFengShuiDefaults) {
        return Theme::partial('short-codes.product-feng-shui', compact('shortcode', 'productFengShuiDefaults'));
    });

    shortcode()->setAdminConfig('ProductFengShui', function (array $attributes) use ($productFengShuiDefaults) {
        $form = ShortcodeForm::createFromArray($attributes);

        $headerFields = [
            'eyebrow' => ['Dòng giới thiệu', 'Khí Cụ Chiêu Tài • Tĩnh Tâm An Gia'],
            'title' => ['Tiêu đề', 'Bảo Vật Hộ Thân Trợ Mệnh'],
            'view_all_label' => ['Nhãn xem tất cả', 'Xem toàn bộ bảo khố'],
            'view_all_url' => ['Liên kết xem tất cả', '#'],
            'button_label' => ['Nhãn nút sản phẩm', 'Thỉnh Vật Phẩm'],
        ];

        foreach ($headerFields as $name => [$label, $default]) {
            $form->add($name, TextField::class, TextFieldOption::make()->label($label)->defaultValue($default));
        }

        $productFields = [
            'badge' => 'Nhãn trên ảnh',
            'element' => 'Ngũ hành',
            'price' => 'Giá hiển thị',
            'title' => 'Tên sản phẩm',
            'note' => 'Ghi chú kiểm định',
        ];

        for ($i = 1; $i <= 3; $i++) {
            $form->add(
                'product_' . $i . '_image',
                MediaImageField::class,
                MediaImageFieldOption::make()->label('Sản phẩm ' . $i . ' - Ảnh')
            );

            foreach ($productFields as $name => $label) {
                $form->add(
                    'product_' . $i . '_' . $name,
                    TextField::class,
                    TextFieldOption::make()
                        ->label('Sản phẩm ' . $i . ' - ' . $label)
                        ->defaultValue($productFengShuiDefaults[$i][$name])
                );
            }

            $form
                ->add(
                    'product_' . $i . '_description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label('Sản phẩm ' . $i . ' - Mô tả')
                        ->rows(3)
                        ->defaultValue($productFengShuiDefaults[$i]['description'])
                )
                ->add(
                    'product_' . $i . '_url',
                    TextField::class,
                    TextFieldOption::make()->label('Sản phẩm ' . $i . ' - Liên kết')->defaultValue('#')
                );
        }

        return $form;
    });

    if (is_plugin_active('ecommerce')) {
        add_shortcode(
            'featured-product-categories',
            __('Featured Product Categories'),
            __('Featured Product Categories'),
            function ($shortCode) {
                $categories = get_featured_product_categories(['take' => null]);

                return Theme::partial('short-codes.featured-product-categories', [
                    'title' => $shortCode->title,
                    'categories' => $categories,
                ]);
            }
        );

        shortcode()->setAdminConfig('featured-product-categories', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                );
        });

        shortcode()->registerLoadingState('featured-product-categories', Theme::getThemeNamespace('partials.short-codes.featured-product-categories-skeleton'));

        add_shortcode('featured-products', __('Featured products'), __('Featured products'), function ($shortCode) {
            $limit = (int) $shortCode->limit ?: 10;
            
            $products = get_featured_products(['take' => $limit]);

            $products = $limit > 1 ? $products : collect([$products]);

            return Theme::partial('short-codes.featured-products', [
                'title' => $shortCode->title,
                'limit' => $shortCode->limit,
                'products' => $products,
            ]);
        });

        shortcode()->setAdminConfig('featured-products', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'limit',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Limit'))
                        ->placeholder(__('Limit'))
                );
        });

        shortcode()->registerLoadingState('featured-products', Theme::getThemeNamespace('partials.short-codes.featured-products-skeleton'));

        add_shortcode('featured-brands', __('Featured Brands'), __('Featured Brands'), function ($shortCode) {
            $brands = get_featured_brands((int) $shortCode->limit ?: 8);

            return Theme::partial('short-codes.featured-brands', [
                'title' => $shortCode->title,
                'brands' => $brands,
            ]);
        });

        shortcode()->setAdminConfig('featured-brands', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'limit',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Number of brands to display'))
                        ->placeholder(__('Number of brands to display'))
                        ->defaultValue(8)
                );
        });

        shortcode()->registerLoadingState('featured-brands', Theme::getThemeNamespace('partials.short-codes.featured-brands-skeleton'));

        add_shortcode(
            'product-collections',
            __('Product Collections'),
            __('Product Collections'),
            function (Shortcode $shortcode) {
                $productCollections = get_product_collections(
                    ['status' => BaseStatusEnum::PUBLISHED],
                    [],
                    ['id', 'name', 'slug']
                );

                if ($productCollections->isEmpty()) {
                    return null;
                }

                $limit = (int) $shortcode->limit ?: 8;

                $products = get_products_by_collections([
                    'collections' => [
                        'by' => 'id',
                        'value_in' => [$productCollections->first()->id],
                    ],
                    'take' => $limit,
                    'with' => EcommerceHelper::withProductEagerLoadingRelations(),
                ]);

                return Theme::partial('short-codes.product-collections', [
                    'title' => $shortcode->title,
                    'productCollections' => $productCollections,
                    'limit' => $limit,
                    'products' => $products,
                ]);
            }
        );

        shortcode()->setAdminConfig('product-collections', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'limit',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Limit'))
                        ->placeholder(__('Limit'))
                        ->defaultValue(8)
                );
        });

        shortcode()->registerLoadingState('product-collections', Theme::getThemeNamespace('partials.short-codes.product-collections-skeleton'));

        add_shortcode(
            'product-category-products',
            __('Product category products'),
            __('Product category products'),
            function (Shortcode $shortcode) {
                $category = ProductCategory::query()
                    ->where([
                        'status' => BaseStatusEnum::PUBLISHED,
                        'id' => (int) $shortcode->category_id,
                    ])
                    ->with([
                        'children' => function ($query) {
                            return $query
                                ->wherePublished()
                                ->orderBy('order')
                                ->limit(3);
                        },
                    ])
                    ->first();

                if (! $category) {
                    return null;
                }

                $limit = (int) $shortcode->limit ?: 8;

                $products = app(ProductInterface::class)->getProductsByCategories([
                    'categories' => [
                        'by' => 'id',
                        'value_in' => array_merge([$category->id], $category->activeChildren->pluck('id')->all()),
                    ],
                    'take' => $limit,
                ]);

                return Theme::partial('short-codes.product-category-products', compact('category', 'products', 'limit'));
            }
        );

        shortcode()->setAdminConfig('product-category-products', function (array $attributes) {
            $categories = ProductCategory::query()
                ->wherePublished()
                ->orderBy('order')->oldest()
                ->get()
                ->pluck('name', 'id')
                ->all();

            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'category_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Select category'))
                        ->choices($categories)
                )
                ->add(
                    'number_of_categories',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Limit number of categories'))
                        ->placeholder(__('Default: 3'))
                        ->defaultValue(3)
                )
                ->add(
                    'limit',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Limit number of products'))
                        ->placeholder(__('Unlimited by default'))
                );
        });

        shortcode()->registerLoadingState('product-category-products', Theme::getThemeNamespace('partials.short-codes.product-category-products-skeleton'));

        add_shortcode('trending-products', __('Trending Products'), __('Trending Products'), function ($shortCode) {
            $products = get_trending_products([
                    'take' => (int) $shortCode->limit ?: 10,
                ]);

            return Theme::partial('short-codes.trending-products', [
                'title' => $shortCode->title,
                'products' => $products,
            ]);
        });

        shortcode()->setAdminConfig('trending-products', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'limit',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Limit'))
                        ->placeholder(__('Limit'))
                        ->defaultValue(10)
                );
        });

        shortcode()->registerLoadingState('trending-products', Theme::getThemeNamespace('partials.short-codes.trending-products-skeleton'));

        add_shortcode('flash-sale', __('Flash sale'), __('Flash sale'), function ($shortCode) {
            if (! $shortCode->flash_sale_id) {
                return null;
            }

            $flashSale = FlashSale::query()
                ->notExpired()
                ->where('id', $shortCode->flash_sale_id)
                ->wherePublished()
                ->with([
                    'products' => function ($query) {
                        return $query
                            ->wherePublished()
                            ->with(EcommerceHelper::withProductEagerLoadingRelations());
                    },
                ])
                ->first();

            if (! $flashSale) {
                return null;
            }

            return Theme::partial('short-codes.flash-sale', [
                'title' => $shortCode->title,
                'flashSale' => $flashSale,
            ]);
        });

        shortcode()->setAdminConfig('flash-sale', function ($attributes) {
            $flashSales = FlashSale::query()
                ->wherePublished()
                ->notExpired()
                ->get()
                ->pluck('name', 'id')
                ->all();

            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'flash_sale_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Select a flash sale'))
                        ->choices($flashSales)
                );
        });

        shortcode()->registerLoadingState('flash-sale', Theme::getThemeNamespace('partials.short-codes.flash-sale-skeleton'));
    }

    if (is_plugin_active('simple-slider')) {
        add_filter(SIMPLE_SLIDER_VIEW_TEMPLATE, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.sliders';
        }, 120);

        add_filter(SHORTCODE_REGISTER_CONTENT_IN_ADMIN, function ($data, $key, $attributes) {
            if ($key == 'simple-slider') {
                $ads = app(AdsInterface::class)->getModel()
                    ->where('status', BaseStatusEnum::PUBLISHED)
                    ->notExpired()
                    ->get()
                    ->pluck('name', 'key')
                    ->all();

                $maxAds = 2;

                $form = ShortcodeForm::createFromArray($attributes);

                for ($i = 1; $i <= $maxAds; $i++) {
                    $form->add(
                        'ads_' . $i,
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Ad :number', ['number' => $i]))
                            ->choices(['' => __('-- select --')] + $ads)
                    );
                }

                return $data . $form->renderForm();
            }

            return $data;
        }, 50, 3);

        function get_ads_keys_from_shortcode(Shortcode $shortcode): array
        {
            $keys = collect($shortcode->toArray())
                ->sortKeys()
                ->filter(function ($value, $key) use ($shortcode) {
                    return Str::startsWith($key, 'ads_') ||
                        ($shortcode->name == 'theme-ads' && Str::startsWith($key, 'key_'));
                });

            return array_filter($keys->toArray() + [$shortcode->ads]);
        }
    }

    if (is_plugin_active('newsletter')) {
        add_shortcode('newsletter-form', __('Newsletter Form'), __('Newsletter Form'), function ($shortCode) {
            return Theme::partial('short-codes.newsletter-form', [
                'title' => $shortCode->title,
                'description' => $shortCode->description,
                'subtitle' => $shortCode->subtitle,
            ]);
        });

        shortcode()->setAdminConfig('newsletter-form', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Subtitle'))
                        ->placeholder(__('Subtitle'))
                        ->rows(3)
                )
                ->add(
                    'description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Description'))
                        ->placeholder(__('Description'))
                        ->rows(3)
                );
        });
    }

    add_shortcode('download-app', __('Download Apps'), __('Download Apps'), function ($shortCode) {
        return Theme::partial('short-codes.download-app', [
            'title' => $shortCode->title,
            'description' => $shortCode->description,
            'subtitle' => $shortCode->subtitle,
            'screenshot' => $shortCode->screenshot,
            'androidAppUrl' => $shortCode->android_app_url,
            'iosAppUrl' => $shortCode->ios_app_url,
        ]);
    });

    shortcode()->setAdminConfig('download-app', function ($attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
            )
            ->add(
                'subtitle',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Subtitle'))
                    ->placeholder(__('Subtitle'))
                    ->rows(3)
            )
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Description'))
                    ->placeholder(__('Description'))
                    ->rows(3)
            )
            ->add(
                'screenshot',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Screenshot'))
            )
            ->add(
                'android_app_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Android app URL'))
                    ->placeholder(__('Android app URL'))
            )
            ->add(
                'ios_app_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('iOS app URL'))
                    ->placeholder(__('iOS app URL'))
            );
    });

    if (is_plugin_active('faq')) {
        add_shortcode('faq', __('FAQs'), __('FAQs'), function ($shortCode) {
            $categories = FaqCategory::query()
                ->wherePublished()
                ->with([
                    'faqs' => function ($query): void {
                        $query->wherePublished();
                    },
                ])
                ->orderBy('order', 'ASC')->latest()
                ->get();

            return Theme::partial('short-codes.faq', [
                'title' => $shortCode->title,
                'categories' => $categories,
            ]);
        });

        shortcode()->setAdminConfig('faq', function ($attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->placeholder(__('Title'))
                );
        });
    }

    add_shortcode('site-features', __('Site features'), __('Site features'), function ($shortcode) {
        return Theme::partial('short-codes.site-features', compact('shortcode'));
    });

    shortcode()->setAdminConfig('site-features', function ($attributes) {
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading();

        for ($i = 1; $i < 6; $i++) {
            $form->add(
                'icon' . $i,
                CoreIconField::class,
                CoreIconFieldOption::make()
                    ->label(__('Icon :number', ['number' => $i]))
            )
            ->add(
                'title' . $i,
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title :number', ['number' => $i]))
                    ->placeholder(__('Title :number', ['number' => $i]))
            )
            ->add(
                'subtitle' . $i,
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Subtitle :number', ['number' => $i]))
                    ->placeholder(__('Subtitle :number', ['number' => $i]))
            );
        }

        return $form;
    });

    if (is_plugin_active('contact') && defined('CONTACT_FORM_TEMPLATE_VIEW')) {
        \Botble\Contact\Forms\ShortcodeContactAdminConfigForm::beforeRendering(function ($form): void {
            $form->add('style', SelectField::class, SelectFieldOption::make()
                ->label('Giao diện liên hệ')
                ->choices(['default' => 'Liên hệ mặc định', 'consultation' => 'Đặt lịch tư vấn phong thủy'])
                ->defaultValue('default'));
        });

        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
        }, 120);
    }

    add_shortcode('contact-info-boxes', __('Contact info boxes'), __('Contact info boxes'), function ($shortCode) {
        return Theme::partial('short-codes.contact-info-boxes', ['title' => $shortCode->title]);
    });

    shortcode()->setAdminConfig('contact-info-boxes', function ($attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
            );
    });

    if (is_plugin_active('ads')) {
        add_shortcode('theme-ads', __('Theme ads'), __('Theme ads'), function ($shortCode) {
            $ads = [];
            $attributes = $shortCode->toArray();

            for ($i = 1; $i < 5; $i++) {
                if (isset($attributes['key_' . $i]) && ! empty($attributes['key_' . $i])) {
                    $ad = AdsManager::displayAds((string) $attributes['key_' . $i]);
                    if ($ad) {
                        $ads[] = $ad;
                    }
                }
            }

            $ads = array_filter($ads);

            return Theme::partial('short-codes.theme-ads', compact('ads'));
        });

        shortcode()->setAdminConfig('theme-ads', function ($attributes) {
            $ads = app(AdsInterface::class)->getModel()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->notExpired()
                ->get()
                ->pluck('name', 'key')
                ->all();

            $form = ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading();

            for ($i = 1; $i < 5; $i++) {
                $form->add(
                    'key_' . $i,
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Ad :number', ['number' => $i]))
                        ->choices(['' => __('-- Select --')] + $ads)
                        ->searchable()
                );
            }

            return $form;
        });
    }

    add_shortcode('coming-soon', __('Coming soon'), __('Coming soon'), function ($shortCode) {
        return Theme::partial('short-codes.coming-soon', [
            'time' => $shortCode->time,
            'image' => $shortCode->image,
        ]);
    });

    shortcode()->setAdminConfig('coming-soon', function ($attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'time',
                DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(__('Time'))
                    ->placeholder(__('Time'))
            )
            ->add(
                'image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image'))
            );
    });
});
