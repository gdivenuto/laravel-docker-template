<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHcdExpedToNormasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('normas', function (Blueprint $table) {

            // Se agrega el campo 'hcd_exped' después del campo 'bloque'
            $table->string('hcd_exped', 50)->nullable()->after('bloque'); // v7
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
            $table->dropColumn('hcd_exped');
        });
    }
}
