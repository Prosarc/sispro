@extends('layouts.app')
@section('htmlheader_title')
	{{ __('adminlte::message.clientcontacto') }}
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, rgb(255, 216, 111), rgb(252, 98, 98)); padding-right:30vw; position:relative; overflow:hidden;">
    {{ __('adminlte::message.clientcontacto') }}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
		<!-- About Me Box -->
			<div class="box box-info">
				<div class="box-body box-profile">
					<div class="col-md-12 col-xs-12">
						@component('layouts.partials.modal')
							@slot('slug')
								{{$Cliente->ID_Cli}}
							@endslot
							@slot('textModal')
								el proveedor <b>{{$Cliente->CliShortname}}</b>
							@endslot
						@endcomponent
						@if($Cliente->CliDelete === 0 && in_array(Auth::user()->UsRol, Permisos::Jefes) || in_array(Auth::user()->UsRol2, Permisos::Jefes))
							<a href="/contactos/{{$Cliente->CliSlug}}/edit" class="btn btn-warning pull-right"><i class="fas fa-edit"></i><b> {{ __('adminlte::message.edit') }}</b></a>
							<a method='get' href='#' data-toggle='modal' data-target='#myModal{{$Cliente->ID_Cli}}' class='btn btn-danger pull-left'><i class="fas fa-trash-alt"></i><b> {{ __('adminlte::message.delete') }}</b></a>
							<form action='/contactos/{{$Cliente->CliSlug}}' method='POST'  class="col-12 pull-right">
								@method('DELETE')
								@csrf
								<input type="submit" id="Eliminar{{$Cliente->ID_Cli}}" style="display: none;">
							</form>
						@else
							@if((in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR) || in_array(Auth::user()->UsRol2, Permisos::PROGRAMADOR)) && ($Cliente->CliDelete === 1))
								<form action='/contactos/{{$Cliente->CliSlug}}' method='POST' class="pull-left">
									@method('DELETE')
									@csrf
									<button type="submit" class='btn btn-success btn-block'>
										<i class="fas fa-plus-square"></i> <b>{{ __('adminlte::message.add') }}</b>
									</button>
								</form>
							@endif
						@endif
					</div>
					<h3 class="profile-username text-center">{{$Cliente->CliShortname}}</h3>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.clientcategoría') }}</b> <a class="pull-right">{{$Cliente->CliCategoria}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.clirazonsoc') }}</b> <a class="pull-right">{{$Cliente->CliName}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.clientnombrecorto') }}</b> <a class="pull-right">{{$Cliente->CliShortname}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.clientNIT') }}</b> <a class="pull-right">{{$Cliente->CliNit}}</a>
					</li>
				</div>
				<div class="box-body box-profile">
					<h3 class="profile-username text-center">{{ __('adminlte::message.sclientsede') }}</h3>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.sclientnamesede') }}</b> <a class="pull-right">{{$Sede->SedeName}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.address') }}</b>
						<a title="{{ __('adminlte::message.copy') }}" onclick="copiarAlPortapapeles('{{ __('adminlte::message.adddress') }}')"><i class="far fa-copy"></i></a>
						<a href="#" class="pull-right textpopover" id="{{ __('adminlte::message.adddress') }}" title="{{ __('adminlte::message.address') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Sede->SedeAddress}} ({{$Municipio->MunName}} - {{$Departamento->DepartName}})</p>">{{$Sede->SedeAddress}} ({{$Municipio->MunName}} - {{$Departamento->DepartName}})</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.phone') }}</b> <a class="pull-right">{{$Sede->SedePhone1}} - {{$Sede->SedeExt1}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.phone') }} 2</b> <a class="pull-right">{{$Sede->SedePhone2}} - {{$Sede->SedeExt2}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.email') }}</b>
						<a title="{{ __('adminlte::message.copy') }}" onclick="copiarAlPortapapeles('{{ __('adminlte::message.emailaddress') }}')"><i class="far fa-copy"></i></a>
						<a href="#" class="pull-right textpopover" id="{{ __('adminlte::message.emailaddress') }}" title="{{ __('adminlte::message.emailaddress') }}" data-toggle="popover" data-trigger="focus" data-html="true" data-placement="bottom" data-content="<p class='textolargo'>{{$Sede->SedeEmail}}</p>">{{$Sede->SedeEmail}}</a>
					</li>
					<li class="list-group-item">
						<b>{{ __('adminlte::message.mobile') }}</b> <a class="pull-right">{{$Sede->SedeCelular}}</a>
					</li>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection