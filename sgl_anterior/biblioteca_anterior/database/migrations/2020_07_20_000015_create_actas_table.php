<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actas', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();
            $table->foreignId('norma_id');

            // ---- Data ------------------------------------------------------
            $table->string('acta_n', 50); // v22^n
            $table->string('acta_r', 50); // v22^r
            $table->string('acta_t', 50); // v22^t
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
        Schema::table('actas', function (Blueprint $table) {
            $table->dropForeign(['norma_id']);
        });

        Schema::dropIfExists('actas');
    }
}
