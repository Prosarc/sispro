@extends('layouts.appReportes')
@section('htmlheader_title','Reportes')
{{-- @endsection --}}
@section('contentheader_title', '')
{{-- @endsection --}}
@section('main-content')
<div class="container-fluid spark-screen">
    <div class="row">
        <div class="col-md-16 col-md-offset-0">
            <div class="box">
                <div class="box-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <h1><center>Seleccione el tipo de reporte</center></h1>
                                <br>
                                <br>
                                <div class="form-group col-md-12">
                                    <a href="{{ route('reportes.refechas')}}" class="btn btn-primary btn-lg" style="float: center;">Registro de Entrada Prosarc</a>
                                    <br>
                                    <br>
                                    <a href="{{ route('reportes.ventasfechas')}}" class="btn btn-success btn-lg" style="float: center;">Informe de Ventas</a>
                                    <br>
                                    <br>
                                    <a href="{{ route('solicitud-serv.2022')}}" class="btn btn-info btn-lg" style="float: center;">Registro de entrada Gestores</a>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
