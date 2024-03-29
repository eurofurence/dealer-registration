<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\TableType::class,'table_type_requested')->nullable()->constrained('table_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\TableType::class,'table_type_assigned')->nullable()->constrained('table_types')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('type'); // Dealer, Share, Assistant
            $table->foreignIdFor(\App\Models\Application::class,'parent_id')->nullable()->constrained('applications')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('display_name')->nullable();
            $table->string('website')->nullable();
            $table->string('table_number')->nullable();

            $table->string('invite_code_shares')->nullable();
            $table->string('invite_code_assistants')->nullable();

            $table->text('merchandise')->nullable();
            $table->text('wanted_neighbors')->nullable();
            $table->text('comment')->nullable();

            $table->boolean('is_afterdark')->nullable();
            $table->boolean('is_power')->nullable();
            $table->boolean('is_wallseat')->nullable();

            $table->timestamp('waiting_at')->nullable();
            $table->timestamp('offer_sent_at')->nullable();
            $table->timestamp('offer_accepted_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
