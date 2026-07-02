<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activos', function (Blueprint $table) {
            // ---- Clave Primaria --------------------------------------------
            $table->bigIncrements('id'); // unsigned big int

            // ---- Generales -------------------------------------------------
            $table->string('nro_inventario', 20)->default(''); // nro de inventario municipal
            $table->string('orden_compra', 50)->default('');
            $table->string('marca', 100)->default('');
            $table->string('modelo', 100)->default('');
            $table->string('nro_serie', 50)->default('');
            $table->string('ubicacion', 100)->default('');     // ubicación física
            $table->date('fecha_alta');
            
            // ---- Para PCs --------------------------------------------------
            $table->string('nombre_equipo', 100)->default('');
            $table->string('sistema_operativo', 100)->default('');
            $table->string('sistema_operativo_serie', 100)->default(''); // ?
            $table->string('cpu', 100)->default('');
            $table->string('memoria', 100)->default('');
            $table->string('motherboard', 100)->default('');
            $table->string('hd_marca', 100)->default('');
            $table->string('hd_capacidad', 100)->default('');
            $table->boolean('dvd_rw')->default(false);
            $table->string('dvd_rw_marca', 100)->default('');
            $table->boolean('ethernet_dinamico')->default(false); 
            $table->string('ethernet_mac', 17)->default('');
            $table->string('ethernet_ip', 15)->default(''); 
            $table->string('ethernet_mask', 15)->default(''); 
            $table->string('ethernet_gw', 15)->default('');
            $table->string('ethernet_dns', 200)->default('');
            $table->boolean('wireless_dinamico')->default(false); 
            $table->string('wireless_mac', 17)->default('');
            $table->string('wireless_ip', 15)->default(''); 
            $table->string('wireless_mask', 15)->default(''); 
            $table->string('wireless_gw', 15)->default(''); 
            $table->string('wireless_dns', 200)->default(''); 
            $table->string('fuente', 100)->default('');

            // ---- Para Impresoras -------------------------------------------
            //$table->string('tipo_insumo', 100)->default('');

            // ---- Para Monitores --------------------------------------------
            //$table->string('pulgadas', 100)->default('');

            // ---- Varios ----------------------------------------------------
            $table->text('observaciones');
            $table->softDeletes();
            $table->timestamps();

            // ---- Claves foráneas  ------------------------------------------
            $table->unsignedBigInteger('tipo_id');
            $table->foreign('tipo_id')->references('id')->on('activo_tipos');

            $table->unsignedInteger('legajo');
            $table->foreign('legajo')->references('legajo')->on('responsables');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            // ---- Indices ---------------------------------------------------
            $table->index('nro_inventario');
            $table->index('orden_compra');
            $table->index('marca');
            $table->index('modelo');
            $table->index('nro_serie');
            $table->index('sistema_operativo');
            $table->index('cpu');
            $table->index('motherboard');
            $table->index('hd_marca');
            $table->index('dvd_rw_marca');
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
            $table->dropIndex(['nro_serie']);
            $table->dropIndex(['modelo']);
            $table->dropIndex(['marca']);
            $table->dropIndex(['orden_compra']);
            $table->dropIndex(['nro_inventario']);

            $table->dropForeign('activos_user_id_foreign');
            $table->dropForeign('activos_legajo_foreign');
            $table->dropForeign('activos_tipo_id_foreign');
        });

        Schema::dropIfExists('activos');
    }
}
