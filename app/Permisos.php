<?php

namespace App;

class Permisos{

	const Jefes = ['Programador','AdministradorPlanta','JefeLogistica','JefeOperaciones','AdministradorBogota','JefeComercial'];
	/* Using ->
		partials/controlsidebar
		PersonalInternoController::Index
		ContactoController::create
		ContactoController::edit
		Contactos/index
		Contactos/show
		Contactos/showProveedor
		partials/mainheader
	*/
	const ProgVehic1 = ['Programador','JefeLogistica', 'JefeOperaciones','Supervisor', 'usaquen'];
	/* Using ->
		ProgramacionVehicle/create
		ProgramacionVehicle/edit
		ManteniVehicle/index
		vehicle/index
		VehicleController::create,edit
		solicitud-serv/show
	*/
	const ProgVehic2 = ['Programador','JefeLogistica','AsistenteLogistica', 'JefeOperaciones','Supervisor', 'usaquen'];
	/* Using ->
		ProgramacionVehicle/index
		ProgramacionVehicle/create
		VehicProgController::edit
		ProgramacionVehicle/edit
		solicitud-serv/show
		SolicitudServicioController::changestatus
	*/
	const PersInter1 = ['Programador','AdministradorPlanta','AdministradorBogota'];
	/* Using ->
		partials/controlsidebar
		permisos/index
		personalInterno/index
		PersonalInternoController::create,edit
		personalInterno/show
		areasInterno/index
		AreaInternoController::create,edit
		cargosInterno/index
		CargoInternoController::create,edit
		partials/controlsidebar
	*/
	const SolSer1 = ['Programador','JefeOperaciones','Supervisor','JefeLogistica', 'AdministradorPlanta', 'AsistenteLogistica'];
	/*Using ->
		solicitud-serv/show
		recursos/show
	*/
	const Conciliar = ['Programador','JefeOperaciones','Supervisor','JefeLogistica', 'AdministradorPlanta', 'AsistenteLogistica', 'cliente'];
	/*Using ->
		solicitud-serv/show
		recursos/show
	*/
	const RESPELPUBLIC = ['Programador','JefeOperaciones', 'usaquen'];
	/*Using ->
		solicitud-serv/show
		recursos/show
	*/
	const SolSer2 = ['Programador','JefeOperaciones','Supervisor','JefeLogistica','AsistenteLogistica', 'AdministradorPlanta'];
	/*Using ->
		solicitud-serv/show
	*/

	const CLIENTE = ['Cliente'];
	/* Using ->
		genercontroller:index,edit
		ClienteController:index,show,edit
		cliencontoller:index,show,edit
		generadores/index
		generadores/show
		clientes/create2
		clientes/index
		clientes/show
		clientes/edit
		ContactoController::Index
		ContactoController::show
		RespelController::Index
		RespelController::Create
		RespelController::Store
		RespelController::Show
		RespelController::Edit
		SolicitudResiduoController::edit
		SolicitudServicioController::index,create,edit,changestatus
		recursos/show
		AreaController:index,create,edit
		CargoController::index,create,edit
		PersonalController::index,create
		personal/index
		solicitud-serv/index
		solicitud-serv/show
		Menu.php
	*/

