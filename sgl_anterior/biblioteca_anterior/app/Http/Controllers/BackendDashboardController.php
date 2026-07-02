<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BackendDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.dashboard.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publishConfirm()
    {
        // Guardo un código de confirmación (uso 'flash' para que viva una sola vez)
        request()->session()->flash('publish_code', Str::random(4));
        return view('backend.dashboard.publish');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request)
    {
        // Verifico variable de sesion
        if (! $request->session()->has('publish_code'))
            return redirect()
                ->route('backend.dashboard.publishconfirm')
                ->withErrors(['Código de validación de publicación no encontrado.']);

        // Verifico input de usuario
        Validator::make($request->all(), [
            'user_publish_code' => [
                'required',
                'string',
                'regex:/^[a-z0-9]{4}$/i',
                Rule::in([$request->session()->get('publish_code')])
            ]
        ])->validate();

        // Genero marca de publicacion
        $publicar_data = [
            'user_name' => Auth::user()->name,
            'user_email' => Auth::user()->email,
            'from_ip' => $request->ip(),
            'timestamp' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ];
        Storage::disk('local')->put('marca_publicacion.txt', json_encode($publicar_data));
        
        // Redireccion
        return redirect()
            ->route('backend.dashboard.publishconfirm')
            ->with('publish_status', 'Proceso de publicación de contenidos iniciado con éxito. En unos minutos los datos estarán disponibles en la web.');
    }
}