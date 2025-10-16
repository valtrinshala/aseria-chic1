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
                        <span class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Currency') }}</span>
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
                        <input type="hidden" id="page-id" value="currency">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('Currency')}}</h2>
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
                                                    <div class="mb-10 fv-row">
                                                        <label for="currency_symbol" class="required form-label fw-bold">{{__('Currency symbol')}}</label>
                                                        <input id="currency_symbol" type="text" name="currency_symbol" value="{{ $data['currency_symbol'] }}"
                                                               class="form-control mb-2" placeholder="{{__('Currency symbol')}}"/>
                                                    </div>
                                                    <div class="fv-row">
                                                        <label for="app_locale"
                                                               class="form-label fw-bold">{{ __('Symbol direction') }}</label>
                                                        <select id="app_locale" name="currency_symbol_on_left" class="form-select mb-2"
                                                                data-control="select2"
                                                                data-placeholder="{{ __('Select an option') }}"
                                                                data-allow-clear="true">
                                                                <option {{ $data['currency_symbol_on_left'] ? 'selected' : ''}} value="1">{{ __('LTR') }}</option>
                                                                <option {{ !$data['currency_symbol_on_left'] ? 'selected' : ''}} value="0">{{ __('RTL') }}</option>
                                                        </select>
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
