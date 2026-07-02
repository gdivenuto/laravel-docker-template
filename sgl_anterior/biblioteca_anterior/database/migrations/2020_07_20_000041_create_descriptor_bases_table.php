<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptorBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptor_bases', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->foreignId('descriptor_id');
            $table->string('base', 10)->default('normas');

            // ---- Data ------------------------------------------------------
            $table->timestamps();

            // ---- Integridad Referencial ------------------------------------
            $table->foreign('descriptor_id')->references('id')->on('descriptores')->onDelete('cascade');

            // ---- Indices ---------------------------------------------------
            $table->primary(['descriptor_id', 'base']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descriptor_bases');
    }
}
