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
                                <h1><center>Reporte Servicios Regulares</center></h1>
                                <form action="{{ route('reportes.regular') }}" method="POST">
                                    @csrf
                                    
                                    <div class="form-group col-md-12">
                                        <label color: black; text-align: left;" >Fecha de inicio</label>
                                        <input required type="date" name="Fecha_Inicio" class="form-control col-xs-12">
                                        </div>
                                     
                                    <div class="form-group col-md-12">
                                        <label color: black; text-align: left;" >Fecha Final</label>
                                        <input required type="date" name="Fecha_Fin" class="form-control col-xs-12">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label color: black; text-align: left;" >Cliente</label>
                                        <select name="cliente_id" class="form-control col-xs-12">
                                            <option value="">Todos los clientes</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->ID_Cli }}">{{ $cliente->CliName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label color: black; text-align: left;" >Cliente</label>
                                        <select name="cliente_id" class="form-control col-xs-12">
                                            <option value="">Todos los clientes</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->ID_Cli }}">{{ $cliente->CliName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" href="/reportes/regular" class="btn btn-info" style="margin: 10px 30px;" id="btn-buscar">Generar</button>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@section('scripts')
<script>
    $(document).ready(function() {
        $('select[name="cliente_id"]').select2({
            placeholder: "Buscar cliente...",
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            },
            theme: "classic",
            width: '100%',
            minimumResultsForSearch: 0
        });
    });
</script>
@endsection
@section('scripts')Add commentMore actions
<script>
    $(document).ready(function() {
        $('select[name="cliente_id"]').select2({
            placeholder: "Buscar cliente...",
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            },
            theme: "classic",
            width: '100%',
            minimumResultsForSearch: 0
        });
    });
</script>
@endsection
@endsection
