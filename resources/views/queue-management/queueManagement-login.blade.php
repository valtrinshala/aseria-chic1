@extends('layouts.blank-view')
@section('title', 'Check pin')

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
                                    <div class="card-body pt-0">
                                        <form action="{{ route('check.eKiosk.pin') }}" method="post">
                                            @csrf
                                            <label class="mt-5">{{__('PIN')}}</label>
                                            <input type="hidden" name="id" value="{{ $queue->id }}">
                                            <input type="password" name="pin" class="form-control mb-8" placeholder="{{ __('Enter your pin') }}">
                                            <select id="language" name="language" class="form-select mb-6"
                                                    data-control="select2" data-placeholder="{{ __('Select an language') }}"
                                                    data-allow-clear="true">
                                                @foreach (\App\Helpers\Helpers::languages() as $language)
                                                    <option value="{{ $language->locale }}">
                                                        {{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                    class="btn btn-primary w-100">{{ __('Connect') }}</button>
                                        </form>
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
