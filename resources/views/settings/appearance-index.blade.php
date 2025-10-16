@extends('layouts.main-view')
@section('title', 'Appearance')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/settings/appearance.js')
@endsection
@section('content')
{{--    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">--}}
{{--        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">--}}
{{--            <div id="kt_app_toolbar" class="app-toolbar px-0 py-10">--}}
{{--                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">--}}
{{--                    <div class="page-title d-flex justify-content-center flex-wrap me-3">--}}
{{--                        <a href="{{ route('settings') }}"--}}
{{--                           class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }}--}}
{{--                            > </a>--}}
{{--                        <span--}}
{{--                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Appearance') }}</span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="container">--}}
{{--                <div class="card card-flush py-4">--}}
{{--                    <div class="card-header">--}}
{{--                        <div class="card-title">--}}
{{--                            <h2>{{ __('Logo') }}</h2>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="card-body text-center pt-0">--}}
{{--                        <style>--}}
{{--                            .image-input-placeholder {--}}
{{--                                background-image: url('{{ $icon }}');--}}
{{--                            }--}}

{{--                            .image-input-placeholder1 {--}}
{{--                                background-image: url('{{ $secondIcon }}');--}}
{{--                            }--}}
{{--                        </style>--}}
{{--                        <form id="kt_ecommerce_appearance">--}}
{{--                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"--}}
{{--                                 data-kt-image-input="true">--}}

{{--                                <div class="image-input-wrapper w-150px h-150px"></div>--}}
{{--                                <label--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">--}}
{{--                                    <i class="ki-duotone ki-pencil fs-7">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}

{{--                                    <input type="file" name="icon" accept=".png, .svg, .webp, .jpg, .jpeg"/>--}}
{{--                                    <input type="hidden" name="image_remove"/>--}}

{{--                                </label>--}}
{{--                                <span--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">--}}
{{--                                    <i class="ki-duotone ki-cross fs-2">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}
{{--                                </span>--}}
{{--                                <span--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">--}}
{{--                                    <i class="ki-duotone ki-cross fs-2">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                            <div--}}
{{--                                class="mx-5 image-input image-input-empty image-input-outline image-input-placeholder1 mb-3"--}}
{{--                                data-kt-image-input="true">--}}
{{--                                <div class="image-input-wrapper w-150px h-150px"></div>--}}
{{--                                <label--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">--}}
{{--                                    <i class="ki-duotone ki-pencil fs-7">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}
{{--                                    <input type="file" name="second_icon" accept=".png, .svg, .webp, .jpg, .jpeg"/>--}}
{{--                                    <input type="hidden" name="image_remove"/>--}}
{{--                                </label>--}}
{{--                                <span--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">--}}
{{--                                    <i class="ki-duotone ki-cross fs-2">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}
{{--                                </span>--}}
{{--                                <span--}}
{{--                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"--}}
{{--                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">--}}
{{--                                    <i class="ki-duotone ki-cross fs-2">--}}
{{--                                        <span class="path1"></span>--}}
{{--                                        <span class="path2"></span>--}}
{{--                                    </i>--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                        <div class="text-muted fs-7">{{ __('Only *.png, *.svg, *.webp, *.jpg and *.jpeg image files') }}--}}
{{--                        </div>--}}
{{--                        <button class="btn btn-primary mt-5 w-125px submit-appearance">{{ __('Save') }}</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
