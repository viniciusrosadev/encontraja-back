<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Imagem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagem', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idLogin');
            $table->integer('idAnuncio')->nullable();
            $table->smallInteger('ativo');
            $table->smallInteger('tipo');
            $table->longText('base64');
            $table->timestamp('uploadEm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imagem');
    }
}
