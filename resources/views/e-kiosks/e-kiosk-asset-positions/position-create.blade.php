@extends('layouts.main-view')
@section('title', 'Create Asset')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/e-kiosk-positions/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('eKioskAssetPosition.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Manage positions >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new position') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('eKioskAssetPosition.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_eKioskAssetPosition_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('eKiosk Positions') }}</h2>
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
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Position Deatils') }}</h2>
                                                    <span
                                                    class="small p-5 text-gray-900">{{ __('This information will be displayed publicly.') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-4">
                                                <div class="row mb-5">
                                                    <div class="fv-row col-6">
                                                        <div class="fv-row">
                                                            <div class="row">
                                                                <label
                                                                    class="col-3 required form-label fw-bold">{{ __('Position name') }}</label>
                                                            </div>
                                                        </div>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2"
                                                            placeholder="{{ __('Position name goes here') }}" />
                                                    </div>
                                                    <div class="fv-row col-6">
                                                        <div class="fv-row">
                                                            <div class="row">
                                                                <label
                                                                    class="col required form-label fw-bold">{{ __('Asset Key') }}</label>
                                                            </div>
                                                        </div>
                                                        <input id="asset_key" type="text" name="asset_key"
                                                            class="form-control mb-2"
                                                            placeholder="{{ __('Asset Key') }}" />
                                                    </div>
                                                </div>
                                                <div class="fv-row">
                                                    <div class="row">
                                                        <label
                                                            class="col-6 required form-label fw-bold">{{ __('Description') }}</label>
                                                        <span
                                                            class="text-end col-6 text-gray-900 fs-7">{{ __('Set a description to the category for better visibility.') }}</span>
                                                        <textarea name="description" id="description" class="form-control mx-3" rows="3"
                                                            placeholder="{{ __('Type a message') }}"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Status') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body py-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the position is deactivated, it will be invisible and cannot be accessed from anywhere.') }}</span></label>
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
