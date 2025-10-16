@extends('layouts.main-view')
@section('title', 'Create Table')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/payment-methods/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('paymentMethod.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Payment Method') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new payment method') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('paymentMethod.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_paymentMethod_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <input type="hidden" value="#5D4BDF" name="color" class="input-color">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-toolbar w-100">
                                        <div>
                                            <h3 id="colorDiv">{{ __('Color') }}</h3>
                                            <span id="infoSpanColor" class="small p-5 text-gray-900"
                                                style="display:none;">{{ __('This category and its products will associate with this color') }}</span>
                                            <h3 id="iconDiv">{{ __('Icon') }}</h3>
                                            <span id="infoSpanDiv" class="small p-5 text-gray-900"
                                                style="display:none;">{{ __('This category and its products will associate with this icon') }}</span>
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
                                                    <div class="col-7 mx-4 card h-250px paymentMethod-color"
                                                        style="background-color:#f44336"></div>
                                                    <div class="col-4 h-250px">
                                                        @foreach ($colors as $color)
                                                            <div class="row paymentCategories">
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
                                                    <label class="w-100 mb-4" title="Change image">
                                                        <div
                                                            class="notice cursor-pointer d-flex bg-light-primary rounded border-primary border border-dashed">
                                                            <i
                                                                class="ki-duotone ki-svg/files/upload.svg fs-2tx text-primary me-4"></i>
                                                            <div class="d-flex flex-stack flex-grow-1">
                                                                <div class="fw-semibold py-2">
                                                                    <div class="fw-bold">
                                                                        {{ __('Quick file uploader') }}
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
                                                <div class="text-muted fs-7">{{ __('Recommended size') }}
                                                    :1080x1080</div>
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
                                                    <h2>{{ __('Payment Method') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-5 fv-row col">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2" placeholder="{{__("Payment Method")}}" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mt-5 col">
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
