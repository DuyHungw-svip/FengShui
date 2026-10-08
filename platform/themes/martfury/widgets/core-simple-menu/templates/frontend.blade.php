@if (in_array($sidebar, ['footer_sidebar', 'bottom_footer_sidebar']))
    @if ($items->isNotEmpty())
        <aside class="widget widget_footer feng-shui-footer__menu">
            <h4 class="widget-title">{{ $config['name'] }}</h4>
            <ul class="ps-list--link">
                @foreach ($items as $item)
                    <li>
                        <a href="{{ url((string) $item->url) }}" title="{{ $item->label }}"
                            {!! $item->attributes ? BaseHelper::clean($item->attributes) : null !!}
                            @if ($item->is_open_new_tab) target="_blank" rel="noopener" @endif
                        >{{ $item->label }}</a>
                    </li>
                @endforeach
            </ul>
        </aside>
    @endif
@else
    @include('packages/widget::widgets.core-simple-menu.frontend')
@endif
