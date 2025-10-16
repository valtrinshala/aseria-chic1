@extends('layouts.blank-view')



@section('content')
    <div class="h-100 background-wraper overflow-auto" style="background-color:#2C1D4D">
        <div class="card-header d-flex justify-content-center border-0 pt-20 mb-20">
            <div class="card-title">
                <div class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <div class="h-90px"></div>
{{--                    <img height="80" alt="Logo" src="{{ asset('images/logo/Image_17.png') }}" class="logo">--}}
                </div>
            </div>
        </div>


        <form action="{{ route('login.verify.key.post') }}" method="post">
            @csrf
            <div class="container card card-flush py-12 my-10 w-700px h-400px border-0" style="background-color:#1C1036">
                <div class="card-header">
                    <div class="container mt-6">
                        <div class="d-flex align-items-center justify-content-center ">
                            <span
                                class="mb-6 text-center text-light-light fw-bold fs-3 welcome-heading">{{ __('Welcome') }}</span>
                        </div>
                        <span
                            class="d-flex align-items-center justify-content-center instruction-text text-light-light text-nowrap fs-5">
                            {{ __('To continue with your account, please add your key access down below.') }}
                        </span>
                        <div>
                            <div class="row mt-2 fw-bold">
                                <div class="mb-2 fv-row col-12 mt-12">
                                    <label class="fw-bold fs-4 text-light-light" for="key " class="form-label"
                                        for="key">{{ __('Key access*') }}</label>
                                    <input name="key" type="text" placeholder="Enter your key access*" id="key"
                                        class="form-control mt-4" />
                                    @if (session('error'))
                                        <div class="text-danger">
                                            <span>{{ session('error') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-8">
                            <button type="submit" class="btn btn-primary fw-bold btn-block create-account-btn w-100">{{ __('Continue') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
