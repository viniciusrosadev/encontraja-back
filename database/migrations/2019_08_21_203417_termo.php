<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Termo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termo', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin');
            $table->smallInteger('tipo');
            $table->smallInteger('statusTermo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termo');
    }
}
