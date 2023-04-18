<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('address');
            $table->string('url')->nullable();
            $table->bigInteger('interval')->default(60000);
            $table->string('note')->nullable();
            $table->string('icon')->default('hdd-network');


            // For custom ping commands
            $table->string('command')->nullable();
            $table->bigInteger('key')->default(Str::uuid());

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
