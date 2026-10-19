@php
    $featuredImage = RvMedia::getImageUrl($featured->image);
    $featuredDescription = $featured->description ?: Str::limit(strip_tags($featured->content ?? ''), 300);
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
                <a class="blog-feng-shui-2__featured-image" href="{{ $featured->url }}" style="--blog-feng-shui-2-featured-bg: url('{{ $featuredImage }}');">
                    <span>{!! BaseHelper::clean($shortcode->featured_badge ?: 'Kỳ Thư Tháng Này') !!}</span>
                </a>

                <div class="blog-feng-shui-2__featured-body">
                    <div class="blog-feng-shui-2__meta">
                        @if ($featured->author_name)
                            <span>{{ $featured->author_name }}</span> •
                        @endif
                        <span>{{ Theme::formatDate($featured->created_at) }}</span>
                        @if ($featured->time_reading)
                            • <span>{{ $featured->time_reading }} phút đọc</span>
                        @endif
                        • <span>{{ number_format($featured->views) }} lượt xem</span>
                    </div>

                    <h3>
                        <a href="{{ $featured->url }}">{!! BaseHelper::clean($featured->name) !!}</a>
                    </h3>

                    <p>{!! BaseHelper::clean($featuredDescription) !!}</p>

                    <a class="blog-feng-shui-2__read-more" href="{{ $featured->url }}">
                        <span>{!! BaseHelper::clean($shortcode->featured_action_label ?: 'Đọc giải pháp chi tiết') !!}</span>
                        {!! BaseHelper::renderIcon('ti ti-book') !!}
                    </a>
                </div>
            </article>

            <div class="blog-feng-shui-2__side-list">
                @foreach ($articles as $article)
                    <article class="blog-feng-shui-2__side-card">
                        <a class="blog-feng-shui-2__side-image" href="{{ $article->url }}">
                            <img src="{{ RvMedia::getImageUrl($article->image) }}" alt="{{ $article->name }}" loading="lazy">
                        </a>

                        <div class="blog-feng-shui-2__side-content">
                            <span>{!! BaseHelper::clean($article->firstCategory?->name ?: __('Blog')) !!}</span>
                            <h3>
                                <a href="{{ $article->url }}">{!! BaseHelper::clean($article->name) !!}</a>
                            </h3>
                            <p>{!! BaseHelper::clean($article->description ?: Str::limit(strip_tags($article->content ?? ''), 220)) !!}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
