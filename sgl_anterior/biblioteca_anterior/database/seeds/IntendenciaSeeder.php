<?php

use Illuminate\Database\Seeder;

class IntendenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
			[ 'id' => 1,  'intendente' => 'Roig', 'nro' => 1, 'fec_desde' => '1983-12-10', 'fec_hasta' => '1991-12-09' ],
			[ 'id' => 2,  'intendente' => 'Russak', 'nro' => 1, 'fec_desde' => '1991-12-10', 'fec_hasta' => '1995-12-09' ],
			[ 'id' => 3,  'intendente' => 'Aprile', 'nro' => 1, 'fec_desde' => '1995-12-10', 'fec_hasta' => '1999-12-09' ],
			[ 'id' => 4,  'intendente' => 'Aprile', 'nro' => 2, 'fec_desde' => '1999-12-10', 'fec_hasta' => '2002-04-01' ],
			[ 'id' => 5,  'intendente' => 'Katz', 'nro' => 1, 'fec_desde' => '2002-04-01', 'fec_hasta' => '2003-12-09' ],
			[ 'id' => 6,  'intendente' => 'Katz', 'nro' => 2, 'fec_desde' => '2003-12-10', 'fec_hasta' => '2007-12-09' ],
			[ 'id' => 7,  'intendente' => 'Pulti', 'nro' => 1, 'fec_desde' => '2007-12-10', 'fec_hasta' => '2011-12-09' ],
			[ 'id' => 8,  'intendente' => 'Pulti', 'nro' => 2, 'fec_desde' => '2011-12-10', 'fec_hasta' => '2015-12-09' ],
			[ 'id' => 9,  'intendente' => 'Arroyo', 'nro' => 1, 'fec_desde' => '2015-12-10', 'fec_hasta' => '2019-12-09' ],
			[ 'id' => 10, 'intendente' => 'Montenegro', 'nro' => 1, 'fec_desde' => '2019-12-10', 'fec_hasta' => null ],
		];

        foreach ($data as $value)
			App\Intendencia::create($value);
    }
}
