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
        Schema::table('table_types', function (Blueprint $table) {
            $table->string('package'); // package name used in regsys
        });

            DB::table('table_types')->where('id', '=', '1')->update([
                'package' => 'dealer-half']
            );
            DB::table('table_types')->where('id', '=', '2')->update([
                'package' => 'dealer-full']
            );
            DB::table('table_types')->where('id', '=', '3')->update([
                'package' => 'dealer-double']
            );
            DB::table('table_types')->where('id', '=', '4')->update([
                'package' => 'dealer-double']
            );
            DB::table('table_types')->where('id', '=', '5')->update([
                'package' => 'dealer-quad']
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_types', function (Blueprint $table) {
            $table->dropColumn('package');
        });
    }
};
