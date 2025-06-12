@extends('layouts.app')
@section('htmlheader_title')
Informe Turno
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	{{'Informe de Turno'}}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="box-header">
                            <b><h4 class="box-title" style="text-align: center">Orden de Producción</h4></b>
                        </div>
                        <table id="incineracionTable" class="table table-compact table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>No. SISPRO</th>
                                    <th>Residuo</th>
                                    <th>Descripción del Residuo</th>
                                    <th>Programado KG</th>
                                    <th>Ejecutado KG</th>
                                    <th>Ejecutado / Programado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody id="readyTable">
                                @foreach($incineraciones as $incineracion)
                                    @php
                                        switch ($incineracion->SolResTypeUnidad) {
                                                    case 'Unidad':
                                                        $TypeUnidad = 'Unidades';
                                                        break;
                                                    case 'Litros':
                                                        $TypeUnidad = 'Litros';
                                                        break;
                                                    default:
                                                        $TypeUnidad = 'Kilogramos';
                                                        break;
                                                }
                                        @endphp
                                <tr>
                                    <td>{{$incineracion->CliName}}</td>
                                    <td>{{$incineracion->FK_Solicitud}}</td>
                                    <td>{{$incineracion->RespelName}}</td>
                                    <td>
                                        <select name="descresiduo" id="descresiduo" class="form-control" required>
                                            <option value="Biologico">Biologico</option>
                                            <option value="Industrial">Industrial</option>
                                            <option value="Farmacos">Farmacos</option>
                                        </select>
                                    </td>
                                    <td>{{number_format($incineracion->Cantidadprog, 2, '.', ',')}}</td>
                                    <td>
                                        @if($incineracion->SolResTypeUnidad == 'Litros' || $incineracion->SolResTypeUnidad == 'Unidad')
                                        <a onclick="addkg(`{{$incineracion->SolResSlug}}`, `{{$incineracion->SolResKgTratado}}`, `{{$incineracion->SolResKgTratado}}`, `{{$TypeUnidad}}`, `{{$incineracion->SolResKgTratado == 0 ? '' : number_format($incineracion->SolResKgTratado, $decimals = 2, $dec_point = ',', $thousands_sep = '.')}}`, null, `{!!json_encode($incineracion->SolResRM, JSON_NUMERIC_CHECK)!!}`)">
                                        @else
                                        <a onclick="addkg(`{{$incineracion->SolResSlug}}`, `{{$incineracion->SolResKgTratado}}`, `{{$incineracion->SolResKgTratado}}`, `{{$TypeUnidad}}`, `{{$incineracion->SolResKgTratado == 0 ? '' : number_format($incineracion->SolResKgTratado, $decimals = 2, $dec_point = ',', $thousands_sep = '.')}}`, null, `{!!json_encode($incineracion->SolResRM, JSON_NUMERIC_CHECK)!!}`)"> 
                                        @endif
                                        <i class="fas fa-marker"></i>{{' '.$incineracion->SolResKgTratado}}</a>
                                    </td>
                                    <td>{{$incineracion->EjecutadovsProgramado. ' %'}}</td>
                                    <td><input type="text" maxlength="16" class="form-control" id="Dieta" name="Dieta"></div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="addkgmodal"></div>
                    </div>
                </div>
            </div>
            <form role="form" action="/termodestruccion/informe/{{$incineracion->FK_inforturno}}" method="POST" enctype="multipart/form-data" data-toggle="validator">
                @csrf
                @if ($errors->edit->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->edit->all() as $error)
                        <p>{{$error}}</p>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="box box-info">
                    <div class="box-body"> 
                        <h4 class="box-title" style="text-align: center"><b>Observaciones Generales Sobre La Orden De Producción</b></h4>
                        <div class="form-group col-md-6">
                            <label for="">Dieta</label>
                            <input type="text" maxlength="800" class="form-control" id="Dieta" name="Dieta">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Condiciones de Operación</label>
                            <input type="text" maxlength="800" class="form-control" id="CondOperacion" name="CondOperacion">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Emisiones</label>
                            <input type="text" maxlength="800" class="form-control" id="Emisiones" name="Emisiones">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Otras Observaciones</label>
                            <input type="text" maxlength="800" class="form-control" id="OtrObsv" name="OtrObsv">
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-body">
                        <h4 class="box-title" style="text-align: center">Parametros de monitoreo</h4>
                    </div>
                    <div style="display: flex; width: 100%;">
                        <div style="width: 50%; padding-right: 10px;">
                            <table id="monitoreoTable" class="table table-compact table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>PARAMETRO</th>
                                        <th>Prom Horario</th>
                                        <th>DIARIO</th>
                                        <th>PICOS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>% OXIGENO</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>MP</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>CO</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>NOx</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>SO2</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody> 
                            </table> 
                        </div>
                        <div style="width: 50%; padding-left: 10px;">
                            <div class="form-group col-md-11">
                                <label for="">ANÁLISIS</label>
                                <input type="text" maxlength="800" class="form-control" id="analisis" name="analisis">
                            </div>
                        </div>
                    </div>   
                </div>
                <div class="box box-info">
                    <div class="box-body">
                        <div style="display: flex; width: 100%;">
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">CÁMARAS DE COMBUSTIÓN Y POSTCOMBUSTIÓN </h4>
                                <div class="form-group col-md-11">
                                    <label for="">Temperatura postcombustión</label>
                                    <input type="text" maxlength="800" class="form-control" id="Tempost" name="Tempost">
                                </div>
                                <div class="form-group col-md-11">
                                    <label for="">Temperatura combustión</label>
                                    <input type="text" maxlength="800" class="form-control" id="Tempcomb" name="Tempcomb">
                                </div>
                            </div>
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">TANQUE DE ENFRIAMIENTO, FILTRO DE MANGAS, TIRO FORZADO, CHIMENEA </h4>
                                <div class="form-group col-md-11">
                                    <label for="">Variador de tiro</label>
                                    <input type="text" maxlength="800" class="form-control" id="teftc" name="teftc">
                                </div>
                                <div class="form-group col-md-11">
                                    <label for="">Temperatura chimenea</label>
                                    <input type="text" maxlength="800" class="form-control" id="TempChim" name="TempChim">
                                </div>
                                <div class="form-group col-md-11">
                                    <label for="">Temperatura filtro de mangas</label>
                                    <input type="text" maxlength="800" class="form-control" id="Tempfmangas" name="Tempfmangas">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-body">
                        <div style="display: flex; width: 100%;">
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">Sistema de Cargue</h4>
                                <div class="form-group col-md-11">
                                    <input type="text" maxlength="800" class="form-control" id="scargue" name="scargue">
                                </div>
                            </div>
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">OTROS EQUIPOS (PLANTA ELÉCTRICA, BÁSCULA DE PESAJE, EQUIPO DE MONITOREO)</h4>
                                <div class="form-group col-md-11">
                                    <input type="text" maxlength="800" class="form-control" id="otrose" name="otrose">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-body">
                        <div style="display: flex; width: 100%;">
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">Actividades de Bodega</h4>
                                <div class="form-group col-md-11">
                                    <input type="text" maxlength="800" class="form-control" id="bodega" name="bodega">
                                </div>
                            </div>
                            <div style="width: 50%; padding-right: 10px;">
                                <h4 class="box-title" style="text-align: center">OBSERVACIONES GENERALES DURANTE EL TURNO (RECURSO HUMANO, INFRAESTRUCTURA, SALIDA DE MATERIAL, VEHÍCULOS)</h4>
                                <div class="form-group col-md-11">
                                    <input type="text" maxlength="800" class="form-control" id="Obsgener" name="Obsgener">
                                </div>
                            </div>
                        </div>
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <button type="submit" class="btn btn-success" id="update">Generar</button>
                            </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('NewScript')
