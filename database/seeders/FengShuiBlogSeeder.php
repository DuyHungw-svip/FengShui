<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class FengShuiBlogSeeder extends Seeder
{
    public function run(): void
    {
        Theme::uses('martfury');

        if (! is_plugin_active('blog')) {
            throw new RuntimeException('The Blog plugin must be active.');
        }

        $page = Page::query()->whereHas('slugable', fn ($query) => $query->where('key', 'home'))->firstOrFail();
        $patterns = [
            'BlogFengShui' => '/\[BlogFengShui(?=\s|\])[^\]]*\]/u',
            'BlogFengShui2' => '/\[BlogFengShui2(?=\s|\])[^\]]*\]/u',
        ];
        $attributes = [];

        foreach ($patterns as $name => $pattern) {
            if (preg_match_all($pattern, $page->content, $matches) !== 1) {
                throw new RuntimeException('Home must contain exactly one ' . $name . ' shortcode.');
            }
            $attributes[$name] = shortcode()->getCompiler()->getAttributes($matches[0][0]);
        }

        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);
        $backup = $backupDirectory . '/home-blog-before-import-' . now()->format('Ymd-His') . '-' . Str::uuid() . '.json';
        File::put($backup, json_encode([
            'page' => $page->getAttributes(),
            'posts' => DB::table('posts')->get(),
            'categories' => DB::table('categories')->get(),
            'post_categories' => DB::table('post_categories')->get(),
            'slugs' => DB::table('slugs')->whereIn('reference_type', [Post::class, Category::class])->get(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        chmod($backup, 0600);
        $this->command?->info('Backup: ' . $backup);

        DB::transaction(function () use ($page, $patterns, $attributes): void {
            $authorId = User::query()->orderBy('id')->value('id');
            $selections = [];

            foreach ($this->articles() as $article) {
                [$name, $prefix, $title, $description, $categoryName, $defaultImage] = $article;
                $values = $attributes[$name];
                $field = $prefix . '_post_id';
                $post = ! empty($values[$field]) ? Post::query()->find($values[$field]) : null;

                if (! $post) {
                    $title = ($values[$prefix . '_title'] ?? '') ?: $title;
                    $existingDescription = $values[$prefix . '_description'] ?? '';
                    $normalized = str_replace(['“', '”'], '"', trim($existingDescription));
                    if ($existingDescription && ! in_array($normalized, $this->legacyDescriptions(), true)) {
                        $description = $existingDescription;
                    }
                    $image = $values[$prefix . '_image'] ?? $defaultImage;
                    $categoryName = $values[$prefix . ($name === 'BlogFengShui' ? '_label' : '_category')] ?? $categoryName;

                    $post = Post::query()->where('name', $title)->first();
                    if (! $post) {
                        $image = str_starts_with($image, 'theme:') ? $this->importThemeImage(substr($image, 6)) : $image;
                        $post = Post::query()->create([
                            'name' => $title, 'description' => $description,
                            'content' => '<p>' . e($description) . '</p>', 'image' => $image,
                            'status' => 'published', 'is_featured' => $prefix === 'featured',
                            'author_id' => $authorId, 'author_type' => User::class,
                        ]);
                        $category = Category::query()->firstOrCreate(['name' => $categoryName], [
                            'status' => 'published', 'author_id' => $authorId, 'author_type' => User::class,
                        ]);
                        if (! $category->slugable) {
                            SlugHelper::createSlug($category);
                        }
                        $post->categories()->syncWithoutDetaching([$category->id]);
                    }
                }

                if (! $post->slugable) {
                    SlugHelper::createSlug($post);
                }
                $selections[$name][$field] = $post->id;
                $this->command?->info($post->id . ': ' . $post->name);
            }

            $content = $page->content;
            foreach ($patterns as $name => $pattern) {
                $content = preg_replace_callback($pattern, function (array $matches) use ($selections, $name): string {
                    $opening = $matches[0];
                    foreach ($selections[$name] as $field => $id) {
                        $attributePattern = '/\s+' . preg_quote($field, '/') . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s\]]+)/u';
                        $opening = preg_replace($attributePattern, '', $opening);
                        $opening = substr($opening, 0, -1) . ' ' . $field . '="' . $id . '"]';
                    }

                    return $opening;
                }, $content);
            }
            if ($content !== $page->content) {
                $page->update(['content' => $content]);
            }
        });
    }

    private function importThemeImage(string $filename): string
    {
        $url = 'feng-shui-blog/' . $filename;
        if ($file = MediaFile::query()->where('url', $url)->first()) {
            return $file->url;
        }
        $result = RvMedia::uploadFromPath(theme_path('martfury/public/img/' . $filename), 0, 'feng-shui-blog');
        if ($result['error'] ?? true) {
            throw new RuntimeException('Cannot import existing theme image: ' . $filename);
        }

        return $result['data']->url;
    }

    private function legacyDescriptions(): array
    {
        return [
            'Bài văn khấn mộc mạc đúng truyền thống Việt, cùng danh mục ngũ quả và thủ tục hỏa lò tụ sinh...',
            'Nguyên tắc "Sơn quản nhân đinh, Thủy quản tài lộc" và những cung vị tuyệt đối không nên kích hoạt...',
            'Tránh quay lưng ra cửa, đón nhận ánh sáng tự nhiên và cách chọn điểm tựa vững chãi để minh mẫn...',
        ];
    }

    private function articles(): array
    {
        return [
            ['BlogFengShui', 'card_1', 'Mệnh & Ngũ Hành',
                'Phân định màu sắc tương hợp, chất liệu bổ trợ, xác định hướng Cát - Hung chuẩn xác theo Cung phi chính tông.',
                'Hạp Thể Tương Sinh', 'theme:blog-feng-shui-bell.png'],
            ['BlogFengShui', 'card_2', 'Tử Vi Bát Tự',
                'An sao lập bàn 12 cung chức vị: Mệnh, Phu, Phúc, Điền, Quan, Nô... Giải mã đại hạn 10 năm và lưu niên cát triệu.',
                'Đẩu Số Mệnh Bàn', ''],
            ['BlogFengShui', 'card_3', 'Ngày Tốt Vạn Sự',
                'Chọn giờ Hoàng đạo, ngày Trực Kiến - Trừ - Mãn cho hỷ sự, động thổ, xuất hành và lễ nghi khánh thành hưng nghiệp.',
                'Lịch Trực Khai Hoa', ''],
            ['BlogFengShui', 'card_4', 'Bát Trạch Nhà Ở',
                'Bố trí Minh đường, tọa hướng Môn - Táo - Thờ, hóa giải loan đầu hình sát bên ngoài và tụ dưỡng nội khí trường.',
                'Bát Trạch Trực Nghiệm', 'theme:blog-feng-shui-compass.png'],
            ['BlogFengShui2', 'featured', 'Bố Trí Không Gian Sống Hài Hòa Âm Dương: Hóa Giải Góc Nhọn Xung Sát (Tiễn Xạ)',
                'Các cạnh nhọn của tủ kệ, dầm ngang bê tông hay hướng nhọn của công trình đối diện trực xung thẳng vào vị trí giường nằm hoặc bàn trà sinh hoạt tạo nên trường khí bất ổn, khiến gia đạo dễ phát sinh nóng giận và hao tán sinh lực.',
                'Thực Địa Ứng Dụng', 'theme:home-feng-shui-hero.png'],
            ['BlogFengShui2', 'article_1', 'Nghi Lễ Tạ Thần Linh & An Vị Gia Tiên',
                'Bài văn khấn mộc mạc đúng truyền thống Việt, cùng danh mục ngũ quả, lễ hương đăng và thủ tục an vị để gia đạo khởi đầu yên ổn.',
                'Nhập Trạch - Khởi Lễ', 'theme:blog-feng-shui-bell.png'],
            ['BlogFengShui2', 'article_2', 'Vị Trí Đặt Tiểu Cảnh Nước Chuẩn Phong Thủy',
                'Nguyên tắc "Sơn quản nhân đinh, Thủy quản tài lộc" giúp chọn vị trí hồ nước, dòng chảy và ánh sáng để kích hoạt sinh khí vừa đủ.',
                'Minh Đường Tụ Thủy', 'theme:blog-feng-shui-compass.png'],
            ['BlogFengShui2', 'article_3', 'Tựa Sơn Nghinh Thủy: Quy Cách Bàn Làm Việc',
                'Tránh quay lưng ra cửa, đón nhận ánh sáng tự nhiên và chọn điểm tựa vững chãi để tinh thần ổn định, công việc hanh thông.',
                'Công Danh Thuận Buồm', 'theme:home-feng-shui-hero.png'],
            ['BlogFengShui2', 'article_4', 'Thanh Lọc Khí Trường: Cách Sắp Đặt Góc Thiền',
                'Chọn góc yên tĩnh, ánh sáng dịu và vật phẩm cân bằng để giảm nhiễu khí, giúp không gian nghỉ ngơi sâu hơn và tinh thần ổn định.',
                'Tĩnh Tâm An Cư', 'theme:blog-feng-shui-compass.png'],
        ];
    }
}
