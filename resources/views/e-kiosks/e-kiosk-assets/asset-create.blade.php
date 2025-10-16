@extends('layouts.main-view')
@section('title', 'Create eKiosk')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/e-kiosk-assets/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('eKioskAsset.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Manage assets') }}
                            ></a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new asset') }}</span>

                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('eKioskAsset.index') }}"
                           class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_eKioskAsset_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="card card-flush py-1">
                                <div class="card-header">
                                    <div class="card-toolbar w-100">
                                        <h2>{{ __('Asset') }}</h2><span
                                            class="small p-5 text-gray-900">{{ __('Only *.png, *.svg, *.webp, *.jpg and *.jpeg image files are accepted') }}</span>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="card-body pt-0 pb-0 px-0">
                                        <div class="image-input w-100 mb-3" data-kt-image-input="true">
                                            <label class="w-100 mb-4" title="Change image">
                                                <div
                                                    class="notice cursor-pointer d-flex bg-light-primary rounded border-primary border border-dashed">
                                                    <i
                                                        class="ki-duotone ki-svg/files/upload.svg fs-2tx text-primary me-4"></i>
                                                    <div class="d-flex flex-stack flex-grow-1">
                                                        <div class="fw-semibold py-2">
                                                            <div class="fw-bold">{{ __('Quick file uploader') }}
                                                            </div>
                                                            <div class="fs-6 text-gray-700">
                                                                {{ __('Drag & Drop or choose files from computer') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input class="d-none allow-video" type="file" name="image"
                                                       accept=".jpg, .jpeg, .png, .svg, .webp, .mp4, .avi, .mov, .flv, .mkv"/>
                                                <input type="hidden" name="image_remove"/>
                                            </label>
                                            <div class="d-flex justify-content-center">
                                                <div class="image-input-wrapper"></div>
                                            </div>
                                        </div>
                                        <div class="text-muted fs-7">{{ __('Recommended size') }}
                                            {{ __(':1080x1080') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Asset position') }}</h2>
                                                </div>
                                            </div>

                                            <div class="card-body pt-0">
                                                <div class="row fv-row">
                                                    <div class="col-6">
                                                        <label
                                                            class="mt-3 required form-label fw-bold">{{ __('Positions') }}</label>
                                                        <select name="position_id" id="positions" name="e_kiosk_id"
                                                                class="form-select"
                                                                data-control="select2"
                                                                data-placeholder="{{ __('Select an option') }}"
                                                                data-allow-clear="true">
                                                            <option></option>
                                                            @foreach ($positions as $position)
                                                                <option value="{{ $position->id }}"
                                                                        data-url="{{ $position->url }}"
                                                                        data-description="{{ $position->description }}"
                                                                        data-status="{{ $position->status }}">{{ $position->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                   <div class="fv-row col-6 mt-3">
                                                        <label
                                                            class="required form-label fw-bold">{{ __('e Kiosks') }}</label>
                                                        <select id="e_kiosks" name="e_kiosk_id" class="form-select"
                                                            data-control="select2"
                                                            data-placeholder="{{ __('Select an option') }}"
                                                            data-allow-clear="true">
                                                            <option></option>
                                                            @foreach ($eKiosks as $eKiosk)
                                                                <option value="{{ $eKiosk->id }}">{{ $eKiosk->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('General') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="mb-10 fv-row">
                                                    <div class="row">
                                                        <label
                                                            class="col required pt-3 form-label fw-bold">{{ __('Asset Name') }}</label>
                                                        <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('A deal name is required and recommended to be unique.') }}</span>
                                                    </div>
                                                    <input type="text" name="name" class="form-control mb-2"
                                                           placeholder="Category name" value=""/>
                                                </div>

                                            </div>

                                            <div class="card-body pt-0">
                                                <div class="mb-10">
                                                    <div class="row">
                                                        <label class="col required form-label fw-bold"
                                                               for="description">{{ __('Description') }}</label>
                                                        <h4 class="mt-2 fs-6 fw-normal extend-description">
                                                        </h4>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Status') }}</h2>
                                                </div>
                                            </div>
                                            <div class="ps-10">
                                                <label class="form-check-label mb-4" for="activated"><span
                                                        class="fw-bold text-gray-900">{{ __('Asset Status') }}</span>
                                                </label>
                                                <div class="form-check form-switch">
                                                    <label class="form-check-label mb-4"
                                                           for="authentication_code_status">{{ __('Activated') }}</label>
                                                    <input class="form-check-input" id="status" name="status"
                                                           type="checkbox" role="checkbox" checked value="1">
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
