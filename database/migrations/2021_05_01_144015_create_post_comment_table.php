<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comment', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('post')->index();
            $table->unsignedBigInteger('parent')->index();
            $table->unsignedBigInteger('author');
            $table->string('content',200);
            $table->boolean('published');
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->foreign('post','FK_post_comment_TO_post')
                ->references('id')->on('post');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_comment');
    }
}
