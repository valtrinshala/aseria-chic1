@extends('layouts.main-view')
@section('title', 'Localization')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/settings/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('settings') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Localization') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        @include('settings.goback-button')
                        <button id="submitButton"
                            class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_setting_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="page-id" value="general">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-425px mb-7 me-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('General setting') }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                    role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{ __('General localization') }}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="fv-row">
                                                        <label for="app_timezone"
                                                               class="form-label fw-bold">{{ __('Timezone') }}</label>
                                                        <select id="app_timezone" name="app_timezone" class="form-select mb-2"
                                                                data-control="select2"
                                                                data-placeholder="{{ __('Select an option') }}"
                                                                data-allow-clear="true">
                                                            <option {{ $data['app_timezone'] == "Europe/Zurich" ? 'selected' : ''}} value="Europe/Zurich">Europe/Zurich</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Berlin" ? 'selected' : ''}} value="Europe/Berlin">Europe/Berlin</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Amsterdam" ? 'selected' : ''}} value="Europe/Amsterdam">Europe/Amsterdam</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Andorra" ? 'selected' : ''}} value="Europe/Andorra">Europe/Andorra</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Astrakhan" ? 'selected' : ''}} value="Europe/Astrakhan">Europe/Astrakhan</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Athens" ? 'selected' : ''}} value="Europe/Athens">Europe/Athens</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Belgrade" ? 'selected' : ''}} value="Europe/Belgrade">Europe/Belgrade</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Bratislava" ? 'selected' : ''}} value="Europe/Bratislava">Europe/Bratislava</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Brussels" ? 'selected' : ''}} value="Europe/Brussels">Europe/Brussels</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Bucharest" ? 'selected' : ''}} value="Europe/Bucharest">Europe/Bucharest</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Budapest" ? 'selected' : ''}} value="Europe/Budapest">Europe/Budapest</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Busingen" ? 'selected' : ''}} value="Europe/Busingen">Europe/Busingen</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Chisinau" ? 'selected' : ''}} value="Europe/Chisinau">Europe/Chisinau</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Copenhagen" ? 'selected' : ''}} value="Europe/Copenhagen">Europe/Copenhagen</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Dublin" ? 'selected' : ''}} value="Europe/Dublin">Europe/Dublin</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Gibraltar" ? 'selected' : ''}} value="Europe/Gibraltar">Europe/Gibraltar</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Guernsey" ? 'selected' : ''}} value="Europe/Guernsey">Europe/Guernsey</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Helsinki" ? 'selected' : ''}} value="Europe/Helsinki">Europe/Helsinki</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Isle_of_Man" ? 'selected' : ''}} value="Europe/Isle_of_Man">Europe/Isle_of_Man</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Istanbul" ? 'selected' : ''}} value="Europe/Istanbul">Europe/Istanbul</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Jersey" ? 'selected' : ''}} value="Europe/Jersey">Europe/Jersey</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Kaliningrad" ? 'selected' : ''}} value="Europe/Kaliningrad">Europe/Kaliningrad</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Kirov" ? 'selected' : ''}} value="Europe/Kirov">Europe/Kirov</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Kyiv" ? 'selected' : ''}} value="Europe/Kyiv">Europe/Kyiv</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Lisbon" ? 'selected' : ''}} value="Europe/Lisbon">Europe/Lisbon</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Ljubljana" ? 'selected' : ''}} value="Europe/Ljubljana">Europe/Ljubljana</option>
                                                            <option {{ $data['app_timezone'] == "Europe/London" ? 'selected' : ''}} value="Europe/London">Europe/London</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Luxembourg" ? 'selected' : ''}} value="Europe/Luxembourg">Europe/Luxembourg</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Madrid" ? 'selected' : ''}} value="Europe/Madrid">Europe/Madrid</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Malta" ? 'selected' : ''}} value="Europe/Malta">Europe/Malta</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Mariehamn" ? 'selected' : ''}} value="Europe/Mariehamn">Europe/Mariehamn</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Minsk" ? 'selected' : ''}} value="Europe/Minsk">Europe/Minsk</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Monaco" ? 'selected' : ''}} value="Europe/Monaco">Europe/Monaco</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Moscow" ? 'selected' : ''}} value="Europe/Moscow">Europe/Moscow</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Oslo" ? 'selected' : ''}} value="Europe/Oslo">Europe/Oslo</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Paris" ? 'selected' : ''}} value="Europe/Paris">Europe/Paris</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Podgorica" ? 'selected' : ''}} value="Europe/Podgorica">Europe/Podgorica</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Prague" ? 'selected' : ''}} value="Europe/Prague">Europe/Prague</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Riga" ? 'selected' : ''}} value="Europe/Riga">Europe/Riga</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Rome" ? 'selected' : ''}} value="Europe/Rome">Europe/Rome</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Samara" ? 'selected' : ''}} value="Europe/Samara">Europe/Samara</option>
                                                            <option {{ $data['app_timezone'] == "Europe/San_Marino" ? 'selected' : ''}} value="Europe/San_Marino">Europe/San_Marino</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Sarajevo" ? 'selected' : ''}} value="Europe/Sarajevo">Europe/Sarajevo</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Saratov" ? 'selected' : ''}} value="Europe/Saratov">Europe/Saratov</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Simferopol" ? 'selected' : ''}} value="Europe/Simferopol">Europe/Simferopol</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Skopje" ? 'selected' : ''}} value="Europe/Skopje">Europe/Skopje</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Sofia" ? 'selected' : ''}} value="Europe/Sofia">Europe/Sofia</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Stockholm" ? 'selected' : ''}} value="Europe/Stockholm">Europe/Stockholm</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Tallinn" ? 'selected' : ''}} value="Europe/Tallinn">Europe/Tallinn</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Tirane" ? 'selected' : ''}} value="Europe/Tirane">Europe/Tirane</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Ulyanovsk" ? 'selected' : ''}} value="Europe/Ulyanovsk">Europe/Ulyanovsk</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Vaduz" ? 'selected' : ''}} value="Europe/Vaduz">Europe/Vaduz</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Vatican" ? 'selected' : ''}} value="Europe/Vatican">Europe/Vatican</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Vienna" ? 'selected' : ''}} value="Europe/Vienna">Europe/Vienna</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Vilnius" ? 'selected' : ''}} value="Europe/Vilnius">Europe/Vilnius</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Volgograd" ? 'selected' : ''}} value="Europe/Volgograd">Europe/Volgograd</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Warsaw" ? 'selected' : ''}} value="Europe/Warsaw">Europe/Warsaw</option>
                                                            <option {{ $data['app_timezone'] == "Europe/Zagreb" ? 'selected' : ''}} value="Europe/Zagreb">Europe/Zagreb</option>
                                                        </select>
                                                    </div>
                                                    <div class="fv-row">
                                                        <label for="default_language"
                                                            class="form-label fw-bold">{{ __('Site language') }}</label>
                                                        <select id="default_language" name="default_language" class="form-select mb-2"
                                                            data-control="select2"
                                                            data-placeholder="{{ __('Select an option') }}"
                                                            data-allow-clear="true">
                                                            @foreach (\App\Helpers\Helpers::languages() as $language)
                                                                <option {{ $data['default_language'] == $language->locale ? 'selected' : '' }} value="{{ $language->locale }}">
                                                                    {{ $language->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
{{--                                                    <div class="mb-5 fv-row">--}}
{{--                                                        <label for="app_direction"--}}
{{--                                                            class="required form-label fw-bold">{{ __('App direction') }}</label>--}}
{{--                                                        <input id="app_direction" type="text" name="app_direction"--}}
{{--                                                            value="{{ $data['app_direction'] }}" class="form-control mb-2"--}}
{{--                                                            placeholder="{{ __('App direction') }}" />--}}
{{--                                                    </div>--}}
                                                    <div class="mb-5 fv-row">
                                                        <label for="app_date_format"
                                                            class="required form-label fw-bold">{{ __('Date format') }}</label>
                                                        <select id="app_date_format" name="app_date_format"
                                                            class="form-select mb-2" data-control="select2"
                                                            data-placeholder="{{ __('Date format') }}">
                                                            <option
                                                                {{ $data['app_date_format'] == 'd-m-Y' ? 'selected' : '' }}
                                                                value="d-m-Y">{{ __('d-m-Y') }}</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y-m-d' ? 'selected' : '' }}
                                                                value="Y-m-d">Y-m-d</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd-m-Y h:s:i' ? 'selected' : '' }}
                                                                value="d-m-Y h:s:i">d-m-Y h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd-m-Y, h:s:i' ? 'selected' : '' }}
                                                                value="d-m-Y, h:s:i">d-m-Y, h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y-m-d h:s:i' ? 'selected' : '' }}
                                                                value="Y-m-d h:s:i">Y-m-d h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd D M Y' ? 'selected' : '' }}
                                                                value="d D M Y">d D M Y</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y M D d' ? 'selected' : '' }}
                                                                value="Y M D d">Y M D d</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd D M Y, h:s:i' ? 'selected' : '' }}
                                                                value="d D M Y, h:s:i">d D M Y, h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y M D d, h:s:i' ? 'selected' : '' }}
                                                                value="Y M D d, h:s:i">Y M D d, h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y M D d h:s:i' ? 'selected' : '' }}
                                                                value="Y M D d h:s:i">Y M D d h:s:i</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y-m-d H:i:s' ? 'selected' : '' }}
                                                                value="Y-m-d H:i:s">Y-m-d H:i:s</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd-m-Y H:i:s' ? 'selected' : '' }}
                                                                value="d-m-Y H:i:s">d-m-Y H:i:s</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'Y/m/d H:i:s' ? 'selected' : '' }}
                                                                value="Y/m/d H:i:s">Y/m/d H:i:s</option>
                                                            <option
                                                                {{ $data['app_date_format'] == 'd/m/Y H:i:s' ? 'selected' : '' }}
                                                                value="d/m/Y H:i:s">d/m/Y H:i:s</option>
                                                        </select>
                                                        <p class="form-description-text text-gray-700">
                                                            {{ __('Default format for dates on the site. List of supported format can be found') }}
                                                            <a href="https://momentjs.com/docs/#/displaying/format/"
                                                                rel="noopener" target="_blank"
                                                                class="text-blue-500">{{ __('here') }}</a>
                                                            .
                                                        </p>
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
