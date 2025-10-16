@extends('layouts.main-view')
@section('title', 'Create Ingredient')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/ingredients/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('ingredient.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Ingredients >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add a new ingredient') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('ingredient.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_ingredient_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Name') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0 ">
                                                <div class="mb-10 fv-row">
                                                    <label
                                                        class="required form-label fw-bold">{{ __('Ingredient name') }}</label>
                                                    <input type="text" name="name" class="form-control mb-2"
                                                        placeholder="{{__("Ingredient name")}}" value="" />
                                                    <div class="text-gray-900 fs-7">
                                                        {{ __('A ingredient name is required and recommended to be unique.') }}
                                                    </div>
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
                                        <div class="card card-flush py-4 pb-0">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('General') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body py-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="cost"
                                                        class="required form-label fw-bold">{{ __('Cost') }}
                                                        ({{($settings->currency_symbol)}})</label>
                                                        <input id="cost" type="text" autocomplete="off" step="0.01" name="cost"
                                                        class="form-control mb-2" placeholder="{{__('Ingredient cost')}}" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="price"
                                                        class="required form-label fw-bold">{{ __('Price') }}
                                                        ({{$settings->currency_symbol}})</label>
                                                    <input id="price" type="text" autocomplete="off" step="0.01" name="price"
                                                        class="form-control mb-2" placeholder="{{__("Ingredient price")}}" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="unit" class="required form-label fw-bold">{{ __('Unit') }}</label>
                                                        <select name="unit" class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="{{ __('Select an option') }}" id="kt_ecommerce_add_product_status_select">
                                                            <option></option>
                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->suffix }}">{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="quantity"
                                                            class="required form-label fw-bold">{{ __('Available Quantity') }}</label>
                                                        <input id="quantity" type="text" autocomplete="off" name="quantity"
                                                            class="form-control mb-2"
                                                            placeholder="{{__("Ingredient available quantity")}}" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="alert_quantity"
                                                            class="required form-label fw-bold">{{ __('Quantity alert') }}</label>
                                                        <input id="alert_quantity" type="text" autocomplete="off" name="alert_quantity"
                                                            class="form-control mb-2"
                                                            placeholder={{__("Ingredient quantity alert")}} />
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
