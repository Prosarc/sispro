@extends('layouts.app')
@section('htmlheader_title')
Termodestrucción
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	{{'Residuos para incinerar'}}
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="row">
	<div class="col-md-3">
		<div class="box box-info" style="overflow-y: auto; max-height: 560px;">
			<div class="box-header with-border">
				<h4 class="box-title">Servicios por Incinerar</h4>
			</div>
			<div class="box-body">
				<div id="external-events">
					@foreach($residuos as $residuo)
						@php
							if($residuo->SolSerAuditable == '1'){
								$color = 'bg-aqua';
							}
							else{
								$color = 'bg-green';
							}
						@endphp
						<p style="background-color: #001f3f; color: #fff; padding-top: 15px !important; padding-bottom: 0 !important; text-align: center;" class="external-event ui-draggable ui-draggable-handle servicionoprogramado col-md-12 form-group col-xs-12" data-tipo="{{$residuo->SolSerAuditable}}" data-id="{{$residuo->ID_SolRes}}">
							<span class="col-md-12 form-group col-xs-12">N° {{$residuo->ID_SolSer.' - '.$residuo->CliName.' - '.$residuo->RespelName. ' / '. $residuo->SolResKgRecibido}}</span>
							<a href="/solicitud-servicio/{{$residuo->RespelSlug}}" target="_blank" class='{{$color}} col-md-12 form-group col-xs-12' style="border-radius: 4px;">{{ __('adminlte::message.see') }}</a>
						</p>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="box box-info">
			<div class="box-body no-padding">
				<div id='calendar'></div>
			</div>
		</div>
	</div>
</div>

{{--  Modal --}}
<div class="modal modal-default fade in" id="CrearDieta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="titleModalCreate">Programación de dieta</h4>
			</div>
			<div class="box box-info">
				<div class="modal-body">
					<div style="margin: auto;" id="descripModalCreate">
						<form action="/termodestruccion/programar" method="POST" id="formularioCreate" data-toggle="validator">
							@csrf
                            <input type="hidden" hidden name="ID_SolSer" class="ID_SolSer" id="ID_SolSer">
							<div class="box-body">
								<div class="form-group col-md-12">
									<label>Seleccione el Turno</label>
									<select name="turno" id="turno" class="form-control" required>
										<option value="">Seleccione...</option>
										<option selected value="1">Turno 1</option>
										<option value="2">Turno 2</option>
									</select>
								</div>
                                <div class="form-group col-md-12">
									<label>Seleccione el Ingeniero de turno</label>
									<select name="ingturno" id="ingturno" class="form-control" required>
										<option value="">Seleccione...</option>
                                        @foreach($supervisores as $supervisor)
										<option value="{{$supervisor->ID_Pers}}">{{$supervisor->PersFirstName. ' '.$supervisor->PersSecondName. ' '. $supervisor->PersLastName}}</option>
                                        @endforeach
									</select>
								</div>
                                <div class="form-group col-md-12">
									<label>Seleccione el Hornero Principal</label>
									<select name="hornerop" id="hornerop" class="form-control" required>
										<option value="">Seleccione...</option>
                                        @foreach($horneos as $horneo)
										<option value="{{$horneo->ID_Pers}}">{{$horneo->PersFirstName. ' '.$horneo->PersSecondName. ' '. $horneo->PersLastName}}</option>
                                        @endforeach
									</select>
								</div>
                                <div class="form-group col-md-12">
									<label>Seleccione el Hornero Secundario</label>
									<select name="horneros" id="horneros" class="form-control" required>
										<option value="">Seleccione...</option>
                                        @foreach($horneossecundarios as $horneosecundario)
										<option value="{{$horneosecundario->ID_Pers}}">{{$horneosecundario->PersFirstName. ' '.$horneosecundario->PersSecondName. ' '. $horneosecundario->PersLastName}}</option>
                                        @endforeach
									</select>
								</div>
                                <div class="form-group col-md-12" id="containerDePrecintos">
									<div class="row" id="Cantidad">
										<div class="col-md-12">
											<label>Cantidad a Incinerar</label>
										</div>
										<div class="col-md-8">
											<input type="text" name="CantidadIncinerar" class="form-control">
										</div>
									</div>
								</div>
								<div class="form-group col-xs-12 col-md-6">
									<label for="ProgIncFecha">Fecha de programación</label>
									<input  class="form-control ProgIncFecha" type="date" id="ProgIncFecha" name="ProgIncFecha" min="{{date('Y-m-d', strtotime("1 months ago"))}}" value="{{old('ProgIncFecha')}}">
									<small class="help-block with-errors"></small>
								</div>
                                <input type="submit" hidden="true" id="submit1" name="submit1">
							</div>
						</form>
					</div>
				</div>
				<div class="box box-info">
					<div class="modal-footer">
						<label for="submit1" class="btn btn-success">{{ __('adminlte::message.add') }}</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- END Modal --}}


