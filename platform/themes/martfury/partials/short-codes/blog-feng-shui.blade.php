<section class="blog-feng-shui">
    <div class="blog-feng-shui__inner">
        <div class="blog-feng-shui__header">
            <span>{!! BaseHelper::clean($shortcode->eyebrow ?: 'Thực Chứng Học Thuật') !!}</span>
            <h2>{!! BaseHelper::clean($shortcode->title ?: 'Tứ Pháp Bí Truyền Ứng Dụng') !!}</h2>
        </div>

        <div class="blog-feng-shui__grid">
            @foreach ($posts as $slot => $post)
                @php
                    $image = $post->image ? RvMedia::getImageUrl($post->image) : null;
                    $category = $post->firstCategory?->name ?: __('Blog');
                    $description = $post->description ?: Str::limit(strip_tags($post->content ?? ''), 220);
                    $kicker = $shortcode->{'card_' . $slot . '_kicker'};
                    $icon = $shortcode->{'card_' . $slot . '_icon'} ?: 'ti ti-book';
                @endphp
                <article class="blog-feng-shui__card {{ $image ? 'is-photo' : 'is-icon' }} {{ $slot === 3 ? 'is-wine' : 'is-gold' }}">
                    <div
                        class="blog-feng-shui__visual"
                        @if ($image)
                            style="--blog-feng-shui-card-bg: url('{{ $image }}');"
                        @endif
                    >
                        @if ($image)
                            <div class="blog-feng-shui__visual-shade"></div>
                        @endif

                        @if ($kicker)
                            <span class="blog-feng-shui__kicker">{!! BaseHelper::clean($kicker) !!}</span>
                        @endif

                        @unless ($image)
                            <div class="blog-feng-shui__icon">
                                {!! BaseHelper::renderIcon($icon) !!}
                            </div>
                        @endunless

                        <span class="blog-feng-shui__label">{!! BaseHelper::clean($category) !!}</span>
                    </div>

                    <div class="blog-feng-shui__body">
                        <h3><a href="{{ $post->url }}" style="color: inherit; font: inherit;">{!! BaseHelper::clean($post->name) !!}</a></h3>
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    </div>

                    <a class="blog-feng-shui__action" href="{{ $post->url }}">
                        <span>{!! BaseHelper::clean($shortcode->{'card_' . $slot . '_action_label'} ?: 'Đọc bài viết') !!}</span>
                        {!! BaseHelper::renderIcon('ti ti-arrow-right') !!}
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
