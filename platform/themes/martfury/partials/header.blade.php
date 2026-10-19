{!! Theme::partial('header-meta') !!}
    <body {!! Theme::bodyAttributes() !!} @if (Theme::get('pageId')) id="{{ Theme::get('pageId') }}" @endif>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}
        <div id="alert-container"></div>

        @if (theme_option('preloader_enabled', 'no') == 'yes')
            <div id="loader-wrapper">
                <div class="preloader-loading"></div>
                <div class="loader-section section-left"></div>
                <div class="loader-section section-right"></div>
            </div>
        @endif

        {!! Theme::get('topHeader') !!}

        <header class="header header--1 header--feng-shui" data-sticky="{{ Theme::get('stickyHeader', theme_option('sticky_header_enabled', 'yes') == 'yes' ? 'true' : 'false') }}">
            <div class="header__top">
                <div class="ps-container align-items-center">
                    @if (is_plugin_active('ecommerce'))
                        <div class="header__left">
                            <a class="ps-logo" href="{{ BaseHelper::getHomepageUrl() }}">
                                {!! Theme::partial('header-brand') !!}
                            </a>
                        </div>
                        <div class="header__center">
                            <form class="ps-form--quick-search" action="{{ route('public.products') }}" data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                                <div class="form-group--icon">
                                    <div class="product-cat-label">{{ __('All') }}</div>
                                    <select class="form-control product-category-select" name="categories[]" aria-label="{{ __('Product categories') }}">
                                        <option value="0">{{ __('All') }}</option>
                                        {!! ProductCategoryHelper::renderProductCategoriesSelect() !!}
                                    </select>
                                </div>
                                <input class="form-control input-search-product" name="q" type="text" placeholder="{{ __("I'm shopping for...") }}" autocomplete="off">
                                <div class="spinner-icon">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </div>
                                <button type="submit"><i class="icon-magnifier" aria-hidden="true"></i><span>{{ __('Search') }}</span></button>
                                <div class="ps-panel--search-result"></div>
                            </form>
                        </div>
                        <div class="header__right">
                            <div class="header__actions">
                                {!! apply_filters('before_theme_header_actions', null) !!}
                                @if (EcommerceHelper::isCompareEnabled())
                                    <a class="header__extra btn-compare" href="{{ route('public.compare') }}" title="{{ __('Compare products') }}"><i class="icon-chart-bars"></i><span><i>{{ Cart::instance('compare')->count() }}</i></span></a>
                                @endif
                                @if (EcommerceHelper::isWishlistEnabled())
                                    <a class="header__extra btn-wishlist" href="{{ route('public.wishlist') }}" title="{{ __('Wishlist') }}"><i class="icon-heart"></i><span><i>{{ !auth('customer')->check() ? Cart::instance('wishlist')->count() : auth('customer')->user()->wishlist()->count() }}</i></span></a>
                                @endif
                                {!! apply_filters('after_theme_header_actions', null) !!}
                                <div class="ps-block--user-header">
                                    <div class="ps-block__left"><i class="icon-user"></i></div>
                                    <div class="ps-block__right">
                                        <span class="header__account-label">{{ __('Account') }}</span>
                                        <div class="header__account-links">
                                        @if (auth('customer')->check())
                                            <a href="{{ route('customer.overview') }}" class="customer-name">{{ auth('customer')->user()->name }}</a>
                                            <a href="{{ route('customer.logout') }}">{{ __('Logout') }}</a>
                                        @else
                                            <a href="{{ route('customer.login') }}">{{ __('Login') }}</a><a href="{{ route('customer.register') }}">{{ __('Register') }}</a>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <nav class="navigation">
                <div class="ps-container">
                    <div class="navigation__right">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'view'    => 'menu',
                            'options' => ['class' => 'menu'],
                        ]) !!}
                        @if (is_plugin_active('ecommerce'))
                            <ul class="navigation__extra">
                                @if (is_plugin_active('language'))
                                    {!! Theme::partial('language-switcher') !!}
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </nav>
        </header>
        @if (Theme::get('headerMobile'))
            {!! Theme::get('headerMobile') !!}
        @else
            {!! Theme::partial('header-mobile') !!}
        @endif
        @if (Theme::get('showBottomBarMenu', true))
            <div class="navigation--list" @if(theme_option('bottom_bar_menu_show_text', 'yes') != 'yes')data-hide-text="true"@endif style="--bottom-bar-menu-text-font-size: {{ theme_option('bottom_bar_menu_text_font_size', 14) }}px;">
                <div class="navigation__content">
                    <a class="navigation__item ps-toggle--sidebar" href="#menu-mobile"><i class="icon-menu"></i><span> {{ __('Menu') }}</span></a>
                    <a class="navigation__item ps-toggle--sidebar" href="#search-sidebar"><i class="icon-magnifier"></i><span> {{ __('Search') }}</span></a>
                </div>
            </div>
        @endif

        @if (is_plugin_active('ecommerce'))
            <div class="ps-panel--sidebar" id="search-sidebar" style="display: none">
                <div class="ps-panel__header">
                    <form class="ps-form--search-mobile" action="{{ route('public.products') }}" data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                        <div class="form-group--nest position-relative">
                            <input class="form-control input-search-product" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}" type="text" autocomplete="off" placeholder="{{ __('Search something...') }}">
                            <div class="spinner-icon">
                                <i class="fa fa-spin fa-spinner"></i>
                            </div>
                            <button type="submit" title="{{ __('Search') }}"><i class="icon-magnifier"></i></button>
                            <div class="ps-panel--search-result"></div>
                        </div>
                    </form>
                </div>
                <div class="navigation__content"></div>
            </div>
        @endif
        <div class="ps-panel--sidebar" id="menu-mobile" style="display: none">
            <div class="ps-panel__header">
                <h3>{{ __('Menu') }}</h3>
            </div>
            <div class="ps-panel__content">
                {!! Menu::renderMenuLocation('main-menu', [
                    'view'    => 'menu',
                    'options' => ['class' => 'menu--mobile'],
                ]) !!}

                <ul class="menu--mobile menu--mobile-extra">
                    @if (is_plugin_active('ecommerce'))
                        @if (EcommerceHelper::isCompareEnabled())
                            <li><a href="{{ route('public.compare') }}"><i class="icon-chart-bars"></i> <span>{{ __('Compare') }}</span></a></li>
                        @endif
                        @if (EcommerceHelper::isWishlistEnabled())
                            <li><a href="{{ route('public.wishlist') }}"><i class="icon-heart"></i> <span>{{ __('Wishlist') }}</span></a></li>
                        @endif
                    @endif

                    @if (is_plugin_active('language'))
                        @php
                            $supportedLocales = Language::getSupportedLocales();
                        @endphp

                        @if ($supportedLocales && count($supportedLocales) > 1)
                            @php
                                $languageDisplay = setting('language_display', 'all');
                            @endphp
                            <li class="menu-item-has-children">
                                <a href="#">
                                    @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                        {!! language_flag(Language::getCurrentLocaleFlag(), Language::getCurrentLocaleName()) !!}
                                    @endif
                                    @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                        {{ Language::getCurrentLocaleName() }}
                                    @endif
                                </a>
                                <span class="sub-toggle"></span>
                                <ul class="sub-menu">
                                    @foreach ($supportedLocales as $localeCode => $properties)
                                        @if ($localeCode != Language::getCurrentLocale())
                                            <li>
                                                <a href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}">
                                                    @if ($languageDisplay == 'all' || $languageDisplay == 'flag'){!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}@endif
                                                    @if ($languageDisplay == 'all' || $languageDisplay == 'name')<span>{{ $properties['lang_name'] }}</span>@endif
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
