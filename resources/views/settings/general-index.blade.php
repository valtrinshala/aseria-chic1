@extends('layouts.main-view')
@section('title', 'General Setting')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/settings/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{route('settings')}}" class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }} > </a>
                        <span class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('General setting')}}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                           @include('settings.goback-button')
                        <button id="submitButton"
                                class="btn btn-primary w-125px border-0">{{__('Save')}}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_setting_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="page-id" value="general">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10 flex-shrink-0 flex-grow-0 w-400px">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('General setting')}}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('General')}}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="app_url" class="required form-label fw-bold">{{__('App URL')}}</label>
                                                        <input readonly id="app_url" type="text" name="app_url" value="{{ $data['app_url'] }}"
                                                               class="form-control mb-2" placeholder="{{__('App URL')}}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="app_name" class="required form-label fw-bold">{{__('App name')}}</label>
                                                        <input id="app_name" type="text" name="app_name" value="{{ $data['app_name'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('App name') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="tva" class="required form-label fw-bold">{{ __('TVA') }}</label>
                                                        <input id="tva" type="text" name="tva" value="{{ $data['tva']}}"
                                                               class="form-control mb-2" placeholder="{{ __('TVA') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_phone" class="required form-label fw-bold">{{ __('App Phone') }}</label>
                                                        <input id="app_phone" type="text" name="app_phone" value="{{ $data['app_phone']}}"
                                                               class="form-control mb-2" placeholder="{{ __('App Phone') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="auth_code_for_e_kiosks" class="required form-label fw-bold">{{ __('Auth code for e kiosks') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="auth_code_for_e_kiosks" type="password" name="auth_code_for_e_kiosks" value="{{ $data['auth_code_for_e_kiosks']}}"
                                                               class="form-control" placeholder="{{ __('Auth code for e kiosks') }}"/>
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_address" class="required form-label fw-bold">{{ __('Address') }}</label>
                                                        <input id="app_address" type="text" name="app_address" value="{{ $data['app_address'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('Address') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_address" class="required form-label fw-bold">{{ __('WI-FI name') }}</label>
                                                        <input id="app_address" type="text" name="wifi_name" value="{{ $data['wifi_name'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('WI-FI password') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_address" class="required form-label fw-bold">{{ __('WI-FI password') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="app_address" type="password" name="wifi_password" value="{{ $data['wifi_password'] }}"
                                                               class="form-control" placeholder="{{ __('WI-FI password') }}"/>
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="web" class="required form-label fw-bold">{{ __('Web') }}</label>
                                                        <input id="web" type="text" name="web" value="{{ $data['web'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('Web') }}"/>
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_address" class="required form-label fw-bold">{{ __('Facebook') }}</label>
                                                        <input id="app_address" type="text" name="socials[facebook]" value="{{ $data['socials']['facebook'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('Facebook') }}"/>
                                                    </div>
{{--                                                    <div class="mb-10 fv-row col-4">--}}
{{--                                                        <label for="app_address" class="required form-label fw-bold">{{ __('X(Twitter)') }}</label>--}}
{{--                                                        <input id="app_address" type="text" name="socials[twitter]" value="{{ $data['socials']['twitter'] }}"--}}
{{--                                                               class="form-control mb-2" placeholder="{{ __('X(Twitter)') }}"/>--}}
{{--                                                    </div>--}}
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="app_address" class="required form-label fw-bold">{{ __('Instagram') }}</label>
                                                        <input id="app_address" type="text" name="socials[instagram]" value="{{ $data['socials']['instagram'] }}"
                                                               class="form-control mb-2" placeholder="{{ __('Instagram') }}"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
