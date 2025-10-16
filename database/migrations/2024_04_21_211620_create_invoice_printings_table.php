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
        Schema::create('invoice_printings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('logo_header')->nullable();
            $table->string('print_name_address_position')->nullable();
            $table->decimal('print_header_footer_font_size')->nullable();
            $table->decimal('print_items_font_size')->nullable();
            $table->decimal('print_name_address_font_size')->nullable();
            $table->decimal('print_width')->nullable();
            $table->decimal('logo_height')->nullable();
            $table->string('invoice_type_title')->nullable();
            $table->boolean('auto_print')->nullable();
            $table->timestamps();
        });
        DB::table('invoice_printings')->insert([
            ['id' => Uuid::uuid4()->toString()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_printings');
    }
};
