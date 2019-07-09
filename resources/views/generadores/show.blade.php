@extends('layouts.app')
@section('htmlheader_title')
{{ trans('adminlte_lang::message.gener') }}
@endsection
@section('contentheader_title')
	{{ trans('adminlte_lang::message.gener') }}
@endsection	
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-body box-profile">
					<div class="col-md-12 col-xs-12">
						@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
							<a href="/generadores/{{$Generador->GenerSlug}}/edit" class="btn btn-warning pull-right"> <i class="fas fa-edit"></i> <b>{{ trans('adminlte_lang::message.edit') }}</b></a>
						@endif
						@component('layouts.partials.modal')
							@slot('slug')
								{{$Generador->ID_Gener}}
							@endslot
							@slot('textModal')
								el generador <b>{{$Generador->GenerShortname}}</b>
							@endslot
						@endcomponent
						@if($Generador->GenerDelete == 0)
							@if(in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
								<a method='get' href='#' data-toggle='modal' data-target='#myModal{{$Generador->ID_Gener}}' class='btn btn-danger pull-left'><i class="fas fa-trash-alt"></i><b> {{ trans('adminlte_lang::message.delete') }}</b></a>
								<form action='/generadores/{{$Generador->GenerSlug}}' method='POST'  class="col-12 pull-right">
									@method('DELETE')
									@csrf
									<input type="submit" id="Eliminar{{$Generador->ID_Gener}}" style="display: none;">
								</form>
							@endif
						@else
							@if (in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR) || in_array(Auth::user()->UsRol2, Permisos::PROGRAMADOR))
								<form action='/generadores/{{$Generador->GenerSlug}}' method='POST' class="pull-left">
									@method('DELETE')
									@csrf
									<button type="submit" class='btn btn-success btn-block'>
										<i class="fas fa-plus-square"></i><b> {{ trans('adminlte_lang::message.add') }}</b>
									</button>
								</form>
							@endif
						@endif
					</div>
					<h3 class="profile-username text-center textolargo col-12">{{$Generador->GenerShortname}}</h3>
					<ul class="list-group list-group-unbordered">
						@if (in_array(Auth::user()->UsRol, Permisos::TODOPROSARC) || in_array(Auth::user()->UsRol2, Permisos::TODOPROSARC))
							<li class="list-group-item">
								<b>{{ trans('adminlte_lang::message.clientcliente') }}</b> 
								<a href="#" class="pull-right textpopover" title="{{ trans('adminlte_lang::message.clientcliente') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Cliente->CliShortname}}</p>">{{$Cliente->CliShortname}}</a>
							</li>
						@endif
						<li class="list-group-item">
							<b>{{ trans('adminlte_lang::message.sclientsede') }}</b> 
							<a href="#" class="pull-right textpopover" title="{{ trans('adminlte_lang::message.sclientsede') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Sede->SedeName}}</p>">{{$Sede->SedeName}}</a>
						</li>
						<li class="list-group-item">
							<b>{{ trans('adminlte_lang::message.clientNIT') }}</b> 
							<a href="#" class="pull-right">{{$Generador->GenerNit}}</a>
						</li>
						<li class="list-group-item">
							<b>{{ trans('adminlte_lang::message.clirazonsoc') }}</b> 
							<a href="#" class="pull-right textpopover" title="{{ trans('adminlte_lang::message.clirazonsoc') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Generador->GenerName}}</p>">{{$Generador->GenerName}}</a>
						</li>
						<li class="list-group-item">
							<b>{{ trans('adminlte_lang::message.clientnombrecorto') }}</b> 
							<a href="#" class="pull-right textpopover" title="{{ trans('adminlte_lang::message.clientnombrecorto') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Generador->GenerShortname}}</p>">{{$Generador->GenerShortname}}</a>
						</li>
						<li class="list-group-item">
							<b>{{ trans('adminlte_lang::message.genercode') }}</b> 
							<a href="#" class="pull-right textpopover" title="{{ trans('adminlte_lang::message.genercode') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Generador->GenerCode}}</p>">{{$Generador->GenerCode}}</a>
						</li>
							<h4 class="text-center"><i>{{ trans('adminlte_lang::message.sedesgener') }}</i></h4>
							<div style='overflow-y:auto; max-height:200px;'>
								@php
									$i = 0;
								@endphp
								@foreach ($GenerSedes as $GenerSede)
									<li class="list-group-item col-md-12 col-xs-12">
										<div class="col-md-6 col-xs-6">
											<b class="textolargo">{{$GenerSede->GSedeName}}</b> 
											<a title="{{ trans('adminlte_lang::message.copy') }}" onclick="copiarAlPortapapeles('SGeneraddress{{$i}}')"><i class="far fa-copy"></i></a>
										</div>
										<div>
											<a href="#" class="pull-right textpopover" id="SGeneraddress{{$i}}" title="{{ trans('adminlte_lang::message.genercode') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$GenerSede->GSedeAddress}}</p>">{{$GenerSede->GSedeAddress}}</a>
										</div>
									</li>
									@php
										$i++;
									@endphp
								@endforeach
							</div>
					</ul>
				</div>
			</div>
		</div>
		@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
			{{--  Modal Agregar un Residuo a una SedeGener--}}
		<form role="form" action="/respelGener" method="POST" enctype="multipart/form-data" data-toggle="validator">
			@csrf
			<div class="modal modal-default fade in" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<div style="font-size: 5em; color: green; text-align: center; margin: auto;">
								<i class="fas fa-plus-circle"></i>
								<span style="font-size: 0.3em; color: black;"><p>{{ trans('adminlte_lang::message.assignrrespelssedegener') }}</p></span>
							</div>
						</div>
						@if ($errors->any())
							<div class="alert alert-danger" role="alert">
								<ul>
									@foreach ($errors->all() as $error)
										<p>{{$error}}</p>
									@endforeach
								</ul>
							</div>
						@endif
						<div class="modal-header">
							<div class="col-md-12 form-group">
								<label for="FK_SGener">{{ trans('adminlte_lang::message.sedesgener') }}</label><small class="help-block with-errors">*</small>
								<select class="form-control select" id="FK_SGener" name="FK_SGener" required>
									<option value="">{{ trans('adminlte_lang::message.select') }}</option>
									@foreach ($GenerSedes as $GenerSede)	
										<option value="{{$GenerSede->ID_GSede}}" {{ old('FK_SGener') == $GenerSede->ID_GSede ? 'selected' : '' }}>{{$GenerSede->GSedeName}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-12 form-group">
								<label for="FK_Respel">{{ trans('adminlte_lang::message.MenuRespel') }} <a id="load"></a></label><small class="help-block with-errors">*</small>
								<select class="form-control select-multiple" id="FK_Respel" name="FK_Respel[]" multiple required>
									@if(isset($Residuos))
										@foreach ($Residuos as $Residuo)
											<option value="{{$Residuo->ID_Respel}}" {{ $old == $Residuo->ID_Respel ? 'selected' : '' }}>{{$Residuo->RespelName}}</option>
										@endforeach 
									@endif 
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary pull-right">{{ trans('adminlte_lang::message.add') }}</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		@endif
	{{-- END Modal --}}
		<div class="col-md-6">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
						{{-- Barra de navegacion --}}
						<li class="active box-info" ><a href="#residuos" data-toggle="tab">{{ trans('adminlte_lang::message.MenuRespel') }}</a></li>
						<li><a href="#sedes" data-toggle="tab">{{ trans('adminlte_lang::message.sclientsedes') }}</a></li>
				</ul>
				<div class="tab-content">
					<div class="active tab-pane" id="residuos">
						@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
							{{-- BOTONES DE CREAR RESIDUOS Y ASIGNARLOS --}}
							<a href="/respels/create" class="btn btn-primary mx-auto"><b>{{ trans('adminlte_lang::message.respelscreate') }}</b></a>
							<a method='get' href='#' data-toggle='modal' data-target='#add'  class="btn btn-success mx-auto pull-right"><i class="fas fa-plus-circle"></i><b> {{ trans('adminlte_lang::message.assignrespels') }}</b></a>
						@endif
						<div style='overflow-y:auto; max-height:503px;'>
							@foreach ($Respels as $Respel)
								<ul class="list-group" style="list-style:none; margin-top:10px;">
									<li class="col-md-11 col-xs-12 col-12">
										@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
											{{-- Boton de eliminar residuo del generador --}}
											<a method='get' href='#' data-toggle='modal' data-target='#eliminar{{$Respel->SlugSGenerRes}}' onclick="deleteRespelGener(`{{$Respel->SlugSGenerRes}}`, `{{$Respel->RespelName}}`, `{{$Generador->GenerShortname}}`)" style="font-size: 1.5em; color: red; margin-bottom:-2px;" class="pull-right" ><i class="fas fa-times-circle"></i></a>
										@endif
										<h4><a href="/respels/{{$Respel->RespelSlug}}" class="list-group-item list-group-item-action list-group-item-light textolargo col-md-offset-1" style="display:flex; justify-content:center;" target="_blank">{{$Respel->RespelName}}</a></h4>
									</li>
									<li class="col-md-12 col-xs-12 col-12">
										{{--  Modal Eliminar un Residuo de una SedeGener--}}
										@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR) && $Generador->GenerDelete == 0)
											<div class="deleterespelgener"></div>
										@endif
										{{-- END Modal --}}
									</li>
								</ul>
							@endforeach
						</div>
					</div>
					<div class="tab-pane" id="sedes">
						<div class="text-center">
							@if (in_array(Auth::user()->UsRol, Permisos::CLIENTE) ||in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
								<a href="/sgeneradores/create" class="btn btn-success"><i class="fas fa-plus-square"></i><b> {{ trans('adminlte_lang::message.addsedegener') }}</b></a>
							@endif
						</div>
						<div style='overflow-y:auto; max-height:503px;'>
							@foreach ($GenerSedes as $GenerSede)
							<ul class="list-group" style="list-style:none; margin-top:10px;">
								<li class="col-md-11 col-xs-12 col-12">
									<h4><a href="/sgeneradores/{{$GenerSede->GSedeSlug}}" class="list-group-item list-group-item-action list-group-item-light textolargo col-md-offset-1" style="display:flex; justify-content:center;" target="_blank">{{$GenerSede->GSedeName}}</a></h4>
								</li>
							</ul>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('NewScript')
<script>
	$(document).ready(function() {
		$("#FK_SGener").change(function(e) {
			id = $("#FK_SGener").val();
			e.preventDefault();
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			});
			$.ajax({
				url: "{{url('/sedegener-respel')}}/" + id,
				method: 'GET',
				data: {},
				beforeSend: function(){
					$("#load").append('<i class="fas fa-sync-alt fa-spin"></i>');
					$("#FK_Respel").prop('disabled', true);
				},
				success: function(res) {
					$("#FK_Respel").empty();
					var respel = new Array();
					for (var i = res.length - 1; i >= 0; i--) {
						if ($.inArray(res[i].ID_Respel, respel) < 0) {
							$("#FK_Respel").append(`<option value="${res[i].ID_Respel}">${res[i].RespelName}</option>`);
							respel.push(res[i].ID_Mun);
						}
					}
				},
				complete: function(){
					$("#load").empty();
					$("#FK_Respel").prop('disabled', false);
				}
			})
		});
	});
</script>
@endsection
