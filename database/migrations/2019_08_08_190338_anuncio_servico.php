<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnuncioServico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anuncioServico', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin');
            $table->integer('idPessoa');
            $table->string('tituloAnuncio');
            $table->dateTime('exibirData'); // Exibir a partir da data selecionada do anuncio
            $table->integer('ativo'); // Anuncio ativo ou desativado
            $table->integer('nivelPrioridade'); //Prioridade do anuncio 1 - Alto, 2 - Médio e 3 - Baixo
            $table->text('descricao')->nullable(); // Descrição do anuncio
            $table->string('telefone')->nullable(); // Telefone para entrar em contato
            $table->string('emailContato')->nullable(); // Email para contato
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
        Schema::dropIfExists('anuncioServico');
    }
}
