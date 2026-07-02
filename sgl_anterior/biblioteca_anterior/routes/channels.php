<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
/*
// Comentada la ruta para que la llamada a 'php artisan route:cache' pueda ser ejecutada 
// sin errores. 
// Ver: https://stackoverflow.com/questions/45266254/laravel-unable-to-prepare-route-for-serialization-uses-closure

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/
