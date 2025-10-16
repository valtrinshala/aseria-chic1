<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('random_id')->nullable();
            $table->uuid('role_id');
            $table->uuid('location_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('pin')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('language')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('status')->default(true);
            $table->foreign('role_id')->references('id')->on('user_roles');
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
//        DB::table('users')->insert([
//            'id' => Uuid::uuid4()->toString(),
//            'name' => "Clirim",
//            'random_id' => time(),
//            'email' => "clirim.bytyci1@gmail.com",
//            'address' => "test",
//            'avatar' => "test",
//            'password' => Hash::make('123123123'),
//            'status' => true,
//            'role_id' => config('constants.role.adminId'),
//            'pin' => "1234",
//        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
