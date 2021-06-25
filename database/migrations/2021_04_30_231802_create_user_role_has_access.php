<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRoleHasAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_role_has_access', function (Blueprint $table) {
            $table->unsignedBigInteger('role');
            $table->unsignedBigInteger('access');
            $table->timestamps();

            $table->primary(['role','access']);

            $table->foreign('role','FK_user_role_has_access_TO_user_role')
                ->references('id')->on('user_role')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('access','FK_user_role_has_access_TO_access')
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
        Schema::dropIfExists('user_role_has_access');
    }
}
