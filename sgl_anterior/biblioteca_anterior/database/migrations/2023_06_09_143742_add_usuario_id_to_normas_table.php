<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioIdToNormasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('normas', function (Blueprint $table) {
            
            // Se agrega el campo 'usuario_id' después del campo 'base'
            $table->integer('usuario_id')->nullable()->after('base');
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
            $table->dropColumn('usuario_id');
        });
    }
}
