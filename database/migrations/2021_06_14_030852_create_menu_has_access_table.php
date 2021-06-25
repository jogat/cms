<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuHasAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_has_access', function (Blueprint $table) {
            $table->unsignedBigInteger('menu');
            $table->unsignedBigInteger('access');
            $table->primary(['menu','access']);
            $table->timestamps();

            $table->foreign('menu','FK_menu_has_access_TO_menu')
                ->references('id')->on('menu')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('access','FK_menu_has_access_TO_access')
                ->references('id')->on('access')
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
        Schema::dropIfExists('menu_has_access');
    }
}
