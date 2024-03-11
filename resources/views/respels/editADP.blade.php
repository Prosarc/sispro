@extends('layouts.app')
{{-- vista de edición para el director de planta --}}
	@section('htmlheader_title')
	{{ trans('adminlte_lang::LangRespel.Respeledittag') }}
	@endsection
	
	@section('contentheader_title')
	  <span style="background-image: linear-gradient(40deg, #FF856D, #CC0000); padding-right:30vw; position:relative; overflow:hidden;">
	  	{{ trans('adminlte_lang::LangRespel.Respeleditmenu') }}
	    <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
	  </span>
	@endsection

	@section('main-content')
		@component('layouts.partials.modal')
			@slot('slug')
				{{$Respels->ID_Respel}}
			@endslot
			@slot('textModal')
				la solicitud <b>N°  {{$Respels->ID_Respel}}</b>
			@endslot
		@endcomponent
		<div class="container-fluid spark-screen">
			<div class="row">
				<div class="col-md-12 col-md-offset-0">
					<!-- Default box -->
					<div class="box">
						<form role="form" action="/respels/{{$Respels->RespelSlug}}" method="POST" id="myform" enctype="multipart/form-data" data-toggle="validator">
							@method('PUT')
							@csrf
							<div class="box-header">
								<h3 class="box-title">{{ trans('adminlte_lang::LangRespel.Respeleditmenu') }}</h3>
							</div>
								<!-- left column -->
								<!-- general form elements -->
							<div class="box box-info">
								<div class="box-body">
									<!-- /.box-header -->
									@if ($errors->any())
										<div class="alert alert-danger" role="alert">
											<ul>
												@foreach ($errors->all() as $error)
													<li>{{$error}}</li>
												@endforeach
											</ul>
										</div>
									@endif
									@if(Auth::user()->UsRol === 'PROGRAMADOR')
                                    <input type="text" name="Sede" style="display: none;" value="{{$cliente->sede}}">
                                    @endif
									@include('layouts.RespelPartials.respelform1Edit')
								</div>
								<div class="box box-info">
									<div class="box-footer">
										<button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i>{{ trans('adminlte_lang::LangRespel.updaterespelButton') }}</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	@endsection