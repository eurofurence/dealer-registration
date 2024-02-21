<?php

use App\Models\Application;
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
        Schema::table('applications', function (Blueprint $table) {
            $table->text('additional_space_request')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('applications', 'additional_space_request')) {
            return;
        }

        foreach (Application::all() as $application) {
            $application->update([
                "comment" => join("\n", [$application->comment, $application->additional_space_request])
            ]);
        }

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('additional_space_request');
        });
    }
};
