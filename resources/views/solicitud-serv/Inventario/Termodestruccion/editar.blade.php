@extends('layouts.app')
@section('htmlheader_title')
Termodestrucción
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	{{'Residuos para incinerar'}}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="box box-info">
	<form role="form" action="/termodestruccion/update/{{$residuos->ID_Incineracion}}" method="POST" enctype="multipart/form-data" data-toggle="validator">
		@csrf
		@if ($errors->edit->any())
		<div class="alert alert-danger" role="alert">
			<ul>
				@foreach ($errors->edit->all() as $error)
				<p>{{$error}}</p>
				@endforeach
			</ul>
		</div>
		@endif
		<div class="box-body">
			<div class="form-group col-md-6">
				<label for="">Servicio N°</label>
				<input disabled type="text" class="form-control" value="{{$residuos->FK_Solicitud}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Residuo</label>
				<input disabled type="text" class="form-control" value="{{$residuos->RespelName}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Estado</label>
				<input disabled type="text" class="form-control" value="{{$residuos->RespelEstado}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Peligrosidad</label>
				<input disabled type="text" class="form-control" value="{{$residuos->RespelIgrosidad}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Cantidad Total</label>
				<input disabled type="text" class="form-control" value="{{$residuos->SolResKgRecibido}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Cantidad Programada</label>
				<input type="text" maxlength="16" class="form-control" id="CantidadIncinerar" name="CantidadIncinerar" value="{{$residuos->Cantidadprog}}">
			</div>
			<div class="form-group col-md-6">
				<label for="">Fecha de Programación</label>
				<input type="date" maxlength="16" class="form-control" id="CantidadIncinerar" name="CantidadIncinerar" value="{{$residuos->FechaIncineracion}}">
			</div>
			<div class="form-group col-md-6">
				<label>Turno</label>
					<select name="Turno" id="Turno" class="form-control" required>
						<option value="1">Turno 1</option>
						<option value="2">Turno 2</option>
					</select>
			</div>
			<div class="form-group col-md-6">
				<label>Ingeniero de Turno</label>
					<select name="Ingturno" id="Ingturno" class="form-control" required>
						@foreach($supervisores as $ingturno)
						<option value="{{$residuos->IngTurno}}">{{$ingturno->PersFirstName. ' '.$ingturno->PersSecondName. ' '.$ingturno->PersLastName}}</option>
						@endforeach
					</select>
			</div>
			<div class="form-group col-md-6">
				<label>Hornero Principal</label>
					<select name="Hornerop" id="Hornerop" class="form-control" required>
						@foreach($horneos as $horneo)
						<option value="{{$residuos->Hornerop}}">{{$horneo->PersFirstName. ' '.$horneo->PersSecondName. ' '.$horneo->PersLastName}}</option>
						@endforeach
					</select>
			</div>
			<div class="form-group col-md-6">
				<label>Hornero Secundario</label>
					<select name="Horneros" id="Horneros" class="form-control" required>
						@foreach($horneossecundarios as $horneos)
						<option value="{{$residuos->Horneroa}}">{{$horneos->PersFirstName. ' '.$horneos->PersSecondName. ' '.$horneos->PersLastName}}</option>
						@endforeach
					</select>
			</div>
			<div class="col-md-8">
				<button type="submit" class="btn btn-success pull-right" id="update">{{ __('adminlte::message.update') }}</button>
			</div>
		 </div>		
	</form>  
</div>      
@endsection