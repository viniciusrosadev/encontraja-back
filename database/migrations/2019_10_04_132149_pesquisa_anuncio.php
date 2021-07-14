<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PesquisaAnuncio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesquisaAnuncio', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin')->nullable();
            $table->integer('cidadePesquisada')->nullable();
            $table->string('termoPesquisado');
            $table->string('descricaoPesquisada')->nullable();
            $table->date('dataInicialPesquisada')->nullable();
            $table->date('dataFinalPesquisada')->nullable();
            $table->timestamp('pesquisadoEm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesquisaAnuncio');
    }
}
