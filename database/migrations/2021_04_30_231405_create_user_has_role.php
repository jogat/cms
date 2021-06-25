<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_role', function (Blueprint $table) {
            $table->unsignedBigInteger('role');
            $table->unsignedBigInteger('user');
            $table->primary(['user','role']);
            $table->timestamps();

            $table->foreign('user','FK_user_has_role_TO_user')
                ->references('id')->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('role','FK_user_has_role_TO_user_role')
                ->references('id')->on('user_role')
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
        Schema::dropIfExists('user_has_role');
    }
}
