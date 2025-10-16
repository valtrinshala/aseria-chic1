@extends('layouts.main-view')
@section('title', 'Create User')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/users/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('user.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Users >') }}</a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Add new user') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('user.index') }}"
                            class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_user_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="create">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('User') }}</h2>
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
                                                    <h2>{{ __('User Details') }}</h2><span
                                                        class="small p-6 text-gray-900">{{ __('This information will be displayed publicly.') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label for="name"
                                                            class="required form-label fw-bold">{{ __('First name and Last name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                            class="form-control mb-2" placeholder="Ex. John Doe" />
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label for="email"
                                                            class="required form-label fw-bold">{{ __('Email address') }}</label>
                                                        <input id="email" type="email" name="email"
                                                            class="form-control mb-2" placeholder="Ex. johndoe@gmail.com" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="password"
                                                            class="required form-label fw-bold">{{ __('Password') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="password" type="password" name="password"
                                                            class="form-control" placeholder="{{__('Write password here')}}" />
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <label for="pin"
                                                               class="form-label fw-bold">{{ __('Pin') }}</label>
                                                        <div class="shared-input-container mb-2">
                                                        <input id="pin" type="password" name="pin"
                                                               class="form-control" placeholder="{{ __('Write password here') }}"/>
                                                            <span class="toggle-password">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="fv-row col-6">
                                                    <label for="location_id" class="required form-label fw-bold">{{ __('Locations') }}</label>
                                                    <select id="location_id" name="location_id" class="form-select"
                                                            data-control="select2"
                                                            data-placeholder="{{ __('Select an location') }}"
                                                            data-allow-clear="false">
                                                        @foreach (\App\Helpers\Helpers::locations() as $location)
                                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('User Settings') }}</h2> <span
                                                        class="small p-6 text-gray-900">{{ __('User access and permission settings.') }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col">
                                                        <label
                                                            class="form-label fw-bold required">{{ __('Role') }}</label>
                                                        <select id="roles" name="role_id" class="form-select mb-2"
                                                            data-control="select2" data-placeholder={{__("Select a role option")}}
                                                            data-allow-clear="true">
                                                            <option></option>
                                                            @foreach ($userRoles as $userRole)
                                                                <option value="{{ $userRole->id }}">{{ $userRole->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-10 fv-row col">
                                                        <label class="form-check-label mb-4" for="activated"><span
                                                                class="fw-bold text-gray-900">{{ __('Status*') }}</span>
                                                            <span
                                                                class="small p-5 text-muted">{{ __('When the user is deactivated, the registry is created in the system, but can not login until it is activated again') }}</span></label>
                                                        <div class="form-check form-switch">
                                                            <label class="form-check-label mb-4"
                                                                for="activated">{{ __('Activated') }}</label>
                                                            <input class="form-check-input" id="activated" name="status"
                                                                type="checkbox" role="checkbox" checked>
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
