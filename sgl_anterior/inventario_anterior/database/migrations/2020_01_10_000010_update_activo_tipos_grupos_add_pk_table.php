<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivoTiposGruposAddPKTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activo_tipos_grupos', function (Blueprint $table) {
            // Agregado de clave primaria             
            $table->primary(['tipo_id','grupo_id']);
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
            $table->dropPrimary(['tipo_id','grupo_id']);
        });
    }
}