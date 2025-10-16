@extends('layouts.main-pos')
@section('title', 'POS')
@section('page-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--<script src="{{ asset('assets/js/custom-js.js') }}"></script>-->
    @vite('resources/assets/js/custom/apps/pos/custom.js')
    @vite('resources/assets/js/custom/apps/pos/z-report.js')
    @vite('resources/assets/js/custom/apps/utils.js')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
@endsection

<style>
    /* This style is added here due to continuos change, incase we have to get it back */
    *:not(.flatpickr-day):not([class*="swal"]:not(.btn)), [class*="round"], .rounded-start, .rounded-end, .rounded-start-2, .rounded-bottom-0 {
        border-radius: 0 !important;
    }
    .flatpickr-day, .flatpickr-weekday {
        font-size: 0.8rem !important;
    }
 
    .flatpickr-current-month input.cur-year.cur-year.cur-year {
        font-size: 0.9rem !important;
    }
    body.nopicker .flatpickr-calendar.open {
        display: none;
    }
  
</style>
@php
    $api_mappings = [
        'drinkId' => ['label' => __('Drinks'), 'key' => 'drinkId', 'element' => 'drink-modifier', 'show' => true, 'config_key' => 'drinks'],
        'drinkHotId' => ['label' => __('Drinks'), 'key' => 'drinkHotId', 'element' => 'drink-modifier', 'show' => false, 'config_key' => 'drinks'],
        'friesId' => ['label' => __('Sides'), 'key' => 'friesId', 'element' => 'fries-modifier', 'show' => true, 'config_key' => 'fries'],
        'sauceId' => ['label' => __('Sauce'), 'key' => 'sauceId', 'element' => 'sauce-modifier', 'show' => true, 'config_key' => 'sauces']
    ];

    $tax_key_filtered = [];
    foreach($taxes as $tax) {
        $tax_key_filtered[$tax->type] = $tax;
    }

    $apis = config('constants.api');
    $configured_apis = [];
    foreach($apis as $index => $api) {
        $check_index = $index;
        if ($check_index == 'friesId') $check_index = 'null';
        if ($check_index == 'sideId') $check_index = 'friesId';

        if (array_key_exists($check_index, $api_mappings)) {
            $val = $api_mappings[$check_index]['label'];
            $key = $api_mappings[$check_index]['key'];
            $config_key = $api_mappings[$check_index]['config_key'];
            $show = $api_mappings[$check_index]['show'];
            $element_key = $api_mappings[$check_index]['element'];

            $configured_apis[] = [
                'id' => $api,
                'key' => $check_index,
                'value' => $val,
                'element' => $element_key,
                'show' => $show,
                'config_key' => $config_key
            ];
        }
    }
@endphp

@section('content')
    <style>
        .custom-modal {
            opacity: 1;
            transition: opacity .5s ease-in;
        }

        .custom-modal.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #2C1D4D;
            z-index: 1030;
            margin: 0;
            padding: 0;
        }

        .custom-modal.flex-switch {
            display: flex;
        }

        .custom-modal.hiding {
            opacity: 0;
        }

        .custom-modal.hidden {
            display: none;
        }
    </style>
    <style>
        @php
    $charMax = 0;
    $extra = 0;

    if (isset($dealCategory)) {
        $charMax = strlen($dealCategory->name);
        $extra = 1;
    }

    foreach($categories as $category) {
        $charMax += strlen($category->name);
    }
    @endphp
    #myList{
            --w: max(100%, calc({{$charMax*.5 * 20}}px + calc({{(count($categories) + $extra) * .5}} * 31px)));
            width: var(--w); max-width: var(--w);
        }

        .list-scrolling { overflow-x: auto; }
    </style>
