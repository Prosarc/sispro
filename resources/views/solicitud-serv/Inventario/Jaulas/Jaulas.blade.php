@extends('layouts.app')
@section('htmlheader_title')
Jaulas de Almacenamiento
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	Jaulas de Almacenamiento
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="box-header">
    <h3 class="box-title"></h3>
    @if(in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR) || in_array(Auth::user()->UsRol, Permisos::PROGRAMADOR))
        <a href="/jaulas/Asignar" class="btn btn-primary pull-right">Asignar Jaula</a>
    @endif
</div>
<div class="box-body" style="display: flex;">
    <div class="box box-info" style="overflow-y: auto; max-height: 560px; width: 20%; margin-right:10px;">
        <div id="external-events">
            <div class="box-header with-border">
				<h4 class="box-title" style="text-align: center"><b>Servicios Pendientes de Jaula</b></h4>
			</div>
            @php
                $color = 'bg-green';
            @endphp
            @foreach($tratamientos as $tratamiento)
                <div>
                    <h5 class="box-title" style="text-align: center"><b>{{$tratamiento->TratName}}</b></h5>
                </div>    
                @foreach($solicitudservicios as $servicios)
                    @if($servicios->TratName == $tratamiento->TratName)
                    <p style="background-color: #0c457d; color: #fff; padding-top: 15px !important; padding-bottom: 0 !important; text-align: center;" class="external-event ui-draggable ui-draggable-handle col-md-12 form-group col-xs-12">
                        <span class="col-md-12 form-group col-xs-12">N° {{$servicios->ID_SolSer.' - '.$servicios->RespelName}}</span>
                        <a href="/respels/{{$servicios->RespelSlug}}" target="_blank" class="{{$color}} col-md-12 form-group col-xs-12" style="border-radius: 4px;">{{ __('adminlte::message.see') }}</a>
                    </p>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="box box-info" style="overflow-y: auto; max-height: 560px; width: 75%; margin-right: 10px">
            <div class="box-header with-border">
				<h4 class="box-title" style="text-align: center"><b>Jaulas y Piscinas</b></h4>
			</div>
            <div style="display: flex; flex-wrap: wrap;">
                @foreach($jaulas as $jaula)
                <div class="box box-info" style="overflow-y: auto; max-height: 560px; width: 30%; margin-left: 20px; margin-bottom: 10px">
                        <div class="box-header with-border">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 80%; text-align: left;"><h4><b>{{$jaula->TratName}}</b></h4></td>
                                    <td style="width: 20%; text-align: left;"><h4><b>{{$jaula->PorcentajeOcupacion}}  %</b></h4></td>
                                </tr>       
                            </table>
                            <div style="text-align: center;">
                                <a href="/jaulas/MostrarJaula/{{$jaula->SlugJaula}}" target="_blank" class="{{$color}} form-group" style="border-radius: 4px; display: inline-block; padding: 10px 20px;">
                                    {{ __('adminlte::message.see') }}
                                </a>
                            </div>
                        </div>
                        <h5 class="box-title" style="text-align: center"><b>{{$jaula->Nombre_Jaula}}</b></h5>
                </div>
                @endforeach    
            </div>    
    </div>
</div>
<div class="box-body" style="display: flex;">
    <div class="box box-info" style="overflow-y: auto; width: 96%; margin-right: 10px">
        <div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
            <h4 class="box-title"><b>Distribución en planta</b></h4>
            <img src="img/Planta.png">
        </div>
    </div>    
</div>
@endsection