	const PROGRAMADOR = ['Programador', 'AdministradorPlanta'];
	/* Using ->
		genercontroller:index
		generadores/index
		generadores/show
		ClienteController:index,show,edit
		ContactoController::Index
		ContactoController::show
		Contactos/show
		Contactos/showProveedor
		RespelController::Index
		RespelController::Create
		RespelController::Edit
		RespelController::Update
		RespelController::Destroy
		RespelController::updateStatusRespel
		AreaController:index,create,edit
		AreaInternoController::index
		CargoController::index,create,edit
		CargoInternoController::index
		PersonalController::index,create,edit
		PersonalInternoController::index
		SolicitudServicioController::create,edit,changestatus
		VehicleController::index
		VehicManteController::index
		personal/index
		personal/show
		ProgramacionVehicle/edit
		solicitud-serv/index
		solicitud-serv/show
		sclientes/sedes/show
	*/
	const TODOPROSARC = ['Programador','AdministradorPlanta','Hseq','JefeLogistica','AsistenteLogistica','Conductor','JefeOperaciones','Supervisor','AdministradorBogota','JefeComercial','Tesorería','Comercial','Comercialap','AsistenteComercial', 'usaquen', 'AsistenteGerencia'];
	/* Using ->
		cliencontoller:index,show,edit
		AreaInternoController::index
		CargoInternoController::index
		SolicitudServicioController::changestatus
		VehicleController::index
		VehicManteController::index
		Menu.php
		VehicProgController::create
		ProgramacionVehicle/index
		clientes/show
	*/
	const TODOPROSARCMenosComercial = ['Programador','AdministradorPlanta','Hseq','JefeLogistica','AsistenteLogistica','Conductor','JefeOperaciones','Supervisor','AdministradorBogota','JefeComercial','Tesorería','AsistenteComercial'];
	/* Using ->
		cliencontoller:index,show,edit
		AreaInternoController::index
		CargoInternoController::index
		SolicitudServicioController::changestatus
		VehicleController::index
		VehicManteController::index
		Menu.php
		VehicProgController::create
		ProgramacionVehicle/index
		clientes/show
	*/
	const CLIENTEYADMINS = ['Programador','Cliente','AdministradorPlanta','AdministradorBogota'];
	/* Using ->
		scliencontroller:create
	*/
	const AREALOGISTICA = ['AsistenteLogistica','JefeLogistica', 'Programador', 'Supervisor' ];
	/* Using ->
		ProgramacionVehicle/edit
	*/
	const LOGISTICA = ['AsistenteLogistica','JefeLogistica', 'Programador' ];
	/* Using ->
		ProgramacionVehicle/edit
	*/
	const ASISTENTELOGISTICA = ['Programador', 'AsistenteLogistica'];
	/* Using ->
		ProgramacionVehicle/edit
	*/
	const JEFELOGISTICA = ['JefeLogistica', 'Programador', 'AdministradorPlanta'];
	/* Using ->
		ProgramacionVehicle/edit
	*/

    const INGDETURNO = ['IngDeTurno'];
    
    const COMERCIALEINGRURNO = ['IngDeTurno', 'Comercial', 'Comercialap', 'AdministradorBogota','Programador'];

	const ADMINISTRADORPLANTA = ['AdministradorPlanta'];
	/* Using ->
		ProgramacionVehicle/edit
	*/

	const ADMINISTRADORBOGOTA = ['AdministradorBogota','Programador', 'AdministradorPlanta'];
	/* Using ->
		ProgramacionVehicle/edit
	*/

	const SolSerCertifi = ['Programador','Tesorería','AdministradorPlanta', 'AdministradorBogota', 'Supervisor', 'AsistenteGerencia'];
	/* Using ->
		solicitud-serv/index
		vehicle-programacion/index
		SolicitudServicioController::changestatus
		SolicitudServicioController::index
		clientes::show
	 */
	const SOLSERACEPTADO = ['Programador','Tesorería'];

	const SEDECOMERCIAL = ['Programador','Tesorería','AsistenteComercial','AdministradorBogota','Comercial','Comercialap','JefeComercial'];
	/* Using ->
		solicitud-serv/index
		SolicitudServicioController::changestatus
		SolicitudServicioController::index
		clientes::show
	 */
	const CONTRATOSCRUD = ['Programador','AsistenteComercial'];
	/* Using ->
		contratos/index
		ContratoController::create,edit
	 */
	const ComercialYJefeComercial = ['AdministradorBogota','Comercial','Comercialap'];
	/* Using ->
		respel/edit
	 */
	
	const COMERCIAL = ['Comercial'];
	/* Using ->
		clientcontoller::index
	*/

	const COMERCIALAP = ['Comercialap'];
	/* Using ->
		respel/edit
	 */

