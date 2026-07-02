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
        $this->call(UsersTableSeeder::class);
        $this->call(IntendenciaSeeder::class);
        
        // El seed del digesto se ejecuta manualmente en la migracion
        //$this->call(DigestoSeeder::class);
    }
}
