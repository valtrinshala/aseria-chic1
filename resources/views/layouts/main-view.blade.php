@extends('layouts.sections.commonMaster')
@section('masterContent')
    @php
        if ($isAdmin){
            $location = session()->get('localization_for_changes_data');
            $slugs = [
                'pos_module' => $location->pos ?? false,
                'kitchen_module' => $location->kitchen ?? false,
                'e_kiosk' => $location->e_kiosk ?? false,
            ];
        }else{
             $slugs = [
                'pos_module' => $user->location->pos,
                'kitchen_module' => $user->location->kitchen,
                'e_kiosk' => $user->location->e_kiosk,
            ];
        }
    @endphp
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div id="kt_app_header" class="app-header h-80px" data-kt-sticky="true"
                 data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
                 data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between mx-11 px-0"
                     id="kt_app_header_container">
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px"
                             id="kt_app_sidebar_mobile_toggle">
                            <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                         id="kt_app_header_wrapper">
                        <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
                             data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                             data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
                             data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
                             data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                             data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                            <div
                                class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
                                id="kt_app_header_menu" data-kt-menu="true">
                                <div data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                     data-kt-menu-placement="bottom-start"
                                     class="menu-item menu-lg-down-accordion me-0 me-lg-2">
                                    <span>
                                        @if ($isAdmin)
                                            @if ($location)
                                                <span class="fs-2 menu-title">{{ $location['name'] }}</span>
                                            @else
                                                <span class="fs-2 menu-title">{{ __('All locations') }}</span>
                                            @endif
                                        @else
                                            <span class="menu-title fs-2">{{ __($user->location?->name) }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="app-navbar flex-shrink-0">
                            <div class="app-navbar-item ms-1 ms-md-4 flex-column-auto">
                                @if ($isAdmin)
                                    <div>
                                        <button type="button" class="btn btn-secondary px-6 me-2 h-45px"
                                                data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                data-kt-menu-placement="bottom-end">
                                            <svg class="me-5" xmlns="http://www.w3.org/2000/svg" width="18"
                                                 height="18" viewBox="0 0 25 25">
                                                <path id="map_FILL0_wght300_GRAD0_opsz48"
                                                      d="M156.551-794.46l-9.065-3.215-5.9,2.34a1.026,1.026,0,0,1-1.067-.042,1.055,1.055,0,0,1-.521-.961v-18.543a1.324,1.324,0,0,1,.224-.754,1.27,1.27,0,0,1,.607-.484l6.654-2.34,9.065,3.18,5.862-2.326a1.112,1.112,0,0,1,1.067.033.973.973,0,0,1,.521.916v18.832a.955.955,0,0,1-.244.653,1.432,1.432,0,0,1-.605.4Zm-.915-2v-17.55l-7.271-2.49v17.55Zm1.6,0,5.161-1.707v-17.8l-5.161,1.955Zm-15.635-.521,5.161-1.969V-816.5L141.6-814.77Zm15.635-17.029v0Zm-10.474-2.49v0Z"
                                                      transform="translate(-140.001 818.46)" fill="#264653"/>
                                            </svg>
                                            {{ __('Locations') }}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                 viewBox="0 0 11 6">
                                                <path id="Path_6" data-name="Path 6"
                                                      d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                      transform="translate(-0.009 -0.033)" fill="#264653"/>
                                            </svg>
                                        </button>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                             data-kt-menu="true">
                                            <div class="card card-body w-auto">
                                                <div class="menu-item">
                                                    @foreach (\App\Helpers\Helpers::locations() as $eachLocation)
                                                        <div class="p-2"
                                                             @if ($location) @if ($eachLocation->id == $location['id']) style="background-color: #F1F1F4;" @endif
                                                            @endif>
                                                            <a
                                                                href="{{ route('settings.set.location.for.admin', ['locale' => $eachLocation->id]) }}">{{ $eachLocation->name }}</a>
                                                        </div>
                                                    @endforeach
                                                    <div class="p-2"
                                                         @if (!$location) style="background-color: #F1F1F4;" @endif>
                                                        <a
                                                            href="{{ route('settings.set.location.for.admin', ['locale' => null]) }}">{{ __('All locations') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <button type="button" class="btn btn-secondary px-3 h-45px"
                                        data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                        data-kt-menu-placement="bottom-end">
                                    <svg class="me-5" xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 25 25">
                                        <g id="language" transform="translate(-8677 -6517)">
                                            <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                                  transform="translate(8677 6517)" fill="none"/>
                                            <path id="language_FILL0_wght300_GRAD0_opsz48"
                                                  d="M112-836a11.713,11.713,0,0,1-4.7-.946,12.208,12.208,0,0,1-3.816-2.566,11.84,11.84,0,0,1-2.557-3.82,11.986,11.986,0,0,1-.93-4.719,11.815,11.815,0,0,1,.93-4.687,11.9,11.9,0,0,1,2.557-3.8,11.859,11.859,0,0,1,3.816-2.541A11.994,11.994,0,0,1,112-860a11.994,11.994,0,0,1,4.7.921,11.859,11.859,0,0,1,3.816,2.541,11.9,11.9,0,0,1,2.557,3.8,11.815,11.815,0,0,1,.93,4.687,11.986,11.986,0,0,1-.93,4.719,11.84,11.84,0,0,1-2.557,3.82,12.208,12.208,0,0,1-3.816,2.566A11.713,11.713,0,0,1,112-836Zm0-1.37a12.242,12.242,0,0,0,1.938-2.757,15.916,15.916,0,0,0,1.246-3.483h-6.35a16.275,16.275,0,0,0,1.239,3.477A11.9,11.9,0,0,0,112-837.369Zm-2-.258a13.215,13.215,0,0,1-1.57-2.681,17.08,17.08,0,0,1-1.075-3.3h-5.028a10.883,10.883,0,0,0,3.046,3.867A12.362,12.362,0,0,0,110-837.626Zm4.04-.019a11.277,11.277,0,0,0,4.484-2.106,11.061,11.061,0,0,0,3.158-3.858H116.67a22.423,22.423,0,0,1-1.139,3.3A14.8,14.8,0,0,1,114.037-837.646Zm-12.175-7.4h5.228q-.119-.865-.153-1.592t-.034-1.415q0-.765.044-1.448t.163-1.477h-5.247a8.742,8.742,0,0,0-.33,1.419,10.765,10.765,0,0,0-.1,1.506,11.506,11.506,0,0,0,.1,1.547A8.923,8.923,0,0,0,101.862-845.043Zm6.7,0h6.9q.138-.955.182-1.625t.044-1.382q0-.68-.044-1.325t-.182-1.6h-6.9q-.138.955-.182,1.6t-.044,1.325q0,.712.044,1.382T108.561-845.043Zm8.332,0h5.247a8.923,8.923,0,0,0,.33-1.46,11.506,11.506,0,0,0,.1-1.547,10.765,10.765,0,0,0-.1-1.506,8.743,8.743,0,0,0-.33-1.419h-5.215q.107,1.045.151,1.7t.044,1.223q0,.707-.053,1.389T116.893-845.043Zm-.243-7.365h5.028a10.425,10.425,0,0,0-3.089-3.953,10.528,10.528,0,0,0-4.585-2.022,14.068,14.068,0,0,1,1.549,2.678,18.969,18.969,0,0,1,1.1,3.3Zm-7.815,0h6.381a14.31,14.31,0,0,0-1.247-3.364A13.026,13.026,0,0,0,112-858.59a8.511,8.511,0,0,0-1.809,2.436A19.775,19.775,0,0,0,108.836-852.408Zm-6.513,0h5.048a17.657,17.657,0,0,1,1.042-3.224,15.1,15.1,0,0,1,1.552-2.733,10.541,10.541,0,0,0-4.55,2.009A10.668,10.668,0,0,0,102.323-852.408Z"
                                                  transform="translate(8576.999 7376.999)" fill="#000"/>
                                        </g>
                                    </svg>
                                    {{ __('Language') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                         viewBox="0 0 11 6">
                                        <path id="Path_6" data-name="Path 6"
                                              d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                              transform="translate(-0.009 -0.033)" fill="#264653"/>
                                    </svg>
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto" data-kt-menu="true">
                                    <div class="card card-body w-auto">
                                        <div class="menu-item">
                                            @foreach (\App\Helpers\Helpers::languages() as $language)
                                                <div class="p-2"
                                                     @if ($language->locale == app()->getLocale()) style="background-color: #F1F1F4;" @endif>
                                                    <a
                                                        href="{{ route('set.language.storage', ['language' => $language->id]) }}">{{ $language->name }}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="app-navbar-item ms-1 ms-md-4 flex-column-auto" id="kt_header_user_menu_toggle">
                                <div class="cursor-pointer symbol symbol-40px"
                                     data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                     data-kt-menu-placement="bottom-end">
                                    <img loading="lazy" src="{{ $external_settings['second_image'] }}" class="rounded-3"
                                         alt="user"/>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                         viewBox="0 0 11 6">
                                        <path id="Path_6" data-name="Path 6"
                                              d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                              transform="translate(-0.009 -0.033)" fill="#264653"/>
                                    </svg>
                                </div>
                                <div
                                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                                    data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <img alt="Logo" src="{{ $external_settings['second_image'] }}"/>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">{{ $user->name }}
                                                </div>
                                                <a href="#"
                                                   class="fw-semibold text-muted text-hover-primary fs-7">{{ $user->email }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-item px-5">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn text-dark p-0">{{ __('Sign Out') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
                                <div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-48px"
                                     id="kt_app_header_menu_toggle">
                                    <i class="ki-duotone ki-element-4 fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <div id="kt_app_sidebar" style="background-color: #252525" class="app-sidebar flex-column"
                     data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
                     data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                     data-kt-drawer-width="225px" data-kt-drawer-direction="start"
                     data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                    <div class="app-sidebar-logo mx-xxl-5 ps-1" id="kt_app_sidebar_logo">
                        <a class="m-0" id="firstIcon" href="{{ url('/') }}"><img alt="Logo"
                                                                                 src="{{ $external_settings['image'] }}"
                                                                                 class="h-40px rounded me-2"/></a>
                        <a class="m-0" style="display:none;" id="secondIcon" href="{{ url('/') }}"><img alt="Logo"
                                                                                                        src="{{ $external_settings['second_image'] }}"
                                                                                                        class="h-40px w-35px rounded me-2"/></a>
                        <div id="kt_app_sidebar_toggle" style="transform: rotate(90deg)"
                             class="app-sidebar-toggle btn btn-icon p-3 btn-shadow btn-sm btn-color-muted"
                             data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                             data-kt-toggle-name="app-sidebar-minimize">
                            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="9"
                                     viewBox="0 0 18 11">
                                    <path id="Path_6" data-name="Path 6"
                                          d="M.4.454a1.3,1.3,0,0,1,1.912,0L9.009,7.565,15.7.454a1.3,1.3,0,0,1,1.912,0,1.5,1.5,0,0,1,0,2.032L9.965,10.612a1.3,1.3,0,0,1-1.912,0L.4,2.485A1.5,1.5,0,0,1,.4.454Z"
                                          transform="translate(-0.099 -0.033)" fill="#264653"/>
                                </svg>
                            </i>
                        </div>
                    </div>
                    @php $per = $user->userRole['permissions'] @endphp
                    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                        <div id="kt_app_sidebar_menu_wrapper" style="background-color: #252525"
                             class="app-sidebar-wrapper">
                            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-0" data-kt-scroll="true"
                                 data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                                 data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                                 data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                                 data-kt-scroll-save-state="false">
                                @foreach (\App\Helpers\Helpers::categories()['menu'] as $menu)
                                    <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
                                         id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                                        @if ($menu['menuHeader'] && (count(array_intersect($menu['permission'], $per)) != 0 || $isAdmin))
                                            @if(array_key_exists('display', $menu))
                                                @if($slugs['pos_module'] || $slugs['kitchen_module'])
                                                    <div class="menu-item mt-4">
                                                        <div class="menu-content pb-0">
                                                        <span
                                                            class="menu-heading text-light fs- fw-bold">{{ $menu['menuHeader'] }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="menu-item mt-4">
                                                    <div class="menu-content pb-0">
                                                        <span
                                                            class="menu-heading text-light fs- fw-bold">{{ $menu['menuHeader'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        @foreach ($menu['menu'] as $category)
                                            @if (isset($category['submenu']))
                                                @if((array_intersect(array_column($category['submenu'], 'slug'), $user->userRole->permissions) || $isAdmin) && $slugs['e_kiosk'])
                                                        @php
                                                            $currentUri = Route::current()->uri;
                                                            $uriParts = explode('/', $currentUri);

                                                            $part1 = $uriParts[0] ?? '';
                                                            $part2 = $uriParts[1] ?? '';
                                                            $part3 = $uriParts[2] ?? '';

                                                            $fullPath = '/' . $part1 . '/' . $part2;
                                                            if ($part3 !== '') {
                                                                $fullPath .= '/' . $part3;
                                                            }
                                                        @endphp
                                                        <div data-kt-menu-trigger="click"
                                                         class="menu-item @if (in_array($fullPath, array_column($category['submenu'], 'url'))) hover show @endif">
                                                    <span class="menu-link ps-xxl-5 navbar-on-hover">
                                                        {!! $category['icon'] !!}
                                                        <span
                                                            class="menu-title text-light ms-3">{{ $category['name'] }}</span>
                                                        <span class="menu-arrow"></span>
                                                    </span>
                                                        <div class="menu-sub menu-sub-accordion">
                                                            @foreach ($category['submenu'] as $submenu)
                                                                @if($isAdmin || in_array($submenu['slug'], $user->userRole->permissions))
                                                                    <div
                                                                        class="navbar-on-hover text-light menu-item {{ $submenu['url'] == '/' . Route::current()->uri || $submenu['url'] == '/' . explode('/', Route::current()->uri)[0] . '/' . explode('/', Route::current()->uri)[1] ? 'active-navbar-link' : '' }}">
                                                                        <a class="menu-link active w-100 remind-scroll-position"
                                                                           href="{{ $submenu['url'] }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                            <span
                                                                                class="menu-title text-light {{--{{ $submenu['url'] == '/' . Route::current()->uri || '/' . explode('/', Route::current()->uri)[0] . '/' . explode('/', Route::current()->uri)[1] == $submenu['url'] ? 'text-primary' : 'text-light' }}--}}">{{ $submenu['name'] }}</span>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                @php
                                                    $isActive = isset($category['url']) && ($category['url'] == '/' . Route::current()->uri || explode('/', Route::current()->uri)[0] . '/' . explode('/', Route::current()->uri)[1] == $category['url']);
                                                @endphp

                                                @if($isAdmin)
                                                    @if(!(isset($slugs[$category['slug']]) && !$slugs[$category['slug']]) || $slugs[$category['slug']])
                                                        <div data-kt-menu-trigger="click" class="navbar-on-hover menu-item text-light here show menu-accordion {{ $isActive ? 'active-navbar-link' : '' }}">
                                                            <div class="ms-xxl-5 menu-sub-accordion">
                                                                <a class="my-2 w-100 remind-scroll-position" href="{{ url($category['url']) }}">
                                                                    @if(isset($category['url']))
                                                                        {!! $category['icon'] !!}
                                                                    @endif
                                                                    <span class="menu-title ms-3 text-light">{{ $category['name'] }}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if(isset($slugs[$category['slug']]) && $slugs[$category['slug']] && in_array($category['slug'], $user->userRole->permissions) || !isset($slugs[$category['slug']]) && in_array($category['slug'], $user->userRole->permissions))
                                                        <div data-kt-menu-trigger="click" class="navbar-on-hover menu-item text-light here show menu-accordion {{ $isActive ? 'active-navbar-link' : '' }}">
                                                            <div class="ms-xxl-5 menu-sub-accordion">
                                                                <a class="my-2 w-100 remind-scroll-position" href="{{ url($category['url']) }}">
                                                                    @if(isset($category['url']))
                                                                        {!! $category['icon'] !!}
                                                                    @endif
                                                                    <span class="menu-title ms-3 text-light">{{ $category['name'] }}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                                <div id="sidebarText" class="my-5 mx-0"
                                     data-kt-scroll-dependencies="#kt_app_sidebar_logo,">
                                    <span class="text-white ps-6 fw-light">{{ __('Version') }} 1.0.0</span>
                                    <br>
                                    <span class="text-white ps-6 fw-light">©  {{ __('2024, Aseria.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main" style="background-color: #ededed7a">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <script>
                    const navbarScrollContainer = document.getElementById("kt_app_sidebar_menu_scroll");

                    // Function to save scroll position
                    function saveScrollPos() {
                        localStorage.setItem("navbarScrollPos", navbarScrollContainer.scrollTop);
                    }

                    // Function to restore scroll position
                    function restoreScrollPos() {
                        const savedScrollPos = localStorage.getItem("navbarScrollPos");
                        if (savedScrollPos !== null) {
                            navbarScrollContainer.scrollTop = parseInt(savedScrollPos, 10);
                        }
                    }

                    // Save scroll position on scroll
                    navbarScrollContainer.addEventListener("scroll", saveScrollPos);

                    // Restore scroll position on page load
                    restoreScrollPos();

                    // Save scroll position when a link with class 'remind-scroll-position' is clicked
                    document.querySelectorAll('.remind-scroll-position').forEach(function(link) {
                        link.addEventListener('click', function() {
                            saveScrollPos();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
