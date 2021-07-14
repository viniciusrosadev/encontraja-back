<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocalizacaoAnuncio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localizacaoAnuncio', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idAnuncio');
            $table->integer('idLogin');
            $table->integer('idEstado');
            $table->integer('idMicroRegiao');
            $table->integer('idCidade');
            $table->smallInteger('cidadePrincipal');
            $table->timestamp('criadoEm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localizacaoAnuncio');
    }
}
