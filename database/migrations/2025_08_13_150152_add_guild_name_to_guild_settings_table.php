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
        Schema::table('guild_settings', function (Blueprint $table) {
            $table->string('guild_name')->nullable()->after('guild_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guild_settings', function (Blueprint $table) {
            $table->dropColumn('guild_name');
        });
    }
};
