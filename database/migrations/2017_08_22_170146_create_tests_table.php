<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('type')->default('form');
            $table->string('template')->default('default');
            $table->string('uniqueness_type')->default('cookies');
            $table->string('publication')->default('author');
            $table->integer('timeout')->default(0);
            $table->string('state')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
