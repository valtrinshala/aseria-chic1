@extends('layouts.blank-view')


@section('content')
    <div class="h-100 background-wraper">
        <div class="h-100 overflow-auto" style="background-color:#2C1D4D">
            <div class="card-header d-flex justify-content-center border-0 pt-6">
                <div class="card-title">
                    <div class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <div class="h-40px">

                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('create.account.post') }}" method="post">
                @csrf
                <div class="container card card-flush py-4 my-4 w-700px border-0" style="background-color:#1C1036">
                    <div class="card-header d-flex justify-content-center">
                        <div class="card-title text-center">
                            <h2 style="color: #fff" class="text-center">{{ __('Create your account') }}</h2>
                        </div>
                    </div>

                    <div class="card-body pt-4">
                        <div class="row">
                            <div class="mb-2 fv-row col-12">
                                <label for="businessname" class="required form-top">{{ __('Business name') }}</label>
                                <input id="businessname" type="text" name="business_name" class="form-control mb-2"
                                    placeholder="Enter your business name" />
                                @error('business_name')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-2 fv-row col-12">

                                <label for="phone" class="required form-top">{{ __('Phone') }}</label>
                                <input id="phone" type="phone" name="business_phone" class="form-control mb-2"
                                    placeholder="Enter your phone number" />
                                @error('business_phone')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-2 fv-row col-12">
                                <label for="username" class="required form-top">{{ __('Username') }}</label>
                                <input id="username" type="username" name="name" class="form-control mb-2"
                                    placeholder="Enter your name" />
                                @error('name')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-2 fv-row col-12">
                                <label for="email" class="required form-top">{{ __('Email address') }}</label>
                                <input id="email" type="email" name="email" class="form-control mb-2"
                                    placeholder="Enter your email address" />
                                @error('email')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-2 fv-row col-12">
                                <label for="password" class="required form-top">{{ __('Password') }}</label>
                                <input id="password" type="password" name="password" class="form-control mb-2"
                                    placeholder="Enter your account password" />
                                @error('password')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-2 fv-row col-12">
                                <label for="restaurantname" class="required form-top">{{ __('Restaurant name') }}</label>
                                <input id="restaurantname" type="restaurantname" name="restaurant_name"
                                    class="form-control mb-2" placeholder="Enter restaurant name" />
                                @error('restaurant_name')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4 fv-row col-12">
                                <label for="location" class="required form-top">{{ __('Location') }}</label>
                                <input id="location" type="location" name="location" class="form-control mb-2"
                                    placeholder="Enter your restaurant location" />
                                @error('location')
                                    <div class="text-danger">
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-primary fw-bold btn-block create-account-btn w-100">{{ __('Create account') }}</button>
                    </div>

            </form>
        </div>
    </div>
@endsection
