<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Mail\VehiculoRecibidoEmail;
use App\Mail\CancelSolServEmail;
use App\Mail\SolSerEmail;
use App\Mail\ProgramacionParafiscales;
use App\Mail\SustanciaControladaProgramada;
use App\Mail\AceiteUsadoProgramado;
use App\Mail\ServicioTipoRecorrido;
use App\audit;
use App\ProgramacionVehiculo;
use App\Vehiculo;
use App\Personal;
use App\SolicitudServicio;
use App\GenerSede;
use App\SolicitudResiduo;
use App\Documento;
use App\Docdato;
use App\Recolect;
use App\Cliente;
use App\Sede;
use App\Requerimiento;
use App\Observacion;
use App\Permisos;

class VehicProgController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC)){
			$programacions = DB::table('progvehiculos')
				->join('solicitud_servicios', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
				->join('clientes', 'solicitud_servicios.FK_SolSerCliente', 'clientes.ID_Cli')
				->select('progvehiculos.*', 'solicitud_servicios.ID_SolSer', 'solicitud_servicios.SolSerSlug', 'solicitud_servicios.SolSerStatus', 'solicitud_servicios.SolSerVehiculo', 'solicitud_servicios.SolSerConductor', 'clientes.CliName', 'clientes.CliCategoria')
				->where(function($query){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('progvehiculos.ProgVehDelete', 0);
					}
					if(in_array(Auth::user()->UsRol, Permisos::CONDUCTOR)||in_array(Auth::user()->UsRol2, Permisos::CONDUCTOR)){
						$query->where('progvehiculos.FK_ProgConductor', Auth::user()->FK_UserPers);
						$query->where('solicitud_servicios.SolSerStatus', 'Notificado');
						$query->whereDate('progvehiculos.ProgVehFecha', Carbon::today());
					}
					if(in_array(Auth::user()->UsRol, Permisos::TESORERIA)||in_array(Auth::user()->UsRol2, Permisos::TESORERIA)){
						$query->where('progvehiculos.ProgVehStatus', 'Pendiente');
					}
				})
				->where('clientes.CliCategoria', 'Cliente')
				->whereYear('progvehiculos.ProgVehFecha','2024')
				->get();
			$personals = DB::table('personals')
				->select('ID_Pers', 'PersFirstName', 'PersLastName')
				->get();
			$vehiculos = DB::table('vehiculos')
				->select('ID_Vehic','VehicPlaca')
				->get();

