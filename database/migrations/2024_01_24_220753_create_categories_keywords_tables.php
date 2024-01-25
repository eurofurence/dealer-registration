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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignIdFor(\App\Models\Category::class)->constrained('categories', 'uuid')->cascadeOnUpdate()->restrictOnDelete()->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('keyword_profile', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Profile::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete()->primary();
            $table->foreignIdFor(\App\Models\Keyword::class)->constrained('keywords', 'uuid')->cascadeOnUpdate()->cascadeOnDelete()->primary();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_profile');
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('categories');
    }
};
