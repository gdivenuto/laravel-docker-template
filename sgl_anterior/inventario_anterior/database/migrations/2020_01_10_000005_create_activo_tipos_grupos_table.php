<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivoTiposGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activo_tipos_grupos', function (Blueprint $table) {
            // Tabla para relación de 'many-to-many' entre tipos de activos y grupos
            
            // ---- Relacion con activo_tipos ---------------------------------
            $table->unsignedBigInteger('tipo_id');
            $table->foreign('tipo_id')->references('id')->on('activo_tipos')->onDelete('cascade');

            // ---- Relacion con grupos ---------------------------------------
            $table->unsignedBigInteger('grupo_id');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');

            // ---- Varios ----------------------------------------------------
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activo_tipos_grupos', function (Blueprint $table) {
            $table->dropForeign(['tipo_id']);
            $table->dropForeign(['grupo_id']);
        });

        Schema::dropIfExists('activo_tipos_grupos');
    }
}
