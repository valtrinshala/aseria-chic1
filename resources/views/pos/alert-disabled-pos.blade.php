@extends('layouts.blank-view')
@section('title', 'Error')

@section('page-style')
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid bg-violet" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container h-100">
                    <div
                        class="tab-content d-flex justify-content-center flex-column flex-lg-row align-items-center h-100">
                        <div class="tab-pane fade show active d-flex justify-content-center"
                             id="kt_ecommerce_add_product_general"
                             role="tab-panel">
                            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                                <div class="card card-flush py-4 w-50 align-self-center text-center w-600px">
                                    <div class="card-header justify-content-center">
                                        <div class="card-title">
                                            <h2>{{ __('Error') }}</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0"><span class="mb-10">
                                        {{ $errors->first('pos_error') ?? $errors->first('kitchen_error')}}
                                        </span>
                                        <div>
                                            <a href="{{ route('pos.index') }}" class="btn btn-primary w-auto mt-5">{{__('Try again')}}</a>
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
@endsection

