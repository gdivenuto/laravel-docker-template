<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInformeInventarioActivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activos', function (Blueprint $table) {
            // ---- Campos agregados para la "Toma de Inventario" del D.E. ----
            
            // tipo_origen: determina si el campo orden_compra representa una 
            // orden de compra, ordenanza, decreto o formulario de transferencia.
            //     OC: Orden de Compra
            //     OR: Ordenanza
            //     DE: Decreto
            //     FT: Formulario de Transferencia
            $table->string('tipo_origen', 2)->default('OC'); // OC: Orden de Compra

            // titularidad: tipo de titularidad del bien
            //     1.0: Propios
            //     1.1: Propios - Adquiridos
            //     1.2: Propios - Donados
            //     1.3: Propios - Traslados internos o externos
            //     2.1: De Terceros - Comodato
            //     2.2: De Terceros - Alquilado
            //     2.3: De Terceros - Leasing
            $table->string('titularidad', 12)->default('1.0'); // 1.0: propios

            // estado: estado del activo
            //     1: Muy Bueno
            //     2: Bueno
            //     3: Regular
            //     4: Malo
            $table->string('estado', 2)->default('1'); // 1: Muy Bueno

            // condicion_uso: condicion de uso del activo
            //     1: Activo
            //     2: Desuso
            //     3: Rezago
            $table->string('condicion_uso', 2)->default('1'); // 1: Activo
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activos', function (Blueprint $table) {
            $table->dropColumn(['tipo_origen', 'titularidad', 'estado', 'condicion_uso']);
        });
    }
}
