<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relaciones', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();
            $table->foreignId('norma_id');

            // ---- Data ------------------------------------------------------
            $table->enum('sentido', ['O', 'D']); // O: origen, D: destino
            $table->enum('tipo', ['A', 'D', 'R', 'M']); // A: abroga, D: deroga, R: reglamenta, M: modifica
            $table->string('a', 50); // v50^a, v51^a, v52^a, v53^a, v60^a, v61^a, v62^a
            $table->string('n', 50); // v50^n, v51^n, v52^n, v53^n, v60^n, v61^n, v62^n
            $table->text('p')->nullable(); ; // v50^p, v51^p, v52^p, v53^p, v60^p, v61^p, v62^p
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
        Schema::table('relaciones', function (Blueprint $table) {
            $table->dropForeign(['norma_id']);
        });

        Schema::dropIfExists('relaciones');
    }
}
