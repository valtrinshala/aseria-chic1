@extends('layouts.main-view')
@section('title', 'Edit User role')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/user-roles/add.js')
@endsection
@section('content')
    @php $permissions = json_decode(file_get_contents(base_path('resources/permissions/permissions.json')))->permissions @endphp
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('userRole.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Roles') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ $userRole->name }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('userRole.index') }}"
                           class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_userRole_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="update">
                        <input type="hidden" id="page-id" value="{{ $userRole->id }}">
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-7">
                                        <div class="card card-flush py-1">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('Role Details') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row">
                                                    <div class="fv-row col-6">
                                                        <label for="name"
                                                               class="required form-label fw-bold">{{ __('Name') }}</label>
                                                        <input id="name" type="text" name="name"
                                                               value="{{ $userRole->name }}" class="form-control mb-2"
                                                               placeholder="Ex. John Doe"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-flush py-2">
                                            <div class="card-header mb-3">
                                                <div class="card-title">
                                                    <h2>{{ __('Permissions') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-6">
                                                        <div class="mb-10">
                                                            <h6>{{ __('Main area') }}</h6>
                                                            @foreach ($permissions->main_area as $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input"
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mb-10">
                                                            <h6>{{ __('Module area') }}</h6>
                                                            @foreach ($permissions->module_area as $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input"
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox" role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="my-10">
                                                            <h6>{{ __('Foods area') }}</h6>
                                                            @foreach ($permissions->food_area as $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input"
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="my-10">
                                                            <h6>{{ __('Reports area') }}</h6>
                                                            @foreach ($permissions->reports_area as $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input"
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-10">
                                                            <h6>{{ __('Backup area') }}</h6>
                                                            @foreach ($permissions->backup_database as $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input"
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <div class="mb-10 fv-row col-6">
                                                            <div class="my-10">
                                                                <h6>{{ __('System asset') }}</h6>
                                                                @foreach ($permissions->system_asset as $permission)
                                                                    <div class="form-check form-switch mb-3">
                                                                        <input class="form-check-input"
                                                                               name="{{ $permission->nameInput }}"
                                                                               type="checkbox"
                                                                               role="checkbox"
                                                                               id="{{ $permission->nameInput }}"
                                                                               @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                        <label class="form-check-label"
                                                                               for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="my-10">
                                                                <h6>{{ __('User area') }}</h6>
                                                                @foreach ($permissions->users_area as $permission)
                                                                    <div class="form-check form-switch mb-3">
                                                                        <input class="form-check-input"
                                                                               name="{{ $permission->nameInput }}"
                                                                               type="checkbox" role="checkbox"
                                                                               id="{{ $permission->nameInput }}"
                                                                               @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                        <label class="form-check-label"
                                                                               for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="my-10">
                                                                <h6>{{ __('Settings') }}</h6>
                                                                @foreach ($permissions->configuration_area as $key => $permission)
                                                                    <div class="form-check form-switch mb-3">
                                                                        <input class="form-check-input @if($key !== 0) display-settings @else settings-module @endif"
                                                                               @disabled($key !== 0 && !in_array($permissions->configuration_area[0]->nameInput, $userRole->permissions))
                                                                               name="{{ $permission->nameInput }}"
                                                                               type="checkbox" role="checkbox"
                                                                               id="{{ $permission->nameInput }}"
                                                                               @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                        <label class="form-check-label"
                                                                               for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="mt-10">
                                                                <h6>{{ __('Advanced area') }}</h6>
                                                                @foreach ($permissions->advanced_area as $permission)
                                                                    <div class="form-check form-switch mb-3">
                                                                        <input class="form-check-input"
                                                                               name="{{ $permission->nameInput }}"
                                                                               type="checkbox"
                                                                               role="checkbox"
                                                                               id="{{ $permission->nameInput }}"
                                                                               @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                        <label class="form-check-label"
                                                                               for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="mb-10 fv-row col-6">
                                                        <div class="mb-10">
                                                            <h6>{{ __('Pos area') }}</h6>
                                                            @foreach ($permissions->pos_area as $key => $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input @if($key !== 0) display-pos-settings @else pos-module @endif"
                                                                           @disabled($key !== 0 && !in_array($permissions->pos_area[0]->nameInput, $userRole->permissions))
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row col-6">
                                                        <div class="mb-10">
                                                            <h6>{{ __('Kitchen area') }}</h6>
                                                            @foreach ($permissions->kitchen_area as $key => $permission)
                                                                <div class="form-check form-switch mb-3">
                                                                    <input class="form-check-input @if($key !== 0) display-kitchen-settings @else kitchen-module @endif"
                                                                           @disabled($key !== 0 && !in_array($permissions->kitchen_area[0]->nameInput, $userRole->permissions))
                                                                           name="{{ $permission->nameInput }}"
                                                                           type="checkbox"
                                                                           role="checkbox"
                                                                           id="{{ $permission->nameInput }}"
                                                                           @if (in_array($permission->nameInput, $userRole->permissions)) checked @endif>
                                                                    <label class="form-check-label"
                                                                           for="{{ $permission->nameInput }}">{{ __($permission->name) }}</label>
                                                                </div>
                                                            @endforeach
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
