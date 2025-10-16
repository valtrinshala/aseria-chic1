@extends('layouts.main-view')
@section('title', 'Databases Backups')
@section('page-script')
    @vite('resources/assets/js/custom/apps/backup-database/delete-restore-backup.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid  m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <g id="users-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                              transform="translate(8719 6517)" fill="none"/>
                                        <path id="folder_copy_FILL0_wght300_GRAD0_opsz48"
                                              d="M61.649-839a1.565,1.565,0,0,1-1.161-.5A1.651,1.651,0,0,1,60-840.693v-14.993h1.3v14.993a.357.357,0,0,0,.1.26.337.337,0,0,0,.253.1H81.408V-839Zm3.165-3.252a1.565,1.565,0,0,1-1.161-.5,1.652,1.652,0,0,1-.487-1.193v-14.361a1.651,1.651,0,0,1,.487-1.193,1.565,1.565,0,0,1,1.161-.5h6.527l2.286,2.348h8.725a1.565,1.565,0,0,1,1.161.5A1.651,1.651,0,0,1,84-855.957v12.013a1.652,1.652,0,0,1-.487,1.193,1.565,1.565,0,0,1-1.161.5Z"
                                              transform="translate(8658.999 7378.499)" fill="#5d4bdf"/>
                                    </g>
                                </svg>

                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Databases Backups') }}</span>
                            </div>
                            <div class="card-toolbar">

                                <div class="d-flex justify-content-end gap-2 gap-lg-3"
                                     data-kt-customer-table-toolbar="base">
                                    <a href="{{ route('generate.backup') }}"
                                       class="btn btn-primary text-nowrap border-0 @if($user->role_id != config('constants.role.adminId') && !in_array('generate_backup', $user->userRole->permissions)) d-none @endif">{{ __('Generate Backup') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: transparent">
                        <div class="mt-1 row g-5 g-xl-6">
                            @php $i = 1 @endphp
                            @foreach ($backups as $backup)
                                <div class="{{ rtrim($backup['name'], '.zip') }} card statistics-widget-1 mb-xl-6">
                                    <div class="row">
                                        <div class="col-xl-7">
                                            <div class="card-body">
                                                <span class="d-block mb-1">#{{ $i }} - {{ $backup['date'] }}</span>
                                                <a class="d-block mb-1">{{ $backup['name'] }}</a>
                                                <span class="d-block mb-1">{{ $backup['size_mb'] }}MB (cc824fde-51d6-4759-aff6-d3e7c5128b71)</span>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 d-flex align-items-center justify-content-center">
                                            <div class="card-body m-0 text-end text-nowrap">
                                                <form class="restore-backup-form d-inline">
                                                    <input type="hidden" name="name" value="{{ $backup['name'] }}">
                                                    <button type="submit" class="mt-auto btn btn-secondary text-dark @if($user->role_id != config('constants.role.adminId') && !in_array('download_backup', $user->userRole->permissions)) d-none @endif">{{ __('Restore') }}</button>
                                                </form>
                                                <form class="delete-backup-form d-inline">
                                                    <input type="hidden" name="name" value="{{ $backup['name'] }}">
                                                    <button type="submit" class="mt-auto btn btn-secondary text-dark @if($user->role_id != config('constants.role.adminId') && !in_array('delete_backup', $user->userRole->permissions)) d-none @endif">{{ __('Delete') }}</button>
                                                </form>
                                                <a href="{{ route('download.backup', $backup['name']) }}"
                                                   class="mt-auto btn btn-secondary text-dark @if($user->role_id != config('constants.role.adminId') && !in_array('download_backup', $user->userRole->permissions)) d-none @endif">{{ __('Download') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @php $i++ @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
