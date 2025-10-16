@extends('layouts.main-view')
@section('title', 'General Setting')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/units/add.js')
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
                        <a href="{{ route('unit.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0 m-4">{{ __('Unit') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fs-3 flex-column justify-content-center fw-bold my-0 m-3">{{ __('Add new Unit') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        @include('settings.goback-button')
                        <button id="submitButton"
                                class="btn btn-primary w-125px border-0">{{__('Save')}}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_unit_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('Unit')}}</h2>
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
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('General')}}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row">
                                                        <label for="name" class="required form-label fw-bold">{{__('Name')}}</label>
                                                        <input id="name" type="text" name="name"
                                                               class="form-control mb-2" placeholder="{{__('Name')}}"/>
                                                    </div>

                                                    <div class="mb-10 fv-row">
                                                        <label for="suffix" class="required form-label fw-bold">{{__('Suffix')}}</label>
                                                        <input id="suffix" type="text" name="suffix"
                                                               class="form-control mb-2" placeholder="{{__('Suffix')}}"/>
                                                    </div>
                                                    <div class="row fv-row">
                                                        <label
                                                            class="col required form-label fw-bold">{{ __('Description') }}</label>
                                                        <span
                                                            class="text-end col text-gray-900 fs-7">{{ __('Set a description to the unit for better visibility.') }}</span>
                                                        <textarea name="description" id="description" class="ms-3 form-control mb-3" rows="3"
                                                                  placeholder="{{ __('Type a message') }}"></textarea>
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
