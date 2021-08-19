<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienceHasRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audience_has_rule', function (Blueprint $table) {
            $table->unsignedBigInteger('audience');
            $table->unsignedBigInteger('rule');
            $table->string('value');
            $table->timestamp('created_at')->default(db()->raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(db()->raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary(['audience','rule', 'value']);

            $table->foreign('audience','FK_audience_has_rule_TO_audience')
                ->references('id')->on('audience')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('rule','FK_audience_has_rule_TO_audience_rule')
                ->references('id')->on('audience_rule')
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
        Schema::dropIfExists('audience_has_rule');
    }
}
