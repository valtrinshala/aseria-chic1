<?php

use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        EnvSet::setKey('WEB_VERIFY_KEY', 'web_verify');
        EnvSet::save();
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->boolean('pos')->default(false);
            $table->boolean('kitchen')->default(false);
            $table->boolean('e_kiosk')->default(false);
            $table->boolean('integrated_payments')->default(false);
            $table->boolean('manual_payments')->default(false);
            $table->boolean('dine_in')->default(false);
            $table->boolean('has_tables')->default(false);
            $table->boolean('has_locators')->default(false);
            $table->boolean('take_away')->default(false);
            $table->boolean('delivery')->default(false);
            $table->boolean('auto_print')->default(false);
            $table->index('auto_print');
            $table->softDeletes();
            $table->timestamps();
        });

//        DB::table('locations')->insert([
//            'id' => config('constants.location.defaultLocationId'),
//            'name' => 'Prishtina Mall',
//            'location' => 'Prishtine',
//            'pos' => true,
//            'kitchen' => true,
//            'dine_in' => false,
//            'take_away' => false,
//            'delivery' => false,
//        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
