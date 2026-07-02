<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioCodigoToNormasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('normas', function (Blueprint $table) {
            // Se agrega el campo 'usuario_codigo' después del campo 'usuario_id'
            $table->string('usuario_codigo', 50)->nullable()->after('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('normas', function (Blueprint $table) {
            // Se elimina el campo agregado
            $table->dropColumn('usuario_codigo');
        });
    }
}
