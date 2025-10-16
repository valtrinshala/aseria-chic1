@extends('layouts.main-view')
@section('title', 'Create Meal')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/meals/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('meal.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">
                            {{ __('Deals') }}> </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add a new deal') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('meal.index') }}"
                           class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_meal_form" class="form d-flex flex-column flex-lg-row">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="card card-flush py-4 pb-1">
                                <div class="card-header">
                                    <div class="card-toolbar w-100">
                                        <div class="row">
                                            <div class="col-3">
                                                <h3 id="colorDiv">{{ __('Color') }}</h3>
                                            </div>
                                            <div class="col-9">
                                                <span id="infoSpanColor" class="small text-gray-900"
                                                      style="">{{ __('This modifier and its products will associate with this color') }}</span>
                                            </div>
                                            <div class="col-3">
                                                <h3 id="iconDiv">{{ __('Image') }}</h3>
                                            </div>
                                            <div class="col-9">
                                                <span id="infoSpan" class="small text-gray-900"
                                                      style="display:none;">{{ __('This modifier and its products will associate with this image') }}</span>
                                            </div>
                                        </div>
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bold w-100 "
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
                                                   href="#kt_security_summary_tab_pane_day">{{ __('Image') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="card-body pt-7 pb-0 px-0">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show"
                                                 id="kt_security_summary_tab_pane_hours"
                                                 role="tabpanel">
                                                <div class="row ms-0 mb-7">
                                                    <div class="col-2 h-45px w-45px card categoryColor"
                                                         style="background-color: {{ $category->color }}"></div>
                                                    <div class="col-10">
                                                        <span
                                                            class="small fw-bold">{{ __('The deals of this category will associate with this color') }}</span>
                                                    </div>
                                                </div>
                                                <span>{{ __('Note: You cannot change the color of the deal. This color will be predefined in the food category.') }}</span>
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
                                                <div class="text-muted fs-7">{{ __('Recommended size') }}:1080x1080
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{ __('Deal Details') }}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="fv-row">
                                        <div class="row">
                                            <label
                                                class="col required form-label fw-bold">{{ __('Categories') }}</label>
                                            <span
                                                class="text-end col text-gray-900 fs-7">{{ __('Add deal to a category.') }}</span>
                                        </div>
                                        <select id="categories" name="categories" class="form-select mb-2"
                                                data-control="select2" data-placeholder="{{ __('Select an option') }}"
                                                data-allow-clear="false">
                                            <option value="{{ $category->id }}"
                                                    data-color="{{ $category->color }}">{{ $category->name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{ __('Deal Tax') }}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="fv-row">
                                        <div class="row">
                                            <label for="tax_id"
                                                   class="col required form-label fw-bold">{{ __('Taxes') }}</label>
                                            <span
                                                class="text-end col text-gray-900 fs-7">{{ __('Add tax to a deal.') }}</span>
                                        </div>
                                        <select id="tax_id" name="tax_id" class="form-select mb-2"
                                                data-control="select2" data-placeholder="{{ __('Select an option') }}"
                                                data-allow-clear="true">
                                            <option></option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a target="_blank" href="{{ route('tax.create') }}"
                                       class="btn-light-primary btn-sm mb-10">
                                        <i class="ki-duotone ki-plus fs-5"></i>{{ __('Add new tax') }}</a>
                                </div>
                            </div>
                            <div class="card card-flush py-4 p-0">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{ __('Deal status') }}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="fv-row">
                                        <div class="row">
                                            <label class="col required form-label fw-bold">{{ __('Status') }}</label>
                                            <span
                                                class="text-end col text-gray-900 fs-7">{{ __('Set the deal status.') }}</span>
                                        </div>
                                    </div>

                                    <select name="status" class="form-select mb-2" data-control="select2"
                                            data-hide-search="true" data-placeholder="{{ __('Select an option') }}"
                                            id="kt_ecommerce_add_product_status_select">
                                        <option></option>
                                        <option value="1" selected="selected">{{ __('Published') }}</option>
                                        <option value="0">{{ __('Inactive') }}</option>
                                    </select>
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
                                                            class="col required form-label fw-bold">{{ __('Deal Name') }}</label>
                                                        <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('A deal name is required and recommended to be unique.') }}</span>
                                                    </div>
                                                    <input type="text" name="name" class="form-control mb-2"
                                                           placeholder="{{ __('Deal name') }}" value=""/>
                                                </div>

                                            </div>
                                            <div class="card-body pt-0 pb-1">
                                                <div class="mb-10">
                                                    <div class="row">
                                                        <label class="col required form-label fw-bold"
                                                               for="description">{{ __('Description') }}</label> <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('Set a description to the category for better visibility.') }}</span>
                                                    </div>
                                                    <textarea name="description" class="form-control mb-3" rows="4"
                                                              placeholder="{{ __('Type a message') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4 fv-row pb-0">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Products') }}</h2>
                                                </div>
                                                <div
                                                    class="text-right d-flex align-items-center position-relative my-1 w-400px">
                                                    <select class="form-select mb-2" id="foodItemDropdown"
                                                            data-control="select2"
                                                            data-placeholder="{{ __('Search products by name or product ID') }}"
                                                            data-allow-clear="true">
                                                        <option></option>
                                                        @foreach ($foodItems as $foodItem)
                                                            <option value="{{ $foodItem }}">{{ $foodItem->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="fv-row mb-2">
                                                    <div class="fv-row">
                                                        <input type="hidden" id="selected_foodItems"
                                                               name="selected_foodItems">
                                                    </div>
                                                    <div class="card card-body pt-0 px-2 pb-0">
                                                        <table class="table align-middle table-row-dashed gy-5"
                                                               id="kt_customers_table">
                                                            <thead>
                                                            <tr class="text-start text-gray-900 fs-7 fw-bold gs-0">
                                                                <th class="w-50">{{ __('Product name') }}</th>
                                                                <th>{{ __('Quantity') }}</th>
                                                                <th>{{ __('Cost') }}</th>
                                                                <th>{{ __('Price') }}</th>
                                                                <th class="text-end w-5px">{{ __('Actions') }}
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>

                                                    </div>
                                                    <div class="row">
                                                        <div class="mb-10 fv-row col">
                                                            <label for="cost"
                                                                   data-currency="{{ $settings->currency_symbol }}"
                                                                   data-direction="{{ $settings->currency_symbol_on_left ? 'start' : 'end' }}"
                                                                   class="mt-4 required form-label">{{ __('Cost') }}
                                                                ({{ $settings->currency_symbol }})</label>
                                                            <input id="cost" type="text" autocomplete="off" name="cost"
                                                                   class="form-control mb-2"
                                                                   placeholder="{{ __('Deal cost') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col">
                                                            <label for="price"
                                                                   class="mt-4 required form-label">{{ __('Price') }}
                                                                ({{ $settings->currency_symbol }})</label>
                                                            <input id="price" type="text" autocomplete="off" name="price"
                                                                   class="form-control mb-2"
                                                                   placeholder="{{ __('Deal price') }}"/>
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