<input type="hidden" id="cash_register_id" value="{{ session()->get('cash_register') }}">
    <div class="d-flex flex-column flex-root app-root w-100" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container h-100 ps-4">
                                <div class="d-flex flex-xl-row h-100">
                                    <div class="flex-row-fluid me-9 mb-0 d-flex categories mt-5 flex-column">
                                        <div class="d-flex justify-content-between mb-3 searchbar ">
                                            <div>
                                                <h3 class="font-heading">{{ $user->name }}</h3>
                                                <h3 class="font-span">{{ __("Cash register") }}: {{ session()->get('cash_register_data')->name  }}</h3>
                                            </div>
                                            <div class="input-group  search-form justify-content-end h-73px  w-65">
                                                <button class="ps-3 cs-border btn btn-outline-secondary bg-white border-end-0 search-icon pr-0 ms-n5" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <input type="search" class="ps-1 form-control rounded bg-white search-product" placeholder="{{ __('Search products by name') }}" aria-label="Search" aria-describedby="search-addon" />

                                            </div>

                                        </div>
                                        <div class=" card-flush card-p-0 bg-transparent border-0 flex-grow-1">
                                            <div class="card-body container-fluid row">
                                                <div class="list-scrolling mb-4">
                                                    <ul id="myList" class="nav nav-pills category-holder d-flex nav-pills-custom gap-2">
                                                        @if (isset($dealCategory))
                                                            <li color="{{ $dealCategory->color }}" class="d-flex nav-item me-0 category-deals">
                                                                <a class="justify-content-center kategorite nav-link white-box-bg   px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" data-bs-toggle="pill" href="#kt_pos_food_content_1" style="width: max-content;height: 50px; padding: 1px !important;" data-category-id="deals">

                                                            <span class="text-gray-800  font-heading cat-item">
                                                                <span class="cat-image">
                                                                    {!! $dealCategory->getImage() !!}
                                                                    {{--
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                                        <path id="fastfood_FILL1_wght300_GRAD0_opsz48" d="M68.848-875.553a4.663,4.663,0,0,1,2.58-4.293,11.45,11.45,0,0,1,5.743-1.461,11.441,11.441,0,0,1,5.749,1.461,4.664,4.664,0,0,1,2.574,4.293Zm0,3.618v-1.473H85.493v1.473Zm.819,3.629a.794.794,0,0,1-.579-.238.775.775,0,0,1-.24-.572v-.663H85.493v.663a.784.784,0,0,1-.235.572.789.789,0,0,1-.585.238Zm17.734,0v-7.486a6.463,6.463,0,0,0-2.007-4.783,9.2,9.2,0,0,0-4.852-2.514L80-887.271h5.714v-5.035H87.04v5.035h5.808l-1.861,17.463a1.6,1.6,0,0,1-.551,1.08,1.67,1.67,0,0,1-1.133.421Z" transform="translate(-68.848 892.306)"/>
                                                                    </svg>
                                                                    --}}
                                                                </span>
                                                                <span class="cat-name">
                                                                    {{ $dealCategory->name }}
                                                                </span>
                                                            </span>
                                                                </a>
                                                            </li>
                                                        @endif

                                                        @foreach($categories as $category)
                                                            <li color="{{ $category['color'] }}" class="d-flex nav-item me-0 category-{{ $category->id }} ">
                                                                <a class="justify-content-center kategorite nav-link white-box-bg   px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" data-bs-toggle="pill" href="#kt_pos_food_content_1" style="width: max-content;height: 50px; padding: 1px !important;" data-category-id="{{ $category->id }}">

                                                            <span class="text-gray-800  font-heading cat-item">
                                                                <span class="cat-image">
                                                                    {!! $category->getImage() !!}
                                                                    {{-- <img height="24" src="{{ $category->getImage() }}" alt=""> --}}
                                                                </span>
                                                                <span class="cat-name">
                                                                    {{ $category->name }}
                                                                </span>
                                                            </span>

                                                                    {{-- <span class="text-gray-500 fw-semibold fs-7">8 Options</span>--}}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="tab-content ms-4 product-clickable-list">
                                                    <div class="tab-pane fade show active" id="kt_pos_food_content_1">
                                                        <div class="overflow-auto food-list d-flex flex-wrap d-grid gap-2 gap-xxl-9 all-products">
                                                            <div class="card card-flush flex-row-fluid p-6 pb-5 mw-100">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-0 w-100 align-items-end d-flex flex-equal mb-4 down-buttons" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                            <label class="open-drawer btn-color-gray-600 btn-active-text-gray-800 white-box-bg btn bottom-button col-5 btn-active-text-gray-800  px-2" data-kt-button="true">

                                                <!--
                                                <svg id="euro-circle_duoline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <path id="Path_21" data-name="Path 21" d="M12,22.2A10.2,10.2,0,1,0,1.8,12,10.2,10.2,0,0,0,12,22.2ZM12,24A12,12,0,1,0,0,12,12,12,0,0,0,12,24Z" fill="#5d4bdf" fill-rule="evenodd"/>
                                                    <path id="Path_22" data-name="Path 22" d="M12.8,14.4a5.7,5.7,0,0,1,9.775-2.271.9.9,0,1,1-1.35,1.191A3.9,3.9,0,0,0,14.7,14.4h2.4a.9.9,0,0,1,0,1.8H14.4V18h2.7a.9.9,0,0,1,0,1.8H14.7a3.9,3.9,0,0,0,6.526,1.079.9.9,0,1,1,1.35,1.191A5.7,5.7,0,0,1,12.8,19.8H11.1a.9.9,0,0,1,0-1.8h1.5V16.2H11.1a.9.9,0,0,1,0-1.8Z" transform="translate(-5.1 -5.1)" fill="#5d4bdf"/>
                                                </svg>
                                                -->
                                                <svg class="primary-children" width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.22672 17.4115C8.22672 19.0833 9.21631 19.3854 11.9976 19.3854C14.7788 19.3854 15.7684 19.0885 15.7684 17.4115C15.7684 17.1146 16.0132 16.8698 16.3101 16.8698H23.6017L18.2267 6.27083H12.1278V11.6875L14.1798 9.63542C14.3934 9.42188 14.7319 9.42188 14.9455 9.63542C15.159 9.84896 15.159 10.1875 14.9455 10.401L11.9663 13.3854C11.9194 13.4323 11.8517 13.474 11.7892 13.5C11.7215 13.526 11.6538 13.5417 11.5809 13.5417C11.508 13.5417 11.4403 13.526 11.3726 13.5C11.3048 13.474 11.2423 13.4323 11.1955 13.3854L8.21631 10.401C8.00277 10.1875 8.00277 9.84896 8.21631 9.63542C8.42985 9.42188 8.76839 9.42188 8.98193 9.63542L11.034 11.6875V6.27083H5.76839L0.393392 16.8698H7.68506C7.98193 16.8698 8.22672 17.1146 8.22672 17.4115Z" fill="#5d4bdf"/>
                                                    <path d="M16.8257 17.9583C16.508 20.474 13.9194 20.474 11.9976 20.474C10.0757 20.474 7.49235 20.474 7.16943 17.9583H0.0444336V24.0521C0.0444336 24.849 0.695475 25.5 1.49235 25.5H22.508C23.3049 25.5 23.9559 24.849 23.9559 24.0521V17.9583H16.8257Z" fill="#5d4bdf"/>
                                                    <path d="M12.1382 1.04167C12.1382 0.744792 11.8934 0.5 11.5965 0.5C11.2996 0.5 11.0548 0.744792 11.0548 1.04167V6.27083H12.1434V1.04167H12.1382Z" fill="#5d4bdf"/>
                                                </svg>


                                                <span class="font-span d-block">{{ __('Open drawer') }}</span>
                                            </label>
                                            <label id="notesbtn" class="btn-color-gray-600 btn-active-text-gray-800 white-box-bg btn  bottom-button col-5   px-2 " data-kt-button="true">

                                                <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24">
                                                    <g id="notes" transform="translate(-3 -4.5)">
                                                        <path id="Path_16" data-name="Path 16" d="M10.5,14.25H26.571V16.1H10.5Z" transform="translate(-3.036 -3.75)" fill="#5d4bdf"/>
                                                        <path id="Path_17" data-name="Path 17" d="M10.5,22.5H26.571v1.846H10.5Z" transform="translate(-3.036 -6.923)" fill="#5d4bdf"/>
                                                        <path id="Path_18" data-name="Path 18" d="M10.5,30.75h8.482V32.6H10.5Z" transform="translate(-3.036 -10.096)" fill="#5d4bdf"/>
                                                        <path id="Path_19" data-name="Path 19" d="M28,4.5H3v24H28ZM26.214,26.654H4.786V6.346H26.214Z" fill="#5d4bdf"/>
                                                    </g>
                                                </svg>
                                                <span class="font-span d-block">{{ __('Notes') }}</span>
                                            </label>
                                            <label id="morebtn" class="btn-color-gray-600 btn-active-text-gray-800 white-box-bg btn bottom-button col-5  btn-active-text-gray-800  px-2 " data-kt-button="true">
                                                <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24">
                                                    <g id="apps" transform="translate(-0.8 -0.6)">
                                                        <path id="Subtraction_4" data-name="Subtraction 4" d="M23.868,24H21.434a1.956,1.956,0,0,1-1.456-.633,2.033,2.033,0,0,1-.62-1.487V19.395a2.132,2.132,0,0,1,.624-1.52,1.918,1.918,0,0,1,1.452-.656h2.434a2,2,0,0,1,1.485.66A2.084,2.084,0,0,1,26,19.395V21.88a2,2,0,0,1-.642,1.482A2.055,2.055,0,0,1,23.868,24Zm-2.735-4.912v3.1H24.17v-3.1ZM14.188,24H11.812a2.138,2.138,0,0,1-2.132-2.12V19.395a2.133,2.133,0,0,1,.622-1.52,1.983,1.983,0,0,1,1.51-.656h2.377a1.982,1.982,0,0,1,1.51.656,2.134,2.134,0,0,1,.623,1.52V21.88A2.139,2.139,0,0,1,14.188,24Zm-2.678-4.912v3.1h2.98v-3.1ZM4.566,24H2.133a2.054,2.054,0,0,1-1.49-.637A1.991,1.991,0,0,1,0,21.88V19.395A2.087,2.087,0,0,1,.647,17.88a2.011,2.011,0,0,1,1.486-.66H4.566a1.922,1.922,0,0,1,1.454.656,2.133,2.133,0,0,1,.623,1.52V21.88a2.032,2.032,0,0,1-.62,1.487A1.957,1.957,0,0,1,4.566,24ZM1.831,19.088v3.1H4.867v-3.1Zm22.037-3.7H21.434a1.955,1.955,0,0,1-1.456-.631,2.106,2.106,0,0,1-.62-1.546V10.786a2.1,2.1,0,0,1,.62-1.544,1.952,1.952,0,0,1,1.456-.633h2.434A2.185,2.185,0,0,1,26,10.786v2.427a2.183,2.183,0,0,1-2.132,2.177Zm-2.735-4.911v3.042H24.17V10.479ZM14.188,15.39H11.812a2.019,2.019,0,0,1-1.513-.631,2.107,2.107,0,0,1-.619-1.546V10.786A2.1,2.1,0,0,1,10.3,9.242a2.016,2.016,0,0,1,1.513-.633h2.377a2.016,2.016,0,0,1,1.513.633,2.1,2.1,0,0,1,.62,1.544v2.427a2.107,2.107,0,0,1-.62,1.546A2.019,2.019,0,0,1,14.188,15.39Zm-2.678-4.911v3.042h2.98V10.479ZM4.566,15.39H2.133a2.053,2.053,0,0,1-1.49-.636A2.057,2.057,0,0,1,0,13.213V10.786a2.055,2.055,0,0,1,.643-1.54,2.05,2.05,0,0,1,1.49-.637H4.566a1.953,1.953,0,0,1,1.456.633,2.1,2.1,0,0,1,.62,1.544v2.427a2.106,2.106,0,0,1-.62,1.546A1.956,1.956,0,0,1,4.566,15.39ZM1.831,10.479v3.042H4.867V10.479Zm22.037-3.7H21.434a1.919,1.919,0,0,1-1.452-.657,2.132,2.132,0,0,1-.624-1.52V2.118a2.037,2.037,0,0,1,.62-1.487A1.955,1.955,0,0,1,21.434,0h2.434a2.053,2.053,0,0,1,1.49.636A2,2,0,0,1,26,2.118V4.6a2.085,2.085,0,0,1-.648,1.515A2,2,0,0,1,23.868,6.781Zm-2.735-4.97v3.1H24.17v-3.1Zm-6.945,4.97H11.812a1.983,1.983,0,0,1-1.51-.657A2.134,2.134,0,0,1,9.679,4.6V2.118A2.038,2.038,0,0,1,10.3.631,2.019,2.019,0,0,1,11.812,0h2.377A2.019,2.019,0,0,1,15.7.631a2.038,2.038,0,0,1,.62,1.487V4.6a2.134,2.134,0,0,1-.623,1.52A1.983,1.983,0,0,1,14.188,6.781Zm-2.678-4.97v3.1h2.98v-3.1ZM4.566,6.781H2.133A2.012,2.012,0,0,1,.647,6.119,2.087,2.087,0,0,1,0,4.6V2.118A1.991,1.991,0,0,1,.643.636,2.053,2.053,0,0,1,2.133,0H4.566A1.956,1.956,0,0,1,6.023.631a2.037,2.037,0,0,1,.62,1.487V4.6a2.133,2.133,0,0,1-.623,1.52A1.923,1.923,0,0,1,4.566,6.781ZM1.831,1.811v3.1H4.867v-3.1Z" transform="translate(0.8 0.6)" fill="#5d4bdf"/>
                                                    </g>
                                                </svg>
                                                <span class="font-span d-block">{{ __('More') }}</span>
                                            </label>
                                            <label class="white-box-bg btn bottom-button btn-active-text-gray-800 px-2" data-kt-button="true">
                                                <button href="#" class="h-100 w-100 justify-content-center btn clear-button fs-7 fw-bold py-4 d-flex align-items-center border-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                        <g id="close-outline" transform="translate(-3 -3)">
                                                            <path id="Path_11" data-name="Path 11" d="M13,3A10,10,0,1,0,23,13,9.938,9.938,0,0,0,13,3Zm0,18.571A8.571,8.571,0,1,1,21.571,13,8.6,8.6,0,0,1,13,21.571Z" fill="#e76f51"/>
                                                            <path id="Path_12" data-name="Path 12" d="M22.357,23.5,18.5,19.643,14.643,23.5,13.5,22.357,17.357,18.5,13.5,14.643,14.643,13.5,18.5,17.357,22.357,13.5,23.5,14.643,19.643,18.5,23.5,22.357Z" transform="translate(-5.5 -5.5)" fill="#e76f51"/>
                                                        </g>
                                                    </svg>
                                                    <span class="ps-2">{{ __('Clear All') }}</span>
                                                </button>
                                            </label>
                                            <label class="btn border-active-primary flex-basis-0 p-0 bottom-button">
                                                <button class="btn purple-bg w-100 py-4 font-span save-button inactive text-white">{{ __('Save Order') }}</button>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex-row-auto w-xl-450px d-flex flex-column vh-100 position-relative">
                                        <div class="card first-element-card card-flush bg-body mb-3 sticky-top flex-grow-1" id="kt_pos_form">
                                            @if(false)
                                                <div class="card-header px-5 pt-5">
                                                    <h3 class="card-title order-title fs-2qx flex-column mt-2 justify-content-end">{{ __('Order ID') }}:<span class="mt-1 order-number fw-bold"></span></h3></br>
                                                    <div class="card-toolbar">
                                                        <a href="#" class="btn clear-button fs-7 fw-bold py-4 d-flex align-items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                                <g id="close-outline" transform="translate(-3 -3)">
                                                                    <path id="Path_11" data-name="Path 11" d="M13,3A10,10,0,1,0,23,13,9.938,9.938,0,0,0,13,3Zm0,18.571A8.571,8.571,0,1,1,21.571,13,8.6,8.6,0,0,1,13,21.571Z" fill="#e76f51"/>
                                                                    <path id="Path_12" data-name="Path 12" d="M22.357,23.5,18.5,19.643,14.643,23.5,13.5,22.357,17.357,18.5,13.5,14.643,14.643,13.5,18.5,17.357,22.357,13.5,23.5,14.643,19.643,18.5,23.5,22.357Z" transform="translate(-5.5 -5.5)" fill="#e76f51"/>
                                                                </g>
                                                            </svg>
                                                            <span class="ps-2">{{ __('Clear All') }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="justify-content-center d-flex px-5 pt-5 eat-options">
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn font-span fw-bold py-4 take-away-border {{ $location->take_away ? 'take-away' : 'disabled' }}  purble-border text-violet">{{ __('Take away') }}</a>
                                                </div>
                                                <div class="card-toolbar">
                                                    <a href="#" id="tablebtn" class="btn font-span fw-bold py-4 dine-in-border {{ $location->dine_in ? 'dine-in' : 'disabled' }}  purble-border text-violet">{{ __('Dine in') }}</a>
                                                </div>
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn font-span fw-bold py-4 delivery-border {{ $location->delivery ? 'delivery ' : 'disabled' }}  purble-border text-violet">{{ __('Delivery') }}</a>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 px-5 h-0-im">
                                                <div class="table-responsive mb-8 position-relative table-radius">
                                                    <table class="position-sticky top-0 bg-white w-100 z-index-99 align-middle gs-0 my-0">
                                                        <thead>
                                                        <tr class="h-3">
                                                            <th class="font-span fw-bold invisible">{{ __('Edit') }}</th>
                                                            <th class="font-span min-w-175px item-name fw-bold">{{ __('Item name') }}</th>
                                                            <th class="font-span w-125px fw-bold text-start">{{ __('Quantity') }}</th>
                                                            <th class="font-span w-follow fw-bold text-end pe-4">{{ __('Price') }}</th>
                                                            <th class="font-span fw-bold invisible d-none">{{ __('Delete') }}</th>
                                                        </tr>
                                                        </thead>
                                                    </table>

                                                    <table id="thetable" class="w-100 align-middle gs-0  my-0">
                                                        <tbody class="order-table py-5 overflow-y-auto" id="orderTableBody">


                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="box-bg box-radius p-5 pt-0 mt-auto">
                                            <div class="d-flex flex-stack  rounded-3 py-4">
                                                <div class="fs-6 ">
                                                    <span class="d-block font-span ">{{ __('Discounts') }} <span class="discount-area"></span></span>
                                                    <div class="tax-list mb-2">
                                                        @if (false)
                                                            <span class="d-block font-span">{{ __('Tax') }} <span class="tax-area">{{ isset($tax_key_filtered['take_away']) ? number_format($tax_key_filtered['take_away']->tax_rate, 2, '.', '') : '0.00'  }}%</span></span>
                                                        @endif
                                                    </div>
                                                    <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                                                </div>
                                                <div class="fs-6 text-end">
                                                    <span class="d-block font-span discount-area-amount">{{ __('No Discounts') }}</span>
                                                    <div class="tax-list-amount mb-2">
                                                        @if (false)
                                                            <span class="d-block font-span tax-area-amount">&nbsp;</span>
                                                        @endif
                                                    </div>
                                                    <span id="totalSpan" class="d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">$00.00</span>
                                                </div>
                                            </div>
                                            <div class="m-0">

                                                <div class="d-flex flex-equal gap-3 px-0" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                                                    <label class=" white-box-bg btn print-last-receipt btn-color-gray-600 btn-active-text-gray-800 py-0 discounts-buttons px-2 " data-kt-button="true">

                                                        <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                            <g id="print" transform="translate(-4.5 -4.5)">
                                                                <path id="Path_13" data-name="Path 13" d="M25.269,10.5H7.846A3.346,3.346,0,0,0,4.5,13.846v9.115a3.231,3.231,0,0,0,3.231,3.231h.462v2.289a2.326,2.326,0,0,0,2.326,2.326H22.482a2.326,2.326,0,0,0,2.326-2.326V26.192h.462A3.231,3.231,0,0,0,28.5,22.962V13.731A3.231,3.231,0,0,0,25.269,10.5ZM22.962,28.482a.482.482,0,0,1-.48.48H10.518a.482.482,0,0,1-.48-.48V19.288a.482.482,0,0,1,.48-.48H22.482a.482.482,0,0,1,.48.48Z" transform="translate(0 -2.308)" fill="#5d4bdf"/>
                                                                <path id="Path_14" data-name="Path 14" d="M23.906,4.5H13.752a3.236,3.236,0,0,0-3.2,2.769H27.1a3.236,3.236,0,0,0-3.2-2.769Z" transform="translate(-2.329)" fill="#5d4bdf"/>
                                                            </g>
                                                        </svg>
                                                        <span class="font-span d-block">{{ __('Print') }}</span>
                                                    </label>
                                                    <label id="discountbtn" class="white-box-bg @disabled($user->role_id != config('constants.role.adminId') && !in_array('pos_discount_module', $user->userRole->permissions)) btn btn-color-gray-600 btn-active-text-gray-800  discounts-buttons py-0 px-2" data-kt-button="true">
                                                        <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                            <g id="percentage-filled" transform="translate(-6 -6)">
                                                                <path id="Path_23" data-name="Path 23" d="M11,16a5,5,0,1,1,5-5A5,5,0,0,1,11,16Z" fill="#5d4bdf"/>
                                                                <path id="Path_24" data-name="Path 24" d="M6,28.586,28.585,6,30,7.415,7.414,30Z" fill="#5d4bdf"/>
                                                                <path id="Path_25" data-name="Path 25" d="M32,37a5,5,0,1,1,5-5A5,5,0,0,1,32,37Z" transform="translate(-7 -7)" fill="#5d4bdf"/>
                                                            </g>
                                                        </svg>
                                                        <span class="font-span d-block">{{ __('Discount') }}</span>
                                                    </label>
                                                    <label class="btn btn-active-text-gray-800 border-active-primary p-0 w-100 h-69px">
                                                        <button id="proced" class="d-flex justify-content-center gap-1 align-items-center pro-svg btn purple-bg w-100 py-4 font-span text-white h-100">
                                                            {{ __('PROCEED') }}
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960">
                                                                <path d="m421-80-71-71 329-329-329-329 71-71 400 400L421-80Z"/>
                                                                <path d="m121-80-71-71 329-329-329-329 71-71 400 400L121-80Z"/>
                                                            </svg>
                                                        </button>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="myModal" class="modal fade" data-backdrop="true">
            <div class="modal-dialog mh-100 mh-padd-3 my-0">
                <div class="modal-content p-8">
                    <div class="px-0 pt-0 modal-header">
                        <div class="modal-title text-md text-center fs-3 mb-3 fw-bold text-center">{{ __('Choose a payment method') }}</div>

                    </div>
                    <ul class="paymentMethods d-flex ps-0 mb-6 nowrap justify-content-center ">
                        @foreach($paymentMethods as $paymentMethod)
                            <li class="d-flex nav-item me-0  paymentMethod-{{ $paymentMethod->id }} ">
                                <a type="{{ $paymentMethod->name }}" class="d-flex justify-content-center align-items-center btn font-span fw-bold py-4  purble-border" style="width: 138px;height: 55px; padding: 1px !important;" data-table-id="{{ $paymentMethod->id }}">

                                    {{ __($paymentMethod->name)}}


                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex flex-stack  rounded-3 pt-4">
                        <div class="fs-6 ">
                            <span class="d-block  lh-1  ">{{ __('Subtotal') }}:</span>
                            <span class="d-block font-span ">{{ __('Discounts') }} <span class="discount-area"></span></span>

                            <div class="tax-list mb-2">
                                @if (false)
                                    <span class="d-block font-span ">{{ __('Tax') }} <span class="tax-area">{{ isset($tax_key_filtered['take_away']) ? number_format($tax_key_filtered['take_away']->tax_rate, 2, '.', '') : '0.00'  }}%</span></span>
                                @endif
                            </div>

                            <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                        </div>
                        <div class="fs-6 text-end">
                            <span id="subTotal" class="d-block text-gray font-span lh-1  " data-kt-pos-element="grant-total">$00.00</span>
                            <span class="d-block font-span discount-area-amount">{{ __('No Discounts') }}</span>
                            <div class="tax-list-amount mb-2">
                                @if (false)
                                    <span class="d-block font-span tax-area-amount">&nbsp;</span>
                                @endif
                            </div>
                            <span id="totalOnDiscount" class="d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">$00.00</span>

                        </div>
                    </div>
                </div>

            </div>

        </div>



        <div id="cashModal" class="modal fade" data-backdrop="true">
            <div class="payment-loader"></div>
            <div class="modal-dialog mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="px-0 modal-header">
                        <div class="modal-title text-md text-center fs-3 mb-3 fw-bold text-center">{{ __('Choose a payment method') }}</div>

                    </div>
                    <div class="justify-content-between d-flex">
                        <div class="d-flex">
                            <ul class="paymentMethods cash d-flex ps-0 mb-0 flex-nowrap justify-content-center ">
                                @foreach($paymentMethods as $paymentMethod)
                                    <li class="d-flex nav-item  me-0  paymentMethod-{{ $paymentMethod->id }} ">
                                        <a type="{{ $paymentMethod->name }}" class="@php echo $paymentMethod->name == 'Cash' ? 'btn-violet text-white' : '' @endphp d-flex justify-content-center align-items-center btn font-span fw-bold py-4  purble-border" style="width: 138px;  height:55px; padding: 1px !important;" data-table-id="{{ $paymentMethod->id }}">

                                            {{ __($paymentMethod->name) }}

                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="">
                            <a href="#" class="btn font-span fw-bold h-100 d-flex align-items-center keyboard-show gray-border show-keyboard">{{ __('Show keyboard') }}</a>
                        </div>

                    </div>
                    <div class="d-flex flex-stack  rounded-3 py-4">
                        <div class="fs-6 ">
                            <span class="d-block  lh-1  ">{{ __('Subtotal') }}:</span>
                            <span class="d-block font-span ">{{ __('Discounts') }} <span class="discount-area"></span></span>
                            <div class="tax-list mb-2">
                                @if (false)
                                    <span class="d-block font-span ">{{ __('Tax') }} <span class="tax-area">{{ isset($tax_key_filtered['take_away']) ? number_format($tax_key_filtered['take_away']->tax_rate, 2, '.', '') : '0.00'  }}%</span></span>
                                @endif
                            </div>
                            <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                        </div>
                        <div class="fs-6 text-end">
                            <span id="subTotalCash" class="d-block text-gray font-span lh-1  " data-kt-pos-element="grant-total">$00.00</span>
                            <span class="d-block font-span discount-area-amount">{{ __('No Discounts') }}</span>
                            <div class="tax-list-amount mb-2">
                                @if (false)
                                    <span class="d-block font-span tax-area-amount">&nbsp;</span>
                                @endif
                            </div>
                            <span id="totalOnCash" class="d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">$00.00</span>
                        </div>
                    </div>
                    <div class="input-group rounded  justify-content-center mb-2  w-100">
                        <div class="input-group rounded position-relative justify-content-center w-100">
                            <label class="position-absolute left-0 added-input-text">{{ __('Amount received') }}:</label>
                            <input type="text" disabled class="mix-focused py-2 text-black form-control text-center recipient-amount rounded out-ins custom-input-bg" />
                        </div>
                    </div>
                    <div class="input-group rounded justify-content-center mb-2 w-100">
                        <div class="input-group rounded position-relative justify-content-center w-100">
                            <label class="position-absolute left-0 added-input-text">{{ __('Balance') }}:</label>
                            <input type="text" disabled class="py-2 text-black form-control balance rounded text-center border-danger input-bg-danger out-ins custom-input-bg" />
                        </div>
                    </div>
                    <div class="calculator card d-flex justify-content-between">



                        <div class="px-0 calculator-keys num-pad">

                            <button type="button" value="7" class="btn waves-effect">7</button>
                            <button type="button" value="8" class="btn waves-effect">8</button>
                            <button type="button" value="9" class="btn waves-effect">9</button>


                            <button type="button" value="4" class="btn waves-effect">4</button>
                            <button type="button" value="5" class="btn waves-effect">5</button>
                            <button type="button" value="6" class="btn waves-effect">6</button>


                            <button type="button" value="1" class="btn waves-effect">1</button>
                            <button type="button" value="2" class="btn waves-effect">2</button>
                            <button type="button" value="3" class="btn waves-effect">3</button>



                            <button type="button" class="decimal function btn btn-secondary waves-effect" value=".">.</button>
                            <button type="button" value="0" class="btn waves-effect">0</button>
                            <button type="button" class="all-clear function btn btn-secondary btn-sm" value="all-clear">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                    <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" fill="#333"/>
                                </svg>
                            </button>



                        </div>
                        <div class="">


                            <div class="total px-0 calculator-keys col-alone-grid pb-0">
                                <button type="button" value="total" class="rounded-top w-100 btn waves-effect">{{ __('Total') }}</button>
                            </div>
                            <div class="payment-keyboard px-0 calculator-keys total-keys pt-0">
                                <button type="button" value="20" class="btn waves-effect addition">20</button>
                                <button type="button" value="50" class="btn waves-effect addition">50</button>



                                <button type="button" value="100" class="btn waves-effect addition">100</button>
                                <button type="button" value="200" class="btn waves-effect addition">200</button>

                                <button type="button" value="500" class="btn waves-effect addition">500</button>
                                <button type="button" value="1000" class="btn waves-effect addition">1000</button>
                            </div>










                        </div>

                    </div>

                    <div class="d-flex justify-content-between buttons-discount d-none close-pay-card">
                        <div class="w-100">
                            <a href="#" class="btn font-span w-100 fw-bold py-4 gray-border btn-violet text-white close-payment-modals">{{ __('Close') }}</a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between buttons-discount mt-4 pay-card-buttons">
                        <div class="">
                            <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet close-payment-modals">{{ __('Discard payment') }}</a>
                        </div>
                        <div class="">
                            <a href="#" class="btn confirm-payment font-span fw-bold py-4 gray-border btn-violet text-white">{{ __('Confirm payment') }}</a>
                        </div>
                    </div>
                </div>


            </div>

        </div>




        <div id="bankModal" class="modal fade" data-backdrop="true">
            <div class="payment-loader"></div>
            <div class="modal-dialog mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="px-0 modal-header px-0">
                        <div class="modal-title text-md text-center fs-3 mb-3 fw-bold text-center">{{ __('Choose a payment method') }}</div>

                    </div>
                    <div class="justify-content-between d-flex">
                        <div class="d-flex">
                            <ul class="paymentMethods bank d-flex ps-0 mb-0 flex-nowrap justify-content-center ">
                                @foreach($paymentMethods as $paymentMethod)
                                    <li class="d-flex nav-item  me-0  paymentMethod-{{ $paymentMethod->id }} ">
                                        <a type="{{ $paymentMethod->name }}" class="@php echo $paymentMethod->name == 'Card' ? 'btn-violet' : '' @endphp d-flex justify-content-center align-items-center btn font-span fw-bold py-4  purble-border text-violet" style="width: 138px;  height:55px; padding: 1px !important;" data-table-id="{{ $paymentMethod->id }}">

                                            <div class="">
                                                <span class="@php echo $paymentMethod->name == 'Card' ? 'text-light' : 'text-violet' @endphp  d-block font-heading">{{ __($paymentMethod->name) }}</span>
                                                {{-- <span class="text-gray-500 fw-semibold fs-7">8 Options</span>--}}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="">
                            <a href="#" class="btn font-span fw-bold h-100 d-flex align-items-center gray-border show-keyboard">{{ __('Show keyboard') }}</a>
                        </div>

                    </div>
                    <div class="d-flex flex-stack  rounded-3 py-4">
                        <div class="fs-6 ">
                            <span class="d-block  lh-1  ">{{ __('Subtotal') }}:</span>
                            <span class="d-block font-span ">{{ __('Discounts') }} <span class="discount-area"></span></span>
                            <div class="tax-list mb-2">
                                @if (false)
                                    <span class="d-block font-span ">{{ __('Tax') }} <span class="tax-area">{{ isset($tax_key_filtered['take_away']) ? number_format($tax_key_filtered['take_away']->tax_rate, 2, '.', '') : '0.00'  }}%</span></span>
                                @endif
                            </div>
                            <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                        </div>
                        <div class="fs-6 text-end">
                            <span id="subTotalBank" class="d-block text-gray font-span lh-1  " data-kt-pos-element="grant-total">$00.00</span>
                            <span class="d-block font-span discount-area-amount">{{ __('No Discounts') }}</span>
                            <div class="tax-list-amount mb-2">
                                @if (false)
                                    <span class="d-block font-span tax-area-amount">&nbsp;</span>
                                @endif
                            </div>
                            <span id="totalOnBank" class="d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">$00.00</span>
                        </div>
                    </div>
                    @if(false)
                        <div class="d-flex banks justify-content-evenly mb-5">
                            <div class="">
                                <a href="#" class="btn font-span fw-bold text-white py-4 gray-border teb-bg ">TEB</a>
                            </div>
                            <div class="">
                                <a href="#" class="btn font-span fw-bold py-4 gray-border text-dark raiffeisen-bg ">Raiffeisen Bank</a>
                            </div>
                            <div class="">
                                <a href="#" class="btn font-span fw-bold py-4 gray-border text-white bkt-bg ">BKT Bank</a>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between buttons-discount d-none close-pay-card">
                        <div class="w-100">
                            <a href="#" class="btn font-span w-100 fw-bold py-4 gray-border btn-violet text-white close-payment-modals">{{ __('Close') }}</a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between buttons-discount pay-card-buttons">
                        <div class="">
                            <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet close-payment-modals">{{ __('Discard payment') }}</a>
                        </div>
                        <div class="">
                            <a href="#" class="btn confirm-payment font-span fw-bold py-4 gray-border btn-violet text-white">{{ __('Confirm payment') }}</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div id="mixModal" class="modal fade" data-backdrop="true">
            <div class="payment-loader"></div>
            <div class="modal-dialog mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="modal-header px-0">
                        <div class="modal-title text-md text-center fs-3 mb-3 fw-bold text-center">{{ __('Choose a payment method') }}</div>

                    </div>
                    <div class="justify-content-between d-flex">
                        <div class="d-flex">
                            <ul class="paymentMethods cash d-flex ps-0 mb-0 flex-nowrap justify-content-center ">
                                @foreach($paymentMethods as $paymentMethod)
                                    <li class="d-flex nav-item  me-0  paymentMethod-{{ $paymentMethod->id }} ">
                                        <a type="{{ $paymentMethod->name }}" class="@php echo $paymentMethod->name == 'Mix' ? 'btn-violet' : '' @endphp d-flex justify-content-center align-items-center btn font-span fw-bold py-4  purble-border text-violet" style="width: 138px;  height:55px; padding: 1px !important;" data-table-id="{{ $paymentMethod->id }}">

                                            <div class="">
                                                <span class="@php echo $paymentMethod->name == 'Mix' ? 'text-light' : 'text-violet' @endphp  d-block font-heading">{{ __($paymentMethod->name) }}</span>
                                                {{-- <span class="text-gray-500 fw-semibold fs-7">8 Options</span>--}}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="">
                            <a href="#" class="btn font-span fw-bold h-100 d-flex align-items-center gray-border show-keyboard">{{ __('Show keyboard') }}</a>
                        </div>

                    </div>
                    <div class="d-flex flex-stack  rounded-3 py-4">
                        <div class="fs-6 ">
                            <span class="d-block  lh-1  ">{{ __('Subtotal') }}:</span>
                            <span class="d-block font-span ">{{ __('Discounts') }} <span class="discount-area"></span></span>
                            <div class="tax-list mb-2">
                                @if (false)
                                    <span class="d-block font-span ">{{ __('Tax') }} <span class="tax-area">{{ isset($tax_key_filtered['take_away']) ? number_format($tax_key_filtered['take_away']->tax_rate, 2, '.', '') : '0.00'  }}%</span></span>
                                @endif
                            </div>
                            <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                        </div>
                        <div class="fs-6 text-end">
                            <span id="subTotalMix" class="d-block text-gray font-span lh-1  " data-kt-pos-element="grant-total">$00.00</span>
                            <span class="d-block font-span discount-area-amount">{{ __('No Discounts') }}</span>
                            <div class="tax-list-amount mb-2">
                                @if (false)
                                    <span class="d-block font-span tax-area-amount">&nbsp;</span>
                                @endif
                            </div>
                            <span id="totalOnMix" class="d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">$00.00</span>

                        </div>
                    </div>
                    <div class="input-group rounded justify-content-center mb-2 w-100">
                        <div class="input-group rounded position-relative justify-content-center w-100">
                            <label class="position-absolute left-0 added-input-text">{{ __('Cash amount') }}:</label>
                            <input type="text" focus-type="cash" class="py-2 mix-focus mix-focused form-control text-center no-key-up cash-mix-input rounded out-ins custom-input-bg" />
                        </div>
                    </div>
                    <div class="input-group rounded justify-content-center mb-2 w-100">
                        <div class="input-group rounded position-relative justify-content-center w-100">
                            <label class="position-absolute left-0 added-input-text">{{ __('Bank amount') }}:</label>
                            <input type="text" focus-type="bank" class="py-2 mix-focus form-control rounded no-key-up bank-mix-input text-center out-ins custom-input-bg" />
                        </div>
                    </div>
                    @if(false)
                        <div class="d-flex banks justify-content-evenly mt-5 mb-5">
                            <div class="">
                                <a href="#" class="btn font-span fw-bold text-white py-4 gray-border teb-bg ">TEB</a>
                            </div>
                            <div class="">
                                <a href="#" class="btn font-span fw-bold py-4 gray-border text-dark raiffeisen-bg ">Raiffeisen Bank</a>
                            </div>
                            <div class="">
                                <a href="#" class="btn font-span fw-bold py-4 gray-border text-white bkt-bg ">BKT Bank</a>
                            </div>
                        </div>
                    @endif

                    <div class="calculator card d-flex justify-content-between">



                        <div class="px-0 calculator-keys num-pad">



                            <button type="button" value="7" class="btn mix-mod waves-effect">7</button>
                            <button type="button" value="8" class="btn mix-mod waves-effect">8</button>
                            <button type="button" value="9" class="btn mix-mod waves-effect">9</button>


                            <button type="button" value="4" class="btn mix-mod waves-effect">4</button>
                            <button type="button" value="5" class="btn mix-mod waves-effect">5</button>
                            <button type="button" value="6" class="btn mix-mod waves-effect">6</button>


                            <button type="button" value="1" class="btn mix-mod waves-effect">1</button>
                            <button type="button" value="2" class="btn mix-mod waves-effect">2</button>
                            <button type="button" value="3" class="btn mix-mod waves-effect">3</button>



                            <button type="button" class="decimal function mix-mod btn btn-secondary waves-effect" value=".">.</button>
                            <button type="button" value="0" class="mix-mod btn waves-effect">0</button>
                            <button type="button" class="all-clear function btn btn-secondary btn-sm" value="all-clear">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                    <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" fill="#333"/>
                                </svg>
                            </button>



                        </div>
                        <div class="">


                            <div class="total calculator-keys px-0 col-alone-grid pb-0">
                                <button type="button" value="total" class="mix-mod rounded-top w-100 btn waves-effect">{{ __('Total') }}</button>
                            </div>
                            <div class="payment-keyboard px-0 calculator-keys total-keys pt-0">
                                <button type="button" value="20" class="btn mix-mod waves-effect addition">20</button>
                                <button type="button" value="50" class="btn mix-mod waves-effect addition">50</button>



                                <button type="button" value="100" class="btn mix-mod waves-effect addition">100</button>
                                <button type="button" value="200" class="btn mix-mod waves-effect addition">200</button>

                                <button type="button" value="500" class="btn mix-mod waves-effect addition">500</button>
                                <button type="button" value="1000" class="btn mix-mod waves-effect addition">1000</button>
                            </div>










                        </div>


                    </div>

                    <div class="d-flex justify-content-between buttons-discount d-none close-pay-card">
                        <div class="w-100">
                            <a href="#" class="btn font-span w-100 fw-bold py-4 gray-border btn-violet text-white close-payment-modals">{{ __('Close') }}</a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between buttons-discount pay-card-buttons">
                        <div class="">
                            <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet close-payment-modals">{{ __('Discard payment') }}</a>
                        </div>
                        <div class="">
                            <a href="#" class="confirm-payment btn font-span fw-bold py-4  btn-violet gray-border text-white">{{ __('Confirm payment') }}</a>
                        </div>
                    </div>
                </div>

            </div>


        </div>



        <div id="discountModal" class="modal fade" data-backdrop="true">

            <div class="modal-dialog mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="justify-content-between pt-10">
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <h2 id="teksti" class="fs-5" style="color: rgb(93, 75, 223);">{{ __('Percentage') }}</h2>
                            <div class="switch_box box_1 d-flex align-items-center">
                                <input type="checkbox" id="disc-type" class="switch_1">
                            </div>

                            <h2 id="headerText" class="opa fs-5">{{ __('Solid amount') }}</h2>
                        </div>
                        <div class="input-group rounded  justify-content-center mb-3  w-100">
                            <input type="search" id="disc-in" class="pe-none py-2 form-control rounded text-center custom-input-bg" placeholder="{{ __('Ex.') }} 10%" aria-label="Search" aria-describedby="search-addon" />
                        </div>
                        <div class="calculator card d-flex justify-content-between w-100">
                            <div class="keypad-locator px-0 calculator-keys w-100 p-0 mb-4">
                                <button type="button" value="7" class="btn dynamic-keypad" targetting="disc-in">7</button>
                                <button type="button" value="8" class="btn dynamic-keypad" targetting="disc-in">8</button>
                                <button type="button" value="9" class="btn dynamic-keypad" targetting="disc-in">9</button>

                                <button type="button" value="4" class="btn dynamic-keypad" targetting="disc-in">4</button>
                                <button type="button" value="5" class="btn dynamic-keypad" targetting="disc-in">5</button>
                                <button type="button" value="6" class="btn dynamic-keypad" targetting="disc-in">6</button>

                                <button type="button" value="1" class="btn dynamic-keypad" targetting="disc-in">1</button>
                                <button type="button" value="2" class="btn dynamic-keypad" targetting="disc-in">2</button>
                                <button type="button" value="3" class="btn dynamic-keypad" targetting="disc-in">3</button>

                                <button type="button" class="btn btn-secondary dynamic-keypad" value="." targetting="disc-in">.</button>
                                <button type="button" value="0" class="btn dynamic-keypad" targetting="disc-in">0</button>
                                <button type="button" class="btn btn-secondary btn-sm dynamic-keypad" value="back" targetting="disc-in">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                        <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" fill="#333"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between buttons-discount">
                            <div class="">
                                <a href="#" class="btn font-span discard fw-bold py-4 gray-border text-violet">{{ __('Discard discount') }}</a>
                            </div>
                            <div class="">
                                <a href="#" class="btn font-span fw-bold add-disc btn-violet  btn-violet py-4 gray-border text-white">{{ __('Add discount') }}</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>



        <div id="notesModal" class="modal fade" data-backdrop="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-8">

                    <label for="basic-url mb-1" class="form-label fw-bold">{{ __('Add a kitchen note') }}</label>
                    <div class="input-group rounded mb-7 w-100">
                        <input type="search" class="notes form-control rounded custom-input-bg" placeholder="{{ __('kitchen note') }}" aria-label="Search" aria-describedby="search-addon" />
                    </div>

                    <div class="d-flex justify-content-between buttons-discount">
                        <div class="">
                            <a href="#" class="btn font-span notes-discard fw-bold py-4 gray-border text-violet">{{ __('Discard changes') }}</a>
                        </div>
                        <div class="">
                            <a href="#" class="btn font-span notes-save notes-discard fw-bold py-4 gray-border btn-violet text-white">{{ __('Update changes') }}</a>
                        </div>
                    </div>

                </div>

            </div>

        </div>



        <div id="moreModal" class="modal fade" data-backdrop="true">
            <div class="modal-dialog more-btn-modal mx-modal-700 mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="modal-header px-0 justify-content-center">
                        <div class="modal-title text-md text-center fs-3 fw-bold text-center ">{{ __('More options') }}</div>
                    </div>

                    <div class="d-flex flex-column gap-10">
                        <div class="more-btn-modal">
                            <div class="row gap-4 mb-4">
                                <div class="col pe-0">
                                    <form class="mb-0" method="POST" action="{{ route('change.cash.register')}}">
                                        @csrf
                                        <button type="submit" class="btn font-span w-100 py-4">{{ __('Change cash register') }}</button>
                                    </form>
                                </div>
                                <div class="col ps-0">
                                    <!-- <a href="#" class="btn font-span w-100 py-4 disabled">{{ __('Queue screen') }}</a> -->
                                    <!-- <a href="#" class="btn font-span w-100 py-4 open-reports-modal">{{ __('Reports menu') }}</a> -->
                                    <a href="#" class="btn font-span w-100 py-4 attempt-print-last-z-report">{{ __('Reports menu') }}</a>
                                </div>
                            </div>

                            <div class="row gap-4 mb-4">
                                <div class="col pe-0">
                                    <a href="{{ route('pos.settings') }}" class="btn font-span w-100 py-4">{{ __('Settings') }}</a>
                                </div>
                                <div class="col ps-0">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="from" value="pos" autocomplete="off">
                                        <button href="#" class="btn font-span w-100 py-4">
                                            <div class="d-flex gap-3 justify-content-center align-items-center">
                                                <div class="">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="danger-children" width="24" height="24" viewBox="0 0 24 24">
                                                        <g id="icon" transform="translate(-27 -757)">
                                                            <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#ed1c24" opacity="0"/>
                                                            <g id="arrow-down-bold" transform="translate(26.033 764.991) rotate(90)">
                                                                <g id="Group_29" data-name="Group 29" transform="translate(-1.068 -16.167)">
                                                                    <g id="Group_28" data-name="Group 28">
                                                                        <path id="Path_6" data-name="Path 6" d="M.232.247a.786.786,0,0,1,1.078,0l3.775,3.62L8.861.247a.786.786,0,0,1,1.078,0,.71.71,0,0,1,0,1.034L5.625,5.419a.786.786,0,0,1-1.078,0L.232,1.281a.71.71,0,0,1,0-1.034Z" transform="translate(-0.009 9.567)" fill="#ed1c24"/>
                                                                        <rect id="Rectangle_57" data-name="Rectangle 57" width="1.846" height="13.467" rx="0.923" transform="translate(4.154)" fill="#ed1c24"/>
                                                                    </g>
                                                                </g>
                                                                <path id="Subtraction_3" data-name="Subtraction 3" d="M1.523,13.551H0V4C0,1.794,1.518,0,3.384,0H18.615C20.481,0,22,1.794,22,4v9.55H20.477V4a2.054,2.054,0,0,0-1.862-2.2H3.384A2.054,2.054,0,0,0,1.523,4V13.55Z" transform="translate(-6.991 -24.967)" fill="#ed1c24"/>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="text-danger">{{ __('Log out') }}</div>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div>

                            <div class="">
                                <a href="#" class="w-100 btn font-span discard-more py-4 gray-border discard-btn">{{ __('Discard actions') }}</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>


        <div id="report-more-modal" class="modal fade" data-backdrop="true">
            <div class="modal-dialog more-btn-modal mx-modal-700 mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="modal-header px-0 justify-content-center">
                        <div class="modal-title text-md text-center fs-3 fw-bold text-center ">{{ __('Report options') }}</div>
                    </div>

                    <div class="d-flex flex-column gap-10">
                        <div class="more-btn-modal">
                            <div class="row gap-4 mb-4">
                                <div class="col pe-0">
                                    <div>
                                        <button type="submit" @disabled($user->role_id != config('constants.role.adminId') && !in_array('pos_x_report_module', $user->userRole->permissions)) open-custom-modal="xreport-employee" data-bs-toggle="modal" class="w-100 btn font-span py-4 x-report-print">{{ __('Shift X report') }}</button>
                                    </div>
                                </div>

                                <div class="col ps-0">
                                    <button type="submit" @disabled($user->role_id != config('constants.role.adminId') && !in_array('pos_end_z_report_module', $user->userRole->permissions)) open-custom-modal="zreport-continue" data-bs-toggle="modal" class="w-100 btn font-span py-4">{{ __('Shift Z report') }}</button>
                                </div>
                            </div>

                            <div class="row gap-4 mb-4">
                                <div class="col pe-0">
                                    <!-- <a href="#" class="btn font-span w-100 py-4 disabled">{{ __('Queue screen') }}</a> -->
                                    <a href="#" class="btn font-span w-100 py-4 confirm-print-last-z-report">{{ __('Print last Z Report') }}</a>
                                </div>
                                <div class="col ps-0">
								   <a href="#" class="btn font-span w-100 py-4 open-manual-z-report">{{ __('Manual Z Report') }}</a>
                                </div>
                            </div>
                        </div>

                        <div>

                            <div class="">
                                <a href="#" class="w-100 btn font-span discard-reports py-4 gray-border discard-btn">{{ __('Discard actions') }}</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

   <div id="print-manual-z-reports" class="modal fade" data-backdrop="true">
            <div class="modal-dialog more-btn-modal mx-modal-700 mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="modal-header px-0 justify-content-center">
                        <div class="modal-title text-md text-center fs-3 fw-bold text-center ">{{ __('Report options') }}</div>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="more-btn-modal">
                            <div class="d-flex align-items-center position-relative mb-10">
                                <div class="input-group form-control p-0">
                                    <input class="form-control form-control-solid rounded rounded-end-0 bg-light ms-0 input-date-picker-months" 
                                        placeholder="{{ __('Select starting & ending date') }}"
                                        id="kt_ecommerce_sales_flatpickr" />
                                    <button class="btn btn-icon btn-light bg-light" id="kt_ecommerce_sales_flatpickr_clear">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 custom-selects-overlay">
                                    <select id="year-cash" name="year-cash"
                                            class="form-control mt-3"
                                            data-control="select2"
                                            data-placeholder="{{ __('Select a year') }}">
                                        <option></option>
                                        <option value="2025">2025</option>
                                        <option value="2024">2024</option>
                                    </select>
                                </div>
                                <div class="col-12 custom-selects-overlay">
                                    <select id="months-cash" name="months-cash"
                                            class="form-control mt-3 mb-3"
                                            data-control="select2"
                                            data-placeholder="{{ __('Select a month') }}">
                                        <option></option>
                                        @foreach($months as $month => $name)
                                            <option value="{{$month}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mx-0 gap-5">
                                <button id="print-custom-z-rep" type="button" class="btn btn-primary col">{{ __('Print') }}</button>
                            </div>
                        </div>
                        <div>
                            <div class="">
                                <a href="#" class="w-100 btn font-span discard-manual-z-report py-4 gray-border discard-btn">{{ __('Discard actions') }}</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>


        <div id="confirm-reprint-z-report" class="modal bg-login-violet" data-backdrop="true">
            <div class="modal-dialog more-btn-modal mx-modal-700 mh-100 mh-padd-3 my-0">
                <div class="modal-content pb-10 px-10">
                    <div class="modal-header px-0 justify-content-center">
                        <div class="modal-title text-md text-center fs-3 fw-bold text-center ">{{ __('Report options') }}</div>
                    </div>

                    <form class="d-flex flex-column mb-0 pin-submit">
                        <div class="d-flex flex-column mb-0 gap-4">
                            <div class="more-btn-modal">
                                <input class="form-control text-center" id="pin-z-rep" name="pin" inputmode="numeric" type="password" placeholder="{{ __('Password') }}">
                            </div>
                            <div>
                                <div class="d-flex gap-4">
                                    <a href="#" class="w-100 btn font-span discard-pin-zreport py-4 gray-border discard-btn">{{ __('Discard actions') }}</a>
                                    <input class="w-100 text-center purple-bg text-white" type="submit" value="{{ __('Open') }}">
                                </div>
                            </div>
                    </form>
                </div>

            </div>

        </div>

    </div>

    <div id="pin-accept-modal-zreport" class="modal bg-login-violet" data-backdrop="true">
        <div class="modal-dialog more-btn-modal mx-modal-700 mh-100 mh-padd-3 my-0">
            <div class="modal-content pb-10 px-10">
                <div class="modal-header px-0 justify-content-center">
                    <div class="modal-title text-md text-center fs-3 fw-bold text-center ">{{ __('Confirmation') }}</div>
                </div>

                <form class="d-flex print-last-z-report-form flex-column mb-0">
                    <div class="d-flex flex-column mb-0 gap-4">
                        <div class="more-btn-modal">
                            <p class="fs-4 text-center">{{ __('Do you wish to print the last Z Report') }}</p>
                        </div>
                        <div>
                            <div class="d-flex gap-4">
                                <a href="#" class="fs-4 w-100 btn font-span discard-pin-modal-zreport py-4 gray-border discard-btn">{{ __('No') }}</a>
                                <input class="fs-4 w-100 text-center purple-bg text-white" type="submit" value="{{ __('Yes') }}">
                            </div>
                        </div>
                </form>
            </div>

        </div>

    </div>

    </div>

    <div id="dine-step" class="wider modal fade" data-backdrop="true">
        <div class="modal-dialog mh-100 mh-padd-3 my-0">
            <div class="modal-content p-10">
                <div class="p-0 mb-3 modal-header justify-content-center">
                    <div class="fs-3 modal-title text-md text-center fw-bold text-center">{{ __('Select a table') }}</div>
                </div>

                <div class="d-flex justify-content-center gap-0">
                    <a href="#" onclick="moveCol(this, 0, 'table-dine')" text="Select a table" class="rem-custom-radius rounded-start btn btn-secondary font-span fw-bold py-3 px-8 gray-border">{{ __('Table') }}</a>
                    <a href="#" onclick="moveCol(this, 1, 'locator-dine')" text="Write locator number" class="rem-custom-radius rounded-end btn btn-secondary font-span fw-bold py-3 px-8 gray-border">{{ __('Locator') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div id="tableModal" class="wider modal fade" data-backdrop="true">
        <div class="modal-dialog mh-100 mh-padd-3 my-0">
            <div class="modal-content pb-10 px-10">
                <div class="px-0 modal-header justify-content-start">
                    <div class="fs-3 mb-3 modal-title text-md text-center fw-bold text-center" id="dine-text">{{ __('Select a table') }}</div>
                </div>

                <div class="justify-content-start mb-5 d-flex">
                    <a href="#" onclick="moveCol(this, 0)" col-group="btns" id-open="table-dine" text="Select a table" class="rem-custom-radius rounded-start btn btn-primary font-span fw-bold py-3 px-8 gray-border">{{ __('Table') }}</a>
                    <a href="#" onclick="moveCol(this, 1)" col-group="btns" id-open="locator-dine" text="Write locator number" class="rem-custom-radius rounded-end btn btn-secondary font-span fw-bold py-3 px-8 gray-border">{{ __('Locator') }}</a>
                </div>
                <div id="table-dine" class="tables">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="service-table-layout w-200px flex-grow-0 d-flex nav-item align-items-center me-0 serviceTable-{service_id}">
                            <a class="d-flex {busy} w-100 align-items-center tavolina btn btn-color-gray-600 btn-active-text-gray-800 py-3 px-2 flex-row font-span show" type="{service_name}" data-table-id="{service_id}">
                                <div class="square flex-shrink-0 me-5 fs-7">X</div>
                                <div class="pe-2">
                                    <span class="text-gray-800 fs-7 d-block font-heading">{service_name}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="locator-dine" class="locator hide-id">
                    <div class="input-group rounded position-relative justify-content-center mb-5  w-100">
                        <label class="position-absolute left-0 added-input-text">{{ __('Locator number') }}:</label>
                        <input type="text" id="loc-num" class="pe-none fw-bold text-center py-2 p-cus-8 form-control rounded custom-input-bg" />
                    </div>
                    <div class="calculator card d-flex justify-content-between w-100">
                        <div class="keypad-locator px-0 calculator-keys w-100 p-0 mb-4">
                            <button type="button" value="7" class="btn locator-keys">7</button>
                            <button type="button" value="8" class="btn locator-keys">8</button>
                            <button type="button" value="9" class="btn locator-keys">9</button>

                            <button type="button" value="4" class="btn locator-keys">4</button>
                            <button type="button" value="5" class="btn locator-keys">5</button>
                            <button type="button" value="6" class="btn locator-keys">6</button>

                            <button type="button" value="1" class="btn locator-keys">1</button>
                            <button type="button" value="2" class="btn locator-keys">2</button>
                            <button type="button" value="3" class="btn locator-keys">3</button>

                            <button type="button" class="decimal function btn btn-secondary locator-keys disabled">.</button>
                            <button type="button" value="0" class="btn locator-keys">0</button>
                            <button type="button" class="btn btn-secondary btn-sm locator-keys" value="back">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                    <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" fill="#333"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between buttons-discount">
                        <div class="">
                            <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet extra close locator-keys">{{ __('Close') }}</a>
                        </div>
                        <div class="">
                            <a href="#" class="btn font-span fw-bold py-4 gray-border btn-violet text-white extra confirm locator-keys">{{ __('Confirm locator') }}</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <div id="edit-itemModal" class="modal edit-modal fade" data-backdrop="true">
        <div class="modal-dialog mh-100 mh-padd-3 my-0">
            <div class="modal-content p-8">
                <div class="px-0 modal-header py-0 justify-content-start ps-0">
                    <div class="modal-title text-md text-center fs-3 mb-3 fw-bold text-start">{{ __('Update food item information') }}</div>

                </div>
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="basic-url" class="form-label fw-bold">{{ __('Food item name') }}</label>
                        <div class="input-group input-height-notes rounded w-100">

                            <button class="form-control edit-buttons text-start rounded name-on-edit custom-input-bg"></button>
                        </div>
                    </div>
                    <div class="d-flex banks justify-content-between">
                        <div class="d-flex flex-column">
                            <label for="basic-url" class="form-label fw-bold">{{ __('Price per unit') }}</label>
                            <div class="flex-grow-1 input-group input-height-notes rounded w-100">

                                <input id="price-on-edit" type="text" inputmode="decimal" class="h-100 not-deal-items text-center form-control edit-buttons rounded bg-white price-on-edit" />
                                <button class="h-100 deal-items form-control edit-buttons rounded price-on-edit custom-input-bg"></button>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <label for="basic-url" class="form-label fw-bold">{{ __('Quantity') }}</label>
                            <div class="input-group flex-grow-1 input-height-notes rounded w-100">

                                <div class="h-100 p-0 form-control edit-buttons align-items-center rounded text-center d-flex justify-content-between custom-input-bg">
                                    <div class="h-100 d-flex clickable h-100 px-3 align-items-center edit-quantity-e" change="-1">
                                        <button class="quantity-btn quantity-on-edit fs-3 bi bi-minus"></button>
                                    </div>
                                    <span class="h-100 d-flex align-items-center" id="editting-quantity">1</span>
                                    <div class="h-100 d-flex clickable h-100 px-3 align-items-center edit-quantity-e" change="1">
                                        <button class="quantity-btn fs-3 bi bi-plus"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <label for="basic-url" class="form-label fw-bold">&nbsp;</label>
                            <div class="input-group flex-grow-1 input-height-notes rounded w-100">

                                <button class="h-100 form-control edit-buttons rounded custom-input-bg">{{ __('Mark as ready') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="product-ingredients-box">
                        <label for="basic-url" class="form-label fw-bold not-deal-items">{{ __('Basic ingredients') }}</label>
                        <div class="d-flex sauces flex-wrap row-gap-2 justify-content-start not-deal-items edit-ingredients">
                        </div>
                    </div>
                    <div class="not-deal-items product-modifiers-box">
                        <label for="basic-url" class="form-label fw-bold">{{ __('Modifiers') }}</label>
                        <ul class="flex-wrap d-flex gap-3 mb-0 overflow-auto p-0 edit-modifiers">
                        </ul>
                    </div>

                    <div class="not-deal-items product-size-box">
                        <label for="basic-url" class="form-label fw-bold">{{ __('Size') }}</label>
                        <ul class="flex-wrap d-flex gap-3 overflow-auto p-0 edit-size">
                        </ul>
                    </div>
                </div>
                <div class="d-flex justify-content-between buttons-discount gap-2 mt-3">
                    <div class="">
                        <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet discard-food-changes">{{ __('Discard changes') }}</a>
                    </div>
                    <div class="deal-items">
                        <a href="#" id="proc-item" class="btn font-span fw-bold py-4 gray-border text-violet meal-changes discard-food-changes" data-bs-toggle="modal" data-bs-target="#edit-meal-modifiers">{{ __('Modify Deal Items') }}</a>
                    </div>
                    <div class="">
                        <a href="#" class="btn update-food-changes font-span fw-bold py-4 gray-border btn-violet text-white">{{ __('Update changes') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-meal-modifiers" class="modal edit-modal fade" data-backdrop="true">
        <div class="modal-dialog mh-100 mh-padd-3 my-0">
            <div class="modal-content pb-10 px-10">
                <div class="px-0 modal-header justify-content-start pt-4 ps-0">
                    <div class="modal-title text-md text-center fs-3 mb-3 fw-bold w-100 text-center">{{ __('Update deal items') }}</div>
                </div>
                <div class="input-group input-height-notes rounded mb-5 w-100">
                    <button class="form-control opened-by-default edit-buttons text-start custom-input-bg text-center tab-meal-switcher" tab="main-meal" key="main-meal">{{ __('Main Meal') }}</button>
                    @foreach($configured_apis as $index => $api)
                        @if($api['show'])
                            <button class="form-control edit-buttons text-start custom-input-bg text-center tab-meal-switcher" tab="{{$api['key']}}" key="{{ $api['id'] }}">{{ $api['value'] }}</button>
                        @endif
                    @endforeach
                </div>
                <div class="main-meal mb-5 meal-modifier-tabs active" layout="main-meal">
                    <div class="item mb-4 food-item-layout" product="{product_id}" prod_num="{prod_num}">
                        <div class="inner-tab d-flex justify-content-between px-4">
                            <label for="basic-url" class="form-label fw-bold">{product_name}</label>
                            <button class="quantity-btn fs-3 minus-btn">-</button>
                        </div>
                        <div class="inner-tab-content m-0 border rounded bg-gray {is_open}">
                            <div class="customization mb-0 p-4 pb-2 ">
                                <div class="mb-2">{{ __('Customize') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 item-ingridients">
                                    <div class="ingredients-layout"> <a prod_num="{prod_num}" tip="main_meal" href="#" ing="{ingredient_id}" prod="{product_id}" class="btn {active_class} font-span fw-bold py-4 gray-border ingredient-list">{ingredient_name}</a> </div>
                                </div>
                            </div>
                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Modify') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications ">
                                    <li class="modifier-layout d-flex gray-border btn edit-buttons p-0 modifier-li custom-input-bg table-radius nav-item me-0">
                                        <a prod_num="{prod_num}" tip="main_meal" mod-id="{mod_id}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" class="justify-content-center w-100 custom-input-bg modifier-list-item btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active position-relative {active_cl}" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block "><span class="modifier-quantity">{quantity_string}</span>{mod_name}</span>
                                                <span class="font-span modifier-name remove-mod text-danger position-absolute end-0 top-0 h-100 px-3 d-flex align-items-center" data-modifier-id="{mod_id}">X</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{{ "+ " . ($settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price}" : "{mod_price} " . $settings['currency_symbol']) }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>

                            <div class="modification p-4 pb-2 pt-2 deal-meal-size-box">
                                <div class="mb-2">{{ __('Size') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications ">
                                    <li class="sizes-layout d-flex gray-border btn edit-buttons p-0 modifier-li custom-input-bg table-radius nav-item mb-3 me-0">
                                        <a prod_num="{prod_num}" mod-id="{mod_id}" other-class="active-mod" data-other="{product_id}-meal-{prod_num}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" tip="main_meal" class="justify-content-center w-100 custom-input-bg size-list-item btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active {active_class}" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block ">{mod_name}</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{plus} {{ ($settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price_view}" : "{mod_price_view} " . $settings['currency_symbol']) }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>

                            {{-- Later --}}
                            @if(false)
                                <div class="modification p-4 pb-2 pt-2">
                                    <div class="mb-2">{{ __('Modify') }}</div>
                                    <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications ">
                                        <li class="modifier-layout d-flex gray-border btn edit-buttons p-0 modifier-li custom-input-bg table-radius nav-item mb-3 me-0">
                                            <a mod-id="{mod_id}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" class="justify-content-center w-100 custom-input-bg modifier-list-item btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                                <div class="">
                                                    <span class="text-gray-800 font-span modifier-name d-block ">{mod_name}</span>
                                                </div>
                                                <div class="">
                                                    <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{{ "+ " . $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price}" : "{mod_price} " . $settings['currency_symbol'] }}</span>
                                                </div>
                                            </a>
                                        </li>

                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>



                </div>

                {{-- These are used for styling, the divs get opened and close --}}
                <div class="Drinks drink-modifier meal-modifier-tabs mb-5" layout="drinkId">
                    <div class="mb-2 drink-modifier-layout" drink-id="{drink_id}" drink-num="{drink_num}">
                        <div class="inner-tab d-flex justify-content-between px-4">
                            <label for="basic-url" class="form-label fw-bold">{{ __('Drink') }} {drink_num}</label>
                            <button class="quantity-btn fs-3 minus-btn">-</button>
                        </div>
                        <div class="inner-tab-content {is_open} m-0 border rounded bg-gray">
                            <div class="customization mb-0 p-4 pb-2 ">
                                <div class="mb-2">{{ __('Customize') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 item-ingridients drink-active-holder">
                                    <div class="all-drink-layout" drink-num="{drink_num}">
                                        <a href="#" drink-id="{drink_id}" owner="{owner_id}" group="{category_id}" drink-num="{drink_num}" class="btn {active_class} font-span fw-bold py-4 gray-border meal-drink-swap">{drink_name}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Modify') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li drink-num="{drink_num}" class="drink-mod-modifier-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" tip="drinks" drink-num="{drink_num}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" class="position-relative meal-drink-mod-swap {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block "><span class="modifier-quantity">{quantity_string}</span>{mod_name}</span>
                                                <span class="font-span modifier-name remove-mod text-danger position-absolute end-0 top-0 h-100 px-3 d-flex align-items-center" data-modifier-id="{mod_id}">X</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">+ {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price}" : "{mod_price} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>

                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Size') }}</div>
                                <div sel-num="{drink_num}" class="drink-size sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li drink-num="{drink_num}" class="drink-mod-sizes-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" drink-num="{drink_num}" data-name="{mod_name}" data-price="{mod_price}" other-class="active-mod" prod="{product_id}" data-other="{drink_num}-drink" data-modifier-id="{mod_id}" tip="drinks" class="size-list-item {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block ">{mod_name}</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{plus} {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price_view}" : "{mod_price_view} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="Fries fries-modifier meal-modifier-tabs mb-5" layout="friesId">

                    <div class="mb-2 fries-modifier-layout" fries-id="{fries_id}" fries-num="{fries_num}">
                        <div class="inner-tab d-flex justify-content-between px-4">
                            <label for="basic-url" class="form-label fw-bold">{{ __('Sides') }} {fries_num}</label>
                            <button class="quantity-btn fs-3 minus-btn">-</button>
                        </div>
                        <div class="inner-tab-content {is_open} m-0 border rounded bg-gray">
                            <div class="customization mb-0 p-4 pb-2 ">
                                <div class="mb-2">{{ __('Customize') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 item-ingridients drink-active-holder">
                                    <div class="all-fries-layout" fries-num="{fries_num}">
                                        <a href="#" group="{category_id}" owner="{owner_id}" fries-id="{fries_id}" fries-num="{fries_num}" class="btn {active_class} font-span fw-bold py-4 gray-border meal-fries-swap">{fries_name}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Modify') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li fries-num="{fries_num}" class="fries-mod-modifier-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" tip="fries" fries-num="{fries_num}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" class="position-relative meal-fries-mod-swap {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block "><span class="modifier-quantity">{quantity_string}</span>{mod_name}</span>
                                                <span class="font-span modifier-name remove-mod text-danger position-absolute end-0 top-0 h-100 px-3 d-flex align-items-center" data-modifier-id="{mod_id}">X</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">+ {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price}" : "{mod_price} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>

                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Size') }}</div>
                                <div sel-num="{fries_num}" class="fries-size sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li fries-num="{fries_num}" class="fries-mod-sizes-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" fries-num="{fries_num}" data-name="{mod_name}" data-price="{mod_price}" data-other="{fries_num}-fries" other-class="active-mod" prod="{product_id}" data-modifier-id="{mod_id}" tip="fries" class="size-list-item {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block ">{mod_name}</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{plus} {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price_view}" : "{mod_price_view} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>
                            </div>
                        </div>
                    </div>




                </div>

                <div class="Sauces sauce-modifier meal-modifier-tabs mb-5" layout="sauceId">

                    <div class="mb-2 sauces-modifier-layout" sauces-id="{sauces_id}" sauces-num="{sauces_num}">
                        <div class="inner-tab d-flex justify-content-between px-4">
                            <label for="basic-url" class="form-label fw-bold">{{ __('Sauces') }} {sauces_num}</label>
                            <button class="quantity-btn fs-3 minus-btn">-</button>
                        </div>
                        <div class="inner-tab-content {is_open} m-0 border rounded bg-gray">
                            <div class="customization mb-0 p-4 pb-2 ">
                                <div class="mb-2">{{ __('Customize') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 item-ingridients drink-active-holder">
                                    <div class="all-sauces-layout" sauces-num="{sauces_num}">
                                        <a href="#" sauces-id="{sauces_id}" owner="{owner_id}" group="{category_id}" sauces-num="{sauces_num}" class="btn {active_class} font-span fw-bold py-4 gray-border meal-sauces-swap">{sauces_name}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Modify') }}</div>
                                <div class="sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li sauces-num="{sauces_num}" class="sauces-mod-modifier-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" tip="sauces" sauces-num="{sauces_num}" prod="{product_id}" data-name="{mod_name}" data-price="{mod_price}" data-modifier-id="{mod_id}" class="position-relative meal-sauces-mod-swap {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block "><span class="modifier-quantity">{quantity_string}</span>{mod_name}</span>
                                                <span class="font-span modifier-name remove-mod text-danger position-absolute end-0 top-0 h-100 px-3 d-flex align-items-center" data-modifier-id="{mod_id}">X</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">+ {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price}" : "{mod_price} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>

                            </div>

                            <div class="modification p-4 pb-2 pt-2">
                                <div class="mb-2">{{ __('Size') }}</div>
                                <div sel-num="{sauces_num}" class="sauces-size sauces d-flex flex-wrap row-gap-3 column-gap-3 item-modifications">
                                    <li sauces-num="{sauces_num}" class="sauces-mod-sizes-layout d-flex gray-border btn edit-buttons p-0 modifier-li table-radius nav-item mb-3 me-0">
                                        <a mod-id="{mod_id}" sauces-num="{sauces_num}" data-name="{mod_name}" prod="{product_id}" data-price="{mod_price}" data-other="{sauces_num}-sauce" other-class="active-mod" data-modifier-id="{mod_id}" tip="sauces" class="size-list-item {active_class} justify-content-center w-100 btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;">
                                            <div class="">
                                                <span class="text-gray-800 font-span modifier-name d-block ">{mod_name}</span>
                                            </div>
                                            <div class="">
                                                <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">{plus} {{  $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " {mod_price_view}" : "{mod_price_view} " . $settings['currency_symbol'] }}</span>
                                            </div>
                                        </a>
                                    </li>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>


                <div class="d-flex justify-content-between buttons-discount gap-2">
                    <div class="">
                        <a href="#" class="btn font-span fw-bold py-4 gray-border text-violet discard-food-changes" data-bs-toggle="modal" data-bs-target="#edit-meal-modifiers">{{ __('Discard changes') }}</a>
                    </div>
                    <div class="">
                        <a href="#" class="btn update-food-changes meal font-span fw-bold py-4 gray-border btn-violet text-white" >{{ __('Update changes') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <div class="tab-content zreport-continue fullscreen hiding hidden flex-switch justify-content-center flex-column flex-lg-row align-items-center h-100 custom-modal">
        <div class="tab-pane fade show active d-flex justify-content-center"
             id="kt_ecommerce_add_product_general"
             role="tab-pane">
            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                <div class="card card-flush w-50 align-self-center w-600px">
                    <div class="card-body">
                        <form action="{{ route('create.zReport') }}" method="post">
                            @csrf
                            <div class="spacing gap-4 d-flex flex-column">
                                <div class="top-info d-flex justify-content-between">
                                    <div>
                                        <h2 class="text-left">{{ __('Close Z Report') }}</h2>
                                        <p class="mb-0">{{ __('Please write how much money you have currently') }}</p>
                                    </div>
                                    <div>
                                        <div class="d-none">
                                            <a href="#" class="btn font-span fw-bold h-100 d-flex align-items-center gray-border show-key-pads">{{ __('Hide keyboard') }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-none align-items-center">
                                    <label class="w-100px">{{ __('Starting') }}</label>
                                    <div class="flex-grow-1 text-center py-2 key-pad-display">
                                        <span class="display-text saldo-amount fs-5"></span>
                                    </div>
                                </div>

                                <div class="d-none align-items-center">
                                    <label class="w-100px">{{ __('Expected') }}</label>
                                    <div class="flex-grow-1 text-center py-2 key-pad-display">
                                        <span class="display-text saldo-expected fs-5"></span>
                                    </div>
                                </div>

                                <input type="hidden" name="closing_amount" class="form-control my-3 input saldo-ui" value="0">
                                <div class="key-pad-display text-center py-2">
                                    <span class="display-text saldo-ui" currency-symbol="{{ $settings['currency_symbol'] }}" currency-left="{{ $settings['currency_symbol_on_left'] ? '1' : '0' }}"></span>
                                </div>

                                <div class="saldo-ui key-pad on-white gap-0" targets="saldo-ui">
                                    <button type="button" value="7" class="btn rounded-start-2 rounded-bottom-0">7</button>
                                    <button type="button" value="8" class="btn">8</button>
                                    <button type="button" value="9" class="btn rounded-end-2 rounded-bottom-0">9</button>

                                    <button type="button" value="4" class="btn">4</button>
                                    <button type="button" value="5" class="btn">5</button>
                                    <button type="button" value="6" class="btn">6</button>

                                    <button type="button" value="1" class="btn">1</button>
                                    <button type="button" value="2" class="btn">2</button>
                                    <button type="button" value="3" class="btn">3</button>

                                    <button type="button" value="." class="btn btn-back flex-grow-1 rounded-start-2 rounded-top-0">.</button>
                                    <button type="button" value="0" class="btn">0</button>
                                    <button type="button" class="btn btn-back rounded-end-2 flex-grow-1 rounded-top-0" value="back">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                            <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="d-flex align-items gap-5">
                                    <button type="button" close-custom-modal="zreport-continue" class="btn btn-secondary w-100">{{ __('Cancel') }}</button>
                                    <button type="button" confirm-custom-modal="zreport-continue" class="btn btn-primary w-100">{{ __('Close') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content zreport-confirm fullscreen hiding hidden flex-switch justify-content-center flex-column flex-lg-row align-items-center h-100 custom-modal">
        <div class="tab-pane fade show active d-flex justify-content-center"
             id="kt_ecommerce_add_product_general"
             role="tab-pane">
            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                <div class="card card-flush w-50 align-self-center w-600px">
                    <div class="card-body">
                        <div class="spacing gap-4 d-flex flex-column">
                            <div>
                                <h2 class="text-left mb-3 text-center">{{ __('Are you sure?') }}</h2>
                                <p class="text-left mb-3 text-center">{{ __('This action will complete your schedule') }}</p>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="button" close-custom-modal="zreport-confirm" class="btn btn-secondary w-100">{{ __('Go back') }}</button>
                                <button type="button" confirm-custom-modal="zreport-confirm" class="btn btn-primary w-100 print-x-report">{{ __('Continue') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content zreport-modal fullscreen hiding hidden flex-switch justify-content-center flex-column flex-lg-row align-items-center h-100 custom-modal">
        <div class="tab-pane fade show active d-flex justify-content-center"
             id="kt_ecommerce_add_product_general"
             role="tab-pane">
            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                <div class="card card-flush w-50 align-self-center w-600px">
                    <div class="card-body">
                        <div class="spacing gap-4 d-flex flex-column">
                            <div>
                                <h2 class="text-left mb-3">{{ __('Shift Z Report') }}</h2>
                            </div>
                            <div>
                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Register') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-reg-name"></label>,&nbsp;
                                        <label class="cash-reg-key"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Returns') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-returns"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Sales') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-sales"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Expected amount') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="expected-amount"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Location Name') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="location-name"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Opened at') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="opened-at"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Status') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="shift-status"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Short/Over') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="short-over"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Total net sales') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="total-net-sales"></label>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex gap-3">
                                <button type="button" onclick="location.href = `{{ route('login.pos') }}`" class="btn btn-primary w-100 print-x-report">{{ __('Go to login') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content xreport-employee fullscreen hiding hidden flex-switch justify-content-center flex-column flex-lg-row align-items-center h-100 custom-modal">
        <div class="tab-pane fade show active d-flex justify-content-center"
             id="kt_ecommerce_add_product_general"
             role="tab-pane">
            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                <div class="card card-flush w-50 align-self-center w-600px">
                    <div class="card-body">
                        <div class="spacing gap-4 d-flex flex-column">
                            <div>
                                <h2 class="text-left mb-3">{{ __('Shift X Report') }}</h2>
                                <select id="employees" name="employees" class="form-select mb-2"
                                        data-control="select2" data-placeholder="{{ __('Select employee') }}"
                                        data-allow-clear="true">
                                    <option value="0" selected>{{ __('All Employees') }}</option>
                                </select>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="button" close-custom-modal="xreport-employee" class="btn btn-secondary w-100">{{ __('Cancel') }}</button>
                                <button type="button" confirm-custom-modal="xreport-employee" class="btn btn-primary w-100 print-x-report">{{ __('Continue') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content xreport-modal fullscreen hiding hidden flex-switch justify-content-center flex-column flex-lg-row align-items-center h-100 custom-modal">
        <div class="tab-pane fade show active d-flex justify-content-center"
             id="kt_ecommerce_add_product_general"
             role="tab-pane">
            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                <div class="card card-flush w-50 align-self-center w-600px">
                    <div class="card-body">
                        <div class="spacing gap-4 d-flex flex-column">
                            <div>
                                <h2 class="text-left mb-3">{{ __('Shift X Report') }}</h2>
                            </div>
                            <div>
                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Register') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-reg-name"></label>,&nbsp;
                                        <label class="cash-reg-key"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Returns') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-returns"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Cash Sales') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="cash-sales"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Employees') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="employees"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Expected amount') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="expected-amount"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Location Name') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="location-name"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Opened at') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="opened-at"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Status') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="shift-status"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Short/Over') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="short-over"></label>
                                    </div>
                                </div>

                                <div class="cash-register d-flex justify-content-between align-items-center">
                                    <div class="left">
                                        <label>{{ __('Total net sales') }}</label>
                                    </div>
                                    <div class="right d-flex">
                                        <label class="total-net-sales"></label>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex gap-3">
                                <button type="button" close-custom-modal="xreport-modal" class="btn btn-secondary w-100">{{ __('Cancel') }}</button>
                                <button type="button" confirm-custom-modal="xreport-modal" class="btn btn-primary w-100 print-x-report">{{ __('Print') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>

        function customerScreenUpdate(data) {
            data.cash_register = `{{ session()->get('cash_register_data')->id }}`

            data.cash_register
            if ('Mine' in window) {
                window.Mine.postMessage('js_second:' + JSON.stringify(data));
            } else {
                // If you're not on PC just send this to customer screen
                console.log(JSON.stringify(data));
            }
        }

        customerScreenUpdate({reload: true});
    </script>

    <script>

        // This is a secondary object do not delete even if you cannot find references as it gets called, far away from here but it gets called
        // Used to get raw details incase stuff go wrong on payment with card
        const rawTerminal = {
            // This is used when we're sending the request, so if the response is slow and we've already sent it (so we're waiting for java) we don't send another one by accident
            debugging: false,

            intermediate: false,
            statuses: { BEFORE_PAYMENT: 1, AFTER_PAYMENT: 2 },
            flows: { IDLING: 0, REPRINTING: 1, REVERSING: 2 },
            doReversal: false,
            currentConnectedStatus: window.connected_terminal,
            statusLoop: null,
            sendingDetails: {},
            // Flow will control whether it's during process or ended so if status loop is continuing or a session already started this can assure you do not do something wrong
            flow: 0,
            // So the flow doesn't get stuck if java failed something without status
            lastInstruction: 0,

            failedRequest: function(status, message) {
                if (debugging) alert(message);
                // Show message later
                this.startStatusLoop();
            },

            startStatusLoop: function() {
                if (this.statusLoop != null) clearInterval(this.statusLoop);
                this.statusLoop = setInterval(() => {
                    this.statusLoop();
                }, 15000);
            },

            statusLoop: function() {
                if (debugging) alert('Sending status requeest');
                if ('Mine' in window)
                    window.Mine.postMessage('terminal_sts:');
                else alert('You are not in APK, terminal_sts:')
            },

            statusLoopDetails: function(status) {
                if (debugging) {
                    alert('Intermediate: ' + this.intermediate + " - " + this.flow + " - " + JSON.stringify(status));
                }

                if (this.intermediate) return;

                if (status.connected) {
                    this.currentConnectedStatus = true;
                    if (status.waitingReprint) {
                        this.flow = this.flows.REPRINTING;
                        this.reprintTicket();
                        // This also sets intermediate to true so until it finishes this funciton will not execute
                        return;
                    }

                    if (this.flow == this.flows.REPRINTING && !status.waitingReprint && this.doReversal) {
                        this.flow = this.flows.REVERSING;
                        this.reversePayment();
                        // This also sets intermediate to true so until it finishes this funciton will not execute
                        return;
                    }

                    if (this.flow == this.flows.REPRINTING || this.flow == this.flows.REVERSING) {
                        // 5 minutes
                        if (Date.now() - this.lastInstruction > 300000) {
                            // Flow has not changed state within 5 minutes
                            if (debugging) alert('5 minutes passed without change');
                        }

                        return;
                    }

                    if (this.flow == this.flows.IDLING && this.currentConnectedStatus && !status.waitingReprint) {
                        // Means there is no required flow, reprint is not needed, we should continue our day and end loop
                        if (debugging) alert('Full flow was done');
                        if (this.statusLoop != null)
                            clearInterval(this.statusLoop);
                    }
                } else {
                    if (debugging) alert('Not connected');
                    if (this.flow == this.flows.IDLING) {
                        // Just continue looping
                        return;
                    }

                    // Loop was interrupted during a phase...
                    // This works if we always go at the beginning (this may need to change if reversal has it's own reprint)
                    this.lastInstruction = Date.now();
                    this.flow = 0;
                }
            },

            rawDetails: function(details) {

            },

            reprintTicket: function() {
                this.intermediate = true;
                this.lastInstruction = Date.now();
            },

            reprintTicketDetails: function(details) {

                // This must happen at the end of the function
                this.intermediate = false;
            },

            reversePayment: function() {
                this.intermediate = true;
                this.lastInstruction = Date.now();
            },

            reversePaymentDetails: function(details) {

                // This must happen at the end of the function
                this.intermediate = false;
            }

        }

        const cardProceed = {
            opened: false,
            proceed: function() {
                this.opened = true;
                const closingButtons = document.querySelectorAll('.pay-card-buttons');
                const openingButtons = document.querySelectorAll('.close-pay-card');

                for (let btn of closingButtons) {
                    btn.classList.add('d-none');
                }

                for (let btn of openingButtons) {
                    btn.classList.remove('d-none');
                }
            },

            returnState: function() {
                this.opened = false;
                const openingButtons = document.querySelectorAll('.pay-card-buttons');
                const closingButtons = document.querySelectorAll('.close-pay-card');

                for (let btn of closingButtons) {
                    btn.classList.add('d-none');
                }

                for (let btn of openingButtons) {
                    btn.classList.remove('d-none');
                }
            }
        }

        const paymentLoader = {
            load: () => {
                const loaders = document.getElementsByClassName('payment-loader');
                for (let loader of loaders) {
                    loader.classList.remove('hidden');
                }
            },
            close: () => {
                const loaders = document.getElementsByClassName('payment-loader');
                for (let loader of loaders) {
                    loader.classList.add('hidden');
                }
            }
        }

        paymentLoader.close();

        window.debugging_print = false;
        window.reconnect_string = '';

        window.terminal_alert = true;
        window.connected_terminal = true;
        rawTerminal.currentConnectedStatus = true;
        window.terminal_testing = false;
        window.terminal_ip = '';

        window.log_alert = false;

        window.printer_testing = false;
        window.connected_printer = false;
        const taxReport = {
            taxList: [],
            add: (taxPerc, taxAmountVal) => {
                const taxLists = document.getElementsByClassName('tax-list');
                const taxListsAmount = document.getElementsByClassName('tax-list-amount');

                for (let taxList of taxLists) {
                    const taxEl = document.createElement('span');
                    taxEl.classList.add('d-block');
                    taxEl.classList.add('font-span');

                    const taxPercShow = parseFloat(taxPerc).toFixed(2);
                    taxEl.innerHTML = `{{ __('Tax') }} <span class="tax-area-${taxPerc}">${taxPercShow}%</span>`
                    taxList.appendChild(taxEl);
                }

                for (let taxListAmount of taxListsAmount) {
                    const taxAmount = document.createElement('span');
                    taxAmount.classList.add('d-block');
                    taxAmount.classList.add('font-span');
                    taxAmount.classList.add('tax-area-amount-'+ taxPerc);

                    let price = parseFloat(taxAmountVal).toFixed(2);
                    let taxString = window.currency_symbol + price;
                    if (window.currency_pos_left == 0) taxString = price + window.currency_symbol;

                    taxAmount.textContent = taxString;
                    taxListAmount.appendChild(taxAmount);
                }
            },
            clear: () => {
                const taxLists = document.getElementsByClassName('tax-list');
                const taxListsAmount = document.getElementsByClassName('tax-list-amount');

                for (let taxList of taxLists) {
                    if (taxList.children.length > 0) {
                        for (let i = taxList.children.length - 1; i >= 0; i--) taxList.children[i].remove();
                    }
                }

                for (let taxListAmount of taxListsAmount) {
                    if (taxListAmount.children.length > 0) {
                        for (let i = taxListAmount.children.length - 1; i >= 0; i--) taxListAmount.children[i].remove();
                    }
                }
            }
        }

        /*
        let lastActivity = Date.now();
        document.addEventListener('click', e => {
            lastActivity = Date.now();
        })

        const min15 = 900000;
        setInterval(() => {
            let now = Date.now();
            if (now - lastActivity > min15) {
                const logForm = document.getElementById('logout-form');
                if (logForm)
                    logForm.submit();
            }
        }, 5000);
        */

        function proceedAndClean() {
            if (cardProceed.opened)
                wipe_all();

            cardProceed.returnState();
        }

        $(document).on('click', '.paymentMethods', function() {
            cardProceed.returnState();
        });

        // Closing the pay modal after paying must wipe because we keep information for few extra time
        $('#mixModal').on('hide.bs.modal', function(e) {
            proceedAndClean();
        });

        $('#bankModal').on('hide.bs.modal', function(e) {
            proceedAndClean();
        });

        $('#cashModal').on('hide.bs.modal', function(e) {
            proceedAndClean();
        });

        $('#myModal').on('hide.bs.modal', function(e) {
            proceedAndClean();
        });

        let searchingTimeout = null;

        $(document).on('DOMContentLoaded', () => {
            $('.custom-modal').each((index, element) => {
                element.close = () => {
                    element.classList.add('hiding');
                    setTimeout(() => {
                        element.classList.add('hidden');
                    }, 700);
                }

                element.open = () => {
                    element.classList.remove('hidden');
                    setTimeout(() => {
                        element.classList.remove('hiding');
                    }, 100);
                }



                const searchProducts = document.getElementsByClassName('search-product');
                for (let searchProduct of searchProducts) {
                    searchProduct.value = '';
                    searchProduct.addEventListener('keyup', e => {
                        if (searchingTimeout != null) clearTimeout(searchingTimeout);
                        if (searchProduct.value == '' || searchProduct.value.length <= 2) return;

                        searchingTimeout = setTimeout(() => {
                            $.ajax({
                                method: 'GET',
                                url: '/admin/pos/search?search=' + searchProduct.value,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType: 'json',
                                async: true,
                                success: function (resp) {
                                    if (resp.status == 2) {
                                        location.href = resp.redirect_uri;
                                        return;
                                    }

                                    if (resp.status == 1) {
                                        // These will change with the library in the next update
                                        Swal.fire({
                                            text: resp.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        });
                                        return;
                                    }

                                    if (resp.status == 0) {
                                        $(".all-products").empty();
                                        categoryActive.clear();
                                        addProductList(resp.data.products, resp.data.modifiers);
                                    }

                                    if ('message' in resp && resp.message != '' && resp.message != null) {
                                        Swal.fire({
                                            text: resp.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        })
                                    }
                                },
                                error: function(err) {
                                    console.log(err);
                                    Swal.fire({
                                        text: `{{ __('An unexpected error occured') }}`,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: `{{ __('Ok, got it!') }}`,
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                }
                            })
                        }, 100);
                    })
                }

            })

            $(document).on('click', '.open-drawer', e => {
                e.preventDefault();
                e.stopPropagation();

                openDrawer();
                return false;
            })

            $(document).on('click', '[close-custom-modal]', function() {
                const modalToClose = this.getAttribute('close-custom-modal');

                const modal = document.querySelectorAll(`.custom-modal.${modalToClose}`);
                if (modal.length > 0) {
                    for (let mod of modal) {
                        if ('close' in mod) mod.close();
                    }
                }
            });

            $(document).on('click', '[open-custom-modal]', async function() {
                const modalToClose = this.getAttribute('open-custom-modal');

                if (modalToClose == 'zreport-continue') {
                    $.ajax({
                        method: 'GET',
                        url: '/admin/pos/getClosingAmount',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (resp) {
                            if (resp.status == 2) {
                                location.href = resp.redirect_uri;
                                return;
                            }

                            if (resp.status == 1) {
                                // These will change with the library in the next update
                                Swal.fire({
                                    text: resp.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                                return;
                            }

                            if (resp.status == 0) {
                                let data = resp.data;

                                let price = parseFloat(data.saldo).toFixed(2);
                                let saldoString = window.currency_symbol + price;
                                if (window.currency_pos_left == 0) saldoString = price + window.currency_symbol;

                                price = parseFloat(data.total_balance_with_cash).toFixed(2);
                                let expected = window.currency_symbol + price;
                                if (window.currency_pos_left == 0) expected = price + window.currency_symbol;


                                $('.saldo-amount').text(saldoString);
                                $('.saldo-expected').text(expected);
                            }

                            if ('message' in resp && resp.message != '') {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            Swal.fire({
                                text: `{{ __('An unexpected error occured') }}`,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __('Ok, got it!') }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    })
                }

                const modal = document.querySelectorAll(`.custom-modal.${modalToClose}`);
                if (modal.length > 0) {
                    for (let mod of modal) {
                        if ('open' in mod) mod.open();
                    }
                }
            });

            let printingString = '';
            $(document).on('click', '[confirm-custom-modal]', function() {
                const fromModal = this.getAttribute('confirm-custom-modal');


                if (fromModal == 'xreport-employee') {
                    const reportModals = document.getElementsByClassName('xreport-modal');
                    if (reportModals.length > 0) {
                        const employeeSelect = document.getElementById('employees');
                        let employee_data = '';
                        if (employeeSelect.value != '0') employee_data = "?employee_id=" + employeeSelect.value;

                        $.ajax({
                            method: 'GET',
                            url: '/admin/pos/printXReport' + employee_data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function (resp) {
                                if (resp.status == 2) {
                                    location.href = resp.redirect_uri;
                                    return;
                                }

                                if (resp.status == 1) {
                                    // These will change with the library in the next update
                                    Swal.fire({
                                        text: resp.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                    return;
                                }

                                if (resp.status == 0) {
                                    let data = resp.data;
                                    if ('print_order' in resp)
                                        printingString = resp.print_order

                                    const xreportMaps = [
                                        {key: '.cash-reg-name', val: data.cash_register_name, type: 'text'},
                                        {key: '.cash-reg-key', val: data.cash_register_key, type: 'text'},
                                        {key: '.cash-returns', val: data.cash_returns, type: 'currency'},
                                        {key: '.cash-sales', val: data.cash_sales, type: 'currency'},
                                        {key: '.employees', val: data.employees, type: 'arr'},
                                        {key: '.expected-amount', val: data.expected_amount, type: 'currency'},
                                        {key: '.location-name', val: data.location_name, type: 'text'},
                                        {key: '.opened-at', val: data.shift_opened, type: 'text'},
                                        {key: '.shift-status', val: data.shift_status, type: 'text'},
                                        {key: '.short-over', val: data.short_over, type: 'currency'},
                                        {key: '.total-net-sales', val: data.total_net_sales, type: 'currency'},

                                    ]

                                    for (let item of xreportMaps) {
                                        let value = item.val;

                                        if (item.type == 'currency') {
                                            let price = parseFloat(item.val).toFixed(2);
                                            let priceString = window.currency_symbol + price;
                                            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                                            value = priceString;
                                        }

                                        if (item.type == 'arr') {
                                            value = value.join(', ');
                                        }

                                        $(item.key).text(value);
                                    }

                                    reportModals[0].open();
                                }

                                if ('message' in resp && resp.message != '') {
                                    Swal.fire({
                                        text: resp.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    })
                                }
                            },
                            error: function(err) {
                                console.log(err);
                                Swal.fire({
                                    text: `{{ __('An unexpected error occured') }}`,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __('Ok, got it!') }}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        })
                    }
                }

                if (fromModal == 'xreport-modal') {
                    if (window.printer_testing || window.connected_printer)
                        window.invoicePrinting(printingString);

                    $('.xreport-modal').each((item, mod) => { if ('close' in mod) mod.close() });
                    $('.xreport-employee').each((item, mod) => { if ('close' in mod) mod.close() });
                }

                if (fromModal == 'zreport-continue') {
                    $('.zreport-confirm').each((item, mod) => { if ('open' in mod) mod.open() });
                }

                if (fromModal == 'zreport-confirm') {

                    const closingValue = document.querySelector('[name="closing_amount"]').value;

                    $.ajax({
                        method: 'POST',
                        url: '/admin/pos/endZReport',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            closing_amount: parseFloat(closingValue)
                        },

                        success: function (resp) {
                            if (resp.status == 2) {
                                location.href = resp.redirect_uri;
                                return;
                            }

                            if (resp.status == 1) {
                                // These will change with the library in the next update
                                Swal.fire({
                                    text: resp.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                                return;
                            }

                            if (resp.status == 0) {
                                let data = resp.data;
                                if ('print_order' in resp)
                                    printingString = resp.print_order

                                // if (printingString && 'Mine' in window) {
                                // Mine.postMessage(`VFV:${printingString}`);

                                if (window.printer_testing || window.connected_printer)
                                    window.invoicePrinting(printingString);
                                // }

                                const zreportMaps = [
                                    {key: '.cash-reg-name', val: data.cash_register_name, type: 'text'},
                                    {key: '.cash-reg-key', val: data.cash_register_key, type: 'text'},
                                    {key: '.cash-returns', val: data.cash_returns, type: 'currency'},
                                    {key: '.cash-sales', val: data.cash_sales, type: 'currency'},
                                    {key: '.expected-amount', val: data.expected_amount, type: 'currency'},
                                    {key: '.location-name', val: data.location_name, type: 'text'},
                                    {key: '.opened-at', val: data.shift_opened, type: 'text'},
                                    {key: '.shift-status', val: data.shift_status, type: 'text'},
                                    {key: '.short-over', val: data.short_over, type: 'currency'},
                                    {key: '.total-net-sales', val: data.total_net_sales, type: 'currency'},

                                ]

                                for (let item of zreportMaps) {
                                    let value = item.val;

                                    if (item.type == 'currency') {
                                        let price = parseFloat(item.val).toFixed(2);
                                        let priceString = window.currency_symbol + price;
                                        if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                                        value = priceString;
                                    }

                                    if (item.type == 'arr') {
                                        value = value.join(', ');
                                    }

                                    $(item.key).text(value);
                                }

                                $('.zreport-modal').each((item, mod) => { if ('open' in mod) mod.open() });
                            }

                            if ('message' in resp && resp.message != '') {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            Swal.fire({
                                text: `{{ __('An unexpected error occured') }}`,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __('Ok, got it!') }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    })
                }




            });

            // Timeout just pushes it at the end of the stack
            /*
            setTimeout(() => {
                @if ($location->take_away)
            $(".take-away").click();
@elseif ($location->dine_in)
            $(".dine-in").click();
@elseif ($location->delivery)
            $(".delivery").click();
@else
            currentMode = 'none';
        takeAwayOrderList = null;
@endif
            });
            */
            currentMode = 'none';
        });

        // These will be filled when meal stuff open
        const stepController = {
            currentStep: 0,
            stepElements: [],
            nextStep: function() {
                this.currentStep++;
                let steps = this.stepElements.length;
                if (this.currentStep >= steps) {
                    this.currentStep--;
                    return null;
                }

                return this.stepElements[this.currentStep];
            },
            previousStep: function() {
                this.currentStep--;
                let steps = this.stepElements.length;
                if (this.currentStep < 0 || steps.length == 0) {
                    this.currentStep++;
                    return null;
                }

                return this.stepElements[this.currentStep];
            },
            reset: function() {
                this.currentStep = 0;
                this.stepElements = [];
            }
        }

        customerScreenUpdate({clean: true});
        /*
        $.ajax({
            method: 'POST',
            url: '/setDataCS',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            data: { data: {clean: true}},
            success: function (data) {
            },
            error: function(err) {

                console.log(err);
            }
        })
         */
        let saving = false;
        let modalType = 0;

        let e_kiosk_id = null;
        let e_kiosk_order_number = null;
        window.sizeKeys = {
            small: `{{ __('Small') }}`,
            medium: `{{ __('Medium') }}`,
            large: `{{ __('Large') }}`
        }

        let orderChange = null;
        let orderDetails = null;

        let currentTax = 0;
        window.extraTax = {};
        let maps = {'dineIn': 'dine_in', 'takeAway': 'take_away', 'delivery': 'delivery'};
        let mealConfigurations = {}

        let cashCalculationsMix = {
            mixType: 'cash',
            steppedValue: '',
            total: 0,
            cash: 0,
            bank: 0,
            change: function(key, value) {
                if (key in this) {
                    this[key] = value;
                }

                return this.calculate();
            },
            calculate: function() {
                if (this.mixType == 'cash')
                    this.bank = this.total - this.cash;
                else
                    this.cash = this.total - this.bank;

                return this;
            },
            clear: function() {
                this.steppedValue = '';
                this.cash = 0;
                this.bank = 0;
                return this;
            }
        }

        let cashCalculations = {
            steppedValue: '',
            total: 0,
            cash: 0,
            return: 0,
            change: function(key, value) {
                if (key in this) {
                    this[key] = value;
                }

                return this.calculate();
            },
            calculate: function() {
                this.return = this.total - this.cash;
                return this;
            },
            clear: function() {
                this.steppedValue = '';
                this.cash = 0;
                this.return = 0;
                return this.calculate();
            }
        }
        // Setup hover and product colours
        $('li[color]').each((index, item) => {
            let color = item.getAttribute('color');
            if (color != '' && color != 'null' && color != null) {
                item.style.setProperty('--bg-color-ex', color);
            }
        })

        let modifyingDealPrep = {}
        let modifyingDealModsPrep = {}

        let mappings = {};
        @php
            foreach($configured_apis as $api)
            echo 'mappings[`' . $api['id'] . '`] = JSON.parse(`'.json_encode($api).'`); ';
        @endphp

        let mappingsKeys = Object.keys(mappings);
        for (let key of mappingsKeys) {
            modifyingDealPrep[key] = [];
            modifyingDealModsPrep[key] = [];
        }

        function fillMealModifiers() {
            $.ajax({
                method: "POST",
                url: "/admin/pos/get/products",
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    category_ids: mappingsKeys,
                },
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        /*
                    Swal.fire({
                        text: resp.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: {{ __("Ok, got it!") }},
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                    return;
                    */
                    }

                    if (resp.status == 0) {
                        for (let key of mappingsKeys) {
                            modifyingDealPrep[key] = [];
                            modifyingDealModsPrep[key] = [];
                        }

                        let data = resp.data;
                        data?.products.forEach(function(product) {
                            product.price = parseFloat(product.price);
                            if (product.food_category_id in modifyingDealPrep)
                                modifyingDealPrep[product.food_category_id].push(product);
                        });

                        data?.modifier.forEach(function(mod) {
                            let modCategories = mod.category;
                            for (let modCat of modCategories) {
                                if (!(modCat.id in modifyingDealModsPrep)) modifyingDealModsPrep[modCat.id] = [];
                                modifyingDealModsPrep[modCat.id].push(mod);
                            }
                        })
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        let openedPaymentModals = [];
        let kitchenNotes = [];
        let currentMeals = {};
        let currentProducts = {};

        class ActiveC {
            constructor(parentClass, active_c = 'current') {
                this.parent = '.' + parentClass;
                this.current = null;
                this.active = active_c;
            }

            setParent(parentSelector) {
                this.parent = parentSelector;
            }

            setCurrent(el) {
                this.current = el;
            }

            run() {
                if (this.current) {
                    $(`${this.parent} .${this.active}`).each((index, el) => {
                        el.classList.remove(this.active);
                    });

                    if ('classList' in this.current)
                        this.current.classList.add(this.active);
                }
            }

            clear() {
                if  (this.current) {
                    this.current.classList.remove(this.active);
                    this.current = null;
                }
            }
        }

        class SwapLayouts {
            constructor(layoutSelector, clear = true, hideable = true) {
                this.keys = [];
                this.layoutSelector = layoutSelector;
                this.hideable = hideable;
                this.attach(this.layoutSelector, false);
                this.getVariables();
                if (clear) this.clear();
            }

            // Variables will only be taken from the first element selected
            getVariables() {
                if (this.parent == null) return;

                this.html = this.construct.outerHTML;
                let keys = this.html.matchAll(/{.+?}/gm);
                let next = {done: false};
                do {
                    next = keys.next();
                    if (next.done == false) {
                        let keyT = next.value;
                        for (let a of keyT)
                            this.keys.push(a);
                    }
                } while(next.done == false)
            }

            clear() {
                if (this.parent == null) return;
                this.parent.innerHTML = '';
                if (this.hideable)
                    $(this.parent).closest('.modification').css('display', 'none');
            }

            insert(obj) {
                if (this.hideable)
                    $(this.parent).closest('.modification').css('display', 'block');

                if (this.parent == null) return;
                let toChange = {};
                let objKeys = Object.keys(obj);
                for (let key of objKeys) {
                    if (this.keys.includes(`{${key}}`)) {
                        toChange[`{${key}}`] = obj[key];
                    }
                }

                let finalHtml = this.html;
                for (let key in toChange) {
                    finalHtml = finalHtml.replaceAll(key, toChange[key]);
                }

                this.parent.innerHTML += finalHtml;
            }

            attach(newSelector, clear = true) {
                this.layoutSelector = newSelector;
                let layoutHtml = document.querySelector(this.layoutSelector);
                if (layoutHtml) {
                    this.construct = layoutHtml;
                    this.parent = this.construct.parentElement;
                }

                if (clear) this.clear();
                return this;
            }

            changeParent(parentSelector) {
                this.parent = document.querySelector(parentSelector);

                return this;
            }
        }

        const drinkModifiers = new SwapLayouts('.drink-mod-modifier-layout', false);
        const drinkSize = new SwapLayouts('.drink-mod-sizes-layout', false);
        window.drinks_modGenSwapper = new SwapLayouts('.drink-mod-sizes-layout', false);
        const otherDrinkLayouts = new SwapLayouts('.all-drink-layout', false);
        const drinkLayout = new SwapLayouts('.drink-modifier-layout');

        const friesModifiers = new SwapLayouts('.fries-mod-modifier-layout', false);
        const friesSize = new SwapLayouts('.fries-mod-sizes-layout', false);
        window.fries_modGenSwapper = new SwapLayouts('.fries-mod-sizes-layout', false);
        const otherFriesLayouts = new SwapLayouts('.all-fries-layout', false);
        const friesLayout = new SwapLayouts('.fries-modifier-layout');

        const saucesModifiers = new SwapLayouts('.sauces-mod-modifier-layout', false);
        const saucesSize = new SwapLayouts('.sauces-mod-sizes-layout', false);
        window.sauces_modGenSwapper = new SwapLayouts('.sauces-mod-sizes-layout', false);
        const othersaucesLayouts = new SwapLayouts('.all-sauces-layout', false);
        const saucesLayout = new SwapLayouts('.sauces-modifier-layout');

        const modLayout = new SwapLayouts('.food-item-layout .modifier-layout', false);
        const ingLayout = new SwapLayouts('.food-item-layout .ingredients-layout', false);
        const sizeLayout = new SwapLayouts('.food-item-layout .sizes-layout', false);
        const foodLayout = new SwapLayouts('.food-item-layout', true, false);
        const serviceTables = new SwapLayouts('.service-table-layout', true, false);

        let activeness = new ActiveC('eat-options');
        let categoryActive = new ActiveC('category-holder');

        // Setup currency
        window.currency_symbol = `{{$settings['currency_symbol']}}`;
        window.currency_pos_left = `{{$settings['currency_symbol_on_left'] ? 1 : 0}}`;
        window.currency_pos_left == 1 ? window.currency_symbol += ' ' : window.currency_symbol = ' ' + window.currency_symbol;

        let locatorNumberFinal = '';
        let locatorNumber = '';
        let superTotal = 0;
        // This is used for removal and edit
        let currentEditting = -1;
        let alreadyIds = [];
        $(document).ready(function() {
            const headerText = document.getElementById('headerText');
            const teksti = document.getElementById('teksti');
            const toggle1 = document.querySelector('.switch_1');

            toggle1.addEventListener('change', function() {
                if (toggle1.checked) {
                    headerText.style.color = '#5D4BDF';
                    teksti.style.color = '#264653';
                    teksti.classList.add('opa');
                    headerText.classList.remove('opa');

                } else {
                    headerText.style.color = '#264653';
                    teksti.style.color = '#5D4BDF';

                    teksti.classList.remove('opa');
                    headerText.classList.add('opa');
                }
            });
        });



        $(document).ready(function() {

            let currentRequest;

            $(".nav-link").click(function() {

                if (currentRequest && 'abort' in currentRequest) {
                    currentRequest.abort();
                }

                var categoryId = $(this).data("category-id");
                if (categoryId == 'deals') {
                    currentRequest = getMeals(true, this);
                } else {
                    currentRequest = getProductRequest(categoryId, this);
                }
            });
        });

        var currentOrderList = {};
        var selectedTable;
        var currentMode;
        var ordersByTable = {};
        var takeAwayOrderList = {};

        function switchMode(newMode) {

            currentOrderList = takeAwayOrderList;
            /*
        if (currentMode === "dineIn" && selectedTable) {
            currentOrderList = ordersByTable[selectedTable]
            // ordersByTable[selectedTable] = [...takeAwayOrderList];
        } else if (currentMode === "takeAway") {
            currentOrderList = takeAwayOrderList;
            // takeAwayOrderList = [ordersByTable[selectedTable]];
			
        }
                */


            currentMode = newMode;


            updateOrderTableUI(currentOrderList);
            updateTotalSpan();
        }

        function toggleInpOn() {
            $('#toggle-trigger').prop('checked', true).change();
        }

        function toggleInpOff() {
            $('#toggle-trigger').prop('checked', false).change();
        }

        function addProductList(products, modifiers) {
            // Small changes in server side prices
            modifiers.forEach((mod, index) => {
                modifiers[index].price = parseFloat(mod.price);
            })

            products.forEach(function(product) {
                let catId = product.food_category_id;
                let s = $(`.category-${catId}`);
                let color = '';
                if (s.length > 0) {
                    let col = s[0].getAttribute('color');
                    if (col != '' && col != 'null' && col != null) {
                        color = `--bg-color-ex: ${col}`;
                    }
                }

                product.price = parseFloat(product.price);

                product.modifiers = modifiers;

                currentProducts[product.id] = product;
                // This could have been more dynamic but consistency has more value since there are places such a function wouldn't reach by this setup
                let price = parseFloat(product.price).toFixed(2);
                let priceString = window.currency_symbol + price;
                if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                var productHtml = `
<div class="card card-flush card-for-cart" type="product" style="${color}" pb-5 mw-100" prod="${product.id}">
    <div class="card-body text-center p-0">
        <div class="mt-3 ms-3 me-3">
            <div class="text-start">
                <span class="fw-bold cursor-pointer  product-name font-span" >${product.name}</span>
            </div>
        </div>
        <span class="text-end mb-3 me-3 fs-1 product-price">${priceString}</span>
    </div>
</div>
`;
                $(".all-products").append(productHtml);
            });
        }

        $(document).ready(function() {
            $(".dine-in").click(function() {

                @if ($location['has_tables'] && $location['has_locators'])
                $("#dine-step").modal("show");
                @elseif ($location['has_tables'])
                moveCol(this, 0, 'table-dine');
                let items = document.querySelectorAll('[id-open]');
                // Yes this executes the same parent more than once however it was necessary to remove spacing due to paddings
                for (let item of items) item.parentElement.classList.add('d-none');
                @elseif ($location['has_locators'])
                moveCol(this, 1, 'locator-dine');
                let items = document.querySelectorAll('[id-open]');
                // Yes this executes the same parent more than once however it was necessary to remove spacing due to paddings
                for (let item of items) item.parentElement.classList.add('d-none');
                @else
                Swal.fire({
                    text: `{{ __('The location does not have tables or locators however the dine in is enabled') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
                @endif

                activeness.setCurrent(this);
            });

            $(".take-away").click(function() {
                // switchMode("takeAway");
                currentMode = "takeAway";
                if (currentMode in maps)
                    changeTax(maps[currentMode]);

                currentOrderList = takeAwayOrderList;
                updateOrderTableUI(currentOrderList);
                updateTotalSpan();
                switchMode('takeAway');

                activeness.setCurrent(this);
                activeness.run();

            });

            $(document).on('click', '.x-report-print', function() {

            });

            $(document).on('click', '.tavolina', function() {
                let newTable = $(this).data("table-id");

                if ($(this).hasClass('busy')) {
                    $.ajax({
                        method: 'GET',
                        url: '/admin/pos/saveOrder',
                        data: {
                            table_id: newTable
                        },
                        dataType: 'json',
                        success: async resp => {
                            if (resp.status == 2) {
                                return location.href = resp.redirect_uri;
                            }

                            if (resp.status == 1) {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                                return;
                            }

                            const order = resp.data.order;

                            loadOrder(order);

                            if (resp.message != '') {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });

                            }
                        },
                        error: err => {
                            Swal.fire({
                                text: `{{ __('Issue with the server, please contact your local administrator') }}`,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });

                        }
                    })

                    $("#tableModal").modal("hide");
                    return;
                }

                currentMode = "dineIn";
                selectedTable = newTable;

                if (currentMode in maps)
                    changeTax(maps[currentMode]);

                if (currentMode === "dineIn" && selectedTable) {
                    if (!(selectedTable in ordersByTable)) {
                        ordersByTable[selectedTable] = {};
                    }
                    // currentOrderList = ordersByTable[selectedTable];
                }

                $("#tableModal").modal("hide");
                switchMode("dineIn");
                updateOrderTableUI(currentOrderList);
                updateTotalSpan();
                activeness.run();
            });
        });

        function getOrderListForTable(tableId) {
            if (!ordersByTable[tableId]) {
                ordersByTable[tableId] = [];
            }
            return ordersByTable[tableId];
        }

        var originalPrices = [];
        var modifiersByFood = {};


        $(document).on('click', '.card-for-cart', function() {
            let type = this.getAttribute('type');
            if (type == 'product') {
                const id = this.getAttribute('prod');
                if (!(id in currentProducts)) return location.reload();

                let productDetails = currentProducts[id];
                let counterFound = 0;
                for (let sK in productDetails.size) {
                    if (productDetails.size[sK] != null) counterFound++;
                }

                let hasSize = counterFound > 1;

                if (productDetails.modifiers.length == 0 && productDetails.ingredients.length == 1 && !hasSize) {
                    let returnedRow = addToOrder(productDetails.id, 'product', productDetails.name, productDetails.price, productDetails, true, true);
                    editRow(returnedRow[0], true, false);
                    updateFoodChanges();
                } else {
                    addToOrder(productDetails.id, 'product', productDetails.name, productDetails.price, productDetails);
                }
            }

            if (type == 'deal' || type == 'meal') {
                const id = this.getAttribute('deal');
                if (!(id in currentMeals)) return location.reload();

                let mealDetails = currentMeals[id];
                addToOrder(mealDetails.id, 'deal', mealDetails.name, mealDetails.price, mealDetails);
            }
        })

        function addMealItem(row, mealId) {
            let modDisp = $(row).find('#modifiersDisplay');
            if (modDisp.length == 0 || !(mealId in currentMeals)) return

            const meal = currentMeals[mealId];

            const items = meal.food_items;
            let strings = [];
            items.forEach(item => {
                strings.push(`${item.pivot.quantity}x ${item.name}`);
            })

            const string = '<span class="d-block">' + strings.join('</span><span class="d-block">') + '</span>';
            modDisp.html(string);
            return string;
        }

        function addToOrder(id, type, _name, _price, details, force = false, edittable = true) {
            if (takeAwayOrderList == null) {
                Swal.fire({
                    text: `{{ __('You do not have any forms of delivery enabled on your current location') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            let currentOrderIndex = alreadyIds.length;

            let name = _name;
            var price = parseFloat(_price).toFixed(2);
            $(".name-on-edit").text(name);
            $(".price-on-edit").text(price);
            $(".price-on-edit").val(price);

            if (details.price_change) {
                $('.price-on-edit').removeAttr('readonly');
                $('.price-on-edit').addClass('bg-white');
            } else {
                $('.price-on-edit').attr('readonly', 'readonly');
                $('.price-on-edit').removeClass('bg-white');
            }
            // $(".quantity-on-edit").val(quantityColumn);
            // var hasModifiers = $(clickedElement).closest('.card-body').find('.modifiers').length > 0;


            var newRow = $(`<tr class='height-following p-0 box-bg table-radius row-ter' type="${type}" product="${id}">`);
            if (!edittable)
                newRow.addClass('ready-item');

            if (!force) {
                newRow[0].style.display = 'none';
            } else {
                newRow[0].final = true;
            }

            newRow.append($(`<td class='fw-bold font-span justify-content-center d-flex w-100px align-items-center edit-td btn-violet text-white' id='edit-td'>{{ __('Edit') }}</td>`));
            var nameAndModifiersColumn = $("<td class='table-left-side fw-bold font-span pt-3 pb-3 ps-3 name-and-modifiers-table'></td>");
            nameAndModifiersColumn.append($("<div class='fw-bold fs-7 pe-3 name-table'>").text(name));

            modifiersByFood[currentOrderIndex] = {
                row: newRow,
                modifiers: []
            };

            nameAndModifiersColumn.append($("<div class='fw-bold fs-4' id='modifiersDisplay'></div>"));
            newRow.append(nameAndModifiersColumn);

            nameAndModifiersColumn.append($("<div class='fw-bold fs-4' id='itemsDisplay'></div>"));

            addMealItem(newRow, id);

            var quantityColumn = $("<td>").addClass("height-follower quantity-column justify-content-center quantity-table d-flex align-items-center");
            var divColumn = $('<div>').addClass('d-flex w-5em align-items-center justify-content-between');

            divColumn.append($("<button>").text("-").addClass("quantity-btn fs-6 minus-btn"));
            divColumn.append($("<span>").text("1").addClass("quantity-value fs-6"));
            divColumn.append($("<button>").text("+").addClass("quantity-btn fs-6 plus-btn"));
            quantityColumn.append(divColumn);


            newRow.append(quantityColumn);


            let priceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

            let priceElement = $('<div class="w-100 text-end">').html(`
            <div class="initial-cond w-100"><span class="external-quan">1</span>x <span class="orig-price">${priceString}</span></div>
            <div class="price-column w-100 font-span">${priceString}</div>
        `);

            newRow.append($("<td class='table-right-side price-column-del text-center pe-4 text-violet fs-9'>").append(priceElement));


            newRow.append($(`<td class='fw-bold font-span text-center w-100px delete-td justify-content-center d-flex align-items-center  btn-orange text-white'>{{ __('Delete') }}</td>`));
            newRow.css("padding-bottom", "10px");
            alreadyIds.push(newRow);

            newRow[0].indexFor = currentOrderIndex;

            if (type == 'product' && edittable) {
                newRow[0].scaling = {
                    ingredient_list: '',
                    sizeScale: 0,
                    modifiers: {}
                }

                newRow[0].scaling.ingredient_list = ''.padStart(details.ingredients.length, '0');
            }

            newRow[0].price = price;

            // currentOrderList[currentOrderIndex] = newRow;
            if (currentMode === "takeAway") {
                takeAwayOrderList[currentOrderIndex] = newRow;
                currentOrderList = takeAwayOrderList;
                // takeAwayOrderList.push(newRow);
            } else {
                if (!(selectedTable in ordersByTable)) ordersByTable[selectedTable] = {};
                takeAwayOrderList[currentOrderIndex] = newRow;
                currentOrderList = takeAwayOrderList;
                ordersByTable[selectedTable][currentOrderIndex] = newRow;
                // currentOrderList = ordersByTable[selectedTable];
            }

            if (!force)
                modalType = 1;

            const editModal = document.getElementById('edit-itemModal');

            if (type == 'product') {
                const productSizes = details.size;
                let sizeScale = 0;
                for (let size in productSizes) {
                    sizeScale++;
                    if (productSizes[size] != null) {
                        setRowSize(currentOrderIndex, size, null, sizeScale);
                        updateRow(newRow);
                        updateQuantity(newRow, 0, 0, [], true);
                        break;
                    }
                }
            }

            if (type == 'meal' || type == 'deal') {
                if (type && type == 'deal') {
                    $('.tab-meal-switcher.opened-by-default').click();
                    editModal.classList.add('deal');
                } else {
                    editModal.classList.remove('deal');
                }

            }

            originalPrices.push(price);
            updateModifiersUI(currentOrderIndex, type);
            updateOrderTableUI(currentOrderList);
            updateTotalSpan();


            if (!force) {
                if (type == 'meal' || type == 'deal') {
                    editRow(newRow[0], true, false);
                    mealEditModal();
                    $('#edit-meal-modifiers').modal('show');
                } else {
                    editRow(newRow[0], true);
                }
            }

            return newRow;

            // updateOrderNumber();
            // var editModalBtn = document.getElementById('edit-td');
            // var editModal = document.getElementById('edit-itemModal');


            // editModalBtn.addEventListener('click', function() {
            //     $(editModal).modal('show');
            // });

            //     $('#thetable').on('click', '.edit-td', function() {
            //     var editModal = $(this).closest('tr').find('.edit-modal');
            //     $(editModal).modal('show');
            // });

            // $(".name-table").click(function () {
            //     var currentTransformEdit = $(".edit-td").css('transform');

            // var editTransform = (currentTransformEdit === 'matrix(1, 0, 0, 1, -94, 0)') ? 'translateX(0)' : 'translateX(-94px)';

            // $(".edit-td").css('transform', editTransform);
            // });


            // $(".price-column").click(function () {

            //     var currentTransformDelete = $(".delete-td").css('transform');

            // var deleteTransform = (currentTransformDelete === 'matrix(1, 0, 0, 1, 40, 0)') ? 'translateX(-56px)' : 'translateX(40px)';

            // $(".delete-td").css('transform', deleteTransform);
            // });

        }

        //var modifiersByFood = {};
        function handleModifierClick(modifier) {
            const currentFoodRow = currentOrderList[currentEditting];
            let index = currentFoodRow[0].indexFor;

            let type = currentFoodRow[0].getAttribute('type');

            let modifierId = $(modifier).data('modifier-id');
            let modifierName = $(modifier).data('name');
            let modifierPrice = $(modifier).data('price');

            if (type == 'deal' || type == 'meal') {
                let prodId = modifier.getAttribute('prod');
                const id = modifier.getAttribute('mod-id');
                const partOf = modifier.getAttribute('tip');

                let rowId = index + "--" + prodId;

                let nums = ['sauces-num', 'drink-num', 'fries-num', 'prod_num'];
                let extra = '';
                let attr = '';
                for (let num of nums) {
                    attr = modifier.getAttribute(num);
                    if (attr) {
                        // rowId += '--' + attr + '.' + num;
                        rowId += '--' + attr;
                        break;
                    }
                }

                if (!(currentEditting in mealConfigurations)) {
                    mealConfigurations[currentEditting] = {
                        main_meal: {},
                        drinks: {},
                        sauces: {},
                        fries: {}
                    }
                }

                const configurations = mealConfigurations[currentEditting];

                if (partOf in configurations) {
                    if (!(attr in configurations[partOf])) {
                        mealConfigurations[currentEditting][partOf][attr] = {}
                    }

                    if (!('modifiers' in mealConfigurations[currentEditting][partOf][attr])) {
                        mealConfigurations[currentEditting][partOf][attr].modifiers = [];
                    }

                    mealConfigurations[currentEditting][partOf][attr].modifiers.push({
                        id: modifierId,
                        modifierName: modifierName,
                        modifierPrice: modifierPrice,
                        type: 'add'
                    });
                }

                index = rowId;
            }

            if (!modifiersByFood[index]) {
                modifiersByFood[index] = {
                    row: currentFoodRow,
                    modifiers: []
                };
            }

            if ('scaling' in currentFoodRow[0]) {
                if (modifierId in currentFoodRow[0].scaling.modifiers) currentFoodRow[0].scaling.modifiers[modifierId]++;
                else currentFoodRow[0].scaling.modifiers[modifierId] = 1;
            }

            modifiersByFood[index].modifiers.push({
                id: modifierId,
                modifierName: modifierName,
                modifierPrice: modifierPrice,
                type: 'add'
            });

            // Find the modifier amount and update the counter
            let amount = modifiersByFood[index].modifiers.reduce((acc, modifier) => {
                if (modifier.id == modifierId) acc++;
                return acc;
            }, 0);

            if (amount == 0) {
                $(modifier).removeClass('active-mod');
                $(modifier).find('.modifier-quantity').text('');
            } else {
                $(modifier).addClass('active-mod');
                $(modifier).find('.modifier-quantity').text(`${amount}x `);
            }

            updateQuantity(currentFoodRow, 0);
            updateModifiersUI(index, type);
            updateTotalSpan();
        }

        function editRow(currentRow, insert = false, modal = true) {
            const editModal = document.getElementById('edit-itemModal');
            let quan = $(currentRow).find('.quantity-value');
            if (0 in quan) $('#editting-quantity').text(quan.text());

            let type = currentRow.getAttribute('type');
            // Fix overall modal
            if (type && type == 'deal') {
                $('.tab-meal-switcher.opened-by-default').click();
                editModal.classList.add('deal');
            } else {
                editModal.classList.remove('deal');
            }

            // Add extra data based on product or deal
            let displayUIName = '';
            let displayUIPrice = '';

            let nonClickedIds = [];
            let clickIngred = null;
            let currentProduct = null;
            if (type == 'product') {
                const prodId = currentRow.getAttribute('product');
                if (!(prodId in currentProducts)) return location.reload();

                currentProduct = currentProducts[prodId];
                displayUIName = currentProduct.name;
                displayUIPrice = currentProduct.price;

                const ingredients = currentProduct.ingredients;
                const ingred = $('.edit-ingredients');
                const modify = $('.edit-modifiers');
                const sizes = $('.edit-size');
                clickIngred = ingred;

                ingred.empty();

                let ind = currentRow.indexFor;

                let modsIng = [];
                let modsMod = [];

                let currentSize = '';
                if (ind in modifiersByFood && 'modifiers' in modifiersByFood[ind]) {
                    let modsMove = modifiersByFood[ind].modifiers;
                    modsIng = modsMove.filter(item => item.type == 'remove').map(item => item.id);
                    modsMod = modsMove.filter(item => item.type == 'add').map(item => item.id);

                    if ('sizeSelected' in modifiersByFood[ind]) {
                        for (let s in modifiersByFood[ind].sizeSelected) {
                            if (modifiersByFood[ind].sizeSelected[s] != null) {
                                currentSize = s;
                                break;
                            }
                        }
                    }
                }


                const placesForIngredients = $('.product-ingredients-box');
                placesForIngredients.hide();
                let ingScale = 0;
                let first = true;

                for (let ingredient of ingredients) {
                    let used = modsIng.includes(ingredient.id) ? 'btn-secondary' : 'btn-primary';

                    if (insert) {
                        if (modsIng.length == 0) {

                            used = 'btn-primary';
                            if (!modifiersByFood[ind]) modifiersByFood[ind] = {}
                            if (!('modifiers' in modifiersByFood[ind])) modifiersByFood[ind].modifiers = [];

                            if (first) {
                                // Old method
                                // If you wish to continue only highlighting the first ingredient, uncoment the line below
                                // first = false;
                                /*
                                used = 'btn-primary';
                                if (!modifiersByFood[ind]) modifiersByFood[ind] = {}
                                if (!('modifiers' in modifiersByFood[ind])) modifiersByFood[ind].modifiers = [];
                                */
                            } else {
                                nonClickedIds.push(ingredient.id);

                                // Old method
                                /*
                                used = 'btn-secondary';
                                modifiersByFood[ind].modifiers.push({
                                        id: ingredient.id,
                                        modifierName: ingredient.name,
                                        modifierPrice: 0,
                                        type: 'remove'
                                });
                                */

                            }
                        }
                    }

                    ingScale++;
                    placesForIngredients.show();
                    const holder = document.createElement('div');

                    holder.innerHTML = `<a href="#" ing="${ingredient.id}" scale="${ingScale}" class="btn ${used} font-span fw-bold py-4 gray-border ingredient-list">${ingredient.name}</a>`;
                    ingred.append(holder);
                }

                const modifiers = currentProduct.modifiers;
                modify.empty();
                const placesForModifiers = $('.product-modifiers-box');
                placesForModifiers.hide();
                for (let modifier of modifiers) {
                    placesForModifiers.show();
                    const holder = document.createElement('li');
                    holder.className = `d-flex gray-border btn edit-buttons p-0 modifier-li custom-input-bg table-radius nav-item me-0 modifier-${modifier.id}`;

                    let price = parseFloat(modifier.price).toFixed(2);
                    let priceString = window.currency_symbol + price;
                    if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                    // Find the modifier amount and update the counter
                    let amountText = '';
                    let amount = 0
                    if (ind in modifiersByFood && 'modifiers' in modifiersByFood[ind]) {
                        amount = modifiersByFood[ind].modifiers.reduce((acc, mod) => {
                            if (mod.id == modifier.id) acc++;
                            return acc;
                        }, 0);

                        if (amount != 0)
                            amountText = `${amount}x `;
                    }

                    let active_cl = amount == 0 ? '' : 'active-mod';

                    holder.innerHTML = `
                    <a class="justify-content-center position-relative w-100 custom-input-bg modifier-list-item ${active_cl} btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show active" style="width: 138px; padding: 1px !important;" data-name="${modifier.title}" data-price="${modifier.price}" data-modifier-id="${modifier.id}">
                        <div class="">
                            <span class="text-gray-800 font-span modifier-name d-block "><span class="modifier-quantity">${amountText}</span>${modifier.title}</span>
                            <span class="font-span modifier-name remove-mod text-danger position-absolute end-0 top-0 h-100 px-3 d-flex align-items-center" data-modifier-id="${modifier.id}">X</span>
                        </div>
                        <div class="">
                            <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">+ ${priceString}</span>
                        </div>
                    </a>
                `;

                    modify.append(holder);
                }

                const sizesArr = Object.keys(currentProduct.size);
                sizes.empty();
                const placesForSize = $('.product-size-box');
                placesForSize.hide();

                let firstSize = true;
                let sizeScale = 0;
                for (let sizeA of sizesArr) {
                    sizeScale++;
                    if (currentProduct.size[sizeA] == null) continue;
                    placesForSize.show();
                    const holder = document.createElement('li');
                    holder.className = `d-flex gray-border btn edit-buttons p-0 modifier-li custom-input-bg table-radius nav-item mb-3 me-0 size-${sizeA}`;

                    // Price is only used for the view the price is gotten elsewhere so we have added the small or smallest logic here
                    let price = parseFloat(currentProduct.size[sizeA]).toFixed(2);
                    if (firstSize) {
                        price = parseFloat(currentProduct.price).toFixed(2);
                    }

                    let priceString = window.currency_symbol + price;
                    if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                    if (!firstSize)
                        priceString = `+ ${priceString}`;

                    firstSize = false;

                    let name = sizeA;
                    if (sizeA in window.sizeKeys) name = window.sizeKeys[sizeA];

                    let active = '';
                    if (sizeA == currentSize) active = 'btn-violet';

                    holder.innerHTML = `
                    <a data-other="mod-prod" scale="${sizeScale}" class="justify-content-center position-relative w-100 custom-input-bg size-list-item btn btn-color-gray-600 btn-active-text-gray-800  px-2  btn-flex  flex-column flex-stack pt-9 pb-7 page-bg show ${active}" style="width: 138px; padding: 1px !important;" data-name="${name}" data-price="${currentProduct.size[sizeA]}" data-size="${sizeA}">
                        <div class="">
                            <span class="text-gray-800 font-span modifier-name d-block ">${name}</span>
                        </div>
                        <div class="">
                            <span class="text-gray-800  d-block modifier-price text-violet smaller-heading font-heading">${priceString}</span>
                        </div>
                    </a>
                `;

                    sizes.append(holder);
                }
            }

            if (type == 'deal') {
                const prodId = currentRow.getAttribute('product');
                if (!(prodId in currentMeals)) return location.reload();
                const currentMeal = currentMeals[prodId];
                displayUIName = currentMeal.name;
                displayUIPrice = currentMeal.price;
            }


            $(".name-on-edit").text(displayUIName);
            $(".price-on-edit").text(displayUIPrice);
            $(".price-on-edit").val(displayUIPrice);

            if (currentProduct != null) {
                if (currentProduct.price_change) {
                    $('.price-on-edit').removeAttr('readonly');
                    $('.price-on-edit').addClass('bg-white');
                } else {
                    $('.price-on-edit').attr('readonly', 'readonly');
                    $('.price-on-edit').removeClass('bg-white');
                }
            }

            if (type != 'deal' && type != 'meal') {
                $(".price-on-edit").text(parseFloat(currentRow.price));
                $(".price-on-edit").val(parseFloat(currentRow.price));
            }


            currentEditting = currentRow.indexFor;
            if (modal) {
                $(editModal).modal('show');
            }


            if (clickIngred != null) {
                for (let removalIngred of nonClickedIds) {
                    const i = clickIngred[0].querySelector(`[ing="${removalIngred}"]`);
                    if (i) i.click();
                }
            }
        }

        function handleModifierClickRem(modifier) {
            const currentFoodRow = currentOrderList[currentEditting];
            let index = currentFoodRow[0].indexFor;

            let modifierId = $(modifier).data('modifier-id');

            let type = currentFoodRow[0].getAttribute('type');
            if (type == 'deal' || type == 'meal') {
                let prodId = modifier.getAttribute('prod');
                const id = modifier.getAttribute('mod-id');
                const partOf = modifier.getAttribute('tip');

                let rowId = index + "--" + prodId;

                let nums = ['sauces-num', 'drink-num', 'fries-num', 'prod_num'];
                let extra = '';
                let attr = '';
                for (let num of nums) {
                    attr = modifier.getAttribute(num);
                    if (attr) {
                        // rowId += '--' + attr + '.' + num;
                        rowId += '--' + attr;
                        break;
                    }
                }

                if (!(currentEditting in mealConfigurations)) {
                    mealConfigurations[currentEditting] = {
                        main_meal: {},
                        drinks: {},
                        sauces: {},
                        fries: {}
                    }
                }

                const configurations = mealConfigurations[currentEditting];

                if (partOf in configurations) {
                    if (!(attr in configurations[partOf])) {
                        mealConfigurations[currentEditting][partOf][attr] = {}
                    }

                    if (!('modifiers' in mealConfigurations[currentEditting][partOf][attr])) {
                        mealConfigurations[currentEditting][partOf][attr].modifiers = [];
                    }

                    let mods = mealConfigurations[currentEditting][partOf][attr].modifiers;
                    if (mods.length > 0) {
                        for (let i = mods.length - 1; i >= 0; i--) {
                            if (mods[i].id == modifierId) {
                                mealConfigurations[currentEditting][partOf][attr].modifiers.splice(i, 1);
                            }
                        }
                    }
                }

                index = rowId;
            }

            if ('scaling' in currentFoodRow[0]) {
                if (modifierId in currentFoodRow[0].scaling.modifiers) delete currentFoodRow[0].scaling.modifiers[modifierId];
            }

            if (!modifiersByFood[index]) {
                modifiersByFood[index] = {
                    row: currentFoodRow,
                    modifiers: []
                };
            }

            let mods = modifiersByFood[index].modifiers;
            if (mods.length > 0) {
                for (let i = mods.length - 1; i >= 0; i--) {
                    if (mods[i].id == modifierId) {
                        modifiersByFood[index].modifiers.splice(i, 1);
                    }
                }
            }

            // Find the modifier amount and update the counter
            let amount = modifiersByFood[index].modifiers.reduce((acc, modifier) => {
                if (modifier.id == modifierId) acc++;
                return acc;
            }, 0);

            if (amount == 0) {
                $(modifier).removeClass('active-mod');
                $(modifier).find('.modifier-quantity').text('')
            } else {
                $(modifier).addClass('active-mod');
                $(modifier).find('.modifier-quantity').text(`${amount}x `);
            }

            updateQuantity(currentFoodRow, 0);
            updateModifiersUI(index, type);
            updateTotalSpan();
        }


        function handleIngredientClick(modifier) {
            const el = modifier.parentElement.parentElement.querySelectorAll('.btn-primary');
            if (modifier.classList.contains('btn-primary') && el.length == 1) {
                return;
            }

            const currentFoodRow = currentOrderList[currentEditting];
            let index = currentFoodRow[0].indexFor;

            const modifierName = modifier.textContent;
            let type = currentFoodRow[0].getAttribute('type');
            let currentConfig = null;
            if (type == 'deal' || type == 'meal') {
                let prodId = modifier.getAttribute('prod');
                const id = modifier.getAttribute('mod-id');
                const partOf = modifier.getAttribute('tip');

                let rowId = index + "--" + prodId;

                let nums = ['sauces-num', 'drink-num', 'fries-num', 'prod_num'];
                let extra = '';
                let attr = '';
                for (let num of nums) {
                    attr = modifier.getAttribute(num);
                    if (attr) {
                        // rowId += '--' + attr + '.' + num;
                        rowId += '--' + attr;
                        break;
                    }
                }

                if (!(currentEditting in mealConfigurations)) {
                    mealConfigurations[currentEditting] = {
                        main_meal: {},
                        drinks: {},
                        sauces: {},
                        fries: {}
                    }
                }

                const configurations = mealConfigurations[currentEditting];

                if (partOf in configurations) {
                    if (!(attr in configurations[partOf])) {
                        mealConfigurations[currentEditting][partOf][attr] = {}
                    }

                    if (!('modifiers' in mealConfigurations[currentEditting][partOf][attr])) {
                        mealConfigurations[currentEditting][partOf][attr].modifiers = [];
                    }

                    currentConfig = mealConfigurations[currentEditting][partOf][attr].modifiers;
                }

                index = rowId;
            }

            if (modifier.classList.contains('btn-primary')) {
                modifier.classList.remove('btn-primary');
                modifier.classList.add('btn-secondary');

                if (!modifiersByFood[index]) {
                    modifiersByFood[index] = {
                        row: currentFoodRow,
                        modifiers: []
                    }
                }

                const scalingPlace = modifier.getAttribute('scale');
                if (index in currentOrderList) {
                    const list = currentOrderList[index][0].scaling.ingredient_list.split('');
                    list[parseFloat(scalingPlace) - 1] = '1';
                    currentOrderList[index][0].scaling.ingredient_list = list.join('');
                }

                let id = modifier.getAttribute('ing');
                modifiersByFood[index].modifiers.push({
                    id: id,
                    modifierName: modifierName,
                    modifierPrice: 0,
                    type: 'remove'
                })

                if (currentConfig != null) {
                    currentConfig.push({
                        id: id,
                        modifierName: modifierName,
                        modifierPrice: 0,
                        type: 'remove'
                    });
                }
            } else {
                modifier.classList.add('btn-primary');
                modifier.classList.remove('btn-secondary');

                const scalingPlace = modifier.getAttribute('scale');
                if (index in currentOrderList) {
                    const list = currentOrderList[index][0].scaling.ingredient_list.split('');
                    list[parseFloat(scalingPlace) - 1] = '0';
                    currentOrderList[index][0].scaling.ingredient_list = list.join('');
                }

                if (modifiersByFood[index]) {
                    for (let i = modifiersByFood[index].modifiers.length-1; i >= 0; i--) {
                        let item = modifiersByFood[index].modifiers[i];
                        if (item.type == 'remove' && item.modifierName == modifierName)
                            modifiersByFood[index].modifiers.splice(i, 1);
                    }
                }

                if (currentConfig != null) {
                    for (let i = currentConfig.length-1; i >= 0; i--) {
                        let item = currentConfig[i];
                        if (item.type == 'remove' && item.modifierName == modifierName)
                            currentConfig.splice(i, 1);
                    }
                }
            }


            updateModifiersUI(index, type);
            updateTotalSpan();
        }

        // function updateModifiersUI(foodName) {
        //     var currentFood = modifiersByFood[foodName];

        //     currentFood.row.find("#modifiersDisplay").empty();

        //     if (currentFood.modifiers && currentFood.modifiers.length) {
        //         var modifierNames = currentFood.modifiers.map(modifier => modifier.modifierName);
        //         var modifierSpan = $("<span>").text("Added: " + modifierNames.join(', '));
        //         currentFood.row.find("#modifiersDisplay").append(modifierSpan);
        //     }
        // }
        function updateModifiersUI(foodName, type = 'product') {
            if (type != 'product') return;

            const currentFood = modifiersByFood[foodName];

            if (!currentFood || !currentFood.row) {
                return;
            }

            var modifiersDisplay = currentFood.row.find("#modifiersDisplay");

            if (modifiersDisplay.length === 0) {
                return;
            }

            modifiersDisplay.empty();

            if (currentFood.modifiers && currentFood.modifiers.length) {
                let addedMods = currentFood.modifiers.filter(modifier => modifier.type == 'add');
                let removedMods = currentFood.modifiers.filter(modifier => modifier.type == 'remove');

                if (addedMods.length > 0) {
                    const modifierQuantity = {};
                    const modifierNames = {};

                    addedMods.forEach(mod => {
                        if (!modifierQuantity[mod.id]) modifierQuantity[mod.id] = 0;
                        modifierQuantity[mod.id]++;

                        if (!modifierNames[mod.id]) modifierNames[mod.id] = mod.modifierName;
                    })

                    const modifierSpan = $('<span>').addClass('d-block').text("Add: " + Object.keys(modifierQuantity).map(key => `${modifierQuantity[key]}x ${modifierNames[key]}`).join(', '));
                    modifiersDisplay.append(modifierSpan);
                }

                if (removedMods.length > 0) {
                    var modifierNames = removedMods.map(modifier => modifier.modifierName);
                    var modifierSpan = $('<span>').addClass('d-block').text("No: " + modifierNames.join(', '));
                    modifiersDisplay.append(modifierSpan);
                }
            }
        }




        $(document).ready(function() {
            let printingZReportString = '';

            $(document).on("click", '.remove-mod', function(e) {
                e.preventDefault();
                handleModifierClickRem(this.parentElement.parentElement);
                return false;
            });

            $(document).on("click", ".modifier-list-item", function(e) {
                handleModifierClick(this);
            });

            $(document).on("click", ".ingredient-list", function() {
                handleIngredientClick(this);
            });



            $(".discard-food-changes").on("click", function() {
                dismissModalEvent(this);
            });

            $('.attempt-print-last-z-report').on('click', function() {
                $("#confirm-reprint-z-report").modal("show");
                $('#report-more-modal').modal('hide');
            });

            $('.confirm-print-last-z-report').on('click', function() {
                $("#pin-accept-modal-zreport").modal("show");
            });

            $('.pin-submit').on('submit', function(e) {
                printingZReportString = '';
                e.preventDefault();
                const pin = $(this).find('[name="pin"]').val();
                // $.ajax check pin and continue
                //open-z-report-btn
                $.ajax({
                    method: 'GET',
                    url: `/admin/pos/pinToPrintReports?pin_to_print_reports=${pin}`,
                    success: resp => {
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            return;
                        }

                        if (resp.status == 0) {
                            printingZReportString = resp.data.printString;

                            $("#moreModal").modal('hide');
                            $("#confirm-reprint-z-report").modal("hide");
                            $('#report-more-modal').modal('show');

                            /*
                            $("#confirm-reprint-z-report").modal("hide");
                            $("#pin-accept-modal-zreport").modal("show");
                             */
                            $("#pin-z-rep").val('');
                        }

                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    },
                    error: err => {
                        console.log(err);
                        Swal.fire({
                            text: `{{ __('An unexpected error occured') }}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                })

            });

            $('.print-last-z-report-form').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (window.printer_testing || window.connected_printer)
                    window.invoicePrinting(printingZReportString);

                Swal.fire({
                    text: `{{ __('ZReport is printing') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })

                $("#pin-accept-modal-zreport").modal("hide");

                return false;
            })

            $(".update-food-changes").on("click", function() {
                if (this.classList.contains('meal') && modalType == 1) {
                    const next = stepController.nextStep();
                    if (next != null) {
                        saving = true;
                        window.manualStep = true;
                        next[0].click();
                        window.manualStep = false;
                        return;
                    }
                }
                saving = true;
                updateFoodChanges();
                $('#edit-meal-modifiers').modal('hide');
            });


        });

        function dismissModalEvent(element = null) {
            // if (originalPrices.length > 0) {
            //     var originalPrice = originalPrices.pop();


            //     let price = parseFloat(originalPrice).toFixed(2);
            //     let priceString = window.currency_symbol + price;
            //     if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
            //     $("#orderTableBody tr:last").find(".price-column").text(priceString);

            //     updateTotalSpan();
            // }
            if (modalType == 1) {
                if (!saving) {
                    if (element == null || !element.classList.contains('meal-changes'))
                        if (currentEditting in currentOrderList) delete currentOrderList[currentEditting];
                }
            } else {
                updateFoodChanges();
            }

            updateOrderTableUI(currentOrderList);
            updateTotalSpan();
            $("#edit-itemModal").modal('hide');
        }

        function updateFoodChanges() {
            const scalingValues = currentOrderList[currentEditting][0].scaling;
            const currentRow = currentOrderList[currentEditting];

            if (currentEditting in currentOrderList && 'scaling' in currentOrderList[currentEditting][0]) {
                if (modalType == 1 && currentEditting in currentOrderList) {
                    // Check scaling

                    let scalingFound = findSimiliar(currentRow, scalingValues);
                    if (scalingFound != null) {
                        // Scaling found we must delete the extra row and it's spacer
                        delete currentOrderList[currentEditting];

                        // Add new quantity to the found row
                        updateQuantity(scalingFound, 1);
                        updateOrderTableUI(currentOrderList);
                    }
                } else {
                    // Check for similarities after edit
                    const scalingFound = findSimiliar(currentRow, scalingValues);
                    if (scalingFound != null) {
                        // Scaling found we must delete the extra row and it's spacer
                        const quantityEl = currentOrderList[currentEditting].find(".quantity-value");
                        const currentQuantity = parseInt(quantityEl.text());

                        delete currentOrderList[currentEditting];

                        // However this time you must get the quantity and add it to the new row instead of just 1
                        updateQuantity(scalingFound, currentQuantity);
                        updateOrderTableUI(currentOrderList);
                    }
                }
            }


            // Disconnected logic from the scaling for products that can't scale
            if (modalType == 1) {
                if (currentEditting in currentOrderList) {
                    currentOrderList[currentEditting][0].style.display = 'table-row';
                    currentOrderList[currentEditting][0].final = true;
                }
            }


            const newPrice = document.getElementById('price-on-edit');
            if (newPrice && !isNaN(parseFloat(newPrice.value))) {
                currentRow[0].price = parseFloat(newPrice.value);
            }

            // Check if the item hasn't been merged
            if (currentEditting in currentOrderList)
                updateModifiersUI(currentEditting, currentOrderList[currentEditting][0].getAttribute('type'));

            updateTotalSpan();
            $("#edit-itemModal").modal('hide');
        }


        function findSimiliar(currentRow, scalingValues) {
            for (let key in currentOrderList) {
                if (key == currentEditting) continue;

                let row = currentOrderList[key]
                let order = row[0];

                const productId = order.getAttribute('product');
                const productIdCurr = currentRow[0].getAttribute('product');
                if (productId != productIdCurr) continue;

                if (scalingValues.ingredient_list == order.scaling.ingredient_list && scalingValues.sizeScale == order.scaling.sizeScale) {
                    const modKeys = Object.keys(scalingValues.modifiers);
                    const modKeysOrder = Object.keys(order.scaling.modifiers);

                    if (modKeys.length == modKeysOrder.length) {
                        let similar = true;
                        for (let id of modKeys) {
                            if (!(id in order.scaling.modifiers) || scalingValues.modifiers[id] != order.scaling.modifiers[id]) {
                                similar = false;
                                break;
                            }
                        }

                        if (similar) {
                            return currentOrderList[key];
                            break;
                        }
                    }
                }
            }

            return null;
        }

        function updateOrderTableUI(orderList) {
            var orderTableBody = $("#orderTableBody");
            orderTableBody.empty();

            let oL = [];
            for (let item in orderList) {
                oL.push(orderList[item]);
                let space = document.createElement('tr');
                space.classList.add('spacer');
                if (orderList[item][0].classList.contains('ready-item')) {
                    let line = document.createElement('tr');
                    line.classList.add('is-ready-line');

                    const fillingTD = document.createElement('td');
                    fillingTD.setAttribute("colspan", 5);
                    fillingTD.textContent = "Ready";

                    line.appendChild(fillingTD);
                    oL.push(line);
                }
                oL.push(space);
            }

            orderTableBody.append(oL);
        }




        // Function to update the quantity
        // function updateQuantity(row, change, originalPrice) {
        //     var quantityElement = row.find(".quantity-value");
        //     var currentQuantity = parseInt(quantityElement.text());
        //     var newQuantity = currentQuantity + change;

        //     if (newQuantity < 0) {
        //         newQuantity = 0;
        //     }

        //     quantityElement.text(newQuantity);

        //     var priceColumn = row.find(".price-column");
        //     var newPrice = originalPrice * newQuantity;
        //     priceColumn.text(newPrice.toFixed(2) + '€');

        //     updateTotalSpan();
        // }
        function updateRow(row) {
            const rowEl = $(row)[0];
            const type = rowEl.getAttribute('type');
            const index = rowEl.indexFor;

            if (type == 'product') {
                const prodId = rowEl.getAttribute('product');
                const product = currentProducts[prodId];

                if (index in modifiersByFood) {
                    // If you ever decide to change where the modifiers get added you can do it here else for now we will just continue with the name


                    const nameEl = row.find('.name-table');
                    if (nameEl.length != 0) {
                        let name = product.name;
                        const modifications = modifiersByFood[index];
                        if ('sizeSelected' in modifications) {
                            let sizes = modifications.sizeSelected;
                            for (let size in sizes) {
                                if (sizes[size] != null && size in sizeKeys) {
                                    name += ', ' + sizeKeys[size];
                                    continue;
                                }
                            }
                        }
                        nameEl.text(name);
                    }


                }
            }
        }

        function updateQuantity(row, change, old_originalPrice, modifiers, force = false) {
            // We use this like this because updateQuantity is called everywhere and existed prior, weird params mate
            updateRow(row);

            var quantityElement = row.find(".quantity-value");

            let type = row[0].getAttribute('type');
            let productId = row[0].getAttribute('product');
            let index = row[0].indexFor;

            let originalPrice = 0;

            if (type == 'product')
                // originalPrice = parseFloat(currentProducts[productId].price);
                originalPrice = parseFloat(row[0].price);
            else if (type == 'meal' || type == 'deal')
                originalPrice = parseFloat(currentMeals[productId].price);
            else return;


            var currentQuantity = parseInt(quantityElement.text());
            var newQuantity = currentQuantity + change;


            if (newQuantity < 1) {
                newQuantity = 1;
            }

            quantityElement.text(newQuantity);
            var priceColumn = row.find(".price-column");



            let mods = {modifiers: []};
            if (index in modifiersByFood && !(type == 'meal' || type == 'deal')) {
                mods = modifiersByFood[index];

                if ('sizeSelected' in modifiersByFood[index]) {
                    let sizePrice = 0;
                    for (let size in modifiersByFood[index].sizeSelected) {
                        let priceSelCur = modifiersByFood[index].sizeSelected[size];
                        if (priceSelCur == null) continue;
                        sizePrice += parseFloat(priceSelCur);
                    }

                    originalPrice += sizePrice;
                }
            }

            // Current
            if (type == 'meal' || type == 'deal') {
                const configurations = mealConfigurations[index];

                for (let type in configurations) {
                    for (let item in configurations[type]) {
                        const details = configurations[type][item];

                        /*
                        if ('extraPrice' in details && details.extraPrice != null) {
                            originalPrice += details.extraPrice;
                        }
                         */

                        if ('sizeSelected' in details) {
                            let sizePrice = 0;
                            for (let size in details.sizeSelected) {
                                let priceSelCur = details.sizeSelected[size];
                                if (priceSelCur == null) continue;
                                sizePrice += parseFloat(priceSelCur);
                            }

                            originalPrice += sizePrice;
                        }

                        if ('modifiers' in details) {
                            mods.modifiers = mods.modifiers.concat(details.modifiers);
                        }
                    }
                }
                // let checkingIndex = index + '--';

                /*
                for (let key in modifiersByFood) {
                    if (!key.startsWith(checkingIndex)) continue;
                    let currMods = modifiersByFood[key];

                    if ('sizeSelected' in currMods) {
                        let sizePrice = 0;
                        for (let size in currMods.sizeSelected) {
                            let priceSelCur = currMods.sizeSelected[size];
                            if (priceSelCur == null) continue;
                            sizePrice += parseFloat(priceSelCur);
                        }

                        originalPrice += sizePrice;
                    }

                    if ('modifiers' in currMods) {
                        mods.modifiers = mods.modifiers.concat(currMods.modifiers);
                    }
                }
                 */
            }

            var totalModifierPrice = calculateTotalModifierPrice(mods.modifiers);

            let origPrice = originalPrice + totalModifierPrice;
            var newPrice = (origPrice) * newQuantity;

            let price = parseFloat(origPrice).toFixed(2);
            let priceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
            row.find('.orig-price').text(priceString);

            price = newPrice.toFixed(2);
            priceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
            priceColumn.text(priceString);
            row.find('.external-quan').text(newQuantity);


            if (!force)
                updateTotalSpan();

            return newPrice;
        }


        // Don't forget to update the row after
        function setRowSize(rowIndex, size, element = null, scale = 0) {
            if (rowIndex in currentOrderList && currentOrderList[rowIndex].length > 0) {
                const row = currentOrderList[rowIndex][0];
                const type = row.getAttribute('type');
                const ind = row.indexFor;

                if (type == 'product') {
                    const prodId = row.getAttribute('product');
                    const product = currentProducts[prodId];

                    let scaling = null;
                    if (element != null) scaling = element.getAttribute('scale');
                    else scaling = scale;

                    if ('scaling' in row && scaling != null) row.scaling.sizeScale = scaling;

                    // if (this.classList.contains('btn-violet')) size = '';

                    if (!(ind in modifiersByFood)) modifiersByFood[ind] = {row: currentOrderList[rowIndex], modifiers: []};
                    if (!('sizeSelected' in modifiersByFood[ind])) modifiersByFood[ind].sizeSelected = {};

                    const sizes = product.size;
                    for (let s in sizes) {
                        if (s == size)
                            modifiersByFood[ind].sizeSelected[s] = sizes[s];
                        else
                            modifiersByFood[ind].sizeSelected[s] = null
                    }

                }

                if ((type == 'meal' || type == 'deal') && element != null) {
                    const id = element.getAttribute('mod-id');
                    const productId = element.getAttribute('prod');
                    const partOf = element.getAttribute('tip');

                    let rowId = ind + "--" + productId;

                    let nums = ['sauces-num', 'drink-num', 'fries-num', 'prod_num'];
                    let extra = '';
                    let attr = '';
                    for (let num of nums) {
                        attr = element.getAttribute(num);
                        if (attr) {
                            // rowId += '--' + attr + '.' + num;
                            rowId += '--' + attr;
                            break;
                        }
                    }

                    const product = currentProducts[productId];

                    if (!(rowId in modifiersByFood)) modifiersByFood[rowId] = {row: currentOrderList[rowId], modifiers: []};
                    if (!('sizeSelected' in modifiersByFood[rowId])) modifiersByFood[rowId].sizeSelected = {};

                    if (!(currentEditting in mealConfigurations)) {
                        mealConfigurations[currentEditting] = {
                            main_meal: {},
                            drinks: {},
                            sauces: {},
                            fries: {}
                        }
                    }

                    const configurations = mealConfigurations[currentEditting];

                    if (partOf in configurations) {
                        if (!(attr in configurations[partOf])) {
                            mealConfigurations[currentEditting][partOf][attr] = {}
                        }

                        mealConfigurations[currentEditting][partOf][attr].sizeSelected = {};
                    }

                    const sizes = product.size;
                    for (let s in sizes) {
                        if (s == id) {
                            if (attr in mealConfigurations[currentEditting][partOf] && 'sizeSelected' in mealConfigurations[currentEditting][partOf][attr]) {
                                mealConfigurations[currentEditting][partOf][attr].sizeSelected[s] = sizes[s];
                            }

                            modifiersByFood[rowId].sizeSelected[s] = sizes[s];
                        } else {
                            if (attr in mealConfigurations[currentEditting][partOf] && 'sizeSelected' in mealConfigurations[currentEditting][partOf][attr])
                                mealConfigurations[currentEditting][partOf][attr].sizeSelected[s] = null;

                            modifiersByFood[rowId].sizeSelected[s] = null
                        }
                    }
                }
            }
        }

        // function updateQuantity(row, change, originalPrice, modifiers) {
        //     var quantityElement = row.find(".quantity-value");
        //     var currentQuantity = parseInt(quantityElement.text());
        //     var newQuantity = currentQuantity + change;

        //     if (newQuantity < 0) {
        //         newQuantity = 0;
        //     }

        //     quantityElement.text(newQuantity);

        //     var priceColumn = row.find(".price-column");

        //     var totalModifierPrice = calculateTotalModifierPrice(modifiers);

        //     var newPrice = (originalPrice + totalModifierPrice) * newQuantity;
        //     priceColumn.text(newPrice.toFixed(2) + '€');

        //     updateTotalSpan();
        // }







        function calculateTotalModifierPrice(modifiers) {
            var totalModifierPrice = 0;
            for (let mod of modifiers) {
                if (mod.type == 'add') {
                    totalModifierPrice += parseFloat(mod.modifierPrice);
                }
            }
            return totalModifierPrice;
        }


        // function calculateTotalPrice() {
        //     var total = 0;

        //     $("#orderTableBody tr").each(function () {
        //         var priceString = $(this).find(".price-column").text();
        //         var price = parseFloat(priceString);

        //         total += isNaN(price) ? 0 : price;
        //     });

        //     return total;
        // }
        function calculateTotalPrice(manual = false) {
            let total = 0;
            let totNo = 0;
            let totNoTax = 0;

            if (currentTax == null) {
                Swal.fire({
                    text: `{{ __('The location has no tax created for the selected form of delivery') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            if (!currentOrderList) return {total: 0, noDisc: 0};
            for (let i in currentOrderList) {
                let item = currentOrderList[i];
                if (item[0].style.display == 'none') continue;
                let c = updateQuantity(item, 0, 0, [], true);
                total += c;
                totNo += c;
                totNoTax += c;
            }

            let cTax = parseFloat(currentTax);
            if (!isNaN(cTax)) {
                let taxFactor = (100 / (100 + cTax));
                total = taxFactor * total;
                totNo = taxFactor * totNo;
                totNoTax = taxFactor * totNoTax;

                // total = (total + currentTax * 0.01 * total).toFixed(2);
            }

            if ('discountT' in window) {
                let disc = parseFloat(window.discountAmount);
                if (!isNaN(disc)) {
                    if (window.discountT == 'solid') {
                        total -= disc;
                        totNoTax -= disc;
                    }

                    if (window.discountT == 'perc') {
                        total = total - total * disc * 0.01
                        totNoTax = totNoTax - totNoTax * disc * 0.01
                    }
                }
            }

            if (!isNaN(cTax)) {
                total = (total + currentTax * 0.01 * total).toFixed(2);
            }

            if (!manual) {
                window.manualDisc = true;
                $('.add-disc').click();
            }

            let taxT = parseFloat(parseFloat(totNoTax) * (currentTax * 0.01)).toFixed(2);
            let priceString = window.currency_symbol + taxT;
            if (window.currency_pos_left == 0) priceString = taxT + window.currency_symbol;

            $('.tax-area-amount').text(priceString);

            const elements = currentOrderList;
            window.extraTax = {};

            const backData = saveOrder();


            let totalPriceFull = 0;
            let totalPriceNoTax = 0;
            let discountAmountValue = 0;

            let is_percentage_disc = window.discountT == 'perc';
            let discount_amount = window.discountAmount;
            let discountInPercentage = discount_amount;

            if (backData && backData.items.length > 0) {
                if (window.discountT != 'perc') {
                    let allSubTotals = 0;
                    for (let item of backData.items) {
                        let product = null;
                        if (item.type == 'product'){
                            product = currentProducts[item.id];
                        } else if (item.type == 'meal' || item.type == 'deal') {
                            product = currentMeals[item.id];
                        }

                        let taxValue = currentTax;
                        if (product.tax) taxValue = parseFloat(product.tax.tax_rate);

                        allSubTotals += item.sub_total / (1 + taxValue * 0.01);
                    }
                    discountInPercentage = (backData.discount_amount / allSubTotals) * 100;
                }

                for (let item of backData.items) {
                    let product = null;
                    if (item.type == 'product') product = currentProducts[item.id];
                    else if (item.type == 'meal' || item.type == 'deal') product = currentMeals[item.id];

                    let taxValue = currentTax;

                    let currTax = {
                        id: 0,
                        tax_rate: currentTax
                    }

                    if (product.tax) {
                        taxValue = parseFloat(product.tax.tax_rate);

                        currTax = {
                            id: product.tax.id,
                            tax_rate: taxValue
                        }
                    }

                    let priceWithoutTax = item.sub_total / (1 + taxValue * 0.01);
                    let priceAfterDiscount = priceWithoutTax - priceWithoutTax * discountInPercentage * 0.01;

                    totalPriceFull += priceAfterDiscount + priceAfterDiscount * taxValue * 0.01;
                    discountAmountValue += priceWithoutTax - priceAfterDiscount;
                    totalPriceNoTax += priceWithoutTax;

                    if (!(currTax.id in window.extraTax))
                        window.extraTax[currTax.id] = {perc: currTax.tax_rate, value: 0}

                    window.extraTax[currTax.id].value += priceAfterDiscount * taxValue * 0.01;
                }
            }

            let areaPercentage = $('.discount-area');
            let areaAmount = $('.discount-area-amount');

            areaPercentage.text(parseFloat(discountInPercentage).toFixed(2) + "%");

            let areaAmountPrice = parseFloat(discountAmountValue).toFixed(2);
            let areaAmountString = window.currency_symbol + areaAmountPrice;
            if (window.currency_pos_left == 0) areaAmountString = areaAmountPrice + window.currency_symbol;

            areaAmount.text(areaAmountString);

            taxReport.clear();
            for (let tax in window.extraTax) {
                taxReport.add(window.extraTax[tax].perc, window.extraTax[tax].value);
            }

            return { total: totalPriceFull, noDisc: totNo, subTot: totalPriceNoTax, disc: {perc: discountInPercentage, value: areaAmountString} };
        }

        function updateTotalSpan() {
            const totalPrice = calculateTotalPrice();
            superTotal = totalPrice.total;

            let price = parseFloat(totalPrice.total);
            let priceNoDisc = totalPrice.subTot.toFixed(2);

            price = price.toFixed(2);
            let priceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

            let priceStringNoDisc = window.currency_symbol + priceNoDisc;
            if (window.currency_pos_left == 0) priceStringNoDisc = priceNoDisc + window.currency_symbol;

            $("#totalSpan").text(priceString);
            $("#totalOnDiscount").text(priceString);
            $("#totalOnCash").text(priceString);
            $("#totalOnBank").text(priceString);
            $("#totalOnMix").text(priceString);
            $("#subTotal").text(priceStringNoDisc);
            $("#subTotalCash").text(priceStringNoDisc);
            $("#subTotalBank").text(priceStringNoDisc);
            $("#subTotalMix").text(priceStringNoDisc);

            let order = saveOrder();

            if (order) {
                order.final_price = priceString;
                order.final_disc_value = totalPrice.disc.value;
                order.final_disc_percentage = totalPrice.disc.perc;

                customerScreenUpdate(order);

                /*
                $.ajax({
                    method: 'POST',
                    url: '/setDataCS',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    dataType: 'json',
                    data: { data: order },
                    success: function (data) {

                    },
                    error: function(err) {

                    }
                })
                */
            } else {
                customerScreenUpdate({clean: 'true'});
            }
        }

        $(document).ready(function() {

            $(".clear-button").click(function() {
                e_kiosk_id = null;
                e_kiosk_order_number = null;
                wipe_all();
                // clearAllOrders();
            });

        });

        function clearAllOrders() {
            /*
        let or = {};
        if (currentMode === "takeAway") {
            or = takeAwayOrderList = {};
        } else if (currentMode === "dineIn" && selectedTable) {
            or = ordersByTable[selectedTable] = {};
        }
        */

            takeAwayOrderList = {};
            updateOrderTableUI(takeAwayOrderList);
            updateTotalSpan();
        }



        $(document).ready(function() {

            updateTotalSpan();


        });
        $(document).ready(function() {
            $(".calculator").hide();
            $(".keyboard-show").click(function() {
                $(".calculator").show();
            })

        });

        $(document).ready(function() {
            $(".waves-effect").click(function() {
                var value = $(this).val();
                if (this.classList.contains('mix-mod')) {
                    cashCalculationsMix.change('total', superTotal);
                    runCalculationsMix(value, this);
                    updateMixInputs();
                } else {
                    cashCalculations.change('total', superTotal);
                    updateRecipientAmount(value, this);
                    updateTotalAndBalance()
                }
            });

            $(".all-clear").click(function() {
                clearRecipientAmount();
                updateTotalAndBalance();

                cashCalculationsMix.clear();
                updateMixInputs();

                updateRecipientAmount(0, this);
                updateTotalAndBalance()
            });
        });

        function runCalculationsMix(value, inp = false) {
            let curr = cashCalculationsMix.steppedValue;

            if (value == 'total') {
                cashCalculationsMix.steppedValue = superTotal;
                if (cashCalculationsMix.mixType == 'cash')
                    cashCalculationsMix.change('cash', superTotal);
                else
                    cashCalculationsMix.change('bank', superTotal);

                return;
            }

            if (inp && inp.classList.contains('addition')) {
                let curr2 = parseFloat(curr);
                curr2 = isNaN(curr2) ? 0 : curr2;
                let final = curr2 + parseFloat(value);

                cashCalculationsMix.change('steppedValue', final);
                if (cashCalculationsMix.mixType == 'cash')
                    cashCalculationsMix.change('cash', final)
                else
                    cashCalculationsMix.change('bank', final)

            } else {
                if (value == '.' && curr.includes('.')) return;
                let final = cashCalculationsMix.steppedValue + value;
                cashCalculationsMix.change('steppedValue', final);

                if (cashCalculationsMix.mixType == 'cash')
                    cashCalculationsMix.change('cash', parseFloat(final));
                else
                    cashCalculationsMix.change('bank', parseFloat(final));

            }
        }

        function updateMixInputs() {
            let price = parseFloat(cashCalculationsMix.cash).toFixed(2);
            let cashPriceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) cashPriceString = price + window.currency_symbol;

            price = parseFloat(cashCalculationsMix.bank).toFixed(2);
            let bankPriceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) bankPriceString = price + window.currency_symbol;

            $('.cash-mix-input').val(cashPriceString);
            $('.bank-mix-input').val(bankPriceString);
        }

        function updateRecipientAmount(value, inp = false) {
            var recipientAmountInput = $(".recipient-amount");

            // let curr = recipientAmountInput.val();
            let curr = cashCalculations.steppedValue;

            if (value == 'total') {
                cashCalculations.steppedValue = superTotal;
                cashCalculations.change('cash', superTotal);

                let price = parseFloat(superTotal).toFixed(2);
                let priceString = window.currency_symbol + price;
                if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
                recipientAmountInput.val(priceString);
                return;
            }

            if (inp && inp.classList.contains('addition')) {
                let curr2 = parseFloat(curr);
                curr2 = isNaN(curr2) ? 0 : curr2;
                let final = curr2 + parseFloat(value);

                cashCalculations.change('steppedValue', final);
                cashCalculations.change('cash', final)

                let price = parseFloat(final).toFixed(2);
                let priceString = window.currency_symbol + price;
                if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
                recipientAmountInput.val(priceString);
            } else {
                if (value == '.' && curr.includes('.')) return;
                let final = cashCalculations.steppedValue + value;
                cashCalculations.change('steppedValue', final);
                cashCalculations.change('cash', parseFloat(final));

                let price = parseFloat(final).toFixed(2);
                let priceString = window.currency_symbol + price;
                if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
                recipientAmountInput.val(priceString);
            }
        }

        function clearRecipientAmount() {
            cashCalculations.steppedValue = '';
            $(".recipient-amount").val('');
            updateTotalAndBalance();
        }

        function clearMixInputs() {
            $('.cash-mix-input').val('');
            $('.bank-mix-input').val('');
        }

        $(document).on('keyup', '.no-key-up', function(e) {
            e.preventDefault();
            return false;
        })

        $(document).on('keydown', '.no-key-up', function(e) {
            e.preventDefault();
            return false;
        })

        function updateTotalAndBalance() {
            // var totalOnCash = $("#totalOnCash");
            var balanceInput = $(".balance");

            if (cashCalculations.steppedValue == '') return balanceInput.val('')

            var currentTotal = cashCalculations.total; // parseFloat(totalOnCash.text().replace('$', '').trim()) || 0;
            var recipientAmount = cashCalculations.cash; // parseFloat($(".recipient-amount").val()) || 0;

            var diff =  recipientAmount - currentTotal;

            cashCalculations.change('return', diff);

            let price = parseFloat(diff).toFixed(2);
            let priceString = window.currency_symbol + price;
            if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
            balanceInput.val(priceString);
            if (price >= 0) {
                balanceInput.addClass('cs-border');
                balanceInput.removeClass('input-bg-danger');
                balanceInput.removeClass('border-danger');
            } else {
                balanceInput.removeClass('cs-border');
                balanceInput.addClass('input-bg-danger');
                balanceInput.addClass('border-danger');
            }
        }
        // function updateOrderNumber() {
        //     var orderCount = 1;
        //         var orderNumberElement = $(".order-number");

        //         orderNumberElement.text(orderCount++);
        //     }
        //     $(document).ready(function () {
        //     $("#thetable").on("click", ".delete-td", function () {
        //         var row = $(this).closest("tr");
        //         row.remove();
        //     });
        // });


        $(document).ready(function() {
            $(".take-away").click(function() {
                updateOrderNumber();
            });

            $("#tablebtn").click(function() {
                updateOrderNumber();
            });

            $("#thetable").on("click", ".moved-delete .delete-td", function() {
                var row = $(this).closest("tr");
                if (!(0 in row)) return;
                let index = row[0].indexFor;
                delete currentOrderList[index];
                row.remove();
                calculateTotalPrice();
                updateTotalSpan();
                updateOrderTableUI(currentOrderList);
            });
        });

        function generateRandomNumber() {
            var randomNumber = Math.floor(10000000 + Math.random() * 9000000);
            return "#" + randomNumber;
        }

        function updateOrderNumber() {
            var orderNumber = generateRandomNumber();
            var orderNumberElement = $(".order-number");

            orderNumberElement.text(orderNumber);
        }



        $(document).ready(function() {
            updateOrderNumber();

            // $("#thetable").on("click", ".delete-td", function() {
            //     var row = $(this).closest("tr");
            //     row.remove();
            // });
        });
        $(document).ready(function() {

            $(".discard-more").click(function(e) {
                e.preventDefault();
                $("#moreModal").modal("hide");
            });
            $(".discard-reports").click(function(e) {
                e.preventDefault();
                $("#report-more-modal").modal("hide");
            });

            $(".discard-pin-zreport").click(function(e) {
                e.preventDefault();
                $("#confirm-reprint-z-report").modal("hide");
            });

            $(".discard-pin-modal-zreport").click(function(e) {
                e.preventDefault();
                $("#pin-accept-modal-zreport").modal("hide");
            });

            $('#pin-accept-modal-zreport').on('hidden.bs.modal', function() {
                // Send cancel session for pin
            })

            $(".notes-discard").click(function(e) {
                e.preventDefault();
                $("#notesModal").modal("hide");

                let noteInputs = $('.notes');
                noteInputs.each((index, input) => {
                    if (index in kitchenNotes) input.value = kitchenNotes[index];
                });
            });

            $('.notes-save').click(function(e) {
                let noteInputs = $('.notes');
                kitchenNotes = [];
                noteInputs.each((index, input) => {
                    kitchenNotes.push(input.value);
                });
            })
        });

        function openPayMod(modal = 'none') {
            if (modal == 'none')
                $('#myModal').modal('hide');

            $('#cashModal').modal('hide');
            $('#bankModal').modal('hide');
            $('#mixModal').modal('hide');

            let newMod = $(modal);
            if (newMod.length == 0) return;
            newMod.modal('show');
        }

        function prepTable() {
            selectedTable = 'locator-' + locatorNumberFinal;
            if (!(selectedTable in ordersByTable))
                ordersByTable[selectedTable] = {};

            // currentOrderList = ordersByTable[selectedTable];



            currentMode = "dineIn";

            if (currentMode in maps)
                changeTax(maps[currentMode]);
            switchMode("dineIn");
            updateOrderTableUI(currentOrderList);
            updateTotalSpan();
            activeness.run();
        }


        function moveCol(el, index, external = false) {
            if (external != false) {
                $('#dine-step').modal('hide');
                $('#tableModal').modal('show');

                const externalEl = document.querySelector(`[id-open="${external}"]`)
                if (external == 'locator-dine') {
                    $('#loc-num').val(locatorNumberFinal);
                }

                if (externalEl) {
                    externalEl.click();
                    return;
                }
            }

            let others = el.getAttribute('col-group');
            if (others) {
                let oth = $(`[col-group="${others}"]`)
                oth.removeClass('btn-primary');
                oth.addClass('btn-secondary');
            }

            let text = el.getAttribute('text');
            if (text) {
                $('#dine-text').text(text);
            }

            if (index == 0) {
                serviceTables.clear();
                $.ajax({
                    method: 'GET',
                    url: '/admin/pos/getTables',
                    success: resp => {
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            return;
                        }

                        if (resp.status == 0) {
                            let tables = resp.data.tables;
                            for (let table of tables) {
                                serviceTables.insert({service_id: table.id, service_name: table.title, busy: table.is_booked ? 'busy' : ''});
                            }

                            if (resp.message != '') {
                                Swal.fire({
                                    text: data.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }

                            return;
                        }

                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    },
                    error: err => {
                        console.log(err);
                        Swal.fire({
                            text: `{{ __('An unexpected error occured') }}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                })

                $('#tableModal').addClass('wider');
            } else $('#tableModal').removeClass('wider');

            el.classList.remove('btn-secondary');
            el.classList.add('btn-primary');
        }

        $(document).on('click', '.edit-quantity-e', function(e) {
            let change = parseFloat(this.getAttribute('change'));
            if (isNaN(change))
                return;

            let edittingQuan = $('#editting-quantity');
            let quan = parseFloat(edittingQuan.text());
            if (isNaN(quan))
                quan = 1;

            quan += change;
            if (quan <= 0) quan = 1;
            edittingQuan.text(quan.toFixed(0));

            // These will be changed to save on update not live but for now because of the old system we need legacy here
            const currentFoodRow = currentOrderList[currentEditting];
            const index = currentFoodRow[0].indexFor;


            const rowCheck = $(currentFoodRow).closest('.row-ter');
            if (!(0 in rowCheck)) return;

            updateQuantity(rowCheck, change, rowCheck[0].price);
        })

        function mealEditModal() {
            saving = true;
            let edittingMeal = currentOrderList[currentEditting];
            const row = edittingMeal[0];
            const index = row.indexFor;

            let type = row.getAttribute('type');
            if (type != 'deal') return;

            let dealId = row.getAttribute('product');
            let currentMeal = currentMeals[dealId];

            let prod_mods = currentMeal.modifiers;
            let products = currentMeal.food_items;

            const counters = {};

            for (let key of mappingsKeys) {
                counters[key] = 0;
            }

            foodLayout.clear();
            drinkLayout.clear();
            friesLayout.clear();
            saucesLayout.clear();
            sizeLayout.clear();

            stepController.reset();

            let drinkIndex = 0;
            let friesIndex = 0;
            let saucesIndex = 0;
            let mealItemIndex = 0;
            let extrasOpen = '';

            let cached_extra = {
                drinks: false,
                fries: false,
                sauces: false
            }

            for (let product of products) {
                if (mappingsKeys.includes(product.food_category_id)) {
                    let mappingObj = mappings[product.food_category_id];

                    if (mappingObj.element == 'drink-modifier') {
                        let amount = product.pivot.quantity;
                        for (let i = 0; i < amount; i++) {
                            if (drinkIndex == 0) extrasOpen = 'active';
                            drinkIndex++;

                            // Yes it's under ++ intentionally everything inside mealConfigurations starts at 1 apart from currentEditting
                            let details = null;
                            if (currentEditting in mealConfigurations)
                                details = mealConfigurations[currentEditting].drinks[drinkIndex];

                            let innerProduct = product;

                            // let items = modifyingDealPrep[innerProduct.food_category_id];

                            let items = [];
                            let mainProductExists = false;

                            // if (innerProduct.food_category_id in currentMeal.extras_by_category) {
                            if (innerProduct.food_category_id in modifyingDealPrep) {
                                // items = currentMeal.extras_by_category[innerProduct.food_category_id];
                                items = modifyingDealPrep[innerProduct.food_category_id];
                                for (let item of items) {
                                    if (item.id == innerProduct.id) mainProductExists = true;
                                }
                            }

                            if (!mainProductExists)
                                items.unshift(innerProduct);

                            const itemsObj = {}
                            for (let item of items) {
                                itemsObj[item.id] = item;
                            }

                            if (!cached_extra.drinks) {
                                for (let item of items) {
                                    currentProducts[item.id] = item;
                                }

                                cached_extra.drinks = true;
                            }

                            let checkId = product.id;

                            let previousSelectedSize = null;

                            if (currentEditting in mealConfigurations && drinkIndex in mealConfigurations[currentEditting].drinks) {
                                const mealConfIndex = mealConfigurations[currentEditting].drinks[drinkIndex];

                                const tempId = mealConfIndex.type;
                                if (itemsObj[tempId]) {
                                    checkId = mealConfIndex.type;
                                    innerProduct = itemsObj[checkId];
                                }

                                if ('sizeSelected' in mealConfIndex &&
                                    Object.keys(mealConfIndex.sizeSelected).length > 0) {

                                    for (let sizeKey in mealConfIndex.sizeSelected) {
                                        if (mealConfIndex.sizeSelected[sizeKey] != null) {
                                            previousSelectedSize = sizeKey;
                                        }
                                    }
                                }

                            }

                            /*
                            let rowId = index + '--' + innerProduct.id + '--' + drinkIndex;
                            let modifyingRow = null;
                            if (rowId in modifiersByFood)
                                modifyingRow = modifiersByFood[rowId];

                            let ingMods = [];
                            if (modifyingRow != null && 'modifiers' in modifyingRow)
                                ingMods = modifyingRow.modifiers.filter(ing => ing.type == 'remove').map(ing => ing.id);

                             */

                            let ingMods = [];
                            if (details != null && 'modifiers' in details)
                                ingMods = details.modifiers.filter(ing => ing.type == 'remove').map(ing => ing.id);

                            drinkLayout.insert({drink_id: innerProduct.name, is_open: extrasOpen, drink_num: drinkIndex});

                            otherDrinkLayouts.attach(`.all-drink-layout[drink-num="${drinkIndex}"]`);
                            if (!(innerProduct.food_category_id in modifyingDealPrep)) return location.reload();

                            let currentElSel = null;

                            items = items.sort((a, b) => a.name > b.name);

                            for (let item of items) {
                                currentProducts[item.id] = item;

                                let activeClass = 'btn-secondary';
                                if (item.id == checkId) {
                                    activeClass = 'btn-primary';
                                    currentElSel = `[drink-id="${item.id}"]`;
                                }

                                otherDrinkLayouts.insert({drink_id: item.id, owner_id: product.id, drink_num: drinkIndex, category_id: innerProduct.food_category_id, drink_name: item.name, active_class: activeClass});
                            }

                            drinkModifiers.attach(`.drink-mod-modifier-layout[drink-num="${drinkIndex}"]`);
                            drinkModifiers.clear();
                            items = modifyingDealModsPrep[innerProduct.food_category_id];
                            for (let mod of items) {
                                let modNum = 0;
                                if (details != null && 'modifiers' in details) {
                                    let amount = details.modifiers.reduce((acc, hmod) => {
                                        if (mod.id == hmod.id) acc++;
                                        return acc;
                                    }, 0);

                                    modNum = amount;
                                }

                                let activeClass = modNum == 0 ? '' : 'active-mod';

                                let price = parseFloat(mod.price).toFixed(2);

                                let quantity_string = '';
                                if (modNum != 0)
                                    quantity_string = modNum + 'x ';

                                drinkModifiers.insert({drink_num: drinkIndex, quantity_string: quantity_string, mod_name: mod.title, mod_price: price, mod_id: mod.id, product_id: innerProduct.id, active_class: activeClass})
                            }


                            drinkSize.attach(`.drink-mod-sizes-layout[drink-num="${drinkIndex}"]`);
                            drinkSize.clear();
                            let sizes = innerProduct.size;
                            let first = true;
                            for (let sizeKey in sizes) {
                                let sizePrice = sizes[sizeKey];
                                if (sizePrice == null) continue;
                                let price = parseFloat(sizePrice).toFixed(2);
                                let viewPrice = price;
                                let plus = '+';

                                let name = sizeKey;
                                let active_class = '';

                                if (previousSelectedSize == null && first) {
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    active_class = 'active-mod';
                                }

                                if (first) {
                                    plus = '';
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    first = false;
                                }

                                if (previousSelectedSize == sizeKey) {
                                    // viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    active_class = 'active-mod';
                                }

                                if (sizeKey in window.sizeKeys) name = window.sizeKeys[sizeKey];

                                drinkSize.insert({drink_num: drinkIndex, product_id: innerProduct.id, mod_name: name, mod_price: price, mod_id: sizeKey, active_class: active_class, mod_price_view: viewPrice, plus})
                            }
                            extrasOpen = '';
                        }

                    }

                    if (mappingObj.element == 'fries-modifier') {
                        let amount = product.pivot.quantity;
                        for (let i = 0; i < amount; i++) {
                            if (friesIndex == 0) extrasOpen = 'active';
                            friesIndex++;

                            // Yes it's under ++ intentionally everything inside mealConfigurations starts at 1 apart from currentEditting
                            let details = null;
                            if (currentEditting in mealConfigurations)
                                details = mealConfigurations[currentEditting].fries[friesIndex];

                            let innerProduct = product;
                            // let items = modifyingDealPrep[innerProduct.food_category_id];

                            let items = [];
                            let mainProductExists = false;

                            // if (innerProduct.food_category_id in currentMeal.extras_by_category) {
                            if (innerProduct.food_category_id in modifyingDealPrep) {
                                // items = currentMeal.extras_by_category[innerProduct.food_category_id];
                                items = modifyingDealPrep[innerProduct.food_category_id];
                                for (let item of items) {
                                    if (item.id == innerProduct.id) mainProductExists = true;
                                }
                            }

                            if (!mainProductExists)
                                items.unshift(innerProduct);

                            const itemsObj = {}
                            for (let item of items) {
                                itemsObj[item.id] = item;
                            }

                            if (!cached_extra.fries) {
                                for (let item of items) {
                                    currentProducts[item.id] = item;
                                }

                                cached_extra.fries = true;
                            }

                            let checkId = product.id;

                            let previousSelectedSize = null;

                            if (currentEditting in mealConfigurations && friesIndex in mealConfigurations[currentEditting].fries) {
                                const mealConfIndex = mealConfigurations[currentEditting].fries[friesIndex];

                                const tempId = mealConfIndex.type;
                                if (itemsObj[tempId]) {
                                    checkId = mealConfIndex.type;
                                    innerProduct = itemsObj[checkId];
                                }

                                if ('sizeSelected' in mealConfIndex &&
                                    Object.keys(mealConfIndex.sizeSelected).length > 0) {

                                    for (let sizeKey in mealConfIndex.sizeSelected) {
                                        if (mealConfIndex.sizeSelected[sizeKey] != null) {
                                            previousSelectedSize = sizeKey;
                                        }
                                    }
                                }
                            }

                            friesLayout.insert({fries_id: innerProduct.name, is_open: extrasOpen, fries_num: friesIndex});

                            otherFriesLayouts.attach(`.all-fries-layout[fries-num="${friesIndex}"]`);
                            if (!(innerProduct.food_category_id in modifyingDealPrep)) return location.reload();

                            let currentElSel = null;


                            items = items.sort((a, b) => a.name > b.name);
                            for (let item of items) {
                                let activeClass = 'btn-secondary';
                                if (item.id == checkId) {
                                    activeClass = 'btn-primary';
                                    currentElSel = `[fries-id="${item.id}"]`;
                                }

                                otherFriesLayouts.insert({fries_id: item.id, category_id: innerProduct.food_category_id, owner_id: product.id, fries_num: friesIndex, fries_name: item.name, active_class: activeClass});
                            }

                            friesModifiers.attach(`.fries-mod-modifier-layout[fries-num="${friesIndex}"]`);
                            friesModifiers.clear();
                            items = modifyingDealModsPrep[innerProduct.food_category_id];
                            for (let mod of items) {
                                let modNum = 0;
                                if (details != null && 'modifiers' in details) {
                                    let amount = details.modifiers.reduce((acc, hmod) => {
                                        if (mod.id == hmod.id) acc++;
                                        return acc;
                                    }, 0);

                                    modNum = amount;
                                }

                                let activeClass = modNum == 0 ? '' : 'active-mod';

                                let price = parseFloat(mod.price).toFixed(2);

                                let quantity_string = '';
                                if (modNum != 0)
                                    quantity_string = modNum + 'x ';

                                friesModifiers.insert({fries_num: friesIndex, mod_name: mod.title, quantity_string: quantity_string, mod_price: price, mod_id: mod.id, product_id: innerProduct.id, active_class: activeClass})
                            }


                            friesSize.attach(`.fries-mod-sizes-layout[fries-num="${friesIndex}"]`);
                            friesSize.clear();
                            let sizes = innerProduct.size;
                            let first = true;
                            for (let sizeKey in sizes) {
                                let sizePrice = sizes[sizeKey];
                                if (sizePrice == null) continue;
                                let price = parseFloat(sizePrice).toFixed(2);
                                let viewPrice = price;
                                let plus = '+';

                                let name = '';
                                let active_class = '';
                                if (previousSelectedSize == null && first) {
                                    active_class = 'active-mod';
                                    first = false;
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    plus = '';
                                }

                                if (first) {
                                    plus = '';
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    first = false;
                                }

                                if (previousSelectedSize == sizeKey) {
                                    active_class = 'active-mod';
                                    // viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                }

                                if (sizeKey == 'small') name = `{{ __('Small') }}`;
                                if (sizeKey == 'medium') name = `{{ __('Medium') }}`;
                                if (sizeKey == 'large') name = `{{ __('Large') }}`;

                                friesSize.insert({fries_num: friesIndex, mod_name: name, mod_price: price, mod_id: sizeKey, active_class: active_class, product_id: innerProduct.id, mod_price_view: viewPrice, plus})
                            }
                            extrasOpen = '';
                        }

                    }

                    if (mappingObj.element == 'sauce-modifier') {
                        let amount = product.pivot.quantity;
                        for (let i = 0; i < amount; i++) {
                            if (saucesIndex == 0) extrasOpen = 'active';
                            saucesIndex++;

                            let details = null;
                            if (currentEditting in mealConfigurations)
                                details = mealConfigurations[currentEditting].sauces[saucesIndex];

                            let innerProduct = product;
                            // let items = modifyingDealPrep[innerProduct.food_category_id];

                            let items = [];
                            let mainProductExists = false;

                            // if (innerProduct.food_category_id in currentMeal.extras_by_category) {
                            if (innerProduct.food_category_id in modifyingDealPrep) {
                                // items = currentMeal.extras_by_category[innerProduct.food_category_id];
                                items = modifyingDealPrep[innerProduct.food_category_id];
                                for (let item of items) {
                                    if (item.id == innerProduct.id) mainProductExists = true;
                                }
                            }

                            if (!mainProductExists)
                                items.unshift(innerProduct);

                            const itemsObj = {}
                            for (let item of items) {
                                itemsObj[item.id] = item;
                            }

                            if (!cached_extra.sauces) {
                                for (let item of items) {
                                    currentProducts[item.id] = item;
                                }

                                cached_extra.sauces = true;
                            }

                            let checkId = product.id;

                            let previousSelectedSize = null;

                            if (currentEditting in mealConfigurations && saucesIndex in mealConfigurations[currentEditting].sauces) {
                                const mealConfIndex = mealConfigurations[currentEditting].sauces[saucesIndex];

                                const tempId = mealConfIndex.type;
                                if (itemsObj[tempId]) {
                                    checkId = mealConfigurations[currentEditting].sauces[saucesIndex].type;
                                    innerProduct = itemsObj[checkId];
                                }

                                if ('sizeSelected' in mealConfIndex &&
                                    Object.keys(mealConfIndex.sizeSelected).length > 0) {

                                    for (let sizeKey in mealConfIndex.sizeSelected) {
                                        if (mealConfIndex.sizeSelected[sizeKey] != null) {
                                            previousSelectedSize = sizeKey;
                                        }
                                    }
                                }
                            }

                            saucesLayout.insert({sauces_id: innerProduct.name, is_open: extrasOpen, sauces_num: saucesIndex});

                            othersaucesLayouts.attach(`.all-sauces-layout[sauces-num="${saucesIndex}"]`);
                            if (!(innerProduct.food_category_id in modifyingDealPrep)) return location.reload();

                            let currentElSel = null;


                            items = items.sort((a, b) => a.name > b.name);
                            for (let item of items) {
                                let activeClass = 'btn-secondary';
                                if (item.id == checkId) {
                                    activeClass = 'btn-primary';
                                    currentElSel = `[sauces-id="${item.id}"]`;
                                }

                                othersaucesLayouts.insert({sauces_id: item.id, category_id: innerProduct.food_category_id, owner_id: product.id, sauces_num: saucesIndex, sauces_name: item.name, active_class: activeClass});
                            }

                            saucesModifiers.attach(`.sauces-mod-modifier-layout[sauces-num="${saucesIndex}"]`);
                            saucesModifiers.clear();
                            items = modifyingDealModsPrep[innerProduct.food_category_id];
                            for (let mod of items) {
                                let modNum = 0;
                                if (details != null && 'modifiers' in details) {
                                    let amount = details.modifiers.reduce((acc, hmod) => {
                                        if (mod.id == hmod.id) acc++;
                                        return acc;
                                    }, 0);

                                    modNum = amount;
                                }

                                let activeClass = modNum == 0 ? '' : 'active-mod';

                                let price = parseFloat(mod.price).toFixed(2);

                                let quantity_string = '';
                                if (modNum != 0)
                                    quantity_string = modNum + 'x ';

                                saucesModifiers.insert({sauces_num: saucesIndex, mod_name: mod.title, quantity_string: quantity_string, product_id: innerProduct.id, mod_price: price, mod_id: mod.id, active_class: activeClass})
                            }


                            saucesSize.attach(`.sauces-mod-sizes-layout[sauces-num="${saucesIndex}"]`);
                            saucesSize.clear();
                            let sizes = innerProduct.size;
                            let first = true;
                            for (let sizeKey in sizes) {
                                let sizePrice = sizes[sizeKey];
                                if (sizePrice == null) continue;
                                let price = parseFloat(sizePrice).toFixed(2);
                                let viewPrice = price;
                                let plus = '+';

                                let name = '';
                                let active_class = '';
                                if (previousSelectedSize == null && first) {
                                    active_class = 'active-mod';
                                    first = false;
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    plus = '';
                                }

                                if (first) {
                                    plus = '';
                                    viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                    first = false;
                                }

                                if (previousSelectedSize == sizeKey) {
                                    active_class = 'active-mod';
                                    // viewPrice = parseFloat(innerProduct.price).toFixed(2);
                                }

                                if (sizeKey == 'small') name = `{{ __('Small') }}`;
                                if (sizeKey == 'medium') name = `{{ __('Medium') }}`;
                                if (sizeKey == 'large') name = `{{ __('Large') }}`;

                                saucesSize.insert({sauces_num: saucesIndex, mod_name: name, mod_price: price, mod_id: sizeKey, active_class: active_class, product_id: innerProduct.id, mod_price_view: viewPrice, plus})
                            }
                            extrasOpen = '';
                        }

                    }

                    // Temporary solution for the server
                    let checking = mappingObj.id;
                    if (mappingObj.key == 'drinkHotId') checking = 'db093595-02e5-4dbd-a908-4e4e8a3ce9a1';
                    if (checking in counters) counters[checking]++;
                    continue;
                }



                let mealOpen = '';
                if (mealItemIndex == 0) mealOpen = 'active';
                let amount = product.pivot.quantity;
                for (let i = 0; i < amount; i++) {
                    mealItemIndex++;
                    if (mealItemIndex != 1) mealOpen = '';

                    let details = null;
                    if (currentEditting in mealConfigurations && mealItemIndex in mealConfigurations[currentEditting].main_meal) {
                        details = mealConfigurations[currentEditting].main_meal[mealItemIndex];
                    }

                    let rowId = index + '--' + product.id + '--' + (i + 1);
                    let modifyingRow = null;
                    if (rowId in modifiersByFood)
                        modifyingRow = modifiersByFood[rowId];

                    let ingMods = [];
                    /*
                    if (modifyingRow != null && 'modifiers' in modifyingRow)
                        ingMods = modifyingRow.modifiers.filter(ing => ing.type == 'remove').map(ing => ing.id);
                     */

                    if (details != null && 'modifiers' in details) {
                        ingMods = details.modifiers.filter(ing => ing.type == 'remove').map(ing => ing.id);
                    }

                    foodLayout.insert({product_name: product.name, is_open: mealOpen, product_id: product.id, prod_num: mealItemIndex});

                    let ings = product.ingredients;
                    const ingAttached = ingLayout.attach(`.food-item-layout[product="${product.id}"][prod_num="${mealItemIndex}"] .ingredients-layout`);
                    let firstIng = true;
                    for (let ing of ings) {
                        // To update it back to just first being clicked by default ingredients, comment is_first and uncomment the line below
                        let is_first = true;
                        // let is_first = firstIng;
                        if (firstIng) {
                            firstIng = false;
                        }

                        let activeClass = ingMods.includes(ing.id) ? 'btn-secondary' : 'btn-primary';
                        /*
                        let activeClass = 'btn-primary';

                        if (ingMods.length == 0) {
                            if (is_first) {
                                activeClass = 'btn-primary';
                                modifiersByFood[rowId] = {modifiers: [], row: edittingMeal}
                            } else {
                                activeClass = 'btn-secondary';

                                modifiersByFood[rowId].modifiers.push({
                                    id: ing.id,
                                    modifierName: ing.name,
                                    modifierPrice: 0,
                                    type: 'remove',
                                })
                            }
                        } else {
                            if (ingMods.includes(ing.id))
                                activeClass = 'btn-secondary';
                        }
                         */

                        ingAttached.insert({ingredient_name: ing.name, ingredient_id: ing.id, product_id: product.id, active_class: activeClass, prod_num: mealItemIndex})
                    }

                    const modAttached = modLayout.attach(`.food-item-layout[product="${product.id}"][prod_num="${mealItemIndex}"] .modifier-layout`);
                    for (let prod_mod of prod_mods) {
                        const categoryKeys = prod_mod.category.map(cat => cat.id);
                        if (!categoryKeys.includes(product.food_category_id)) continue;

                        let price = parseFloat(prod_mod.price).toFixed(2);
                        let priceString = window.currency_symbol + price;
                        if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                        let modNum = 0;
                        if (details != null && 'modifiers' in details) {
                            let amount = details.modifiers.reduce((acc, mod) => {
                                if (mod.id == prod_mod.id) acc++;
                                return acc;
                            }, 0);

                            modNum = amount;
                        }

                        /*
                        if (modifyingRow != null && 'modifiers' in modifyingRow) {

                            let amount = modifyingRow.modifiers.reduce((acc, mod) => {
                                if (mod.id == prod_mod.id) acc++;
                                return acc;
                            }, 0);

                            modNum = amount;
                        }
                         */

                        let quantity_string = '';
                        if (modNum != 0)
                            quantity_string = modNum + 'x ';

                        modAttached.insert({mod_id: prod_mod.id, quantity_string: quantity_string, active_cl: modNum == 0 ? '' : 'active-mod', mod_name: prod_mod.title, mod_price: price, product_id: product.id, prod_num: mealItemIndex});
                    }

                    const sizeAttached = sizeLayout.attach(`.food-item-layout[product="${product.id}"][prod_num="${mealItemIndex}"] .sizes-layout`);

                    let mealSizeBox = $(`.food-item-layout[product="${product.id}"][prod_num="${mealItemIndex}"] .deal-meal-size-box`);
                    mealSizeBox.hide();
                    let sizes = product.size;
                    let first = true;

                    let currentSize = '';
                    if (details != null && 'sizeSelected' in details) {
                        for (let s in details.sizeSelected) {
                            if (details.sizeSelected[s] != null) {
                                currentSize = s;
                                break;
                            }
                        }
                    }

                    /*
                    if (modifyingRow != null && 'sizeSelected' in modifyingRow) {
                        for (let s in modifyingRow.sizeSelected) {
                            if (modifyingRow.sizeSelected[s] != null) {
                                currentSize = s;
                                break;
                            }
                        }
                    }
                     */

                    let found = false;
                    for (let s in sizes) {
                        if (currentSize == s) {
                            found = true;
                        }
                    }
                    for (let s in sizes) {
                        let price = parseFloat(sizes[s]).toFixed(2);
                        let viewPrice = price;

                        let plus = '+';

                        if (sizes[s] == null) continue;
                        mealSizeBox.show();
                        let active_class = '';

                        if (found && currentSize == s) {
                            active_class = 'active-mod';
                        }

                        if (first) {
                            viewPrice = parseFloat(product.price).toFixed(2);
                            plus = '';
                        }

                        if (!found && first) {
                            active_class = 'active-mod';
                        }
                        first = false

                        let priceString = window.currency_symbol + price;
                        if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                        sizeAttached.insert({mod_id: s, mod_name: sizeKeys[s], mod_price: price, product_id: product.id, active_class, prod_num: mealItemIndex, mod_price_view: viewPrice, plus});
                    }
                }
            }

            let mainMealElement = document.querySelector('.opened-by-default.tab-meal-switcher');
            mainMealElement.stepping = 0;
            stepController.stepElements.push(mainMealElement);
            for (let key in counters) {
                let counter = counters[key];
                const element = $(`[tab][key="${key}"]`);
                if (counter == 0) element.css('display', 'none');
                else {
                    element[0].setAttribute('stepping', stepController.stepElements.length);
                    stepController.stepElements.push(element);
                    element.css('display', 'block');
                }
            }
        }

        $(document).on('click', '.meal-changes', mealEditModal);

        $(document).on('click', '.confirm-payment', function(e) {
            // let method = this.getAttribute('type');
            const order = saveOrder();
            order.payment_data = null;
            order.payment_status = 0;

            if (window.method.id == `{{ config('constants.paymentMethod.paymentMethodCardId') }}` || window.method.id == `{{ config('constants.paymentMethod.paymentMethodMixId') }}`) {
                payWithCard(order, window.method);
                return;
            }

            sendOrder(order, window.method).catch(err => {
                cardProceed.opened = false;
                cardProceed.returnState();
                $('#myModal').modal('show');
            });
        })

        $(document).on('click', '.save-button:not(.inactive)', function(e) {
            const order = saveOrder(window.method);
            $.ajax({
                method: 'POST',
                url: '/admin/order',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: order,
                success: resp => {
                    $('#myModal').modal('hide');
                    $('#cashModal').modal('hide');
                    $('#bankModal').modal('hide');
                    $('#mixModal').modal('hide');

                    orderChange = null;
                    wipe_all();
                    Swal.fire({
                        text: resp.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                },
                error: err => {
                    console.log(err);
                    Swal.fire({
                        text: `{{ __('An unexpected error occured') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            })
        })


        function calculateRowPrice(rowGet, rowId = false) {
            let row = rowGet;
            if (rowId)
                row = currentOrderList[rowGet];

            if (row.length == 0) return;

            const currentRow = row[0];
            const index = currentRow.indexFor;
            const type = currentRow.getAttribute('type');
            const id = currentRow.getAttribute('product');
        }

        function changeTax(tax_type) {
            let tax = false;
            let found = false;
            @foreach($tax_key_filtered as $index => $tax)
            if (tax_type == `{{ $index }}`) {
                tax = parseFloat(`{{ $tax->tax_rate }}`);
                found = true;
            }
            @endforeach

            if (!found) {
                Swal.fire({
                    text: `{{ __('The location has no tax created for the selected form of delivery') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                currentTax = null;

                return;
            }
            currentTax = tax;
            if (tax) {
                $('.tax-area').text(`${tax.toFixed(2)}%`);
            } else {
                $('.tax-area').text('0.00%');
                $('.tax-area-amount').html('&nbsp;');
            }

            // Here tax extra
            calculateTotalPrice();

            temporaryPlacement(tax_type);
        }

        function temporaryPlacement(type) {

            if (type == 'dine_in' && !selectedTable.startsWith('locator-')) {
                $('.save-button.inactive').removeClass('inactive');
            } else {
                $('.save-button').addClass('inactive');
            }
        }

        $(document).on('click', '.mix-focus', function(e) {
            e.preventDefault();
            this.blur();

            $('.mix-focus').removeClass("mix-focused");
            this.classList.add('mix-focused');
            cashCalculationsMix.change('mixType', this.getAttribute('focus-type'));
            cashCalculationsMix.change('steppedValue', '');
            return false;
        });

        function saveOrder() {
            if (currentTax == null) {
                Swal.fire({
                    text: `{{ __('The location has no tax created for the selected form of delivery') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }
            if (Object.keys(currentOrderList).length == 0) return;

            let mode = currentMode;
            if (currentMode in maps)
                mode = maps[currentMode];

            let tableId = null;
            let locator = null;
            if (selectedTable) {
                if (selectedTable.startsWith('locator-')) {
                    let locNum = selectedTable.split('-')[1];
                    locator = locNum;
                } else {
                    tableId = selectedTable;
                }
            }


            let is_percentage_disc = true;
            let discount_amount = 0;
            if ('discountAmount' in window && 'discountT' in window) {
                is_percentage_disc = window.discountT == 'perc';
                discount_amount = window.discountAmount;
            }


            let orderProducts = [];
            let cart_total = 0;
            Object.keys(currentOrderList).forEach(i => {
                let item = currentOrderList[i];
                if (item[0].final != true) return;
                let finalObj = {};
                let subTotal = 0;

                let row = item[0];
                let index = row.indexFor;
                let type = row.getAttribute('type');

                let quantityElement = item.find(".quantity-value");
                let currentQuantity = parseInt(quantityElement.text());

                finalObj.type = type;
                if (type == 'product') {
                    let prodId = row.getAttribute('product');
                    let prod = currentProducts[prodId];

                    finalObj.id = prod.id;
                    // finalObj.price_per = prod.price;
                    finalObj.price_per = parseFloat(row.price);
                    finalObj.quantity = currentQuantity;
                    finalObj.name = prod.name;
                    subTotal += (finalObj.price_per * currentQuantity);

                    let finalIngMods = [];
                    if (prod.ingredients)
                        finalIngMods = prod.ingredients.map(mod => mod.id);

                    finalObj.size = {};
                    for (let size in prod.size) {
                        finalObj.size[size] = null;
                    }

                    finalObj.ingredients = [];
                    finalObj.removed_ingredients_names = [];

                    if (index in modifiersByFood) {
                        if ('sizeSelected' in modifiersByFood[index]) {
                            const sizes = modifiersByFood[index].sizeSelected;
                            for (let size in sizes) {
                                finalObj.size[size] = sizes[size];
                                if (sizes[size] != null) {
                                    // finalObj.name += ', ' + window.sizeKeys[size];
                                    finalObj.price_per += sizes[size];
                                    subTotal += sizes[size] * currentQuantity;
                                }
                            }
                        }

                        if ('modifiers' in modifiersByFood[index]) {
                            let mods = modifiersByFood[index].modifiers;
                            let removedMods = [];
                            let ingMods = mods.filter(mod => mod.type == 'remove').map(mod => {
                                removedMods.push(mod.modifierName);
                                return mod.id;
                            });

                            finalObj.removed_ingredients_names = removedMods;

                            finalIngMods = finalIngMods.filter(mod => !ingMods.includes(mod));

                            let modMods = mods.filter(mod => mod.type == 'add').map(mod => { return {id: mod.id, price_per: mod.modifierPrice, name: mod.modifierName, quantity: 1}});

                            let fixedmodMods = {};
                            for (let mod of modMods) {
                                subTotal += mod.price_per * currentQuantity;
                                if (mod.id in fixedmodMods) fixedmodMods[mod.id].quantity++;
                                else fixedmodMods[mod.id] = mod;
                            }

                            let finalmods = [];
                            for (let mod in fixedmodMods) {
                                finalmods.push(fixedmodMods[mod]);
                            }

                            finalObj.modifiers = finalmods;
                        }
                    }

                    finalObj.ingredients = finalIngMods;
                }

                if (type == 'meal' || type == 'deal') {
                    const mealId = row.getAttribute('product');
                    const meal = currentMeals[mealId];
                    finalObj.id = meal.id;
                    finalObj.price_per = meal.price;
                    finalObj.quantity = currentQuantity;
                    finalObj.name = meal.name;
                    subTotal = meal.price * finalObj.quantity;

                    finalObj.products = [];

                    let prepTable = {};

                    for (let modFix in modifyingDealPrep) {
                        let prodFix = {};
                        let items = modifyingDealPrep[modFix];
                        for (let item of items) {
                            prodFix[item.id] = item;
                        }

                        prepTable[modFix] = prodFix;
                    }

                    let globIs = {};
                    for (let product_meal of meal.food_items) {
                        let product = product_meal;

                        let quan = product.pivot.quantity;

                        // This would have been used for adding products together but we've decided to keep them separate in meals
                        /*
                    let weights = {
                        size: 0,
                        modifiers: 0,
                        ingredients: 0
                    }
                    */

                        let currentMap = null;
                        if (product.food_category_id in mappings)
                            currentMap = mappings[product.food_category_id];

                        for (let i = 0; i < quan; i++) {
                            let innerProduct = product;
                            let inIndex = i + 1;
                            let details = null;
                            let foundSizes = null;
                            // This is done incase there are no configurations in the system
                            if (currentMap == null) currentMap = {config_key: 'main_meal'};
                            if (currentMap != null) {

                                if (!(currentMap.config_key in globIs)) globIs[currentMap.config_key] = -1;
                                globIs[currentMap.config_key]++;

                                inIndex = globIs[currentMap.config_key] + 1;

                                if (index in mealConfigurations) {
                                    let currentMealConfig = mealConfigurations[index];
                                    if (currentMap.config_key in currentMealConfig) {

                                        let configuration = currentMealConfig[currentMap.config_key];
                                        if (inIndex in configuration) {
                                            details = configuration[inIndex];

                                            /*
                                            if ('extraPrice' in details && details.extraPrice != null) {
                                                subTotal += details.extraPrice;
                                            }
                                             */

                                            if (details.type) {
                                                let newProductId = details.type;
                                                // It's already in predefined config key so the category key exists
                                                if (newProductId in prepTable[innerProduct.food_category_id])
                                                    innerProduct = prepTable[innerProduct.food_category_id][newProductId];
                                            }

                                            if ('sizeSelected' in details && Object.keys(details.sizeSelected).length > 0) {
                                                foundSizes = details.sizeSelected;
                                            }
                                        }
                                    }
                                }
                            }

                            let sizesWithDefault = {};
                            let foundDefault = false;

                            for (let size in innerProduct.size) {
                                if (!foundDefault && innerProduct.size[size] != null && !isNaN(innerProduct.size[size])) {
                                    sizesWithDefault[size] = innerProduct.size[size];
                                    foundDefault = true;
                                } else sizesWithDefault[size] = null;
                            }

                            let prod = {
                                id: innerProduct.id,
                                price_per: innerProduct.price,
                                name: innerProduct.name,
                                quantity: 1,
                                ingredients: innerProduct.ingredients.map(ing => ing.id),
                                modifiers: [],
                                size: sizesWithDefault,
                                sub_total: innerProduct.price,
                                removed_ingredients_names: []
                            }

                            let prodSubTotal = parseFloat(prod.sub_total);

                            if (foundSizes != null) {
                                for (let size in foundSizes) {
                                    prod.size[size] = foundSizes[size];
                                    if (foundSizes[size] != null) {
                                        // prod.name += ', ' + window.sizeKeys[size];
                                        prod.price_per += foundSizes[size];
                                        prodSubTotal += parseFloat(foundSizes[size]);
                                        subTotal += parseFloat(foundSizes[size]) * currentQuantity;
                                    }
                                }
                            } else {
                                if (foundDefault) {
                                    for (let size in sizesWithDefault) {
                                        prod.size[size] = sizesWithDefault[size];
                                        if (sizesWithDefault[size] != null) {
                                            // prod.name += ', ' + window.sizeKeys[size];
                                            prod.price_per += sizesWithDefault[size];
                                            prodSubTotal += parseFloat(sizesWithDefault[size]);
                                            subTotal += parseFloat(sizesWithDefault[size]) * currentQuantity;
                                        }

                                    }
                                }
                            }

                            // let mealProductIndex = index + "--" + product.id + "--" + (i + 1);
                            // if (mealProductIndex in modifiersByFood) {
                            // const sizes = modifiersByFood[mealProductIndex].sizeSelected;

                            prod.modifiers =  [];
                            if (details != null && 'modifiers' in details) {
                                let mods = details.modifiers;
                                let ingModsAll = mods.filter(mod => mod.type == 'remove');

                                let ingRemovedNames = [];
                                let ingMods = ingModsAll.map(mod => {
                                    ingRemovedNames.push(mod.modifierName);
                                    return mod.id
                                });

                                prod.removed_ingredients_names = ingRemovedNames;

                                finalIngMods = prod.ingredients.filter(mod => !ingMods.includes(mod));
                                prod.ingredients = finalIngMods;

                                let modMods = mods.filter(mod => mod.type == 'add').map(mod => { return {id: mod.id, price_per: mod.modifierPrice, name: mod.modifierName, quantity: 1}});

                                let fixedmodMods = {};
                                for (let mod of modMods) {
                                    prodSubTotal += parseFloat(mod.price_per);
                                    subTotal += parseFloat(mod.price_per) * currentQuantity;
                                    if (mod.id in fixedmodMods) fixedmodMods[mod.id].quantity++;
                                    else fixedmodMods[mod.id] = mod;
                                }

                                let finalmods = [];
                                for (let mod in fixedmodMods) {
                                    finalmods.push(fixedmodMods[mod]);
                                }

                                prod.modifiers = finalmods;
                            }
                            // }

                            prod.sub_total = prodSubTotal;
                            // subTotal += prod.sub_total;

                            finalObj.products.push(prod);
                        }

                    }
                }


                finalObj.sub_total = subTotal;
                cart_total += subTotal;
                orderProducts.push(finalObj);
            });


            const order = {
                order_type: mode,
                table_id: tableId,
                locator: locator,
                cart_total_item: Object.keys(currentOrderList).length,
                cart_total_price: cart_total,
                tax: currentTax,
                payment_method_id: 0,
                is_discount_in_percentage: is_percentage_disc,
                discount_amount: discount_amount,
                items: orderProducts,
                paid_cash: 0,
                paid_bank: 0,
                payment_return: 0,
                save_order: true
            }

            if (orderChange != null)
                order.order_id = orderChange;

            if (kitchenNotes.length != 0) {
                order.kitchen_notes = kitchenNotes;
            }

            if (e_kiosk_id != null) {
                order.e_kiosk_id = e_kiosk_id;
                order.order_number = e_kiosk_order_number;
            }
            return order;
        }

        function wipe_all() {
            window.clearDiscount = true;
            let el = document.querySelector('[value="back"][targetting="disc-in"]');
            if (el) el.click();

            clearAllOrders();
            $('.all-products').empty();
            currentOrderList = [];
            $('.kategorite.current').removeClass('current');
            updateTotalSpan();
            switchMode('takeAway');
            /*

            @if ($location->take_away)
            $(".take-away").click();
@elseif ($location->dine_in)
            $(".dine-in").click();
@elseif ($location->delivery)
            $(".delivery").click();
@else
            currentMode = 'none';
            takeAwayOrderList = null;
@endif
            */

            $('.take-away').removeClass('current');
            $('.dine-in').removeClass('current');
            $('.delivery').removeClass('current');
            currentMode = 'none';

            window.discountT = 'perc';
            window.discountAmount = 0;
            $('.discount-area-amount').text('No Discounts');
            // Temporary or full solution we don't know yet but this is fine this is fine i swear (I'll add more later if needed but yeh shhhh)

            selectedTable = null;
            locatorNumber = '';
            locatorNumberFinal = '';
        }

        function openDrawer() {
            if ('Mine' in window) {
                window.Mine.postMessage('drawer:close');
            }
        }

        function sendOrder(orderDetails, payment_method, extraOptions = {}) {
            return new Promise((resolve, reject) => {
                if (!('payment_skip' in extraOptions) || extraOptions.payment_skip == false) {
                    let paidCash = 0;
                    let paidBank = 0;
                    let paymentReturn = 0;

                    if (payment_method.type == 'Card') {
                        paidBank = superTotal;
                    }

                    if (payment_method.type == 'Mix') {
                        paidCash = cashCalculationsMix.cash;
                        paidBank = cashCalculationsMix.bank;
                    }

                    if (payment_method.type == 'Cash') {
                        paidCash = cashCalculations.cash;
                        // Payment return is used as a negative value for other calculations, in case of sending in the back we need a positive value
                        paymentReturn = cashCalculations.return * -1;
                    }

                    orderDetails.paid_cash = paidCash,
                        orderDetails.paid_bank = paidBank,
                        orderDetails.payment_return = paymentReturn;

                    orderDetails.payment_method_id = payment_method;
                    delete orderDetails.save_order;
                }


                $('#myModal').modal('hide');
                cardProceed.proceed();

                $.ajax({
                    method: 'POST',
                    url: '/admin/order',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: orderDetails,
                    success: resp => {
                        paymentLoader.close();
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            reject(orderDetails);
                            return;
                        }

                        if (resp.status == 1) {
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            reject(orderDetails);
                            return;
                        }

                        if (resp.status == 0) {
                            e_kiosk_id = null;
                            orderChange = null;

                            openDrawer();

                            if ('print_order' in resp && resp.print_order != null && resp.print_order != '') {
                                localStorage.setItem('last_printed_string', resp.print_order);

                                if (window.printer_testing || window.connected_printer)
                                    window.invoicePrinting(resp.print_order);
                            }

                            // alert('This is a test, the /admin/order has been hit and sent data in console');
                            /*
                            $('#cashModal').modal('hide');
                            $('#bankModal').modal('hide');
                            $('#mixModal').modal('hide');
                             */

                            // wipe_all();
                            Swal.fire({
                                text: resp.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });

                            resolve(orderDetails);
                            return;
                        }

                        if ('message' in resp) {
                            Swal.fire({
                                text: resp.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                            resolve(orderDetails);
                        }
                    },
                    error: err => {
                        console.error(err);
                        paymentLoader.close();

                        /*
                        if ('type' in extraOptions && extraOptions.type == 'payment') {
                            // The order is already paid and we must save it.
                            orderDetails.later_order = true;
                            let failedPaymentObjects = [orderDetails];

                            const failedPayments = localStorage.getItem('failed_payments');
                            if (failedPayments) {
                                try {
                                    const payments = JSON.parse(failedPayments);
                                    if (Array.isArray(payments)) {
                                        failedPaymentObjects = payments;
                                        failedPaymentObjects.push(orderDetails);
                                    }
                                } catch(err) {
                                    // meh
                                }
                            }

                            localStorage.setItem('failed_payments', JSON.stringify(failedPaymentObjects));
                            reject(orderDetails);
                            return;
                        }
                        */

                        /*
                        Swal.fire({
                            text: JSON.stringify(err),
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                         */


                        /*
                        Swal.fire({
                            text: `{{ __('An unexpected error occured') }}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __('Ok, got it!') }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                         */

                        reject(orderDetails);
                    }
                })
            });
        }

        setInterval(() => {
            const main = document.querySelectorAll('.height-following');
            for (let a of main) {
                let item = a.querySelectorAll('.height-follower');
                for (let m of item) {
                    m.style.height = getComputedStyle(a).height;
                }
            }
        });

        setInterval(() => {
            const svgLoader = document.querySelectorAll('[svg-src]:not([ready])');
            for (let svg of svgLoader) {
                svg.setAttribute('ready', 'true');
                const source = svg.getAttribute('svg-src');
                fetch(source).then(sourceBlob => {
                    sourceBlob.text().then(svgContent => {
                        if (svgContent.includes('<svg'))
                            svg.innerHTML = svgContent;
                        else {
                            const image = document.createElement('img');
                            image.src = source;
                            image.style.height = '100%';
                            image.style.width = '100%';
                            svg.appendChild(image);
                        }

                    });
                }).catch(err => console.error('Some svgs could not load'));
            }
        });

        function fillProductsFromIds(product_ids, show = true) {
            return new Promise((resolve, reject) => {
                currentRequest = $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/admin/pos/get/product/ids",
                    type: "GET",
                    dataType: 'json',
                    data: { product_ids },
                    success: resp => {
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            reject(false);
                            return;
                        }

                        if (resp.status == 1) {
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            reject(false);
                            return;
                        }

                        if (resp.status == 0) {
                            const items = resp.food_items;

                            let data = resp.data;

                            if (!('modifiers' in data)) data.modifiers = [];
                            data?.modifiers.forEach((mod, index) => {
                                data.modifiers[index].price = parseFloat(mod.price);
                            })

                            data?.food_items.forEach(function(product) {
                                let catId = product.food_category_id;
                                let s = $(`.category-${catId}`);
                                let color = '';
                                if (s.length > 0) {
                                    let col = s[0].getAttribute('color');
                                    if (col != '' && col != 'null' && col != null) {
                                        color = `--bg-color-ex: ${col}`;
                                    }
                                }

                                product.price = parseFloat(product.price);

                                product.modifiers = data.modifiers;

                                currentProducts[product.id] = product;
                                if (show) {

                                    // This could have been more dynamic but consistency has more value since there are places such a function wouldn't reach by this setup
                                    let price = product.price.toFixed(2);
                                    let priceString = window.currency_symbol + price;
                                    if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

                                    var productHtml = `
    <div class="card card-flush card-for-cart" type="product" style="${color}" pb-5 mw-100" prod="${product.id}">

    <div class="card-body text-center p-0">

        <div class="mt-3 ms-3 me-3">
            <div class="text-start">
                <span class="fw-bold cursor-pointer  product-name font-span" >${product.name}</span>

            </div>
        </div>
        <span class="text-end mb-3 me-3 fs-1 product-price">${priceString}</span>


    </div>

    </div>
    `;
                                    $(".all-products").append(productHtml);
                                }
                            });

                            if (show) {
                                categoryActive.setCurrent(this);
                                categoryActive.run();
                            }
                            if (resp.message != '') {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }


                            resolve(currentProducts);
                        }


                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            reject(false);
                            return;
                        }
                    },
                    error: err => {
                        Swal.fire({
                            text: '{{ __("There was an issue with the server please try again after refresh") }}',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });

                        reject(false);
                    }
                });

                return currentRequest;
            })
        }

        async function loadOrder(order) {
            orderChange = order.id;
            wipe_all();
            const fullMealProductDetails = {
                preppedOrdered: {},
                prepped: {},
                prep: function(obj) {
                    this.prepped = {};
                    const products = obj.food_items;
                    for (let product of products) this.prepped[product.id] = product;

                    let orderIndex = 0;
                    for (let product of products) {
                        let loop = 1;
                        if ('pivot' in product && 'quantity' in product.pivot) loop = product.pivot.quantity;
                        for (let i = 0; i < loop; i++) {
                            this.preppedOrdered[orderIndex] = product;
                            orderIndex++;
                        }
                    }
                },
                findByOrder(index) {
                    if (index in this.preppedOrdered) return this.preppedOrdered[index];
                    return false;
                },
                find: function(id) {
                    if (id in this.prepped) return this.prepped[id];
                    return false;
                }
            }

            orderDetails = {
                id: order.id
            }

            let hasMeals = false;
            const productsToQuery = [];

            const items = order.items;
            // Setup of currentProducts and currentMeals
            for (let item of items) {


                if (item.type == 'product')
                    productsToQuery.push(item.id);
                else if (item.type == 'meal' || item.type == 'deal')
                    hasMeals = true;
            }

            if (productsToQuery.length != 0) {
                try {
                    await fillProductsFromIds(productsToQuery, false);
                } catch(err) {
                    // The error shows from within
                    console.error(err);
                    return;
                }
            }

            if (hasMeals) {
                try {
                    await getMeals(false);
                } catch(err) {
                    // The error shows from within
                    console.error(err);
                    return;
                }
            }

            for (let item of items) {
                // Add item to order

                let fullObj = {};
                if (item.type == 'product')
                    fullObj = currentProducts[item.id];
                else
                    fullObj = currentMeals[item.id];

                let edittable = 'is_ready' in item && item.is_ready == false;

                const row = addToOrder(item.id, item.type, item.name, fullObj.price, fullObj, true, edittable);
                const rowId = row[0].indexFor;
                if (item.type == 'product') {

                    // Add items modifiers and removed ingredients
                    item.price = parseFloat(item.price_per);
                    modifiersByFood[rowId] = {
                        row: row,
                        modifiers: [],
                    }

                    const sizes = item.size;
                    let hasSize = false;
                    for (let i in sizes) {
                        if (sizes[i] != null) {
                            hasSize = true;
                            sizes[i] = parseFloat(sizes[i]);
                        }
                    }

                    if (hasSize)
                        modifiersByFood[rowId].sizeSelected = sizes;

                    const ingredients = fullObj.ingredients.filter(ing => !item.ingredients.includes(ing.id));
                    for (let ing of ingredients) {
                        modifiersByFood[rowId].modifiers.push({
                            id: ing.id,
                            modifierName: ing.name,
                            modifierPrice: 0,
                            type: 'remove'
                        });
                    }

                    const mods = item.modifiers;
                    for (let mod of mods) {
                        let quan = parseFloat(mod.quantity);
                        for (let i = 0; i < quan; i++) {
                            modifiersByFood[rowId].modifiers.push({
                                id: mod.id,
                                modifierName: mod.name,
                                modifierPrice: parseFloat(mod.price_per),
                                type: 'add'
                            });
                        }
                    }
                }


                if (item.type == 'meal' || item.type == 'deal') {
                    fullMealProductDetails.prep(fullObj);

                    const products = item.products;
                    // The products are sent as quantity 1 and separated, so to know the number of the product we must count
                    const categoryCounters = {};
                    let productCounters = {};

                    let fullIndex = 0;
                    for (let product of products) {
                        const index = fullIndex;
                        fullIndex++;


                        if (product.food_category_id in categoryCounters) categoryCounters[product.food_category_id]++;
                        else categoryCounters[product.food_category_id] = 1;


                        let mappingObj = mappings[product.food_category_id];
                        if (!mappingObj) {
                            mappingObj = {config_key: 'main_meal'};
                        }


                        const prodDetails = fullMealProductDetails.findByOrder(index);
                        if (prodDetails == false) {
                            continue;
                        }

                        if (mappingObj) {
                            if (!(rowId in mealConfigurations)) {
                                mealConfigurations[rowId] = {
                                    main_meal: {},
                                    drinks: {},
                                    sauces: {},
                                    fries: {}
                                }
                            }

                            const currentIndex = categoryCounters[product.food_category_id];
                            if (!(currentIndex in mealConfigurations[rowId][mappingObj.config_key])) {
                                mealConfigurations[rowId][mappingObj.config_key][currentIndex] = {
                                    modifiers: [],
                                    sizeSelected: {}
                                }
                            }

                            const itemConfig = mealConfigurations[rowId][mappingObj.config_key][currentIndex];

                            if (mappingObj.config_key != 'main_meal') {
                                itemConfig.type = product.id;

                                if (product.id != prodDetails.id) {
                                    const extraPrice = parseFloat((parseFloat(product['price-from-db']) - parseFloat(prodDetails.price)).toFixed(5));
                                    itemConfig.extraPrice = extraPrice;
                                } else itemConfig.extraPrice = null;
                            }

                            // Ingredients removal
                            if ('ingredients' in product) {
                                const ingredients = prodDetails.ingredients.filter(ing => !product.ingredients.includes(ing.id));
                                for (let ing of ingredients) {
                                    itemConfig.modifiers.push({
                                        id: ing.id,
                                        modifierName: ing.name,
                                        modifierPrice: 0,
                                        type: 'remove'
                                    });
                                }
                            }

                            // Modifiers addition
                            if ('modifiers' in product) {
                                const mods = product.modifiers;
                                for (let mod of mods) {
                                    let quan = parseFloat(mod.quantity);
                                    for (let i = 0; i < quan; i++) {
                                        itemConfig.modifiers.push({
                                            id: mod.id,
                                            modifierName: mod.name,
                                            modifierPrice: parseFloat(mod.price_per),
                                            type: 'add'
                                        });
                                    }
                                }
                            }

                            // Size changes
                            if ('size' in product) {
                                const sizes = product.size;
                                let hasSize = false;
                                for (let i in sizes) {
                                    if (sizes[i] != null) {
                                        hasSize = true;
                                        sizes[i] = parseFloat(sizes[i]);
                                    }
                                }

                                if (hasSize)
                                    itemConfig.sizeSelected = sizes;
                            }


                        }

                        // This is necessary to both find the product and know which product is being referred to in fullObj (the actual meal)
                        if (!(product.id in productCounters)) productCounters[product.id] = 0;

                        // Yes you can ++ but this is more readable
                        productCounters[product.id] += 1;

                        /*
                        This is old way of doing meals
                        const rowIdMeal = rowId + "--" + product.id + "--" + productCounters[product.id];

                        modifiersByFood[rowIdMeal] = {
                            row: row,
                            modifiers: [],
                        }

                        // Product swaps (for drinks and such)

                        // Ingredients removal
                        if ('ingredients' in product) {
                            const ingredients = prodDetails.ingredients.filter(ing => !product.ingredients.includes(ing.id));
                            for (let ing of ingredients) {
                                modifiersByFood[rowIdMeal].modifiers.push({
                                    id: ing.id,
                                    modifierName: ing.name,
                                    modifierPrice: 0,
                                    type: 'remove'
                                });
                            }
                        }

                        // Modifiers addition
                        if ('modifiers' in product) {
                            const mods = product.modifiers;
                            for (let mod of mods) {
                                let quan = parseFloat(mod.quantity);
                                for (let i = 0; i < quan; i++) {
                                    modifiersByFood[rowIdMeal].modifiers.push({
                                        id: mod.id,
                                        modifierName: mod.name,
                                        modifierPrice: parseFloat(mod.price_per),
                                        type: 'add'
                                    });
                                }
                            }
                        }

                        // Size changes
                        if ('size' in product) {
                            const sizes = product.size;
                            let hasSize = false;
                            for (let i in sizes) {
                                if (sizes[i] != null) {
                                    hasSize = true;
                                    sizes[i] = parseFloat(sizes[i]);
                                }
                            }

                            if (hasSize)
                                modifiersByFood[rowIdMeal].sizeSelected = sizes;
                        }
                         */


                    }
                }

                updateQuantity(row, parseFloat(item.quantity)-1, row[0].price);

                updateRow(row);
                updateModifiersUI(rowId, item.type);
            }

            currentMode = order.order_type == 'dine_in' ? 'dineIn' : 'takeAway';

            if (currentMode == 'dineIn') {
                if ('locator' in order && order.locator != null) {
                    selectedTable = 'locator-' + order.locator;
                    locatorNumber = order.locator;
                    locatorNumberFinal = order.locator;
                } else if ('table_id' in order && order.table_id != null) {
                    selectedTable = order.table_id;
                }
            }

            if (currentMode in maps)
                changeTax(maps[currentMode]);

            if (currentMode == "dineIn") {
                if (!(selectedTable in ordersByTable)) {
                    ordersByTable[selectedTable] = {};
                    // currentOrderList = ordersByTable[selectedTable];
                }

                activeness.setCurrent($('.dine-in')[0]);
            } else if (currentMode == 'takeAway') {
                activeness.setCurrent($('.take-away')[0]);
            }

            switchMode(currentMode);
            updateOrderTableUI(currentOrderList);
            updateTotalSpan();
            activeness.run();

            if ('e_kiosk_id' in order && order.e_kiosk_id != null) {
                e_kiosk_id = order.e_kiosk_id;
                e_kiosk_order_number = order.order_number;
            }
        }

        function getMeals(showMeals = true, el = null) {
            return new Promise((resolve, reject) => {
                currentRequest = $.ajax({
                    async: false,
                    url: "/admin/pos/getMeals",
                    method: "GET",
                    success: resp => {
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            reject(false);
                            return;
                        }

                        if (resp.status == 1) {
                            // These will change with the library in the next update
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            reject(false);
                            return;
                        }

                        if (resp.status == 0) {
                            let data = resp.data;
                            $(".all-products").empty();
                            if ('meals' in data) {
                                let deals = data.meals;
                                currentMeals = {};
                                for (let deal of deals) {
                                    deal.price = parseFloat(deal.price);
                                    deal.food_items.forEach((item, index) => {
                                        deal.food_items[index].price = parseFloat(item.price);
                                    })

                                    let color = '';
                                    const dealItem = document.getElementsByClassName('category-deals');
                                    if (dealItem.length > 0) {
                                        let col = dealItem[0].getAttribute('color');
                                        if (col != '' && col != 'null' && col != null) {
                                            color = `--bg-color-ex: ${col}`;
                                        }
                                    }

                                    deal.extras_by_category = {};

                                    const keysToExtraParse = ['cold_drink_food_items', 'hot_drink_food_items', 'sauces_food_items', 'sides_food_items'];
                                    for (let key of keysToExtraParse) {
                                        if (key in deal) {
                                            if (deal[key].length > 0) {
                                                deal.extras_by_category[deal[key][0].food_category_id] = deal[key];
                                            }
                                        }
                                    }

                                    currentMeals[deal.id] = deal;
                                    let price = parseFloat(deal.price).toFixed(2);
                                    let priceString = window.currency_symbol + price;
                                    if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;


                                    let inclusionHTML = '<p class="m-0">';
                                    let endInclusionHTML = '</p>';
                                    let inclusionEls = [];

                                    let products = deal.food_items.map(item => {
                                        currentProducts[item.id] = item;
                                        return {quantity: item.pivot.quantity, name: item.name}
                                    });

                                    for (let prods of products) {
                                        inclusionEls.push(`${prods.quantity}x ${prods.name}`);
                                    }

                                    let incHTML = inclusionHTML + inclusionEls.join(endInclusionHTML + inclusionHTML) + endInclusionHTML;

                                    let productHtml = `
                                    <div style="${color}" class="card-for-cart card card-flush card-deal pb-5 mw-100" type="deal" deal="${deal.id}">
                                        <div class="card-body text-center px-3 py-2 h-100 justify-content-start gap-2">
                                            <div class="">
                                                <div class="text-start">
                                                    <span class="fw-bold cursor-pointer  product-name font-span" >${deal.name}</span>
                                                </div>
                                            </div>
                                            <div class="inclusions text-start text-white overflow-auto fs-9 w-70">
                                                ${incHTML}
                                            </div>
                                            <span class="me-4 mb-4 bottom-0 end-0 position-absolute text-end fs-1 product-price">${priceString}</span>
                                        </div>
                                    </div>
                                `;

                                    if (showMeals) {
                                        $(".all-products").append(productHtml);

                                        categoryActive.setCurrent(el);
                                        categoryActive.run();
                                    }

                                }
                            }
                            if (resp.message != '') {
                                Swal.fire({
                                    text: resp.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }
                            fillMealModifiers();
                            resolve(currentMeals);
                            return;
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            text: '{{ __("There was an issue with the server please try again after refresh") }}',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        reject(false);
                    }
                });
                return currentRequest;
            });
        }

        function getProductRequestSync(categoryId) {
            return $.ajax({
                url: "/admin/pos/get/products/" + categoryId,
                async: false,
                method: "GET",
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        Swal.fire({
                            text: resp.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        let searching = document.getElementsByClassName('search-product');
                        for (let search of searching) {
                            if ('value' in search) search.value = '';
                        }

                        categoryActive.clear();
                        let data = resp.data;
                        $(".all-products").empty();

                        addProductList(data.products, data.modifiers);

                        categoryActive.setCurrent(this);
                        categoryActive.run();
                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            })
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function getProductRequest(categoryId, esThis = null) {
            return $.ajax({
                url: "/admin/pos/get/products/" + categoryId,
                async: true,
                method: "GET",
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        Swal.fire({
                            text: resp.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        let searching = document.getElementsByClassName('search-product');
                        for (let search of searching) {
                            if ('value' in search) search.value = '';
                        }

                        categoryActive.clear();
                        let data = resp.data;
                        $(".all-products").empty();

                        addProductList(data.products, data.modifiers);

                        if (esThis != null)  {
                            categoryActive.setCurrent(esThis);
                            categoryActive.run();
                        }

                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            })
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        $('#edit-meal-modifiers').on("hidden.bs.modal", function () {
            if (!saving) {
                dismissModalEvent();
            }

            saving = false;
        });

        $('#edit-itemModal').on("hidden.bs.modal", function () {
            if (!saving) {
                dismissModalEvent();
            }

            saving = false;
        });

    </script>
    <script>
        // Scripts moved due to localhost testing
        $(document).on("click", '#morebtn', function(e) {
            let moreModal = document.getElementById('moreModal');
            $(moreModal).modal('show');
        });
    </script>
    <script>
        window.terminal_configuration = null;
        let printerSettings = localStorage.getItem('printer_settings');
        if (printerSettings != null) {
            try {
                printerSettings = JSON.parse(printerSettings);

                if ('receipt_printer' in printerSettings && printerSettings.receipt_printer && printerSettings.receipt_printer.status == 1) {
                    if ('Mine' in window) {
                        // Default port would be 9100 please pay attention 192.168.178.167
                        window.reconnect_string = `con:order-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`;
                        window.Mine.postMessage(`con:order-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`);
                    }
                } else {
                    // No printer or the printer status is 0
                }

                if ('terminal' in printerSettings && printerSettings.terminal && printerSettings.terminal.status == 1) {
                    window.terminal_ip = printerSettings.terminal.terminal.ip;
                    window.terminal_configuration = printerSettings.terminal.terminal;
                    startTerminalDiscovery();
                } else {
                    // No terminal or the terminal status is 0
                }
            } catch(err) {
                // No printer connected
            }
        } else {
            // No printer selected (no messages here until instructed otherwise)
        }

        function connected_terminals(terminal_ips_str) {
            /*
            Swal.fire({
                text: "Returned terminal ips: " + terminal_ips_str,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: {{ __("Ok, got it!") }},
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            */

            const terminal_ips = terminal_ips_str.split(',');
            if (terminal_ips.includes(window.terminal_ip)) {
                // if (window.terminal_alert) {
                Swal.fire({
                    text: nonce + ` {{ __("terminal connected") }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });

                window.connected_terminal = true;
                rawTerminal.currentConnectedStatus = true;
                // }
            }
        }


        let paymentSavingItems = {order: {}, method: {}};
        function payWithCard(order, method) {
            paymentSavingItems.order = order;
            paymentSavingItems.method = method;




            @if($location->manual_payments)
            if (!('terminal_configuration' in window && window.terminal_configuration) || ('terminal_manual_payment' in window.terminal_configuration && window.terminal_configuration.terminal_manual_payment)) {
                payment_complete(true, 1, '{}');
                return;
            }
            @endif

            let amount = '0';
            if (method.id == `{{ config('constants.paymentMethod.paymentMethodCardId') }}`) amount = superTotal;
            if (method.id == `{{ config('constants.paymentMethod.paymentMethodMixId') }}`) amount = cashCalculationsMix.bank;
            if (amount == 0) {
                sendOrder(order, method).catch(err => {
                    cardProceed.opened = false;
                    cardProceed.returnState();
                    $('#myModal').modal('show');
                });
                return;
            }

            if (window.terminal_ip == '') {
                if (window.window.terminal_testing) {
                    sendOrder(order, method).catch(err => {
                        cardProceed.opened = false;
                        cardProceed.returnState();
                        $('#myModal').modal('show');
                    });
                    return;
                } else {
                    Swal.fire({
                        text: `{{ __('The terminal is not connected') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                    return;
                }
            }


            // let ip = window.terminal_ip;
            let terminalDetails = getTerminalDetails();
            if ('Mine' in window) {
                paymentLoader.load();
                // window.Mine.postMessage('pia:' + amount + ':' + ip);
                window.Mine.postMessage('pia:' + amount + ':' + terminalDetails);
            } else console.log('pia:' + amount + ':' + terminalDetails);
            return;
        }

        function payment_complete(data, isJson, rawData) {
            let manualCard = false;
            if (data === true) {
                manualCard = true;
                data = '{}';
            }

            paymentSavingItems.order.payment_status = 1;
            // We would use this but honestly a try catch felt better and safer
            /*
            if (isJson == 1) {
                JSON.parse(data);
            }
            */

            const requiredKeys = [
                "title", "type", "card_type",
                "card_number", "date", "trm_id", "aid",
                "seq_cnt", "acq_id", "total",
                "ref_no", "auth_code"
            ];

            let backData = {};
            try {
                backData = JSON.parse(data);
                for (let key of requiredKeys) {
                    if (!(key in backData)) {
                        backData[key] = null;
                    }
                }

                backData.isJson = isJson;
                backData.raw_manual = data;
                backData.raw_extra_data = rawData;
            } catch(err) {
                backData = {
                    raw_full_data: data,
                    raw_extra_data: rawData
                }
            }

            paymentSavingItems.order.payment_data = backData;
            if (manualCard) paymentSavingItems.order.is_manual_payment = 'true';

            sendOrder(paymentSavingItems.order, paymentSavingItems.method, {type: 'payment'}).catch(async err => {
                cardProceed.opened = false;
                cardProceed.returnState();
                $('#myModal').modal('show');
                paymentLoader.close();

                // Retrying madness xd
                let retries = 0;
                let done = false;
                while (done == false && retries < 4) {
                    try {
                        const order = await sendOrder(orderDetails, null, {payment_skip: true})
                        done = true;
                    } catch(err) {
                        cardProceed.opened = false;
                        cardProceed.returnState();
                        $('#myModal').modal('show');
                        retries++;
                    }
                }

                if (!done) {
                    cardProceed.opened = false;
                    cardProceed.returnState();
                    if ('type' in extraOptions && extraOptions.type == 'payment') {
                        // The order is already paid and we must save it.
                        orderDetails.later_order = true;
                        let failedPaymentObjects = [orderDetails];

                        const failedPayments = localStorage.getItem('failed_payments');
                        if (failedPayments) {
                            try {
                                const payments = JSON.parse(failedPayments);
                                if (Array.isArray(payments)) {
                                    failedPaymentObjects = payments;
                                    failedPaymentObjects.push(orderDetails);
                                }
                            } catch(err) {
                                // meh
                            }
                        }

                        localStorage.setItem('failed_payments', JSON.stringify(failedPaymentObjects));
                        reject(orderDetails);
                        return;
                    }
                }
            });
        }

        function failed_terminal(terminal_message = '') {
            Swal.fire({
                text: terminal_message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: `{{ __("Ok, got it!") }}`,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
            paymentLoader.close();

            if ('Mine' in window) {
                window.Mine.postMessage('terminal_sts:' + window.terminal_ip);
            } else alert('You are not in apk. terminal_sts:' + window.terminal_ip);
        }

        function startTerminalDiscovery() {
            if ('Mine' in window)
                window.Mine.postMessage('sd:;');

            setTimeout(() => {
                if (window.connected_terminal) return;
                Swal.fire({
                    text: `{{ __("The selected terminal was not found within a minute, retrying.") }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                startTerminalDiscovery();
            }, 60_000);
        }

        function getTerminalDetails() {
            const config = window.terminal_configuration;

            for (key in config) {
                // Ensure splits are not broken, it's better not to work then crash
                config[key] = config[key].replaceAll('--', '__');
            }

            return config.ip + "--" + config.terminal_type + "--" + config.socket_mode + "--" + config.port + "--" + config.compatibility_port
        }

        function connected(nonce) {
            window.connected_printer = true;
            if ('debugging_print' in window && window.debugging_print) alert(nonce + " connected");
            console.log('mutation');
            console.log(nonce + ' printer connected');
        }

        function failed_connection(nonce, reason, status) {
            // window.connected_printer = false;
            console.log('mutation');
            console.log(nonce, reason, status);

            // This would have been better but it's required to finish very sooooon
            if (reason == 'Succeed') {
                window.connected_printer = true;
                if ('debugging_print' in window && window.debugging_print) {
                    alert('Printed');
                }
                // Printing worked
                return;
            }

            if (false && status == 3) {
                Swal.fire({
                    text: nonce + ` {{ __('failed to connect, please reconfigure your devices.') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });

                window.connected_printer = false;
            } else {
                if (window.debugging_print) {
                    Swal.fire({
                        text: nonce + ` __("failed to connect for external reasons, please contact support and show this message:") ` + reason,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }

                window.connected_printer = false;

                setTimeout(() => {
                    if ('Mine' in window && window.reconnect_string != '') {
                        window.Mine.postMessage(window.reconnect_string);
                    }
                }, 5000);
            }
        }

        function test_logs() {
            if ('Mine' in window) {
                window.Mine.postMessage('log_test:');
                return;
            }
            alert('Not in apk');
        }

        function send_terminal_logs(message) {
            $.ajax({
                method: 'POST',
                url: '/logs',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    message: message
                },
                success: resp => {
                    if ('message' in resp) {
                        if (window.log_alert) {
                            Swal.fire({
                                text: 'Log was saved',
                                icon: 'success',
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            })
                        }
                    } else {
                        if (window.log_alert) {
                            Swal.fire({
                                text: "Log couldn't save",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    }
                },
                error: err => {
                    if (window.log_alert) {
                        Swal.fire({
                            text: "Log couldn't save",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                }
            })
        }

        /*
        For later
        window.onlineStatus = {
            events: {
                online: [],
                offline: []
            },
            isOnline: true,
            image: null,
            onlineCheckTime: 60000,
            offlineCheckTime: 5000,
            url: `{{ asset('assets/online/1.jpg') }}`,
            interval: null,
            init: function() {
                this.image = document.createElement('img');
                this.image.onload = () => {
                    this.isOnline = true;
                    this.startChecking(this.onlineCheckTime);

                    for (let item of this.events.online) {
                        if (typeof(item) == 'function')
                            item();
                    }
                }

                this.image.onerror = () => {
                    this.isOnline = false;
                    this.startChecking(this.offlineCheckTime);

                    for (let item of this.events.offline) {
                        if (typeof(item) == 'function')
                            item();
                    }
                }
            },
            stopChecking: function() {
                if (this.interval != null) clearInterval(this.interval);
            },
            startChecking: function(time) {
                if (this.interval != null) clearInterval(this.interval);

                this.interval = setInterval(() => {
                    this.check();
                }, time);
            },
            check: function() {
                this.image.src = this.url + "?v=" + Date.now();
            }
        }
        window.onlineStatus.init();

        window.onlineStatus.startChecking(60000);
        */
    </script>

    <script>
        @if ($order != null)
        document.addEventListener("DOMContentLoaded", () => {
            fillMealModifiers();
            loadOrder(JSON.parse(`{!! json_encode($order) !!}`));
        });
        @endif
    </script>
<script>
let details = {
    dateStr: null,
    parts: null
}

const yearSelect = document.getElementById('year-cash');
const monthSelect = document.getElementById('months-cash');

const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
if (clearButton) {
    clearButton.addEventListener('click', e => {
        details.dateStr = null;

        const inputsToClear = document.querySelectorAll('#kt_ecommerce_sales_flatpickr, #kt_ecommerce_sales_flatpickr ~ input');
        for (let inputToClear of inputsToClear) {
            inputToClear.value = '';
        }

        details.parts = null;

        yearSelect.value = '';
        monthSelect.value = '';
        $(yearSelect).select2();
        $(monthSelect).select2();

        flatpickr.clear();
    });
}

const element = document.querySelector('#kt_ecommerce_sales_flatpickr');
let flatpickr;
flatpickr = $(element).flatpickr({
    altInput: true,
    altFormat: "d/m/Y",
    dateFormat: "Y-m-d",
    mode: "range",
    onChange: function (selectedDates, dateStr, instance) {
        if(selectedDates.length === 2 || selectedDates.length === 0){
            details.parts = null;

            yearSelect.value = '';
            monthSelect.value = '';
            $(yearSelect).select2();
            $(monthSelect).select2();
            
            details.dateStr = dateStr;
        }
    },
});

$(yearSelect).on('select2:opening', () => {
    document.body.classList.add('nopicker');
})

$(monthSelect).on('select2:opening', () => {
    document.body.classList.add('nopicker');
})

$(yearSelect).on('select2:close', () => {
    document.body.classList.remove('nopicker');
})

$(monthSelect).on('select2:close', () => {
    document.body.classList.remove('nopicker');
})

$(yearSelect).on('change', e => {
    details.dateStr = null;
    const inputsToClear = document.querySelectorAll('#kt_ecommerce_sales_flatpickr, #kt_ecommerce_sales_flatpickr ~ input');
    for (let inputToClear of inputsToClear) {
        inputToClear.value = '';
    }

    if (details.parts == null) {
        details.parts = {
            year: null,
            month: null
        }
    }

    details.parts.year = yearSelect.value;
})

$(monthSelect).on('change', e => {
    details.dateStr = null;
    const inputsToClear = document.querySelectorAll('#kt_ecommerce_sales_flatpickr, #kt_ecommerce_sales_flatpickr ~ input');
    for (let inputToClear of inputsToClear) {
        inputToClear.value = '';
    }

    if (details.parts == null) {
        details.parts = {
            year: null,
            month: null
        }
    }

    details.parts.month = monthSelect.value;
});

// Stupidity makes you do great things
$(document).on('mousedown', '.swal2-container', () => {
    setTimeout(() => {
        element._flatpickr.close();
    });
})

 $('.open-manual-z-report').on('click', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $('#report-more-modal').modal('hide');
  $("#print-manual-z-reports").modal('show');
  return false;
});
  
$(".discard-manual-z-report").click(function(e) {
  e.preventDefault();
  $("#print-manual-z-reports").modal("hide");
});
  
const printBtn = document.getElementById('print-custom-z-rep');
printBtn.addEventListener('click', e => {

    e.preventDefault();


    setTimeout(() => {
        element._flatpickr.close();
    });

  

    const endpoint = ['cash_register_id=' + document.getElementById('cash_register_id').value];
    if (details.dateStr !== null) {
        endpoint.push('date=' + encodeURIComponent(details.dateStr));
    } else {
        if (details.parts === null) {
            Swal.fire({
                text: "You must select either the date or the year and month",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
            return;
        } 

        if (details.parts.month === null && details.parts.year === null) {
            Swal.fire({
                text: "You must select either the date or the year and month",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
            return;
        }

        endpoint.push('month=' + encodeURIComponent(details.parts.month));
        endpoint.push('year=' + encodeURIComponent(details.parts.year));
    }

    $.ajax({
        method: 'GET',
        async: false,
        url: `/zReport/month?` + endpoint.join('&'),
        success: resp => {
            if (resp.status == 2) {
                location.href = resp.redirect_uri;
                return;
            }

            if (resp.status == 1) {
                // These will change with the library in the next update
                Swal.fire({
                    text: resp.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            if (resp.status == 0) {
                if (window.printer_testing || window.connected_printer) {
                    Swal.fire({
                        text: window.printing,
                        icon: "info",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });


                    window.invoicePrinting(resp.print_order);

                } else {
                    Swal.fire({
                        text: window.noPrinterConfigured,
                        icon: "info",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            }

            if ('message' in resp && resp.message != '') {
                Swal.fire({
                    text: resp.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
            }
        },
        error: function(err) {
            console.log(err);
            Swal.fire({
                text: window.unexpectedError,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
        }
    });

    return false;
})
</script>
@endsection