	const COMERCIALEXPRESS = ['Programador', 'Comercial','Comercialap', 'usaquen', 'Ejecutivo Comercial'];
	/* Using ->
		clientcontoller::index
	*/

	const COMERCIALES = ['Programador', 'AdministradorBogota', 'Comercial','Comercialap'];
	/* Using ->
		clientcontoller::index
	*/

	const SUPERVISOR = ['Supervisor','Comercialap'];
	/* Using ->
		respel/index
	*/
	const ADMINPLANTA = ['AdministradorPlanta'];
	/* Using ->
		solicitudServicio/show
	*/

	const TESORERIA = ['Tesorería'];
	/* Using ->
		vehicle-programacion/index
	*/

	const UpdateCantConciliada = ['Programador','AdministradorPlanta'];
	/* Using ->
		vehicle-programacion/index
	*/


	const GrupoShowRespel = ['AdministradorPlanta','Hseq','JefeLogistica','AsistenteLogistica','Conductor','Supervisor','Tesorería','AsistenteComercial'];
	/* Using ->
		
	*/

	const JefeComercial = ['Programador','JefeComercial','AdministradorBogota'];
	/* Using ->
		clientcontoller::index
	*/
	const AsigComercial = ['Programador','AdministradorBogota'];
	/* Using ->
		clientes/index
		cleintes/show requerimientos
	*/

	const JefeOperaciones = ['Programador','JefeOperaciones', 'Supervisor', 'AdministradorPlanta'];
	/*Using ->
		pretratamientos/edit
		pretratamientocontoller::destroy
	*/
	const CONDUCTOR = ['Conductor'];
	/* Using->
		VehicProgController::index
	 */

	const CONDUCTOREXPRESS = ['Programador','Conductor', 'usaquen'];
	/* Using->
		VehicProgController::index
	 */

	const GrupoEdicionRespel = ['Cliente','Programador','JefeOperaciones','AdministradorBogota','JefeComercial','Comercial','Comercialap','JefeLogistica','AsistenteLogistica', 'usaquen'];
	/* Using->
		respelcontroller::edit
	 */

	const GrupoEvaluacionRespel = ['Programador','JefeOperaciones','AdministradorBogota','JefeComercial','Comercial','Comercialap', 'Supervisor', 'AdministradorPlanta', 'usaquen'];
	/* Using->
		respelcontroller::edit
	 */

	const EDITMANIFCERT = ['Programador','JefeLogistica','AsistenteLogistica', 'Supervisor', 'AdministradorBogota', 'JefeOperaciones','AdministradorPlanta', 'Comercialap', 'AsistenteGerencia'];
	/*Using ->
		solicitud-serv/show/documentos
	*/

	const SIGNMANIFCERT = ['Programador','JefeLogistica','JefeOperaciones','AdministradorPlanta','Hseq','AsistenteLogistica', 'AsistenteGerencia'];
	/*Using ->
		solicitud-serv/show/documentos
	*/

	const REVERSAR = ['Programador','JefeOperaciones','Supervisor','JefeLogistica','AsistenteLogistica','AdministradorPlanta'];
	/* Using ->
		solserv/show
	*/

	const REVERSARADMIN = ['Programador','AdministradorPlanta'];
	/* Using ->
		solserv/show
	*/
	
	const EXPRESS = ['Programador', 'usaquen', 'AdministradorPlanta','Hseq','JefeLogistica','AsistenteLogistica','Conductor','JefeOperaciones','Supervisor','AdministradorBogota','JefeComercial','Tesorería','Comercial','Comercialap','AsistenteComercial'];
	/* Using ->
		solserv/show
	*/

	const COTIZACION = ['Programador','Comercial','AsistenteComercial','AdministradorBogota','JefeComercial'];

