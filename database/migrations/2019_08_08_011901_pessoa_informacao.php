<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PessoaInformacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pessoaInformacao', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin')->unique()->nullable();
            $table->string('nomePessoa');
            $table->dateTime('dataNascimento');
            $table->integer('sexo');
            $table->double('classificacao');
            $table->text('descricao')->nullable();
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
        Schema::dropIfExists('pessoaInformacao');
    }
}
