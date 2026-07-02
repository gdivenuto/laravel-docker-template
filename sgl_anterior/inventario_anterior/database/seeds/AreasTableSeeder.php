<?php

use Illuminate\Database\Seeder;

use App\Area;

class AreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Datos exportados con PHPMyAdmin
        $data = array(
			array('ca_id' => '01000000','ca_nombre' => 'Planta Permanente','ca_tipo' => 'P','ca_depende_de' => '0','ca_habilitado' => '1'),
			array('ca_id' => '01010000','ca_nombre' => 'Dirección de Administración','ca_tipo' => 'P','ca_depende_de' => '01000000','ca_habilitado' => '1'),
			array('ca_id' => '01010100','ca_nombre' => 'Departamento de Mesa de Entradas','ca_tipo' => 'P','ca_depende_de' => '01010000','ca_habilitado' => '1'),
			array('ca_id' => '01010200','ca_nombre' => 'Departamento de Despacho','ca_tipo' => 'P','ca_depende_de' => '01010000','ca_habilitado' => '1'),
			array('ca_id' => '01010300','ca_nombre' => 'Departamento de Mayordomía','ca_tipo' => 'P','ca_depende_de' => '01010000','ca_habilitado' => '1'),
			array('ca_id' => '01010400','ca_nombre' => 'Departamento de Informática','ca_tipo' => 'P','ca_depende_de' => '01010000','ca_habilitado' => '1'),
			array('ca_id' => '01010500','ca_nombre' => 'Departamento de Prensa y Protocolo','ca_tipo' => 'P','ca_depende_de' => '01010000','ca_habilitado' => '1'),
			array('ca_id' => '01020000','ca_nombre' => 'Dirección de Actas, Referencia Legislativa y Digesto','ca_tipo' => 'P','ca_depende_de' => '01000000','ca_habilitado' => '1'),
			array('ca_id' => '01020100','ca_nombre' => 'Departamento de Referencia Legislativa y Digesto','ca_tipo' => 'P','ca_depende_de' => '01020000','ca_habilitado' => '1'),
			array('ca_id' => '01020200','ca_nombre' => 'Departamento de Actas','ca_tipo' => 'P','ca_depende_de' => '01020000','ca_habilitado' => '1'),
			array('ca_id' => '01030000','ca_nombre' => 'Dirección de Comisiones ','ca_tipo' => 'P','ca_depende_de' => '01000000','ca_habilitado' => '1'),
			array('ca_id' => '01100000','ca_nombre' => 'Defensoría del Pueblo','ca_tipo' => 'P','ca_depende_de' => '01000000','ca_habilitado' => '1'),
			array('ca_id' => '02000000','ca_nombre' => 'Planta Política','ca_tipo' => 'B','ca_depende_de' => '0','ca_habilitado' => '1'),
			array('ca_id' => '02010000','ca_nombre' => 'Bloque Acción Marplatense','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02020000','ca_nombre' => 'Bloque Agrupación Atlántica','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02030000','ca_nombre' => 'Bloque Frente para la Victoria','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02040000','ca_nombre' => 'Bloque Union Civica Radical','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02050000','ca_nombre' => 'Bloque Frente es Posible','ca_tipo' => 'B','ca_depende_de' => '0','ca_habilitado' => '0'),
			array('ca_id' => '02060000','ca_nombre' => 'Movimiento Peronista','ca_tipo' => 'B','ca_depende_de' => '0','ca_habilitado' => '0'),
			array('ca_id' => '02070000','ca_nombre' => 'Bloque Frente Renovador','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02080000','ca_nombre' => 'Bloque Histórico','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02090000','ca_nombre' => 'Bloque PRO','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02100000','ca_nombre' => 'Bloque Agrupación Atlántica-PRO','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02110000','ca_nombre' => 'Bloque CREAR Mar del Plata','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02120000','ca_nombre' => 'Bloque Unidad Ciudadana','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02130000','ca_nombre' => 'Bloque 1País','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02140000','ca_nombre' => 'Bloque Vamos Juntos','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02150000','ca_nombre' => 'Bloque Coalición Cívica - ARI - Mar del Plata','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1'),
			array('ca_id' => '02160000','ca_nombre' => 'Bloque Frente de Todos','ca_tipo' => 'B','ca_depende_de' => '02000000','ca_habilitado' => '1')
		);

        foreach ($data as $v)
			$a = Area::updateOrCreate(
				['cod_area' => $v['ca_id']],
				[
					'nombre' => $v['ca_nombre'],
					'tipo' => $v['ca_tipo'],
					'cod_area_padre' => $v['ca_depende_de']
				]);

    }
}
