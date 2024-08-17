@extends('layouts.app')
@section('htmlheader_title')
{{__('adminlte::message.vehicletitle')}}
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, rgb(69, 202, 252), rgb(48, 63, 159)); padding-right:30vw; position:relative; overflow:hidden;">
	{{ __('adminlte::message.vehicletitle') }}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">{{__('adminlte::message.vehiclelist')}}</h3>
					@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic1) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic1))
					<a href="/vehicle/create" class="btn btn-primary pull-right">{{__('adminlte::message.create')}}</a>
					@endif
				</div>
				<!-- /.box-header -->
				<div class="box box-info">
					<div class="box-body">
						<table id="VehicleTable" class="table table-compact table-bordered table-striped">
							<thead>
								<tr>
									<th>{{__('adminlte::message.vehicplaca')}}</th>
									<th>{{__('adminlte::message.vehictipo')}}</th>
									<th>{{__('adminlte::message.vehiccapacidad')}}</th>
									<th>{{__('adminlte::message.vehickm')}}</th>
									<th>{{__('adminlte::message.vehicsedes')}}</th>
									@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic1) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic1))
									<th>{{__('adminlte::message.edit')}}</th>
									@endif
								</tr>
							</thead>
							<tbody id="readyTable">
								@foreach ($Vehicles as $Vehicle)
									<tr style="{{$Vehicle->VehicDelete === 1 ? 'color: red' : ''}}">
										<td>{{$Vehicle->VehicPlaca}}</td>
										<td>{{$Vehicle->VehicTipo}}</td>
										<td>{{$Vehicle->VehicCapacidad}} kg</td>
										<td>{{$Vehicle->VehicKmActual}}</td>
										<td>{{$Vehicle->SedeName}}</td>
										@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic1) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic1))
										<td><a href='/vehicle/{{$Vehicle->VehicPlaca}}/edit' class='btn btn-warning btn-block'><i class="fas fa-edit"></i> <b>{{__('adminlte::message.edit')}}</b></a></td>
										@endif
									</tr>
								@endforeach
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection