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
        Schema::create('position_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->index('name');
            $table->string('asset_key')->nullable();
            $table->index('asset_key');
            $table->string('description')->nullable();
            $table->boolean('status')->default(0);
            $table->index('status');
            $table->softDeletes();
            $table->timestamps();
        });
        $positionId = [
            "a9742090-b2a0-4596-b5eb-ba8d61faed5f",
            "db8da6d3-6a69-4222-8caa-0e22acebda35",
            "9c93bde0-d23e-475f-85e9-15bf0a7df7ad",
            "05ed253d-95d7-466a-886e-52894a25e850",
            "c0afd8b6-5fd4-49c5-91dd-6502c6be73ff",
            "0e8bcd41-1e52-4eab-8e70-2cd4f7041eff",
            "46919de0-0a42-4bfe-b67a-afa5f4a64c40",
            "ebf09a46-73d5-4072-b8e8-84651126af11",
            "64a8264a-77fc-4794-8e77-36bcd286a931",
            "9f1d628c-80fe-4fdb-bb84-f661347b1604",
            "c67d0c5a-1ca9-4eb4-bdb6-03f245dfd131",
            "2957f7a3-c5bb-4d3d-b7b1-d1528279a357",
            "58b04f4e-41da-4ad0-95d7-9fffc4cc236d"
        ];
        DB::table('position_assets')->insert([
            ['id' => $positionId[0], 'name' => 'Tap to order screen','asset_key' => 'tap_to_order_screen', 'status' => true],
            ['id' => $positionId[1], 'name' => 'Company logo','asset_key' => 'company_logo', 'status' => true],
            ['id' => $positionId[2], 'name' => 'Vertical banner homepage','asset_key' => 'vertical_banner_homepage', 'status' => true],
            ['id' => $positionId[3], 'name' => 'Horizontal banner homepage','asset_key' => 'horizontal_banner_homepage', 'status' => true],
            ['id' => $positionId[4], 'name' => 'Decoration top right','asset_key' => 'decoration_top_right', 'status' => true],
            ['id' => $positionId[5], 'name' => 'Waiting screen','asset_key' => 'waiting_screen', 'status' => true],
            ['id' => $positionId[6], 'name' => 'Item added to cart','asset_key' => 'item_added_to_cart', 'status' => true],
            ['id' => $positionId[7], 'name' => 'Number of call','asset_key' => 'locator', 'status' => true],
            ['id' => $positionId[8], 'name' => 'Card payment successful','asset_key' => 'payment_successful_msg', 'status' => true],
            ['id' => $positionId[9], 'name' => 'Card payment failed','asset_key' => 'payment_failed_msg', 'status' => true],
            ['id' => $positionId[10], 'name' => 'Insert card on payments terminal','asset_key' => 'insert_card_on_pos', 'status' => true],
            ['id' => $positionId[11], 'name' => 'Arrows down on insert card on terminal','asset_key' => 'arrow_down_insert_card_on_pos', 'status' => true],
            ['id' => $positionId[12], 'name' => 'Dynamic Second company logo','asset_key' => 'second_company_logo', 'status' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_assets');
    }
};
