<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResponsablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsables', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->unsignedInteger('legajo');

            // ---- Datos -----------------------------------------------------
            $table->string('apellido', 100)->default('');
            $table->string('nombre', 100)->default('');

            $table->enum('tipo_doc', ['DNI', 'LE', 'LC'])->default('DNI');
            $table->string('nro_doc', 10)->default('');

            $table->string('tel_fijo', 30)->default('');
            $table->string('tel_movil', 30)->default('');
            $table->string('email', 128)->default('');
            $table->string('domicilio', 200)->default('');

            $table->unsignedInteger('legajo_padre')->nullable(); // referencia circular

            $table->text('observaciones');

            $table->timestamps();

            // ---- Claves foráneas  ------------------------------------------
            $table->string('cod_area', 8);
            $table->foreign('cod_area')->references('cod_area')->on('areas');

            // ---- Indices ---------------------------------------------------
            $table->primary('legajo');
            $table->index('apellido');
            $table->index(['apellido', 'nombre']);
            $table->index('nro_doc');
            $table->index(['tipo_doc', 'nro_doc']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('responsables', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['tipo_doc', 'nro_doc']);
            $table->dropIndex(['nro_doc']);
            $table->dropIndex(['apellido', 'nombre']);
            $table->dropIndex(['apellido']);
            $table->dropPrimary(['legajo']);

            $table->dropForeign('responsables_cod_area_foreign');
        });

        Schema::dropIfExists('responsables');
    }
}
