<?php

use Illuminate\Database\Seeder;

class DigestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ---- Digesto 'basico' ----------------------------------------------
        $dig = App\Digesto::create([
            'id' => 1,  
            'nombre' => 'digesto', 
            'publicado' => true,
            'descripcion' => 'Todas las Normas sancionadas vigentes de carácter general y permanente.', 
            'filtro' => '[["base","=","normas"],["dec_promulga","<>","esp-pro"],["recopila","=","s"]]'
        ]);

        // ---- Digesto Ambiental ---------------------------------------------
        $dig = App\Digesto::create([
            'id' => 2,
            'nombre' => 'digesto ambiental', 
            'publicado' => true,
            //'descripcion' => 'Todas las Normas sancionadas vigentes de carácter general y permanente relacionadas a cuestiones del medio ambiente. <br/><a href="/storage/digesto_ambiental_municipal.pdf" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver documentación</a>', 
            'descripcion' => 'Todas las Normas sancionadas vigentes de carácter general y permanente relacionadas a cuestiones del medio ambiente.', 
            'filtro' => '[["base","=","normas"],["dec_promulga","<>","esp-pro"],["recopila","=","s"]]'
        ]);

        // Criterios obligatorios
        // como es un 'like', los descriptores pueden escribirse con '%'
        $crit_desc_and = [
            'AMBIENTE'
        ];

        $desc_and = null;
        foreach ($crit_desc_and as $crit) {
            $desc_and = is_null($desc_and)
                ? App\Descriptor::where('tag', Str::contains($crit, '%') ? 'like' : '=', $crit)
                : $desc_and->orWhere('tag', Str::contains($crit, '%') ? 'like' : '=', $crit);
        }
        $desc_and = $desc_and->get()->pluck('id')->toArray();

        foreach ($desc_and as $d_id) 
        {
            $dig->descriptores()->attach($d_id, ['condicion' => 'and']);
        }

        // Criterios opcionales
        $crit_desc_or = [
            'AGROQUIMICOS',
            'AGUA CORRIENTE',
            'ANIMALES',
            'ARBOLADO',
            'AREAS NATURALES PROTEGIDAS',
            'BIENES PATRIMONIALES',
            'CAMBIO CLIMÁTICO',
            'CANTERAS',
            'COMBUSTIBLES',
            'CONTAMINACION',
            'EDUCACION AMBIENTAL',
            'ENERGIA',
            'ESPACIOS VERDES',
            'FAUNA SILVESTRE',
            'HUMEDALES',
            'IMPACTO AMBIENTAL',
            'INDUSTRIAS',
            'INSTRUMENTOS',
            'OSSE',
            'PLAGAS',
            'PLAGUICIDAS',
            'RECURSOS HIDRICOS',
            'RESERVA FORESTAL',
            'RESIDUOS',
            'SERVICIO SANITARIO',
            'SUELOS',
            'TURISMO'
        ];

        $desc_or = null;
        foreach ($crit_desc_or as $crit) {
            $desc_or = is_null($desc_or)
                ? App\Descriptor::where('tag', Str::contains($crit, '%') ? 'like' : '=', $crit)
                : $desc_or->orWhere('tag', Str::contains($crit, '%') ? 'like' : '=', $crit);
        }
        $desc_or = $desc_or->get()->pluck('id')->toArray();

        foreach ($desc_or as $d_id) 
        {
            $dig->descriptores()->attach($d_id, ['condicion' => 'or']);
        }

        // ---- Digesto Discapacidad ------------------------------------------
        $dig = App\Digesto::create([
            'id' => 3,
            'nombre' => 'digesto discapacidad', 
            'publicado' => true,
            //'descripcion' => 'Todas las Normas sancionadas vigentes de carácter general y permanente relacionadas a cuestiones de personas con discapacidad. <br/><a href="/storage/digesto_discapacidad_municipal.pdf" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver documentación</a>', 
            'descripcion' => 'Todas las Normas sancionadas vigentes de carácter general y permanente relacionadas a cuestiones de personas con discapacidad.',
            'filtro' => '[["base","=","normas"],["dec_promulga","<>","esp-pro"],["recopila","=","s"]]'
        ]);

        // Criterios obligatorios
        // como es un 'like', los descriptores pueden escribirse con '%'
        $crit_desc_and = [
            'PERSONAS CON DISCAPACIDAD'
        ];

        $desc_and = null;
        foreach ($crit_desc_and as $crit) {
            $desc_and = is_null($desc_and)
                ? App\Descriptor::where('tag', Str::contains($crit, '%') ? 'like' : '=', $crit)
                : $desc_and->orWhere('tag', Str::contains($crit, '%') ? 'like' : '=', $crit);
        }
        $desc_and = $desc_and->get()->pluck('id')->toArray();

        foreach ($desc_and as $d_id) 
        {
            $dig->descriptores()->attach($d_id, ['condicion' => 'and']);
        }

        // Criterios opcionales
        $crit_desc_or = [
            'ACCESIBILIDAD',
            'ADHESION',
            'ADMINISTRACION DE PERSONAL',
            'BALNEARIOS',
            'BARRERAS ARQUITECTONICAS',
            'ESPECTACULOS PUBLICOS',
            'PERMISOS ESPECIALES DE ESTACIONAMIENTO',
            'PERRO LAZARILLO',
            'QUIOSCOS',
            'SALUD PUBLICA',
            'SIMBOLOS',
            'TASAS Y DERECHOS MUNICIPALES',
            'TAXIS',
            'TRANSPORTE COLECTIVO',
            'TRANSPORTE PRIVADO'
        ];

        $desc_or = null;
        foreach ($crit_desc_or as $crit) {
            $desc_or = is_null($desc_or)
                ? App\Descriptor::where('tag', Str::contains($crit, '%') ? 'like' : '=', $crit)
                : $desc_or->orWhere('tag', Str::contains($crit, '%') ? 'like' : '=', $crit);
        }
        $desc_or = $desc_or->get()->pluck('id')->toArray();

        foreach ($desc_or as $d_id) 
        {
            $dig->descriptores()->attach($d_id, ['condicion' => 'or']);
        }
    }
}
