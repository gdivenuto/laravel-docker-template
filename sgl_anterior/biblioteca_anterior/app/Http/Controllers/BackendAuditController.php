<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackendAuditController extends Controller
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
        return view('backend.audit.index');
    }

    /**
     * Returns statistic data
     * 
     * @return [type] [description]
     */
    public function dtGetAuditJson() 
    {
        $audit = new Audit();
        
        $info = $audit->getInfo();

        return datatables()->of($info)->toJson();
    }    

}
