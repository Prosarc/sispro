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
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">{{ __('adminlte::message.MenuContactos') }}</h3>
					@if (in_array(Auth::user()->UsRol, Permisos::Jefes) || in_array(Auth::user()->UsRol2, Permisos::Jefes))
						<a href="/contactos/create" class="btn btn-primary pull-right">{{ __('adminlte::message.create') }}</a>
					@endif
				</div>
				<div class="box box-info">
					<div class="box-body">
						<table id="contactosTable" class="table table-compact table-bordered table-striped">
							<thead>
							<tr>
								<th>{{ __('adminlte::message.clientcategoría') }}</th>
								<th>{{ __('adminlte::message.clirazonsoc') }}</th>
								<th>{{ __('adminlte::message.clientnombrecorto') }}</th>
								<th>{{ __('adminlte::message.clientNIT') }}</th>
								<th>{{ __('adminlte::message.seemore') }}</th>
							</tr>
							</thead>
							<tbody id="readyTable">
							@foreach($Clientes as $Cliente)
							<tr @if($Cliente->CliDelete === 1)
									style="color: red;" 
								@endif
							>
								<td>{{$Cliente->CliCategoria}}</td>
								<td>{{$Cliente->CliName}}</td>
								<td>{{$Cliente->CliShortname}}</td>
								<td>{{$Cliente->CliNit}}</td>
								<td>
									<a method='get' href='/contactos/{{$Cliente->CliSlug}}' class='btn btn-info btn-block' title="{{ __('adminlte::message.seemoredetails')}}"><i class="fas fa-search"></i></a>
								</td>
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