@extends('layouts.app')

@section('htmlheader_title')
{{ trans('adminlte_lang::message.areatitle') }}
@endsection

@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #FFFFFF, #A3A2AE); padding-right:30vw; position:relative; overflow:hidden;">
	{{ trans('adminlte_lang::message.areatitle') }}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection

@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-16 col-md-offset-0">
				<div class="box">
					<div class="box-header">
						@component('layouts.partials.modal')
							@slot('slug')
								{{$Areas->AreaSlug}}
							@endslot
							@slot('textModal')
								el área de <b>{{$Areas->AreaName}}</b>
							@endslot
						@endcomponent
						<h3 class="box-title">{{ trans('adminlte_lang::message.editarea') }}</h3>
						@if($Areas->ID_Area <> $AreaOne->ID_Area)
							@if($Areas->AreaDelete == 0)
								<a method='get' href='#' data-toggle='modal' data-target='#myModal{{$Areas->AreaSlug}}' class='btn btn-danger pull-right'><i class="fas fa-trash-alt"></i><b> {{ trans('adminlte_lang::message.delete') }}</b></a>
								<form action='/areasInterno/{{$Areas->AreaSlug}}' method='POST'>
									@method('DELETE')
									@csrf
									<input  type="submit" id="Eliminar{{$Areas->AreaSlug}}" style="display: none;">
								</form>
							@else
								<form action='/areasInterno/{{$Areas->AreaSlug}}' method='POST' class="pull-right">
									@method('DELETE')
									@csrf
									<button type="submit" class='btn btn-success btn-block'>{{ trans('adminlte_lang::message.add') }}</button>
								</form>
							@endif
						@endif
					</div>
					<div class="box box-info">
						<form role="form" action="/areasInterno/{{$Areas->AreaSlug}}" method="POST" enctype="multipart/form-data" data-toggle="validator">
							@method('PATCH')
							@csrf
							@if ($errors->any())
								<div class="alert alert-danger" role="alert">
									<ul>
										@foreach ($errors->all() as $error)
											<p>{{$error}}</p>
										@endforeach
									</ul>
								</div>
							@endif
							<div class="box-body">
								<div class="form-group col-xs-12 col-md-12">
									<label for="SedeSelect">{{ trans('adminlte_lang::message.sclientsede') }}</label><small class="help-block with-errors">*</small>
									<select name="FK_AreaSede" id="SedeSelect" class="form-control select" required>
										@foreach($Sedes as $Sede)
											<option value="{{$Sede->SedeSlug}}" {{$Areas->FK_AreaSede == $Sede->ID_Sede ? 'selected' : ''}}>{{$Sede->SedeName}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group col-xs-12 col-md-12">
									<label for="NombreArea">{{ trans('adminlte_lang::message.areaname') }}</label><small class="help-block with-errors">*</small>
									<input data-minlength="5" required="true" name="AreaName" autofocus="true" type="text" class="form-control inputText" id="NombreArea" value="{{$Areas->AreaName}}">
								</div>
							</div>
							<div class="box box-info">
								<div class="box-footer">
									<button type="submit" class="btn btn-success pull-right">{{ trans('adminlte_lang::message.update') }}</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
