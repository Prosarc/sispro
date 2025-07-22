@extends('layouts.app')
@section('htmlheader_title')
Dashboard CRM
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #00C851, #007E33); padding-right:30vw; position:relative; overflow:hidden;">
    {{ 'Dashboard CRM' }}
    <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
    <!-- Estadísticas Rápidas -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{number_format($totalOportunidades)}}</h3>
                    <p>Total Oportunidades</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{number_format($oportunidadesGanadas)}}</h3>
                    <p>Oportunidades Ganadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{number_format($oportunidadesAbiertas)}}</h3>
                    <p>Oportunidades Abiertas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>${{number_format($totalValor, 2)}}</h3>
                    <p>Valor Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablero Kanban -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-body">
                    <div class="row">
                        <!-- Columna Portafolio -->
                        <div class="col-md-2">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Portafolio ({{$portafolio->count()}})</h3>
                                </div>
                                <div class="box-body">
                                    @foreach($portafolio as $oportunidad)
                                    <div class="info-box bg-aqua">
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{$oportunidad->OportNombre}}</span>
                                            <span class="info-box-number">${{number_format($oportunidad->OportValor, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 20%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{$oportunidad->cliente->CliName}}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Columna Cotización -->
                        <div class="col-md-2">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Cotización ({{$cotizacion->count()}})</h3>
                                </div>
                                <div class="box-body">
                                    @foreach($cotizacion as $oportunidad)
                                    <div class="info-box bg-yellow">
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{$oportunidad->OportNombre}}</span>
                                            <span class="info-box-number">${{number_format($oportunidad->OportValor, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 40%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{$oportunidad->cliente->CliName}}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Columna Programación -->
                        <div class="col-md-2">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Programación ({{$programacion->count()}})</h3>
                                </div>
                                <div class="box-body">
                                    @foreach($programacion as $oportunidad)
                                    <div class="info-box bg-green">
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{$oportunidad->OportNombre}}</span>
                                            <span class="info-box-number">${{number_format($oportunidad->OportValor, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 60%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{$oportunidad->cliente->CliName}}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Columna Cobro 15 días -->
                        <div class="col-md-3">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Cobro 15 días ({{$cobro15->count()}})</h3>
                                </div>
                                <div class="box-body">
                                    @foreach($cobro15 as $oportunidad)
                                    <div class="info-box bg-aqua">
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{$oportunidad->OportNombre}}</span>
                                            <span class="info-box-number">${{number_format($oportunidad->OportValor, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 80%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{$oportunidad->cliente->CliName}}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Columna Cobro 30 días -->
                        <div class="col-md-3">
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Cobro 30 días ({{$cobro30->count()}})</h3>
                                </div>
                                <div class="box-body">
                                    @foreach($cobro30 as $oportunidad)
                                    <div class="info-box bg-red">
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{$oportunidad->OportNombre}}</span>
                                            <span class="info-box-number">${{number_format($oportunidad->OportValor, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 100%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{$oportunidad->cliente->CliName}}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Interacciones -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Últimas Interacciones</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Personal</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasInteracciones as $interaccion)
                                <tr>
                                    <td>{{$interaccion->cliente->CliName}}</td>
                                    <td>{{$interaccion->personal->PersFirstName}} {{$interaccion->personal->PersLastName}}</td>
                                    <td>{{$interaccion->InterTipo}}</td>
                                    <td>{{$interaccion->created_at->format('d/m/Y H:i')}}</td>
                                    <td>{{$interaccion->InterDescripcion}}</td>
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

@section('NewScript')
<script>
$(function () {
    // Inicializar Select2
    $('.select2').select2();

    // Función para filtrar las oportunidades
    function filtrarOportunidades() {
        var comercial = $('#filtro-comercial').val();
        var fechaDesde = $('#filtro-fecha-desde').val();
        var fechaHasta = $('#filtro-fecha-hasta').val();

        $('.info-box').each(function() {
            var mostrar = true;
            
            // Filtro por comercial
            if(comercial) {
                if($(this).data('comercial') != comercial) {
                    mostrar = false;
                }
            }

            // Filtro por fecha
            if(fechaDesde && fechaHasta) {
                var fecha = $(this).data('fecha');
                if(fecha < fechaDesde || fecha > fechaHasta) {
                    mostrar = false;
                }
            }

            $(this).toggle(mostrar);
        });
    }

    // Eventos de filtrado
    $('#filtro-comercial, #filtro-fecha-desde, #filtro-fecha-hasta').on('change', filtrarOportunidades);
});
</script>
@endsection 