<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstencionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abstenciones', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();
            $table->foreignId('norma_id');

            // ---- Data ------------------------------------------------------
            $table->string('nombre', 100); // v110
            $table->timestamps();

            // ---- Integridad Referencial ------------------------------------
            $table->foreign('norma_id')->references('id')->on('normas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('abstenciones', function (Blueprint $table) {
            $table->dropForeign(['norma_id']);
        });

        Schema::dropIfExists('abstenciones');
    }
}
