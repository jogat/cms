<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_access', function (Blueprint $table) {
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('access');
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['user','access']);

            $table->foreign('access','FK_user_has_access_TO_access')
                ->references('id')->on('access')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user','FK_user_has_access_TO_user')
                ->references('id')->on('user')
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
        Schema::dropIfExists('user_has_access');
    }
}
