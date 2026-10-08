@if (in_array($sidebar, ['footer_sidebar', 'bottom_footer_sidebar']))
    @if ($config['name'] || $config['content'] || Theme::getLogo())
        <aside class="widget widget_footer feng-shui-footer__about">
            @if (Theme::getLogo())
                <a class="feng-shui-footer__logo" href="{{ BaseHelper::getHomepageUrl() }}" aria-label="{{ __('Home') }}">
                    {!! Theme::getLogoImage(['style' => 'max-height: 80px']) !!}
                </a>
            @endif
            @if ($config['name'])
                <h4 class="widget-title">{!! BaseHelper::clean($config['name']) !!}</h4>
            @endif
            @if ($config['content'])
                <div class="feng-shui-footer__description">{!! BaseHelper::clean(shortcode()->compile($config['content'])) !!}</div>
            @endif
        </aside>
    @endif
@else
    @include('packages/widget::widgets.text.frontend')
@endif
