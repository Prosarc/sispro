@extends('layouts.app')
@section('htmlheader_title')
    Importar Residuos
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, rgb(69, 202, 252), rgb(48, 63, 159)); padding-right:30vw; position:relative; overflow:hidden;">
    Importar Residuos
    <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
    <div class="row">
        <div class="col-md-16 col-md-offset-0">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Importación Masiva de Residuos</h3>
                </div>
                <div class="box-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#instrucciones" aria-controls="instrucciones" role="tab" data-toggle="tab">
                                <i class="fas fa-info-circle"></i> Instrucciones
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#clientes" aria-controls="clientes" role="tab" data-toggle="tab">
                                <i class="fas fa-building"></i> Lista de Clientes y Sedes
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#importar" aria-controls="importar" role="tab" data-toggle="tab">
                                <i class="fas fa-file-import"></i> Importar Archivo
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="instrucciones">
                            <div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title">Instrucciones para la Importación</h3>
                                </div>
                                <div class="box-body">
                                    <h4>Formato del Archivo CSV</h4>
                                    <p>El archivo debe ser un CSV con las siguientes columnas (en este orden exacto):</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Columna</th>
                                                    <th>Descripción</th>
                                                    <th>Valores Permitidos</th>
                                                    <th>Obligatorio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>cliente_id</td>
                                                    <td>ID del cliente</td>
                                                    <td>Número entero (ver pestaña "Lista de Clientes")</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>sede_id</td>
                                                    <td>ID de la sede</td>
                                                    <td>Número entero (ver pestaña "Lista de Clientes")</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>nombre</td>
                                                    <td>Nombre del residuo</td>
                                                    <td>Texto (máx. 128 caracteres)</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>descripcion</td>
                                                    <td>Descripción detallada del residuo</td>
                                                    <td>Texto</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>clasificacion_y</td>
                                                    <td>Clasificación Y según decreto 4741</td>
                                                    <td>Y1 a Y45, o vacío</td>
                                                    <td>No</td>
                                                </tr>
                                                <tr>
                                                    <td>clasificacion_a</td>
                                                    <td>Clasificación A según decreto 4741</td>
                                                    <td>A1010 a A4160, o vacío</td>
                                                    <td>No</td>
                                                </tr>
                                                <tr>
                                                    <td>peligrosidad</td>
                                                    <td>Tipo de peligrosidad del residuo</td>
                                                    <td>"No peligroso", "Corrosivo", "Reactivo", "Explosivo", "Toxico", "Inflamable", "Patógeno - Infeccioso", "Radiactivo"</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>estado_fisico</td>
                                                    <td>Estado físico del residuo</td>
                                                    <td>"Líquido", "Sólido", "Gaseoso", "SemiSólido", "Aerosol"</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>tratamiento</td>
                                                    <td>Tipo de tratamiento</td>
                                                    <td>"incineracion" o "celda_seguridad"</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>sustancia_controlada</td>
                                                    <td>¿Es una sustancia controlada?</td>
                                                    <td>1 (Sí) o 0 (No)</td>
                                                    <td>Sí</td>
                                                </tr>
                                                <tr>
                                                    <td>tipo_sustancia_controlada</td>
                                                    <td>Tipo de sustancia controlada</td>
                                                    <td>1 o 0</td>
                                                    <td>Solo si sustancia_controlada = 1</td>
                                                </tr>
                                                <tr>
                                                    <td>nombre_sustancia_controlada</td>
                                                    <td>Nombre de la sustancia controlada</td>
                                                    <td>Texto (máx. 128 caracteres)</td>
                                                    <td>Solo si sustancia_controlada = 1</td>
                                                </tr>
                                                <tr>
                                                    <td>declaracion_residuo</td>
                                                    <td>¿Tiene declaración?</td>
                                                    <td>1 (Sí) o 0 (No)</td>
                                                    <td>Sí</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <h4>Notas Importantes:</h4>
                                    <ul>
                                        <li>El archivo debe estar en formato CSV (valores separados por comas)</li>
                                        <li>La primera línea debe contener los nombres exactos de las columnas</li>
                                        <li>Los valores de texto que contengan comas deben estar entre comillas dobles</li>
                                        <li>Asegúrese de que el cliente_id y sede_id correspondan a una relación válida</li>
                                        <li>Los campos de clasificación Y y A son obligatorios si el residuo es peligroso</li>
                                        <li>Para residuos peligrosos, se creará automáticamente una hoja de seguridad por defecto</li>
                                    </ul>

                                    <div class="alert alert-info">
                                        <i class="fas fa-download"></i> 
                                        <a href="{{ asset('plantillas/plantilla_residuos.csv') }}" class="alert-link">Descargar plantilla CSV</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="clientes">
                            <div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title">Lista de Clientes y sus Sedes</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID Cliente</th>
                                                    <th>Nombre Cliente</th>
                                                    <th>ID Sede</th>
                                                    <th>Dirección Sede</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($clientes as $cliente)
                                                    @foreach($cliente->sedes as $sede)
                                                    <tr>
                                                        <td>{{$cliente->ID_Cli}}</td>
                                                        <td>{{$cliente->CliName}}</td>
                                                        <td>{{$sede->ID_Sede}}</td>
                                                        <td>{{$sede->SedeAddress}}</td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="importar">
                            <div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title">Subir Archivo CSV</h3>
                                </div>
                                <div class="box-body">
                                    <form action="{{ route('respel.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="file">Seleccione el archivo CSV</label>
                                            <input type="file" class="form-control" name="file" accept=".csv,.txt" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Importar Residuos
                                        </button>
                                    </form>
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