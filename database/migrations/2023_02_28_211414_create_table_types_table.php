<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('table_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('spaces'); // Spaces
            $table->integer('seats'); // Seats in your space
            $table->integer('price');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
};
