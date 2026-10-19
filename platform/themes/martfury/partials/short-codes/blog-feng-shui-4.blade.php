@php
    $contactUrl = trim($content['contact_url']);
    $scheme = parse_url($contactUrl, PHP_URL_SCHEME);
    $allowedLink = $scheme
        ? in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true)
        : str_starts_with($contactUrl, '/') || str_starts_with($contactUrl, '#');
    $contactUrl = $allowedLink ? $contactUrl : url('/contact');
@endphp

<section class="blog-feng-shui-4">
    <div class="blog-feng-shui-4__inner">
        <div class="blog-feng-shui-4__gallery">
            @foreach ($pictures as $number => $picture)
                <figure class="blog-feng-shui-4__picture is-picture-{{ $number }}">
                    <img
                        src="{{ $picture['image'] ? RvMedia::getImageUrl($picture['image']) : Theme::asset()->url($picture['fallback']) }}"
                        alt="{{ $picture['alt'] }}"
                        loading="lazy"
                        decoding="async"
                    >
                    @if ($picture['caption'])
                        <figcaption>{{ $picture['caption'] }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>

        <div class="blog-feng-shui-4__copy">
            @if ($content['eyebrow'])
                <p class="blog-feng-shui-4__eyebrow">{{ $content['eyebrow'] }}</p>
            @endif
            <h2 class="blog-feng-shui-4__title">{{ $content['title'] }}</h2>
            <p class="blog-feng-shui-4__description">{{ $content['description'] }}</p>

            @if ($features)
                <ul class="blog-feng-shui-4__features">
                    @foreach ($features as $feature)
                        <li>
                            <span class="blog-feng-shui-4__feature-icon" aria-hidden="true">
                                {!! BaseHelper::renderIcon($feature['icon']) !!}
                            </span>
                            <p>
                                @if ($feature['title'])
                                    <strong>{{ $feature['title'] }}:</strong>
                                @endif
                                {{ $feature['description'] }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($content['contact_label'])
                <a class="blog-feng-shui-4__contact" href="{{ $contactUrl }}">
                    <span>{{ $content['contact_label'] }}</span>
                    {!! BaseHelper::renderIcon('ti ti-arrow-right') !!}
                </a>
            @endif
        </div>
    </div>
</section>
