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
        Schema::create('guild_settings', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->unique();
            $table->boolean('enabled')->default(true);
            $table->boolean('show_credit')->default(true);
            $table->boolean('auto_mode')->default(true);
            $table->integer('twitter_conversions')->default(0);
            $table->integer('x_conversions')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guild_settings');
    }
};
