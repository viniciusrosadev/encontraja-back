<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InstituicaoInformacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instituicaoInformacao', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin')->unique();
            $table->string('nomeInstituicao');
            $table->dateTime('dataAbertura');
            $table->dateTime('dataFechado')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('tipo');
            $table->text('descricao');
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
        Schema::dropIfExists('instituicaoInformacao');
    }
}
