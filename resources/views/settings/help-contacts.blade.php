@extends('layouts.main-view')
@section('title', 'General Setting')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/settings/help-contacts.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{route('settings')}}" class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }} > </a>
                        <span class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Help contacts')}}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        @include('settings.goback-button')
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_help_contact" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="page-id" value="general">
                        <div class="d-flex flex-column gap-7 w-25 mb-7 me-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10 flex-shrink-0 flex-grow-0 w-400px">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('Help Contacts')}}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column w-auto flex-row-fluid gap-7 gap-lg-10">
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
                                                <table class="w-100 help-contacts">
                                                    <tr>
                                                        <td width="15%" class="form-label">{{__('Company name')}}</td>
                                                        <td width="auto" class="form-label">{{ isset($data['data']) ? $data['data']['company_name'] : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Website') }} : </td>
                                                        <td class="form-label"><a target="_blank" href="{{ isset($data['data']) ? $data['data']['website'] : null }}">{{ isset($data['data']) ? $data['data']['website'] : null }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Working hours') }} : </td>
                                                        <td class="form-label">{{ isset($data['data']) ? $data['data']['working_hours'] : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Working days') }} : </td>
                                                        <td class="form-label">{{ isset($data['data']) ? $data['data']['working_days'] : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Telephone') }} : </td>
                                                        <td class="form-label"><a href="tel:{{ isset($data['data']) ? $data['data']['telephone'] : null }}">{{ isset($data['data']) ? $data['data']['telephone'] : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Urgent calls') }} : </td>
                                                        <td class="form-label"><a href="tel:{{ isset($data['data']) ? $data['data']['urgent_calls'] : null }}">{{ isset($data['data']) ? $data['data']['urgent_calls'] : null }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">{{ __('Email') }} : </td>
                                                        <td class="form-label"><a href="mailto:{{ isset($data['data']) ? $data['data']['email'] : null }}">{{ isset($data['data']) ? $data['data']['email'] : null }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label">&nbsp;</td>
                                                        <td class="form-label"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="form-label fw-bold">{{__('Description')}} : </td>
                                                        <td class="form-label fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <div class="w-100">

                                                        </div>
                                                        <td colspan="2" class="form-label w-auto">{{ isset($data['data']) ? $data['data']['description'] : null }} </td>
                                                    </tr>
                                                </table>
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
