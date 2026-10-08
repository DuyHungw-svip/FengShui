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

    add_shortcode('BlogFengShui', __('BlogFengShui'), __('Blog Feng Shui card section'), function (Shortcode $shortcode) {
        return Theme::partial('short-codes.blog-feng-shui', compact('shortcode'));
    });

    shortcode()->setAdminConfig('BlogFengShui', function (array $attributes) {
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'eyebrow',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Eyebrow'))
                    ->placeholder(__('Eyebrow'))
                    ->defaultValue('Thực Chứng Học Thuật')
            )
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
                    ->defaultValue('Tứ Pháp Bí Truyền Ứng Dụng')
            );

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

        for ($i = 1; $i <= 4; $i++) {
            $form
                ->add(
                    'card_' . $i . '_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Card :number image', ['number' => $i]))
                )
                ->add(
                    'card_' . $i . '_kicker',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Card :number kicker', ['number' => $i]))
                        ->placeholder(__('Card :number kicker', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['kicker'] ?? '')
                )
                ->add(
                    'card_' . $i . '_label',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Card :number label', ['number' => $i]))
                        ->placeholder(__('Card :number label', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['label'])
                )
                ->add(
                    'card_' . $i . '_title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Card :number title', ['number' => $i]))
                        ->placeholder(__('Card :number title', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['title'])
                )
                ->add(
                    'card_' . $i . '_description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Card :number description', ['number' => $i]))
                        ->placeholder(__('Card :number description', ['number' => $i]))
                        ->rows(3)
                        ->defaultValue($cardDefaults[$i]['description'])
                )
                ->add(
                    'card_' . $i . '_action_label',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Card :number action label', ['number' => $i]))
                        ->placeholder(__('Card :number action label', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['action_label'])
                )
                ->add(
                    'card_' . $i . '_url',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Card :number URL', ['number' => $i]))
                        ->placeholder(__('Card :number URL', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['url'])
                )
                ->add(
                    'card_' . $i . '_icon',
                    CoreIconField::class,
                    CoreIconFieldOption::make()
                        ->label(__('Card :number icon', ['number' => $i]))
                        ->defaultValue($cardDefaults[$i]['icon'])
                );
        }

        return $form;
    });

    add_shortcode('BlogFengShui2', __('BlogFengShui2'), __('Blog Feng Shui featured article section'), function (Shortcode $shortcode) {
        return Theme::partial('short-codes.blog-feng-shui-2', compact('shortcode'));
    });

    shortcode()->setAdminConfig('BlogFengShui2', function (array $attributes) {
        $form = ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'eyebrow',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Eyebrow'))
                    ->placeholder(__('Eyebrow'))
                    ->defaultValue('Thực Địa Ứng Dụng')
            )
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
                    ->defaultValue('Cẩm Nang Dịch Lý Nhà Ở Đương Đại')
            )
            ->add(
                'subtitle',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Subtitle'))
                    ->placeholder(__('Subtitle'))
                    ->rows(2)
                    ->defaultValue('Giải pháp hóa giải thế sát, khơi thông điểm nghẽn năng lượng, kiến tạo không gian sống dung hòa sinh thái và tâm an.')
            )
            ->add(
                'featured_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Featured image'))
            )
            ->add(
                'featured_badge',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Featured badge'))
                    ->placeholder(__('Featured badge'))
                    ->defaultValue('Kỳ Thư Tháng Này')
            )
            ->add(
                'featured_meta',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Featured meta'))
                    ->placeholder(__('Featured meta'))
                    ->defaultValue('Đại Việt Dịch Học • 8 phút đọc • 12.400 lượt xem')
            )
            ->add(
                'featured_title',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Featured title'))
                    ->placeholder(__('Featured title'))
                    ->rows(2)
                    ->defaultValue('Bố Trí Không Gian Sống Hài Hòa Âm Dương: Hóa Giải Góc Nhọn Xung Sát (Tiễn Xạ)')
            )
            ->add(
                'featured_description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Featured description'))
                    ->placeholder(__('Featured description'))
                    ->rows(3)
                    ->defaultValue('Các cạnh nhọn của tủ kệ, dầm ngang bê tông hay hướng nhọn của công trình đối diện trực xung thẳng vào vị trí giường nằm hoặc bàn trà sinh hoạt tạo nên trường khí bất ổn, khiến gia đạo dễ phát sinh nóng giận và hao tán sinh lực.')
            )
            ->add(
                'featured_action_label',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Featured action label'))
                    ->placeholder(__('Featured action label'))
                    ->defaultValue('Đọc giải pháp chi tiết')
            )
            ->add(
                'featured_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Featured URL'))
                    ->placeholder(__('Featured URL'))
                    ->defaultValue('#')
            );

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

        for ($i = 1; $i <= 4; $i++) {
            $form
                ->add(
                    'article_' . $i . '_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Article :number image', ['number' => $i]))
                )
                ->add(
                    'article_' . $i . '_category',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Article :number category', ['number' => $i]))
                        ->placeholder(__('Article :number category', ['number' => $i]))
                        ->defaultValue($articleDefaults[$i]['category'])
                )
                ->add(
                    'article_' . $i . '_title',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Article :number title', ['number' => $i]))
                        ->placeholder(__('Article :number title', ['number' => $i]))
                        ->rows(2)
                        ->defaultValue($articleDefaults[$i]['title'])
                )
                ->add(
                    'article_' . $i . '_description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Article :number description', ['number' => $i]))
                        ->placeholder(__('Article :number description', ['number' => $i]))
                        ->rows(2)
                        ->defaultValue($articleDefaults[$i]['description'])
                )
                ->add(
                    'article_' . $i . '_url',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Article :number URL', ['number' => $i]))
                        ->placeholder(__('Article :number URL', ['number' => $i]))
                        ->defaultValue($articleDefaults[$i]['url'])
                );
        }

        return $form;
    });

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
