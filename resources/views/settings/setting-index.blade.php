@extends('layouts.main-view')
@section('title', 'Settings')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card-header border-0 pt-6 p-0 h-100px">
                        <div class="card-title d-flex h-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24.001 24"
                                style="align-self: center">
                                <g id="settings-active" transform="translate(-8718.999 -6517)">
                                    <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                        transform="translate(8719 6517)" fill="none" />
                                    <path id="Subtraction_5" data-name="Subtraction 5"
                                        d="M14.542-72H9.456l-.58-3.8a8.534,8.534,0,0,1-1.418-.643,7.9,7.9,0,0,1-1.309-.892L2.562-75.724,0-80.186l3.242-2.349a6.12,6.12,0,0,1-.093-.726c-.019-.265-.029-.509-.029-.726s.01-.449.029-.714a7.6,7.6,0,0,1,.093-.763L0-87.826l2.563-4.426,3.574,1.584a9.87,9.87,0,0,1,1.322-.887,6.958,6.958,0,0,1,1.406-.617L9.456-96h5.086l.58,3.81a10.728,10.728,0,0,1,1.417.638,6.782,6.782,0,0,1,1.275.885l3.635-1.584L24-87.826l-3.29,2.346a4.382,4.382,0,0,1,.111.758c.015.249.023.491.023.722s-.009.457-.028.7a6.915,6.915,0,0,1-.105.772l3.278,2.342-2.562,4.462-3.611-1.62a10.756,10.756,0,0,1-1.294.914,5.789,5.789,0,0,1-1.4.621L14.542-72ZM11.963-87.558a3.534,3.534,0,0,0-2.556,1.036A3.413,3.413,0,0,0,8.349-84a3.415,3.415,0,0,0,1.057,2.523,3.537,3.537,0,0,0,2.556,1.036,3.529,3.529,0,0,0,2.561-1.036A3.424,3.424,0,0,0,15.576-84a3.421,3.421,0,0,0-1.052-2.523A3.526,3.526,0,0,0,11.963-87.558Z"
                                        transform="translate(8719 6613)" fill="#5d4bdf" />
                                </g>
                            </svg>

                            <span
                                class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 p-4">
                                {{ __('Settings') }}</span>
                        </div>
                        <div style="background: transparent">
                            <style>
                                .setting-cards a, .setting-cards .card {
                                    height: 100%;
                                }

                                .setting-cards {
                                    row-gap: var(--bs-gutter-x);
                                }
                            </style>
                            <div class="row setting-cards">
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('general_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('settings.get.general') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/general-settings.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('General') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure general site settings') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('locations_access', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('ourLocation.index') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/our-locations.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Our locations') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Manage your restaurant locations') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                {{--<div class="col-xl-3">
                                    <a class="d-block" href="{{ route('settings.get.appearance') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/appearance.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-3">{{ __('Appearance') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure the site icon and background') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>--}}
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('client_appearance_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('settings.get.client.appearance') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/appearance.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-3">{{ __('Client appearance') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure the client  site icon and background') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('currency_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('settings.get.currency') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/currency.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Currency') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure site currency symbology') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('unit_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('unit.index') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/units.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Units') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure units for ingredients') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('tax_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a class="d-block" href="{{ route('tax.index') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/finance-settings.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Tax') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure tax rate, type and implementation') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('authentication_configuration', $user->userRole->permissions)) d-none @endif">
                                    {{--                                <a href="{{ route('settings.get.authentication') }}"> --}}
                                    <a href="javascript:void(0);">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/authentication.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Authentication') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure registration, and related things') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('captcha_configuration', $user->userRole->permissions)) d-none @endif">
                                    {{--                                <a href="{{ route('settings.get.captcha') }}"> --}}
                                    <a href="javascript:void(0)">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/captcha.svg') }}" alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Captcha') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure captcha settings & preferences') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('localization_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a href="{{ route('settings.get.localization') }}">
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/language.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Localization') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure localization settings for the site') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('printer_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a href="{{ route('settings.get.invoicePrinting') }}">
{{--                                    <a href="javascript:void(0)">--}}
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/print-settings.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Invoice Printing') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure printing settings for the sale invoice') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('contact_helper_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a href="{{ route('settings.get.helpContacts') }}">
{{--                                    <a href="javascript:void(0)">--}}
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/out-going-mail.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Help desk contact info') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure help desk') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 @if($user->role_id != config('constants.role.adminId') && !in_array('printer_settings_configuration', $user->userRole->permissions)) d-none @endif">
                                    <a href="{{ route('printerSettings.index') }}">
{{--                                    <a href="javascript:void(0)">--}}
                                        <div
                                            class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch">
                                            <div class="card-body text-center">
                                                <img width="25" src="{{ asset('images/svg/print-settings.svg') }}"
                                                    alt="">
                                                <h3 class="fw-bold my-1 mt-5">{{ __('Device settings') }}</h3>
                                                <p class="text-gray-900-75 fw-semibold fs-6">
                                                    {{ __('Configure device settings') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
