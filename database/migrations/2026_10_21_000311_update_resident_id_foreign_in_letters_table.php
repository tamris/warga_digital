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
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable()->change();
            $table->foreign('resident_id')->references('id')->on('residents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable(false)->change();
            $table->foreign('resident_id')->references('id')->on('residents')->restrictOnDelete();
        });
    }
};
