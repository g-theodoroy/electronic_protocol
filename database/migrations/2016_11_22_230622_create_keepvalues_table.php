<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeepvaluesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keepvalues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fakelos')->required()->unique();
            $table->integer('keep')->unsigned()->nullable();
            $table->string('keep_alt')->nullable();
            $table->string('describe')->nullable()->index();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keepvalues');
    }
}
