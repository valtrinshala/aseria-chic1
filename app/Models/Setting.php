<?php

namespace App\Models;

use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'app_name',
        'app_address',
        'app_phone',
        'app_https',
        'app_url',
        'tva',
        'app_about',
        'app_date_format',
        'app_date_locale',
        'app_default_role',
        'app_background',
        'app_icon',
        'app_second_icon',
        'app_client_icon',
        'app_locale',
        'app_timezone', 'app_direction',
        'app_user_registration',
        'socials',
        'wifi_name',
        'wifi_password',
        'socials',
        'auth_code_for_e_kiosks',

        'queue_connection',

        'mail_from_name',
        'mail_from_address',
        'mail_mailer',
        'mail_host',
        'mail_username',
        'mail_password',
        'mail_port',
        'mail_encryption',

        'mailgun_domain',
        'mailgun_endpoint',
        'mailgun_secret',

        'recaptcha_enabled',
        'recaptcha_public',
        'recaptcha_private',

        'currency_symbol',
        'currency_symbol_on_left',
        'tax_rate', 'is_vat',
        'is_tax_fix',
        'tax_id',
        'is_tax_included',
        'web',
        'default_language',
        'print_font_family',
        'print_name_address_position',
        'print_name_address_size',
        'print_header_footer_size',
        'print_items_font_size',
        'print_terms_conditions_font_size',
        'print_font_color',
        'printer_width',
        'printer_height',
        'signature_required',
        'terms_conditions',
        'print_auto',
        'invoice_type_title',
        'custom_fields',
        'is_setup'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        /*
         * Register an updated model event with the dispatcher.
         *
         * @param \Closure|string $callback
         * @return void
         */
        self::updating(
            static function ($model) {
                $writeable = [
                    'app_url', 'app_name', 'default_language', 'app_https',
                    'app_timezone', 'app_date_format',
                    'mail_from_address', 'mail_from_name',
                    'mail_mailer', 'mail_encryption',
                    'mail_host', 'mail_password',
                    'mail_port', 'mail_username',
                    'queue_connection',
                    'mailgun_domain', 'mailgun_secret',
                    'mailgun_endpoint'
                ];

                $writeable = collect($model)->only($writeable)->all();
                foreach ($writeable as $key => $value) {
                    EnvSet::setKey(strtoupper($key), $value);
                    EnvSet::save();
                }
            }
        );
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'app_user_registration' => 'boolean',
        'recaptcha_enabled' => 'boolean',
        'app_https' => 'boolean',
        'sub_wid_is_flex' => 'boolean',
        'sub_wid_phone_input' => 'boolean',
        'sub_wid_email_input' => 'boolean',
        'currency_symbol_on_left' => 'boolean',
        'is_fix' => 'boolean',
        'is_vat' => 'boolean',
        'is_tax_fix' => 'boolean',
        'is_tax_included' => 'boolean',
        'signature_required' => 'boolean',
        'print_auto' => 'boolean',
        'socials' => 'json',
        'is_setup' => 'boolean'
    ];

    /**
     * Application icon URL
     *
     * @param mixed $icon icon
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->app_icon ? Storage::disk('public')->url($this->app_icon) : asset('images/default/icon.png');
    }
    /**
     * Application icon URL
     *
     * @param mixed $icon icon
     *
     * @return string
     */
    public function getSecondImage(): string
    {
        return $this->app_second_icon ? Storage::disk('public')->url($this->app_second_icon) : asset('images/default/icon.png');
    }
    /**
     * Application icon URL
     *
     * @param mixed $icon icon
     *
     * @return string
     */
    public function getClientImage(): string
    {
        return $this->app_client_icon ? Storage::disk('public')->url($this->app_client_icon) : asset('images/default/icon.png');
    }
}
