<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_meta', function (Blueprint $table) {
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('meta');
            $table->string('value');
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['user','meta', 'value']);

            $table->foreign('meta','FK_user_has_meta_TO_user_meta')
                ->references('id')->on('user_meta')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user','FK_user_has_meta_TO_user')
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
        Schema::dropIfExists('user_has_meta');
    }
}