	/*CONJUNTO DE ARRAY PARA EL MENU.PHP PARA PERSONAL DE PROSARC*/
	const AREAS = ['Programador','AdministradorPlanta','AdministradorBogota'];
	const CARGOS = ['Programador','AdministradorPlanta','AdministradorBogota'];
	const PERSONAL = ['Programador','AdministradorPlanta','JefeLogistica','JefeOperaciones','AdministradorBogota','Conductor','IngDeTurno'];
	const PROGRAMACIONES = ['Programador', 'usaquen', 'AdministradorPlanta','JefeLogistica','AsistenteLogistica','Conductor','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','JefeComercial', 'AsistenteGerencia'];
	const VEHICULOS = ['Programador','AdministradorPlanta','JefeLogistica','AsistenteLogistica','AdministradorBogota','JefeComercial'];
	const CONTACTOS = ['Programador','AdministradorPlanta','JefeLogistica','AdministradorBogota','JefeOperaciones','JefeComercial','Conductor', 'Supervisor', 'AsistenteGerencia'];
	const CONTRATOS = ['Programador','AdministradorPlanta','AdministradorBogota','Comercial','AsistenteComercial','JefeComercial'];
	const LISTACLIENTES = ['Programador','AdministradorPlanta','JefeLogistica','AsistenteLogistica','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','AsistenteComercial','JefeComercial','Conductor', 'usaquen', 'AsistenteGerencia'];
	const LISTAGENERADORES = ['Programador','usaquen', 'AdministradorPlanta','JefeLogistica','AsistenteLogistica','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','AsistenteComercial','JefeComercial','Conductor', 'AsistenteGerencia'];
	const LISTARESIDUOS = ['Programador', 'usaquen', 'AdministradorPlanta','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','JefeComercial','AsistenteLogistica','Conductor', 'AsistenteGerencia'];
	const TRATAMIENTOS = ['Programador','AdministradorPlanta','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','JefeComercial', 'AsistenteGerencia'];
	const PERSONALCLIENTE = ['Programador', 'usaquen', 'AdministradorPlanta','JefeLogistica','AsistenteLogistica','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','AsistenteComercial','Conductor', 'AsistenteGerencia'];
	const SERVICIOS = ['Programador', 'usaquen', 'AdministradorPlanta','JefeLogistica','AsistenteLogistica','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','AsistenteComercial','JefeComercial', 'AsistenteGerencia'];
	const PRETRATAMIENTOS = ['Programador','AdministradorPlanta','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','JefeComercial', 'AsistenteGerencia'];
	const ALMACENAMIENTO = ['Programador','AdministradorPlanta','JefeOperaciones','Supervisor','AdministradorBogota','Tesorería','Comercial','Comercialap','JefeComercial', 'AsistenteGerencia'];


	/*Recibo de Materia */
	const RECIBOMATERIAL = ['Programador','AdministradorPlanta','Supervisor', 'Conductor', 'JefeOperaciones'];
	const RECEPCIONPDA = ['Programador', 'Supervisor', 'JefeOperaciones'];


	const USAQUEN = ['usaquen']; 
	const ASISTENTEGERENCIA = ['AsistenteGerencia']; 

	const CambioTratamiento = ['Programador','JefeOperaciones','Supervisor', 'AdministradorPlanta'];
	/*Using ->solicitud-serv/show  cambio-tratamiento*/

	// Permisos personalizados para roles con nombres exactos de la base de datos
	const LOGISTICAEXACTA = ['Jefe de area Logistica', 'Asistente de area Logistica', 'JefeLogistica', 'AsistenteLogistica'];
	const TURNOEXACTO = ['Ingeniero de turno', 'Supervisor de Turno', 'IngDeTurno', 'Supervisor'];
	const EJECUTIVOCOMERCIAL = ['Ejecutivo Comercial', 'Comercial', 'Comercialap', 'usaquen'];

}

/*
Programador
AdministradorPlanta
Hseq
JefeLogistica
AsistenteLogistica
Conductor
JefeOperaciones
Supervisor
AdministradorBogota
Tesorería
Comercial
AsistenteComercial
Cliente
 */