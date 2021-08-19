<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginAttempts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_attempts', function (Blueprint $table) {
            $table->string('email');
            $table->integer('user')->nullable(true);
            $table->tinyInteger('success');
            $table->string('session',50);
            $table->string('ip',20);
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_attempts');
    }
}
