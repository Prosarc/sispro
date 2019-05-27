@extends('layouts.app')
@section('htmlheader_title')
{{ trans('adminlte_lang::message.progvehictitle') }}
@endsection
@section('contentheader_title')
{{ trans('adminlte_lang::message.progvehictitle') }}
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">{{ trans('adminlte_lang::message.progvehiclist') }}</h3>
					@if(Auth::user()->UsRol == trans('adminlte_lang::message.Programador') ||Auth::user()->UsRol == trans('adminlte_lang::message.JefeLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AuxiliarLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AsistenteLogistica'))
						<a href="/vehicle-programacion/create" class="btn btn-info pull-right"><i class="fas fa-calendar-alt"></i> {{ trans('adminlte_lang::message.progvehiccreatetext') }}</a>
					@endif
				</div>
				<div class="box box-info">
					<div class="box-body">
						<table id="ProgVehicleTable" class="table table-compact table-bordered table-striped" data-order='[[ 1, "desc"]]'>
							<thead>
								<tr>
									<th>{{ trans('adminlte_lang::message.progvehicclient') }}</th>
									<th>{{ trans('adminlte_lang::message.progvehicfech') }}</th>
									<th>{{ trans('adminlte_lang::message.progvehicvehic') }}</th>
									<th>{{ trans('adminlte_lang::message.progvehicayudan') }}</th>
									<th>{{ trans('adminlte_lang::message.progvehicservi2') }}</th>
									<th>{{ trans('adminlte_lang::message.progvehicsalida') }}</th>
									@if(Auth::user()->UsRol <> trans('adminlte_lang::message.Conductor'))
										<th>{{ trans('adminlte_lang::message.progvehicllegada') }}</th>
										<th>{{ trans('adminlte_lang::message.progvehicconduc') }}</th>
									@endif
									@if(Auth::user()->UsRol == trans('adminlte_lang::message.Programador') ||Auth::user()->UsRol == trans('adminlte_lang::message.JefeLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AuxiliarLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AsistenteLogistica'))
										<th>{{ trans('adminlte_lang::message.edit') }}</th>
									@elseif(Auth::user()->UsRol == trans('adminlte_lang::message.SupervisorTurno'))
										<td>{{ trans('adminlte_lang::message.titleconducedit') }}</td>
									@endif
								</tr>
							</thead>
							<tbody  hidden onload="renderTable()" id="readyTable">
								{{-- <h1 id="loadingTable">LOADING...</h1> --}}
								@include('layouts.partials.spinner')
								@foreach($programacions as $programacion)
								<tr @if($programacion->ProgVehDelete === 1)
									style="color: red;"
									@endif
									>
									<td>{{$programacion->CliShortname}}</td>
									<td>{{$programacion->ProgVehFecha}}</td>
									<td>{{$programacion->VehicPlaca}}</td>
									<td>{{$programacion->ayudname." ".$programacion->ayudlastname}}</td>
									<td><a href="/solicitud-servicio/{{$programacion->SolSerSlug}}"class='btn btn-info btn-block'>{{ trans('adminlte_lang::message.see') }}</a></td>
									<td>{{date('h:i A', strtotime($programacion->ProgVehSalida))}}</td>
									@if(Auth::user()->UsRol <> trans('adminlte_lang::message.Conductor'))
										<td>{{$programacion->ProgVehEntrada <> null ? date('h:i A', strtotime($programacion->ProgVehEntrada)) : ''}}</td>
										<td>{{$programacion->condname." ".$programacion->condlastname}}</td>
									@endif
									@if(Auth::user()->UsRol == trans('adminlte_lang::message.Programador') ||Auth::user()->UsRol == trans('adminlte_lang::message.JefeLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AuxiliarLogistica') || Auth::user()->UsRol == trans('adminlte_lang::message.AsistenteLogistica'))
										<td><a method='get' href='/vehicle-programacion/{{$programacion->ID_ProgVeh}}/edit' class='btn btn-warning btn-block'>{{ trans('adminlte_lang::message.edit') }}</a></td>
									@elseif(Auth::user()->UsRol == trans('adminlte_lang::message.SupervisorTurno'))
										<td><a method='get' href='/vehicle-programacion/{{$programacion->ID_ProgVeh}}/edit' class='btn btn-warning btn-block'>{{ trans('adminlte_lang::message.progvehicconducedit') }}</a></td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection