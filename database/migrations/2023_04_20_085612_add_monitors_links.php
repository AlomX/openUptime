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
        // add links column to monitors table
        Schema::table('monitors', function (Blueprint $table) {
            $table->json('links')->nullable()->after('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove links column from monitors table
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn('links');
        });
    }
};
