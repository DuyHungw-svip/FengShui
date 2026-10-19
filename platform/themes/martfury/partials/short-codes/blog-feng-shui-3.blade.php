@php
    $image = $content['image'] ? RvMedia::getImageUrl($content['image']) : Theme::asset()->url('img/blog-feng-shui-compass.png');
    $safeLink = static function (string $value, string $fallback): string {
        $value = trim($value);
        $scheme = parse_url($value, PHP_URL_SCHEME);
        $allowed = $scheme
            ? in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true)
            : str_starts_with($value, '/') || str_starts_with($value, '#');

        return $allowed ? $value : $fallback;
    };
@endphp

<section class="blog-feng-shui-3">
    <div class="blog-feng-shui-3__inner">
        <div class="blog-feng-shui-3__intro">
            <div class="blog-feng-shui-3__copy">
                @if ($content['eyebrow'])
                    <p class="blog-feng-shui-3__eyebrow"><span aria-hidden="true"></span>{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="blog-feng-shui-3__title">{{ $content['title'] }}</h2>
                <p class="blog-feng-shui-3__description">{{ $content['description'] }}</p>

                @if ($content['quote_title'] || $content['quote_description'])
                    <div class="blog-feng-shui-3__quote">
                        <span class="blog-feng-shui-3__quote-icon" aria-hidden="true">{!! BaseHelper::renderIcon('ti ti-yin-yang') !!}</span>
                        <div><h3>{{ $content['quote_title'] }}</h3><p>{{ $content['quote_description'] }}</p></div>
                    </div>
                @endif

                <div class="blog-feng-shui-3__actions">
                    @if ($content['primary_label'])
                        <a class="blog-feng-shui-3__button is-primary" href="{{ $safeLink($content['primary_url'], url('/contact')) }}">
                            {!! BaseHelper::renderIcon('ti ti-book') !!}<span>{{ $content['primary_label'] }}</span>
                        </a>
                    @endif
                    @if ($content['secondary_label'])
                        <a class="blog-feng-shui-3__button is-secondary" href="{{ $safeLink($content['secondary_url'], url('/blog')) }}">
                            {!! BaseHelper::renderIcon('ti ti-sitemap') !!}<span>{{ $content['secondary_label'] }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="blog-feng-shui-3__visual">
                <figure class="blog-feng-shui-3__figure">
                    <img src="{{ $image }}" alt="{{ $content['image_alt'] }}" loading="lazy" decoding="async">
                    @if ($content['image_eyebrow'] || $content['image_caption'])
                        <figcaption><span>{{ $content['image_eyebrow'] }}</span><p>{{ $content['image_caption'] }}</p></figcaption>
                    @endif
                </figure>
                @if ($content['badge_title'] || $content['badge_description'])
                    <div class="blog-feng-shui-3__badge">
                        <span class="blog-feng-shui-3__badge-icon" aria-hidden="true">{!! BaseHelper::renderIcon('ti ti-rosette-discount-check') !!}</span>
                        <div><strong>{{ $content['badge_title'] }}</strong><p>{{ $content['badge_description'] }}</p></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="blog-feng-shui-3__stats">
            @foreach ($stats as $stat)
                <article class="blog-feng-shui-3__stat">
                    <div class="blog-feng-shui-3__stat-heading">{!! BaseHelper::renderIcon($stat['icon']) !!}<span>{{ $stat['label'] }}</span></div>
                    <h3>{{ $stat['value'] }}</h3>
                    <p>{{ $stat['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
