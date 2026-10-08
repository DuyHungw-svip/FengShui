@php
    $products = [];

    foreach ($productFengShuiDefaults as $number => $defaults) {
        $product = [];

        foreach ($defaults as $field => $value) {
            $product[$field] = $shortcode->{'product_' . $number . '_' . $field} ?: $value;
        }

        $product['image'] = $product['image']
            ? RvMedia::getImageUrl($product['image'])
            : Theme::asset()->url('img/blog-feng-shui-compass.png');
        $product['url'] = $shortcode->{'product_' . $number . '_url'} ?: '#';
        $products[] = $product;
    }
@endphp

<section class="product-feng-shui">
    <div class="product-feng-shui__inner">
        <div class="product-feng-shui__header">
            <div class="product-feng-shui__heading">
                <span class="product-feng-shui__eyebrow">{{ $shortcode->eyebrow ?: 'Khí Cụ Chiêu Tài • Tĩnh Tâm An Gia' }}</span>
                <h2>{{ $shortcode->title ?: 'Bảo Vật Hộ Thân Trợ Mệnh' }}</h2>
            </div>
            <a class="product-feng-shui__view-all" href="{{ $shortcode->view_all_url ?: '#' }}">
                <span>{{ $shortcode->view_all_label ?: 'Xem toàn bộ bảo khố' }}</span>
                {!! BaseHelper::renderIcon('ti ti-arrow-right') !!}
            </a>
        </div>

        <div class="product-feng-shui__grid">
            @foreach ($products as $product)
                <article class="product-feng-shui__card">
                    <a class="product-feng-shui__image" href="{{ $product['url'] }}" aria-label="{{ $product['title'] }}">
                        <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" loading="lazy" decoding="async" />
                        <span class="product-feng-shui__badge {{ $loop->iteration === 2 ? 'is-gold' : '' }}">{{ $product['badge'] }}</span>
                    </a>

                    <div class="product-feng-shui__body">
                        <div class="product-feng-shui__meta">
                            <span class="product-feng-shui__element">{{ $product['element'] }}</span>
                            <span class="product-feng-shui__price">{{ $product['price'] }}</span>
                        </div>
                        <h3><a href="{{ $product['url'] }}">{{ $product['title'] }}</a></h3>
                        <p>{{ $product['description'] }}</p>

                        <div class="product-feng-shui__footer">
                            <span class="product-feng-shui__note">
                                {!! BaseHelper::renderIcon('ti ti-rosette-discount-check') !!}
                                <span>{{ $product['note'] }}</span>
                            </span>
                            <a class="product-feng-shui__button" href="{{ $product['url'] }}" aria-label="{{ ($shortcode->button_label ?: 'Thỉnh Vật Phẩm') . ' - ' . $product['title'] }}">
                                {{ $shortcode->button_label ?: 'Thỉnh Vật Phẩm' }}
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
