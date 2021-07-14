<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VisitasAnuncio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitasAnuncio', function (Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('idAnuncio');
            $table->integer('idLogin');
            $table->timestamp('acessadoEm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitasAnuncio');
    }
}
