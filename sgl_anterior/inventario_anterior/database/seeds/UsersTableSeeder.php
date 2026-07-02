<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
			[ 'id' => 1, 'name' => 'Informática', 'email' => 'informatica@concejomdp.gov.ar', 'can_transfer_login' => false, 'password' => bcrypt('12345678') ],
            [ 'id' => 2, 'name' => 'SGL', 'email' => 'sgl@concejomdp.gov.ar', 'can_transfer_login' => true, 'password' => bcrypt('eZrh9YfOVB7d8UqyjMywMu30Ca3LuTA2MqitXifARoY=') ],
		];

        foreach ($data as $value)
			App\User::create($value);
    }
}
