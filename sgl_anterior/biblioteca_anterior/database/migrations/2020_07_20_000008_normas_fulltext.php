<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NormasFulltext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ---- Full Text Search Indexes ----------------------------------
        DB::statement('ALTER TABLE normas ADD FULLTEXT fulltext_index (origen, nro_hcd, exped, bloque, dec_promulga, boletin_nro, boletin_pag, abrogacion_a, abrogacion_n, contenido, nro_tema, recopila, sin_nro, ingresa, aprobado, acto_nro)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
