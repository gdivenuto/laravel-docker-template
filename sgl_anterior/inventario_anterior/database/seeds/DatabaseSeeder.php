<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            AreasTableSeeder::class,
            ResponsablesTableSeeder::class,
            ActivoTiposTableSeeder::class,
            ActivoTiposExtra1TableSeeder::class/*,
            ActivosTableSeeder::class*/
        ]);
    }
}
