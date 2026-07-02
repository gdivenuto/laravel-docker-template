<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivoTiposDescDinamicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activo_tipos', function (Blueprint $table) {
            // ---- Campo agregado para la "Descripción Dinámica" ----
            // El campo contendrá, separado por comas, el nombre de cada campo a mostrar
            // cuando se llame al accesor de la clase 'Activo'; p.e.: $activo->desc_dinamica
             
            $table->string('atrib_desc_dinamica')->default('marca,modelo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activo_tipos', function (Blueprint $table) {
            $table->dropColumn('atrib_desc_dinamica');
        });
    }
}
