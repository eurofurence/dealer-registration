<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index('applications_user_id_foreign');
            $table->unsignedBigInteger('table_type_requested')->nullable()->index('applications_table_type_requested_foreign');
            $table->unsignedBigInteger('table_type_assigned')->nullable()->index('applications_table_type_assigned_foreign');
            $table->string('type');
            $table->uuid('parent_id')->nullable()->index('applications_parent_id_foreign');
            $table->string('display_name')->nullable();
            $table->string('website')->nullable();
            $table->string('table_number')->nullable();
            $table->string('invite_code_shares')->nullable()->unique();
            $table->string('invite_code_assistants')->nullable()->unique();
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
            $table->timestamp('checked_out_at')->nullable();
            $table->text('additional_space_request')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('user_id')->index('comments_user_id_foreign');
            $table->uuid('application_id')->index('comments_application_id_foreign');
            $table->text('text');
            $table->boolean('admin_only');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('keyword_profile', function (Blueprint $table) {
            $table->uuid('profile_id')->index('keyword_profile_profile_id_foreign');
            $table->unsignedBigInteger('keyword_id')->index('keyword_profile_keyword_id_foreign');
            $table->timestamps();
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('category_id')->index('keywords_category_id_foreign');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id')->nullable()->index('profiles_application_id_foreign');
            $table->text('short_desc')->nullable();
            $table->text('artist_desc')->nullable();
            $table->text('art_desc')->nullable();
            $table->text('website')->nullable();
            $table->text('twitter')->nullable();
            $table->text('telegram')->nullable();
            $table->text('discord')->nullable();
            $table->text('tweet')->nullable();
            $table->text('art_preview_caption')->nullable();
            $table->string('image_thumbnail')->nullable();
            $table->string('image_art')->nullable();
            $table->string('image_artist')->nullable();
            $table->boolean('attends_thu')->nullable();
            $table->boolean('attends_fri')->nullable();
            $table->boolean('attends_sat')->nullable();
            $table->timestamps();
            $table->string('mastodon')->nullable();
            $table->string('bluesky')->nullable();
            $table->boolean('is_hidden')->default(false);
        });

        Schema::create('table_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('spaces');
            $table->integer('seats');
            $table->integer('price');
            $table->timestamps();
            $table->string('package');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reg_id')->nullable();
            $table->string('identity_id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
            $table->longText('groups')->nullable();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreign(['parent_id'])->references(['id'])->on('applications')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['table_type_assigned'])->references(['id'])->on('table_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['table_type_requested'])->references(['id'])->on('table_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::table('keyword_profile', function (Blueprint $table) {
            $table->foreign(['keyword_id'])->references(['id'])->on('keywords')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['profile_id'])->references(['id'])->on('profiles')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('keywords', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign('profiles_application_id_foreign');
        });

        Schema::table('keywords', function (Blueprint $table) {
            $table->dropForeign('keywords_category_id_foreign');
        });

        Schema::table('keyword_profile', function (Blueprint $table) {
            $table->dropForeign('keyword_profile_keyword_id_foreign');
            $table->dropForeign('keyword_profile_profile_id_foreign');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign('comments_application_id_foreign');
            $table->dropForeign('comments_user_id_foreign');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign('applications_parent_id_foreign');
            $table->dropForeign('applications_table_type_assigned_foreign');
            $table->dropForeign('applications_table_type_requested_foreign');
            $table->dropForeign('applications_user_id_foreign');
        });

        Schema::dropIfExists('users');

        Schema::dropIfExists('table_types');

        Schema::dropIfExists('profiles');

        Schema::dropIfExists('keywords');

        Schema::dropIfExists('keyword_profile');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('comments');

        Schema::dropIfExists('categories');

        Schema::dropIfExists('applications');
    }
};
