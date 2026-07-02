<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->string('cod_area', 8);

            // ---- Datos -----------------------------------------------------
            $table->string('nombre', 100)->default('');
            $table->enum('tipo', ['P', 'B'])->default('P');

            $table->string('cod_area_padre')->nullable(); // depende de "area"... referencia circular

            $table->timestamps();

            // ---- Indices ---------------------------------------------------
            $table->primary('cod_area');
            $table->unique('nombre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
            $table->dropPrimary(['cod_area']);
        });

        Schema::dropIfExists('areas');
    }
}
