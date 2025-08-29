@extends('layouts.app')

@section('htmlheader_title')
{{ __('Reportes de Cliente') }}
@endsection

@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
    Reportes de Servicios
    <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection

@section('main-content')
<div class="container-fluid spark-screen">
    <div class="row">
        <div class="col-md-16 col-md-offset-0">
            <!-- /.box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Generar Reporte de Servicios</h3>
                </div>
                <div class="box-body">
                    <form action="{{ route('reportes.cliente.generar') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Fecha de inicio</label>
                                            <input required type="date" name="Fecha_Inicio" class="form-control" value="{{ old('Fecha_Inicio', isset($request) ? $request->Fecha_Inicio : date('Y-m-01')) }}">
                                            @error('Fecha_Inicio')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label>Fecha Final</label>
                                            <input required type="date" name="Fecha_Fin" class="form-control" value="{{ old('Fecha_Fin', isset($request) ? $request->Fecha_Fin : date('Y-m-t')) }}">
                                            @error('Fecha_Fin')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-search"></i> Consultar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(isset($servicios) && $servicios->count() > 0)
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title">Resultados de la búsqueda</h3>
                            <div class="box-tools pull-right">
                                <form action="{{ route('reportes.cliente.excel') }}" method="POST" class="form-inline">
                                    @csrf
                                    <input type="hidden" name="Fecha_Inicio" value="{{ isset($request) ? $request->Fecha_Inicio : '' }}">
                                    <input type="hidden" name="Fecha_Fin" value="{{ isset($request) ? $request->Fecha_Fin : '' }}">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-file-excel-o"></i> Exportar a Excel
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table id="serviciosTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha de Servicio</th>
                                        <th>N° de Servicio</th>
                                        <th>Estado</th>
                                        <th>Residuo</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Tratamiento</th>
                                        <th>Certificado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servicios as $servicio)
                                        @foreach($servicio->SolicitudResiduo as $residuo)
                                        <tr>
                                            <td>{{$servicio->programacionesrecibidas->first()->ProgVehFecha ?? 'N/A'}}</td>
                                            <td>{{$servicio->ID_SolSer}}</td>
                                            <td>{{$servicio->SolSerStatus}}</td>
                                            <td>{{$residuo->generespel->respels->RespelName}}</td>
                                            <td>{{$residuo->SolResKgConciliado ?? $residuo->SolResKgRecibido ?? $residuo->SolResKgEnviado ?? 'N/A'}}</td>
                                            <td>{{$residuo->SolResTypeUnidad}}</td>
                                            <td>{{$residuo->requerimiento->tratamiento->TratName ?? 'N/A'}}</td>
                                            <td>
                                                @if($residuo->certdatoexpress && $residuo->certdatoexpress->certificado)
                                                    <a href="{{ route('certificados.show', $residuo->certdatoexpress->certificado->CertSlug) }}" class="btn btn-info btn-sm"><i class="fa fa-file-pdf-o"></i></a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @elseif(isset($servicios))
                    <div class="alert alert-info">
                        <h4><i class="icon fa fa-info"></i> No se encontraron resultados</h4>
                        No hay servicios registrados para el período seleccionado.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($servicios) && $servicios->count() > 0)
<script>
$(document).ready(function() {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#serviciosTable')) {
        $('#serviciosTable').DataTable().destroy();
    }    
    // Inicializar la tabla
    $('#serviciosTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        },
        "order": [[ 0, "desc" ]],
        "responsive": true,
        "pageLength": 25
    });
});
</script>
@endif
@endsection 