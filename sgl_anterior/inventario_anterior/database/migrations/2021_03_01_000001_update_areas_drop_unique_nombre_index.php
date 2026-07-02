<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAreasDropUniqueNombreIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areas', function (Blueprint $table) {
            // Quito el indice unique por campo 'nombre', porque sino puede fallar 
            // el proceso de migración automatizado cuando un area cambia de 
            // codigo de dependencia.
            $table->dropUnique(['nombre']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->unique('nombre');
        });
    }
}
