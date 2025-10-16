@extends('layouts.main-view')
@section('title', 'Invoice Printing')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/settings/invoice-printers.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('settings') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Invoice Printing') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('settings') }}"
                           class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_invoice_printers" class="form d-flex flex-column flex-lg-row">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-550px mb-7 me-lg-7">
                            <div class="card card-flush py-4">
                                <div class="card-toolbar w-100 card-header">
                                    <div class="row">
                                        <div class="col-3 d-none">
                                            <h3 id="iconDiv" style="">Image</h3>
                                        </div>
                                        <div class="col-9 d-none">
                                            <span id="infoSpan" class="small text-gray-900" style="">This modifier and its products will associate with this image</span>
                                        </div>
                                    </div>
                                    <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bold w-100"
                                        id="kt_security_summary_tabs" role="tablist">
                                        <li class="nav-item w-100 border-bottom border-3" role="presentation">
                                            <a class="justify-content-center m-0 w-100 nav-link text-active-primary active"
                                               data-kt-countup-tabs="true" data-bs-toggle="tab"
                                               id="kt_security_summary_tab_day"
                                               href="#kt_security_summary_tab_pane_day" data-kt-initialized="1"
                                               aria-selected="true" role="tab">{{ __('Icon') }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="card-body pt-7 pb-0 px-0">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="kt_security_summary_tab_pane_day"
                                                 role="tabpanel" aria-labelledby="kt_security_summary_tab_day">
                                                <div class="image-input w-100 mb-3" data-kt-image-input="true">
                                                    <label class="w-100 mb-4" title="Change image">
                                                        <div
                                                            class="notice cursor-pointer d-flex bg-light-primary rounded border-primary border border-dashed">
                                                            <i class="ki-duotone ki-svg/files/upload.svg fs-2tx text-primary me-4"></i>
                                                            <div class="d-flex flex-stack flex-grow-1">
                                                                <div class="fw-semibold py-2">
                                                                    <div class="fw-bold">Quick file uploader
                                                                    </div>
                                                                    <div class=" fs-6 text-gray-700">
                                                                        Drag &amp; Drop or
                                                                        choose
                                                                        files
                                                                        from computer
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input class="d-none" type="file" name="image"
                                                               accept=".png, .svg, .webp, .jpg, .jpeg">
                                                        <input type="hidden" name="image_remove">
                                                    </label>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="image-input-wrapper"
                                                             style="background-image: url('{{ Storage::disk('public')->url($invoicePrinting->logo_header) }}')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-muted fs-7">{{__('Recommended size:1080x1080')}}
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
                                                    <h2>{{ __('General') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-1">
                                                <div class="mb-10 fv-row">
                                                    <div class="row">

                                                        <div class="mb-10 fv-row col-6"><label for="print_name_address_position"
                                                                                               class="required form-label fw-bold">{{ __('Print receipt header position') }}</label>
                                                            <select id="print_name_address_position" name="print_name_address_position"
                                                                    class="form-control mb-2"
                                                                    data-control="select2"
                                                                    data-placeholder="{{ __('Select an option') }}"
                                                                    data-allow-clear="true">
                                                                <option @selected($invoicePrinting->print_name_address_position == 'center') value="center">{{__('Center')}}</option>
                                                                <option @selected($invoicePrinting->print_name_address_position == 'left') value="left">{{__('Left')}}</option>
                                                                <option @selected($invoicePrinting->print_name_address_position == 'right') value="right">{{__('Right')}}</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-10 fv-row col-6">
                                                            <label for="print_header_footer_font_size"
                                                                   class="required form-label fw-bold">{{ __('Print header footer font size(px)') }}</label>
                                                            <input id="print_header_footer_font_size" type="number" name="print_header_footer_font_size"
                                                                   class="form-control mb-2" value="{{ $invoicePrinting->print_header_footer_font_size }}"
                                                                   placeholder="{{ __('Print header footer font size(px)') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col-6">
                                                            <label for="print_items_font_size"
                                                                   class="required form-label fw-bold">{{ __('Print items font size(px)') }}</label>
                                                            <input id="print_items_font_size" type="number"
                                                                   name="print_items_font_size"
                                                                   class="form-control mb-2" value="{{ $invoicePrinting->print_items_font_size }}"
                                                                   placeholder="{{ __('Print items font size(px)') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col-6">
                                                            <label for="print_width"
                                                                   class="required form-label fw-bold">{{ __('Printer width(mm)') }}</label>
                                                            <input id="print_width" type="number" name="print_width"
                                                                   class="form-control mb-2" value="{{ $invoicePrinting->print_width }}"
                                                                   placeholder="{{ __('Printer width(mm)') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col-6">
                                                            <label for="logo_height"
                                                                   class="required form-label fw-bold">{{ __('Logo Height(px)') }}</label>
                                                            <input id="logo_height" type="number" name="logo_height"
                                                                   class="form-control mb-2" value="{{ $invoicePrinting->logo_height }}"
                                                                   placeholder="{{ __('Printer width(px)') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col-6">
                                                            <label for="invoice_type_title"
                                                                   class="required form-label fw-bold">{{ __('Invoice type title') }}</label>
                                                            <input id="invoice_type_title" type="text" name="invoice_type_title"
                                                                   class="form-control mb-2" value="{{ $invoicePrinting->invoice_type_title }}"
                                                                   placeholder="{{ __('Invoice type title') }}"/>
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
