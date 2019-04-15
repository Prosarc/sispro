<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Area;
use App\audit;
use Illuminate\Support\Facades\Auth;



class AreaController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        if(Auth::user()->UsRol === "Programador"){
            $Areas = DB::table('areas')
                ->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
                ->select('areas.ID_Area', 'areas.AreaName','areas.AreaDelete','sedes.SedeName')
                ->get();
        	return view('areas.index', compact('Areas'));
        }
        $Areas = DB::table('areas')
                ->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
                ->select('areas.ID_Area', 'areas.AreaName','areas.AreaDelete','sedes.SedeName')
                ->where('areas.AreaDelete',0)
                ->get();
        return view('areas.index', compact('Areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $Sedes = DB::table('sedes')
            ->select('ID_Sede', 'SedeName')
            ->get();
    	return view('areas.create', compact('Sedes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $area = new Area();
        $area->AreaName = $request->input('NomArea');
        $area->FK_AreaSede= $request->input('AreaSede');
        $area->AreaDelete = 0;
        $area->save();

        $log = new audit();
        $log->AuditTabla="areas";
        $log->AuditType="Creado";
        $log->AuditRegistro=$area->ID_Area;
        $log->AuditUser=Auth::user()->email;
        $log->Auditlog=$request->all();
        $log->save();
        
        return redirect()->route('areas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     **/
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $Areas = Area::where('ID_Area', $id)->first();
        $Sedes = DB::table('sedes')
            ->select('ID_Sede', 'SedeName')
            ->get();
        return view('areas.edit', compact('Sedes', 'Areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        /*return $request;*/
        $Area = Area::where('ID_Area', $id)->first();
        $Area->AreaName = $request->input('NomArea');
        $Area->FK_AreaSede = $request->input('AreaSede');
        $Area->save();

        $log = new audit();
        $log->AuditTabla="areas";
        $log->AuditType="Modificado";
        $log->AuditRegistro=$Area->ID_Area;
        $log->AuditUser=Auth::user()->email;
        $log->Auditlog=$request->all();
        $log->save();

        return redirect()->route('areas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $Area = Area::where('ID_Area', $id)->first();
            if ($Area->AreaDelete == 0) {
                $Area->AreaDelete = 1;
            }
            else{
                $Area->AreaDelete = 0;
            }
        $Area->save();

        $log = new audit();
        $log->AuditTabla = "areas";
        $log->AuditType = "Eliminado";
        $log->AuditRegistro = $Area->ID_Area;
        $log->AuditUser = Auth::user()->email;
        $log->Auditlog = $Area->AreaDelete;
        $log->save();

        return redirect()->route('areas.index');
    }
}