<div id="ModalDelete"></div>
@endsection
@section('NewScript')
{{-- fullcalendar --}}
<script type="text/javascript" src="{{ url (mix('/js/fullcalendar.js')) }}"></script>

<script>
	@if ($errors->create->any())
		$(document).ready(function(){
			$('#CrearDieta').modal("show");
		});
	@endif
	document.addEventListener('DOMContentLoaded', function() {
		@if(session('Delete'))
			NotifiTrue('{{session('Delete')}}');
		@endif
		var calendarEl = document.getElementById('calendar');
		@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic1) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic1))
		var Draggable = FullCalendarInteraction.Draggable;
		var containerEl = document.getElementById('external-events');
		new Draggable(containerEl, {
			itemSelector: '.external-event',
			eventData: function(eventEl) {
				return {
					id: eventEl.dataset.id,
					title: eventEl.dataset.tipo,
				};
			}
		});
		@endif
		var calendar = new FullCalendar.Calendar(calendarEl, {
			plugins: ['interaction', 'dayGrid', 'timeGrid'],
			locale: 'es',
			timeZone: 'UTC',
			defaultView: 'dayGridMonth',
			buttonText:{
				today: 'Hoy',
				day: 'Día',
				month: 'Mes',
				week: 'Semana'
			},
			defaultRangeSeparator: ' - ',
			height: 'parent',
			customButtons: {
				ListProg: {
					text: 'Listar Programaciones',
					click: function() {
						window.location.href = "{{url('/termodestruccion/programacion')}}";
					}
				}
			},
			header: {
				left: 'dayGridMonth,timeGridWeek,timeGridDay',
				center: 'title',
				right: 'prev,today,next'
			},
			footer: {
				left: '',
				center: '',
				right: 'ListProg'
			},
			aspectRatio: 2,
			// displayEventTime : false,
			eventSources: [{
                events: [
                    @foreach($incineracion as $residuo)
                    {
                        id: '{{ $residuo->ID_SolRes }}',
						url: "{{ url('/termodestruccion/programar/editar/'.$residuo->ID_Incineracion) }}",
						color: '{{ $residuo->IncColor }}',
						title: '{{ $residuo->CliName." - ".$residuo->Cantidadprog. "Kg" }}',
						start: '{{ \Carbon\Carbon::parse($residuo->FechaIncineracion)->toISOString() }}',
						textColor: 'black'
                    }@if(!$loop->last),@endif
                    @endforeach
                ],
            }],

			eventLimit: true,
			eventLimitText: "más",
			views: {
				month: {
					eventLimit: 4
				}
			},
			dateClick: function(info) {
				calendar.changeView('timeGridDay', info.dateStr);
			},
			@if(in_array(Auth::user()->UsRol, Permisos::ProgVehic1) || in_array(Auth::user()->UsRol2, Permisos::ProgVehic1))
			droppable: true,
			eventStartEditable: true,
			drop : function( dropInfo ) {
				let hora = FullCalendar.formatDate(dropInfo.date.toUTCString(), {
					hour: '2-digit',
					hour12: false,
					minute: '2-digit'
				});
			},
			eventReceive: function( info ) {
				var id = info.event.id;
				var tipo = info.event.title;
				$('#ID_SolSer').val(id);
				info.event.remove();
				$('#CrearDieta').modal();
				$("#CrearDieta").on("hidden.bs.modal", function () {
					$('#turno').val("");
					$('#ingturno').val("");
					$('#hornerop').val("");
					$('#horneros').val("");
                    $('#CantidadIncinerar').val("");
                    $('#ProgIncFecha').val("");
				});
				
			},
			eventDrop: function( eventDropInfo ) {
				CambioDeFecha(eventDropInfo.event);
			},
			eventClick: function(info){
				info.jsEvent.preventDefault();
				window.open(info.event.url);
			}
			@endif
		});
		calendar.render();
	});
</script>
@endsection