@extends('layouts.main-view')
@section('title', 'Edit cash register')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/cash-registers/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('cashRegister.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Update cash register >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ $cashRegister->name }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('cashRegister.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_cashRegister_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="update">
                        <input type="hidden" id="page-id" value="{{ $cashRegister->id }}">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Cash register') }}</h2>
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
                                                    <h2>{{ __('General') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 col-6 fv-row">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Cash register name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            value="{{ $cashRegister->name }}" class="form-control mb-2"
                                                            placeholder="{{ __('name') }}" />
                                                    </div>
                                                    <div class="mb-10 col-6 fv-row">
                                                        <label for="hidden_key"
                                                               class="required form-label fw-bold">{{ __('Key') }}</label>
                                                        <input id="hidden_key" disabled type="text" name="key"
                                                               value="{{ $cashRegister->key }}" class="form-control mb-2"
                                                               placeholder="{{ __('Key') }}" />
                                                        <input type="hidden" id="key" name="key" value="{{ $cashRegister->key }}"/>
                                                    </div>
                                                    <div class="row fv-row">
                                                        <label
                                                            class="col required form-label fw-bold">{{ __('Description') }}</label>
                                                        <span
                                                            class="text-end col text-gray-900 fs-7">{{ __('Set a description to the unit for better visibility.') }}</span>
                                                        <textarea name="description" id="description" class="ms-3 form-control mb-3" rows="3"
                                                                  placeholder="{{ __('Type a message') }}">{{ $cashRegister->description }}</textarea>
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status*') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the cash register is deactivated, it cannot be used.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                   for="activated">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="activated" name="status"
                                                                   type="checkbox" role="checkbox" value="1" {{ $cashRegister->status ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 col-12 fv-row">
                                                        <label for="pin"
                                                            class="required form-label fw-bold">{{ __('Cash register authentication pin') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="pin" type="password" name="pin"
                                                            value="{{ $cashRegister->pin }}" class="form-control"
                                                            placeholder="{{ __('Cash register authentication pin') }}" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 col-12 fv-row">
                                                        <label for="pin_for_settings"
                                                               class="required form-label fw-bold">{{ __('Pin for payment terminal settings') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="pin_for_settings" type="password" name="pin_for_settings" value="{{ $cashRegister->pin_for_settings }}"
                                                               class="form-control mb-2" placeholder="{{ __('Pin for payment terminal settings') }}" />
                                                        <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 col-12 fv-row">
                                                        <label for="pin_to_print_reports"
                                                               class="required form-label fw-bold">{{ __('Pin to print reports') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="pin_to_print_reports" type="password" name="pin_to_print_reports" value="{{ $cashRegister->pin_to_print_reports }}"
                                                               class="form-control mb-2" placeholder="{{ __('Pin for payment terminal settings') }}" />
                                                        <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
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
