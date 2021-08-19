<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostHasCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_has_category', function (Blueprint $table) {
            $table->unsignedBigInteger('post');
            $table->unsignedBigInteger('category');
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['post','category']);

            $table->foreign('post','FK_post_has_category_TO_post')
                ->references('id')->on('post')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('category','FK_post_has_category_TO_post_category')
                ->references('id')->on('post_category')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_has_category');
    }
}
