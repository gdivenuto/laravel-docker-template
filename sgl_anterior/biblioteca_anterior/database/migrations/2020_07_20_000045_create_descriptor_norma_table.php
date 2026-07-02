<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptorNormaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptor_norma', function (Blueprint $table) {
            // ---- Data ------------------------------------------------------
            $table->foreignId('descriptor_id');
            $table->foreignId('norma_id');
            $table->timestamps();

            // ---- Integridad Referencial ------------------------------------
            $table->foreign('descriptor_id')->references('id')->on('descriptores')->onDelete('cascade');
            $table->foreign('norma_id')->references('id')->on('normas')->onDelete('cascade');

            // ---- Indices --------------------------------------------------- 
            $table->primary(['descriptor_id', 'norma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('descriptor_norma', function (Blueprint $table) {
            $table->dropForeign(['norma_id']);
            $table->dropForeign(['descriptor_id']);
        });        
        Schema::dropIfExists('descriptor_norma');
    }
}
