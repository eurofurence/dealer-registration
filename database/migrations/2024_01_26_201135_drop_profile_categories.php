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
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('is_print');
            $table->dropColumn('is_artwork');
            $table->dropColumn('is_fursuit');
            $table->dropColumn('is_commissions');
            $table->dropColumn('is_misc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('is_print')->nullable();
            $table->boolean('is_artwork')->nullable();
            $table->boolean('is_fursuit')->nullable();
            $table->boolean('is_commissions')->nullable();
            $table->boolean('is_misc')->nullable();
        });
    }
};
