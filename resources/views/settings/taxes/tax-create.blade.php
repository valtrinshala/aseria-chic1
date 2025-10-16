@extends('layouts.main-view')
@section('title', 'Create Table')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/taxes/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('settings') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }}
                            > </a>
                        <a href="{{ route('tax.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0 m-4">{{ __('Tax') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fs-3 flex-column justify-content-center fw-bold my-0 m-3">{{ __('Add new Tax Detail') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('tax.index') }}"
                            class="btn fw-bold btn-light btn-flex btn-center btn-white w-125px justify-content-center">{{ __('Discard') }}</a>
                        <button id="submitButton"
                            class="btn fw-bold btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_tax_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Tax Details') }}</h2>
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
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Tax details') }}</h2> <span
                                                        class="small p-5 text-gray-500">{{ __('Enter tax details') }}</span>
                                                </div>
                                            </div>

                                            <div class="card-body pt-0">
                                                <div class="row">
                                                    <div class="fv-row col-6">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2" placeholder="{{ __('Name') }}" />
                                                    </div>
                                                    @php $type =  request()->query->get('type') @endphp
                                                    <div class="fv-row col-6">
                                                        <label for="dropdown"
                                                            class="required form-label fw-bold">{{ __('Type') }}</label>
                                                        <select id="dropdown" name="type" data-control="select2" class="form-control mb-2">
                                                            <option>&nbsp;</option>
                                                            <option @selected($type == "dine_in") value="dine_in">{{ __('Dine in') }}</option>
                                                            <option @selected($type == "take_away") value="take_away">{{ __('Take away') }}</option>
                                                            <option @selected($type == "delivery") value="delivery">{{ __('Delivery') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="fv-row col-4">
                                                        <label for="tax_rate"
                                                            class="required form-label fw-bold">{{ __('Tax rate') }}</label>
                                                        <input id="tax_rate" type="number" step="0.01" name="tax_rate"
                                                            class="form-control mb-2" placeholder="{{ __('Tax rate') }}" />
                                                    </div>
                                                    <div class="fv-row col-4">
                                                        <label for="tax_id"
                                                            class="required form-label fw-bold">{{ __('Tax ID') }}</label>
                                                        <input id="tax_id" type="text" name="tax_id"
                                                            class="form-control mb-2" placeholder="{{__("Tax ID")}}" />
                                                    </div>
                                                    <div class="fv-row col-4">
                                                        <label for="description"
                                                            class="required form-label fw-bold">{{ __('Tax Description') }}</label>
                                                        <input id="description" type="text" name="description"
                                                            class="form-control mb-2" placeholder="{{__("Description")}}" />
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-flush py-2">
                                            <div class="card-header mb-3">
                                                <div class="card-title row w-100">
                                                    <div class="col-10 p-0">
                                                        <h2 class="d-inline">{{ __('Other Settings') }}</h2>
                                                        <span
                                                            class="small text-gray-800 ms-5">{{ __('Configure other settings for tax details') }}</span>
                                                    </div>
                                                    <div class="col-2 d-flex justify-content-end align-items-center">
                                                            <span class="text-gray-900 text-end small fw-bold">
                                                                {{ __('Deactivate') }}
                                                            </span>
                                                        <div class="form-switch p-0 ps-3">
                                                            <input class="form-check-input m-0" id="activated"
                                                                   name="size-status" type="checkbox" role="checkbox">
                                                        </div>
                                                        <span class="text-gray-900 text-end ps-2 small fw-bold">
                                                                {{ __('Activate') }}
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 d-none activate-other-settings">
                                                <div class="row">
                                                    <div class="fv-row col-6">
                                                        <div class="mb-8">
                                                            <span class="fw-bolder">{{ __('Is VAT calculation') }}</span>
                                                            <div class="form-check form-switch mt-5 mb-9">
                                                                <input class="form-check-input" name="tax_calculation"
                                                                    type="checkbox" role="checkbox" id="im_ex1"
                                                                    value="1">
                                                                <label
                                                                    for="im_ex1">{{ __('Basic tax calculation will be implemented') }}.</label>
                                                                <span
                                                                    class="ms-3 text-gray-600">{{ __('(This will control tax calculation implementation)') }}</span>
                                                            </div>
                                                            <span
                                                                class="mb-4 fw-bolder">{{ __('Is tax included') }}</span>
                                                            <div class="form-check form-switch mt-5 mb-9">
                                                                <input class="form-check-input" name="tax_included"
                                                                    type="checkbox" role="checkbox" id="im_ex2"
                                                                    value="1">
                                                                <label
                                                                    for="im_ex2">{{ __('The tax amount will be excluded and added') }}.</label>
                                                                <span
                                                                    class="ms-3 text-gray-600">{{ __('(This will control how tax can be add to total amount)') }}</span>
                                                            </div>
                                                            <span
                                                                class="fw-bolder mb-4">{{ __('Is fix or percentage?') }}</span>
                                                            <div class="form-check form-switch mt-5 mb-9">
                                                                <input class="form-check-input" name="tax_fix_percentage"
                                                                    type="checkbox" role="checkbox" id="im_ex3"
                                                                    value="1">
                                                                <label
                                                                    for="im_ex3">{{ __('The percentage of amount will be added') }}.</label>
                                                                <span
                                                                    class="ms-3 text-gray-600">{{ __('(This will control tax implementation could be percentage or fixed)') }}</span>
                                                            </div>
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