			$programacions = $programacions->map(function ($item) {
			  	$programacion = ProgramacionVehiculo::with(['puntosderecoleccion.generadors'])
			        ->where('ID_ProgVeh', $item->ID_ProgVeh)
			        // ->where('forevaluation', 0)
			        ->first();

		        $item->puntosderecoleccion =  $programacion->puntosderecoleccion;
			  	return $item;
			});
			// return $programacions;
			return view('ProgramacionVehicle.index', compact('programacions', 'personals', 'vehiculos'));
		}
		 /*Validacion para usuarios no permitidos en esta vista*/
		else{
			abort(403);
		}
			// return $programacions;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC) || in_array(Auth::user()->UsRol2, Permisos::TODOPROSARC)){
			$programacions = DB::table('progvehiculos')
				->join('solicitud_servicios', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
				->join('clientes', 'solicitud_servicios.FK_SolSerCliente', '=', 'clientes.ID_Cli')
				->select('progvehiculos.*', 'solicitud_servicios.ID_SolSer', 'clientes.CliName', 'clientes.CliCategoria')
				->where('progvehiculos.ProgVehDelete', 0)
				->where('clientes.CliCategoria', 'Cliente')
				->get();
			$transportadores = DB::table('clientes')
				->select('CliName', 'CliSlug')
				->where('CliCategoria', 'Transportador')
				->where('CliDelete', 0)
				->get();
			$mantenimientos = DB::table('mantenvehics')
				->join('vehiculos', 'mantenvehics.FK_VehMan', '=', 'vehiculos.ID_Vehic')
				->select('mantenvehics.*','vehiculos.VehicPlaca')
				->where('mantenvehics.HoraMavFin', '>', now())
				->get();
			/*conductores de prosarc*/
			$conductors = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('ID_Pers', 'PersFirstName', 'PersLastName')
				->where('CargName', 'Conductor')
				->where('ID_Cli', 1)
				->where('PersDelete', '!=' , 1)
				->get();
			/*auxiliares de prosarc*/
			$ayudantes = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('ID_Pers', 'PersFirstName', 'PersLastName')
				->whereIn('AreaName', ['Operaciones', 'Logística', 'Mantenimiento'])
				->whereNotIn('CargName', ["Asistente", 'Jefe', 'Supervisor'])
				->where('ID_Cli', 1)
				->where('PersDelete', '!=' , 1)
				->get();
			$vehiculos = DB::table('vehiculos')
				->select('ID_Vehic','VehicPlaca')
				->where('vehiculos.FK_VehiSede', 1)
				->where('VehicDelete', 0)
				->get();
			
			if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol2, Permisos::SUPERVISOR)){
				$serviciosnoprogramados = DB::table('solicitud_servicios')
				->join('clientes', 'solicitud_servicios.FK_SolSerCliente', '=', 'clientes.ID_Cli')
				->select('solicitud_servicios.ID_SolSer', 'solicitud_servicios.SolSerSlug', 'solicitud_servicios.SolSerTipo', 'clientes.CliName', 'clientes.CliCategoria')
				->where('SolSerDelete', 0)
				->where('SolSerStatus', 'Aprobado')
				->where('clientes.CliCategoria', 'Cliente')
				->whereIn('SolSerTipo', ['Cliente', 'Externo', 'Generador'])
				->orderBy('solicitud_servicios.updated_at', 'asc')
				->get();

			}else if(in_array(Auth::user()->UsRol, Permisos::AREALOGISTICA) || in_array(Auth::user()->UsRol2, Permisos::AREALOGISTICA)){
				$serviciosnoprogramados = DB::table('solicitud_servicios')
				->join('clientes', 'solicitud_servicios.FK_SolSerCliente', '=', 'clientes.ID_Cli')
				->select('solicitud_servicios.ID_SolSer', 'solicitud_servicios.SolSerSlug', 'solicitud_servicios.SolSerTipo', 'clientes.CliName', 'clientes.CliCategoria')
				->where('SolSerDelete', 0)
				->where('SolSerStatus', 'Aprobado')
				->where('clientes.CliCategoria', 'Cliente')
				->where('SolSerTipo', 'Interno')
				->orderBy('solicitud_servicios.updated_at', 'asc')
				->get();
			}else{
				$serviciosnoprogramados = DB::table('solicitud_servicios')
				->join('clientes', 'solicitud_servicios.FK_SolSerCliente', '=', 'clientes.ID_Cli')
				->select('solicitud_servicios.ID_SolSer', 'solicitud_servicios.SolSerSlug', 'solicitud_servicios.SolSerTipo', 'clientes.CliName', 'clientes.CliCategoria')
				->where('SolSerDelete', 0)
				->where('SolSerStatus', 'Aprobado')
				->where('clientes.CliCategoria', 'Cliente')
				->orderBy('solicitud_servicios.updated_at', 'asc')
				->get();
			}	
				/*return $programacions;*/
			return view('ProgramacionVehicle.create', compact('programacions', 'conductors', 'ayudantes', 'vehiculos', 'serviciosnoprogramados', 'mantenimientos', 'transportadores'));
		}
		 /*Validacion para usuarios no permitidos en esta vista*/
		else{
			abort(403);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
    {$validate = $request->validate([
		// 'ProgVehPrecintos'   =>   'max:16|min:1'
	]);
	// return $request;
	$programacion = new ProgramacionVehiculo();
	if(date('H', strtotime($request->input('ProgVehSalida'))) >= 12){
		$turno = "0";
	}
	else{
		$turno = "1";
	}
	$programacion->ProgVehTurno = $turno;
	$programacion->ProgVehFecha = $request->input('ProgVehFecha');
	$programacion->ProgVehSalida = $request->input('ProgVehFecha').' '.date('H:i:s', strtotime($request->input('ProgVehSalida')));
	$programacion->ProgVehEntrada = now();

	/*typetransportador = 0 -> transporte prosarc*/
	/*typetransportador = 1 -> transporte alquilado*/
	if(!is_null($request->input('typetransportador'))){
		if($request->input('typetransportador') == 0){
			/*ProgVehtipo = 0 -> transporte externo*/
			/*ProgVehtipo = 1 -> transporte interno prosarc*/
			/*ProgVehtipo = 2 -> transporte alquilado*/

			$programacion->ProgVehtipo = 1;
			$programacion->FK_ProgVehiculo = $request->input('FK_ProgVehiculo');

			// Asignar color con validación
			$color = $request->input('ProgVehColor');
			if(empty($color) || !preg_match('/#[a-f0-9]{6}/i', $color)) {
				$color = '#0000f6';
			}
			$programacion->ProgVehColor = $color;

			$programacion->FK_ProgConductor = $request->input('FK_ProgConductor');
			$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
			$conductor = Personal::select('PersFirstName', 'PersLastName')->where('ID_Pers', $request->input('FK_ProgConductor'))->first();
			$nomConduct = $conductor->PersFirstName." ".$conductor->PersLastName;
			$vehiculo = Vehiculo::select('VehicPlaca')->where('ID_Vehic', $request->input('FK_ProgVehiculo'))->first()->VehicPlaca;
			$transportador = DB::table('clientes')
				->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
				->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName', 'municipios.ID_Mun')
				->where('ID_Cli', 1)
				->first();
		}
		else{
			$programacion->ProgVehtipo = 2;
			$programacion->FK_ProgVehiculo = $request->input('vehicalqui');
			$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
			$programacion->ProgVehDocConductorEXT = $request->input('ProgVehDocConductorEXT');
			$programacion->ProgVehNameConductorEXT = $request->input('ProgVehNameConductorEXT');
			$programacion->ProgVehDocAuxiliarEXT = $request->input('ProgVehDocAuxiliarEXT');
			$programacion->ProgVehNameAuxiliarEXT = $request->input('ProgVehNameAuxiliarEXT');
			$programacion->ProgVehPlacaEXT = $request->input('ProgVehPlacaEXT');
			$programacion->ProgVehTipoEXT = $request->input('ProgVehTipoEXT');
			$programacion->ProgVehColor = '#FFFF00';
			$programacion->ProgVehPrecintos = $request->input('ProgVehPrecintos');
			if ($request->input('vehicalqui')!=null) {
				$vehiculo = Vehiculo::select('VehicPlaca')
				->where('ID_Vehic', $request->input('FK_ProgVehiculo'))
				->first();

			// Evitar error si no se encuentra el vehículo
			$placaVehiculo = $vehiculo ? $vehiculo->VehicPlaca : "No definido";
			}
			else{
				$vehiculo = null;
			}
			$nomConduct = null;
			$transportador = DB::table('clientes')
				->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
				->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName', 'municipios.ID_Mun')
				->where('CliSlug', $request->input('transport'))
				->first();
		}
		
	}
	else{
		$nomConduct = null;
		$vehiculo = null;
		$programacion->ProgVehtipo = 0;
		// Establecer color verde para vehículos externos
		$programacion->ProgVehColor = '#00a65a';
	}
	
	// Verificar que typetransportador existe y es un array antes de iterar
	if ($request->has('typetransportador') && is_array($request->input('typetransportador'))) {
		foreach ($request->input('typetransportador') as $key => $tipo) {
			$programacion = new ProgramacionVehiculo();
		
			$programacion->ProgVehTurno = (date('H', strtotime($request->input('ProgVehSalida'))) >= 12) ? "0" : "1";
			$programacion->ProgVehFecha = $request->input('ProgVehFecha');
			$programacion->ProgVehSalida = $request->input('ProgVehFecha') . ' ' . date('H:i:s', strtotime($request->input('ProgVehSalida')));
			$programacion->ProgVehEntrada = now();
			$programacion->ProgVehtipo = ($tipo == 0) ? 1 : 2; // 1 = Prosarc, 2 = Alquilado
			$programacion->FK_ProgServi = $request->input('FK_ProgServi');
			$programacion->ProgVehStatus = 'Autorizado';
		
			if ($tipo == 0) { // Transporte interno Prosarc
				$programacion->FK_ProgVehiculo = $request->input('FK_ProgVehiculo')[$key] ?? null;
				$programacion->FK_ProgConductor = $request->input('FK_ProgConductor')[$key] ?? null;
				$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante')[$key] ?? null;
			} else { // Transporte alquilado
				$programacion->ProgVehDocConductorEXT = $request->input('ProgVehDocConductorEXT')[$key] ?? null;
				$programacion->ProgVehNameConductorEXT = $request->input('ProgVehNameConductorEXT')[$key] ?? null;
				$programacion->ProgVehPlacaEXT = $request->input('ProgVehPlacaEXT')[$key] ?? null;
			}
		
			$programacion->save();
		}
	} else {
		// Si no hay typetransportador, guardamos el objeto programación para vehículos externos
		$programacion->FK_ProgServi = $request->input('FK_ProgServi');
		$programacion->ProgVehStatus = 'Autorizado';
		$programacion->save();
	}

	$SolicitudServicio = SolicitudServicio::where('ID_SolSer', $programacion->FK_ProgServi)->first();

	if ($SolicitudServicio->SolSerStatus == 'Aprobado') {

		$serviciovalidado = $request->input('FK_ProgServi');
		/*cuenta los diferentes generadores*/
		$generadoresdelasolicitud = GenerSede::whereHas('resgener.solres', function ($query) use ($serviciovalidado) {
			$query->where('solicitud_residuos.FK_SolResSolSer', $serviciovalidado);
		})
		->with(['resgener' => function ($query) use ($serviciovalidado){
			$query->with(['solres' => function ($query) use ($serviciovalidado){
				$query->where('FK_SolResSolSer', $serviciovalidado);
			}]);
			$query->whereHas('solres', function ($query) use ($serviciovalidado){
				$query->where('FK_SolResSolSer', $serviciovalidado);
			});
		}])
		->get();
	}
	// return $generadoresdelasolicitud;
	/*$programacion->ProgVehtipo: 0 = externo; 1 = Prosarc; 2 = alquilado; */
	/*SolSerTypeCollect: 99 = sedes generadores; 98 = sede cliente; 97 = direccion especifica; */
	/*DocType: 0 = manifiesto de carga; 1 = certificado; 2 = manifiesto de envio*/
	switch ($SolicitudServicio->SolSerTypeCollect) {
		/*recolectar en sedes de los generadores*/
		case '99':
			foreach ($generadoresdelasolicitud as $sole) {
				switch ($programacion->ProgVehtipo) {
					/*externo*/
					case '0':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 0;
						$nuevodoc->DocEspName = 0;
						$nuevodoc->DocEspValue = 0;
						$nuevodoc->DocObservacion = "no deberia generar documentos";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 0;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 0;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 0;
						// return $nuevodoc;

						break;

					/*Prosarc*/
					case '1':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 1;
						$nuevodoc->DocEspName = 1;
						$nuevodoc->DocEspValue = 1;
						$nuevodoc->DocObservacion = "ok generar varios documentos";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 1;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 1;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 1;
						// return $nuevodoc;

						break;

					/*Alquilado*/
					case '2':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 2;
						$nuevodoc->DocEspName = 2;
						$nuevodoc->DocEspValue = 2;
						$nuevodoc->DocObservacion = "ok generar varios documentos";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 2;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 2;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 2;
						// return $nuevodoc;

						break;

					default:
						return "no encontro el tipo de servicio";
						break;
				}
					$nuevodoc->save();

					foreach ($sole->resgener as $resgener) {
						foreach ($resgener->solres as $key) {
							$nuevodocdato = new Docdato;
							$nuevodocdato->FK_DatoDoc = $nuevodoc->ID_Doc;
							$nuevodocdato->FK_DatoSolRes = $key->ID_SolRes;
							$nuevodocdato->save();
						}
					}
				$puntoderecoleccion = new Recolect;
				$puntoderecoleccion->FK_ColectSgen = $sole->ID_GSede;
				$puntoderecoleccion->FK_ColectProg = $programacion->ID_ProgVeh;
				$puntoderecoleccion->save();

				// return $puntoderecoleccion;

			}
			break;

		/*recolectar en sede del cliente*/
		case '98':
				switch ($programacion->ProgVehtipo) {
					/*externo*/
					case '0':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 0;
						$nuevodoc->DocEspName = 0;
						$nuevodoc->DocEspValue = 0;
						$nuevodoc->DocObservacion = "no deberia generar documentos";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 0;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 0;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 0;
						// return $nuevodoc;

						break;

					/*Prosarc*/
					case '1':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 1;
						$nuevodoc->DocEspName = 1;
						$nuevodoc->DocEspValue = 1;
						$nuevodoc->DocObservacion = "documento con la sede del generador";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 1;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 1;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 1;
						// return $nuevodoc;

						break;

					/*Alquilado*/
					case '2':
						$nuevodoc = new Documento;
						$nuevodoc->DocType = 0;
						$nuevodoc->DocNumero = 2;
						$nuevodoc->DocEspName = 2;
						$nuevodoc->DocEspValue = 2;
						$nuevodoc->DocObservacion = "documento con la sede del generador";
						$nuevodoc->DocSlug = hash('sha256', rand().time());
						$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
						$nuevodoc->DocNumRm = 2;
						$nuevodoc->DocAuthHseq = 0;
						$nuevodoc->DocAuthJl = 0;
						$nuevodoc->DocAuthDp = 0;
						$nuevodoc->DocAnexo = 2;
						$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
						$nuevodoc->DocEspValue = 2;
						// return $nuevodoc;

						break;

					default:
						return "no encontro el tipo de servicio";
						break;
				}
					$nuevodoc->save();

				foreach ($generadoresdelasolicitud as $sole) {
					foreach ($sole->resgener as $resgener) {
						foreach ($resgener->solres as $key) {
							$nuevodocdato = new Docdato;
							$nuevodocdato->FK_DatoDoc = $nuevodoc->ID_Doc;
							$nuevodocdato->FK_DatoSolRes = $key->ID_SolRes;
							$nuevodocdato->save();
						}
					}
				}
				$puntoderecoleccion = new Recolect;
				$puntoderecoleccion->FK_ColectProg = $programacion->ID_ProgVeh;
				$puntoderecoleccion->save();



				// return $puntoderecoleccion;
			break;

		/*recolectar en direccion especifica*/
		case '97':
			switch ($programacion->ProgVehtipo) {
				/*externo*/
				case '0':
					$nuevodoc = new Documento;
					$nuevodoc->DocType = 0;
					$nuevodoc->DocNumero = 0;
					$nuevodoc->DocEspName = 0;
					$nuevodoc->DocEspValue = 0;
					$nuevodoc->DocObservacion = "no deberia generar documento";
					$nuevodoc->DocSlug = hash('sha256', rand().time());
					$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
					$nuevodoc->DocNumRm = 0;
					$nuevodoc->DocAuthHseq = 0;
					$nuevodoc->DocAuthJl = 0;
					$nuevodoc->DocAuthDp = 0;
					$nuevodoc->DocAnexo = 0;
					$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
					$nuevodoc->DocEspValue = 0;
					// return $nuevodoc;

					break;

				/*Prosarc*/
				case '1':
					$nuevodoc = new Documento;
					$nuevodoc->DocType = 0;
					$nuevodoc->DocNumero = 1;
					$nuevodoc->DocEspName = 1;
					$nuevodoc->DocEspValue = 1;
					$nuevodoc->DocObservacion = "documento con la direccion especifica";
					$nuevodoc->DocSlug = hash('sha256', rand().time());
					$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
					$nuevodoc->DocNumRm = 1;
					$nuevodoc->DocAuthHseq = 0;
					$nuevodoc->DocAuthJl = 0;
					$nuevodoc->DocAuthDp = 0;
					$nuevodoc->DocAnexo = 1;
					$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
					$nuevodoc->DocEspValue = 1;
					// return $nuevodoc;

					break;

				/*Alquilado*/
				case '2':
					$nuevodoc = new Documento;
					$nuevodoc->DocType = 0;
					$nuevodoc->DocNumero = 2;
					$nuevodoc->DocEspName = 2;
					$nuevodoc->DocEspValue = 2;
					$nuevodoc->DocObservacion = "documento con la direccion especifica";
					$nuevodoc->DocSlug = hash('sha256', rand().time());
					$nuevodoc->DocSrc = $nuevodoc->DocSlug.'.pdf';
					$nuevodoc->DocNumRm = 2;
					$nuevodoc->DocAuthHseq = 0;
					$nuevodoc->DocAuthJl = 0;
					$nuevodoc->DocAuthDp = 0;
					$nuevodoc->DocAnexo = 2;
					$nuevodoc->FK_CertSolser = $SolicitudServicio->ID_SolSer;
					$nuevodoc->DocEspValue = 2;
					// return $nuevodoc;

					break;

				default:
					return "no encontro el tipo de servicio";
					break;
			}
				$nuevodoc->save();

			foreach ($generadoresdelasolicitud as $sole) {
				foreach ($sole->resgener as $resgener) {
					foreach ($resgener->solres as $key) {
						$nuevodocdato = new Docdato;
						$nuevodocdato->FK_DatoDoc = $nuevodoc->ID_Doc;
						$nuevodocdato->FK_DatoSolRes = $key->ID_SolRes;
						$nuevodocdato->save();
					}
				}
			}
			$puntoderecoleccion = new Recolect;
			$puntoderecoleccion->FK_ColectProg = $programacion->ID_ProgVeh;
			$puntoderecoleccion->save();
			break;

		default:
			// return "el tipo de servicio es externo";
			break;
	}

	$SolicitudServicio->SolSerStatus = 'Programado';
	// Inicializar arreglos para almacenar múltiples valores
$nombresConductores = [];
$placasVehiculos = [];

// Verificar si hay múltiples transportadores
if(!is_null($request->input('typetransportador'))) {
    foreach ($request->input('typetransportador') as $key => $tipo) {
        if ($tipo == 0) {
            // Transporte Prosarc
            $conductor = Personal::select('PersFirstName', 'PersLastName')
                ->where('ID_Pers', $request->input('FK_ProgConductor')[$key] ?? null)
                ->first();
            
            $nombreConductor = $conductor ? $conductor->PersFirstName." ".$conductor->PersLastName : "No definido";
            $vehiculo = Vehiculo::select('VehicPlaca')
                ->where('ID_Vehic', $request->input('FK_ProgVehiculo')[$key] ?? null)
                ->first();
            
            $placaVehiculo = $vehiculo ? $vehiculo->VehicPlaca : "No definido";
        } elseif ($tipo == 1) {
            // Transporte Alquilado
            $nombreConductor = $request->input('ProgVehNameConductorEXT')[$key] ?? "No definido";
            $placaVehiculo = $request->input('ProgVehPlacaEXT')[$key] ?? "No definido";
        } else {
            $nombreConductor = "No definido";
            $placaVehiculo = "No definido";
        }

        // Agregar los valores al arreglo
        $nombresConductores[] = $nombreConductor;
        $placasVehiculos[] = $placaVehiculo;
    }
}

// Concatenar los valores en una sola cadena, separada por comas
$SolicitudServicio->SolSerConductor = implode(', ', $nombresConductores);
$SolicitudServicio->SolSerVehiculo = implode(', ', $placasVehiculos);
$SolicitudServicio->SolSerNameTrans = $transportador->CliName ?? "No asignado";
$SolicitudServicio->SolSerNitTrans = $transportador->CliNit ?? "No asignado";
$SolicitudServicio->SolSerAdressTrans = $transportador->SedeAddress ?? "No asignado";
$SolicitudServicio->SolSerCityTrans = $transportador->ID_Mun ?? "No asignado";


$SolicitudServicio = SolicitudServicio::where('ID_SolSer', $request->input('FK_ProgServi'))->first();
$SolicitudServicio->SolSerConductor = json_encode($nombresConductores);
$SolicitudServicio->SolSerVehiculo = json_encode($placasVehiculos);
$SolicitudServicio->SolSerStatus = 'Programado';
$SolicitudServicio->save();

	// return redirect()->route('vehicle-programacion.create');
	//return redirect()->route('vehicle-programacion.edit' , ['id' => $programacion->ID_ProgVeh]);
	return redirect()->route('vehicle-programacion.edit', ['vehicle_programacion' => $programacion->ID_ProgVeh]);
 }
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$Programacion = ProgramacionVehiculo::with('puntosderecoleccion')
		->where('ID_ProgVeh', $id)
		->first();
		// return $Programacion;
		if (!$Programacion) {
			abort(404, 'La programación de servicio no existe');
		}
		$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
			->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
			->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName', 'personals.PersEmail', 'personals.PersCellphone', 'personals.PersPhoneNumber', 'cargos.CargName', 'areas.AreaName')
			->where('solicitud_servicios.ID_SolSer', $Programacion->FK_ProgServi)
			->first();
		if (!$SolicitudServicio) {
			abort(404, 'La solicitud de servicio no existe');
		}
		$SolSerCollectAddress = $SolicitudServicio->SolSerCollectAddress;
		$SolSerConductor = $SolicitudServicio->SolSerConductor;
		// if($SolicitudServicio->SolSerTipo == 'Interno'){
		// 	$SolSerConductor = Personal::where('ID_Pers', $SolicitudServicio->SolSerConductor)->first();
		// }
		if($SolicitudServicio->SolSerTypeCollect == 98){
			$Address = Sede::select('SedeAddress')->where('ID_Sede',$SolicitudServicio->SolSerCollectAddress)->first();
			$SolSerCollectAddress = $Address->SedeAddress;
		}
		if($SolicitudServicio->SolSerCityTrans <> null){
			$Municipio1 = DB::table('municipios')
				->select('MunName')
				->where('ID_Mun', $SolicitudServicio->SolSerCityTrans)
				->first();
			$Municipio = $Municipio1->MunName;
		}
		if($SolicitudServicio->FK_SolSerCollectMun <> null){
			$Municipio2 = DB::table('municipios')
				->join('departamentos', 'municipios.FK_MunCity', '=', 'departamentos.ID_Depart')
				->select('municipios.MunName', 'departamentos.DepartName')
				->where('municipios.ID_Mun', $SolicitudServicio->FK_SolSerCollectMun)
				->first();
			$SolSerCollectAddress = $SolSerCollectAddress." (".$Municipio2->MunName." - ".$Municipio2->DepartName.")";
		}
		$TextProgramacion = null;
		if($SolicitudServicio->SolSerStatus == 'Programado'){
			setlocale(LC_ALL, "es_CO.UTF-8");
			// $Programacion = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)->where('ProgVehDelete', 0)->first();
			if(date('H', strtotime($Programacion->ProgVehSalida)) >= 12){
				$horas = " en las horas de la tarde";
			}
			else{
				$horas = " en las horas de la mañana";
			}
			$TextProgramacion = "El día ".strftime("%d", strtotime($Programacion->ProgVehFecha))." del mes de ".strftime("%B", strtotime($Programacion->ProgVehFecha)).$horas;
			$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
			// ->where('ProgVehEntrada', null)
			->where('ProgVehDelete', 0)
			->get();
			$ProgramacionesActivas = count(ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
			->where('ProgVehEntrada', null)
			->where('ProgVehDelete', 0)
			->get());
			// $ProgramacionesActivas = ($Programaciones);
		}
		$Cliente = DB::table('clientes')
			->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
			->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'sedes.SedePhone1', 'sedes.SedeExt1', 'sedes.SedePhone2', 'sedes.SedeExt2', 'municipios.MunName')
			->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
			->first();
		/*puntos de recoleccion de la solicitud array de ID_Gsede*/
		$puntos = $Programacion->puntosderecoleccion->map(function ($item) {
		  	return $item->ID_GSede;
		});
		$GenerResiduos = DB::table('solicitud_residuos')
			->distinct()
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('municipios', 'gener_sedes.FK_GSedeMun', '=', 'municipios.ID_Mun')
			->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress', 'municipios.MunName')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->whereIn('gener_sedes.ID_GSede', $puntos)
			->get();
		// $Residuos = DB::table('solicitud_residuos')
		// 	->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
		// 	->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
		// 	->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.RespelName','respels.RespelSlug', 'respels.RespelStatus')
		// 	->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
		// 	->get();
		$Residuosoriginal = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->join('requerimientos' , 'solicitud_residuos.FK_SolResRequerimiento', '=', 'requerimientos.ID_Req')
			->join('tratamientos' , 'requerimientos.FK_ReqTrata', '=', 'tratamientos.ID_Trat')
			->join('sedes' , 'tratamientos.FK_TratProv', '=', 'sedes.ID_Sede')
			->join('clientes' , 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
			->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'clientes.CliName')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->whereIn('residuos_geners.FK_SGener', $puntos)
			// ->where('requerimientos.ofertado', 1)
	        // ->where('forevaluation', 0)
			->get();
		$Observaciones = Observacion::where('FK_ObsSolSer', $SolicitudServicio->ID_SolSer)
			->first();	
		$Residuos = $Residuosoriginal->map(function ($item) {
		  $requerimientos = Requerimiento::with(['pretratamientosSelected'])
	        ->where('ID_Req', $item->FK_SolResRequerimiento)
	        // ->where('forevaluation', 0)
	        ->first();

	        $item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
		  	return $item;
		});
		return view('documentos.ManifCarga2', compact('SolicitudServicio','Observaciones', 'Residuos', 'GenerResiduos', 'Cliente', 'SolSerCollectAddress', 'SolSerConductor', 'TextProgramacion', 'Programacion', 'Municipio'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $programacion->FK_ProgServi
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(in_array(Auth::user()->UsRol, Permisos::ProgVehic2) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2)){
			
			$programacion = ProgramacionVehiculo::where('ID_ProgVeh', $id)->with('servicio')->first();
			if (!$programacion) {
				abort(404);
			}
			
			$vehiculos = DB::table('vehiculos')
				->select('ID_Vehic','VehicPlaca')
				->where('VehicDelete', 0)
				->get();

			//return $programacion;	
			if($programacion->ProgVehtipo <> 0){
				$SedeVehiculo = DB::table('sedes')
					->join('vehiculos', 'sedes.ID_Sede', '=', 'vehiculos.FK_VehiSede')
					->select('sedes.ID_Sede')
					->where('vehiculos.ID_Vehic', $programacion->FK_ProgVehiculo)
					->first();
					$Vehiculos2 = $SedeVehiculo 
					? DB::table('vehiculos')
						->select('VehicPlaca', 'ID_Vehic')
						->where('FK_VehiSede', $SedeVehiculo->ID_Sede)
						->where('VehicDelete', 0)
						->get()
					: collect();
				} elseif ($programacion->ProgVehtipo == 2) { // Vehículo alquilado
					// Para vehículos alquilados no se relaciona directamente la sede,
					// así que se obtiene el vehículo por su ID
					$Vehiculos2 = DB::table('vehiculos')
						->select('VehicPlaca', 'ID_Vehic')
						->where('ID_Vehic', $programacion->FK_ProgVehiculo)
						->where('VehicDelete', 0)
						->get();
					$SedeVehiculo = null;
				} else {
					$SedeVehiculo = null;
					$Vehiculos2 = collect();
				}
			/*conductores de prosarc*/
			$conductors = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('ID_Pers', 'PersFirstName', 'PersLastName')
				->where('CargName', 'Conductor')
				->where('ID_Cli', 1)
				->where('PersDelete', '!=' , 1)
				->get();
			/*auxiliares de prosarc*/
			$ayudantes = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('ID_Pers', 'PersFirstName', 'PersLastName', 'PersDocNumber')
				->whereIn('AreaName', ['Operaciones', 'Logística', 'Mantenimiento'])
				->whereNotIn('CargName', ["Asistente", 'Jefe'])
				->where('ID_Cli', 1)
				->get();

			$personalconparafiscales = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('ID_Pers', 'PersFirstName', 'PersLastName', 'PersDocNumber', 'PersParafiscales', 'PersParafiscalesExpire', 'AreaName')
				->whereIn('AreaName', ['Operaciones', 'Logística', 'Mantenimiento'])
				->whereNotIn('CargName', ["Asistente", 'Jefe'])
				->where('ID_Cli', 1)
				->where('PersParafiscales' , '!=', null)
				->get();

			$transportadores = DB::table('clientes')
				->select('CliName', 'CliSlug')
				->where('CliCategoria', 'Transportador')
				->where('CliDelete', 0)
				->get();

			$serviciovalidado = $programacion->FK_ProgServi;
			/*cuenta los diferentes generadores*/
			$generadoresdelasolicitud = GenerSede::whereHas('resgener.solres', function ($query) use ($serviciovalidado) {
			    $query->where('solicitud_residuos.FK_SolResSolSer', $serviciovalidado);
			})
			// ->with(['resgener' => function ($query) use ($serviciovalidado){
			//     $query->with(['solres' => function ($query) use ($serviciovalidado){
			//     	$query->where('FK_SolResSolSer', $serviciovalidado);
			//     }]);
			//     $query->whereHas('solres', function ($query) use ($serviciovalidado){
			//     	$query->where('FK_SolResSolSer', $serviciovalidado);
			//     });
			// }])
			->get('ID_GSede');

			/*se crea array de las sedes de generador en la solicitud de servicio*/
			$sedesgenerfiltrado = collect([]);
			foreach ($generadoresdelasolicitud as $key => $value) {
				$sedesgenerfiltrado = $sedesgenerfiltrado->concat([$value->ID_GSede]);
			}
			$recolectPointsService = GenerSede::with('generadors')
				->whereIn('ID_GSede', $sedesgenerfiltrado)
				->get();

			// return $recolectPointsService;

			$recolectPointsProg = Recolect::with('sedegen.generadors')
				->where('FK_ColectProg', $programacion->ID_ProgVeh)
				->get();

			/*return $programacion*/;

			return view('ProgramacionVehicle.edit', compact('programacion', 'vehiculos', 'conductors', 'ayudantes', 'Vehiculos2', 'transportadores', 'recolectPointsService', 'recolectPointsProg', 'personalconparafiscales'));
		}
		 /*Validacion para usuarios no permitidos en esta vista*/
		else{
			abort(403);
		}
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
		$validate = $request->validate([
			// 'ProgVehPrecintos'   =>   'max:16|min:6'
		]);

		$programacion = ProgramacionVehiculo::where('ID_ProgVeh', $id)->first();
		if (!$programacion) {
			abort(404);
		}

		$programacion->ProgVehFecha = $request->input('ProgVehFecha');
		$programacion->ProgVehExclusive = $request->input('ProgVehExclusive');
		$salida = date('H:i:s', strtotime($request->input('ProgVehSalida')));
		$llegada = date('H:i:s', strtotime($request->input('ProgVehEntrada')));
		
		// Manejo del turno
		$programacion->ProgVehTurno = ($salida >= 12) ? "0" : "1";
		$programacion->ProgVehSalida = $request->input('ProgVehFecha').' '.$salida;

		// Para vehículos Prosarc (tipo 1)
		if($programacion->ProgVehtipo == 1) {
			// Solo actualizar el color si se proporciona uno nuevo y el estado no está cerrado
			if($request->has('ProgVehColor') && $programacion->ProgVehStatus != 'Cerrada') {
				$color = $request->input('ProgVehColor');
				// Verificar que el color es válido, si no usar el azul predeterminado
				if(empty($color) || !preg_match('/#[a-f0-9]{6}/i', $color)) {
					$color = '#0000f6';
				}
				$programacion->ProgVehColor = $color;
			}
			
			if($request->input('ProgVehEntrada')) {
				$programacion->ProgVehEntrada = $request->input('ProgVehFecha').' '.$llegada;
				$programacion->progVehKm = $request->input('progVehKm');
				$programacion->ProgVehStatus = 'Cerrada';

				$vehiculo = Vehiculo::where('ID_Vehic', $request->input('FK_ProgVehiculo'))->first();
				$vehiculo->VehicKmActual = $request->input('progVehKm');
				$vehiculo->save();
			} else {
				$programacion->ProgVehEntrada = null;
				$programacion->progVehKm = null;
			}

			$conductor = Personal::select('PersFirstName', 'PersLastName')
				->where('ID_Pers', $request->input('FK_ProgConductor'))
				->first();
			$nomConduct = $conductor->PersFirstName." ".$conductor->PersLastName;
			
			$vehiculo = Vehiculo::select('VehicPlaca')
				->where('ID_Vehic', $request->input('FK_ProgVehiculo'))
				->first()
				->VehicPlaca;

			$programacion->FK_ProgVehiculo = $request->input('FK_ProgVehiculo');
			$programacion->FK_ProgConductor = $request->input('FK_ProgConductor');
			$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
			$programacion->ProgVehPrecintos = $request->input('ProgVehPrecintos');
		}
		// Para vehículos alquilados (tipo 2)
		else if($programacion->ProgVehtipo == 2) {
			// Mantener el color amarillo para vehículos alquilados
			$programacion->ProgVehColor = '#FFFF00';
			
			if($request->input('ProgVehEntrada')) {
				$programacion->ProgVehEntrada = $request->input('ProgVehFecha').' '.$llegada;
				$programacion->ProgVehStatus = 'Cerrada';
			}
			
			$programacion->ProgVehDocConductorEXT = $request->input('ProgVehDocConductorEXT');
			$programacion->ProgVehNameConductorEXT = $request->input('ProgVehNameConductorEXT');
			$programacion->ProgVehDocAuxiliarEXT = $request->input('ProgVehDocAuxiliarEXT');
			$programacion->ProgVehNameAuxiliarEXT = $request->input('ProgVehNameAuxiliarEXT');
			$programacion->ProgVehPlacaEXT = $request->input('ProgVehPlacaEXT');
			$programacion->ProgVehTipoEXT = $request->input('ProgVehTipoEXT');
			$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
			$programacion->FK_ProgVehiculo = $request->input('vehicalqui');
			$programacion->ProgVehPrecintos = $request->input('ProgVehPrecintos');
		}
		// En el método update, añadir sección para vehículos tipo 0
		else if($programacion->ProgVehtipo == 0) {
			// Color verde predeterminado para vehículos externos
			$programacion->ProgVehColor = '#00a65a';
			
			if($request->input('ProgVehEntrada')) {
				$programacion->ProgVehEntrada = $request->input('ProgVehFecha').' '.$llegada;
				$programacion->ProgVehStatus = 'Cerrada';
			}
			
			// Si hay vehicalqui, actualizar
			if($request->has('vehicalqui')) {
				$programacion->FK_ProgVehiculo = $request->input('vehicalqui');
			}
			
			// Si hay FK_ProgAyudante, actualizar
			if($request->has('FK_ProgAyudante')) {
				$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
			}
			
			// Si hay precintos, actualizar
			if($request->has('ProgVehPrecintos')) {
				$programacion->ProgVehPrecintos = $request->input('ProgVehPrecintos');
			}
		}

		$programacion->update();
		
		// Actualizar puntos de recolección si existen
		if($request->has('ProgGenerSedes')) {
			$programacion->puntosderecoleccion()->sync($request->input('ProgGenerSedes'));
		}

		// Actualizar información en la solicitud de servicio
		$SolicitudServicio = SolicitudServicio::where('ID_SolSer', $programacion->FK_ProgServi)->first();
		if($programacion->ProgVehtipo != 0) {
			$SolicitudServicio->SolSerConductor = isset($nomConduct) ? $nomConduct : null;
			$SolicitudServicio->SolSerVehiculo = isset($vehiculo) ? $vehiculo : null;
		}
		$SolicitudServicio->save();

		// Registrar la auditoría
		$log = new audit();
		$log->AuditTabla = "progvehiculos";
		$log->AuditType = "Modificado";
		$log->AuditRegistro = $programacion->ID_ProgVeh;
		$log->AuditUser = Auth::user()->email;
		$log->Auditlog = $request->all();
		$log->save();

		return redirect()->route('vehicle-programacion.edit', ['vehicle_programacion' => $id])
			->with('mensaje', __('adminlte::message.progvehceditsuccess'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$programacion = ProgramacionVehiculo::where('ID_ProgVeh', $id)->first();
		if (!$programacion) {
			abort(404, 'la programación de vehículo que trata de eliminar no se encuentra en la base de datos');
		}
		$SolicitudServicio = SolicitudServicio::where('ID_SolSer', $programacion->FK_ProgServi)->first();
		switch ($SolicitudServicio->SolSerStatus) {
			case 'Aprobado':
			case 'Programado':
				if ($programacion->ProgVehDelete == 0){
					$programacion->ProgVehDelete = 1;
					$programacion->save();
					$programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)->where('ProgVehDelete', 0)->where('ID_ProgVeh', '<>', $programacion->ID_ProgVeh)->first();
					if($programaciones == null && ($SolicitudServicio->SolSerStatus == 'Programado')){
						$SolicitudServicio->SolSerStatus = 'Aprobado';
						if($SolicitudServicio->SolSerTipo == 'Interno'){
							$transportador = DB::table('clientes')
								->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
								->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
								->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName',  'municipios.ID_Mun')
								->where('ID_Cli', 1)
								->first();
							$SolicitudServicio->SolSerConductor = null;
							$SolicitudServicio->SolSerVehiculo = null;
							$SolicitudServicio->SolSerNameTrans = $transportador->CliName;
							$SolicitudServicio->SolSerNitTrans = $transportador->CliNit;
							$SolicitudServicio->SolSerAdressTrans = $transportador->SedeAddress;
							$SolicitudServicio->SolSerCityTrans = $transportador->ID_Mun;
						}
						$SolicitudServicio->save();

						/*inicio de espacio para notificacion de programacion cancelada*/
						$SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudServicio->FK_SolSerCliente)->first();

						$emailCliente = DB::table('solicitud_servicios')
						->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
						->select('personals.PersEmail', 'solicitud_servicios.*')
						->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
						->first();


						// se establece la lista de destinatarios
						if ($SolicitudServicio['cliente']->CliComercial <> null) {
							$comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
							$destinatarios = [/*'dirtecnica@prosarc.com.co',*/
												'asistentelogistica@prosarc.com.co',
												'auxiliarlogistico@prosarc.com.co',
												'recepcionpda@prosarc.com.co',
												'gerenteplanta@prosarc.com.co',
												$emailCliente->PersEmail,
												$comercial->PersEmail
											];
						}else{
							$comercial = "";
							$destinatarios = ['dirtecnica@prosarc.com.co',
												'asistentelogistica@prosarc.com.co',
												'gerenteplanta@prosarc.com.co',
												'recepcionpda@prosarc.com.co',
												$emailCliente->PersEmail
											];
						}

						$SolicitudServicio['comercial'] = $comercial;
						$SolicitudServicio['personalcliente'] = Personal::where('ID_Pers', $SolicitudServicio->FK_SolSerPersona)->first();

						if ($SolicitudServicio->SolServMailCopia == "null") {
							Mail::to($destinatarios)
							->send(new CancelSolServEmail($SolicitudServicio));
						}else{
							Mail::to($destinatarios)
							->cc(json_decode($SolicitudServicio->SolServMailCopia))
							->send(new CancelSolServEmail($SolicitudServicio));
						}

						/*fin de espacio para notificacion de programacion cancelada*/

					}

					$log = new audit();
					$log->AuditTabla = "progvehiculos";
					$log->AuditType = "Eliminado";
					$log->AuditRegistro = $programacion->ID_ProgVeh;
					$log->AuditUser = Auth::user()->email;
					$log->Auditlog = $programacion->ProgVehDelete;
					$log->save();
					return redirect()->route('vehicle-programacion.create')->with('Delete', __('adminlte::message.progvehcdeletesuccess'));
				}
				else{
					$programacion->ProgVehDelete = 0;
					if($SolicitudServicio->SolSerStatus == 'Aprobado'){
						$SolicitudServicio->SolSerStatus = 'Programado';
						if($SolicitudServicio->SolSerTipo == 'Interno'){
							$SolicitudServicio->SolSerConductor = $programacion->FK_ProgConductor;
							$SolicitudServicio->SolSerVehiculo = $programacion->FK_ProgVehiculo;
						}
						$SolicitudServicio->save();
					}

					$log = new audit();
					$log->AuditTabla = "progvehiculos";
					$log->AuditType = "Restaurado";
					$log->AuditRegistro = $programacion->ID_ProgVeh;
					$log->AuditUser = Auth::user()->email;
					$log->Auditlog = $programacion->ProgVehDelete;
					$log->save();
					$programacion->save();
					return redirect()->route('vehicle-programacion.edit',['id' => $id])->with('mensaje', __('adminlte::message.progvehcdelete2success'));
				}
			break;

			case 'Notificado':
				if ($programacion->ProgVehDelete == 0){
					$programacion->ProgVehDelete = 1;
					$programacion->save();
					$programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)->where('ProgVehDelete', 0)->where('ID_ProgVeh', '<>', $programacion->ID_ProgVeh)->first();
					if($programaciones == null && ($SolicitudServicio->SolSerStatus == 'Notificado')){
						$SolicitudServicio->SolSerStatus = 'Aprobado';
						if($SolicitudServicio->SolSerTipo == 'Interno'){
							$transportador = DB::table('clientes')
								->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
								->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
								->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName',  'municipios.ID_Mun')
								->where('ID_Cli', 1)
								->first();
							$SolicitudServicio->SolSerConductor = null;
							$SolicitudServicio->SolSerVehiculo = null;
							$SolicitudServicio->SolSerNameTrans = $transportador->CliName;
							$SolicitudServicio->SolSerNitTrans = $transportador->CliNit;
							$SolicitudServicio->SolSerAdressTrans = $transportador->SedeAddress;
							$SolicitudServicio->SolSerCityTrans = $transportador->ID_Mun;
						}
						$SolicitudServicio->save();

						/*inicio de espacio para notificacion de programacion cancelada*/
						$SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudServicio->FK_SolSerCliente)->first();

						$emailCliente = DB::table('solicitud_servicios')
						->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
						->select('personals.PersEmail', 'solicitud_servicios.*')
						->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
						->first();


						// se establece la lista de destinatarios
						if ($SolicitudServicio['cliente']->CliComercial <> null) {
							$comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
							$destinatarios = ['dirtecnica@prosarc.com.co',
												'asistentelogistica@prosarc.com.co',
												'recepcionpda@prosarc.com.co',
												'gerenteplanta@prosarc.com.co',
												$emailCliente->PersEmail,
												$comercial->PersEmail
											];
						}else{
							$comercial = "";
							$destinatarios = ['dirtecnica@prosarc.com.co',
												'asistentelogistica@prosarc.com.co',
												'gerenteplanta@prosarc.com.co',
												'recepcionpda@prosarc.com.co',
												$emailCliente->PersEmail
											];
						}

						$SolicitudServicio['comercial'] = $comercial;
						$SolicitudServicio['personalcliente'] = Personal::where('ID_Pers', $SolicitudServicio->FK_SolSerPersona)->first();

						if ($SolicitudServicio->SolServMailCopia == "null") {
							Mail::to($destinatarios)
							->send(new CancelSolServEmail($SolicitudServicio));
						}else{
							Mail::to($destinatarios)
							->cc(json_decode($SolicitudServicio->SolServMailCopia))
							->send(new CancelSolServEmail($SolicitudServicio));
						}

						/*fin de espacio para notificacion de programacion cancelada*/

					}

					$log = new audit();
					$log->AuditTabla = "progvehiculos";
					$log->AuditType = "Eliminado";
					$log->AuditRegistro = $programacion->ID_ProgVeh;
					$log->AuditUser = Auth::user()->email;
					$log->Auditlog = $programacion->ProgVehDelete;
					$log->save();

					/*se guarda la observacion de la modificación del servicio hacia status aprobado*/
					$Observacion = new Observacion();
					$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
					$Observacion->ObsMensaje = 'Todas las programaciones de vehículos relativas a la Solicitud de Servicio N° '.$SolicitudServicio->ID_SolSer.' han sido canceladas por el área de Logistica... dado que la solicitud de servicio vuelve al status de Aprobado, el cliente puede nuevamente eliminar o editar la solicitud de servicio según necesite.';
					$Observacion->ObsTipo = 'prosarc';
					$Observacion->ObsRepeat = 1;
					$Observacion->ObsDate = now();
					$Observacion->ObsUser = Auth::user()->email;
					$Observacion->ObsRol = Auth::user()->UsRol;
					$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
					$Observacion->save();

					return redirect()->route('vehicle-programacion.create')->with('Delete', __('adminlte::message.progvehcdeletesuccess'));
				}
				else{
					$programacion->ProgVehDelete = 0;
					$programacion->save();

					if($SolicitudServicio->SolSerTipo == 'Interno'){
						$SolicitudServicio->SolSerConductor = $programacion->FK_ProgConductor;
						$SolicitudServicio->SolSerVehiculo = $programacion->FK_ProgVehiculo;
						$SolicitudServicio->save();
					}

					$log = new audit();
					$log->AuditTabla = "progvehiculos";
					$log->AuditType = "Restaurado";
					$log->AuditRegistro = $programacion->ID_ProgVeh;
					$log->AuditUser = Auth::user()->email;
					$log->Auditlog = $programacion->ProgVehDelete;
					$log->save();

					return redirect()->route('vehicle-programacion.edit',['id' => $id])->with('mensaje', __('adminlte::message.progvehcdelete2success'));
				}
			break;

			default:
			abort(503, 'No se puede eliminar la programación de vehículo ya que la solicitud de servicio fue completada');
				break;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateStatus(Request $request, $id)
	{
		$programacion = ProgramacionVehiculo::where('ID_ProgVeh', $id)->first();
		if (!$programacion) {
			abort(404, 'la programación de vehículo que trata de actualizar no se encuentra en la base de datos');
		}
		$SolicitudServicio = SolicitudServicio::with(['SolicitudResiduo.requerimiento.respel'])
		->where('ID_SolSer', $programacion->FK_ProgServi)->first();
		$programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
		->where('ProgVehDelete', 0)
		->get();

		$SolicitudServicio->SolSerStatus='Notificado';
		$SolicitudServicio->SolSerDescript=$request->input('solserdescript');
		$SolicitudServicio->save();

		// $residuosdelservicio = $SolicitudServicio->SolicitudResiduo()->requerimiento()->respel()->get();

		$cantidadDeResiduosControlados = 0;

		foreach ($SolicitudServicio->SolicitudResiduo as $key => $value) {
			$respel = $value->requerimiento->respel;
			if ($respel->SustanciaControlada == 1) {
				$cantidadDeResiduosControlados++;
			}
		}

		$cantidadDeAceitesUsados = 0;

		$programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
		->where('ProgVehDelete', 0)
		//->where('FK_ProgVehiculo', 8)
		->first();
		
		foreach ($SolicitudServicio->SolicitudResiduo as $key => $value){
			$respel = $value->requerimiento->respel;
			
			if($respel->AceiteUsado == 1 && $programaciones->FK_ProgVehiculo == 8){
				$cantidadDeAceitesUsados++;
				}
			}	

		// return $cantidadDeResiduosControlados;

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Notificado";
		$log->AuditRegistro=$SolicitudServicio->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$SolicitudServicio->SolSerStatus;
		$log->save();

		/*se guarda la observacion inicial de la creación del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
		$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
		$Observacion->ObsTipo = 'prosarc';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
		$Observacion->save();

		$email = DB::table('solicitud_servicios')
			->join('progvehiculos', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->select('personals.*', 'solicitud_servicios.*', 'progvehiculos.ProgVehFecha', 'progvehiculos.ProgVehSalida', 'clientes.CliName', 'clientes.CliComercial')
			->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
			->where('progvehiculos.FK_ProgServi', '=', $SolicitudServicio->ID_SolSer)
			->where('progvehiculos.ProgVehDelete', 0)
			->first();
		$comercial = Personal::where('ID_Pers', $email->CliComercial)->first();
		$destinatarios = ['asistentelogistica@prosarc.com.co',
							'auxiliarpda@prosarc.com.co',
							'recepcionpda@prosarc.com.co',
							'asistentepda@prosarc.com.co',
							$comercial->PersEmail
						];
		if ($cantidadDeResiduosControlados > 0) {
			//enviar notificacion de servicion con sustancia controladas
			Mail::to('dirtecnica@prosarc.com.co')->cc(['sistemas@prosarc.com.co','asistentepda@prosarc.com.co', 'logistica@prosarc.com.co', 'recepcionpda@prosarc.com.co', 'auxiliarpda@prosarc.com.co', 'jefedetratamiento@prosarc.com.co', 'asistentelogistica@prosarc.com.co', 'auxiliarlogistico@prosarc.com.co', 'conciliaciones@prosarc.com.co'])->send(new SustanciaControladaProgramada($email, $SolicitudServicio));
		}else{
			array_push($destinatarios, 'dirtecnica@prosarc.com.co');
		}

		if($cantidadDeAceitesUsados > 0){
			//enviar notificacion de servicion con Aceite Usado
			Mail::to('dirtecnica@prosarc.com.co')->cc(['sistemas@prosarc.com.co', 'logistica@prosarc.com.co', 'asistentelogistica@prosarc.com.co', 'auxiliarlogistico@prosarc.com.co', ])->send(new AceiteUsadoProgramado($email, $SolicitudServicio));
		}else{
			array_push($destinatarios, 'dirtecnica@prosarc.com.co');
		}
		if ($SolicitudServicio->SolServMailCopia == "null") {
			if ($programacion->ProgVehExclusive == 0) {
				Mail::to($email->PersEmail)->cc($destinatarios)->send(new ServicioTipoRecorrido($email));
			}else{
				Mail::to($email->PersEmail)->cc($destinatarios)->send(new SolSerEmail($email));
			}

		}else{
			foreach (json_decode($SolicitudServicio->SolServMailCopia) as $key => $value) {
				array_push($destinatarios, $value);
			}
			if ($programacion->ProgVehExclusive == 0) {
				Mail::to($email->PersEmail)->cc($destinatarios)->send(new ServicioTipoRecorrido($email));
			}else{
				Mail::to($email->PersEmail)->cc($destinatarios)->send(new SolSerEmail($email));
			}
		}

		if($request->input('destino') == 'vehiprog-edit'){
			return redirect()->route('vehicle-programacion.edit', ['vehicle_programacion' => $id]);
			//return redirect()->route('vehicle-programacion.edit', ['id' => $id]);
		}else{
			return redirect()->route('vehicle-programacion.index');
		}


	}

		/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function añadirVehiculo(Request $request, $id)
	{
		// return $request;
		$programacion = new ProgramacionVehiculo();
		if(date('H', strtotime($request->input('ProgVehSalida'))) >= 12){
			$turno = "0";
		}
		else{
			$turno = "1";
		}
		$programacion->ProgVehTurno = $turno;
		$programacion->ProgVehFecha = $request->input('ProgVehFecha');
		$programacion->ProgVehSalida = $request->input('ProgVehFecha').' '.date('H:i:s', strtotime($request->input('ProgVehSalida')));
		/*typetransportador = 0 -> transporte prosarc*/
		/*typetransportador = 1 -> transporte alquilado*/
		if(!is_null($request->input('typetransportador'))){
			if($request->input('typetransportador') == 0){
				/*ProgVehtipo = 0 -> transporte externo*/
				/*ProgVehtipo = 1 -> transporte interno prosarc*/
				/*ProgVehtipo = 2 -> transporte alquilado*/

				$programacion->ProgVehtipo = 1;
				$programacion->FK_ProgVehiculo = $request->input('FK_ProgVehiculo');
				
				// Validar el color para vehículos de tipo 1 (prosarc)
				$color = $request->input('ProgVehColor');
				if(empty($color) || !preg_match('/#[a-f0-9]{6}/i', $color)) {
					$color = '#0000f6'; // Azul por defecto para vehículos Prosarc
				}
				$programacion->ProgVehColor = $color;
				
				$programacion->FK_ProgConductor = $request->input('FK_ProgConductor');
				$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
				$conductor = Personal::select('PersFirstName', 'PersLastName')->where('ID_Pers', $request->input('FK_ProgConductor'))->first();
				$nomConduct = $conductor->PersFirstName." ".$conductor->PersLastName;
				$vehiculo = Vehiculo::select('VehicPlaca')->where('ID_Vehic', $request->input('FK_ProgVehiculo'))->first()->VehicPlaca;
				$transportador = DB::table('clientes')
					->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
					->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
					->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName', 'municipios.ID_Mun')
					->where('ID_Cli', 1)
					->first();
			}
			else{
				$programacion->ProgVehtipo = 2;
				$programacion->FK_ProgVehiculo = $request->input('vehicalqui');
				$programacion->FK_ProgAyudante = $request->input('FK_ProgAyudante');
				$programacion->ProgVehDocConductorEXT = $request->input('ProgVehDocConductorEXT');
				$programacion->ProgVehNameConductorEXT = $request->input('ProgVehNameConductorEXT');
				$programacion->ProgVehDocAuxiliarEXT = $request->input('ProgVehDocAuxiliarEXT');
				$programacion->ProgVehNameAuxiliarEXT = $request->input('ProgVehNameAuxiliarEXT');
				$programacion->ProgVehPlacaEXT = $request->input('ProgVehPlacaEXT');
				$programacion->ProgVehTipoEXT = $request->input('ProgVehTipoEXT');
				$programacion->ProgVehColor = '#FFFF00'; // Amarillo para vehículos alquilados
				if ($request->input('vehicalqui')!=null) {
					$vehiculo = Vehiculo::select('VehicPlaca')->where('ID_Vehic', $request->input('vehicalqui'))->first()->VehicPlaca;
				}else{
					$vehiculo = null;
				}

				$nomConduct = null;
				$transportador = DB::table('clientes')
					->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
					->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
					->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName', 'municipios.ID_Mun')
					->where('CliSlug', $request->input('transport'))
					->first();
			}
		}
		else{
			$nomConduct = null;
			$vehiculo = null;
			$programacion->ProgVehtipo = 0;
			// Establecer color verde (#00a65a) para vehículos externos (tipo 0)
			$programacion->ProgVehColor = '#00a65a';
		}
		$programacion->FK_ProgServi = $id;
		$programacion->ProgVehDelete = 0;
		$programacion->ProgVehStatus =  $request->input('StatusProgServi');
		$programacion->ProgVehExclusive =  $request->input('ProgVehExclusive');
		$programacion->save();

		// $SolicitudServicio = SolicitudServicio::where('ID_SolSer', $programacion->FK_ProgServi)->first();
		// $SolicitudServicio->SolSerStatus = 'Programado';
		// if(!is_null($request->input('typetransportador'))){
		// 	$SolicitudServicio->SolSerConductor = $nomConduct;
		// 	$SolicitudServicio->SolSerVehiculo = $vehiculo;
		// 	$SolicitudServicio->SolSerNameTrans = $transportador->CliName;
		// 	$SolicitudServicio->SolSerNitTrans = $transportador->CliNit;
		// 	$SolicitudServicio->SolSerAdressTrans = $transportador->SedeAddress;
		// 	$SolicitudServicio->SolSerCityTrans = $transportador->ID_Mun;
		// }
		// $SolicitudServicio->save();

		return redirect()->route('vehicle-programacion.index');

	}

			/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function sendParafiscales(Request $request, $id)
	{
		$programacion = ProgramacionVehiculo::where('ID_ProgVeh', $id)->first();
		// return $request;
		if (!$programacion) {
			abort(404, 'la programación de vehículo que trata de notificar no se encuentra en la base de datos');
		}
		$SolicitudServicio = SolicitudServicio::where('ID_SolSer', $programacion->FK_ProgServi)->first();
		$SolicitudServicio->SolSerDescript=$request->input('solserdescript');
		$SolicitudServicio->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="parafiscales";
		$log->AuditRegistro=$SolicitudServicio->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$SolicitudServicio->SolSerStatus;
		$log->save();

		/*se guarda la observacion inicial de la creación del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = 'Parafiscales Enviados';
		$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
		$Observacion->ObsTipo = 'prosarc';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
		$Observacion->save();

		if($request->input('destino') == 'vehiprog-edit'){
			$email = DB::table('solicitud_servicios')
				->join('progvehiculos', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
				->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
				->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
				->select('personals.PersEmail', 'solicitud_servicios.*', 'progvehiculos.ProgVehFecha', 'progvehiculos.ProgVehSalida', 'clientes.CliName', 'clientes.CliComercial')
				->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
				->where('progvehiculos.FK_ProgServi', '=', $SolicitudServicio->ID_SolSer)
				->where('progvehiculos.ProgVehDelete', 0)
				->first();
			$comercial = Personal::where('ID_Pers', $email->CliComercial)->first();
			$destinatarios = ['asistentelogistica@prosarc.com.co',
								$comercial->PersEmail
							];

			$Parafiscales = [];

			foreach ($request->input('personalParafiscales') as $key => $value) {
				$pdf = Personal::where('ID_Pers', $value)->first('PersParafiscales');
				if ($pdf) {
					$Parafiscales = Arr::prepend($Parafiscales, $pdf->PersParafiscales);
				}
			}

			if ($SolicitudServicio->SolServMailCopia == "null") {
				Mail::to($email->PersEmail)
				->cc($destinatarios)
				->send(new ProgramacionParafiscales($email, $Observacion, $programacion, $Parafiscales));
			}else{
				foreach (json_decode($SolicitudServicio->SolServMailCopia) as $key => $value) {
					array_push($destinatarios, $value);
				}
				Mail::to($email->PersEmail)
				->cc($destinatarios)
				->send(new ProgramacionParafiscales($email, $Observacion, $programacion, $Parafiscales));
			}

			return redirect()->route('vehicle-programacion.edit', ['id' => $id]);
		}else{
			return redirect()->route('email-solser', ['slug' => $SolicitudServicio->SolSerSlug]);
		}
	}
}
