<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Application::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->text('short_desc')->nullable();
            $table->text('artist_desc')->nullable();
            $table->text('art_desc')->nullable();
            $table->text('website')->nullable();
            $table->text('twitter')->nullable();
            $table->text('telegram')->nullable();
            $table->text('discord')->nullable();
            $table->text('tweet')->nullable();
            $table->text('art_preview_caption')->nullable();

            $table->boolean('is_print')->nullable();
            $table->boolean('is_artwork')->nullable();
            $table->boolean('is_fursuit')->nullable();
            $table->boolean('is_commissions')->nullable();
            $table->boolean('is_misc')->nullable();

            $table->boolean('attends_thu')->nullable();
            $table->boolean('attends_fri')->nullable();
            $table->boolean('attends_sat')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_profiles');
    }
};
