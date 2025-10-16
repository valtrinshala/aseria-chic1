<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('e_kiosk_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->index('name');
            $table->uuid('e_kiosk_id')->nullable();
            $table->foreign('e_kiosk_id')->references('id')->on('e_kiosks')->onDelete('cascade');
            $table->uuid('position_id')->nullable();
            $table->foreign('position_id')->references('id')->on('position_assets')->nullOnDelete();
            $table->boolean('status')->default(false);
            $table->index('status');
            $table->string('image')->nullable();
            $table->string('type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
//        $positionId = [
//            "a9742090-b2a0-4596-b5eb-ba8d61faed5f",
//            "db8da6d3-6a69-4222-8caa-0e22acebda35",
//            "9c93bde0-d23e-475f-85e9-15bf0a7df7ad",
//            "05ed253d-95d7-466a-886e-52894a25e850",
//            "c0afd8b6-5fd4-49c5-91dd-6502c6be73ff",
//            "0e8bcd41-1e52-4eab-8e70-2cd4f7041eff",
//            "46919de0-0a42-4bfe-b67a-afa5f4a64c40",
//            "ebf09a46-73d5-4072-b8e8-84651126af11",
//            "64a8264a-77fc-4794-8e77-36bcd286a931",
//            "9f1d628c-80fe-4fdb-bb84-f661347b1604",
//            "c67d0c5a-1ca9-4eb4-bdb6-03f245dfd131",
//            "2957f7a3-c5bb-4d3d-b7b1-d1528279a357",
//            "619e0c1e-985b-4ee3-b159-22373c1881c1",
//            "11adfa02-65ae-437f-bd1b-a5e807cf01cd",
//            "4ea5a149-fc01-42e1-901b-1725c95d06b3",
//            "f060095b-d41d-40f6-a83e-f0f4c47880dc",
//            "babfc0b8-0b90-49ce-bdac-50e7a6ba0869",
//            "2258a9a3-cb17-4ea8-903c-ece4fc64a65a"
//        ];
//        DB::table('e_kiosk_assets')->insert([
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[0], 'name' => 'Tap to order', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[1], 'name' => 'Dine In', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[2], 'name' => 'Take Away', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[3], 'name' => 'Company Logo', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[4], 'name' => 'TopRight Menu Screen Decoration', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[5], 'name' => 'Review Top Image', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[6], 'name' => 'Card Verification Icon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[7], 'name' => 'Number Of CallIcon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[8], 'name' => 'Pos Information Image', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[9], 'name' => 'Pos Information Bottom Icon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[10], 'name' => 'Payment Successful Icon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[11], 'name' => 'Order Number Icon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[12], 'name' => 'Item EditTop Image', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[13], 'name' => 'Delete Icon', 'status' => true],
//            ['id' => \Ramsey\Uuid\Uuid::uuid4()->toString(), 'position_id' => $positionId[14], 'name' => 'Edit Icon', 'status' => true],
//        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_kiosk_assets');
    }
};
