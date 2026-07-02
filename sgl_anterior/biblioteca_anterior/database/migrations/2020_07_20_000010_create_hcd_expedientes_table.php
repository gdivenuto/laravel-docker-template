<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHcdExpedientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hcd_expedientes', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();
            $table->foreignId('norma_id');

            // ---- Data ------------------------------------------------------
            $table->string('hcd_exped', 50); // v7
            $table->timestamps();

            // ---- Integridad Referencial ------------------------------------
            $table->foreign('norma_id')->references('id')->on('normas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hcd_expedientes', function (Blueprint $table) {
            $table->dropForeign(['norma_id']);
        });

        Schema::dropIfExists('hcd_expedientes');
    }
}
