<?php

namespace Database\Seeders;

use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FengShuiContactPageSeeder extends Seeder
{
    public function run(): void
    {
        if (! is_plugin_active('contact')) {
            throw new \RuntimeException('The contact plugin must be enabled.');
        }

        $slug = Slug::query()->where('reference_type', Page::class)->where('key', 'contact')->firstOrFail();
        $page = Page::query()->findOrFail($slug->reference_id);
        $content = '<shortcode>[contact-form style="consultation" display_fields="phone,email" mandatory_fields="phone"][/contact-form]</shortcode>';

        if ($page->content === $content) {
            return;
        }

        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);
        $path = $directory.'/contact-page-before-redesign-'.now()->format('Ymd-His-u').'.json';
        File::put($path, json_encode($page->getRawOriginal(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        chmod($path, 0600);

        $page->update(['content' => $content]);
    }
}
