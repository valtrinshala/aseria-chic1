<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ViewSettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $external_settings = \App\Helpers\Helpers::getSettingsData();

            $settings = Setting::first();
            $user = auth()->user();
            if (!$user){
                $view->with([
                    'settings' => $settings,
                    'external_settings' => $external_settings
                ]);
            }else{
                if ($user->role_id == config('constants.role.adminId')){
                    $isAdmin = true;
                    $location = session()->get('localization_for_changes_data');
                }else{
                    $isAdmin = false;
                    $location = $user->location;
                }
                $view->with([
                    'mainLanguage' => Language::find(config('constants.language.languageId')),
                    'languageInQuery' => Language::find(app(Request::class)->query('language_update_id')),
                    'settings' => $settings,
                    'isAdmin' => $isAdmin,
                    'location' => $location,
                    'user' => $user,
                    'external_settings' => $external_settings
                ]);
            }
        });
    }
}
