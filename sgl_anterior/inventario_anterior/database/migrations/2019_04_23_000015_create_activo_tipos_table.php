<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivoTiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activo_tipos', function (Blueprint $table) {
            // ---- Clave Primaria --------------------------------------------
            $table->bigIncrements('id');

            // ---- Normales --------------------------------------------------
            $table->string('nombre', 100);

            // ---- Generales -------------------------------------------------
            $table->boolean('has_nro_inventario')->default(true);
            $table->boolean('has_orden_compra')->default(true);
            $table->boolean('has_marca')->default(true);
            $table->boolean('has_modelo')->default(true);
            $table->boolean('has_nro_serie')->default(true);
            $table->boolean('has_ubicacion')->default(true);
            $table->boolean('has_fecha_alta')->default(true);
            
            // ---- Para PCs --------------------------------------------------
            $table->boolean('has_nombre_equipo')->default(false);
            $table->boolean('has_sistema_operativo')->default(false);
            $table->boolean('has_sistema_operativo_serie')->default(false);
            $table->boolean('has_cpu')->default(false);
            $table->boolean('has_memoria')->default(false);
            $table->boolean('has_motherboard')->default(false);
            $table->boolean('has_hd_marca')->default(false);
            $table->boolean('has_hd_capacidad')->default(false);
            $table->boolean('has_dvd_rw')->default(false);
            $table->boolean('has_dvd_rw_marca')->default(false);
            $table->boolean('has_ethernet_mac')->default(false);
            $table->boolean('has_ethernet_dinamico')->default(false); 
            $table->boolean('has_ethernet_ip')->default(false); 
            $table->boolean('has_ethernet_mask')->default(false); 
            $table->boolean('has_ethernet_gw')->default(false);
            $table->boolean('has_ethernet_dns')->default(false);
            $table->boolean('has_wireless_mac')->default(false);
            $table->boolean('has_wireless_dinamico')->default(false); 
            $table->boolean('has_wireless_ip')->default(false); 
            $table->boolean('has_wireless_mask')->default(false); 
            $table->boolean('has_wireless_gw')->default(false); 
            $table->boolean('has_wireless_dns')->default(false); 
            $table->boolean('has_fuente')->default(false)->default(false);

            // ---- Para Impresoras -------------------------------------------
            //$table->boolean('has_tipo_insumo')->default(false);

            // ---- Para Monitores --------------------------------------------
            //$table->boolean('has_pulgadas')->default(false);

            // ---- Varios ----------------------------------------------------
            $table->boolean('has_observaciones')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // ---- Indices ---------------------------------------------------
            $table->unique('nombre');
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
            $table->dropUnique(['nombre']);
        });

        Schema::dropIfExists('activo_tipos');
    }
}
