<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Digesto;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Acceso publico para el demo
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard.index');
    }

    public function showDBSelector()
    {   
        $bases = [
            'normas' => 'Todas las Normas sancionadas vigentes o no, incluye normativa particular y transitoria a partir de 1994.',
            'sesiones' => 'Normas y temas del Honorable Cuerpo, por ejemplo, cuestión previa, homenajes y pedidos de informes.',
            'decretos' => 'Decretos del Departamento Ejecutivo.',
            'todas' => 'Buscar en toda las bases de datos de la Biblioteca-HCD.'
        ];

        // ---- Lista de digestos
        $digestos = Digesto::with('descriptores:id,tag');
            
        // Si estoy en modo 'NO-BACKEND', filtro por digesto publicado
        if (! config('params.backend_enabled'))
            $digestos = $digestos->where('publicado', true);
            
        $digestos = $digestos->orderBy('nombre')
            ->get();

        return view('dashboard.dbselector', compact('bases','digestos'));
    }
}
