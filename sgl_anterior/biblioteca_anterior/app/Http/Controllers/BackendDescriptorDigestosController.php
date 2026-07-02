<?php

namespace App\Http\Controllers;

use App\Digesto;
use App\Descriptor;
use App\DescriptorBase;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Http\Requests\DescriptorDigestosSaveRequest;

class BackendDescriptorDigestosController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Digesto $digesto)
    {
        return view('backend.descriptor_digestos.edit', compact('digesto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DescriptorDigestosSaveRequest $request, Digesto $digesto)
    {
        $descriptores_data = [];
        foreach ($request->descriptores as $d) {
            $descriptores_data[$d['descriptor_id']] = ['condicion' => $d['descriptor_condicion']];
        }
        
        $digesto->descriptores()->sync($descriptores_data);

        return redirect()
            ->route('backend.descriptordigestos.edit', ['digesto' => $digesto])
            ->with('save_status', 'Descriptores guardados con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
