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
			[ 'id' => 1, 'name' => 'Informática', 'email' => 'informatica@concejomdp.gov.ar', 'can_transfer_login' => false, 'password' => bcrypt('m3d10c4f3gr4nd3!') ],
			[ 'id' => 2, 'name' => 'Biblioteca', 'email' => 'biblioteca@concejomdp.gov.ar', 'can_transfer_login' => false, 'password' => bcrypt('!-l4ur1t4-!') ],
            [ 'id' => 3, 'name' => 'SGL', 'email' => 'sgl@concejomdp.gov.ar', 'can_transfer_login' => true, 'password' => bcrypt('31GAVmL+KBg9HKQ626vMEF2cN9CttLwv5SaNCXHURv4=') ],
		];

        foreach ($data as $value)
			App\User::create($value);
    }
}
