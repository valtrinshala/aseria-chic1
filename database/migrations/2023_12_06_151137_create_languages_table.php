<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name')->nullable();
            $table->string('locale')->nullable()->unique();
            $table->string('set2')->nullable()->unique();
            $table->string('direction')->default('ltl');
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('languages')->insert([
            [
                'id' => config('constants.language.languageId'),
                'name' => 'English',
                'locale' => 'en',
                'set2' => 'eng',
                'direction' => 'ltr',
                'created_at' => "2019-01-01"
            ]
        ]);
        $directoryPath = base_path('resources/lang/');
        $filePath = $directoryPath . 'en.json';
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }
        if (!File::exists($filePath)) {
            File::put($filePath, json_encode(['key' => 'value'], JSON_PRETTY_PRINT));
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
