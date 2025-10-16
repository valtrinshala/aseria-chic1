@extends('layouts.blank-view')
@section('title', 'Cash Register')

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
{{--                                @if(!$location || !session()->get('cash_register'))--}}
                                    <div class="card card-flush py-4 w-50 align-self-center text-center w-600px">
                                        <div class="card-header justify-content-center">
                                            <div class="card-title">
                                                    <h2>{{ __('Choose your cash register') }}</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0"><span class="mb-10">{{__('')}}</span>
                                            <form action="{{ route('set.location.cashRegister.pos') }}" method="get">
                                                @if(!session()->get('cash_register'))
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <input type="hidden" name="from_pos" value="1">
                                                            <select id="cash_register" name="cash_register"
                                                                    class="form-control my-7"
                                                                    data-control="select2"
                                                                    data-placeholder="{{ __('Select an cash register') }}">
                                                                <option></option>
                                                                @foreach($cashRegisters as $cashRegister)
                                                                    <option @if(old('cash_register') == $cashRegister->id) selected @endif value="{{ $cashRegister->id }}">{{ $cashRegister->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input id="pin" type="password" name="pin"
                                                                   class="form-control mt-7"
                                                                   placeholder="{{__('Pin')}}"/>
                                                            @if(session('error'))
                                                                <div class="text-danger">
                                                                    {{ session('error') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="submit" class="btn btn-primary w-100">{{ __('Acknowledge') }}</button>
                                            </form>
                                        </div>
                                        @if(count($cashRegisters) == 0)
                                            <a href="{{ route('cashRegister.create') }}">{{__('Create a new cash register here')}}!</a>
                                        @endif
                                    </div>
{{--                                @else--}}
{{--                                    <div class="card card-flush py-4 w-50 align-self-center text-center w-600px">--}}
{{--                                        <div class="card-header justify-content-center">--}}
{{--                                            <div class="card-title">--}}
{{--                                                <h2>{{ __('Continue?') }}</h2>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="card-body pt-0">--}}
{{--                                                <span--}}
{{--                                                    class="mb-10">{{__('All changes made here')}}</span>--}}
{{--                                            <form action="{{ route('settings.set.location.for.admin') }}" method="get">--}}
{{--                                                <select id="categories" name="categories" class="form-control my-7"--}}
{{--                                                        data-control="select2"--}}
{{--                                                        data-placeholder="{{ __('Select an location') }}">--}}
{{--                                                    <option></option>--}}
{{--                                                    @foreach(\App\Helpers\Helpers::locations() as $eachLocation)--}}
{{--                                                        <option--}}
{{--                                                            value="{{ $eachLocation->id }}">{{ $eachLocation->name }}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}

{{--                                                <button type="submit"--}}
{{--                                                        class="btn btn-primary w-100">{{ __('Acknowledge') }}</button>--}}
{{--                                            </form>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

