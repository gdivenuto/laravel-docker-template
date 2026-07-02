<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNormasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('normas', function (Blueprint $table) {
            // ---- Primary Key -----------------------------------------------
            $table->id(); // mfn

            // ---- Data ------------------------------------------------------
            $table->string('acto', 10)->default(''); // v1
            $table->string('nro', 10)->default(''); // v2
            $table->string('origen', 10)->default(''); // v3
            $table->string('nro_hcd', 20)->default(''); // v4
            $table->string('exped', 50)->default(''); // v5
            $table->string('bloque', 50)->default(''); // v6
            // v7      hcd_exped @ hcd_expedientes
            $table->date('fec_sancion')->nullable(); // v10
            $table->date('fec_promulga')->nullable(); // v11
            $table->date('fec_publica')->nullable(); // v12
            $table->string('dec_promulga', 10)->default(''); // v15
            $table->string('boletin_nro', 10)->nullable(); // v20^n
            $table->string('boletin_pag', 10)->nullable(); // v20^p
            $table->string('registro_t', 10)->default(''); // v21^t
            $table->string('registro_f', 10)->default(''); // v21^f
            // v22^n   acta_n @ actas
            // v22^r   acta_r @ actas
            // v22^t   acta_t @ actas
            $table->string('abrogacion_a', 20)->default(''); // v30^a
            $table->string('abrogacion_n', 20)->default(''); // v30^n
            $table->text('contenido')->nullable(); // v40
            // v41     descriptor (in table descriptores, string)
            $table->string('nro_tema', 50)->nullable(); // v42
            // v50^a   abroga_a (in table relaciones, 'O', 'A', string)
            // v50^n   abroga_n (in table relaciones, 'O', 'A', string)
            // v51^a   deroga_a (in table relaciones, 'O', 'D', string)
            // v51^n   deroga_n (in table relaciones, 'O', 'D', string)
            // v52^a   regla_a (in table relaciones, 'O', 'R', string)
            // v52^n   regla_n (in table relaciones, 'O', 'R', string)
            // v53^a   modif_a (in table relaciones, 'O', 'M', string)
            // v53^n   modif_n (in table relaciones, 'O', 'M', string)
            // v60^a   abroga_a (in table relaciones, 'D', 'A', string)
            // v60^n   abroga_n (in table relaciones, 'D', 'A', string)
            // v61^a   deroga_a (in table relaciones, 'D', 'D', string)
            // v61^n   deroga_n (in table relaciones, 'D', 'D', string)
            // v62^a   regla_a (in table relaciones, 'D', 'R', string)
            // v62^n   regla_n (in table relaciones, 'D', 'R', string)
            // v63     obs (in table observaciones, text)
            $table->string('alcance', 10)->default(''); // v70
            $table->string('caracter', 10)->default(''); // v71
            $table->string('recopila', 10)->default(''); // v72
            $table->date('fec_incluido')->nullable(); // v73
            $table->date('fec_excluido')->nullable(); // v74
            $table->string('sin_nro', 10)->default(''); // v80
            $table->string('ingresa', 10)->default(''); // v90
            // v95     nombre (in table procesamientos, string)
            $table->string('aprobado', 10)->default(''); // v100
            // v110    nombre (in table abstensiones, string)
            $table->string('ausentes', 10)->default(0); // v120
            $table->string('base', 10)->default('normas');
            $table->timestamps();

            // ---- Fake Search data ------------------------------------------
            $table->string('acto_nro', 20)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('normas');
    }
}