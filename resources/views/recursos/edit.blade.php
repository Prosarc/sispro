@extends('layouts.app')

@section('htmlheader_title','Recursos')

@section('contentheader_title', 'Crear un nuevo Recursos')
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<!-- Default box -->
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Datos</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i></button>
						<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
						<i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="row">
					<!-- left column -->
					<div class="col-md-12">
						<!-- general form elements -->
						<div class="box box-primary">
                            <!-- form start -->
                            {{-- @foreach ($Recursos as $Recurso) --}}
                                
                            {{-- <form role="form" action="/recurso/{{$ResGeners->ID_SGenerRes}}" method="POST" enctype="multipart/form-data"> --}}
                            <form role="form" action="/recurso/{{$ResGeners->ID_SGenerRes}}" method="POST" enctype="multipart/form-data">
                                @csrf
                            @method('PATCH')
								{{-- {{csrf_field()}} --}}
                                <div class="col-md-12">
                                    <label for="SolSer">Solicitud Servicio</label>
                                    <select class="form-control" id="SolSer" name="FK_ResGer" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($SolServs as $SolServ)
                                            <option value="{{$SolServ->ID_SolSer}}">{{$SolServ->ID_SolSer}}, {{$SolServ->RespelName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                        <label for="nombre">Nombre del Cliente (recurso)</label>
                                        <select class="form-control" id="nombre" name="RecName" required>
                                            <option value="">Seleccione...</option>
                                            @foreach ($Clientes as $Cliente)
                                                <option value="{{$Cliente->CliShortname}}">{{$Cliente->CliShortname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                {{-- <div class="col-md-12">
                                    <label for="categoria">Categoria</label>
                                    <select class="form-control" id="categoria" name="RecCarte" required>
                                        <option value="{{$Recursos->RecCarte}}">Seleccione...</option>
                                        <option>Foto</option>
                                        <option>Video</option>
                                    </select>
                                </div> --}}
                                {{-- <div class="col-md-12">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control" id="tipo" name="RecTipo" required>
                                        <option value="{{$Recursos->RecTipo}}">Seleccione...</option>
                                        <option>Cargue</option>
                                        <option>Descargue</option>
                                        <option>Pesaje</option>
                                        <option>Reempacado</option>
                                        <option>Mezclado</option>
                                        <option>Destruccion</option>
                                        
                                        <option>Datos del Personal</option>
                                        <option>Bascula</option>
                                        <option>Planillas</option>
                                        <option>Devolucion de elementos</option>
                                        <option>Alistamiento de residuos</option>
                                        <option>personal con Capacitacion</option>
                                        <option>vehiculo con Plataforma</option>
                                        <option>Mas Personal de cargue/descargue</option>
                                        <option>Certificacion Especial</option>
                                        <option>tipo de elementos</option>
                                        
                                    </select>
                                </div> --}}
                                {{-- <div class="col-md-12">
                                    <label for="soliservicioinputext3">Ruta de guardado</label>
                                    <input type="file" class="form-control" id="soliservicioinputext3" name="RecSrc[]" accept=".jpg, .jpeg, .png" multiple>
                                </div> --}}
                                {{-- <input hidden value="{{$Recursos->RecName}}" name="RecName"> --}}
                                <div class="col-md-8">
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Registrar</button>
                                    </div>
                                </div>
                            </form>
                            {{-- @endforeach --}}
                            
							</div>					
							<!-- /.box -->
						</div>
						<!-- /.box-body -->
					</div>
				<!-- /.box -->
			</div>
			<!--/.col (right) -->
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</div>
@endsection