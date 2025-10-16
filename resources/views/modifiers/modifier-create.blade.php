@extends('layouts.main-view')
@section('title', 'Create Modifier')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/modifiers/add.js')
@endsection
@section('content')
    <script>
        window.units = {
            @foreach($units as $unit)
            '{{ $unit->name }}': '{{ $unit->suffix }}',
            @endforeach
        };
    </script>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('modifier.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Modifiers ') }}
                            ></a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add a new modifier') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('modifier.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_modifier_form" class="form d-flex flex-column flex-lg-row">
                        <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
{{--                            <div class="card card-flush py-4 pb-1">--}}
{{--                                <div class="card-header">--}}
{{--                                    <div class="card-toolbar w-100">--}}
{{--                                        <div class="row">--}}
{{--                                            <div class="col-3">--}}
{{--                                                <h3 id="colorDiv">{{ __('Color') }}</h3>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-9">--}}
{{--                                                <span id="infoSpanColor" class="small text-gray-900"--}}
{{--                                                      style="">{{ __('This modifier and its products will associate with this color') }}</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-3">--}}
{{--                                                <h3 id="iconDiv">{{ __('Image') }}</h3>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-9">--}}
{{--                                                <span id="infoSpanDiv" class="small text-gray-900"--}}
{{--                                                      style="display:none;">{{ __('This modifier and its products will associate with this image') }}</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bold w-100 "--}}
{{--                                            id="kt_security_summary_tabs">--}}
{{--                                            <li class="nav-item w-50 border-bottom border-3">--}}
{{--                                                <a class="justify-content-center m-0 w-100 nav-link text-active-primary active"--}}
{{--                                                   data-kt-countup-tabs="true" data-bs-toggle="tab"--}}
{{--                                                   href="#kt_security_summary_tab_pane_hours">{{ __('Color') }}</a>--}}
{{--                                            </li>--}}
{{--                                            <li class="nav-item w-50 border-bottom border-3">--}}
{{--                                                <a class="justify-content-center m-0 w-100 nav-link text-active-primary"--}}
{{--                                                   data-kt-countup-tabs="true" data-bs-toggle="tab"--}}
{{--                                                   id="kt_security_summary_tab_day"--}}
{{--                                                   href="#kt_security_summary_tab_pane_day">{{ __('Image') }}</a>--}}
{{--                                            </li>--}}
{{--                                        </ul>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="card-body pt-0">--}}
{{--                                    <div class="card-body pt-7 pb-0 px-0">--}}
{{--                                        <div class="tab-content">--}}
{{--                                            <div class="tab-pane fade active show"--}}
{{--                                                 id="kt_security_summary_tab_pane_hours"--}}
{{--                                                 role="tabpanel">--}}
{{--                                                <div class="row ms-0 mb-7">--}}
{{--                                                    <div class="col-2 h-45px w-45px card categoryColor"--}}
{{--                                                         style="background-color: #ffff"></div>--}}
{{--                                                    <div class="col-10">--}}
{{--                                                        <span--}}
{{--                                                            class="small fw-bold">{{ __('The products of this category will associate with this color') }}</span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <span>{{ __('Note: You cannot change the color of the product. This color will be predefined in the food category.') }}</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="tab-pane fade" id="kt_security_summary_tab_pane_day"--}}
{{--                                                 role="tabpanel">--}}
{{--                                                <div class="image-input w-100 mb-3" data-kt-image-input="true">--}}
{{--                                                    <label class="w-100 mb-4" title="Change image">--}}
{{--                                                        <div--}}
{{--                                                            class="notice cursor-pointer d-flex bg-light-primary rounded border-primary border border-dashed">--}}
{{--                                                            <i--}}
{{--                                                                class="ki-duotone ki-svg/files/upload.svg fs-2tx text-primary me-4"></i>--}}
{{--                                                            <div class="d-flex flex-stack flex-grow-1">--}}
{{--                                                                <div class="fw-semibold py-2">--}}
{{--                                                                    <div class="fw-bold">{{ __('Quick file uploader') }}--}}
{{--                                                                    </div>--}}
{{--                                                                    <div class=" fs-6 text-gray-700">--}}
{{--                                                                        {{ __('Drag & Drop or choose files from computer') }}--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                        <input class="d-none" type="file" name="image"--}}
{{--                                                        accept=".png, .svg, .webp, .jpg, .jpeg" />--}}
{{--                                                        <input type="hidden" name="image_remove" />--}}
{{--                                                    </label>--}}
{{--                                                    <div class="d-flex justify-content-center">--}}
{{--                                                        <div class="image-input-wrapper"></div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="text-muted fs-7">{{ __('Recommended size') }}:1080x1080--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="card card-flush py-4">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{ __('Modifier Details') }}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="fv-row">
                                        <div class="row">
                                            <label
                                                class="col required form-label fw-bold">{{ __('Categories') }}</label>
                                            <span
                                                class="text-end col text-gray-900 fs-7">{{ __('Add modifier to a category.') }}</span>
                                        </div>
                                        <select id="categories" multiple="multiple" name="categories[]" class="form-select mb-2"
                                                data-control="select2" data-placeholder="{{ __('Select an option') }}"
                                                data-allow-clear="true">
                                            <option></option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <a target="_blank" href="{{ route('foodCategory.create') }}"
                                       class="btn-light-primary btn-sm mb-10">
                                        <i class="ki-duotone ki-plus fs-5"></i>{{ __('Add new category') }}</a>
                                </div>
                            </div>
                            <div class="card card-flush py-4 p-0">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{ __('Modifier status') }}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <div class="fv-row">
                                        <div class="row">
                                            <label class="col required form-label fw-bold">{{ __('Status') }}</label>
                                            <span
                                                class="text-end col text-gray-900 fs-7">{{ __('Set the modifier status.') }}</span>
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
                                                            class="col required form-label fw-bold">{{ __('Modifier Name') }}</label>
                                                        <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('A modifier name is required and recommended to be unique.') }}</span>
                                                    </div>
                                                    <input type="text" name="title" class="form-control mb-2"
                                                           placeholder="{{ __('Modifier title') }}" value=""/>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-1">
                                                <div class="mb-10">
                                                    <div class="row">
                                                        <label class="col required form-label fw-bold"
                                                               for="description">{{ __('Description') }}</label> <span
                                                            class="col text-end text-gray-900 fs-7">{{ __('Set a description to the modifier for better visibility.') }}</span>
                                                    </div>
                                                    <textarea name="description" class="form-control mb-3" rows="4"
                                                              placeholder="{{ __('Type a message') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4 fv-row pb-0">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Ingredients') }}</h2>
                                                </div>
                                                <div
                                                    class="text-right d-flex align-items-center position-relative my-1 w-400px">
                                                    <select class="form-select mb-2" id="ingredientDropdown"
                                                            data-control="select2"
                                                            data-placeholder="{{ __('Search Ingredients by name or ingredient ID') }}"
                                                            data-allow-clear="true">
                                                        <option></option>
                                                        @foreach ($ingredients as $ingredient)
                                                            <option value="{{ $ingredient }}">{{ $ingredient->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="fv-row mb-2">
                                                    <div class="fv-row">
                                                        <input type="hidden" id="selected_ingredients"
                                                               name="selected_ingredients">
                                                    </div>
                                                    <div class="card card-body pt-0 px-2 pb-0">
                                                        <table class="table align-middle table-row-dashed gy-5"
                                                               id="kt_customers_table">
                                                            <thead>
                                                            <tr class="text-start text-gray-900 fs-7 fw-bold gs-0">
                                                                <th class="w-33">{{ __('Ingredient name') }}</th>
                                                                <th>{{ __('In stock') }}</th>
                                                                <th class="w-150px">{{ __('Qty') }}</th>
                                                                <th class="w-150px">{{ __('Units') }}</th>
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
                                                                   placeholder="{{ __('Product cost') }}"/>
                                                        </div>
                                                        <div class="mb-10 fv-row col">
                                                            <label for="price"
                                                                   class="mt-4 required form-label">{{ __('Price') }}
                                                                ({{ $settings->currency_symbol }})</label>
                                                            <input id="price" autocomplete="off" type="text" name="price"
                                                                   class="form-control mb-2"
                                                                   placeholder="{{ __('Product price') }}"/>
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
