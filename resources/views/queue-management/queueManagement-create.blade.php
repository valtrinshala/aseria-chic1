@extends('layouts.main-view')
@section('title', 'Create Queue')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/queue-management/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('queueManagement.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Queue management') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new queue') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('queueManagement.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_queueManagement_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __("Queue Management") }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Queue Details') }}</h2> <span
                                                        class="small p-5 text-gray-900">{{ __('Enter queue details') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Queue name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2"
                                                            placeholder="{{ __('Queue name') }}" />
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="hidden_key"
                                                            class="required form-label fw-bold">{{ __('Key') }}</label>
                                                        <input id="hidden_key" disabled type="text"
                                                            class="form-control mb-2" />
                                                        <input type="hidden" id="key" name="key" />
                                                    </div>
                                                    <div class="mb-10 fv-row col-4">
                                                        <label for="location"
                                                            class="required form-label fw-bold">{{ __('Queue location') }}</label>
                                                        <input id="location" type="text" disabled
                                                            value="{{ $location['name'] }}" class="form-control mb-2"
                                                            placeholder="{{ __('Enter queue location') }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Authentication') }}</h2><span
                                                        class="small p-5 text-gray-900">{{ __('Configure queue authentication') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="authentication_code"
                                                            class="required form-label fw-bold">{{ __('Authentication Code') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="authentication_code" type="password"
                                                            name="authentication_code" class="form-control"
                                                            placeholder="{{ __('Enter authentication code') }}" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status*') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the status is deactivated, it cannot be used.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="authentication_code_status">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="authentication_code_status"
                                                                name="authentication_code_status" type="checkbox"
                                                                role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title fw-bold">
                                                    <h2>{{ __('Queue URL') }}</h2><span
                                                        class="small p-5 text-gray-900">{{ __('Configure queue link') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="url"
                                                            class="required form-label">{{ __('URL') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend w-100">
                                                                <div class="input-group-text w-100 create-url" data-url-static="{{ rtrim(getenv('APP_URL'), '/') }}/queue/{{ Illuminate\Support\Str::slug($location->name, '_') }}/">
                                                                    {{ rtrim(getenv('APP_URL'), '/') }}/queue/{{ Illuminate\Support\Str::slug($location->name, '_') }}/
                                                                </div>
                                                            </div>
                                                            <input id="url" type="hidden" name="url"
                                                                   class="form-control mb-2 input-url-static"
                                                                   data-url="queue/{{ Illuminate\Support\Str::slug($location->name, '_') }}/"
                                                                   placeholder="{{ __('Enter route') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status*') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the status is deactivated, it cannot be used.') }}</span></label>

                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="status">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="status" name="status"
                                                                type="checkbox" role="checkbox" checked value="1">
                                                        </div>
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
