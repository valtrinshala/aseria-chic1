@extends('layouts.main-view')
@section('title', 'Create Category')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/food-categories/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('foodCategory.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Categories >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new category') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('foodCategory.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_category_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <input type="hidden" value="#5D4BDF" name="color" class="input-color">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-toolbar w-100">
                                        <div class="row">
                                            <div class="col-3">
                                                <h3 id="colorDiv">{{ __('Color') }}</h3>
                                            </div>
                                            <div class="col-9">
                                                <span id="infoSpanColor"
                                                    class="small text-gray-900">{{ __('This category and its products will associate with this color') }}</span>
                                            </div>
                                            <div class="col-3">
                                                <h3 id="iconDiv">{{ __('Icon') }}</h3>
                                            </div>
                                            <div class="col-9">
                                                <span id="infoSpanDiv" class="small text-gray-900"
                                                    style="display:none;">{{ __('This category and its products will associate with this icon') }}</span>
                                            </div>
                                        </div>
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bold w-100"
                                            id="kt_security_summary_tabs">
                                            <li class="nav-item w-50 border-bottom border-3">
                                                <a class="justify-content-center m-0 w-100 nav-link text-active-primary active"
                                                    data-kt-countup-tabs="true" data-bs-toggle="tab"
                                                    href="#kt_security_summary_tab_pane_hours">{{ __('Color') }}</a>
                                            </li>
                                            <li class="nav-item w-50 border-bottom border-3">
                                                <a class="justify-content-center m-0 w-100 nav-link text-active-primary"
                                                    data-kt-countup-tabs="true" data-bs-toggle="tab"
                                                    id="kt_security_summary_tab_day"
                                                    href="#kt_security_summary_tab_pane_day">{{ __('Icon') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="card-body pt-7 pb-0 px-0">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="kt_security_summary_tab_pane_hours"
                                                role="tabpanel">
                                                <div class="row">
                                                    <div class="col-7 mx-4 card h-250px category-color"
                                                        style="background-color:#f44336"></div>
                                                    <div class="col-4 h-250px">
                                                        @foreach ($colors as $color)
                                                            <div class="row colorCategories">
                                                                @foreach ($color as $eachColor)
                                                                    <div data-color="{{ $eachColor }}"
                                                                        style="background-color: {{ $eachColor }};"
                                                                        class="col-sm m-1 h-35px cursor-pointer rounded">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="kt_security_summary_tab_pane_day"
                                                role="tabpanel">
                                                <div class="image-input w-100 mb-3" data-kt-image-input="true">
                                                    <label class="w-100 mb-4" title="{{__("Change image")}}">
                                                        <div
                                                            class="notice cursor-pointer d-flex bg-light-primary rounded border-primary border border-dashed">
                                                            <i
                                                                class="ki-duotone ki-svg/files/upload.svg fs-2tx text-primary me-4"></i>
                                                            <div class="d-flex flex-stack flex-grow-1">
                                                                <div class="fw-semibold py-2">
                                                                    <div class="fw-bold">{{ __('Quick file uploader') }}
                                                                    </div>
                                                                    <div class=" fs-6 text-gray-700">
                                                                        {{ __('Drag & Drop or choose files from computer') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input class="d-none" type="file" name="image"
                                                        accept=".png, .svg, .webp, .jpg, .jpeg" />
                                                        <input type="hidden" name="image_remove" />
                                                    </label>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="image-input-wrapper"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted fs-7">{{ __('Recommended size') }} :1080x1080</div>
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
                                                    <h2>{{ __('General') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-1">
                                                <div class="mb-10 fv-row">
                                                    <div class="row">
                                                        <label
                                                            class="col required form-label fw-bold">{{ __('Category Name') }}</label>
                                                        <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('A category name is required and recommended to be unique.') }}</span>
                                                    </div>
                                                    <input type="text" name="name" class="form-control mb-2"
                                                        placeholder={{ __('Category Name') }} value="" />
                                                </div>

                                            </div>
                                            <div class="card-body pt-0 pb-1">
                                                <div class="mb-10">
                                                    <div class="row">
                                                        <label class="col required form-label fw-bold"
                                                            for="description">{{ __('Description') }}</label> <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('Set a description to the category for better visibility.') }}</span>
                                                    </div>
                                                    <textarea name="description" class="form-control mb-3" rows="4" placeholder="{{ __('Type a message') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-3">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Status') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status*') }}</span>
                                                            <span
                                                                class="small p-5 text-muted text-nowrap">{{ __('When the category is deactivated, the category will be invisible and cannot be accessed from anywhere.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="activated">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="activated" name="status"
                                                                type="checkbox" role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Category for kitchen and side and a drink for eKiosk') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 col">
                                                        <label class="form-check-label mb-4" for="category_for_kitchen"><span
                                                                class="fw-bold text-gray-900">{{ __('Category for kitchen') }}</span>
                                                            <span
                                                                class="small p-5 text-muted text-nowrap">{{ __('When the category is deactivated for kitchen, the category will be invisible and cannot be accessed from kitchen.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="category_for_kitchen">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="category_for_kitchen" name="category_for_kitchen"
                                                                type="checkbox" role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 col">
                                                        <label class="form-check-label mb-4" for="category_to_ask_for_extra_kitchen"><span
                                                                class="fw-bold text-gray-900">{{ __('Ask to add a side and a drink for eKiosk') }}</span>
                                                            <span
                                                                class="small p-5 text-muted text-nowrap">{{ __('When the category is deactivated for eKiosk, eKiosk does not ask if you want to get something extra.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="category_to_ask_for_extra_kitchen">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="category_to_ask_for_extra_kitchen" name="category_to_ask_for_extra_kitchen"
                                                                type="checkbox" role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Show this category for POS and eKiosk') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 col">
                                                        <label class="form-check-label" for="category_for_pos"><span
                                                                class="fw-bold text-gray-900">{{ __('POS') }}</span></label>
                                                            <div class="form-check form-switch mt-4">
                                                            <label class="form-check-label mb-4"
                                                                   for="category_for_pos">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="category_for_pos" name="category_for_pos"
                                                                   type="checkbox" role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 col">
                                                        <label class="form-check-label" for="category_for_kiosk"><span
                                                                class="fw-bold text-gray-900">{{ __('eKiosk') }}</span></label>
                                                            <div class="form-check form-switch mt-4">
                                                            <label class="form-check-label mb-4"
                                                                   for="category_for_kiosk">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="category_for_kiosk" name="category_for_kiosk"
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
