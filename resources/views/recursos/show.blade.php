@extends('layouts.app')

@section('htmlheader_title','Recursos')

@section('contentheader_title', 'Ver todos los Recursos')
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
                            <div class="box-body">
                                <div class="col-md-12">
                                    <label for="tipo">Foto</label>
                                    <a method='get' href='#' data-toggle='modal' data-target='#myModal'  class="btn btn-primary" style="float: right;">Añadir</a>
                                
                                    <form role="form" action="/recurso/{{$ResGeners->ID_SGenerRes}}" method="POST" enctype="multipart/form-data">
                                        @method('PUT')
                                        {{csrf_field()}}
                                        @csrf
                                            @component('layouts.partials.modalañadirecurso')
                                            @endcomponent
                                    </form>                    
                                </div>
                            </div>
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Cargue' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="CargueRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Cargue')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Descargue' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="DescargueRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Descargue')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Pesaje' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="PesajeRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Pesaje')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Reempacado' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="ReempacadoRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Reempacado')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Mezclado' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="MezcladoRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Mezclado')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                    @foreach ($Recursos as $Recurso)
                                        @if ($Recurso->RecTipo == 'Destruccion' and $Recurso->RecCarte == 'Foto')
                                        <div class="col-md-12">
                                            <label>{{$Recurso->RecTipo}}</label>
                                            <div id="DestruccionRec">
                                                <iframe  width="100%" max-width="1200" height="auto" src="https://www.youtube.com/embed/N5olyi6xywU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                         
                                                @foreach ($Recursos as $Recurso)
                                                    @if ($Recurso->RecTipo == 'Destruccion')
                                                    <img src="{{ asset($Recurso->RecSrc . '/' . $Recurso->RecRmSrc) }}" height="auto" width="100%" max-width="1200">
                                                    @endif
                                                @endforeach  
                                            </div>
                                        </div>
                                        @break
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>


                        
                        <div class="box-body">
                            <div class="col-md-12">
                                <label for="tipo">Video</label>
                            </div>
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Cargue' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Descargue' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Pesaje' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Reempacado' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Mezclado' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
                            @foreach ($Recursos as $Recurso)
                                @if ($Recurso->RecTipo == 'Destruccion' and $Recurso->RecCarte == 'video')
                                    <div class="col-md-12">
                                        <label>{{$Recurso->RecTipo}}</label>
                                    </div>
                                    @foreach ($Recursos as $Recurso)
                                    <img src="{{ asset($Recurso->RecSrc) }}" alt="" width="150" height="150" value="">  
                                    @endforeach
                                    @break
                                    @endif
                            @endforeach
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