@extends('layouts.main-view')
@section('title', 'Our Location')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <a href="{{ route('settings') }}"
                                    class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }}
                                    ></a>
                                <span
                                    class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Locations') }}</span>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end gap-2 gap-lg-3"
                                    data-kt-customer-table-toolbar="base">
                                    @include('settings.goback-button')
                                    <a href="{{ route('ourLocation.create') }}"
                                        class="btn btn-primary w-125px border-0">{{ __('Add new') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: transparent">
                        <div class="mt-1 row g-5 g-xl-8">
                            <h3 class="mt-3 mb-3">{{ __('Restaurants') }}</h3>
                            @foreach ($ourLocations as $ourLocation)
                                <div class="col-xl-4 m-0">
                                    <div class="card statistics-widget-1 mb-xl-8">
                                        <div class="row">
                                            <div class="col-xl-7">
                                                <div class="card-body">
                                                    <div>
                                                    <a href="{{route('ourLocation.edit', $ourLocation->id)}}"
                                                            class="card-title fw-bold fs-4">{{ $ourLocation->name }}</a>
                                                        <a href="{{ route('ourLocation.edit', ['ourLocation' => $ourLocation->id]) }}"
                                                            class="card-title fw-bold fs-4">{{ $ourLocation->address }}</a>
                                                            <p class="fs-5 m-0 pt-2">
                                                                {{$ourLocation->location}}
                                                            </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-5 d-flex align-items-center justify-content-center">
                                                <div class="card-body m-0 text-end">
                                                    <a href="{{ route('ourLocation.edit', ['ourLocation' => $ourLocation->id]) }}"
                                                        class="mt-auto text-dark">{{ __('Location detalis') }} ></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
