<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClassificacaoUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('classificacaoUsuario', function (Blueprint $table) {
                $table->integer('id')->index()->autoIncrement();
                $table->integer('idLogin');
                $table->integer('idPessoaAcessada');
                $table->double('nota');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classificacaoUsuario');
    }
}