<script>
    function addkg(slug, cantidad, cantidadmax, tipo, cantidadKG, KgConciliado, SolResRM){
        console.log('solresRM = '+SolResRM);
        var rmSelected = JSON.parse(SolResRM);
        var inputUnid =  '<label for="SolResCantiUnidadTratada">Cantidad Recibida'+tipo+'</label><small class="help-block with-errors">*</small><input type="text" class="form-control numberKg" id="SolResCantiUnidadTratada" name="SolResCantiUnidadTratada" maxlength="5" value="'+cantidad+'" required>';
        var inputKg =  '<label for="SolResCantiUnidadTratada">Cantidad Recibida'+tipo+'</label><small class="help-block with-errors">*</small><input type="text" class="form-control numberKg" id="SolResCantiUnidadTratada" name="SolResKg" maxlength="5" value="'+cantidad+'" required>';
     
        $('#addkgmodal').empty();
        $('#addkgmodal').append(`
            <form role="form" action="/solicitud-residuo/`+slug+`/Update" method="POST" enctype="multipart/form-data" data-toggle="validator" id="FormKg">
                @method('PUT')
                @csrf
                <div class="modal modal-default fade in" id="editkgRecibido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div style="font-size: 5em; color: green; text-align: center; margin: auto;">
                                    <i class="fas fa-plus-circle"></i>
                                    <span style="font-size: 0.3em; color: black;"><p>
                                        Cantidad
                                        @switch($incineracion->SolSerStatus)
                                            @case('Programado')
                                            @case('Notificado')
                                                Recibida
                                                @break
                                            @case('No Conciliado')
                                            @case('Completado')
                                                Conciliada
                                                @break
                                            @case('Conciliado')
                                                Tratada
                                                @break
                                        @endswitch
                                    </p></span>
                                </div>
                            </div>
                            <div class="modal-header">
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <p>{{$error}}</p>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                    @switch($incineracion->SolSerStatus)
                                        @case('Programado')
                                        @case('Notificado')
                                        <div class="form-group col-md-12">
                                            <label for="SolResKgRecibido">Cantidad Recibida (kg)</label>
                                            <small class="help-block with-errors">*</small>
                                            <input type="number" step=".01" class="form-control numberKg" id="SolResKgRecibido" name="SolResKg" maxlength="5" value="`+cantidadKG+`" required>
                                        </div>
                                        <div class="form-group col-md-12">	
                                            `+(tipo != 'Kilogramos' ? '<label for="SolResCantiUnidadRecibida">Cantidad Recibida '+tipo+'</label><small class="help-block with-errors">*</small><input type="number" step=".1" min="0" class="form-control numberKg" id="SolResCantiUnidadRecibida" name="SolResCantiUnidadRecibida" maxlength="5" value="'+cantidad+'" required>' : '')+`
                                        </div>
                                            @break
                                        @case('No Conciliado')
                                        @case('Completado')
                                        <div class="form-group col-md-12">	
                                            <label for="SolResKgConciliado">Cantidad Conciliada (kg)</label><small class="help-block with-errors">*</small><input type="number" step=".01" min="0" class="form-control" id="SolResKgConciliado" name="SolResKg" maxlength="5" value="`+cantidadKG+`" required>
                                        </div>
                                        <div class="form-group col-md-12">	
                                                `+(tipo != 'Kilogramos' ? '<label for="SolResCantiUnidadConciliada">Cantidad Conciliada '+tipo+' </label><small class="help-block with-errors">*</small><input type="number" step=".1" min="0" class="form-control" id="SolResCantiUnidadConciliada" name="SolResCantiUnidadConciliada" maxlength="5" value="'+cantidad+'" required>' : '')+`
                                        </div>
                                            @break
                                        @case('Conciliado')
                                        @case('Certificacion')
                                        @case('Facturado')
                                        <div class="form-group col-md-12">	
                                            <label for="SolResKgTratado">Cantidad Tratada (kg)</label>
                                            <small class="help-block with-errors">*</small>
                                            <div class="input-group">
                                                <input type="number" step=".01" min="0" class="form-control cantidadmax" id="SolResKgTratado" name="SolResKg" maxlength="5" value="`+cantidadKG+`" max="`+KgConciliado+`" required>
                                                <div class="input-group-btn">
                                                    <a title="Lo conciliado ya esta tratado" id="btn-consiliado" class="btn btn-success" `+(tipo != 'Kilogramos' ? 'onclick="submit('+cantidadmax+','+KgConciliado+',\''+tipo+'\')"' : 'onclick="submit('+null+','+KgConciliado+',\''+tipo+'\')"')+`>Tratado</a>
                                                    <div id="conciliadokg"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">	
                                            `+(tipo != 'Kilogramos' ? '<label for="SolResCantiUnidadTratada">Cantidad Tratada '+tipo+' </label><small class="help-block with-errors">*</small><input type="number" step=".1" min="0" class="form-control" id="SolResCantiUnidadTratada" name="SolResCantiUnidadTratada" maxlength="5" max="'+cantidadmax+'" value="'+cantidad+'" required>' : '')+`
                                        </div>
                                            @break
                                    @endswitch
                                    <input type="text" hidden name="SolRes" value="`+slug+`">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right">{{__('adminlte::message.save')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        `);
        switch('{{$incineracion->SolSerStatus}}'){
            case('Programado'):
            case('Notificado'):
                numeroKg();
                break;
            case('Completado'):
            case('No Conciliado'):
                    $('.cantidadmax').inputmask({ alias: 'numeric', max:cantidadmax, rightAlign:false});
                break;
            case('Conciliado'):
                    $('.cantidadmax').inputmask({ alias: 'numeric', max:cantidadmax, rightAlign:false});
                break;
        };
        $('#editkgRecibido').modal();

        var arrayRMs = {!! json_encode($incineracion->SolSerRMs) !!};

        /*se verifica si todos los valores son nulos*/
        var nulos = 0;
        for (let indexnulos = 0; indexnulos < arrayRMs.length; indexnulos++) {
            if (arrayRMs[indexnulos] == null) {
                nulos++;
            }
        }
        SelectsMultiple();
        $('#FormKg').validator('update');
    };
</script>
@endsection