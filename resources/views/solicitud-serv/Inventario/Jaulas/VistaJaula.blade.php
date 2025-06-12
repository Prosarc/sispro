@extends('layouts.app')
@section('htmlheader_title')
MostrarJaula
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	Jaula {{$numjaula}}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection   
@section('main-content')

    <div class="box-body" style="display: flex;">
        <div class="box box-info" style="overflow-y: auto; width: 96%; margin-right: 10px">
            <div class="box-body">
                <table class="table table-compact table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha de Ingreso</th>
                            <th>Nombre residuo</th>
                            <th>Peso Kg</th>
                            <th>Peligrosidad</th>
                            <th>Numero de Solicitud</th>
                            <th>Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($residuos as $residuo)
                        <tr>
                            <th>{{$residuo->FechaIngresoJaula}}</th>
                            <th>{{$residuo->RespelName}}</th>
                            <th>{{$residuo->SolResKgRecibido}}</th>
                            <th>{{$residuo->RespelIgrosidad}}</th>
                            <th>{{$residuo->ID_SolSer}}</th>
                            <th>{{$residuo->CliName}}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
