@extends('layouts.main-view')
@section('title', 'Create pos')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/pos/admin/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('devices-pos.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Pos >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new pos') }}</span>

                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('devices-pos.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton"
                            class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_pos_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Device') }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="requestId" name="requestId" value="{{ $posIncomingRequest?->id }}">
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Pos  Details') }}</h2> <span
                                                        class="small p-5 text-gray-900">{{ __('This information will be displayed publicly.') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-4">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('Pos name') }}</label>
                                                        <input id="name" type="text" name="name" value="{{ $posIncomingRequest?->pos_name }}"
                                                            class="form-control mb-2" placeholder="Ex. Pos 1" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="pos_id"
                                                            class="required form-label fw-bold">{{ __('Pos ID') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="pos_id" type="password" name="pos_id" @if($posIncomingRequest) disabled @endif
                                                            class="form-control text-gray-900 disabled" value="{{ $posIncomingRequest?->device_id }}"
                                                            placeholder="Unique ID" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                        @if($posIncomingRequest)
                                                        <input id="pos_id" type="hidden" name="pos_id"
                                                            class="form-control mb-2 text-gray-900 disabled" value="{{ $posIncomingRequest?->device_id }}"
                                                            placeholder="Unique ID" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="authentication_code"
                                                            class="required form-label fw-bold">{{ __('Authentication Code') }}
                                                        </label>
                                                        <div class="shared-input-container mb-2">
                                                            <input id="authentication_code" type="password"
                                                                name="authentication_code"
                                                                class="form-control"
                                                                   value="{{ $posIncomingRequest?->authentication_code }}"
                                                                placeholder="Write password here" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="dropdown"
                                                            class="required form-label fw-bold">{{ __('Location') }}
                                                        </label>
                                                            <select name="location_id" class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="{{ __('Select an option') }}">
                                                                <option></option>
                                                                @foreach (\App\Helpers\Helpers::locations() as $eachLocation)
                                                                    <option value="{{ $eachLocation->id }}">{{ $eachLocation->name }}</option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="authentication_code"
                                                               class="required form-label fw-bold">{{ __('Pin for payment terminal settings') }}
                                                        </label>
                                                        <div class="shared-input-container mb-2">
                                                            <input id="authentication_code" type="text"
                                                                   name="pin_for_settings"
                                                                   class="form-control"
                                                                   placeholder="Write your pin here" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6 ps-8">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the pos is deactivated, it cannot be used.') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="activated">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="activated" name="status"
                                                                type="checkbox" role="checkbox" checked value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="row fv-row">
                                                        <label
                                                            for="description"
                                                            class="col-5 required form-label fw-bold">{{ __('Description') }}</label>
                                                        <span
                                                            class="text-end col-7 text-gray-900 fs-7 pe-0">{{ __('Set a description to the unit for better visibility.') }}</span>
                                                        <textarea name="description" id="description" class="ms-3 form-control mb-3" rows="3"
                                                                  placeholder="{{ __('Type a message') }}">{{ $posIncomingRequest?->description }}</textarea>
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
