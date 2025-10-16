<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('ratio')->nullable();
            $table->string('description')->nullable();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('units')->insert([
                ['id' => Uuid::uuid4()->toString(), 'name' => "Kilogram", 'suffix' => "Kilogram", 'ratio' => 1],
                ['id' => Uuid::uuid4()->toString(), 'name' => "Gram", 'suffix' => "Gram", 'ratio' => 0.001],
                ['id' => Uuid::uuid4()->toString(), 'name' => "Milliliter", 'suffix' => "Milliliter", 'ratio' => 0.001],
                ['id' => Uuid::uuid4()->toString(), 'name' => "Liter", 'suffix' => "Liter", 'ratio' => 1],
                ['id' => Uuid::uuid4()->toString(), 'name' => "Unit", 'suffix' => "Unit", 'ratio' => 1],
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
