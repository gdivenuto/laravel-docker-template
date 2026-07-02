<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digestos', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();

            // ---- Data ------------------------------------------------------
            $table->string('nombre', 100)->unique();
            $table->boolean('publicado')->default(false);
            $table->text('descripcion');
            $table->text('filtro'); // json array con los filtros "directos" sobre los campos
            $table->timestamps();

            // ---- Index -----------------------------------------------------
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digestos');
    }
}
