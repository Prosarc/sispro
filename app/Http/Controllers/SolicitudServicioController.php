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
use App\Http\Controllers\SolicitudResiduoController;
use App\Mail\NewSolServEmail;
use App\Mail\SolSerLeftRespel;
use App\Mail\NewSolServProsarcEmail;
use App\Mail\ServicioReversado;
use App\Mail\CertUpdated;
use App\Mail\SolSerRM;
use App\Mail\SolserAuditar;
use App\Mail\SustanciaControladaProgramada;
use App\Mail\AceiteUsadoProgramado;
use App\Mail\SustanciaControladaCreada;
use App\Mail\AceiteUsadoCreado;
use App\SolicitudServicio;
use App\SolicitudResiduo;
use App\audit;
use App\Sede;
use App\GenerSede;
use App\Respel;
use App\ResiduosGener;
use App\Cliente;
use App\Tratamiento;
use App\Generador;
use App\Personal;
use App\Departamento;
use App\Municipio;
use App\Tarifa;
use App\Rango;
use App\Certificado;
use App\Certdato;
use App\CertificadoExpress;
use App\CertExpressdato;
use App\Manifiesto;
use App\Manifdato;
use App\Requerimiento;
use App\Documento;
use App\Docdato;
use App\ProgramacionVehiculo;
use App\RequerimientosCliente;
use App\Observacion;
use App\CTarifa;
use App\Prefactura;
use App\PrefacturaTratamiento;
use App\PrefacturaResiduo;
use App\FirmasServicios;
use Permisos;
use PDF;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;


