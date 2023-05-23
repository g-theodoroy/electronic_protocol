<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProtocolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocols', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->required();
            $table->integer('protocolnum')->required()->unsigned()->index();
            $table->integer('protocoldate')->required()->unsigned();
            $table->integer('etos')->unsigned()->required();
            $table->string('fakelos')->nullable();
            $table->string('thema')->nullable();
            $table->string('in_num')->nullable();
            $table->integer('in_date')->unsigned()->nullable();
            $table->string('in_topos_ekdosis')->nullable();
            $table->string('in_arxi_ekdosis')->nullable();
            $table->string('in_paraliptis')->nullable();
            $table->string('diekperaiosi')->nullable();
            $table->integer('diekp_eos')->unsigned()->nullable();
            $table->string('in_perilipsi')->nullable();
            $table->integer('out_date')->unsigned()->nullable();
            $table->integer('diekp_date')->unsigned()->nullable();
            $table->string('sxetiko')->nullable();
            $table->string('out_to')->nullable();
            $table->string('out_perilipsi')->nullable();
            $table->string('keywords')->nullable();
            $table->text('paratiriseis')->nullable();
            $table->timestamps();
            $table->unique(array('protocolnum', 'etos'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('protocols');
    }
}
