<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;  
use App\audit;
use App\Recurso;
use App\SolicitudServicio;
use App\cliente;
use App\ResiduosGener;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $Recursos2 = DB::table('recursos')
            ->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'recursos.FK_ResGer')
            ->select('recursos.*')
            ->get();

        $Recursos = DB::table('residuos_geners')
            ->join('respels', 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
            ->select('respels.RespelName', 'residuos_geners.*')
            ->get();

        return view('recursos.index', compact('Recursos', 'Recursos2'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Clientes = cliente::all();

        $ResGeners = DB::table('residuos_geners')
            ->join('respels', 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
            ->select('respels.RespelName', 'residuos_geners.FK_SolSer', 'residuos_geners.ID_SGenerRes')
            ->get();

        $Recursos = DB::table('recursos')
            ->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'recursos.FK_ResGer')
            ->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'residuos_geners.FK_SolSer')
            ->select('recursos.*', 'residuos_geners.FK_SolSer')
            ->get();

        return view('recursos.create', compact('ResGeners', 'Clientes', 'Recursos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasfile('RecSrc')) {

        foreach($request->RecSrc as $file){ 
        
        $Recurso = new Recurso();
        
        $name = time().$file->getClientOriginalName();
        $Extension = $file->extension();
        $file->move(public_path().'/Recursos/'.$request->input("RecName").time(),$name);
        $Src = 'Recursos/'.$request->input("RecName").time().'/'.$name;
        $Folder = $request->input("RecName").time();
        
        // return $Folder;
        $Recurso->RecName = $request->input("RecName");
        $Recurso->RecTipo = $request->input("RecTipo");
        $Recurso->RecCarte = $request->input("RecCarte");
        $Recurso->RecRmSrc = $Folder;
        $Recurso->SlugRec = 'Slug'. $request->input("RecName").$name;
        $Recurso->RecSrc = $Src;
        $Recurso->RecFormat = '.'.$Extension;
        $Recurso->FK_ResGer = $request->input("FK_ResGer");
        $Recurso->save();
        }
    }
        return redirect()->route('recurso.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Recursos = DB::table('recursos')
            ->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'recursos.FK_ResGer')
            ->select('recursos.*')
            ->where('FK_ResGer', '=', $id)
            ->get();
            
        return view('recursos.show', compact('Recursos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ResGeners = ResiduosGener::where('ID_SGenerRes', $id)->first();

        $Clientes = cliente::all();

        $SolServs = SolicitudServicio::all();

        // $SolServs = DB::table('solicitud_servicios')
        // ->join('residuos_geners', 'solicitud_servicios.ID_SolSer', '=', 'residuos_geners.FK_Respel')
        // ->join('respels', 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
        // ->select('respels.RespelName', 'solicitud_servicios.*')
        // // ->where('residuos_geners.ID_SGenerRes', $id)
        // ->get();

        return view('recursos.edit', compact('ResGeners', 'Clientes', 'GenerRes', 'SolServs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Recursos = Recurso::where('FK_ResGer', $id)->update(['RecName' => $request->input("RecName")]);

        $ResGeners = ResiduosGener::where('ID_SGenerRes', $id)->first();
        $ResGeners->FK_SolSer = $request->input("FK_SolSer");
        $ResGeners->save(); 

        $log = new audit();
        $log->AuditTabla="residuos_geners y recurso";
        $log->AuditType="Modificado";
        $log->AuditRegistro = $ResGeners->ID_SGenerRes;
        $log->AuditUser=Auth::user()->email;
        $log->Auditlog=$request->all();
        $log->save();

        return redirect()->route('recurso.index');
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
