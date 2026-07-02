<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInformeInventarioActivoTiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activo_tipos', function (Blueprint $table) {
            // ---- Campos agregados para la "Toma de Inventario" del D.E. ----

            $table->boolean('has_tipo_origen')->default(true);
            $table->boolean('has_titularidad')->default(true);
            $table->boolean('has_estado')->default(true);
            $table->boolean('has_condicion_uso')->default(true);
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
            $table->dropColumn(['has_tipo_origen', 'has_titularidad', 'has_estado', 'has_condicion_uso']);
        });
    }
}
