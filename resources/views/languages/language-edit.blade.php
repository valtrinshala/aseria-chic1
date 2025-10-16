@extends('layouts.main-view')
@section('title', 'Edit Language')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/languages/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('language.index') }}"
                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Update language') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ $language->name }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <button class="btn delete-button btn-danger w-125px border-0"
                                data-id="{{ $language->id }}"
                                data-name="{{ $language->name }}">{{ __('Delete') }}</button>
                        <a href="{{ route('language.create') }}"
                           class="btn btn-light w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                        <button id="submitButton" class="btn fw-bold btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div>
                        <form id="kt_ecommerce_add_language_form" class="form d-flex flex-column flex-lg-row">
                            <input type="hidden" id="method" value="update">
                            <input type="hidden" id="page-id" value="{{ $language->id }}">
                            <div class="d-flex flex-column gap-7 gap-lg-7 w-100 w-lg-425px mb-7 me-lg-7">
                                <div class="card card-flush py-6">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>{{ __('Flag') }}</h2>
                                            <span
                                                class="small fs-8 p-5 text-nowrap">{{ __('This language will associate with this flag') }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body text-center pt-0">
                                        <style>
                                            .image-input-placeholder {
                                                background-image: url('{{ $language->getImage() }}');
                                            }
                                        </style>
                                        <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                                             data-kt-image-input="true">
                                            <div class="image-input-wrapper w-150px h-150px"></div>
                                            <label
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="{{__("Change image")}}">
                                                <i class="ki-duotone ki-pencil fs-7">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <input type="file" name="image" accept=".png, .svg, .webp, .jpg, .jpeg" />
                                                <input type="hidden" name="image_remove" />
                                            </label>
                                            <span
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                title="Cancel avatar">
                                                <i class="ki-duotone ki-cross fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                title="Remove avatar">
                                                <i class="ki-duotone ki-cross fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div class="text-muted fs-7">
                                            {{ __('Only *.png, *.svg, *.webp, *.jpg and *.jpeg image files') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                                <div class="tab-content">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                             role="tab-panel">
                                            <div class="d-flex flex-column gap-7 gap-lg-7">
                                                <div class="card card-flush py-4">
                                                    <div class="card-header">
                                                        <div class="card-title">
                                                            <h2>{{ __('Language Details') }}</h2>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 pb-1">
                                                        <div class="row">
                                                            <div class="mb-10 fv-row col">
                                                                <label for="name"
                                                                       class="required form-label fw-bold">{{ __('Name') }}</label>
                                                                <input disabled id="name" type="text"
                                                                       value="{{ $language->name }}" class="form-control mb-2"
                                                                       placeholder="ex. English" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="mb-10 fv-row col-4">
                                                                <label for="locale"
                                                                       class="form-label fw-bold">{{ __('Locale') }}</label>
                                                                <input disabled id="locale" type="text" name="locale"
                                                                       value="{{ $language->locale }}"
                                                                       class="form-control mb-2" placeholder="ex. en" />
                                                            </div>
                                                            <div class="mb-10 fv-row col-4">
                                                                <label for="locale"
                                                                       class="form-label fw-bold">{{ __('Set 2') }}</label>
                                                                <input disabled id="locale" type="text"
                                                                       value="{{ $language->set2 }}"
                                                                       class="form-control mb-2" placeholder="ex. en" />
                                                            </div>
                                                            <div class="mb-10 fv-row col-4">
                                                                <label for="direction"
                                                                       class="form-label fw-bold">{{ __('App direction') }}</label>
                                                                <select id="direction" name="direction"
                                                                        class="form-select mb-2" data-control="select2"
                                                                        data-placeholder="Select an option"
                                                                        data-allow-clear="true">
                                                                    <option
                                                                        {{ $language->direction == 'LTR' ? 'selected' : '' }}
                                                                        value="LTR">{{ __('LTR') }}</option>
                                                                    <option
                                                                        {{ $language->direction == 'RTL' ? 'selected' : '' }}
                                                                        value="RTL">{{ __('RTL') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>{{ __('Words') }}</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            @foreach ($keys as $i => $key)
                                <li class="list-group">
                                    <div class="my-2">
                                        <label for="input-{{ $i }}">{{ $key }}</label>
                                        <input id="input-{{ $i }}" name="keys[{{ $key }}]"
                                               placeholder="'{{ $key }}' in {{ $language->name }}"
                                               value="{{ $words[$key] ?? null }}" class="form-control p-2">
                                    </div>
                                </li>
                            @endforeach
                        </div>
                        <div class="card-header">
                            <div class="card-title">
                                <h2>{{ __('E Kiosk Words') }}</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            @foreach ($androidKeys as $i => $androidKey)
                                <li class="list-group">
                                    <div class="my-2">
                                        <label for="input-{{ $i }}">{{ $androidKey['value'] }}</label>
                                        <input id="input-{{ $i }}" name="android[{{ $androidKey['key'] }}]"
                                               placeholder="'{{ $androidKey['value'] }}' in {{ $language->name }}"
                                               value="{{  $androidWords[$i]['value'] ?? null }}" class="form-control p-2">
                                    </div>
                                </li>
                            @endforeach
                        </div>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
