@extends('layouts.app')
@section('htmlheader_title')
Residuos recepcionados
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	Residuos recepcionados
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	@component('layouts.partials.modal')
		@slot('slug')
			{{-- {{$SolicitudServicio->SolSerSlug}} --}}
		@endslot
		@slot('textModal')
			{{-- la solicitud <b>N° {{$SolicitudServicio->ID_SolSer}}</b> --}}
		@endslot
	@endcomponent
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header with-border">
					<div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
						<h1 class="box-title"><b>Residuos Recepcionados</b></h1>	
					</div>
					@if ($errors->any())
					<div class="alert alert-danger" role="alert">
						<ul>
						@foreach ($errors->all() as $error)
							<p>{{$error}}</p>
						@endforeach
						</ul>
					</div>
					@endif
				</div>				
					<div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
						<h4 class="box-title"><b>Kilogramos por Tratamientos</b></h4>	
					</div>
					<div class="box-header with-border">
						<div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
							<div style="display: flex; justify-content: space-between; align-items: flex-start;">
								<div style="width: 50%; text-align: left;">
									<table id="resumeninventario" class="custom-table" style="margin-left: auto;">
										<thead>
											<tr>
												<th colspan="5">Tratamiento</th>
												<th style="text-align: center; white-space: nowrap;">Kilogramos Conciliados</th>
												<th style="text-align: center; white-space: nowrap;">Kilogramos Tratados</th>
												<th style="text-align: center; white-space: nowrap;">Kilogramos Pendiente</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($cantidadesXtratamiento as $key => $value)
											<tr>
												<th colspan="5">{{$key}}</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['conciliado'], 0, '.', ',') }} kg</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['tratado'], 0, '.', ',')}} kg</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['conciliado'] - $value['tratado'], 0, '.', ',')}} kg</th>
											</tr>
											@endforeach
											<tr>
												<th colspan="5">{{__('adminlte::message.solsershowcantitotal')}}</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['conciliado'], 0, '.', ',')}} kg</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['tratado'], 0, '.', ',')}} kg</th>
												<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['conciliado'] - $total['tratado'], 0, '.', ',')}} kg</th>
											</tr>
										</tbody>
									</table>
								</div>
								<div style="width: 50%; text-align: right;">
									<canvas id="Tratamiento" style="width: 100%; height: auto;"></canvas>
								</div>
							</div>
						</div>
					</div>
				<b>
					<div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
						<h4 class="box-title"><b>Kilogramos por gestores</b></h4>	
					</div>	
						<div class="box-header with-border">
							<div class="col-md-12" id="titulo" style="font-size: 1.2em; text-align:center;">
								<div style="display: flex; justify-content: space-between; align-items: flex-start;">
									<div style="width: 50%; text-align: left;">
										<table id="resumeninventario" class="custom-table">
											<thead>
												<tr>
													<th colspan="5">Gestor</th>
													<th style="text-align: center; white-space: nowrap;">Kilogramos Conciliados</th>
													<th style="text-align: center; white-space: nowrap;">Kilogramos Tratados</th>
													<th style="text-align: center; white-space: nowrap;">Kilogramos Pendiente</th>
												</tr>
											</thead>
											<tbody>
												@foreach ($cantidadesXgestor as $key => $value)
												<tr>
													<th colspan="5">{{$key}}</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['conciliado'], 0, '.', ',') }} kg</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['tratado'], 0, '.', ',')}} kg</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($value['conciliado'] - $value['tratado'], 0, '.', ',')}} kg</th>
												</tr>
												@endforeach
												<tr>
													<th colspan="5">{{__('adminlte::message.solsershowcantitotal')}}</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['conciliado'],0, '.', ',')}} kg</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['tratado'], 0, '.', ',')}} kg</th>
													<th style="text-align: center; white-space: nowrap;"> {{ number_format($total['conciliado'] - $total['tratado'], 0, '.', ',')}} kg</th>
												</tr>
											</tbody>	
										</table>	
									</div>	
									{{--<div style="width: 50%; text-align: right;">
										<canvas id="Gestor" style="width: 100%; height: auto;"></canvas>
									</div>	--}}
								</div>
							</div> 
						</div>
				<br>
				<div class="box-body">
					<table id="RespelStorageTable" class="table table-compact table-bordered table-striped">
						<thead>
							<tr>
								<th>Fecha de Ingreso</th>
								<th>Servicio</th>
								<th>Cliente</th>
								<th>Generador</th>
								<th>Residuo</th>
								<th>Corriente</th>
								<th>Tratamiento</th>
								<th>Gestor</th>
								<th>No. Jaula</th>
								<th>Ingreso a Jaula</th>
								<th>Fecha Envío Gestor</th>
								<th>Cantidad<br>conciliada</th>
								<th>Cantidad<br>tratada</th>
								<th>Cantidad<br>faltante</th>
							</tr>
						</thead>
						<tbody>
							@foreach($solicitudservicios as $servicio)
								<tr>
									<td>{{ $servicio->ProgVehFecha }}</td>
									<td>{{ $servicio->ID_SolSer }}</td>
									<td>{{ $servicio->CliName }}</td>
									<td>{{ $servicio->GenerName }}</td>
									<td>{{ $servicio->RespelName }}</td>
									@if($servicio->YRespelClasf4741 !== null)
										<td class="text-center">{{ $servicio->YRespelClasf4741 }}</td>
									@elseif($servicio->ARespelClasf4741 !== null)
										<td class="text-center">{{ $servicio->ARespelClasf4741 }}</td>
									@else
										<td class="text-center">N/D</td>
									@endif
									<td>{{ $servicio->TratName }}</td>			
									<td>{{ optional($gestor->firstWhere('ID_Trat', $servicio->ID_Trat))->CliShortname ?? 'N/A' }}</td>
									<td>{{$servicio->Jaula}}</td>
									<td>{{$servicio->FechaIngresoJaula}}</td>
									<td></td>
									<td>{{ $servicio->SolResKgConciliado }}</td>
									<td>{{ $servicio->SolResKgTratado }}</td>
									@php
										$pendiente = $servicio->SolResKgConciliado - $servicio->SolResKgTratado;
									@endphp
									<td>{{ $pendiente }}</td>
								</tr>
							@endforeach
						</tbody>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
