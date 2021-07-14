<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TokenUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokenUsuario', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idUsuario')->unique();
            $table->string('token')->unique();
            $table->timestamp('geradoEm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokenUsuario');
    }
}
