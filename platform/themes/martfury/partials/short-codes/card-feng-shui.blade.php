<section class="card-feng-shui">
    <div class="card-feng-shui__inner">
        <header class="card-feng-shui__header">
            @if ($heading['eyebrow'])
                <p class="card-feng-shui__eyebrow">{{ $heading['eyebrow'] }}</p>
            @endif
            <h2 class="card-feng-shui__title">{{ $heading['title'] }}</h2>
            @if ($heading['subtitle'])
                <p class="card-feng-shui__subtitle">{{ $heading['subtitle'] }}</p>
            @endif
        </header>

        <div class="card-feng-shui__grid">
            @foreach ($cards as $card)
                <article class="card-feng-shui__card">
                    @if ($card['icon'])
                        <div class="card-feng-shui__icon" aria-hidden="true">{!! BaseHelper::renderIcon($card['icon']) !!}</div>
                    @endif
                    <h3>{{ $card['title'] }}</h3>
                    <p class="card-feng-shui__description">{{ $card['description'] }}</p>
                    @if (filled($card['note']))
                        <p class="card-feng-shui__note">
                            {!! BaseHelper::renderIcon('ti ti-circle-check') !!}
                            <span>{{ $card['note'] }}</span>
                        </p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
