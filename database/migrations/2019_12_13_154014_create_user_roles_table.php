<?php

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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name');
            $table->boolean('is_primary')->default(false);
            $table->longText('permissions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('user_roles')->insert([
            ['id' => config('constants.role.adminId'), 'name' => 'Admin', 'is_primary' => true, 'permissions' => json_encode([])], //has all permissions
            ['id' => config('constants.role.orderTakerId') , 'name' => 'Order taker', 'is_primary' => false, 'permissions' => json_encode(['pos_module'])],
            ['id' => config('constants.role.chefId') , 'name' => 'Chef', 'is_primary' => false, 'permissions' => json_encode(['kitchen_module'])],
            ['id' => config('constants.role.billerId') , 'name' => 'Biller', 'is_primary' => false, 'permissions' => json_encode(['pos_module', 'order_checkout'])],
            ['id' => config('constants.role.managerId') , 'name' => 'Manager', 'is_primary' => false, 'permissions' => json_encode(['dashboard_access', 'overall_report', 'tax_report', 'stock_alerts'])],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
