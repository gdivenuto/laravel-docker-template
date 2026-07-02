<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            // ---- Clave Primaria --------------------------------------------
            $table->bigIncrements('id');

            // ---- Normales --------------------------------------------------
            $table->string('nombre', 100);
            $table->text('observaciones');

            // ---- Indices ---------------------------------------------------
            $table->unique('nombre');

            // ---- Varios ----------------------------------------------------
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
        });

        Schema::dropIfExists('grupos');
    }
}
