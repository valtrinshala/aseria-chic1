@extends('layouts.main-view')
@section('title', 'Create Customer')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/customers/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3 m-5">
                        <a href="{{ route('customer.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Customer >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new customer') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('customer.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton"
                            class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_customer_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Customer') }}</h2>
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
                                        <div class="card card-flush py-4 mt-0 p-0">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Customer details') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2" placeholder="Ex. John Doe" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="email"
                                                            class="required form-label fw-bold">{{ __('Email') }}</label>
                                                        <input id="email" type="email" name="email"
                                                            class="form-control mb-2" placeholder="Ex. johndoe@gmail.com" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="phone"
                                                            class="required form-label fw-bold">{{ __('Phone') }}</label>
                                                        <input id="phone" type="number" name="phone"
                                                            class="form-control mb-2" placeholder="{{__("Phone number")}}" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="address"
                                                            class="required form-label fw-bold">{{ __('Address') }}</label>
                                                        <input id="address" type="text" name="address"
                                                            class="form-control mb-2" placeholder="{{__("Address")}}" />
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