class SolicitudServicioController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC) || in_array(Auth::user()->UsRol2, Permisos::TODOPROSARC)){
			return view('solicitud-serv.año');
		}else{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->join('solicitud_residuos', 'solicitud_residuos.FK_SolResSolSer', '=', 'solicitud_servicios.ID_SolSer')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->select('solicitud_servicios.ID_SolSer',
			'solicitud_servicios.SolSerStatus',
			'solicitud_servicios.SolSerTipo',
			'solicitud_servicios.SolSerAuditable',
			'solicitud_servicios.SolSerConductor',
			'solicitud_servicios.SolSerVehiculo',
			'solicitud_servicios.SolSerSlug',
			'solicitud_servicios.created_at',
			'solicitud_servicios.updated_at',
			'solicitud_servicios.SolSerDelete',
			'solicitud_servicios.SolResAuditoriaTipo',
			'solicitud_servicios.SolSerNameTrans',
			'solicitud_servicios.SolSerNitTrans',
			'solicitud_servicios.SolSerAdressTrans',
			'solicitud_servicios.SolSerTypeCollect',
			'solicitud_servicios.SolSerCollectAddress',
			'solicitud_servicios.SolServCertStatus',
			'solicitud_servicios.SolNumeroFactura',
			'clientes.CliName',
			'clientes.CliSlug',
			'clientes.CliStatus',
			'clientes.TipoFacturacion',
			'clientes.CliCategoria',
			'personals.PersFirstName',
			'personals.PersLastName',
			'personals.PersSlug',
			'personals.PersEmail',
			'personals.PersCellphone',
			'Comercial.ID_Pers as ComercialID_Pers',
			'Comercial.PersFirstName as ComercialPersFirstName',
			'Comercial.PersLastName as ComercialPersLastName',
			'Comercial.PersSlug as ComercialPersSlug',
			'Comercial.PersEmail as ComercialPersEmail',
			'Comercial.PersCellphone as ComercialPersCellphone',
			'gener_sedes.GSedeName')
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli',userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			//->whereBetween('solicitud_servicios.created_at',['2022-01-01 00:00:00','2022-12-31 23:59:00'])
			->orderBy('created_at', 'desc')
			->distinct()
			->get();
		$Cliente = Cliente::select('CliName','ID_Cli', 'CliStatus')->where('ID_Cli',userController::IDClienteSegunUsuario())->first();
		foreach ($Servicios as $servicio) {
			if($servicio->SolSerTypeCollect == 98){
				$Address = Sede::select('SedeAddress')->where('ID_Sede',$servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}

			/* validacion para encontrar la fecha de recepción en planta del servicio */
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if($fechaRecepcion){
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			}else{
				$servicio->recepcion = null;
			}
		}
		if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
			return view('solicitud-serv.index', compact('Servicios', 'Cliente'));
		}else{
			return view('solicitud-serv.indexprosarc', compact('Servicios', 'Cliente'));
		}
	}

	}

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2020()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2020)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2020', compact('Servicios', 'Cliente'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2021()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2021)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2021', compact('Servicios', 'Cliente'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2022()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2022)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2022', compact('Servicios', 'Cliente'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2023()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2023)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2023', compact('Servicios', 'Cliente'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2024()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2024)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2024', compact('Servicios', 'Cliente'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function soli2025()
	{
		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select(
				'solicitud_servicios.ID_SolSer',
				'solicitud_servicios.SolSerStatus',
				'solicitud_servicios.SolSerTipo',
				'solicitud_servicios.SolSerAuditable',
				'solicitud_servicios.SolSerConductor',
				'solicitud_servicios.SolSerVehiculo',
				'solicitud_servicios.SolSerSlug',
				'solicitud_servicios.created_at',
				'solicitud_servicios.updated_at',
				'solicitud_servicios.SolSerDelete',
				'solicitud_servicios.SolResAuditoriaTipo',
				'solicitud_servicios.SolSerNameTrans',
				'solicitud_servicios.SolSerNitTrans',
				'solicitud_servicios.SolSerAdressTrans',
				'solicitud_servicios.SolSerTypeCollect',
				'solicitud_servicios.SolSerCollectAddress',
				'solicitud_servicios.SolServCertStatus',
				'solicitud_servicios.SolNumeroFactura',
				'clientes.CliName',
				'clientes.CliSlug',
				'clientes.CliStatus',
				'clientes.TipoFacturacion',
				'clientes.CliCategoria',
				'personals.PersFirstName',
				'personals.PersLastName',
				'personals.PersSlug',
				'personals.PersEmail',
				'personals.PersCellphone',
				'Comercial.ID_Pers as ComercialID_Pers',
				'Comercial.PersFirstName as ComercialPersFirstName',
				'Comercial.PersLastName as ComercialPersLastName',
				'Comercial.PersSlug as ComercialPersSlug',
				'Comercial.PersEmail as ComercialPersEmail',
				'Comercial.PersCellphone as ComercialPersCellphone'
			)
			->where(function($query){
				if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
					$query->where('ID_Cli', userController::IDClienteSegunUsuario());
				}
				if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
					if(!in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR)){
						$query->where('solicitud_servicios.SolSerStatus', 'Pendiente');
						$query->orWhere('solicitud_servicios.SolServCertStatus', 1);
					}
				}
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
					if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL)){
						$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
					}
				}
			})
			->where('CliCategoria', 'Cliente')
			->whereYear('solicitud_servicios.created_at', 2025)
			->orderBy('created_at', 'desc')
			->get();
	
		$Cliente = Cliente::select('CliName', 'ID_Cli', 'CliStatus')->where('ID_Cli', userController::IDClienteSegunUsuario())->first();
	
		foreach ($Servicios as $servicio) {
			if ($servicio->SolSerTypeCollect == 98) {
				$Address = Sede::select('SedeAddress')->where('ID_Sede', $servicio->SolSerCollectAddress)->first();
				$servicio->SolSerCollectAddress = $Address->SedeAddress;
			}
	
			// validación para encontrar la fecha de recepción en planta del servicio
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if ($fechaRecepcion) {
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			} else {
				$servicio->recepcion = null;
			}
		}
			return view('solicitud-serv.2025', compact('Servicios', 'Cliente'));
	}
	 /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function almacenamientogeneral(){

		// Primera consulta
			$solicitudservicios = DB::table('solicitud_residuos')
			->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'solicitud_residuos.FK_SolResSolSer')
			->join('progvehiculos', 'progvehiculos.FK_ProgServi', '=', 'solicitud_servicios.ID_SolSer')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('respels', 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->join('requerimientos', 'requerimientos.FK_ReqRespel', '=', 'respels.ID_Respel')
			->join('tratamientos', 'tratamientos.ID_Trat', '=', 'requerimientos.FK_ReqTrata')
			->join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
			->select(
				'solicitud_residuos.SolResKgConciliado',
				'solicitud_residuos.SolResKgTratado',
				'solicitud_servicios.ID_SolSer',
				'clientes.CliName',
				'generadors.GenerName',
				'respels.RespelName',
				'respels.YRespelClasf4741',
				'respels.ARespelClasf4741',
				'tratamientos.TratName',
				'tratamientos.ID_Trat',
				'progvehiculos.ProgVehFecha'
			)
			->whereBetween('solicitud_servicios.created_at', ['2024-01-01 00:00:00', '2024-12-31 23:59:00'])
			->distinct()
			->get();

			// Obtener una lista única de ID_Respel de la primera consulta
			$tratamientosIds = $solicitudservicios->pluck('ID_Trat')->unique()->filter(); // Filtramos valores vacíos

			//return $tratamientosIds;
			// Verificamos si $respelIds no está vacío antes de la segunda consulta
			$gestor = collect(); // Creamos una colección vacía en caso de que no haya resultados
			if ($tratamientosIds->isNotEmpty()) {
			$gestor = DB::table('tratamientos')
				->join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
				->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->select('tratamientos.*', 'clientes.CliShortname')
				->whereIn('tratamientos.ID_Trat', $tratamientosIds)
				->distinct()
				->get();
			}

				$Solicitudes = SolicitudServicio::with(['Personal', 'cliente', 'municipio', 'solicitudResiduo' => function ($query) {
					$query->whereColumn('SolResKgConciliado', '!=', 'SolResKgTratado');
				}])
				->whereBetween('solicitud_servicios.created_at', ['2024-01-01 00:00:00', '2024-12-31 23:59:00'])				
				->orderBy('solicitud_servicios.created_at', 'desc')
				->get();
			
				
			/*se inicializan las variables para el calculo de totales */
		$total['conciliado'] = 0;
		$total['tratado'] = 0;
		$cantidadesXtratamiento = [];

		/* se itera sobre todos los residuos de las solicitudes de servicio */
		foreach ($Solicitudes as $servicio) {
			foreach ($servicio->SolicitudResiduo as $residuo) {
				$collection = collect($cantidadesXtratamiento);

				/* si el tratamiento existe en la lista se suman las cantidadesxtratamiento y los totales correspondientes */
				if ($collection->has($residuo->requerimiento->tratamiento->TratName)) {
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] + $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] + $residuo->SolResKgTratado;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}else{
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $residuo->SolResKgTratado;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}		
			}
		}	

		$totalgestor['conciliado'] = 0;
		$totalgestor['tratado'] = 0;
		$cantidadesXgestor = [];

		$gestrata = DB::table('solicitud_servicios')
			->join('solicitud_residuos', 'solicitud_residuos.FK_SolResSolSer', '=', 'solicitud_servicios.ID_SolSer')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('respels', 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->join('requerimientos', 'requerimientos.FK_ReqRespel', '=', 'respels.ID_Respel')
			->join('tratamientos', 'tratamientos.ID_Trat', '=', 'requerimientos.FK_ReqTrata')
			->join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
			->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->whereBetween('solicitud_servicios.created_at', ['2024-01-01 00:00:00', '2024-12-31 23:59:00'])
			->select('solicitud_residuos.*', 'clientes.CliShortname') // Selección de columnas necesarias
			->orderBy('solicitud_servicios.created_at', 'desc')
			->distinct()
			->get();

		$cantidadesXgestor = [];
		$totalgestor = ['conciliado' => 0, 'tratado' => 0];

		
		foreach ($gestrata as $servicio) {
			$clienteClave = $servicio->CliShortname;
			if (isset($cantidadesXgestor[$clienteClave])) {
				$cantidadesXgestor[$clienteClave]['conciliado'] += $servicio->SolResKgConciliado;
				$cantidadesXgestor[$clienteClave]['tratado'] += $servicio->SolResKgTratado;
			} else {
				$cantidadesXgestor[$clienteClave] = [
					'conciliado' => $servicio->SolResKgConciliado,
					'tratado' => $servicio->SolResKgTratado
				];
			}
			$totalgestor['conciliado'] += $servicio->SolResKgConciliado;
			$totalgestor['tratado'] += $servicio->SolResKgTratado;
		}

		return view('solicitud-serv.Inventario.almacenamientogeneral', compact('solicitudservicios', 'gestor', 'cantidadesXtratamiento', 'total', 'cantidadesXgestor', 'totalgestor', 'cantidadesXgestor', 'totalgestor'));
	
}

	
	public function indexalmacenados()
	{
		$SolicitudesServicios = SolicitudServicio::with(['Personal', 'cliente', 'municipio', 'SolicitudResiduo' => function ($query) {
							$query->where('SolResKgConciliado', '!=', 'SolResKgTratado');
							}])
							->whereBetween('solicitud_servicios.created_at',['2022-01-01 00:00:00','2022-12-31 23:59:00'])
							->orderBy('created_at', 'desc')
							->get();


		/*se inicializan las variables para el calculo de totales */
		$total['recibido'] = 0;
		$total['conciliado'] = 0;
		$total['tratado'] = 0;
		$cantidadesXtratamiento = [];


		/* se itera sobre todos los residuos de las solicitudes de servicio */
		foreach ($SolicitudesServicios as $servicio) {
			foreach ($servicio->SolicitudResiduo as $residuo) {
				$collection = collect($cantidadesXtratamiento);

				/* si el tratamiento existe en la lista se suman las cantidadesxtratamiento y los totales correspondientes */
				if ($collection->has($residuo->requerimiento->tratamiento->TratName)) {
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] + $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] + $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] + $residuo->SolResKgTratado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}else{
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $residuo->SolResKgTratado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}
			}
		}
		// return $total;

		return view('solicitud-serv.almacenamiento', compact('SolicitudesServicios', 'cantidadesXtratamiento', 'total'));
	}
	
	public function createit()
    {
        if ( in_array(Auth::user()->UsRol, Permisos::COMERCIALEINGRURNO)|| in_array(Auth::user()->UsRol, Permisos::COMERCIALEINGRURNO)) {
            $ID_Cli = Cliente::where('CliDelete', 0)->get();
            $Departamentos = Departamento::all();
    
            return view('solicitud-serv.createit', compact('ID_Cli'));
        } else {
            abort(403);
        }
    } 
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
    		if (in_array(Auth::user()->UsRol, Permisos::CLIENTE)) {
			
			$Departamentos = Departamento::all();
			$Cliente = Cliente::select('CliName', 'CliName','ID_Cli', 'CliStatus', 'TipoFacturacion')->where('ID_Cli',userController::IDClienteSegunUsuario())->first();
			$Sedes = Sede::select('SedeSlug','SedeName')->where('FK_SedeCli', $Cliente->ID_Cli)
			->where('sedes.SedeDelete', 0)
			->get();
			$SGeneradors = DB::table('gener_sedes')
				->join('generadors', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
				->join('sedes', 'generadors.FK_GenerCli', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('gener_sedes.GSedeSlug', 'gener_sedes.GSedeName', 'generadors.GenerName')
				->where('clientes.ID_Cli', userController::IDClienteSegunUsuario())
				->where('generadors.GenerDelete', 0)
				->where('gener_sedes.GSedeDelete', 0)
				->get();
			$Personals = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('personals.PersSlug', 'personals.PersFirstName', 'personals.PersLastName', 'personals.PersEmail')
				->where('clientes.ID_Cli', userController::IDClienteSegunUsuario())
				->where('personals.PersDelete', 0)
				->get();
			
            $Requerimientos = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->get();
            // return $Requerimientos;
			// if ($Cliente->CliStatus=="Bloqueado") {
			// 	abort(403, 'Actualmente se encuentra deshabilitado para realizar nuevas solicitudes de servicio... Para mas detalles comuníquese con su Asesor Comercial');
			// }else{
			// 	return view('solicitud-serv.create', compact('Personals','Cliente', 'SGeneradors', 'Departamentos', 'Sedes', 'Requerimientos'));
			// }
				return view('solicitud-serv.create', compact('Personals','Cliente', 'SGeneradors', 'Departamentos', 'Sedes', 'Requerimientos'));

		} elseif(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol, Permisos::COMERCIALEINGRURNO)) {
			
			$clienteId = intval($request->input('ID_Cli'));
				
			$Cliente = Cliente::select('*')->where('ID_Cli', $clienteId)->first();	

			if (!$Cliente) {
				abort(403, 'Cliente no encontrado.');
			}
	
			$Sedes = Sede::select('SedeSlug', 'SedeName')
				->where('FK_SedeCli', $Cliente->ID_Cli)
				->where('SedeDelete', 0)
				->get();
	
			$Departamentos = Departamento::all();

			$ID_Cli = Cliente::where('CliDelete', 0)->get();
	
			$SGeneradors = DB::table('gener_sedes')
				->join('generadors', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
				->join('sedes', 'generadors.FK_GenerCli', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('gener_sedes.GSedeSlug', 'gener_sedes.GSedeName', 'generadors.GenerName')
				->where('clientes.ID_Cli', $Cliente->ID_Cli)
				->where('generadors.GenerDelete', 0)
				->where('gener_sedes.GSedeDelete', 0)
				->get();
	
			$Personals = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('personals.PersSlug', 'personals.PersFirstName', 'personals.PersLastName', 'personals.PersEmail')
				->where('clientes.ID_Cli', $Cliente->ID_Cli)
				->where('personals.PersDelete', 0)
				->get();

			$Clientes = DB::table('clientes')
        	->select('ID_Cli', 'CliName', 'TipoFacturacion')
        	->where('CliDelete', 0)
        	->get();
	
			$Requerimientos = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->get();
			
			return view('solicitud-serv.create', compact('Personals', 'Cliente', 'SGeneradors', 'Departamentos', 'Sedes', 'Requerimientos','Clientes'));
	
		} else {
			Log::error('Acceso no autorizado');
			abort(403, 'Solo los Clientes registrados pueden realizar nuevas solicitudes de servicio');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(SolServStoreRequest $request)
	{
		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="request store PRE-saved";
		$log->AuditRegistro="";
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=json_encode($request->all());
		$log->save();

		// return $request;
		$SolicitudServicio = new SolicitudServicio();
		$SolicitudServicio->SolSerStatus = 'Aprobado';
		$SolicitudServicio->SolServMailCopia = json_encode($request->input('SolServMailCopia'));
		switch ($request->input('SolResAuditoriaTipo')) {
			case 99:
				$SolicitudServicio->SolSerAuditable = 1;
				$SolicitudServicio->SolResAuditoriaTipo = "Virtual";
				break;
			case 98:
				$SolicitudServicio->SolSerAuditable = 1;
				$SolicitudServicio->SolResAuditoriaTipo = "Presencial";
				break;
			case 97:
				$SolicitudServicio->SolSerAuditable = 0;
				$SolicitudServicio->SolResAuditoriaTipo = "No Auditable";
				break;
		}
		$direccioncollect = 'No aplica';

		switch ($request->input('SolSerTipo')) {
			case '96':
				$transportadorname = $request->input('SolSerNameTrans');
				$transportadornit = $request->input('SolSerNitTrans');
				$transportadoradress = $request->input('SolSerAdressTrans');
				$transportadorcity = $request->input('SolSerCityTrans');
				$tipo = "Externo";
				$conductor = $request->input('SolSerConductor');
				$vehiculo = $request->input('SolSerVehiculo');
				$FechaLlegada = $request->input('SolSerFecha');
				break;

			case '97':
				$generador = DB::table('generadors')
					->join('gener_sedes', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
					->join('municipios', 'gener_sedes.FK_GSedeMun', '=', 'municipios.ID_Mun')
					->select('generadors.ID_Gener', 'generadors.GenerNit', 'generadors.GenerName', 'gener_sedes.GSedeAddress', 'municipios.ID_Mun')
					->where('GSedeSlug', $request->input('SolSerTransportador'))
					->first();
				$transportadorname = $generador->GenerName;
				$transportadornit = $generador->GenerNit;
				$transportadoradress = $generador->GSedeAddress;
				$transportadorcity = $generador->ID_Mun;
				$tipo = "Generador";
				$conductor = $request->input('SolSerConductor');
				$vehiculo = $request->input('SolSerVehiculo');
				break;

			case '98':
				$cliente = DB::table('clientes')
					->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
					->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
					->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'sedes.SedeSlug', 'municipios.ID_Mun')
					->where('SedeSlug', $request->input('SolSerTransportador'))
					->first();
				$transportadorname = $cliente->CliName;
				$transportadornit = $cliente->CliNit;
				$transportadoradress = $cliente->SedeAddress;
				$transportadorcity = $cliente->ID_Mun;
				$tipo = "Cliente";
				$conductor = $request->input('SolSerConductor');
				$vehiculo = $request->input('SolSerVehiculo');
				$FechaLlegada = $request->input('SolSerFecha');
				break;

			case '99':
				$cliente = DB::table('clientes')
					->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
					->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
					->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.ID_Mun')
					->where('ID_Cli', 1)
					->first();
				$tipo = "Interno";
				$transportadorname = $cliente->CliName;
				$transportadornit = $cliente->CliNit;
				$transportadoradress = $cliente->SedeAddress;
				$transportadorcity = $cliente->ID_Mun;
				$conductor = null;
				$vehiculo = null;
				switch ($request->input('SolSerTypeCollect')) {
					case 99:
						$direccioncollect = "Recolección en la sede de cada generador";
						break;
					case 98:
						$sede = Sede::select(['ID_Sede','FK_SedeMun'])->where('SedeSlug', $request->input('SedeCollect'))->first();
						$direccioncollect = $sede->ID_Sede;
						$SolicitudServicio->FK_SolSerCollectMun = $sede->FK_SedeMun;
						break;
					case 97:
						$direccioncollect = $request->input('AddressCollect');
						$SolicitudServicio->FK_SolSerCollectMun = $request->input('FK_SolSerCollectMun');
						break;
					case null:
						$FechaLlegada = $request->input('SolSerFecha');
						break;
				}
				break;

			default:
				# code...
				break;
		}

		if(isset($request['SupportPay'])){
			$fileSupport = $request['SupportPay'];
			$nameSupport = hash('sha256', rand().time().$fileSupport->getClientOriginalName()).'.pdf';
			$fileSupport->move(public_path().'\img\SupportPay/',$nameSupport);
			$SolicitudServicio->SolSerSupport = $nameSupport;
		}
		$SolicitudServicio->SolSerTipo = $tipo;
		$SolicitudServicio->SolSerNameTrans = $transportadorname;
		$SolicitudServicio->SolSerNitTrans = $transportadornit;
		$SolicitudServicio->SolSerAdressTrans = $transportadoradress;
		$SolicitudServicio->SolSerCityTrans = $transportadorcity;
		$SolicitudServicio->SolSerConductor = $conductor;
		$SolicitudServicio->SolSerVehiculo = $vehiculo;
		$SolicitudServicio->SolSerFecha = $request->input('SolSerFecha');
		$SolicitudServicio->SolSerDescript = $request->input('SolSerDescript');
		$SolicitudServicio->SolSerTypeCollect = $request->input('SolSerTypeCollect');
		$SolicitudServicio->SolSerCollectAddress = $direccioncollect;
		if($request->input('SolSerBascula')){
			$SolicitudServicio->SolSerBascula = 1;
		}
		if($request->input('SolSerCapacitacion')){
			$SolicitudServicio->SolSerCapacitacion = 1;
		}
		if($request->input('SolSerMasPerson')){
			$SolicitudServicio->SolSerMasPerson = 1;
		}
		if($request->input('SolSerVehicExclusive')){
			$SolicitudServicio->SolSerVehicExclusive = 1;
		}
		if($request->input('SolSerPlatform')){
			$SolicitudServicio->SolSerPlatform = 1;
		}
		if($request->input('SolSerDevolucion')){
			$SolicitudServicio->SolSerDevolucion = 1;
			$SolicitudServicio->SolSerDevolucionTipo = $request->input('SolSerDevolucionTipo');
		}
		$SolicitudServicio->SolSerSlug = hash('sha256', rand().time().$SolicitudServicio->SolSerNameTrans);
		$SolicitudServicio->SolSerDelete = 0;
		$personal = Personal::select('ID_Pers')->where('PersSlug', $request->input('FK_SolSerPersona'))->first();
		$SolicitudServicio->FK_SolSerPersona = $personal ? $personal->ID_Pers : null;
		if (in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR) || in_array(Auth::user()->UsRol, Permisos::COMERCIALEINGRURNO)) {
			$SolicitudServicio->FK_SolSerCliente = $request->input('FK_SolSerCliente');
			
		}else{
			$SolicitudServicio->FK_SolSerCliente = userController::IDClienteSegunUsuario();
		}
		$SolicitudServicio->save();
		$this->createSolRes($request, $SolicitudServicio->ID_SolSer);

		$log = new audit();
        $log->AuditTabla="Solicitud_servicios";
        $log->AuditType="Nuevo servicio";
        $log->AuditRegistro=$SolicitudServicio->ID_SolSer;
        $log->AuditUser=Auth::user()->email;
        $log->Auditlog=json_encode($request->all());
        $log->save();


		/*se guarda la observacion inicial de la creación del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
		$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
		$Observacion->ObsTipo = 'cliente';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
		$Observacion->save();

		// se verifica si el cliente tiene comercial asignado
		$SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudServicio->FK_SolSerCliente)->first();
		// se establece la lista de destinatarios
		if ($SolicitudServicio['cliente']->CliComercial <> null) {
			$comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
			$destinatarios = ['logistica@prosarc.com.co',
								'asistentelogistica@prosarc.com.co',
								'gestion@prosarc.com.co',
								'recepcionpda@prosarc.com.co',
								$comercial->PersEmail
							 ];
			$destinatarios1 = ['logistica@prosarc.com.co',
								'jefedetratamiento@prosarc.com.co',];				 
		}else{
			$comercial = "";
			$destinatarios = ['logistica@prosarc.com.co',
								'asistentelogistica@prosarc.com.co',
								'gestion@prosarc.com.co',
								'recepcionpda@prosarc.com.co'
							 ];
			$destinatarios1 = ['logistica@prosarc.com.co',
							 'jefedetratamiento@prosarc.com.co',];
		}

		$SolicitudServicio['comercial'] = $comercial;
		$SolicitudServicio['personalcliente'] = Personal::where('ID_Pers', $SolicitudServicio->FK_SolSerPersona)->first();

		$destinatariorecepciom = ['jefedetratamiento@prosarc.com.co',
								'supervisordeoperaciones@prosarc.com.co'];
		// se envia un correo por cada residuo registrado
		Mail::to($destinatarios)->send(new NewSolServEmail($SolicitudServicio));

		if($SolicitudServicio->SolSerAuditable = 2 || $SolicitudServicio->SolSerAuditable = 1){

			Mail::to($destinatarios1)->send(new SolserAuditar($SolicitudServicio));

		}
		if($SolicitudServicio->SolSerTipo = 'Cliente' || $SolicitudServicio->SolSerAuditable = 'Externo'){
			Mail::to($destinatariorecepciom)->send(new NewSolServEmail($SolicitudServicio));
		}
		// Verificar si hay sustancias controladas o aceites usados en la solicitud
		$SolicitudServicio = SolicitudServicio::with(['SolicitudResiduo.requerimiento.respel'])
			->where('ID_SolSer', $SolicitudServicio->ID_SolSer)->first();

		$cantidadDeResiduosControlados = 0;
		$cantidadDeAceitesUsados = 0;

		foreach ($SolicitudServicio->SolicitudResiduo as $residuo) {
			$respel = $residuo->requerimiento->respel;
			if ($respel->SustanciaControlada == 1) {
				$cantidadDeResiduosControlados++;
			}
			if ($respel->AceiteUsado == 1) {
				$cantidadDeAceitesUsados++;
			}
		}

		// Enviar notificaciones específicas si hay sustancias controladas o aceites usados
		if ($cantidadDeResiduosControlados > 0) {
			// Preparar datos para el email (similar a como se hace en VehicProgController)
			$email = DB::table('solicitud_servicios')
				->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
				->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
				->select('personals.*', 'solicitud_servicios.*', 'clientes.CliName', 'clientes.CliComercial')
				->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
				->first();

			// Enviar notificación de sustancia controlada creada
			Mail::to('dirtecnica@prosarc.com.co')->cc(['sistemas@prosarc.com.co', 'logistica@prosarc.com.co', 'jefedetratamiento@prosarc.com.co', 'asistentelogistica@prosarc.com.co', 'auxiliarlogistico@prosarc.com.co', 'conciliaciones@prosarc.com.co'])->send(new SustanciaControladaCreada($email, $SolicitudServicio));
		}

		if ($cantidadDeAceitesUsados > 0) {
			// Preparar datos para el email si no se hizo antes
			if (!isset($email)) {
				$email = DB::table('solicitud_servicios')
					->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
					->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
					->select('personals.*', 'solicitud_servicios.*', 'clientes.CliName', 'clientes.CliComercial')
					->where('solicitud_servicios.SolSerSlug', '=', $SolicitudServicio->SolSerSlug)
					->first();
			}

			// Enviar notificación de aceite usado creado
			Mail::to('dirtecnica@prosarc.com.co')->cc(['sistemas@prosarc.com.co', 'logistica@prosarc.com.co', 'asistentelogistica@prosarc.com.co', 'auxiliarlogistico@prosarc.com.co'])->send(new AceiteUsadoCreado($email, $SolicitudServicio));
		}
		//return redirect()->route('solicitud-servicio.show', ['id' => $SolicitudServicio->SolSerSlug]);
		return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $SolicitudServicio->SolSerSlug]);
		
 
	}


	/*
	*
	* Create from solicitud de residuo
	*
	*/
	public function createSolRes($request, $ID_SolSer)
	{
		foreach ($request->input('SGenerador') as $Generador => $value) {
			for ($y=0; $y < count($request['FK_SolResRg'][$Generador]); $y++) {
				$SolicitudResiduo = new SolicitudResiduo();
				if(in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL)){
				$SolicitudResiduo->SolResKgEnviado = 0;
				$SolicitudResiduo->SolResKgRecibido = $request['SolResKgEnviado'][$Generador][$y];
				}else {
				$SolicitudResiduo->SolResKgEnviado = $request['SolResKgEnviado'][$Generador][$y];
				$SolicitudResiduo->SolResKgRecibido = 0;
				}
				$SolicitudResiduo->SolResKgConciliado = 0;
				$SolicitudResiduo->SolResKgTratado = 0;
				$SolicitudResiduo->SolResDelete = 0;
				$SolicitudResiduo->SolResSlug = hash('sha256', rand().time().$SolicitudResiduo->SolResKgEnviado);
				$SolicitudResiduo->FK_SolResSolSer = $ID_SolSer;
				if ((isset($request['SolResTypeUnidad'][$Generador][$y]))){
					if($request['SolResTypeUnidad'][$Generador][$y] == 99){
						$SolicitudResiduo->SolResTypeUnidad = "Unidad";
					}
					else if($request['SolResTypeUnidad'][$Generador][$y] == 98){
						$SolicitudResiduo->SolResTypeUnidad = "Litros";
					}
					if (isset($request['SolResCantiUnidad'][$Generador][$y])&&$request['SolResCantiUnidad'][$Generador][$y] != null) {
						$SolicitudResiduo->SolResCantiUnidad = $request['SolResCantiUnidad'][$Generador][$y];
						$SolicitudResiduo->SolResCantiUnidadConciliada = 0;
						$SolicitudResiduo->SolResCantiUnidadRecibida = 0;
					}else {
						$SolicitudResiduo->SolResCantiUnidad = 0;
						$SolicitudResiduo->SolResCantiUnidadConciliada = 0;
						$SolicitudResiduo->SolResCantiUnidadRecibida = 0;
					}
				}

				switch ($request['SolResEmbalaje'][$Generador][$y]) {
					case 99:
						$SolicitudResiduo->SolResEmbalaje = "Sacos/Bolsas";
						break;
					case 98:
						$SolicitudResiduo->SolResEmbalaje = "Bidones Pequeños";
						break;
					case 97:
						$SolicitudResiduo->SolResEmbalaje = "Bidones Grandes";
						break;
					case 96:
						$SolicitudResiduo->SolResEmbalaje = "Estibas";
						break;
					case 95:
						$SolicitudResiduo->SolResEmbalaje = "Garrafones/Jerricanes";
						break;
					case 94:
						$SolicitudResiduo->SolResEmbalaje = "Cajas";
						break;
					case 93:
						$SolicitudResiduo->SolResEmbalaje = "Cuñetes";
						break;
					case 92:
						$SolicitudResiduo->SolResEmbalaje = "Big Bags";
						break;
					case 91:
						$SolicitudResiduo->SolResEmbalaje = "Isotanques";
						break;
					case 90:
						$SolicitudResiduo->SolResEmbalaje = "Tachos";
						break;
					case 89:
						$SolicitudResiduo->SolResEmbalaje = "Embalajes Compuestos";
						break;
					case 88:
						$SolicitudResiduo->SolResEmbalaje = "Granel";
						break;
					case 87:
						$SolicitudResiduo->SolResEmbalaje = "Canecas 55 gal.";
						break;
					case 86:
						$SolicitudResiduo->SolResEmbalaje = "Canecas 05 gal.";
						break;
				}
		 		$SolicitudResiduo->SolResAlto = $request['SolResAlto'][$Generador][$y];
				$SolicitudResiduo->SolResAncho = $request['SolResAncho'][$Generador][$y];
				$SolicitudResiduo->SolResProfundo = $request['SolResProfundo'][$Generador][$y];
				$SolicitudResiduo->SolResFotoDescargue_Pesaje = $request['SolResFotoDescargue_Pesaje'][$Generador][$y];
				$SolicitudResiduo->SolResFotoTratamiento = $request['SolResFotoTratamiento'][$Generador][$y];
				$SolicitudResiduo->SolResVideoDescargue_Pesaje = $request['SolResVideoDescargue_Pesaje'][$Generador][$y];
				$SolicitudResiduo->SolResVideoTratamiento = $request['SolResVideoTratamiento'][$Generador][$y];
				$SolicitudResiduo->SolResAuditoria = $request['SolResAuditoria'][$Generador][$y];
				// $SolicitudResiduo->SolResAuditoriaTipo = $request['SolResAuditoriaTipo'][$Generador][$y];
				$SolicitudResiduo->SolResDevolucion = $request['SolResDevolucion'][$Generador][$y];
				if ($SolicitudResiduo->SolResDevolucion == 0 || $SolicitudResiduo->SolResDevolucion == null) {
					$SolicitudResiduo->SolResDevolCantidad = 0;
				}else{
					$SolicitudResiduo->SolResDevolCantidad = $request['SolResDevolCantidad'][$Generador][$y];
				}
				$SolicitudResiduo->FK_SolResRg = ResiduosGener::select('ID_SGenerRes')->where('SlugSGenerRes',$request['FK_SolResRg'][$Generador][$y])->first()->ID_SGenerRes;
				/*validar el residuo para saber el tratamiento*/
				$respelref = ResiduosGener::select('FK_Respel')->where('SlugSGenerRes',$request['FK_SolResRg'][$Generador][$y])->first()->FK_Respel;
				/*asignar el requerimiento segun el tratamiento ofertado actualmente*/
				// $SolicitudResiduo->FK_SolResRequerimiento = Requerimiento::select('ID_Req')
				// ->where('FK_ReqRespel', $respelref)
				// ->where('ofertado', 1)
				// ->first()->ID_Req;
				// $SolicitudResiduo->save();
				$requerimientoparacopiar = Requerimiento::with(['pretratamientosSelected'])
				->where('FK_ReqRespel', $respelref)
				->where('ofertado', 1)
				->where('forevaluation', 1)
				->first();

				$nuevorequerimiento = $requerimientoparacopiar->replicate();
				$nuevorequerimiento->ReqSlug= hash('md5', rand().time().$respelref);
				$nuevorequerimiento->forevaluation=0;
				$nuevorequerimiento->ofertado=0;
				$nuevorequerimiento->save();
				$nuevorequerimiento->pretratamientosSelected()->attach($requerimientoparacopiar['pretratamientosSelected']);

				$tarifaparacopiar = Tarifa::with(['rangos'])
				->where('FK_TarifaReq', $requerimientoparacopiar->ID_Req)->first();
				$nuevatarifa = $tarifaparacopiar->replicate();
				$nuevatarifa->FK_TarifaReq=$nuevorequerimiento->ID_Req;
				$nuevatarifa->save();

				foreach ($tarifaparacopiar->rangos as $rango) {
					$rangoparacopiar = Rango::find($rango->ID_Rango);
					$nuevarango = $rangoparacopiar->replicate();
					$nuevarango->FK_RangoTarifa = $nuevatarifa->ID_Tarifa;
					$nuevarango->save();
				}
	
					// Obtener la instancia completa del modelo Respel
				$sustancia = Respel::where('ID_Respel', $respelref)->first();
	
						if (!$sustancia) {
							return response()->json(['error' => 'Sustancia not found'], 404);
						}
	
						$originalAttributes = $sustancia->getOriginal();
	
						/* Verificar si se cargó un documento en este campo */
						if ($request->hasFile('SustanciaControlada')) {
							$files = $request->file('SustanciaControlada');
							
							// Verificar si $files es un array o un solo archivo
							if (!is_array($files)) {
								$files = [$files]; // Asegurarse de que siempre es un array, incluso si es un solo archivo
							}
	
							// Borrar el documento actual si existe
							if ($sustancia->SustanciaControladaDocumento !== null && file_exists(public_path().'/img/SustanciaControlDoc/'.$sustancia->SustanciaControladaDocumento)) {
								unlink(public_path().'/img/SustanciaControlDoc/'.$sustancia->SustanciaControladaDocumento);
							}
	
							foreach ($files as $file4) {
								if ($file4 instanceof \Illuminate\Http\UploadedFile) {
									$ctrlDoc = hash('sha256', rand().time().$file4->getClientOriginalName()).'.pdf';
									$file4->move(public_path().'/img/SustanciaControlDoc/', $ctrlDoc);
									// Guardar el último documento subido, podrías cambiar esto si necesitas guardar varios documentos
									$sustancia->SustanciaControladaDocumento = $ctrlDoc;
								} else {
									// Manejar el caso en que $file4 no sea una instancia de UploadedFile
									// Esto podría ser un error inesperado
									return response()->json(['error' => 'File is not an instance of UploadedFile'], 400);
								}
							}
	
							// Guardar los cambios en la base de datos
							$sustancia->save();
						} else {
							$ctrlDoc = $sustancia->SustanciaControladaDocumento;
						}
	
						$SolicitudResiduo->FK_SolResRequerimiento = $nuevorequerimiento->ID_Req;
						$SolicitudResiduo->save();
					}
			}

		if(in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL)){
            } else {
                // Obtener los generadores de la solicitud
                $generadores = $request->input('SGenerador');

                // Si no hay generadores específicos, obtener el generador por defecto
                if(!$generadores){
                    $generadores = [DB::table('solicitud_servicios')
                        ->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
                        ->join('generadors', 'generadors.FK_GenerCli', '=', 'clientes.ID_Cli')
                        ->where('solicitud_servicios.ID_SolSer', $ID_SolSer)
                        ->select('generadors.GenerSlug')
                        ->first()->GenerSlug];
                }

                // Para cada generador en la solicitud
                foreach($generadores as $generadorSlug){
                    $generadorInfo = DB::table('generadors')
                        ->join('gener_sedes', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
                        ->join('municipios', 'gener_sedes.FK_GSedeMun', '=', 'municipios.ID_Mun')
                        ->select('generadors.ID_Gener', 'generadors.GenerNit', 'generadors.GenerName', 'gener_sedes.GSedeAddress', 'municipios.ID_Mun', 'gener_sedes.ID_GSede')
                        ->where('GSedeSlug', $generadorSlug)
                        ->first();

                    // Crear registro de firma para este generador
                    $firmas = new FirmasServicios();
                    $firmas->FK_SolSer = $ID_SolSer;
                    $firmas->FK_Gener = $generadorInfo->ID_Gener;
                    $firmas->FirmaCliente = '0';
                    $firmas->FirmaConductor = '0';
                    $firmas->FirmaPDA = '0';
                    $firmas->SlugFirmas = hash('md5', rand() . time());
                    $firmas->NombreFuncionario = '';
                    $firmas->Cedula = '0';
                    $firmas->Observaciones = '';

                    // Establecer FK_SGener según el tipo de servicio
                    switch($request->input('SolSerTypeCollect')){
                        case null: // Cliente lleva residuos a planta
                            $firmas->FK_SGener = 0;
                            break;
                            
                        case '97': // Dirección específica
                            $firmas->FK_SGener = 0;
                            break;
                            
                        case '98': // Sede del cliente
                            $firmas->FK_SGener = $generadorInfo->ID_GSede;
                            break;
                            
                        case '99': // Sede de cada generador
                            $firmas->FK_SGener = $generadorInfo->ID_GSede;
                            break;
                    }
                    
                    $firmas->save();
                }
            }
		}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName', 'personals.PersEmail')
			->where('solicitud_servicios.SolSerSlug', $id)
			->first();
		if (!$SolicitudServicio) {
			abort(404);
		}

		$Observaciones = Observacion::where('FK_ObsSolSer', $SolicitudServicio->ID_SolSer)->orderBy('ObsDate', 'desc')->get();

		if($SolicitudServicio->SolSerStatus == 'Completado'||$SolicitudServicio->SolSerStatus == 'Corregido'){
			$ultimoRecordatorio = Observacion::where('FK_ObsSolSer', $SolicitudServicio->ID_SolSer)
								->where('ObsStatus', 'Recordatorio+')
								->orderBy('ObsDate', 'desc')
								->first();
			if(!$ultimoRecordatorio){
				$ultimoRecordatorio = Observacion::where('FK_ObsSolSer', $SolicitudServicio->ID_SolSer)
								->where('ObsStatus', 'Completado')
								->orderBy('ObsDate', 'asc')
								->first();
				if(!$ultimoRecordatorio){
					$ultimoRecordatorio = collect();
					$ultimoRecordatorio->ObsDate = $SolicitudServicio->updated_at;
				}
				$ultimoRecordatorio->ObsRepeat = 0;
			}
		}


		$SolSerCollectAddress = $SolicitudServicio->SolSerCollectAddress;
		$SolSerConductor = $SolicitudServicio->SolSerConductor;
		if($SolicitudServicio->SolSerTipo == 'Interno'){
			$SolSerConductor = Personal::where('ID_Pers', $SolicitudServicio->SolSerConductor)->first();
		}
		if($SolicitudServicio->SolSerTypeCollect == 98){
			$Address = Sede::select(['SedeAddress', 'SedeName'])->where('ID_Sede',$SolicitudServicio->SolSerCollectAddress)->first();
			$SolSerCollectAddress = $Address->SedeName.' - '.$Address->SedeAddress;
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
		switch ($SolicitudServicio->SolSerStatus) {
			case 'Notificado':
			case 'Programado':
				setlocale(LC_ALL, "es_CO.UTF-8");
				$Programacion = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)->where('ProgVehDelete', 0)->first();
				if(date('H', strtotime($Programacion->ProgVehSalida)) >= 12){
					$horas = " en las horas de la tarde";
				}
				else{
					$horas = " en las horas de la mañana";
				}
				$TextProgramacion = "El día ".strftime("%d", strtotime($Programacion->ProgVehFecha))." del mes de ".strftime("%B", strtotime($Programacion->ProgVehFecha)).$horas;
				$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehDelete', 0)
				->get();
				$ProgramacionesActivas = count(ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehEntrada', null)
				->where('ProgVehDelete', 0)
				->get());
				// $ProgramacionesActivas = ($Programaciones);
				break;

			case 'Residuo Faltante':
				setlocale(LC_ALL, "es_CO.UTF-8");
				$Programacion = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)->where('ProgVehDelete', 0)->first();
				if(date('H', strtotime($Programacion->ProgVehSalida)) >= 12){
					$horas = " en las horas de la tarde";
				}
				else{
					$horas = " en las horas de la mañana";
				}
				$TextProgramacion = "";
				$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehDelete', 0)
				->get();
				$ProgramacionesActivas = count(ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehEntrada', null)
				->where('ProgVehDelete', 0)
				->get());
				// $ProgramacionesActivas = ($Programaciones);
				break;

			default:
				$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				// ->where('ProgVehEntrada', null)
				->where('ProgVehDelete', 0)
				->get();
				break;
		}
		$Cliente = DB::table('clientes')
			->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
			->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName')
			->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
			->first();
		$GenerResiduos = DB::table('solicitud_residuos')
			->distinct()
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
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
			->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'tratamientos.ID_Trat', 'clientes.CliShortName')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			// ->where('requerimientos.ofertado', 1)
	        // ->where('forevaluation', 0)
			->get();	

		$Residuos = $Residuosoriginal->map(function ($item) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected', 'tarifa.rangos' => function($query){
				$query->orderBy('TarifaDesde');
			}])
			->where('ID_Req', $item->FK_SolResRequerimiento)
			// ->where('forevaluation', 0)
			->first();

			$rm = SolicitudResiduo::with('SolicitudServicio')->where('SolResSlug', $item->SolResSlug)->first(['SolResRM', 'FK_SolResSolSer']);

	        $item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
	        $item->tarifa = $requerimientos->tarifa;
			if ($requerimientos->tarifa->TarifaSpecial === 1) {
				switch ($item->SolResTypeUnidad) {
					case 'Unidad':
						$tarifatipo = 'Unid';
						break;

					case 'Litros':
						$tarifatipo = 'Lt';
						break;

					default:
						$tarifatipo = 'Kg';
						break;
				}

				$tarifaResiduo = CTarifa::with('rangos')
					->where('FK_Cliente', $rm->SolicitudServicio->FK_SolSerCliente)
					->where('FK_Tratamiento', $requerimientos->FK_ReqTrata)
					->where('Tarifatipo', $tarifatipo)
					->first();

				if ($tarifaResiduo === null) {
					$item->ctarifa = null;
				}else{
					$item->ctarifa = $tarifaResiduo;
				}
			}else{
				$item->ctarifa = null;
			}
	        $item->SolResRM2 = $rm->SolResRM;
		  	return $item;
		});

		$SolicitudServicio->Repetible = 0;

			/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerRMs usando eloquent*/
	$rms = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerRMs');
	$SolicitudServicio->SolSerRMs = $rms->SolSerRMs;

	/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerVehiculo usando eloquent*/
	$vehiculo = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerVehiculo');
	$SolicitudServicio->SolSerVehiculo = $vehiculo->SolSerVehiculo;

		//return $Residuos;

		foreach ($Residuos as $residuo => $value) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected'])
	        ->where('ID_Req', $value->FK_SolResRequerimiento)
	        ->first();
			$residuoSinTratamiento = Requerimiento::where('FK_ReqRespel', $requerimientos->FK_ReqRespel)
			->where('ofertado', 1)
			->where('forevaluation', 1)
	        ->first();


			if ($residuoSinTratamiento == null) {
				$SolicitudServicio->Repetible++;
			}
		}

		$SolicitudesServicioscount = SolicitudServicio::with(['Personal', 'cliente', 'municipio', 'SolicitudResiduo'])
			->where('ID_SolSer', $SolicitudServicio->ID_SolSer)
			->orderBy('created_at', 'desc')
			->get();

		/*se inicializan las variables para el calculo de totales */
		$total['estimado'] = 0;
		$total['recibido'] = 0;
		$total['conciliado'] = 0;
		$total['tratado'] = 0;
		$cantidadesXtratamiento = [];


		/* se itera sobre todos los residuos de las solicitudes de servicio */
		foreach ($SolicitudesServicioscount as $servicio) {
			foreach ($servicio->SolicitudResiduo as $residuo) {
				$collection = collect($cantidadesXtratamiento);

				/* si el tratamiento existe en la lista se suman las cantidadesxtratamiento y los totales correspondientes */
				if ($collection->has($residuo->requerimiento->tratamiento->TratName)) {
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] + $residuo->SolResKgEnviado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] + $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] + $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] + $residuo->SolResKgTratado;
					$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}else{
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $residuo->SolResKgEnviado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $residuo->SolResKgTratado;
					$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}
			}
		}
		if (in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol, Permisos::SolSer1)) {
			$tratamientos = Tratamiento::join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
			->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->select('*')
			->where('TratDelete', 0)
			->get();
		}else{
			$tratamientos = 'NoAutorizado';
		}

		/* validacion para encontrar la fecha de recepción en planta del servicio */
		$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
		if($fechaRecepcion){
			$SolicitudServicio->recepcion = $fechaRecepcion->ProgVehSalida;
		}else{
			$SolicitudServicio->recepcion = null;
		}

		//Buscar corrientes del residuo
		
			$PublicRespels = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->select('respels.ID_Respel', 'respels.YRespelClasf4741', 'respels.ARespelClasf4741')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->distinct()
			->get();

        // adjuntar variables segun status del servicio
        switch ($SolicitudServicio->SolSerStatus) {
            case 'Residuo Faltante':
            case 'Notificado':
            case 'Programado':
				//return $Residuos;
		       return view('solicitud-serv.show', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerCollectAddress', 'SolSerConductor', 'TextProgramacion', 'Municipio', 'Programaciones', 'ProgramacionesActivas', 'total', 'cantidadesXtratamiento', 'tratamientos', 'Observaciones', 'PublicRespels'));
                break;

            case 'Corregido':
            case 'Completado':
			//	return $tratamientos;
		       return view('solicitud-serv.show', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerCollectAddress', 'SolSerConductor', 'TextProgramacion', 'Municipio', 'Programaciones', 'total', 'cantidadesXtratamiento', 'tratamientos', 'Observaciones', 'ultimoRecordatorio', 'PublicRespels'));
                break;

            default:
			//return $tratamientos;
       		return view('solicitud-serv.show', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerCollectAddress', 'SolSerConductor', 'TextProgramacion', 'Municipio', 'Programaciones', 'total', 'cantidadesXtratamiento', 'tratamientos', 'Observaciones', 'PublicRespels'));
                break;
        }
	}


public function changestatus(Request $request)
	{
		$Solicitud = SolicitudServicio::where('SolSerSlug', $request->input('solserslug'))->first();
		if (!$Solicitud) {
			abort(404);
		}
		if ($Solicitud->SolSerStatus <> 'Certificacion') {
			if(in_array(Auth::user()->UsRol, Permisos::CLIENTE) || in_array(Auth::user()->UsRol, Permisos::AREALOGISTICA)){
				if ($Solicitud->SolSerStatus == 'Recepcionado' || $Solicitud->SolSerStatus == 'Corregido') {
					if($request->input('solserstatus') == 'No Deacuerdo'){
						$Solicitud->SolSerStatus = 'No Conciliado';
					}
					if($request->input('solserstatus') == 'Conciliada'){
						$Solicitud->SolSerStatus = 'Conciliado';
					}
				} else {
					if (in_array(Auth::user()->UsRol, Permisos::CLIENTE)) {
						abort(403, 'el servicio no esta habilitado para la conciliación de pesos');
					}
				}
			}

			if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC) || in_array(Auth::user()->UsRol2, Permisos::TODOPROSARC)){
				switch ($request->input('solserstatus')) {
					case 'Aprobada':
						if(in_array(Auth::user()->UsRol, Permisos::ProgVehic2 ) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2 )){
							$Solicitud->SolSerStatus = 'Aprobado';
						}
						break;
					case 'Aceptada':
						if(in_array(Auth::user()->UsRol, Permisos::SOLSERACEPTADO) || in_array(Auth::user()->UsRol2, Permisos::SOLSERACEPTADO)){
							$Solicitud->SolSerStatus = 'Aceptado';
						}
						break;
					case 'Recibida':
						if(in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol2, Permisos::RECIBOMATERIAL)){
							$Solicitud->SolSerStatus = 'Completado';
						}
						break;
					case 'Recepcionado':
						if(in_array(Auth::user()->UsRol, Permisos::RECEPCIONPDA) || in_array(Auth::user()->UsRol2, Permisos::RECEPCIONPDA)){
							$Solicitud->SolSerStatus = 'Recepcionado';
						}
						break;
					case 'Fallido':
						if(in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol2, Permisos::RECIBOMATERIAL)){
							$Solicitud->SolSerStatus = 'Fallido';
						}
						break;
					case 'Residuo Faltante':
						if(in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol2, Permisos::SolSer1)){
							$Solicitud->SolSerStatus = 'Residuo Faltante';
						}
						break;
					case 'Conciliación':
						if(in_array(Auth::user()->UsRol, Permisos::ProgVehic2) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2)){
							$Solicitud->SolSerStatus = 'Corregido';
						}
						break;
					case 'Tratada':
						if(in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol2, Permisos::SolSer1)){
							$Solicitud->SolSerStatus = 'Tratado';
						}
						break;
					case 'Conciliada':
						if(in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol2, Permisos::SolSer1)){
							$Solicitud->SolSerStatus = 'Conciliado';
						}
						break;
					case 'No Deacuerdo':
						if (in_array(Auth::user()->UsRol, Permisos::ProgVehic2) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2)){
							$Solicitud->SolSerStatus = 'No Conciliado';
						}
						break;
					case 'Certificada':
						if(in_array(Auth::user()->UsRol, Permisos::SolSerCertifi) || in_array(Auth::user()->UsRol2, Permisos::SolSerCertifi)){
							$Solicitud->SolSerStatus = 'Certificacion';
							$Solicitud->SolServCertStatus = 2;
							$Solicitud->SolSerDescript = $request->input('solserdescript');
							$Solicitud->save();

							$log = new audit();
							$log->AuditTabla="solicitud_servicios";
							$log->AuditType="Modificado Status";
							$log->AuditRegistro=$Solicitud->ID_SolSer;
							$log->AuditUser=Auth::user()->email;
							$log->Auditlog=$Solicitud->SolSerStatus;
							$log->save();

							// return redirect()->route('solicitud-servicio.index');
							$slug = $Solicitud->SolSerSlug;
							return redirect()->route('email-solser', compact('slug'));

						}
						break;
					case 'Facturada':
						if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES)){
							$Solicitud->SolSerStatus = 'Facturado';
						}
						break;
				}
			}
		}else{
			abort(403, 'el servicio ya ha sido certificado y no admite cambios de status');
		}
		$Solicitud->SolSerDescript = $request->input('solserdescript');
		$Solicitud->save();

		if ($Solicitud->SolSerStatus == 'Conciliado') {
			$this->solservdocstore($Solicitud->ID_SolSer);

			$Solicitud->SolSerStatus = 'Conciliado';
			$Solicitud->SolServCertStatus = 2;
			$Solicitud->SolSerDescript = $request->input('solserdescript');
			$Solicitud->save();
			/** se guarda log en la tabla de auditoria */
	
			$log = new audit();
			$log->AuditTabla="solicitud_servicios";
			$log->AuditType="certificar";
			$log->AuditRegistro=$Solicitud->ID_SolSer;
			$log->AuditUser=Auth::user()->email;
			$log->Auditlog=[$Solicitud->SolSerStatus, $Solicitud->SolSerDescript];
			$log->save();
	
			/*se guarda la observacion de la modificacion del servicio*/
			$Observacion = new Observacion();
			$Observacion->ObsStatus = $Solicitud->SolSerStatus;
			$Observacion->ObsMensaje = $Solicitud->SolSerDescript;
			$Observacion->ObsTipo = 'prosarc';
			$Observacion->ObsRepeat = 1;
			$Observacion->ObsDate = now();
			$Observacion->ObsUser = Auth::user()->email;
			$Observacion->ObsRol = Auth::user()->UsRol;
			$Observacion->FK_ObsSolSer = $Solicitud->ID_SolSer;
			$Observacion->save();
			
		$certificados = Certificado::with(['certdato.solres', 'cliente.sedes.Municipios.Departamento', 'sedegenerador.generadors', 'sedegenerador.municipio.Departamento', 'gestor.sedes.Municipios.Departamento', 'tratamiento', 'transportador.sedes.Municipios.Departamento', 'SolicitudServicio' => function ($query){
			$query->with(['SolicitudResiduo' => function ($query){
				$query->where('SolResKgConciliado', '>', 0);
				$query->orWhere('SolResCantiUnidadConciliada', '>', 0);
				$query->with('generespel.respels');
				$query->with('requerimiento');
			}]);
		}])
		->where('FK_CertSolser', $Solicitud->ID_SolSer)
		->get();

//loop over $certificados
foreach ($certificados as $certificado) {

	$fecharecepcionenplanta = $certificado->SolicitudServicio->programacionesrecibidas()->first('ProgVehSalida');
	if ($fecharecepcionenplanta != null) {
		$fechaLlegadaPlanta = $fecharecepcionenplanta->ProgVehSalida;
	}else{
		$certificado->recepcion = "";
	}

	if ($request->input('solserRecepcionDate')) {
		$certificado->solserRecepcionDate = $request->input('solserRecepcionDate');
	}else {
		$certificado->solserRecepcionDate = $certificado->created_at;
	}
  //sreturn $certificado->recepcion;

	$qrCode = new QrCode(route('certificados.show', ['certificado' => $certificado->CertSlug]));
	//$qrCode->setLogoPath(asset('img/LogoQR.png'));
	$qrCode->setLogoSize(60, 60);
	$qrCode->setSize(300);
	$qrCode->setMargin(0);
	$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK);

// Obtén la colección de certificados, supongamos que $certificados es una colección.
$certificados = Certificado::where('FK_CertSolser', $Solicitud->ID_SolSer)->get();


foreach ($certificados as $certificado) {
    switch ($certificado->tratamiento->TratName) {
        case 'TermoDestrucción':
            $pdf = PDF::setPaper('letter', 'portrait')->loadView('certificados.topdf', compact(['certificado', 'Solicitud', 'qrCode', 'fechaLlegadaPlanta']));
            $nombre = $certificado->CertSlug . '.pdf';
            $path = 'public/certificadoRegular/' . sprintf("%0s", $nombre);

            Storage::put($path, $pdf->output(), 'public');

            // Actualiza el campo 'CertSrc' en el certificado específico
            $certificado->update(['CertSrc' => $nombre]);

            // Recopila los números de RM relacionados con el certificado
            $collection2 = collect([]);
            foreach($certificado->SolicitudServicio->SolicitudResiduo as $Residuo){
                if($Residuo->requerimiento->FK_ReqTrata == $certificado->FK_CertTrat && $Residuo->generespel->gener_sedes->ID_GSede == $certificado->FK_CertGenerSede) {
                    if($Residuo->SolResRM2 !== null && is_array($Residuo->SolResRM2)) {
                        foreach ($Residuo->SolResRM2 as $rm2 => $value2) {
                            $collection2 = $collection2->concat([$value2]);
                        }
                    } else {
                        if (is_array($Residuo->SolResRM)) {
                            foreach ($Residuo->SolResRM as $rm => $value) {
                                $collection2 = $collection2->concat([$value]);
                            }
                        } else {
                            $uniquestring = 'RM Invalido -> '.$Residuo->SolResRM;
                        }
                    }
                }
            }

            // Verifica si ya se envió un correo electrónico similar antes de enviarlo
            $uniqueKey = md5($certificado->ID_Cert . $certificado->FK_CertTrat . $certificado->FK_CertGenerSede);
            if (!Cache::has($uniqueKey)) {
                // Si la colección de números de RM no está vacía, genera una cadena de valores únicos
                if ($collection2->isNotEmpty()) {
                    $unicos = collect($collection2->unique());
                    $uniquestring = $unicos->values()->join(', ');
                }
                $certificado->update(['CertNumRm' => $uniquestring]);

                // Envía el correo electrónico solo si no se ha enviado uno similar recientemente
                $servicio = SolicitudServicio::where('ID_SolSer', $certificado->FK_CertSolser)->first();
                $destinatarios = ['dirtecnica@prosarc.com.co',
                                        'logistica@prosarc.com.co',
                                        'gerenteplanta@prosarc.com.co',
                                        'conciliaciones@prosarc.com.co',
                                        'auxiliarlogistico@prosarc.com.co',
                                        'asistentegerencia@prosarc.com.co'
                                        ];

                $cliente = Cliente::where('ID_Cli', $servicio->FK_SolSerCliente)->first();

                Mail::to($destinatarios)->send(new CertUpdated($certificado, $servicio, $cliente));

                // Almacena la clave única en la caché por un tiempo determinado
                Cache::put($uniqueKey, true, 1440);
            }
            break;

        default:
            $pdf = PDF::setPaper('letter', 'portrait')->loadView('certificados.topdfmanifiesto', compact(['certificado', 'Solicitud', 'qrCode', 'fechaLlegadaPlanta']));
            $nombre = $certificado->CertSlug . '.pdf';
            $path = 'public/manifiestosRegular/' . sprintf("%0s", $nombre);

            Storage::put($path, $pdf->output(), 'public');

            // Actualiza el campo 'CertSrc' en el certificado específico
            $certificado->update(['CertSrc' => $nombre]);

            // Recopila los números de RM relacionados con el certificado
            $collection2 = collect([]);
            foreach($certificado->SolicitudServicio->SolicitudResiduo as $Residuo){
                if($Residuo->requerimiento->FK_ReqTrata == $certificado->FK_CertTrat && $Residuo->generespel->gener_sedes->ID_GSede == $certificado->FK_CertGenerSede) {
                    if($Residuo->SolResRM2 !== null && is_array($Residuo->SolResRM2)) {
                        foreach ($Residuo->SolResRM2 as $rm2 => $value2) {
                            $collection2 = $collection2->concat([$value2]);
                        }
                    } else {
                        if (is_array($Residuo->SolResRM)) {
                            foreach ($Residuo->SolResRM as $rm => $value) {
                                $collection2 = $collection2->concat([$value]);
                            }
                        } else {
                            $uniquestring = 'RM Invalido -> '.$Residuo->SolResRM;
                        }
                    }
                }
            }

            // Verifica si ya se envió un correo electrónico similar antes de enviarlo
            $uniqueKey = md5($certificado->ID_Cert. $certificado->FK_CertTrat . $certificado->FK_CertGenerSede);
            if (!Cache::has($uniqueKey)) {
                // Si la colección de números de RM no está vacía, genera una cadena de valores únicos
                if ($collection2->isNotEmpty()) {
                    $unicos = collect($collection2->unique());
                    $uniquestring = $unicos->values()->join(', ');
                }
                $certificado->update(['CertNumRm' => $uniquestring]);

                // Envía el correo electrónico solo si no se ha enviado uno similar recientemente
                $servicio = SolicitudServicio::where('ID_SolSer', $certificado->FK_CertSolser)->first();
                $destinatarios = ['dirtecnica@prosarc.com.co',
                                        'logistica@prosarc.com.co',
                                        'gerenteplanta@prosarc.com.co',
                                        'conciliaciones@prosarc.com.co',
                                        'auxiliarlogistico@prosarc.com.co',
                                        'asistentegerencia@prosarc.com.co'
                                        ];

                $cliente = Cliente::where('ID_Cli', $servicio->FK_SolSerCliente)->first();

                Mail::to($destinatarios)->send(new CertUpdated($certificado, $servicio, $cliente));

                // Almacena la clave única en la caché por un tiempo determinado
                Cache::put($uniqueKey, true, 1440);
            }
            break;
    }
		}	
	}
}
		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Modificado Status";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=[$Solicitud->SolSerStatus, $Solicitud->SolSerDescript];
		$log->save();


		/*se guarda la observacion de la modificacion del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = $Solicitud->SolSerStatus;
		$Observacion->ObsMensaje = $Solicitud->SolSerDescript;
		switch ($Solicitud->SolSerStatus) {
			case 'Aprobado':
				$Observacion->ObsTipo = 'cliente';
				break;

			case 'Programado':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Notificado':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Completado':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Conciliado':
				if (in_array(Auth::user()->UsRol, Permisos::ADMINPLANTA) || in_array(Auth::user()->UsRol2, Permisos::ADMINPLANTA)) {
					$Observacion->ObsTipo = 'prosarc';
				}else{
					$Observacion->ObsTipo = 'cliente';
				}
				break;

			case 'No Conciliado':
				$Observacion->ObsTipo = 'cliente';
				break;

			case 'Tratado':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Certificacion':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Corregido':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Residuo Faltante':
				$Observacion->ObsTipo = 'prosarc';
				break;

			case 'Facturado':
				$Observacion->ObsTipo = 'prosarc';
				break;

			default:
			$Observacion->ObsTipo = 'prosarc';
				break;
		}
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $Solicitud->ID_SolSer;
		$Observacion->save();

		switch($Solicitud->SolSerStatus){
			case 'Tratado':
			case 'Facturado':
				return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $Solicitud->SolSerSlug]);
				break;
			case 'Aceptado':
				return redirect()->route('solicitud-servicio.index');
				break;
			case 'Conciliado':
				return redirect()->route('solicitud-servicio.index');
			case 'Completado':
				return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $Solicitud->SolSerSlug]);	
			default:
			    $slug = $Solicitud->SolSerSlug;
				return redirect()->route('email-solser', compact('slug'));
		}
	    return redirect()->route('solicitud-servicio.index');
	}
	
	public function repeat(Request $request, $slug)
	{
		$SolicitudOld = SolicitudServicio::where('SolSerSlug', $slug)->first();
		if (!$SolicitudOld) {
			abort(404, 'la solicitud que esta tratando de repetir no se encuentra en la base de datos');
		}

		$Cliente = Cliente::where('ID_Cli', $SolicitudOld->FK_SolSerCliente)->first();
        $Requerimiento = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->first();

		if(!is_null($SolicitudOld)){
				$SolResOlds = SolicitudResiduo::where('FK_SolResSolSer', $SolicitudOld->ID_SolSer)->get();
				$SolicitudNew = new SolicitudServicio();
				$SolicitudNew->SolSerStatus = 'Aprobado';
				$SolicitudNew->SolSerAuditable = $SolicitudOld->SolSerAuditable;
				$SolicitudNew->SolResAuditoriaTipo = $SolicitudOld->SolResAuditoriaTipo;
				$SolicitudNew->SolSerTipo = $SolicitudOld->SolSerTipo;
				$SolicitudNew->SolSerNameTrans = $SolicitudOld->SolSerNameTrans;
				$SolicitudNew->SolSerNitTrans = $SolicitudOld->SolSerNitTrans;
				$SolicitudNew->SolSerAdressTrans = $SolicitudOld->SolSerAdressTrans;
				$SolicitudNew->SolSerCityTrans = $SolicitudOld->SolSerCityTrans;
				$SolicitudNew->SolSerConductor = $SolicitudOld->SolSerConductor;
				$SolicitudNew->SolSerVehiculo = $SolicitudOld->SolSerVehiculo;
				$SolicitudNew->SolSerTypeCollect = $SolicitudOld->SolSerTypeCollect;
				$SolicitudNew->SolSerCollectAddress = $SolicitudOld->SolSerCollectAddress;
				if ($Requerimiento->RequeCliBascula==0) {
					$SolicitudNew->SolSerBascula = 0;
				}else{
					$SolicitudNew->SolSerBascula = $SolicitudOld->SolSerBascula;
				}

				if ($Requerimiento->RequeCliCapacitacion==0) {
					$SolicitudNew->SolSerCapacitacion = 0;
				}else{
					$SolicitudNew->SolSerCapacitacion = $SolicitudOld->SolSerCapacitacion;
				}

				if ($Requerimiento->RequeCliMasPerson==0) {
					$SolicitudNew->SolSerMasPerson = 0;
				}else{
					$SolicitudNew->SolSerMasPerson = $SolicitudOld->SolSerMasPerson;
				}

				if ($Requerimiento->RequeCliVehicExclusive==0) {
					$SolicitudNew->SolSerVehicExclusive = 0;
				}else{
					$SolicitudNew->SolSerVehicExclusive = $SolicitudOld->SolSerVehicExclusive;
				}

				if ($Requerimiento->RequeCliPlatform==0) {
					$SolicitudNew->SolSerPlatform = 0;
				}else{
					$SolicitudNew->SolSerPlatform = $SolicitudOld->SolSerPlatform;
				}
				$SolicitudNew->SolSerDevolucion = $SolicitudOld->SolSerDevolucion;
				$SolicitudNew->SolSerDevolucionTipo = $SolicitudOld->SolSerDevolucionTipo;
				$SolicitudNew->FK_SolSerPersona = $SolicitudOld->FK_SolSerPersona;
				$SolicitudNew->FK_SolSerCliente = $SolicitudOld->FK_SolSerCliente;
				$SolicitudNew->SolServMailCopia = $SolicitudOld->SolServMailCopia;
				$SolicitudNew->SolSerSlug = hash('sha256', rand().time().$SolicitudNew->SolSerNameTrans);
				$SolicitudNew->SolSerDelete = 0;
				$SolicitudNew->SolSerDescript = $request->input('solserdescript');
				$SolicitudNew->save();

				foreach ($SolResOlds as $SolResOld) {
					$SolResNew = new SolicitudResiduo();
					$SolResNew->SolResKgEnviado = $SolResOld->SolResKgEnviado;
					$SolResNew->SolResKgRecibido = 0;
					$SolResNew->SolResKgConciliado = 0;
					$SolResNew->SolResKgTratado = 0;
					$SolResNew->SolResDelete = 0;
					$SolResNew->SolResTypeUnidad = $SolResOld->SolResTypeUnidad;
					$SolResNew->SolResCantiUnidad = $SolResOld->SolResCantiUnidad;
					$SolResNew->SolResEmbalaje = $SolResOld->SolResEmbalaje;
					$SolResNew->SolResAlto = $SolResOld->SolResAlto;
					$SolResNew->SolResAncho = $SolResOld->SolResAncho;
					$SolResNew->SolResProfundo = $SolResOld->SolResProfundo;
					$SolResNew->SolResSlug = hash('sha256', rand().time().$SolResNew->SolResKgEnviado);
					$SolResNew->FK_SolResRg = $SolResOld->FK_SolResRg;
					$SolResNew->FK_SolResSolSer = $SolicitudNew->ID_SolSer;
					/*se verifica el requerimiento actualmente ofertado para el residuo*/
					$respelgener= ResiduosGener::find($SolResOld->FK_SolResRg);

					$requerimientoOfertado = Requerimiento::with(['pretratamientosSelected'])
						->where('FK_ReqRespel', '=', $respelgener->FK_Respel)
						->where('ofertado', '=', 1)
						->where('forevaluation', '=', 1)
						->first();

					if ($requerimientoOfertado == null) {
						$SolicitudNew->delete();

						$log = new audit();
						$log->AuditTabla="solicitud_servicios";
						$log->AuditType="repetir fallido";
						$log->AuditRegistro=$SolicitudNew->ID_SolSer;
						$log->AuditUser=Auth::user()->email;
						$log->Auditlog=$SolicitudNew;
						$log->save();

						abort(404, 'el servicio no se puede repetir debido a que alguno de los residuos no posee tratamiento ofertado, Verifique con su asesor Comercial');
					}
					if ($requerimientoOfertado->ReqFotoDescargue==0) {
						$SolResNew->SolResFotoDescargue_Pesaje = 0;
					}else{
						$SolResNew->SolResFotoDescargue_Pesaje = $SolResOld->SolResFotoDescargue_Pesaje;
					}

					if ($requerimientoOfertado->ReqFotoDestruccion==0) {
						$SolResNew->SolResFotoTratamiento = 0;
					}else{
						$SolResNew->SolResFotoTratamiento = $SolResOld->SolResFotoTratamiento;
					}

					if ($requerimientoOfertado->ReqVideoDescargue==0) {
						$SolResNew->SolResVideoDescargue_Pesaje = 0;
					}else{
						$SolResNew->SolResVideoDescargue_Pesaje = $SolResOld->SolResVideoDescargue_Pesaje;
					}

					if ($requerimientoOfertado->ReqVideoDestruccion==0) {
						$SolResNew->SolResVideoTratamiento = 0;
					}else{
						$SolResNew->SolResVideoTratamiento = $SolResOld->SolResVideoTratamiento;
					}

					if ($requerimientoOfertado->ReqDevolucion==0) {
						$SolResNew->SolResDevolucion = 0;
					}else{
						$SolResNew->SolResDevolucion = $SolResOld->SolResDevolucion;
					}

					if ($requerimientoOfertado->ReqAuditoria==0) {
						$SolResNew->SolResAuditoria = 0;
					}else{
						$SolResNew->SolResAuditoria = $SolResOld->SolResAuditoria;
					}
					$SolResNew->SolResVideoTratamiento = $SolResOld->SolResVideoTratamiento;
					$SolResNew->SolResDevolucion = $SolResOld->SolResVideoTratamiento;
					$SolResNew->SolResDevolCantidad = $SolResOld->SolResVideoTratamiento;
					$SolResNew->SolResAuditoria = $SolResOld->SolResVideoTratamiento;
					$SolResNew->SolResAuditoriaTipo = $SolResOld->SolResVideoTratamiento;
					/*se verifica los requerimientos y pretratamientos seleccionados para copiarlos*/

					$nuevorequerimiento = $requerimientoOfertado->replicate();
					$nuevorequerimiento->ReqSlug= hash('md5', rand().time().$respelgener->FK_Respel);
					$nuevorequerimiento->forevaluation=0;
					$nuevorequerimiento->ofertado=0;
					$nuevorequerimiento->save();
					$nuevorequerimiento->pretratamientosSelected()->attach($requerimientoOfertado['pretratamientosSelected']);

					$tarifaparacopiar = Tarifa::with(['rangos'])
					->where('FK_TarifaReq', $requerimientoOfertado->ID_Req)->first();
					$nuevatarifa = $tarifaparacopiar->replicate();
					$nuevatarifa->FK_TarifaReq=$nuevorequerimiento->ID_Req;
					$nuevatarifa->save();

					foreach ($tarifaparacopiar->rangos as $rango) {
						$rangoparacopiar = Rango::find($rango->ID_Rango);
						$nuevarango = $rangoparacopiar->replicate();
						$nuevarango->FK_RangoTarifa = $nuevatarifa->ID_Tarifa;
						$nuevarango->save();
					}
					$SolResNew->FK_SolResRequerimiento = $nuevorequerimiento->ID_Req;
					$SolResNew->save();
				}

			$SolicitudServicio = $SolicitudNew;

			if (in_array(Auth::user()->UsRol, Permisos::CLIENTE)) {
				/*se guarda la observacion inicial del servicio repetido*/
				$Observacion = new Observacion();
				$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
				$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
				$Observacion->ObsTipo = 'cliente';
				$Observacion->ObsRepeat = 1;
				$Observacion->ObsDate = now();
				$Observacion->ObsUser = Auth::user()->email;
				$Observacion->ObsRol = Auth::user()->UsRol;
				$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
				$Observacion->save();

			} else {

				/* se incluye la primera observacion del cliente del servicio original */
				$observacionOriginal = Observacion::where('FK_ObsSolSer', $SolicitudOld->ID_SolSer)->first();
				/*se guarda la observacion inicial del servicio repetido*/
				$Observacion = new Observacion();
				$Observacion->ObsStatus = $observacionOriginal->ObsStatus;
				$Observacion->ObsMensaje = $observacionOriginal->ObsMensaje;
				$Observacion->ObsTipo = $observacionOriginal->ObsTipo;
				$Observacion->ObsRepeat = 1;
				$Observacion->ObsDate = $observacionOriginal->ObsDate;
				$Observacion->ObsUser = $observacionOriginal->ObsUser;
				$Observacion->ObsRol = $observacionOriginal->ObsRol;
				$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
				$Observacion->save();


				/*se guarda la observacion inicial del servicio repetido*/
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
			}


						// se verifica si el cliente tiene comercial asignado
			$SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudNew->FK_SolSerCliente)->first();
			// se establece la lista de destinatarios
			if ($SolicitudServicio['cliente']->CliComercial <> null) {
				$comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
				$destinatarios = ['dirtecnica@prosarc.com.co',
									'logistica@prosarc.com.co',
									'asistentelogistica@prosarc.com.co',
									'subgerencia@prosarc.com.co',
									$comercial->PersEmail
								];
			}else{
				$comercial = "";
				$destinatarios = ['dirtecnica@prosarc.com.co',
									'logistica@prosarc.com.co',
									'asistentelogistica@prosarc.com.co',
									'subgerencia@prosarc.com.co'
								];
			}

			$SolicitudServicio['comercial'] = $comercial;
			$SolicitudServicio['personalcliente'] = Personal::where('ID_Pers', $SolicitudNew->FK_SolSerPersona)->first();

			if ($SolicitudServicio->SolServMailCopia != "null") {
				foreach (json_decode($SolicitudServicio->SolServMailCopia) as $key => $value) {
					array_push($destinatarios, $value);
				}
			}

			if (in_array(Auth::user()->UsRol, Permisos::CLIENTE)) {
				Mail::to($SolicitudServicio['personalcliente']->PersEmail)->cc($destinatarios)->send(new NewSolServEmail($SolicitudServicio));
			}else{
				Mail::to($SolicitudServicio['personalcliente']->PersEmail)->cc($destinatarios)->send(new NewSolServProsarcEmail($SolicitudServicio));
			}

			$log = new audit();
			$log->AuditTabla="Solicitud_servicios";
			$log->AuditType="servicio Repetido";
			$log->AuditRegistro=$SolicitudOld->ID_SolSer;
			$log->AuditUser=Auth::user()->email;
			$log->Auditlog=json_encode($SolicitudNew->ID_SolSer);
			$log->save();

                // Obtener los generadores de la solicitud
                $generadores = $request->input('SGenerador');

                // Para cada generador en la solicitud
                foreach($generadores as $generadorSlug){
                    $generadorInfo = DB::table('generadors')
                        ->join('gener_sedes', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
                        ->join('municipios', 'gener_sedes.FK_GSedeMun', '=', 'municipios.ID_Mun')
                        ->select('generadors.ID_Gener', 'generadors.GenerNit', 'generadors.GenerName', 'gener_sedes.GSedeAddress', 'municipios.ID_Mun', 'gener_sedes.ID_GSede')
                        ->where('GSedeSlug', $generadorSlug)
                        ->first();

                    // Crear registro de firma para este generador
                    $firmas = new FirmasServicios();
                    $firmas->FK_SolSer = $SolicitudNew->ID_SolSer;
                    $firmas->FK_Gener = $generadorInfo->ID_Gener;
                    $firmas->FirmaCliente = '0';
                    $firmas->FirmaConductor = '0';
                    $firmas->FirmaPDA = '0';
                    $firmas->SlugFirmas = hash('md5', rand() . time());
                    $firmas->NombreFuncionario = '';
                    $firmas->Cedula = '0';
                    $firmas->Observaciones = '';

                    // Establecer FK_SGener según el tipo de servicio
                    switch($request->input('SolSerTypeCollect')){
                        case null: // Cliente lleva residuos a planta
                            $firmas->FK_SGener = 0;
                            break;
                            
                        case '97': // Dirección específica
                            $firmas->FK_SGener = 0;
                            break;
                            
                        case '98': // Sede del cliente
                            $firmas->FK_SGener = $generadorInfo->ID_GSede;
                            break;
                            
                        case '99': // Sede de cada generador
                            $firmas->FK_SGener = $generadorInfo->ID_GSede;
                            break;
                    }
                    
                    $firmas->save();				
			
			}


			return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $SolicitudNew->SolSerSlug]);
		}
		else{
			abort(404, 'la solicitud que esta tratando de repetir no se encuentra en la base de datos');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
    {
        if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
            $Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
            if (!$Solicitud) {
                abort(404);
            }
            if(in_array(Auth::user()->UsRol, Permisos::CLIENTE) && $Solicitud->SolSerStatus === 'Tratado' || $Solicitud->SolSerStatus === 'Certificacion' || $Solicitud->SolSerStatus === 'Completado'){
                abort(403);
            }
            if($Solicitud->SolSerCityTrans <> null){
                $Municipio = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->SolSerCityTrans)->first();
                $Departamento = Departamento::where('ID_Depart', $Municipio->FK_MunCity)->first();
                $Municipios = Municipio::where('FK_MunCity', $Departamento->ID_Depart)->get();
            }
            if($Solicitud->FK_SolSerCollectMun <> null){
                $Municipio2 = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->FK_SolSerCollectMun)->first();
                $Departamento2 = Departamento::where('ID_Depart', $Municipio2->FK_MunCity)->first();
                $Municipios2 = Municipio::where('FK_MunCity', $Departamento2->ID_Depart)->get();
            }
            $Departamentos = Departamento::all();
            $Cliente = Cliente::where('ID_Cli', $Solicitud->FK_SolSerCliente)->first();
            $Requerimientos = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->get();
            $Sedes = Sede::select('SedeSlug','SedeName', 'ID_Sede')->where('FK_SedeCli', $Cliente->ID_Cli)->get();
            $SGeneradors = DB::table('gener_sedes')
                ->join('generadors', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
                ->join('sedes', 'generadors.FK_GenerCli', '=', 'sedes.ID_Sede')
                ->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
                ->select('gener_sedes.GSedeSlug', 'gener_sedes.GSedeName', 'generadors.GenerName', 'generadors.GenerNit')
                ->where('clientes.ID_Cli', userController::IDClienteSegunUsuario())
                ->get();
            $Persona = Personal::where('ID_Pers', $Solicitud->FK_SolSerPersona)
                ->select('PersSlug','PersFirstName','PersLastName')
                ->first();
            $Personals = DB::table('personals')
                ->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
                ->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
                ->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
                ->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
                ->select('personals.PersSlug', 'personals.PersFirstName', 'personals.PersLastName', 'personals.PersEmail')
                ->where('clientes.ID_Cli', userController::IDClienteSegunUsuario())
                ->where('personals.PersDelete', 0)
                ->get();
            $KGenviados = DB::table('solicitud_residuos')
                ->select('SolResKgEnviado')
                ->where('FK_SolResSolSer', $Solicitud->ID_SolSer)
                ->get();
            $totalenviado = 0;
            foreach ($KGenviados as $KGenviado) {
                $totalenviado = $totalenviado + $KGenviado->SolResKgEnviado;
            }
            return view('solicitud-serv.edit', compact('Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios',  'Sedes', 'totalenviado', 'Requerimientos'));

        } elseif(in_array(Auth::user()->UsRol2, Permisos::RECEPCIONPDA)){
            $Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
            if (!$Solicitud) {
                abort(404);
            }
            if($Solicitud->SolSerCityTrans <> null){
                $Municipio = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->SolSerCityTrans)->first();
                $Departamento = Departamento::where('ID_Depart', $Municipio->FK_MunCity)->first();
                $Municipios = Municipio::where('FK_MunCity', $Departamento->ID_Depart)->get();
            }
            if($Solicitud->FK_SolSerCollectMun <> null){
                $Municipio2 = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->FK_SolSerCollectMun)->first();
                $Departamento2 = Departamento::where('ID_Depart', $Municipio2->FK_MunCity)->first();
                $Municipios2 = Municipio::where('FK_MunCity', $Departamento2->ID_Depart)->get();
            }
            $Departamentos = Departamento::all();
            $Cliente = Cliente::where('ID_Cli', $Solicitud->FK_SolSerCliente)->first();
            $Requerimientos = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->get();
            $Sedes = Sede::select('SedeSlug','SedeName', 'ID_Sede')->where('FK_SedeCli', $Cliente->ID_Cli)->get();
            $SGeneradors = DB::table('gener_sedes')
                ->join('generadors', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
                ->join('sedes', 'generadors.FK_GenerCli', '=', 'sedes.ID_Sede')
                ->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
                ->select('gener_sedes.GSedeSlug', 'gener_sedes.GSedeName', 'generadors.GenerName', 'generadors.GenerNit')
                ->where('clientes.ID_Cli', $Solicitud->FK_SolSerCliente )
                ->get();
            $Persona = Personal::where('ID_Pers', $Solicitud->FK_SolSerPersona)
                ->select('PersSlug','PersFirstName','PersLastName')
                ->first();
            $Personals = DB::table('personals')
                ->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
                ->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
                ->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
                ->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
                ->select('personals.PersSlug', 'personals.PersFirstName', 'personals.PersLastName', 'personals.PersEmail')
				->where('clientes.ID_Cli', $Solicitud->FK_SolSerCliente)
                ->where('personals.PersDelete', 0)
                ->get();
            $KGenviados = DB::table('solicitud_residuos')
                ->select('SolResKgEnviado')
                ->where('FK_SolResSolSer', $Solicitud->ID_SolSer)
                ->get();
            $totalenviado = 0;
            foreach ($KGenviados as $KGenviado) {
                $totalenviado = $totalenviado + $KGenviado->SolResKgEnviado;
            }
            //return $Departamentos;
			if(empty($Departamentos2)){
				return view('solicitud-serv.edit', compact('Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios',  'Sedes', 'totalenviado', 'Requerimientos'));	
			} else {
            return view('solicitud-serv.edit', compact('Departamento2', 'Municipios2', 'Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios',  'Sedes', 'totalenviado', 'Requerimientos'));
			}
        }
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
		$SolicitudServicio = SolicitudServicio::where('SolSerSlug', $id)->first();
		if (!$SolicitudServicio) {
			abort(404);
		}
		$SolicitudServicio->SolServMailCopia = json_encode($request->input('SolServMailCopia'));

	    if ($SolicitudServicio->SolSerStatus === "Aprobado"||(($SolicitudServicio->SolSerStatus === "Programado"||$SolicitudServicio->SolSerStatus === "Notificado"||$Solicitud->SolSerStatus === "Tratado" ||$Solicitud->SolSerStatus === "Certificacion"||  $Solicitud->SolSerStatus === "Completado")&&$SolicitudServicio->SolSerTipo !== 'Interno')){
			switch ($request->input('SolResAuditoriaTipo')) {
				case 99:
					$SolicitudServicio->SolSerAuditable = 1;
					$SolicitudServicio->SolResAuditoriaTipo = "Presencial";
					break;
				case 98:
					$SolicitudServicio->SolSerAuditable = 1;
					$SolicitudServicio->SolResAuditoriaTipo = "Virtual";
					break;
				case 97:
					$SolicitudServicio->SolSerAuditable = 0;
					$SolicitudServicio->SolResAuditoriaTipo = "No Auditable";
					break;
			}
			$collect = null;
			$SolicitudServicio->FK_SolSerCollectMun = null;
			$direccioncollect = 'No aplica';
			switch ($request->input('SolSerTipo')) {
				case '96':
					$transportadorname = $request->input('SolSerNameTrans');
					$transportadornit = $request->input('SolSerNitTrans');
					$transportadoradress = $request->input('SolSerAdressTrans');
					$transportadorcity = $request->input('SolSerCityTrans');
					$tipo = "Externo";
					$conductor = $request->input('SolSerConductor');
					$vehiculo = $request->input('SolSerVehiculo');
					$FechaLlegada = $request->input('SolSerFecha');
					break;

				case '97':
					$generador = DB::table('generadors')
						->join('gener_sedes', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
						->join('municipios', 'gener_sedes.FK_GSedeMun', '=', 'municipios.ID_Mun')
						->select('generadors.ID_Gener', 'generadors.GenerNit', 'generadors.GenerName', 'gener_sedes.GSedeAddress', 'municipios.ID_Mun')
						->where('GSedeSlug', $request->input('SolSerTransportador'))
						->first();
					$transportadorname = $generador->GenerName;
					$transportadornit = $generador->GenerNit;
					$transportadoradress = $generador->GSedeAddress;
					$transportadorcity = $generador->ID_Mun;
					$tipo = "Generador";
					$conductor = $request->input('SolSerConductor');
					$vehiculo = $request->input('SolSerVehiculo');
					break;

				case '98':
					$cliente = DB::table('clientes')
						->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
						->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
						->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'sedes.SedeSlug', 'municipios.ID_Mun')
						->where('SedeSlug', $request->input('SolSerTransportador'))
						->first();
					$transportadorname = $cliente->CliName;
					$transportadornit = $cliente->CliNit;
					$transportadoradress = $cliente->SedeAddress;
					$transportadorcity = $cliente->ID_Mun;
					$tipo = "Cliente";
					$conductor = $request->input('SolSerConductor');
					$vehiculo = $request->input('SolSerVehiculo');
					$FechaLlegada = $request->input('SolSerFecha');
					break;

				case '99':
					$cliente = DB::table('clientes')
						->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
						->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
						->select('clientes.ID_Cli', 'clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.ID_Mun')
						->where('ID_Cli', 1)
						->first();
					$tipo = "Interno";
					$transportadorname = $cliente->CliName;
					$transportadornit = $cliente->CliNit;
					$transportadoradress = $cliente->SedeAddress;
					$transportadorcity = $cliente->ID_Mun;
					$conductor = null;
					$vehiculo = null;
					switch ($request->input('SolSerTypeCollect')) {
						case 99:
							$direccioncollect = "Recolección en la sede de cada generador";
							$SolicitudServicio->FK_SolSerCollectMun = null;
							break;
						case 98:
							$sede = Sede::select(['ID_Sede', 'FK_SedeMun'])->where('SedeSlug', $request->input('SedeCollect'))->first();
							$direccioncollect = $sede->ID_Sede;
							$SolicitudServicio->FK_SolSerCollectMun = $sede->FK_SedeMun;
							break;
						case 97:
							$direccioncollect = $request->input('AddressCollect');
							$SolicitudServicio->FK_SolSerCollectMun = $request->input('FK_SolSerCollectMun');
							break;
						case null:
								$FechaLlegada = $request->input('SolSerFecha');
								$SolicitudServicio->SolSerFecha = $FechaLlegada;
								break;	
					}
					$collect = $request->input('SolSerTypeCollect');
					break;

				default:
					# code...
					break;
			}

			if(isset($request['SupportPay'])){
				if($SolicitudServicio->SolSerSupport <> null && file_exists(public_path().'/img/SupportPay/'.$SolicitudServicio->SolSerSupport)){
					unlink(public_path().'/img/SupportPay/'.$SolicitudServicio->SolSerSupport);
				}
				$fileSupport = $request['SupportPay'];
				$nameSupport = hash('sha256', rand().time().$fileSupport->getClientOriginalName()).'.pdf';
				$fileSupport->move(public_path().'\img\SupportPay/',$nameSupport);
				$SolicitudServicio->SolSerSupport = $nameSupport;
			}
			$SolicitudServicio->SolSerTipo = $tipo;
			$SolicitudServicio->SolSerNameTrans = $transportadorname;
			$SolicitudServicio->SolSerNitTrans = $transportadornit;
			$SolicitudServicio->SolSerAdressTrans = $transportadoradress;
			$SolicitudServicio->SolSerCityTrans = $transportadorcity;
			$SolicitudServicio->SolSerConductor = $conductor;
			$SolicitudServicio->SolSerVehiculo = $vehiculo;
			$SolicitudServicio->SolSerTypeCollect = $collect;
			$SolicitudServicio->SolSerCollectAddress = $direccioncollect;
			if($request->input('SolSerBascula')){
				$SolicitudServicio->SolSerBascula = 1;
			}
			else{
				$SolicitudServicio->SolSerBascula = null;
			}
			if($request->input('SolSerCapacitacion')){
				$SolicitudServicio->SolSerCapacitacion = 1;
			}
			else{
				$SolicitudServicio->SolSerCapacitacion = null;
			}
			if($request->input('SolSerMasPerson')){
				$SolicitudServicio->SolSerMasPerson = 1;
			}
			else{
				$SolicitudServicio->SolSerMasPerson = null;
			}
			if($request->input('SolSerVehicExclusive')){
				$SolicitudServicio->SolSerVehicExclusive = 1;
			}
			else{
				$SolicitudServicio->SolSerVehicExclusive = null;
			}
			if($request->input('SolSerPlatform')){
				$SolicitudServicio->SolSerPlatform = 1;
			}
			else{
				$SolicitudServicio->SolSerPlatform = null;
			}
			if($request->input('SolSerDevolucion')){
				$SolicitudServicio->SolSerDevolucion = 1;
				$SolicitudServicio->SolSerDevolucionTipo = $request->input('SolSerDevolucionTipo');
			}
			else{
				$SolicitudServicio->SolSerDevolucion = null;
				$SolicitudServicio->SolSerDevolucionTipo = null;
			}

			if(!is_null($request->input('SGenerador'))){
				$this->createSolRes($request, $SolicitudServicio->ID_SolSer);
			}
		}

		$SolicitudServicio->FK_SolSerPersona = Personal::select('ID_Pers')->where('PersSlug',$request->input('FK_SolSerPersona'))->first()->ID_Pers;
		$SolicitudServicio->SolSerDescript = $request->input('SolSerDescript');
		$SolicitudServicio->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Modificado";
		$log->AuditRegistro=$SolicitudServicio->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=json_encode($request->all());
		$log->save();

		/*se guarda la observacion de la modificacion del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
		$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
		$Observacion->ObsTipo = 'cliente';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
		$Observacion->save();

		$destinatarios1 = ['logistica@prosarc.com.co',
							'jefedetratamiento@prosarc.com.co',];
		if($SolicitudServicio->SolSerAuditable = 2 || $SolicitudServicio->SolSerAuditable = 1){

			Mail::to($destinatarios1)->send(new SolserAuditar($SolicitudServicio));

		}
		return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $id]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$SolicitudServicio = SolicitudServicio::where('SolSerSlug', $id)->first();

		if (!$SolicitudServicio) {
			abort(404, 'no se pudo eliminar la solicitud de servicio ya que no se encuentra en la base da datos');
		}

		switch ($SolicitudServicio->SolSerStatus) {
			case 'Pendiente':
			case 'Aceptado':
			case 'Programado':
			case 'Notificado':
			case 'Aprobado':

				$documentos = Documento::where('FK_CertSolser', $SolicitudServicio->ID_SolSer)->get();

				foreach ($documentos as $key => $documento) {
					$docdato = DocDato::where('FK_DatoDoc', $documento->ID_Doc)->get();

					foreach ($docdato as $key => $dato) {
							DocDato::destroy($dato->ID_Dato);
					}
					Documento::destroy($documento->ID_Doc);
				}

				SolicitudServicio::destroy($SolicitudServicio->ID_SolSer);

				break;

			default:
				abort(503, 'el servicio no puede ser eliminado si ya fue recibido en Planta');
				break;
		}


		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Eliminado";
		$log->AuditRegistro=$SolicitudServicio->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$SolicitudServicio->SolSerDelete;
		$log->save();
		$SolicitudServicio->save();

		return redirect()->route('solicitud-servicio.index');
	}

	/**
	 * list the related documents for specific solserv.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function solservdocindex($id)
	{
		if (in_array(Auth::user()->UsRol, Permisos::CLIENTE)) {
			$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName','personals.PersEmail')
			->where('solicitud_servicios.SolSerSlug', $id)
			->where('solicitud_servicios.SolSerStatus', 'Certificacion')
			->first();
			if (!$SolicitudServicio) {
				abort(403,'Sus residuos aun no han sido certificados');
			}
			$certificados = Certificado::where(function($query) use ($SolicitudServicio){
			    $UserSedeID = DB::table('personals')
			    ->join('cargos', 'cargos.ID_Carg', 'personals.FK_PersCargo')
			    ->join('areas', 'areas.ID_Area', 'cargos.CargArea')
			    ->join('sedes', 'sedes.ID_Sede', 'areas.FK_AreaSede')
			    ->join('clientes', 'clientes.ID_Cli', 'sedes.FK_SedeCli')
			    ->where('personals.ID_Pers', Auth::user()->FK_UserPers)
			    ->value('clientes.ID_Cli');

			    $query->where('FK_CertCliente', $UserSedeID);
			    $query->where('CertAuthJo', '!=', 0);
			    $query->where('CertAuthJl', '!=', 0);
			    $query->where('CertAuthDp', '!=', 0);
			    $query->where('FK_CertSolser', $SolicitudServicio->ID_SolSer);

			})
			->with(['tratamiento'])
			->get();
		}else{
			$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName','personals.PersEmail')
			->where('solicitud_servicios.SolSerSlug', $id)
			->first();
			if (!$SolicitudServicio) {
				abort(404);
			}


			$SolicitudServicio->cliente = Cliente::where('ID_CLi', $SolicitudServicio->FK_SolSerCliente)->first(['CliName', 'CliSlug']);

			if ($SolicitudServicio->cliente->CliCategoria == 'ClientePrepago') {
				$certificados = CertificadoExpress::with(['certdato.solres'])
				->where('FK_CertSolser', $SolicitudServicio->ID_SolSer)
				->get();

			} else {
				$certificados = Certificado::with(['certdato.solres'])
				->where('FK_CertSolser', $SolicitudServicio->ID_SolSer)
				->get();
			}
		}
		/* validacion para encontrar la fecha de recepción en planta del servicio */
		$fechaRecepcion = SolicitudServicio::find($SolicitudServicio->ID_SolSer)->programacionesrecibidas()->first();
		if($fechaRecepcion){
			$SolicitudServicio->recepcion = $fechaRecepcion->ProgVehSalida;
		}else{
			$SolicitudServicio->recepcion = null;
		}

		// return $certificados;
		return view('solicitud-serv.documentos', compact('SolicitudServicio', 'certificados'));
	}

	public function sendtobilling($id)
	{
		$Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
		if (!$Solicitud) {
			abort(404);
		}

		$Solicitud->SolServCertStatus=1;
		$Solicitud->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="enviado a facturacion";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$Solicitud->SolServCertStatus;
		$log->save();

		return redirect()->route('solicitud-servicio.show', ['id' => $id]);
	}

	public function updateRms(Request $request, $id)
	{
		$Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
		if (!$Solicitud) {
			abort(404);
		}

		$Solicitud->SolSerRMs=$request->input('SolServRM');
		$Solicitud->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="actualizados los RMs";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$request;
		$log->save();

		return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $id]);
	}

	public function solservdocstore($id)
	{

		$SolicitudServicio = SolicitudServicio::with(['SolicitudResiduo.requerimiento.tarifa.rangos' => function ($query){
			$query->orderBy('TarifaDesde', 'desc');
		}])->where('ID_SolSer', $id)->first();
		$serviciovalidado = $id;
		/*cuenta los diferentes generadores*/
		$generadoresdelasolicitud = GenerSede::whereHas('resgener.solres', function ($query) use ($serviciovalidado) {
		    $query->where('solicitud_residuos.FK_SolResSolSer', $serviciovalidado);
		})
		->with(['resgener' => function ($query) use ($serviciovalidado){
		    $query->with(['solres' => function ($query) use ($serviciovalidado){
		    	$query->where('FK_SolResSolSer', $serviciovalidado);
		    	$query->with(['requerimiento.tratamiento.gestor', 'requerimiento:ID_Req,FK_ReqTrata']);
		    }]);
		    $query->whereHas('solres', function ($query) use ($serviciovalidado){
		    	$query->where('FK_SolResSolSer', $serviciovalidado);
		    });
		}])
		->get();
		// return $generadoresdelasolicitud;
		/*consulta para el cliente de esta solicitud*/
		$cliente = Cliente::whereHas('sedes.generador', function ($query) use ($generadoresdelasolicitud) {
		    $query->where('generadors.ID_Gener', $generadoresdelasolicitud[0]->FK_GSede);
		})->first();
		foreach ($generadoresdelasolicitud as $genersede) {
			foreach ($genersede->resgener as $resgener) {
				foreach ($resgener->solres as $key) {
					if ($key->SolResKgConciliado > 0) {
						switch ($key->requerimiento->tratamiento->TratTipo) {
							case 0:
								// "tratamiento tipo: interno; Certificado";

								$certificadoprevio = Certificado::where('FK_CertTrat', $key->requerimiento->tratamiento->ID_Trat)
								->where('FK_CertSolser', $id)
								->where('FK_CertGenerSede', $genersede->ID_GSede)
								->first();

								$gestor = Sede::where('ID_Sede', $key->requerimiento->tratamiento->FK_TratProv)
								->first();

								if ((isset($certificadoprevio))&&($certificadoprevio->FK_CertTrat == $key->requerimiento->tratamiento->ID_Trat)&&($certificadoprevio->FK_CertGenerSede == $genersede->ID_GSede)) {

									$dato = new Certdato;
									$dato->FK_DatoCert = $certificadoprevio->ID_Cert;
									$dato->FK_DatoCertSolRes = $key->ID_SolRes;
									$dato->save();

								}else{

									$certificado = new Certificado;
									if ($key->requerimiento->tratamiento->TratName == 'TermoDestrucción') {
										$certificado->CertType = 0;
										$certificado->CertObservacion = "certificado con observacion generica";
									}else{
										$certificado->CertType = 1;
										$certificado->CertObservacion = "manifiesto con observacion generica";
									}
									$certificado->CertNumero = "";
									$certificado->CertManifNumero = "";
									$certificado->CertManifPrepend = "";
									$certificado->CertiEspName = "";
									$certificado->CertiEspValue = "";
									$certificado->CertSlug = hash('sha256', rand().time());
									$certificado->CertSrc = 'CertificadoDefault.pdf';
									// $certificado->CertNumRm = "C-130";
									$certificado->CertAuthHseq = 0;
									$certificado->CertAuthJl = 0;
									$certificado->CertAuthDp = 0;
									$certificado->CertAuthJo = 0;
									$certificado->CertAnexo = "anexo de certificado ".$key->requerimiento->tratamiento->TratName.$key->requerimiento->tratamiento->FK_TratProv;
									$certificado->FK_CertSolser = $id;
									$certificado->FK_CertCliente = $cliente->ID_Cli;
									$certificado->FK_CertGenerSede = $genersede->ID_GSede;
									$certificado->FK_CertGestor = $key->requerimiento->tratamiento->gestor->FK_SedeCli;
									$certificado->FK_CertTrat = $key->requerimiento->tratamiento->ID_Trat;
									switch ($SolicitudServicio->SolSerTipo) {
										case 'Externo':
											$certificado->FK_CertTransp = $cliente->ID_Cli;

											break;

										case 'Cliente':
											$certificado->FK_CertTransp = $cliente->ID_Cli;

											break;

										case 'Generador':
											$certificado->FK_CertTransp = $cliente->ID_Cli;

											break;

										case 'Interno':
											$certificado->FK_CertTransp = 1;
											break;

										default:
											$certificado->FK_CertTransp = 1;
											break;
									}

									$certificado->SolicitudServicio->SolicitudResiduo = $certificado->SolicitudServicio->SolicitudResiduo->map(function ($item) {
										$rm = SolicitudResiduo::where('SolResSlug', $item->SolResSlug)->first('SolResRM');
										$item->SolResRM2 = $rm->SolResRM;
										return $item;
									});
									$certificado->save();

									$dato = new Certdato;
									$dato->FK_DatoCert = $certificado->ID_Cert;
									$dato->FK_DatoCertSolRes = $key->ID_SolRes;
									$dato->save();

								}

								break;

							case 1:
								// "tratamiento tipo: externo ; manifiesto";
								/*se verifica si ya existe un documento con ese tratamiento para esa solicitud de servicio*/
								$manifiestoprevio = Manifiesto::where('FK_ManifTrat', $key->requerimiento->tratamiento->ID_Trat)
								->where('FK_ManifSolser', $id)
								->first();

								if ((isset($manifiestoprevio))&&($manifiestoprevio->FK_ManifTrat == $key->requerimiento->tratamiento->ID_Trat)) {

									$dato = new Manifdato;
									$dato->FK_DatoManif = $manifiestoprevio->ID_Manif;
									$dato->FK_DatoManifSolRes = $key->ID_SolRes;
									$dato->save();

								}else{

									$manifiesto = new Manifiesto;
									$manifiesto->ManifNumero = "";
									$manifiesto->ManifiEspName = "";
									$manifiesto->ManifiEspValue = "";
									$manifiesto->ManifObservacion = "manifiesto con observacion generica";
									$manifiesto->ManifSlug = hash('sha256', rand().time());
									$manifiesto->ManifSrc = 'ManifiestoDefault.pdf';
									$manifiesto->ManifNumRm = "M-16";
									$manifiesto->ManifAuthHseq = 0;
									$manifiesto->ManifAuthJl = 0;
									$manifiesto->ManifAuthDp = 0;
									$manifiesto->ManifAuthJo = 0;
									$manifiesto->ManifAnexo = "anexo de manifiesto ".$key->requerimiento->tratamiento->TratName.$key->requerimiento->tratamiento->FK_TratProv;
									$manifiesto->FK_ManifSolser = $id;
									$manifiesto->FK_ManifCliente = $cliente->ID_Cli;
									$manifiesto->FK_ManifGenerSede = $genersede->ID_GSede;
									$manifiesto->FK_ManifGestor = $key->requerimiento->tratamiento->gestor->FK_SedeCli;
									$manifiesto->FK_ManifTrat = $key->requerimiento->tratamiento->ID_Trat;
									switch ($SolicitudServicio->SolSerTipo) {
										case 'Externo':
											$manifiesto->FK_ManifTransp = $cliente->ID_Cli;

											break;

										case 'Cliente':
											$manifiesto->FK_ManifTransp = $cliente->ID_Cli;

											break;

										case 'Generador':
											$manifiesto->FK_ManifTransp = $genersede->ID_GSede;

											break;

										case 'Interno':
											$manifiesto->FK_ManifTransp = 1;
											break;

										default:
											$manifiesto->FK_ManifTransp = 1;
											break;
									}
									$manifiesto->save();

									// Generar PDF inmediatamente para manifiestos de aprovechamiento
									if (stripos($key->requerimiento->tratamiento->TratName, 'APROVECHAMIENTO') !== false) {
										try {
											// Generar QR Code
											$qrCode = new QrCode(route('manifiestos.show', ['manifiesto' => $manifiesto->ManifSlug]));
											$qrCode->setLogoPath(asset('img/LogoQR.png'));
											$qrCode->setLogoSize(60, 60);
											$qrCode->setSize(300);
											$qrCode->setMargin(0);
											$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK);

											// Generar PDF - usar plantilla específica para aprovechamiento
											$view = (stripos($key->requerimiento->tratamiento->TratName, 'APROVECHAMIENTO') !== false) 
												? 'certificados.topdfmanifiesto-aprovechamiento' 
												: 'certificados.topdfmanifiesto';
											$pdf = PDF::setPaper('letter', 'portrait')->loadView($view, compact(['manifiesto', 'qrCode']));
											$nombre = $manifiesto->ManifSlug . '.pdf';
											$path = 'public/manifiestosRegular/' . $nombre;

											Storage::put($path, $pdf->output(), 'public');

											// Actualizar ManifSrc
											$manifiesto->update(['ManifSrc' => $nombre]);

										} catch (Exception $e) {
											// Log error but continue
											Log::error('Error generando PDF para manifiesto ID: ' . $manifiesto->ID_Manif . ' - ' . $e->getMessage());
										}
									}

									$dato = new Manifdato;
									$dato->FK_DatoManif = $manifiesto->ID_Manif;
									$dato->FK_DatoManifSolRes = $key->ID_SolRes;
									$dato->save();
								}

								break;

							default:
								return back()->withErrors(['msg' => ['alguno de los residuos no posee tratamiento asignado favor verifica que su asesor comercial la evaluacion de los residuos.']]);
								break;
						}
					}
				}
			}
		}

		/*ajuste de los precios para facturacion en cada residuo de la solicitud segun los rangos de tarifas */

		// Generar PDFs automáticamente para certificados y manifiestos de aprovechamiento
		
		// 1. Certificados de aprovechamiento (CertType = 1)
		$certificados = Certificado::with(['tratamiento', 'SolicitudServicio', 'cliente', 'sedegenerador', 'gestor'])
			->where('FK_CertSolser', $id)
			->where('CertType', 1) // Solo manifiestos (aprovechamiento)
			->get();

		foreach ($certificados as $certificado) {
			try {
				// Generar QR Code
				$qrCode = new QrCode(route('certificados.show', ['certificado' => $certificado->CertSlug]));
				$qrCode->setLogoPath(asset('img/LogoQR.png'));
				$qrCode->setLogoSize(60, 60);
				$qrCode->setSize(300);
				$qrCode->setMargin(0);
				$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK);
				
				// Generar PDF - usar plantilla específica para aprovechamiento
				$view = (stripos($certificado->tratamiento->TratName, 'APROVECHAMIENTO') !== false) 
					? 'certificados.topdfmanifiesto-aprovechamiento' 
					: 'certificados.topdfmanifiesto';
				$pdf = PDF::setPaper('letter', 'portrait')->loadView($view, compact(['certificado', 'qrCode']));
				$nombre = $certificado->CertSlug . '.pdf';
				$path = 'public/manifiestosRegular/' . $nombre;
				
				Storage::put($path, $pdf->output(), 'public');
				
				// Actualizar CertSrc
				$certificado->update(['CertSrc' => $nombre]);
				
			} catch (Exception $e) {
				// Log error but continue with other certificates
				Log::error('Error generando PDF para certificado ID: ' . $certificado->ID_Cert . ' - ' . $e->getMessage());
			}
		}
		
		// 2. Manifiestos de aprovechamiento (tabla manifiestos)
		$manifiestos = Manifiesto::with(['tratamiento', 'SolicitudServicio', 'cliente', 'sedegenerador', 'gestor'])
			->where('FK_ManifSolser', $id)
			->get();

		foreach ($manifiestos as $manifiesto) {
			try {
				// Verificar si es un tratamiento de aprovechamiento
				if (stripos($manifiesto->tratamiento->TratName, 'APROVECHAMIENTO') !== false) {
					// Generar QR Code
					$qrCode = new QrCode(route('manifiestos.show', ['manifiesto' => $manifiesto->ManifSlug]));
					$qrCode->setLogoPath(asset('img/LogoQR.png'));
					$qrCode->setLogoSize(60, 60);
					$qrCode->setSize(300);
					$qrCode->setMargin(0);
					$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK);
					
					// Generar PDF
					$pdf = PDF::setPaper('letter', 'portrait')->loadView('certificados.topdfmanifiesto', compact(['manifiesto', 'qrCode']));
					$nombre = $manifiesto->ManifSlug . '.pdf';
					$path = 'public/manifiestosRegular/' . $nombre;
					
					Storage::put($path, $pdf->output(), 'public');
					
					// Actualizar ManifSrc
					$manifiesto->update(['ManifSrc' => $nombre]);
				}
				
			} catch (Exception $e) {
				// Log error but continue with other manifests
				Log::error('Error generando PDF para manifiesto ID: ' . $manifiesto->ID_Manif . ' - ' . $e->getMessage());
			}
		}

		foreach ($SolicitudServicio->SolicitudResiduo as $key => $solres) {
			switch ($solres->SolResTypeUnidad) {
				case 'Unidad':
					$tarifatipo = 'Unid';
					break;

				case 'Litros':
					$tarifatipo = 'Lt';
					break;

				default:
					$tarifatipo = 'Kg';
					break;
			}

			$tarifaCliente = CTarifa::with('rangos')
			->where('FK_Cliente', $cliente->ID_Cli)
			->where('FK_Tratamiento', $solres->requerimiento->FK_ReqTrata)
			->where('Tarifatipo', $tarifatipo)
			->first();

			$tarifaResiduo = $solres->requerimiento->tarifa;


			$residuoparaprecio = SolicitudResiduo::where('ID_SolRes', $solres->ID_SolRes)->first();

			if ($tarifaResiduo === null || $solres->SolResKgConciliado <= 0) {
				$residuoparaprecio->SolResPrecio = 0;
			} else {
				if ($tarifaResiduo->TarifaSpecial === 1) {
					foreach ($tarifaResiduo->rangos as $rango) {
						switch ($solres->SolResTypeUnidad) {
							case 'Unidad':
							case 'Litros':
								if ($solres->SolResCantiUnidadConciliada >= $rango->TarifaDesde) {
									$residuoparaprecio->SolResPrecio = $rango->TarifaPrecio;
									$residuoparaprecio->SolResTypePrecio = 1;
									break 2;
								}
								break;
							default:
								if ($solres->SolResKgConciliado >= $rango->TarifaDesde) {
									$residuoparaprecio->SolResPrecio = $rango->TarifaPrecio;
									$residuoparaprecio->SolResTypePrecio = 1;
									break 2;
								}
								break;
						}
					}
				}else{
					if ($tarifaCliente !== null) {
						foreach ($tarifaCliente->rangos as $rango) {
							switch ($solres->SolResTypeUnidad) {
								case 'Unidad':
								case 'Litros':
									if ($solres->SolResCantiUnidadConciliada > $rango->CTarifaDesde) {
										$residuoparaprecio->SolResPrecio = $rango->CTarifaPrecio;
										$residuoparaprecio->SolResTypePrecio = 2;
										break 2;
									}
									break;
								default:
									if ($solres->SolResKgConciliado > $rango->CTarifaDesde) {
										$residuoparaprecio->SolResPrecio = $rango->CTarifaPrecio;
										$residuoparaprecio->SolResTypePrecio = 2;
										break 2;
									}
									break;
							}
						}
					}

				}
			}
			$residuoparaprecio->save();
		}
	}

	/**
	 * muestra el formulario para añadir residuos adicionales al servicio en status Residuo Faltante.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function addRespel($id)
	{
		if(in_array(Auth::user()->UsRol, Permisos::CLIENTE) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL)){
			$Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
			if (!$Solicitud) {
				abort(404);
			}
			if($Solicitud->SolSerStatus !== 'Residuo Faltante' && $Solicitud->SolSerStatus !== 'Programado'  && $Solicitud->SolSerStatus !== 'Notificado'){
				abort(403, 'El servicio no se encuentra en el estado correcto para añadir residuos');
			}
			if($Solicitud->SolSerCityTrans <> null){
				$Municipio = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->SolSerCityTrans)->first();
				$Departamento = Departamento::where('ID_Depart', $Municipio->FK_MunCity)->first();
				$Municipios = Municipio::where('FK_MunCity', $Departamento->ID_Depart)->get();
			}
			if($Solicitud->FK_SolSerCollectMun <> null){
				$Municipio2 = Municipio::select('FK_MunCity')->where('ID_Mun', $Solicitud->FK_SolSerCollectMun)->first();
				$Departamento2 = Departamento::where('ID_Depart', $Municipio2->FK_MunCity)->first();
				$Municipios2 = Municipio::where('FK_MunCity', $Departamento2->ID_Depart)->get();
			}
			$Departamentos = Departamento::all();
			$Cliente = Cliente::where('ID_Cli', $Solicitud->FK_SolSerCliente)->first();
            $Requerimientos = RequerimientosCliente::where('FK_RequeClient', $Cliente->ID_Cli)->get();
			$Sedes = Sede::select('SedeSlug','SedeName', 'ID_Sede')->where('FK_SedeCli', $Cliente->ID_Cli)->get();
			$SGeneradors = DB::table('gener_sedes')
				->join('generadors', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
				->join('sedes', 'generadors.FK_GenerCli', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('gener_sedes.GSedeSlug', 'gener_sedes.GSedeName', 'generadors.GenerName')
				->where('clientes.ID_Cli', $Solicitud->FK_SolSerCliente)
				->get();
			$Persona = Personal::where('ID_Pers', $Solicitud->FK_SolSerPersona)
				->select('PersSlug','PersFirstName','PersLastName')
				->first();
			$Personals = DB::table('personals')
				->join('cargos', 'personals.FK_PersCargo', '=', 'cargos.ID_Carg')
				->join('areas', 'cargos.CargArea', '=', 'areas.ID_Area')
				->join('sedes', 'areas.FK_AreaSede', '=', 'sedes.ID_Sede')
				->join('clientes', 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('personals.PersSlug', 'personals.PersFirstName', 'personals.PersLastName', 'personals.PersEmail')
				->where('clientes.ID_Cli', userController::IDClienteSegunUsuario())
				->where('personals.PersDelete', 0)
				->get();
			$KGenviados = DB::table('solicitud_residuos')
				->select('SolResKgEnviado')
				->where('FK_SolResSolSer', $Solicitud->ID_SolSer)
				->get();
			$totalenviado = 0;
			foreach ($KGenviados as $KGenviado) {
				$totalenviado = $totalenviado + $KGenviado->SolResKgEnviado;
			}
            //return view('solicitud-serv.addrespel', compact('Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios', 'Departamento2','Municipios2', 'Sedes', 'totalenviado', 'Requerimientos'));
			//return view('solicitud-serv.addrespel', compact('Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios', 'Sedes', 'totalenviado', 'Requerimientos',);
			return view('solicitud-serv.addrespel', compact('Solicitud','Cliente','Persona','Personals','Departamentos','SGeneradors', 'Departamento','Municipios', 'Sedes', 'totalenviado', 'Requerimientos'));
			//return $SGeneradors;
		}
		else{
			abort(403);
		}
	}

	/**
	 * ingresa los residuos adicionales a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateRespel(Request $request, $id)
	{
		// return $request;
		$SolicitudServicio = SolicitudServicio::where('SolSerSlug', $id)->first();
		if (!$SolicitudServicio) {
			abort(404, 'solicitud de servicio no encontrada');
		}

		if(!is_null($request->input('SGenerador'))){
			$this->createSolRes($request, $SolicitudServicio->ID_SolSer);
		}

		if(in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL)){
			$SolicitudServicio->SolSerStatus = 'Programado';
		} else {
			$SolicitudServicio->SolSerStatus = 'Notificado';
		}
		$SolicitudServicio->SolSerDescript = $request->input('SolSerDescript');
		$SolicitudServicio->save();
		

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="residuos adicionales";
		$log->AuditRegistro=$SolicitudServicio->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=json_encode($request->all());
		$log->save();

		/*se guarda la observacion inicial de la creación del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = $SolicitudServicio->SolSerStatus;
		if ($SolicitudServicio->SolSerDescript = "") {
			$Observacion->ObsMensaje = 'Residuos faltantes ya incluidos por el cliente';
		}else{
			$Observacion->ObsMensaje = $SolicitudServicio->SolSerDescript;
		}
		$Observacion->ObsTipo = 'cliente';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $SolicitudServicio->ID_SolSer;
		$Observacion->save();

		$SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudServicio->FK_SolSerCliente)->first();
		// se establece la lista de destinatarios
		$destinatarios = ['conciliaciones@prosarc.com.co'];
		$destinatarioscc = ['recepcionpda@prosarc.com.co'];

		if ($SolicitudServicio['cliente']->CliComercial <> null) {
			$comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
			array_push($destinatarioscc, $comercial->PersEmail);
		}else{
			$comercial = "";
		}

		$SolicitudServicio['comercial'] = $comercial;
		$SolicitudServicio['personalcliente'] = Personal::where('ID_Pers', $SolicitudServicio->FK_SolSerPersona)->first();

		// añadir destinatarios para copia del cliente
		if ($SolicitudServicio->SolServMailCopia !== "null") {
			foreach (json_decode($SolicitudServicio->SolServMailCopia) as $key => $value) {
				array_push($destinatarioscc, $value);
			}
		}

		Mail::to($destinatarios)->cc($destinatarioscc)->send(new SolSerLeftRespel($SolicitudServicio));

		if(in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL) || in_array(Auth::user()->UsRol, Permisos::RECIBOMATERIAL)){
			return redirect()->route('recibo.material', ['id' => $id]);
		} else {
			return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $id]);
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function serviciosCompletados()
	{
		if(in_array(Auth::user()->UsRol, Permisos::CLIENTE)){
			abort(401, 'no tiene autorización para acceder a esta página');
		}

		$Servicios = DB::table('solicitud_servicios')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('personals as Comercial', 'Comercial.ID_Pers', '=', 'clientes.CliComercial')
			->select('solicitud_servicios.ID_SolSer',
			'solicitud_servicios.SolSerStatus',
			'solicitud_servicios.SolSerTipo',
			'solicitud_servicios.SolSerAuditable',
			'solicitud_servicios.SolSerConductor',
			'solicitud_servicios.SolSerVehiculo',
			'solicitud_servicios.SolSerSlug',
			'solicitud_servicios.created_at',
			'solicitud_servicios.updated_at',
			'solicitud_servicios.SolSerDelete',
			'solicitud_servicios.SolResAuditoriaTipo',
			'solicitud_servicios.SolSerNameTrans',
			'solicitud_servicios.SolSerNitTrans',
			'solicitud_servicios.SolSerAdressTrans',
			'solicitud_servicios.SolSerTypeCollect',
			'solicitud_servicios.SolSerCollectAddress',
			'solicitud_servicios.SolServCertStatus',
			'clientes.CliName',
			'clientes.CliSlug',
			'clientes.CliStatus',
			'clientes.TipoFacturacion',
			'clientes.CliCategoria',
			'personals.PersFirstName',
			'personals.PersLastName',
			'personals.PersSlug',
			'personals.PersEmail',
			'personals.PersCellphone',
			'Comercial.PersFirstName as ComercialPersFirstName',
			'Comercial.PersLastName as ComercialPersLastName',
			'Comercial.PersSlug as ComercialPersSlug',
			'Comercial.PersEmail as ComercialPersEmail',
			'Comercial.PersCellphone as ComercialPersCellphone')
			->where('solicitud_servicios.SolSerStatus', 'Completado')
			->where('clientes.CliCategoria', 'Cliente')
			->orderBy('created_at', 'desc')
			->get();

		$Cliente = Cliente::select('CliName','ID_Cli', 'CliStatus')->where('ID_Cli',userController::IDClienteSegunUsuario())->first();
		foreach ($Servicios as $servicio) {
			/* validacion para encontrar la fecha de recepción en planta del servicio */
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if($fechaRecepcion){
				$servicio->recepcion = $fechaRecepcion->ProgVehSalida;
			}else{
				$servicio->recepcion = null;
			}
			$servicio->ultimoRecordatorio = SolicitudServicio::find($servicio->ID_SolSer)->ultimorecordatorio();
			$servicio->fechaCompletado = SolicitudServicio::find($servicio->ID_SolSer)->fechacompletado();
		}

		// return $Servicios;

		//return view('solicitud-serv.indexrecordatorios', compact('Servicios', 'Residuos', 'Cliente'));
		return view('solicitud-serv.indexrecordatorios', compact('Servicios', 'Cliente'));
	}

	public function reversarStatus(Request $request)
	{
		$Solicitud = SolicitudServicio::with('SolicitudResiduo')->where('SolSerSlug', $request->input('solserslug'))->first();
		if (!$Solicitud) {
			abort(404);
		}
		if ($Solicitud->SolSerStatus == 'Certificacion') {
			if (!in_array(Auth::user()->UsRol, Permisos::REVERSARADMIN) && !in_array(Auth::user()->UsRol2, Permisos::REVERSARADMIN)) {
				abort(403, 'el servicio ya ha sido certificado y no admite cambios de status');
			}
		}
        // se guarda el status nuevo y el anterior
        $oldValue = $Solicitud->SolSerStatus;
        $newValue = $request->input('solserstatus');


		switch ($request->input('solserstatus')) {
			case 'Notificado':
			case 'Completado':
			case 'Residuo Faltante':
			case 'Corregido':
			case 'Programado':
			case 'No Conciliado':
			case 'Residuo Faltante':
				if ($Solicitud->SolSerStatus == 'Conciliado'||$Solicitud->SolSerStatus == 'Tratado'||$Solicitud->SolSerStatus == 'Certificacion'||$Solicitud->SolSerStatus == 'Facturado') {
					$certificadosDelete = Certificado::with('certdato')->where('FK_CertSolser', $Solicitud->ID_SolSer)->get();
					foreach ($certificadosDelete as $key => $value) {
						foreach ($value->certdato as $key2 => $value2) {
							$value2->delete();
						}
						//$value->delete();
					}
					foreach ($Solicitud->SolicitudResiduo as $key => $residuoparareversar) {
						$residuoparareversar->SolResPrecio = 0;
						$residuoparareversar->SolResTypePrecio = 0;
						$residuoparareversar->save();
					}
					$prefaturaToDelete = Prefactura::with('prefacTratamiento.prefacresiduo')->where('FK_Servicio', $Solicitud->ID_SolSer)->get();
					foreach ($prefaturaToDelete as $key => $value) {
						foreach ($value->prefacTratamiento as $key2 => $value2) {
							foreach ($value2->prefacresiduo as $key3 => $value3) {
								$value3->delete();
							}
							$value2->delete();
						}
						$value->delete();
					}
				}
				break;

			case 'Conciliado':
				if ($Solicitud->SolSerStatus == 'Tratado'||$Solicitud->SolSerStatus == 'Certificacion'||$Solicitud->SolSerStatus == 'Facturado') {
					$prefaturaToDelete = Prefactura::with('prefacTratamiento.prefacresiduo')->where('FK_Servicio', $Solicitud->ID_SolSer)->get();
					foreach ($prefaturaToDelete as $key => $value) {
						foreach ($value->prefacTratamiento as $key2 => $value2) {
							foreach ($value2->prefacresiduo as $key3 => $value3) {
								$value3->delete();
							}
							$value2->delete();
						}
						$value->delete();
					}
				}
				break;
		}
		$Solicitud->SolSerStatus = $request->input('solserstatus');
		$Solicitud->SolSerDescript = $request->input('solserdescript');
		$Solicitud->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Reversado Status";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=[$Solicitud->SolSerStatus, $Solicitud->SolSerDescript];
		$log->save();


		/*se guarda la observacion de la modificacion del servicio*/
		$Observacion = new Observacion();
		$Observacion->ObsStatus = 'Devuelto a status: '.$Solicitud->SolSerStatus;
		$Observacion->ObsMensaje = $Solicitud->SolSerDescript;
		$Observacion->ObsTipo = 'prosarc';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $Solicitud->ID_SolSer;
		$Observacion->save();

        $Solicitud['oldValue'] = $oldValue;
        $Solicitud['newValue'] = $newValue;

        $SolicitudServicio = $Solicitud;

        // verificar el status anterior del servicio para validar si se eliminaron certificados o manifiestos y enviar la notificacion al comercial

        // se consulta el correo del comercial respectivo
        $comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();

        // se verifica si el cliente tiene comercial asignado
        $SolicitudServicio['cliente'] = Cliente::where('ID_Cli', $SolicitudServicio->FK_SolSerCliente)->first();
        // se establece la lista de destinatarios
        if ($SolicitudServicio['cliente']->CliComercial <> null) {
            $comercial = Personal::where('ID_Pers', $SolicitudServicio['cliente']->CliComercial)->first();
            $destinatarios = ['subgerencia@prosarc.com.co',
                                $comercial->PersEmail
                            ];
        }else{
            $comercial = "";
            $destinatarios = ['subgerencia@prosarc.com.co'];
        }

        // enviar correo  al comercial respectivo
        Mail::to($destinatarios)->send(new ServicioReversado($SolicitudServicio, $Observacion));

		return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $Solicitud->SolSerSlug]);
		//return redirect()->route('solicitud-servicio.show', ['id' => $Solicitud->SolSerSlug]);

	}

	public function CancelarServicio(Request $request)
	{
		// return $request;
		$Solicitud = SolicitudServicio::where('SolSerSlug', $request->input('solserslug'))->first();
		if (!$Solicitud) {
			abort(404);
		}

		$statusAllowingCancel = ['Pendiente',
								'Cancelado',
								'Aceptado',
								'Aprobado',
								'Programado',
								'Notificado'];

		if (!in_array($Solicitud->SolSerStatus, $statusAllowingCancel)) {
			abort(403, 'el servicio #'.$Solicitud->ID_SolSer.' no debe ser cancelado, ya que se encuentra en status '.$Solicitud->SolSerStatus);
		}

		// eliminar las programaciones relacionadas con el servicio
		$programacionesDelete = ProgramacionVehiculo::where('FK_ProgServi', $Solicitud->ID_SolSer)
		->where('ProgVehDelete', 0)
		->get();

		foreach ($programacionesDelete as $key => $value) {
			$value->ProgVehDelete = 1;
			$value->save();
		}

		// cabiar el status del servicio
		switch ($request->input('solserstatus')) {
			case 'Aprobado':
				$Solicitud->SolSerStatus = 'Aprobado';
				break;
			case 'Cancelado':
				$Solicitud->SolSerStatus = 'Cancelado';
				break;
		}
		$Solicitud->SolSerDescript = $request->input('solserdescript');
		$Solicitud->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="Servicio cancelado";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=[$Solicitud->SolSerStatus, $Solicitud->SolSerDescript];
		$log->save();


		/*se guarda la observacion de la modificacion del servicio*/
		$Observacion = new Observacion();
		// cabiar el status de la observación
		switch ($request->input('solserstatus')) {
			case 'Aprobado':
				$Observacion->ObsStatus = 'Reactivado';
				break;
			case 'Cancelado':
				$Observacion->ObsStatus = 'Cancelado';
				break;
		}
		$Observacion->ObsMensaje = $Solicitud->SolSerDescript;
		$Observacion->ObsTipo = 'prosarc';
		$Observacion->ObsRepeat = 1;
		$Observacion->ObsDate = now();
		$Observacion->ObsUser = Auth::user()->email;
		$Observacion->ObsRol = Auth::user()->UsRol;
		$Observacion->FK_ObsSolSer = $Solicitud->ID_SolSer;
		$Observacion->save();
		return redirect()->route('solicitud-servicio.index');
	}

	/**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	public function NumFactura(Request $request, $id)
	{

		$Solicitud = SolicitudServicio::where('SolSerSlug', $id)->first();
		if (!$Solicitud) {
			abort(404);
		}

		$Solicitud->SolNumeroFactura=$request->input('numero_factura');
		$Solicitud->save();

		$log = new audit();
		$log->AuditTabla="solicitud_servicios";
		$log->AuditType="actualizado la FVE";
		$log->AuditRegistro=$Solicitud->ID_SolSer;
		$log->AuditUser=Auth::user()->email;
		$log->Auditlog=$request;
		$log->save();

		//return $Solicitud;

		return redirect()->route('solicitud-servicio.show', ['solicitud_servicio' => $id]);
		
	}

	/**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	 public function recibomaterial($id){

        $users  = Auth::user()->id;
        
		$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('cargos', 'personals.FK_PersCargo', '=', 'ID_Carg')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName', 'personals.PersEmail', 'personals.PersCellphone', 'cargos.CargName')
			->where('solicitud_servicios.SolSerSlug', $id)
			->first();
		if($SolicitudServicio->SolSerTypeCollect === null){

			$SolSerConductor = $SolicitudServicio->SolSerConductor;

			if($SolicitudServicio->SolSerTipo == 'Interno'){
				$SolSerConductor = Personal::where('ID_Pers', $SolicitudServicio->SolSerConductor)->first();
			}
			if($SolicitudServicio->SolSerTypeCollect == 98){
				$Address = Sede::select(['SedeAddress', 'SedeName'])->where('ID_Sede',$SolicitudServicio->SolSerCollectAddress)->first();
				$SolSerCollectAddress = $Address->SedeName.' - '.$Address->SedeAddress;
			}

					$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
					->where('ProgVehDelete', 0)
					->get();

					$ProgramacionesActivas = count(ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
					->where('ProgVehEntrada', null)
					->where('ProgVehDelete', 0)
					->get());

			$Cliente = DB::table('clientes')
				->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
				->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName')
				->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
				->first();

			$GenerResiduos = DB::table('solicitud_residuos')
			->distinct()
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('sedes', 'sedes.ID_Sede', '=', 'generadors.FK_GenerCli')
			->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->join('firmas_servicio', 'firmas_servicio.FK_Gener', '=', 'clientes.ID_Cli')
			->join('municipios', 'municipios.ID_Mun', '=', 'gener_sedes.FK_GSedeMun')
			->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener','generadors.ID_Gener', 'generadors.FK_GenerCli', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress', 'gener_sedes.GSedeEmail', 'gener_sedes.GSedeCelular',  'firmas_servicio.SlugFirmas', 'municipios.MunName')
			->where('firmas_servicio.FK_SolSer', $SolicitudServicio->ID_SolSer)
			//->select('residuos_geners.*')
			//->where('firmas_servicio.FK_SolSer', $SolicitudServicio->ID_SolSer)
			->get();
			//return $GenerResiduos;
				
			$Residuosoriginal = DB::table('solicitud_residuos')
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
				->join('requerimientos' , 'solicitud_residuos.FK_SolResRequerimiento', '=', 'requerimientos.ID_Req')
				->join('tratamientos' , 'requerimientos.FK_ReqTrata', '=', 'tratamientos.ID_Trat')
				->join('sedes' , 'tratamientos.FK_TratProv', '=', 'sedes.ID_Sede')
				->join('clientes' , 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'tratamientos.ID_Trat', 'clientes.CliShortName')
				->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
				->get();
            //return $Residuosoriginal;					

			$Residuos = $Residuosoriginal->map(function ($item) {
				$requerimientos = Requerimiento::with(['pretratamientosSelected', 'tarifa.rangos' => function($query){
					$query->orderBy('TarifaDesde');
				}])
				->where('ID_Req', $item->FK_SolResRequerimiento)
				->first();

				$rm = SolicitudResiduo::with('SolicitudServicio')->where('SolResSlug', $item->SolResSlug)->first(['SolResRM', 'FK_SolResSolSer']);

				$item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
				$item->tarifa = $requerimientos->tarifa;
				if ($requerimientos->tarifa->TarifaSpecial === 1) {
					switch ($item->SolResTypeUnidad) {
						case 'Unidad':
							$tarifatipo = 'Unid';
							break;

						case 'Litros':
							$tarifatipo = 'Lt';
							break;

						default:
							$tarifatipo = 'Kg';
							break;
					}

					$tarifaResiduo = CTarifa::with('rangos')
						->where('FK_Cliente', $rm->SolicitudServicio->FK_SolSerCliente)
						->where('FK_Tratamiento', $requerimientos->FK_ReqTrata)
						->where('Tarifatipo', $tarifatipo)
						->first();

					if ($tarifaResiduo === null) {
						$item->ctarifa = null;
					}else{
						$item->ctarifa = $tarifaResiduo;
					}
				}else{
					$item->ctarifa = null;
				}
				$item->SolResRM2 = $rm->SolResRM;
				return $item;
			});

			$SolicitudServicio->Repetible = 0;

			/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerRMs usando eloquent*/
			$rms = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerRMs');
			$SolicitudServicio->SolSerRMs = $rms->SolSerRMs;

			foreach ($Residuos as $residuo => $value) {
				$requerimientos = Requerimiento::with(['pretratamientosSelected'])
				->where('ID_Req', $value->FK_SolResRequerimiento)
				->first();
				$residuoSinTratamiento = Requerimiento::where('FK_ReqRespel', $requerimientos->FK_ReqRespel)
				->where('ofertado', 1)
				->where('forevaluation', 1)
				->first();


				if ($residuoSinTratamiento == null) {
					$SolicitudServicio->Repetible++;
				}
			}

			$SolicitudesServicioscount = SolicitudServicio::with(['Personal', 'cliente', 'municipio', 'SolicitudResiduo'])
				->where('ID_SolSer', $SolicitudServicio->ID_SolSer)
				->orderBy('created_at', 'desc')
				->get();

			/*se inicializan las variables para el calculo de totales */
			$total['estimado'] = 0;
			$total['recibido'] = 0;
			$total['conciliado'] = 0;
			$total['tratado'] = 0;
			$cantidadesXtratamiento = [];


			/* se itera sobre todos los residuos de las solicitudes de servicio */
			foreach ($SolicitudesServicioscount as $servicio) {
				foreach ($servicio->SolicitudResiduo as $residuo) {
					$collection = collect($cantidadesXtratamiento);

					/* si el tratamiento existe en la lista se suman las cantidadesxtratamiento y los totales correspondientes */
					if ($collection->has($residuo->requerimiento->tratamiento->TratName)) {
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] + $residuo->SolResKgEnviado;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] + $residuo->SolResKgRecibido;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] + $residuo->SolResKgConciliado;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] + $residuo->SolResKgTratado;
						$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
						$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
						$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
						$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
					}else{
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $residuo->SolResKgEnviado;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $residuo->SolResKgRecibido;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $residuo->SolResKgConciliado;
						$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $residuo->SolResKgTratado;
						$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
						$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
						$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
						$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
					}
				}
			}
			if (in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol, Permisos::SolSer1)) {
				$tratamientos = Tratamiento::join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
				->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->select('*')
				->get();
			}else{
				$tratamientos = 'NoAutorizado';
			}

			/* validacion para encontrar la fecha de recepción en planta del servicio */
			$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
			if($fechaRecepcion){
				$SolicitudServicio->recepcion = $fechaRecepcion->ProgVehSalida;
			}else{
				$SolicitudServicio->recepcion = null;
			}
			//Buscar corrientes del residuo
			
				$PublicRespels = DB::table('solicitud_residuos')
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
				->select('respels.ID_Respel', 'respels.YRespelClasf4741', 'respels.ARespelClasf4741')
				->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
				->distinct()
				->get();
				//return $GenerResiduos;
			// adjuntar variables segun status del servicio
			switch ($SolicitudServicio->SolSerStatus) {
				case 'Residuo Faltante':
				case 'Notificado':
					return view('solicitud-serv.rmplanta', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerConductor', 'Programaciones', 'ProgramacionesActivas', 'total', 'cantidadesXtratamiento', 'tratamientos', 'PublicRespels'));
					break;
				case 'Programado':
				return view('solicitud-serv.rmplanta', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerConductor', 'Programaciones', 'ProgramacionesActivas', 'total', 'cantidadesXtratamiento', 'tratamientos', 'PublicRespels'));
					break;
				case 'Corregido':
				case 'Completado':
				default:
					break;
			}
		} else {

			$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('cargos', 'personals.FK_PersCargo', '=', 'ID_Carg')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName', 'personals.PersEmail', 'personals.PersCellphone', 'cargos.CargName')
			->where('solicitud_servicios.SolSerSlug', $id)
			->first();
		if (!$SolicitudServicio) {
			abort(404);
		}

		$SolSerConductor = $SolicitudServicio->SolSerConductor;

		if($SolicitudServicio->SolSerTipo == 'Interno'){
			$SolSerConductor = Personal::where('ID_Pers', $SolicitudServicio->SolSerConductor)->first();
		}
		if($SolicitudServicio->SolSerTypeCollect == 98){
			$Address = Sede::select(['SedeAddress', 'SedeName'])->where('ID_Sede',$SolicitudServicio->SolSerCollectAddress)->first();
			$SolSerCollectAddress = $Address->SedeName.' - '.$Address->SedeAddress;
		}

				$Programaciones = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehDelete', 0)
				->get();

				$ProgramacionesActivas = count(ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehEntrada', null)
				->where('ProgVehDelete', 0)
				->get());

		$Cliente = DB::table('clientes')
			->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
			->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName')
			->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
			->first();
		
		$GenerResiduos = DB::table('solicitud_residuos')
			->distinct()
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('firmas_servicio', 'firmas_servicio.FK_Gener', '=', 'generadors.ID_Gener')
			->join('municipios', 'municipios.ID_Mun', '=', 'gener_sedes.FK_GSedeMun')
			->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener','generadors.ID_Gener', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress', 'gener_sedes.GSedeEmail', 'gener_sedes.GSedeCelular', 'municipios.MunName', 'firmas_servicio.SlugFirmas', 'firmas_servicio.FK_SolSer', 'solicitud_residuos.SolResKgEnviado')
			->where('firmas_servicio.FK_SolSer', $SolicitudServicio->ID_SolSer)
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->groupBy('gener_sedes.ID_GSede', 'residuos_geners.FK_SGener')
			->get();
		//return $GenerResiduos;
		$Residuosoriginal = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->join('requerimientos' , 'solicitud_residuos.FK_SolResRequerimiento', '=', 'requerimientos.ID_Req')
			->join('tratamientos' , 'requerimientos.FK_ReqTrata', '=', 'tratamientos.ID_Trat')
			->join('sedes' , 'tratamientos.FK_TratProv', '=', 'sedes.ID_Sede')
			->join('clientes' , 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
			->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'tratamientos.ID_Trat', 'clientes.CliShortName')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			// ->where('requerimientos.ofertado', 1)
	        // ->where('forevaluation', 0)
			->get();

		$Residuos = $Residuosoriginal->map(function ($item) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected', 'tarifa.rangos' => function($query){
				$query->orderBy('TarifaDesde');
			}])
			->where('ID_Req', $item->FK_SolResRequerimiento)
			// ->where('forevaluation', 0)
			->first();

			$rm = SolicitudResiduo::with('SolicitudServicio')->where('SolResSlug', $item->SolResSlug)->first(['SolResRM', 'FK_SolResSolSer']);

	        $item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
	        $item->tarifa = $requerimientos->tarifa;
			if ($requerimientos->tarifa->TarifaSpecial === 1) {
				switch ($item->SolResTypeUnidad) {
					case 'Unidad':
						$tarifatipo = 'Unid';
						break;

					case 'Litros':
						$tarifatipo = 'Lt';
						break;

					default:
						$tarifatipo = 'Kg';
						break;
				}

				$tarifaResiduo = CTarifa::with('rangos')
					->where('FK_Cliente', $rm->SolicitudServicio->FK_SolSerCliente)
					->where('FK_Tratamiento', $requerimientos->FK_ReqTrata)
					->where('Tarifatipo', $tarifatipo)
					->first();

				if ($tarifaResiduo === null) {
					$item->ctarifa = null;
				}else{
					$item->ctarifa = $tarifaResiduo;
				}
			}else{
				$item->ctarifa = null;
			}
	        $item->SolResRM2 = $rm->SolResRM;
		  	return $item;
		});

		$SolicitudServicio->Repetible = 0;

		/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerRMs usando eloquent*/
		$rms = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerRMs');
		$SolicitudServicio->SolSerRMs = $rms->SolSerRMs;

		// return $Residuos;

		foreach ($Residuos as $residuo => $value) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected'])
	        ->where('ID_Req', $value->FK_SolResRequerimiento)
	        ->first();
			$residuoSinTratamiento = Requerimiento::where('FK_ReqRespel', $requerimientos->FK_ReqRespel)
			->where('ofertado', 1)
			->where('forevaluation', 1)
	        ->first();


			if ($residuoSinTratamiento == null) {
				$SolicitudServicio->Repetible++;
			}
		}

		$SolicitudesServicioscount = SolicitudServicio::with(['Personal', 'cliente', 'municipio', 'SolicitudResiduo'])
			->where('ID_SolSer', $SolicitudServicio->ID_SolSer)
			->orderBy('created_at', 'desc')
			->get();

		/*se inicializan las variables para el calculo de totales */
		$total['estimado'] = 0;
		$total['recibido'] = 0;
		$total['conciliado'] = 0;
		$total['tratado'] = 0;
		$cantidadesXtratamiento = [];


		/* se itera sobre todos los residuos de las solicitudes de servicio */
		foreach ($SolicitudesServicioscount as $servicio) {
			foreach ($servicio->SolicitudResiduo as $residuo) {
				$collection = collect($cantidadesXtratamiento);

				/* si el tratamiento existe en la lista se suman las cantidadesxtratamiento y los totales correspondientes */
				if ($collection->has($residuo->requerimiento->tratamiento->TratName)) {
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] + $residuo->SolResKgEnviado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] + $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] + $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] + $residuo->SolResKgTratado;
					$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}else{
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['estimado'] = $residuo->SolResKgEnviado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['recibido'] = $residuo->SolResKgRecibido;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['conciliado'] = $residuo->SolResKgConciliado;
					$cantidadesXtratamiento[$residuo->requerimiento->tratamiento->TratName]['tratado'] = $residuo->SolResKgTratado;
					$total['estimado'] = $total['estimado'] + $residuo->SolResKgEnviado;
					$total['recibido'] = $total['recibido'] + $residuo->SolResKgRecibido;
					$total['conciliado'] = $total['conciliado'] + $residuo->SolResKgConciliado;
					$total['tratado'] = $total['tratado'] + $residuo->SolResKgTratado;
				}
			}
		}
		if (in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol, Permisos::SolSer1)) {
			$tratamientos = Tratamiento::join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
			->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->select('*')
			->get();
		}else{
			$tratamientos = 'NoAutorizado';
		}

		/* validacion para encontrar la fecha de recepción en planta del servicio */
		$fechaRecepcion = SolicitudServicio::find($servicio->ID_SolSer)->programacionesrecibidas()->first();
		if($fechaRecepcion){
			$SolicitudServicio->recepcion = $fechaRecepcion->ProgVehSalida;
		}else{
			$SolicitudServicio->recepcion = null;
		}

		//Buscar corrientes del residuo
		
			$PublicRespels = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->select('respels.ID_Respel', 'respels.YRespelClasf4741', 'respels.ARespelClasf4741')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->distinct()
			->get();

        // adjuntar variables segun status del servicio
        switch ($SolicitudServicio->SolSerStatus) {
            case 'Residuo Faltante':
            case 'Notificado':
				return view('solicitud-serv.rm', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerConductor', 'Programaciones', 'ProgramacionesActivas', 'total', 'cantidadesXtratamiento', 'tratamientos', 'PublicRespels'));
                break;
            case 'Programado':
		      return view('solicitud-serv.rm', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente', 'SolSerConductor', 'Programaciones', 'ProgramacionesActivas', 'total', 'cantidadesXtratamiento', 'tratamientos', 'PublicRespels'));
                break;
            case 'Corregido':
            case 'Completado':
                break;
            default:
                break;

		}
	 }
	}

	 /**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	 public function firmacliente(Request $request, $id)
	 {
		//return $request;
		 $idGener = $request->input('ID_Gener');
		
		 $solser = DB::table('solicitud_servicios')
			 ->select('ID_SolSer')
			 ->where('SolSerSlug', $id)
			 ->first();
			 			
		if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol, Permisos::SUPERVISOR)){
			$firmacliente = DB::table('firmas_servicio')
				->where('FK_SolSer', $solser->ID_SolSer)
				->first();
		}else{
		 if ($solser) {
			 $firmacliente = DB::table('firmas_servicio')
				 ->where('FK_SolSer', $solser->ID_SolSer)
				 ->where('FK_SGener', $idGener)
				 ->first();
				 
		 }
		}
		 
	 
		 // Guardar la firma del cliente
		 $data_uri = $request->input('FirmaCliente');
		 $encoded_image = explode(",", $data_uri)[1];
		 $decoded_image = base64_decode($encoded_image);
		 $nombreDeFirma = hash('md5', rand() . time());
		 Storage::put('public/FirmasClientesRegulares/' . $nombreDeFirma . '.png', $decoded_image, 'public');
			
		 
		 // Guardar la firma en la base de datos
		 if ($firmacliente) {
			if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol, Permisos::SUPERVISOR)){
				DB::table('firmas_servicio')
					->where('FK_SolSer', $solser->ID_SolSer)
					->update([
						'FirmaCliente' => $nombreDeFirma,
						'NombreFuncionario' =>$request->input('NombreFuncionario'),
						'Cedula' => $request->input('CedulaFuncionario'),
						'Observaciones' => $request->input('Observacion'),
					]);
			}else{
				DB::table('firmas_servicio')
					->where('FK_SolSer', $solser->ID_SolSer)
					->where('FK_SGener', $idGener)
					->update([
						'FirmaCliente' => $nombreDeFirma,
						'NombreFuncionario' =>$request->input('NombreFuncionario'),
						'Cedula' => $request->input('CedulaFuncionario'),
						'Observaciones' => $request->input('Observacion'),
					]);

		 }
		}
		 return redirect()->route('recibo.material', ['id' => $id]);
	 }

	 /**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	 public function firmaconductor(Request $request, $id)
	 {
		 $idGener = $request->input('ID_Gener');
	 
		 $solser = DB::table('solicitud_servicios')
			 ->select('ID_SolSer')
			 ->where('SolSerSlug', $id)
			 ->first();

		if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol, Permisos::SUPERVISOR)){
			$firmaconductor = DB::table('firmas_servicio')
				->where('FK_SolSer', $solser->ID_SolSer)
				->first();
			}else{
			 if ($solser) {
				 $firmaconductor = DB::table('firmas_servicio')
					 ->where('FK_SolSer', $solser->ID_SolSer)
					 ->where('FK_SGener', $idGener)
					 ->first();			 
			 }
			} 
	 
		 // Guardar la firma del cliente
		 $data_uri = $request->input('FirmaConductor');
		 $encoded_image = explode(",", $data_uri)[1];
		 $decoded_image = base64_decode($encoded_image);
		 $nombreDeFirma = hash('md5', rand() . time());
		 Storage::put('public/FirmaConductor/' . $nombreDeFirma . '.png', $decoded_image, 'public');
	 
		 // Guardar la firma en la base de datos
		 if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol, Permisos::SUPERVISOR)){
			DB::table('firmas_servicio')
			->where('FK_SolSer', $solser->ID_SolSer)
			->update([
				'FirmaConductor' => $nombreDeFirma,
			]);
		 }else{
		 	if ($firmaconductor) {
				DB::table('firmas_servicio')
					->where('FK_SolSer', $solser->ID_SolSer)
					->where('FK_SGener', $idGener)
					->update([
						'FirmaConductor' => $nombreDeFirma,
					]);
			}
		}
		 return redirect()->route('recibo.material', ['id' => $id]);
	 } 

	 /**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	 public function firmapda(Request $request, $id)
	 {
	 
		 $solser = DB::table('solicitud_servicios')
			 ->select('ID_SolSer')
			 ->where('SolSerSlug', $id)
			 ->first();
			 
	 
		 if ($solser) {
			 $firmaPDA = DB::table('firmas_servicio')
				 ->where('FK_SolSer', $solser->ID_SolSer)
				 ->get();
		 }

		 $generadores = $firmaPDA;
		 $numeroDeGeneradores = count($generadores);

		 

		 // Guardar la firma del cliente
		 $data_uri = $request->input('FirmaPDA');
		 $encoded_image = explode(",", $data_uri)[1];
		 $decoded_image = base64_decode($encoded_image);
		 $nombreDeFirma = hash('md5', rand() . time());
		 Storage::put('public/FirmaPDA/' . $nombreDeFirma . '.png', $decoded_image, 'public');
	 
		 // Guardar la firma en la base de datos
		 for($y=0; $y < $numeroDeGeneradores ; $y++){
		 if ($firmaPDA) {
			 DB::table('firmas_servicio')
				 ->where('FK_SolSer', $solser->ID_SolSer)
				 ->update([
					 'FirmaPDA' => $nombreDeFirma,
				 ]);
		 }
		}
		 return redirect()->route('recibo.material', ['id' => $id]);
	 } 

	 /**
	 * ingresa el numero de factura a la base de datos.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function rmtemplate($id, $slug)
	{
		if(in_array(Auth::user()->UsRol, Permisos::JefeOperaciones) || in_array(Auth::user()->UsRol, Permisos::SUPERVISOR)){
		$firmas = DB::table('firmas_servicio')
			->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'firmas_servicio.FK_SolSer')
			->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
			->join('generadors' , 'generadors.ID_Gener', '=', 'firmas_servicio.FK_Gener')
    			//->where('firmas_servicio.FK_SGener', $id)
			->where('solicitud_servicios.SolSerSlug',$slug )
			->select('firmas_servicio.*', 'clientes.CliName', 'generadors.ID_Gener', 'generadors.GenerNit', 'generadors.GenerName', 'generadors.GenerShortname', 'generadors.GenerCode', 'generadors.GenerType', 'generadors.GenerSlug', 'generadors.FK_GenerCli', 'generadors.GenerDelete')
			->first();

		}else{
			$firmas = DB::table('firmas_servicio')
				->join('solicitud_servicios', 'solicitud_servicios.ID_SolSer', '=', 'firmas_servicio.FK_SolSer')
				->join('clientes', 'clientes.ID_Cli', '=', 'solicitud_servicios.FK_SolSerCliente')
				->join('generadors' , 'generadors.ID_Gener', '=', 'firmas_servicio.FK_Gener')
    			->where('firmas_servicio.FK_SGener', $id)
				->where('solicitud_servicios.SolSerSlug',$slug )
    			->select('firmas_servicio.*', 'clientes.CliName', 'generadors.*')
				->first();
		}
		//return $firmas;
		$SolicitudServicio = DB::table('solicitud_servicios')
			->join('personals', 'personals.ID_Pers', '=', 'solicitud_servicios.FK_SolSerPersona')
			->join('cargos', 'personals.FK_PersCargo', '=', 'ID_Carg')
			->select('solicitud_servicios.*','personals.PersFirstName','personals.PersLastName', 'personals.PersEmail', 'personals.PersCellphone', 'cargos.CargName')
			->where('solicitud_servicios.ID_SolSer', $firmas->FK_SolSer)
			->first();
		//return $SolicitudServicio;		
			
		if (!$SolicitudServicio) {
			abort(404);
		}

		if($SolicitudServicio->SolSerTypeCollect === Null){

			$SolSerCollectAddress = $SolicitudServicio->SolSerCollectAddress;
			$SolSerConductor = $SolicitudServicio->SolSerConductor;
			//return $SolicitudServicio;
					
			$Programaciones = DB::table('progvehiculos')
				//->join('personals', 'personals.ID_Pers', '=', 'progvehiculos.FK_ProgAyudante')
				->where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->where('ProgVehDelete', 0)
				->select('*')
				->first();
			//return $Programaciones;	

			if($SolicitudServicio->SolSerTypeCollect === Null){
				$precintosString = 'No Aplica, el cliente trae en su propio equipo';
			}
			else{
			$Precintos = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
				->select('ProgVehPrecintos')
				->first();	
			
			$precintosString = implode(', ', $Precintos->ProgVehPrecintos);
			
			}


			$Cliente = DB::table('clientes')
				->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
				->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName')
				->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
				->first();

			if($firmas){
			$GenerResiduos = DB::table('solicitud_residuos')
				->distinct()
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
				->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
				->join('municipios', 'municipios.ID_Mun', '=', 'gener_sedes.FK_GSedeMun')
				->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener','generadors.ID_Gener', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress', 'gener_sedes.GSedeEmail', 'gener_sedes.GSedeCelular', 'municipios.MunName')
				//->where('generadors.ID_Gener', $firmas->FK_Gener)
				->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
				->get();
			}	
			//return $GenerResiduos;

			$Residuosoriginal = DB::table('solicitud_residuos')
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
				->join('generadors', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
				->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
				->join('requerimientos' , 'solicitud_residuos.FK_SolResRequerimiento', '=', 'requerimientos.ID_Req')
				->join('tratamientos' , 'requerimientos.FK_ReqTrata', '=', 'tratamientos.ID_Trat')
				->join('sedes' , 'tratamientos.FK_TratProv', '=', 'sedes.ID_Sede')
				->join('clientes' , 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
				->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'tratamientos.ID_Trat', 'clientes.CliShortName', 'gener_sedes.FK_GSede', 'generadors.*')
				->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
				//->where('generadors.ID_Gener', $firmas->FK_Gener )
				// ->where('requerimientos.ofertado', 1)
				// ->where('forevaluation', 0)
				->get();
			//return $Residuosoriginal;
				
			$Residuos = $Residuosoriginal->map(function ($item) {
				$requerimientos = Requerimiento::with(['pretratamientosSelected', 'tarifa.rangos' => function($query){
					$query->orderBy('TarifaDesde');
				}])
				->where('ID_Req', $item->FK_SolResRequerimiento)
				// ->where('forevaluation', 0)
				->first();

				$rm = SolicitudResiduo::with('SolicitudServicio')->where('SolResSlug', $item->SolResSlug)->first(['SolResRM', 'FK_SolResSolSer']);

				$item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
				$item->tarifa = $requerimientos->tarifa;
				if ($requerimientos->tarifa->TarifaSpecial === 1) {
					switch ($item->SolResTypeUnidad) {
						case 'Unidad':
							$tarifatipo = 'Unid';
							break;

						case 'Litros':
							$tarifatipo = 'Lt';
							break;

						default:
							$tarifatipo = 'Kg';
							break;
					}

					$tarifaResiduo = CTarifa::with('rangos')
						->where('FK_Cliente', $rm->SolicitudServicio->FK_SolSerCliente)
						->where('FK_Tratamiento', $requerimientos->FK_ReqTrata)
						->where('Tarifatipo', $tarifatipo)
						->first();

					if ($tarifaResiduo === null) {
						$item->ctarifa = null;
					}else{
						$item->ctarifa = $tarifaResiduo;
					}
				}else{
					$item->ctarifa = null;
				}
				$item->SolResRM2 = $rm->SolResRM;
				return $item;
			});

			$SolicitudServicio->Repetible = 0;

			/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerRMs usando eloquent*/
			$rms = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerRMs');
			$SolicitudServicio->SolSerRMs = $rms->SolSerRMs;

			// return $Residuos;

			foreach ($Residuos as $residuo => $value) {
				$requerimientos = Requerimiento::with(['pretratamientosSelected'])
				->where('ID_Req', $value->FK_SolResRequerimiento)
				->first();
				$residuoSinTratamiento = Requerimiento::where('FK_ReqRespel', $requerimientos->FK_ReqRespel)
				->where('ofertado', 1)
				->where('forevaluation', 1)
				->first();


				if ($residuoSinTratamiento == null) {
					$SolicitudServicio->Repetible++;
				}
			}

			$SolicitudesServicioscount = DB::table('solicitud_servicios')
				->join('solicitud_residuos' , 'solicitud_residuos.FK_SolResSolSer', '=', 'solicitud_servicios.ID_SolSer')
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
				->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
				->where('ID_SolSer', $SolicitudServicio->ID_SolSer)
				->select('solicitud_residuos.*', 'generadors.*')
				->get();

				$cantidadArreglos = $SolicitudesServicioscount->count();

				$totales = 0;		

				foreach ($SolicitudesServicioscount as $servicio){

					$pesorecibido = $servicio->SolResKgRecibido;
					$totales = $totales + $pesorecibido;
				}
						
			if (in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol, Permisos::SolSer1)) {
				$tratamientos = Tratamiento::join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
				->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
				->select('*')
				->get();
			}else{
				$tratamientos = 'NoAutorizado';
			}
			//Buscar corrientes del residuo
			
				$PublicRespels = DB::table('solicitud_residuos')
				->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
				->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
				->select('respels.ID_Respel', 'respels.YRespelClasf4741', 'respels.ARespelClasf4741')
				->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
				->distinct()
				->get();

			$user  = Auth::user()->id;
			
			
			//Generación de PDF
				$pdf = PDF::setPaper('letter', 'portrait')->loadView('solicitud-serv.rmtemplateplanta', compact(['SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente',  'SolSerConductor',  'Programaciones',  'totales', 'tratamientos', 'PublicRespels', 'precintosString', 'firmas', 'user']));
				$nombre = $firmas->SlugFirmas . '.pdf';
				$path = 'public/RecibosdeMaterial/' . sprintf("%0s", $nombre);

				Storage::put($path, $pdf->output(), 'public');
				
				$pdfPath = storage_path('app/public/RecibosdeMaterial/' . $firmas->SlugFirmas . '.pdf');
			//Envio de documento al correo

			$destinatarios = [ 'logistica@prosarc.com.co',
								'gerenteplanta@prosarc.com.co',
								'conciliaciones@prosarc.com.co',
								'jefedetratamiento@prosarc.com.co',
								'auxiliarlogistico@prosarc.com.co',
								'sistemas@prosarc.com.co'
											];
			//dd($firmas);								

			Mail::to($SolicitudServicio->PersEmail)->cc($destinatarios)->send(new SolSerRM($pdf, $pdfPath, $Cliente, $GenerResiduos, $firmas));
		
			// Si el usuario es conductor, abrir el PDF en una nueva pestaña
			if (Auth::user()->UsRol == 'Conductor') {
				return response($pdf->output(), 200, [
					'Content-Type' => 'application/pdf',
					'Content-Disposition' => 'inline; filename="Recibo_Material_' . $SolicitudServicio->ID_SolSer . '.pdf"'
				]);
			}
			
			return redirect()->route('recibo.material', ['id' => $SolicitudServicio->SolSerSlug]);
			//return view ('solicitud-serv.rmtemplate', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente',  'SolSerConductor',  'Programaciones', 'totales', 'tratamientos', 'PublicRespels', 'precintosString', 'firmas'));

		} else {

		$SolSerCollectAddress = $SolicitudServicio->SolSerCollectAddress;
		
		// Verificar si es conductor alquilado o de Prosarc
		$programacion = DB::table('progvehiculos')
			->where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
			->where('ProgVehDelete', 0)
			->first();
		
		if ($programacion && $programacion->ProgVehtipo == 2) {
			// Es conductor alquilado, usar el nombre guardado en la programación
			$SolSerConductor = $programacion->ProgVehNameConductorEXT ?: $SolicitudServicio->SolSerConductor;
		} else {
			// Es conductor de Prosarc, ya tiene el nombre completo
			$SolSerConductor = $SolicitudServicio->SolSerConductor;
		}
				
		$Programaciones = DB::table('progvehiculos')
			->join('personals', 'personals.ID_Pers', '=', 'progvehiculos.FK_ProgAyudante')
			->where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
			->where('ProgVehDelete', 0)
			->select('*')
			->first();
		//return $Programaciones;	

		if($SolicitudServicio->SolSerTypeCollect === Null){
			$precintosString = 'No Aplica, el cliente trae en su propio equipo';
		}
		else{
		$Precintos = ProgramacionVehiculo::where('FK_ProgServi', $SolicitudServicio->ID_SolSer)
			->select('ProgVehPrecintos')
			->first();	
			
    		if(!$Precintos){	
		
		$precintosString = implode(', ', $Precintos->ProgVehPrecintos);
		
    		} else{
    		    $precintosString = 'No se asigno precinto';
    		}
		}


		$Cliente = DB::table('clientes')
			->join('sedes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->join('municipios', 'sedes.FK_SedeMun', '=', 'municipios.ID_Mun')
			->select('clientes.CliNit', 'clientes.CliName', 'sedes.SedeAddress', 'municipios.MunName')
			->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
			->first();
		//return $firmas;
		if($firmas){
			$GenerResiduos = DB::table('solicitud_residuos')
			->distinct()
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('firmas_servicio', 'firmas_servicio.FK_Gener', '=', 'generadors.ID_Gener')
			->join('municipios', 'municipios.ID_Mun', '=', 'gener_sedes.FK_GSedeMun')
			->select('gener_sedes.GSedeName', 'residuos_geners.FK_SGener','generadors.ID_Gener', 'generadors.GenerName','gener_sedes.GSedeSlug', 'gener_sedes.GSedeAddress', 'gener_sedes.GSedeEmail', 'gener_sedes.GSedeCelular', 'municipios.MunName')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->where('generadors.ID_Gener', $firmas->FK_Gener)
			->where('residuos_geners.FK_SGener',$firmas->FK_SGener)
			->get();
			
		}	
	
		$Residuosoriginal = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			->join('generadors', 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->join('requerimientos' , 'solicitud_residuos.FK_SolResRequerimiento', '=', 'requerimientos.ID_Req')
			->join('tratamientos' , 'requerimientos.FK_ReqTrata', '=', 'tratamientos.ID_Trat')
			->join('sedes' , 'tratamientos.FK_TratProv', '=', 'sedes.ID_Sede')
			->join('clientes' , 'sedes.FK_SedeCli', '=', 'clientes.ID_Cli')
			->select('solicitud_residuos.*','residuos_geners.FK_SGener', 'respels.*', 'requerimientos.ID_Req', 'tratamientos.TratName', 'tratamientos.ID_Trat', 'clientes.CliShortName', 'gener_sedes.FK_GSede', 'generadors.*')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->where('generadors.ID_Gener', $firmas->FK_Gener )
			->where('residuos_geners.FK_SGener',$firmas->FK_SGener)
			// ->where('requerimientos.ofertado', 1)
	        // ->where('forevaluation', 0)
			->get();

		//return $Residuosoriginal;
		
			
		$Residuos = $Residuosoriginal->map(function ($item) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected', 'tarifa.rangos' => function($query){
				$query->orderBy('TarifaDesde');
			}])
			->where('ID_Req', $item->FK_SolResRequerimiento)
			// ->where('forevaluation', 0)
			->first();

			$rm = SolicitudResiduo::with('SolicitudServicio')->where('SolResSlug', $item->SolResSlug)->first(['SolResRM', 'FK_SolResSolSer']);

	        $item->pretratamientosSelected = $requerimientos->pretratamientosSelected;
	        $item->tarifa = $requerimientos->tarifa;
			if ($requerimientos->tarifa->TarifaSpecial === 1) {
				switch ($item->SolResTypeUnidad) {
					case 'Unidad':
						$tarifatipo = 'Unid';
						break;

					case 'Litros':
						$tarifatipo = 'Lt';
						break;

					default:
						$tarifatipo = 'Kg';
						break;
				}

				$tarifaResiduo = CTarifa::with('rangos')
					->where('FK_Cliente', $rm->SolicitudServicio->FK_SolSerCliente)
					->where('FK_Tratamiento', $requerimientos->FK_ReqTrata)
					->where('Tarifatipo', $tarifatipo)
					->first();

				if ($tarifaResiduo === null) {
					$item->ctarifa = null;
				}else{
					$item->ctarifa = $tarifaResiduo;
				}
			}else{
				$item->ctarifa = null;
			}
	        $item->SolResRM2 = $rm->SolResRM;
		  	return $item;
		});


		$SolicitudServicio->Repetible = 0;

		/* se convierte el tipo de dato a aray mediante la consulta en el modelo de la columna SolSerRMs usando eloquent*/
		$rms = SolicitudServicio::where('SolSerSlug', $SolicitudServicio->SolSerSlug)->first('SolSerRMs');
		$SolicitudServicio->SolSerRMs = $rms->SolSerRMs;

		// return $Residuos;

		foreach ($Residuos as $residuo => $value) {
			$requerimientos = Requerimiento::with(['pretratamientosSelected'])
	        ->where('ID_Req', $value->FK_SolResRequerimiento)
	        ->first();
			$residuoSinTratamiento = Requerimiento::where('FK_ReqRespel', $requerimientos->FK_ReqRespel)
			->where('ofertado', 1)
			->where('forevaluation', 1)
	        ->first();


			if ($residuoSinTratamiento == null) {
				$SolicitudServicio->Repetible++;
			}
		}

		  $SolicitudesServicioscount = DB::table('solicitud_servicios')
			  ->join('solicitud_residuos' , 'solicitud_residuos.FK_SolResSolSer', '=', 'solicitud_servicios.ID_SolSer')
			  ->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			  ->join('gener_sedes', 'gener_sedes.ID_GSede', '=', 'residuos_geners.FK_SGener')
			  ->join('generadors' , 'generadors.ID_Gener', '=', 'gener_sedes.FK_GSede')
			  ->where('ID_SolSer', $SolicitudServicio->ID_SolSer)
			  ->where('gener_sedes.ID_GSede', $firmas->FK_SGener)
			  //->where('generadors.ID_Gener', $firmas->FK_Gener)
			  ->select('solicitud_residuos.*', 'generadors.*')
			  ->get();
			//return $SolicitudesServicioscount;
			  $cantidadArreglos = $SolicitudesServicioscount->count();

			  $totales = 0;		

			  foreach ($SolicitudesServicioscount as $servicio){

				$pesorecibido = $servicio->SolResKgRecibido;
				$totales = $totales + $pesorecibido;
			  }
					
		if (in_array(Auth::user()->UsRol, Permisos::SolSer1) || in_array(Auth::user()->UsRol, Permisos::SolSer1)) {
			$tratamientos = Tratamiento::join('sedes', 'sedes.ID_Sede', '=', 'tratamientos.FK_TratProv')
			->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
			->select('*')
			->get();
		}else{
			$tratamientos = 'NoAutorizado';
		}
		//Buscar corrientes del residuo
		
			$PublicRespels = DB::table('solicitud_residuos')
			->join('residuos_geners', 'residuos_geners.ID_SGenerRes', '=', 'solicitud_residuos.FK_SolResRg')
			->join('respels' , 'respels.ID_Respel', '=', 'residuos_geners.FK_Respel')
			->select('respels.ID_Respel', 'respels.YRespelClasf4741', 'respels.ARespelClasf4741')
			->where('solicitud_residuos.FK_SolResSolSer', $SolicitudServicio->ID_SolSer)
			->distinct()
			->get();

			$user = Auth::user();
			
		//	return $user;
		
		//Generación de PDF
			$pdf = PDF::setPaper('letter', 'portrait')->loadView('solicitud-serv.rmtemplate', compact(['SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente',  'SolSerConductor',  'Programaciones',  'totales', 'tratamientos', 'PublicRespels', 'precintosString', 'firmas', 'user']));
            $nombre = $firmas->SlugFirmas . '.pdf';
            $path = 'public/RecibosdeMaterial/' . sprintf("%0s", $nombre);
        //return $GenerResiduos;
            Storage::put($path, $pdf->output(), 'public');
            
			
			$pdfPath = storage_path('app/public/RecibosdeMaterial/' . $firmas->SlugFirmas . '.pdf');
		//Envio de documento al correo

		$destinatarios = [ 'logistica@prosarc.com.co',
                            'gerenteplanta@prosarc.com.co',
                            'conciliaciones@prosarc.com.co',
                            'auxiliarlogistico@prosarc.com.co',
                            'sistemas@prosarc.com.co'
                                        ];
		//dd($firmas);								

		Mail::to($SolicitudServicio->PersEmail)->cc($destinatarios)->send(new SolSerRM($pdf, $pdfPath, $Cliente, $GenerResiduos, $firmas));
	
		// Si el usuario es conductor, abrir el PDF en una nueva pestaña
		if (Auth::user()->UsRol == 'Conductor') {
			return response($pdf->output(), 200, [
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'inline; filename="Recibo_Material_' . $SolicitudServicio->ID_SolSer . '.pdf"'
			]);
		}
		
		return redirect()->route('recibo.material', ['id' => $SolicitudServicio->SolSerSlug]);
		//return view ('solicitud-serv.rmtemplate', compact('SolicitudServicio','Residuos', 'GenerResiduos', 'Cliente',  'SolSerConductor',  'Programaciones', 'totales', 'tratamientos', 'PublicRespels', 'precintosString', 'firmas'));
		}
	}
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	 public function NuevoRespel(Request $request, $id)
	 {
		$SolicitudServicio = SolicitudServicio::where('SolSerSlug', $id)->first();

		$idGener = $request->input('SGenerador');

		$Gener = DB::table('generadors')
			->join('gener_sedes', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
			->where('generadors.GenerName', $idGener)
			->select('*')
			->first(); 

		$respels = ResiduosGener::select('ID_SGenerRes')
			->where('FK_SGener', $Gener->ID_Gener) 
			->first();

		$Generadors = DB::table('generadors')
        ->join('gener_sedes', 'gener_sedes.FK_GSede', '=', 'generadors.ID_Gener')
		->join('sedes', 'sedes.ID_Sede', '=', 'generadors.FK_GenerCli')
        ->join('clientes', 'clientes.ID_Cli', '=', 'sedes.FK_SedeCli')
		->where('clientes.ID_Cli', $SolicitudServicio->FK_SolSerCliente)
		->get();

		//return $Gener;	
		
		//Enlazar el residuo a generador
		$numeroDeGeneradores = count($Generadors);
		
			foreach($Generadors as $Generador){
				$RespelSedeGener = new ResiduosGener;
				$RespelSedeGener->FK_SGener = $Generador->ID_GSede;
				$RespelSedeGener->FK_Respel = $request->input('residuo-select');			
				$RespelSedeGener->SlugSGenerRes = hash('sha256', rand().time().$RespelSedeGener->FK_SGener);
				$RespelSedeGener->DeleteSGenerRes = 0;
				$RespelSedeGener->save();
			}

					$SolicitudResiduo = new SolicitudResiduo();
					$SolicitudResiduo->SolResKgEnviado = 0;
					$SolicitudResiduo->SolResKgRecibido = $request->input('SolResCantiUnidad');
					$SolicitudResiduo->SolResKgConciliado = 0;
					$SolicitudResiduo->SolResKgTratado = 0;
					$SolicitudResiduo->SolResDelete = 0;
					$SolicitudResiduo->SolResSlug = hash('sha256', rand().time().$SolicitudResiduo->SolResKgEnviado);
					$SolicitudResiduo->FK_SolResSolSer = $SolicitudServicio->ID_SolSer;
					switch ($request->input('SolResEmbalaje')) {
						case 99:
							$SolicitudResiduo->SolResEmbalaje = "Sacos/Bolsas";
							break;
						case 98:
							$SolicitudResiduo->SolResEmbalaje = "Bidones Pequeños";
							break;
						case 97:
							$SolicitudResiduo->SolResEmbalaje = "Bidones Grandes";
							break;
						case 96:
							$SolicitudResiduo->SolResEmbalaje = "Estibas";
							break;
						case 95:
							$SolicitudResiduo->SolResEmbalaje = "Garrafones/Jerricanes";
							break;
						case 94:
							$SolicitudResiduo->SolResEmbalaje = "Cajas";
							break;
						case 93:
							$SolicitudResiduo->SolResEmbalaje = "Cuñetes";
							break;
						case 92:
							$SolicitudResiduo->SolResEmbalaje = "Big Bags";
							break;
						case 91:
							$SolicitudResiduo->SolResEmbalaje = "Isotanques";
							break;
						case 90:
							$SolicitudResiduo->SolResEmbalaje = "Tachos";
							break;
						case 89:
							$SolicitudResiduo->SolResEmbalaje = "Embalajes Compuestos";
							break;
						case 88:
							$SolicitudResiduo->SolResEmbalaje = "Granel";
							break;
						case 87:
							$SolicitudResiduo->SolResEmbalaje = "Canecas 55 gal.";
							break;
						case 86:
							$SolicitudResiduo->SolResEmbalaje = "Canecas 05 gal.";
							break;
					}
					$SolicitudResiduo->SolResAlto = "";
					$SolicitudResiduo->SolResAncho = "";
					$SolicitudResiduo->SolResProfundo = "";
					$SolicitudResiduo->SolResFotoDescargue_Pesaje = "";
					$SolicitudResiduo->SolResFotoTratamiento = "";
					$SolicitudResiduo->SolResVideoDescargue_Pesaje = "";
					$SolicitudResiduo->SolResVideoTratamiento = "";
					$SolicitudResiduo->SolResAuditoria = "";
					$SolicitudResiduo->SolResDevolucion = "";
					$SolicitudResiduo->FK_SolResRg = ResiduosGener::select('ID_SGenerRes')->where('FK_SGener', $Gener->ID_GSede)->latest('ID_SGenerRes')->first()->ID_SGenerRes;
					/*validar el residuo para saber el tratamiento*/
					$respelref = ResiduosGener::select('FK_Respel')->where('FK_SGener', $Gener->ID_GSede)->first()->FK_Respel;
					
					$requerimientoparacopiar = Requerimiento::with(['pretratamientosSelected'])
					->where('FK_ReqRespel', $respelref)
					->where('ofertado', 1)
					->where('forevaluation', 1)
					->first();
					
					//return $requerimientoparacopiar;

					$nuevorequerimiento = $requerimientoparacopiar->replicate();
					$nuevorequerimiento->ReqSlug= hash('md5', rand().time().$respelref);
					$nuevorequerimiento->forevaluation=0;
					$nuevorequerimiento->ofertado=0;
					$nuevorequerimiento->save();
					$nuevorequerimiento->pretratamientosSelected()->attach($requerimientoparacopiar['pretratamientosSelected']);

					$tarifaparacopiar = Tarifa::with(['rangos'])
					->where('FK_TarifaReq', $requerimientoparacopiar->ID_Req)->first();
					$nuevatarifa = $tarifaparacopiar->replicate();
					$nuevatarifa->FK_TarifaReq=$nuevorequerimiento->ID_Req;
					$nuevatarifa->save();

					foreach ($tarifaparacopiar->rangos as $rango) {
						$rangoparacopiar = Rango::find($rango->ID_Rango);
						$nuevarango = $rangoparacopiar->replicate();
						$nuevarango->FK_RangoTarifa = $nuevatarifa->ID_Tarifa;
						$nuevarango->save();
					}

					$SolicitudResiduo->FK_SolResRequerimiento = $nuevorequerimiento->ID_Req;
					$SolicitudResiduo->save();

		

        return redirect()->route('recibo.material', ['id' => $SolicitudServicio->SolSerSlug]);
	 }

	 public function duplicarpesos(Request $request, $id){

		$solicitud = DB::table('solicitud_servicios')
			->where('SolSerSlug', $id)
			->select('ID_SolSer')
			->first();

		$residuos = DB::table('solicitud_residuos')
			->where('FK_SolResSolSer', $solicitud->ID_SolSer)
			->select('SolResKgEnviado', 'ID_SolRes')
			->get();
				
		//return $residuos;	

		foreach ($residuos as $residuo) {

			$SolResKgRecibido = $residuo->SolResKgEnviado;
			DB::table('solicitud_residuos')
			->where('FK_SolResSolSer', $solicitud->ID_SolSer)
			->where('ID_SolRes', $residuo->ID_SolRes)
			->update([
				'SolResKgRecibido' => $SolResKgRecibido
			]);
		}
		
		return $this->recibomaterial($id);

	 }
 }