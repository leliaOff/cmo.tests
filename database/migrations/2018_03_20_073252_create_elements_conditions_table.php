<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementsConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elements_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('element_id');
            $table->foreign('element_id')->references('id')->on('elements');
            $table->unsignedInteger('conditions_element_id');
            $table->foreign('conditions_element_id')->references('id')->on('elements');
            $table->text('conditions_answer');
            $table->enum('operand', ['=', '!', '<', '>', '<=', '>=']);
            $table->enum('combination', ['or', 'and'])->default('and');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elements_conditions');
    }
}