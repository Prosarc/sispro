<?php
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
				if(in_array(Auth::user()->UsRol, Permisos::COMERCIAL) && Auth::user()->email != 'comercial1@prosarc.com.co'){
					$query->where('Comercial.ID_Pers', Auth::user()->FK_UserPers);
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