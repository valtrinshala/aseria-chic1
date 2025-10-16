<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default(env('APP_NAME'));
            $table->string('app_address')->nullable();
            $table->string('app_phone')->nullable();
            $table->string('app_https')->nullable();
            $table->string('app_url')->default(rtrim(getenv('APP_URL'), '/'));
            $table->string('tva')->nullable();
            $table->string('app_date_format')->default('L');
            $table->string('app_date_locale')->default('en');
            $table->string('app_default_role')->default('2');
            $table->string('app_background')->nullable();
            $table->string('app_icon')->nullable();
            $table->string('app_second_icon')->nullable();
            $table->string('app_client_icon')->nullable();
            $table->string('app_locale')->default('en');
            $table->string('app_direction')->default('ltl');
            $table->string('app_timezone')->default('Europe/Zurich');
            $table->string('default_language')->default('fr');
            $table->boolean('app_user_registration')->default(false);
            $table->text('socials')->nullable();
            $table->text('web')->nullable();
            $table->text('wifi_name')->nullable();
            $table->text('wifi_password')->nullable();
            $table->string('auth_code_for_e_kiosks')->default(substr(sha1(mt_rand()),17,10));
            //outgoing email
            $table->string('queue_connection')->default('sync');
            $table->string('mail_from_name')->default('Business Help desk');
            $table->string('mail_from_address')->nullable();
            $table->string('mail_mailer')->default('log');
            $table->string('mail_host')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_port')->default('2525');
            $table->string('mail_encryption')->nullable()->default('tls');
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_endpoint')->nullable();
            $table->string('mailgun_secret')->nullable();
            //re captcha
            $table->boolean('recaptcha_enabled')->default(false);
            $table->string('recaptcha_public')->nullable();
            $table->string('recaptcha_private')->nullable();

            $table->string('tax_rate')->default(0);
            $table->boolean('is_tax_fix')->default(false);
            $table->boolean('is_vat')->default(false);
            $table->boolean('is_tax_included')->default(false);
            $table->string('tax_id')->nullable();

            $table->string('currency_symbol')->default('CHF');
            $table->boolean('currency_symbol_on_left')->default(false);
            $table->json('custom_fields')->nullable();
            $table->boolean('is_setup')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        \App\Models\Setting::create(['app_url' => rtrim(getenv('APP_URL'), '/'), 'socials' => ['facebook' => null, 'instagram' => null, 'twitter' => null]]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
