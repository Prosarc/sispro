<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\SolServStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Http\Controllers\userController;
use App\Cliente;
use App\FirmasServicios;
use Permisos;
use PDF;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

class RecibomaterialController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
         if(Auth::user()->UsRol == 'Cliente'){
            $UserSedeID = DB::table('personals')
                ->join('cargos', 'cargos.ID_Carg', 'personals.FK_PersCargo')
                ->join('areas', 'areas.ID_Area', 'cargos.CargArea')
                ->join('sedes', 'sedes.ID_Sede', 'areas.FK_AreaSede')
                ->join('clientes', 'clientes.ID_Cli', 'sedes.FK_SedeCli')
                ->where('personals.ID_Pers', Auth::user()->FK_UserPers)
                ->where('clientes.CliStatus', 'Autorizado')
                ->value('clientes.ID_Cli');

                $rms = DB::table('firmas_servicio')    
                ->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'firmas_servicio.FK_SolSer')
                ->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
                ->leftJoin('progvehiculos', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
                ->select('firmas_servicio.*', 'solicitud_servicios.SolSerSlug', 'clientes.CliName', 
                    'progvehiculos.ProgVehEntrada as ProgVehFecha')
                ->where('firmas_servicio.FirmaCliente', '!=', 0)
                ->orderBy('firmas_servicio.created_at', 'desc')
                ->groupBy('firmas_servicio.FK_SolSer', 'firmas_servicio.created_at', 'firmas_servicio.ID_FirmaServ', 
                    'firmas_servicio.FirmaDriver', 'firmas_servicio.FirmaCliente', 'firmas_servicio.FirmaAuxiliar', 
                    'firmas_servicio.SlugFirmas', 'firmas_servicio.updated_at', 'solicitud_servicios.SolSerSlug', 
                    'clientes.CliName', 'progvehiculos.ProgVehEntrada')
                ->get();
         } else {

            $rms = DB::table('firmas_servicio')    
                ->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'firmas_servicio.FK_SolSer')
                ->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
                ->join('progvehiculos', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
                ->select('firmas_servicio.*', 'solicitud_servicios.SolSerSlug', 'clientes.CliName', 'progvehiculos.ProgVehFecha')
                ->where('firmas_servicio.FirmaCliente', '!=', 0)
                ->orderBy('firmas_servicio.created_at', 'desc')
                ->groupBy('firmas_servicio.FK_SolSer', 'firmas_servicio.created_at', 'firmas_servicio.ID_FirmaServ', 'firmas_servicio.FK_SolSer', 'firmas_servicio.FirmaDriver', 'firmas_servicio.FirmaCliente', 'firmas_servicio.FirmaAuxiliar', 'firmas_servicio.SlugFirmas', 'firmas_servicio.updated_at', 'solicitud_servicios.SolSerSlug', 'clientes.CliName', 'progvehiculos.ProgVehFecha')
                ->get();
         }
                return view('recibomaterial.index', compact('rms'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tarifa  $tarifa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarifa $tarifa)
    {  
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tarifa  $tarifa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarifa $tarifa)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tarifa  $tarifa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarifa $tarifa)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tarifa  $tarifa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarifa $tarifa)
    {
    }

}    