<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntendenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intendencias', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();

            // ---- Data ------------------------------------------------------
            $table->string('intendente', 100);
            $table->integer('nro')->default(1);
            $table->date('fec_desde')->nullable();
            $table->date('fec_hasta')->nullable();
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
        Schema::dropIfExists('intendencias');
    }
}
