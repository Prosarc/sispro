@extends('layouts.app')
@section('htmlheader_title')
Asignar Jaula
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	Asignar Jaula
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="box-body" style="display: flex;">
    <div class="box box-info" style="overflow-y: auto; width: 96%; margin-right: 10px">
        <form id= "formAsignarJaulas" method="POST" action="{{ route('asignar.jaulas') }}">
            @csrf
            <div class="form-group col-md-12" style="width: 50%">
                <br>
                <label data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" title="<b> Seleccione la Solicitud</b>" data-content="Seleccione la solicitud">
                    <i style="font-size: 1.8rem; color: Dodgerblue;" class="fas fa-info-circle fa-2x fa-spin"></i>
                    Seleccione la solicitud
                </label>
                <small class="help-block with-errors">*</small>
                <select id="Num_Solicitud" name="Num_Solicitud" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($solicitudservicios as $servicio)
                        <option>{{$servicio->ID_SolSer}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12" style="width: 50%">
                <br>
                <label data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" title="<b> Seleccione el tratamiento</b>" data-content="Seleccione el tratamiento">
                    <i style="font-size: 1.8rem; color: Dodgerblue;" class="fas fa-info-circle fa-2x fa-spin"></i>
                    Tratamientos Pendientes
                </label>
                <small class="help-block with-errors">*</small>
                <select id="Tratamientos_Pendientes" name="Tratamientos_Pendientes" class="form-control" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div id="contenedorJaulas"></div>
            <br>
            <br>
            <div class="box-footer">
            <button type="submit" class="btn btn-primary">Asignar Jaula</button>
            </div>
        </form>

    </div>    
</div>
@endsection
@section('NewScript')
<script>
    $(document).ready(function() {
        $('#Num_Solicitud').on('change', function() {
            var solicitudId = $(this).val();
            
            // Vacía el campo de tratamientos antes de llenarlo con nuevos datos
            $('#Tratamientos_Pendientes').empty().append('<option value="">Seleccione...</option>');
            
            if (solicitudId) {
                $.ajax({
                    url: '/jaulas/tratamiento/' + solicitudId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $.each(data, function(index, tratamiento) {
                            $('#Tratamientos_Pendientes').append(
                                '<option value="' + tratamiento.ID_Trat + '">' + tratamiento.TratName + '</option>'
                            );
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar tratamientos:', error); 
                        alert('Hubo un error al cargar los tratamientos: ' + xhr.responseText);
                    }
                });
            }
        });

        // Evento para cuando se selecciona una opción en Tratamientos_Pendientes
        $('#Tratamientos_Pendientes').on('change', function() {
            var tratamientoSeleccionado = $(this).val();
            var solicitudId = $('#Num_Solicitud').val(); // Obtener solicitudId actualizado
            if (tratamientoSeleccionado && solicitudId) {
                mostrarJaulasDisponibles(tratamientoSeleccionado, solicitudId);
            }
        });
    });

    function mostrarJaulasDisponibles(tratamientoSeleccionado, solicitudId) {
    // Realiza una solicitud AJAX para obtener los datos específicos de jaulas para el tratamiento y solicitud seleccionados
    $.ajax({
        url: '/jaulas/disponibles/' + tratamientoSeleccionado + '/' + solicitudId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log(data);

            // Inicializa el HTML de jaulas disponibles
            var html = `
                <div class="form-group col-md-12" style="width: 50%">
                            <div class="form-group col-md-6" style="width: 50%">
                                <label data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" 
                                    title="<b>Jaulas Disponibles</b>" data-content="Jaulas Disponibles">
                                    <i style="font-size: 1.8rem; color: Dodgerblue;" class="fas fa-info-circle fa-2x fa-spin"></i>
                                    Seleccione las jaulas 
                                </label>
                                <div style="overflow-y: auto; max-height: 560px; margin-left: 20px; margin-bottom: 10px">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%"></th>
                                                <th style="width: 80%; text-align: left;">Nombre Jaula</th>
                                                <th style="width: 20%; text-align: left;">Capacidad (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                                    // Itera sobre cada jaula en la respuesta y construye las filas de la tabla
                                    $.each(data.jaulas, function(index, jaula) {
                                        html += `
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="jaulas[]" value="${jaula.ID_Jaula}">
                                                </td>
                                                <td>Jaula Número ${jaula.ID_Jaula} - ${jaula.TratName}</td>
                                                <td>${jaula.PorcentajeOcupacion} %</td>
                                            </tr>`;
                                    });

                                     html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                </div>`;


            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group col-md-6" style="width: 50%">
                        <label data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" 
                            title="<b>Residuos de Solicitud</b>" data-content="Residuos de Solicitud">
                            <i style="font-size: 1.8rem; color: Dodgerblue;" class="fas fa-info-circle fa-2x fa-spin"></i>
                            Residuos de Solicitud
                        </label>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Residuo</th>
                                    <th>Hoja de Seguridad</th>
                                    <th>Peso</th>
                                </tr>
                            </thead>
                            <tbody>`;

            // Itera sobre cada residuo en la respuesta y construye las filas de la tabla
            $.each(data.residuos, function(index, residuo) {
                html += `
                    <tr>
                        <td>${residuo.RespelName}</td>
                        <td class="text-center">
                            <a href="/img/HojaSeguridad/${residuo.RespelHojaSeguridad}" target="_blank" class="btn btn-success">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </a>
                        </td>
                        <td>${residuo.SolResKgRecibido} - Kg</td>
                    </tr>`;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>`;

            // Agrega el HTML en el lugar deseado
            $('#contenedorJaulas').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar jaulas disponibles:', error);
            alert('Hubo un error al cargar las jaulas disponibles: ' + xhr.responseText);
        }
    });
}



</script>


@endsection
