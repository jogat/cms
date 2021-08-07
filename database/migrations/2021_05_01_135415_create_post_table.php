<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('author');
            $table->unsignedBigInteger('published_by')->nullable(true);
            $table->string('title',100);
            $table->string('description',255)->default('');
            $table->unsignedBigInteger('type');
            $table->unsignedBigInteger('status');

            $table->string('thumbnail')->nullable();
            $table->string('resource')->nullable(); // image, video,link
            $table->text('body')->nullable(); // body post
            $table->json('json_data')->nullable(); // survey, poll, data

            $table->timestamps();
            $table->timestamp('last_published_date')->nullable();// update date every time status is changed to published

            $table->foreign('published_by','FK_published_by_TO_user')
                ->references('id')->on('user')
                ->onDelete('cascade');

            $table->foreign('author','FK_author_TO_user')
                ->references('id')->on('user')
                ->onDelete('cascade');

            $table->foreign('type','FK_post_TO_post_type')
                ->references('id')->on('post_type')
                ->onDelete('cascade');

            $table->foreign('status','FK_post_TO_post_status')
                ->references('id')->on('post_status')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post');
    }
}
