 @extends('layouts.blank-view')

 @section('content')
     <div class="h-100 background-wraper bg-login-violet overflow-auto position-relative">
         <div class="card-header d-flex justify-content-center border-0 mt-20 pt-20">
             <div class="card-title">
                 <div
                     class="page-heading remove-image d-flex flex-column text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                     <img height="80" alt="Logo" src="{{ $external_settings['image'] }}" class="logo">
                 </div>
             </div>
         </div>
         <div class="container card card-flush border-0 w-400px position-sticky top-50 ty-50 background-style">
             <div class="card-body pt-4">
                 <div class="text-center mb-10">
                     <h2 class="text-white fs-3tx fw-light mb-2">{{__('Sign in')}}</h2>
                     <p class="text-white fs-4">{{__('Sign in and start managing your inventory')}}</p>
                 </div>
                 <form action="{{ route('login') }}" method="post">
                     @csrf
                     <div class="mb-2 fv-row col-12 ">
                         <div class="mt-10 mb-6">
{{--                             <label for="email" class="required form-top">{{ __('Enter username') }}</label>--}}
                             <input id="exampleInputEmail1" aria-describedby="emailHelp" type="email"
                                 name="email" class="form-control rounded-0 mb-2" placeholder={{__("Enter username")}} />
                             @error('email')
                                 <div class="text-danger">
                                     <span>{{ $message }}</span>
                                 </div>
                             @enderror
                         </div>
                         <div class="mb-2 fv-row col-12">
{{--                             <label for="text" class="required form-top">{{ __('Enter password') }}</label>--}}
                             <input id="exampleInputEmail2" type="password" name="password" class="form-control rounded-0 mb-2"
                                 placeholder={{__("Enter password")}} />
                             @error('password')
                                 <div class="text-danger">
                                     <span>{{ $message }}</span>
                                 @enderror
                             </div>
                             <button type="submit"
                                 class="btn btn-primary fw-bold btn-block create-account-btn w-100 mt-8 rounded-0 button-style">{{ __('Login') }}</button>
                         </div>
                     </div>
                 </form>
             </div>
             <div class="footer position-fixed bottom-0 text-white w-100 d-flex justify-content-center fs-5 gap-4 mb-2">
                <span class="date">{{ date('d.m.Y') }}</span> | <span class="time">{{ date('H:i:s')}}</span> | <span class="email"><a class="text-white" href="mailto:{{ $external_settings['email'] }}">{{ $external_settings['email'] }}</a></span> | <span class="phone"><a class="text-white" href="tel:{{ $external_settings['telephone'] }}">{{ $external_settings['telephone'] }}</a></span>
            </div>
         </div>

         </div>
 @endsection
