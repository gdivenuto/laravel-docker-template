<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptores', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id();

            // ---- Data ------------------------------------------------------
            $table->string('tag', 150)->unique(); // v110
            $table->timestamps();

            // ---- Indices ---------------------------------------------------
            $table->index(['tag']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('descriptores', function (Blueprint $table) {
            $table->dropIndex('descriptores_tag_index');
        });
        Schema::dropIfExists('descriptores');
    }
}
