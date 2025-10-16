@extends('layouts.main-view')
@section('title', 'Create Table')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/our-locations/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 h-100px">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{route('settings')}}" class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0 m-1">{{ __('Settings') }} ></a>
                        <a href="{{route('ourLocation.index')}}" class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0 m-1">{{ __('Location') }} ></a>
                        <span class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Create location') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('ourLocation.index')}}"
                           class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{__('Discard')}}</a>
                        <button id="submitButton"
                                class="btn btn-primary w-125px border-0">{{__('Save')}}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_ourLocation_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Location Details') }}</h2>
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
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('Location')}}</h2> <span
                                                    class="small p-5 text-gray-900">{{ __('Enter location details') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row">
                                                    <div class="fv-row col-6">
                                                        <label for="name" class="required form-label fw-bold">{{__('Restaurant name')}}</label>
                                                        <input id="name" type="text" name="name"
                                                               class="form-control mb-2" placeholder="{{__('Restaurant name')}}"/>
                                                    </div>
                                                    <div class="fv-row col-6">
                                                        <label for="location" class="required form-label fw-bold">{{__('Location')}}</label>
                                                        <input id="location" type="text" name="location"
                                                               class="form-control mb-2" placeholder="{{__("Location")}}"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-2">
                                            <div class="card-header mb-3">
                                                <div class="card-title">
                                                    <h2>{{__('Module')}}</h2> <span
                                                    class="small p-5 text-gray-900">{{ __('Select module permissions for this location') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row">
                                                    <div class="fv-row mb-5">
                                                        <h6 class="">{{__('POS')}}</h6>
                                                        <div class="form-check form-switch mb-3">
                                                            <input class="form-check-input" name="pos" type="checkbox" role="checkbox" id="pos_1" value="1">
                                                            <label class="form-check-label" for="pos_1">{{__('Pos module is activated')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row mb-5">
                                                        <h6 class="">{{__('Kitchen')}}</h6>
                                                        <div class="form-check form-switch mb-3">
                                                            <input class="form-check-input" name="kitchen" type="checkbox" role="checkbox" id="kitchen_1" value="1">
                                                            <label class="form-check-label" for="kitchen_1">{{__('Kitchen module is activated')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row mb-10">
                                                        <h6 class="">{{__('e Kiosk')}}</h6>
                                                        <div class="form-check form-switch mb-3">
                                                            <input class="form-check-input" name="e_kiosk" type="checkbox" role="checkbox" id="e_kiosk_1" value="1">
                                                            <label class="form-check-label" for="e_kiosk_1">{{__('e Kiosk module is activated')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row mb-5">
                                                        <h6 class="">{{__('Integrated payment')}}</h6>
                                                        <div class="form-check form-switch mb-3">
                                                            <input class="form-check-input" name="integrated_payments" type="checkbox" role="checkbox" id="integrated_payments" value="1">
                                                            <label class="form-check-label" for="integrated_payments">{{__('Integrated payment is activated')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row mb-5">
                                                        <h6 class="">{{__('Manual payment')}}</h6>
                                                        <div class="form-check form-switch mb-3">
                                                            <input class="form-check-input" name="manual_payments" type="checkbox" role="checkbox" id="manual_payments" value="1">
                                                            <label class="form-check-label" for="manual_payments">{{__('Manual payment is activated')}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-2">
                                            <div class="card-header mb-3">
                                                <div class="card-title">
                                                    <h2>{{__('Other Settings')}}</h2> <span
                                                    class="small p-5 text-gray-900">{{ __('Configure other settings for this location') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-1 pb-0">
                                                <div class="row">
                                                    <h5 class="mb-4">{{__('Forms of delivery')}}</h5>
                                                    <div class="fv-row col-6 pt-2 w-100">
                                                        <div class="form-check form-switch  mb-7">
                                                            <input class="form-check-input check-taxes" name="dine_in" type="checkbox" role="checkbox" id="dine_in" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="dine_in">{{__('Dine In')}}</label>
                                                            <span class="small p-5 text-gray-900">{{ __('Automatically open the system keyboard as necessary for Dine-In orders') }}</span>
                                                        </div>
                                                        <div class="form-check form-switch  mb-7">
                                                            <input class="form-check-input" name="has_tables" type="checkbox" role="checkbox" id="has_tables" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="has_tables">{{__('Has tables')}}</label>
{{--                                                            <span class="small p-5 text-gray-900">{{ __('Automatically open the system keyboard as necessary for Dine-In orders') }}</span>--}}
                                                        </div>
                                                        <div class="form-check form-switch  mb-7">
                                                            <input class="form-check-input" name="has_locators" type="checkbox" role="checkbox" id="has_locators" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="has_locators">{{__('Has locators')}}</label>
{{--                                                            <span class="small p-5 text-gray-900">{{ __('Automatically open the system keyboard as necessary for Dine-In orders') }}</span>--}}
                                                        </div>
                                                        <div class="form-check form-switch mb-7">
                                                            <input class="form-check-input check-taxes" name="take_away" type="checkbox" role="checkbox" id="take_away" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="take_away">{{__('Take away')}}</label>
                                                            <span
                                                    class="small p-5 text-gray-900">{{ __('Automatically open system keyboard as necessary for Take away orders') }}</span>
                                                        </div>

                                                        <div class="form-check form-switch mb-7">
                                                            <input class="form-check-input check-taxes" name="delivery" type="checkbox" role="checkbox" id="delivery" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="delivery">{{__('Delivery')}}</label>
                                                            <span
                                                    class="small p-5 text-gray-900">{{ __('Automatically open the system keyboard as necessary for Delivery orders') }}</span>
                                                        </div>
                                                        <div class="form-check form-switch mb-7">
                                                            <input class="form-check-input" name="auto_print" type="checkbox" id="auto_print" role="checkbox" value="1">
                                                            <label class="form-check-label text-gray-800 fw-bold" for="auto_print">{{__('Auto print')}}</label>
{{--                                                            <span--}}
{{--                                                    class="small p-5 text-gray-900">{{ __('Automatically open the system keyboard as necessary for Delivery orders') }}</span>--}}
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

