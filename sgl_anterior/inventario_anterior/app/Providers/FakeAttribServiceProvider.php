<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FakeAttribServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path() . '/Helpers/FakeAttrib.php';
    }
}