<style>
.custom-table {
    width: 50%;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
}

.custom-table th, .custom-table td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ccc;
}

.custom-table thead {
    background-color: #4CAF50;
    color: white;
}
</style>
@section('NewScript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Variables con los datos 
    var labels = @json($labels);
    var data = @json($data);
    var ctx = document.getElementById("Tratamiento").getContext("2d");

    // Define los colores que quieres usar para cada segmento
    var backgroundColors = [
        'rgba(51, 255, 122)',  
        'rgba(255, 99, 132)',  
        'rgba(54, 162, 235)',   
        'rgba(255, 206, 86)',   
        'rgba(75, 192, 192)', 
		'rgba(51, 190, 255)',

        // Agrega más colores según la cantidad de datos que tengas
    ];

    var grafica = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [{
                label: 'Tratamientos Conciliados',
                data: data,
                backgroundColor: backgroundColors,  // Asigna el array de colores aquí
                borderColor: 'rgba(0, 0, 0, 0.2)',   // Color del borde, puede ser igual o diferente
                borderWidth: 3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

</script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		//Variables con los datos 
		var labels = @json($labelsgestor);
		var data = @json($datagestor);
		var ctx = document.getElementById("Gestor").getContext("2d");
		var grafica = new Chart(ctx, {
			type: "bar",
			data: {
				labels: labelsgestor,
				datasets: [{
					label: 'Gestores Conciliados',
					data: datagestor,
					backgroundColor: 'rgba(51, 255, 122)',
					borderColor: 'rgba(51, 255, 122)',
					borderWidth: 3
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: true
					}
				}
			}
		});
	});
	
	</script>
@endsection
