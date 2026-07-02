<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHabilitadoToActivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activos', function (Blueprint $table) {
            // Se agrega el campo 'habilitado' después del campo 'condicion_uso'
            $table->string('habilitado', 2)->default('1')->after('condicion_uso');
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
            // Se elimina el campo agregado
            $table->dropColumn('habilitado');
        });
    }
}
