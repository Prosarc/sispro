@extends('layouts.app')
@section('htmlheader_title')
Lista de Certificados
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #fbc2eb, #aa66cc); padding-right:30vw; position:relative; overflow:hidden;">
	Certificados
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-16 col-md-offset-0">
				<div class="box">
					<div class="box-header with-border">
						{{-- <a href="/solicitud-servicio/{{$SolicitudServicio->SolSerSlug}}/documentos/create" class="btn btn-success"><i class="fas fa-file-contract"></i> Añadir Certificado</a> --}}
						{{-- <a disabled href="" class="btn btn-success"><i class="fas fa-file-invoice"></i> Añadir Manifiesto</a> --}}
					</div>
					<div class="box-body">
						<table class="table table-compact table-bordered table-striped">
							<thead>
								<th>Servicio</th>
								<th>#</th>
								<th>Documento</th>
								<th>Observación</th>
								<th>Aprobación HSEQ</th>
								<th>Aprobación Operaciones</th>
								<th>Aprobación Logística</th>
								<th>Aprobación Director Planta</th>
								
								@if(in_array(Auth::user()->UsRol, Permisos::EDITMANIFCERT))
									<th>Ver</th>
								@endif
								@if(in_array(Auth::user()->UsRol, Permisos::SIGNMANIFCERT))
									<th>Firmar</th>
								@endif
								<th>Actualizado el:</th>
							</thead>
							<tbody>
								@foreach($certificados as $certificado)
								<tr>
									<td>{{$certificado->FK_CertSolser}}</td>
									<td>{{$certificado->ID_Cert}}</td>
									@if($certificado->CertSrc!=="CertificadoDefault.pdf")
										<td class="text-center"><a method='get' href='/img/Certificados/{{$certificado->CertSrc}}' target='_blank' class='btn btn-success'><i class='fas fa-file-contract fa-lg'></a></td>
									@else
										<td class="text-center"><a disabled method='get' href='/img/{{$certificado->CertSrc}}' class='btn btn-default'><i class='fas fa-file-contract fa-lg'></a></td>
									@endif
									<td>{{$certificado->CertObservacion}}</td>
									<td class="text-center">
										@if($certificado->CertAuthHseq === 1)
											<i class='fas fa-signature'></i>
										@else
											<p>Pendiente</p>
										@endif
									</td>
									<td class="text-center">
										@if($certificado->CertAuthJo === 1)
											<i class='fas fa-signature'></i>
										@else
											<p>Pendiente </p>
										@endif
									</td>
									<td class="text-center">
										@if($certificado->CertAuthJl === 1)
											<i class='fas fa-signature'></i>
										@else
											<p>Pendiente </p>
										@endif
									</td>
									<td class="text-center">
										@if($certificado->CertAuthDp === 1)
											<i class='fas fa-signature'></i>
										@else
											<p>Pendiente </p>
										@endif
									</td>
									@if(in_array(Auth::user()->UsRol, Permisos::EDITMANIFCERT))
									<td class="text-center"><a method='get' href='/certificado/{{$certificado->CertSlug}}' data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" title="<b>Actualizar Certificado</b>" data-content="<p style='width: 50%'>Puede actualizar el Certificado e ingresar información relevante para la generación del mismo </p>" class='btn fixed_widthbtn btn-warning'><i class='fas fa-lg fa-file-signature'></i></a></td>
									@endif
									@if(in_array(Auth::user()->UsRol, Permisos::SIGNMANIFCERT))
									<td class="text-center"><a method='get' href='/certificado/{{$certificado->CertSlug}}/firmar/{{$SolicitudServicio->SolSerSlug}}' data-placement="auto" data-trigger="hover" data-html="true" data-toggle="popover" title="<b>Firmar Certificado</b>" data-content="<p style='width: 50%'>Este boton le permite marcar el certificado como firmado en la Base de datos </p>" class='btn fixed_widthbtn btn-warning'><i class='fas fa-lg fa-file-signature'></i></a></td>
									@endif
									<td>{{$certificado->updated_at}}</td>
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
@section('NewScript')
@endsection