@extends('layouts.app')
@section('htmlheader_title')
Programacion Termodestruccion
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	{{'Programación'}}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Lista de Programación</h3>
                    <br>
                    <br>
					@if(in_array(Auth::user()->UsRol, Permisos::Jefes) || in_array(Auth::user()->UsRol2, Permisos::Jefes))
						<a href="/termodestruccion" class="btn btn-info pull-right"><i class="fas fa-calendar-alt"></i> {{ __('adminlte::message.progvehiccreatetext') }}</a>
					@endif
                    @if(in_array(Auth::user()->UsRol, Permisos::SUPERVISOR) || in_array(Auth::user()->UsRol2, Permisos::SUPERVISOR))
						<a href="/termodestruccion/informe" class="btn btn-info pull-right"><i class="fas fa-file-alt"></i> Informe de Turno</a>
                        <a href="/termodestruccion/incineracion" class="btn btn-info pull-left"><i class="fas fa-chart-bar"></i> Informe de Incineracion</a>
					@endif
				</div>
				<div class="box box-info">
					<div class="box-body">
						<table id="ProgTermoTable" class="table table-compact table-bordered table-striped" data-order='[[ 1, "desc"]]'>
							<thead>
								<tr>
									<th>Residuo</th>
									<th>Fecha de Programación</th>
									<th>Turno</th>
									<th>Ing. de Turno</th>
                                    <th>Hornero Principal</th>
                                    <th>Hornero Secundario</th>
									<th>Peso Total</th>
									<th>Peso programado</th>
									<th>Numero de Solicitud</th>
									@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic2) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2))
									<th>{{ __('adminlte::message.edit') }}</th>
									@endif
								</tr>
							</thead>
							<tbody id="readyTable">
								@foreach($incineraciones as $incineracion)
								<tr>
									<td>{{$incineracion->RespelName}}</td>
									<td>{{\Carbon\Carbon::parse($incineracion->FechaIncineracion)->format('Y-m-d')}}</td>
									<td>{{$incineracion->Turno}}</td>
									<td>{{$incineracion->turno_nombre.' '.$incineracion->turno_apellido}}</td>
                                    <td>{{$incineracion->hornop_nombre.' '.$incineracion->hornop_apellido}}</td>
                                    <td>{{$incineracion->hornos_nombre.' '.$incineracion->hornos_apellido}}</td>
                                    <td>{{ number_format($incineracion->SolResKgRecibido, 2, '.', ',')}}</td>
                                    <td>{{ number_format($incineracion->Cantidadprog, 2, '.', ',')}}</td>
                                    <td>{{$incineracion->FK_Solicitud}}</td>
                                    @if(in_array(Auth::user()->UsRol, Permisos::ProgVehic2) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic2))
                                    <td><a href="/termodestruccion/programar/editar/{{$incineracion->ID_Incineracion}}"class='btn btn-info btn-block' title="Editar"><i class="fas fa-search"></i> Editar</a></td>
                                    @endif
								@endforeach
								<div id="ModalStatus"></div>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
