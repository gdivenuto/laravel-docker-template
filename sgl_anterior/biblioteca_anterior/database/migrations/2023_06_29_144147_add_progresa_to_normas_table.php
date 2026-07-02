<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgresaToNormasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('normas', function (Blueprint $table) {
            
            // Se agrega el campo 'procesa' después del campo 'ingresa'
            $table->string('procesa', 10)->nullable()->after('ingresa');
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
            $table->dropColumn('procesa');
        });
    }
}
