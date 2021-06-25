<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTrackActionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_track_action_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action')->index();
            $table->string('title',150)->index();
            $table->longText('value');
            $table->timestamps();

            $table->foreign('action','FK_user_track_action_detail_TO_user_track_action')
                ->references('id')->on('user_track_action')
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
        Schema::dropIfExists('user_track_action_detail');
    }
}
