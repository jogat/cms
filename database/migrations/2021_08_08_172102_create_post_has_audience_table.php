<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostHasAudienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_has_audience', function (Blueprint $table) {
            $table->unsignedBigInteger('post');
            $table->unsignedBigInteger('audience');
            $table->primary(['post','audience']);

            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('post','FK_post_has_audience_TO_post')
                ->references('id')->on('post')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('audience','FK_post_has_audience_TO_audience')
                ->references('id')->on('audience')
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
        Schema::dropIfExists('post_has_audience');
    }
}
