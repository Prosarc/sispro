@extends('layouts.app')
@section('htmlheader_title')
Lista de Certificados
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #F1B378, #D66841); padding-right:30vw; position:relative; overflow:hidden;">
	Certificados 2022
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-16 col-md-offset-0">
				<div class="box">
					<div class="box-header with-border">
						{{-- <a href="/solicitud-servicio/documentos/create" class="btn btn-success"><i class="fas fa-file-contract"></i> Añadir Certificado</a> --}}
						{{-- <a disabled href="" class="btn btn-success"><i class="fas fa-file-invoice"></i> Añadir Manifiesto</a> --}}
					</div>
					<div class="box-body">
						<div id="ModalStatus"></div>
						<table class="table table-compact table-bordered table-striped">
							<thead>
								<th>Fecha recepción</th>
								@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
									<th>Cliente</th>
									@endif
								<th># RM</th>
								<th>Servicio</th>
								<th>Tratamiento</th>
								<th># Documento</th>
								<th>Observación</th>
								<th>Archivo</th>
								@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
									<th>Aprobación Dirección</th>
									<th>Aprobación Logística</th>
									<th>Aprobación Operaciones</th>
								@endif


								@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
									<th>Ver</th>
								@endif
								@if(in_array(Auth::user()->UsRol, Permisos::SIGNMANIFCERT))
									<th>Aprobar</th>
								@endif
								@if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES))
									<th>{{'Facturar'}}</th>
								@endif
								@if(in_array(Auth::user()->UsRol, Permisos::SolSerCertifi) || in_array(Auth::user()->UsRol2, Permisos::SolSerCertifi))
									<th>{{trans('adminlte_lang::message.solserstatuscertifi')}}</th>
								@endif
								<th>Actualizado el:</th>
							</thead>
							<tbody>
								@foreach($certificados as $certificado)
								<tr>
									<td>{{date('Y/m/d', strtotime($certificado->recepcion))}}</td>
									@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
										<td class="text-center"><b>{{$certificado->cliente}}</b></td>
										@endif
									<td class="text-center">{{$certificado->CertNumRm}}</td>
									<td class="text-center">#{{$certificado->FK_CertSolser}}</br>
									({{$certificado->SolSerStatus}})
									</td>
									<td class="text-center">{{$certificado->tratamiento->TratName}}</td>
									<td class="text-center">
									@switch($certificado->CertType)
										@case(0)
											{{$certificado->CertNumero}}
											@break
										@case(1)
											{{$certificado->CertManifNumero}}
											@break
										@case(2)
											{{$certificado->CertNumeroExt}}
											@break
										@default
											{{$certificado->ID_Cert}}
									@endswitch
									</td>
									<td>{{$certificado->CertObservacion}}</td>
									@switch($certificado->CertType)
										@case(0)
											@if($certificado->CertSrc!=="CertificadoDefault.pdf")
												<td class="text-center"><a method='get' href='/img/Certificados/{{$certificado->CertSrc}}' target='_blank' class='btn btn-success'><i class='fas fa-file-contract fa-lg'></a></td>
											@else
												<td class="text-center"><a disabled method='get' href='/img/CertificadoDefault.pdf' class='btn btn-default'><i class='fas fa-file-contract fa-lg'></a></td>
											@endif
											@break
										@case(1)
											@if($certificado->CertSrcManif!=="CertificadoDefault.pdf")
												<td class="text-center"><a method='get' href='/img/Manifiestos/{{$certificado->CertSrcManif}}' target='_blank' class='btn btn-primary'><i class='far fa-file-alt fa-lg'></a></td>
											@else
												<td class="text-center"><a disabled method='get' href='/img/CertificadoDefault.pdf' target='_blank' class='btn btn-default'><i class='far fa-file-alt fa-lg'></a></td>
											@endif
											@break
										@case(2)
											@if($certificado->CertSrcExt!=="CertificadoDefault.pdf")
												<td class="text-center"><a method='get' href='/img/CertificadosEXT/{{$certificado->CertSrcExt}}' target='_blank' class='btn btn-warning'><i class='far fa-file-alt fa-lg'></a></td>
											@else
												<td class="text-center"><a disabled method='get' href='/img/CertificadoDefault.pdf' target='_blank' class='btn btn-default'><i class='far fa-file-alt fa-lg'></a></td>
											@endif
											@break
										@default

									@endswitch


									@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
										<td class="text-center" id="AD{{$certificado->CertSlug}}">
											@switch($certificado->CertAuthDp)
												@case(0)
													<p>Pendiente</p>
													@break

												@case(1)
													<i class='fas fa-signature fa-lg'></i>
													<p>Director de Planta</p>
													@break

												@case(2)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Logística</p>
													@break

												@case(3)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Operaciones</p>
													@break

												@case(4)
													<i class='fas fa-signature fa-lg'></i>
													<p>Supervisor de Turno</p>
													@break

												@case(5)
													<i class='fas fa-signature fa-lg'></i>
													<p>Ingeniero HSEQ</p>
													@break

												@case(6)
													<i class='fas fa-signature fa-lg'></i>
													<p>Asistente de Logística</p>
													@break

												@case(7)
													<i class='fas fa-signature fa-lg'></i>
													<p>Programador</p>
													@break

												@default
												<p>Error en Firma Digital</p>
											@endswitch
										</td>
										<td class="text-center" id="AL{{$certificado->CertSlug}}">
											@switch($certificado->CertAuthJl)
												@case(0)
													<p>Pendiente</p>
													@break

												@case(1)
													<i class='fas fa-signature fa-lg'></i>
													<p>Director de Planta</p>
													@break

												@case(2)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Logística</p>
													@break

												@case(3)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Operaciones</p>
													@break

												@case(4)
													<i class='fas fa-signature fa-lg'></i>
													<p>Supervisor de Turno</p>
													@break

												@case(5)
													<i class='fas fa-signature fa-lg'></i>
													<p>Ingeniero HSEQ</p>
													@break

												@case(6)
													<i class='fas fa-signature fa-lg'></i>
													<p>Asistente de Logística</p>
													@break

												@case(7)
													<i class='fas fa-signature fa-lg'></i>
													<p>Programador</p>
													@break

												@default
												<p>Error en Firma Digital</p>
											@endswitch
										</td>

										<td class="text-center" id="AO{{$certificado->CertSlug}}">
											@switch($certificado->CertAuthJo)
												@case(0)
													<p>Pendiente</p>
													@break

												@case(1)
													<i class='fas fa-signature fa-lg'></i>
													<p>Director de Planta</p>
													@break

												@case(2)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Logística</p>
													@break

												@case(3)
													<i class='fas fa-signature fa-lg'></i>
													<p>Jefe de Operaciones</p>
													@break

												@case(4)
													<i class='fas fa-signature fa-lg'></i>
													<p>Supervisor de Turno</p>
													@break

												@case(5)
													<i class='fas fa-signature fa-lg'></i>
													<p>Ingeniero HSEQ</p>
													@break

												@case(6)
													<i class='fas fa-signature fa-lg'></i>
													<p>Asistente de Logística</p>
													@break

												@case(7)
													<i class='fas fa-signature fa-lg'></i>
													<p>Programador</p>
													@break

												@default
												<p>Error en Firma Digital</p>
											@endswitch
										</td>
									@endif

									@if(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC))
									<td class="text-center"><a method='get' href='/certificados/{{$certificado->CertSlug}}' class='btn fixed_widthbtn btn-info'><i class='fas fa-lg fa-search'></i></a></td>
									@endif
									@php
										$Status = ['Conciliado', 'Tratado', 'Facturado'];
										$Status2 = ['Conciliado', 'Tratado'];
									@endphp
									@if(in_array(Auth::user()->UsRol, Permisos::SIGNMANIFCERT))
										@if (in_array($certificado->SolicitudServicio->SolSerStatus, $Status))
											<td class="text-center">
												<button id="{{'buttonfirmarDoc'.$certificado->CertSlug}}" class='btn fixed_widthbtn btn-warning' onclick="firmarDocumento('{{$certificado->CertSlug}}')"><i class='fas fa-lg fa-file-signature'></i></button>
											</td>
										@else
											<td class="text-center">
												<button id="{{'buttonfirmarDoc'.$certificado->CertSlug}}" class='btn fixed_widthbtn btn-default' disabled><i class='fas fa-lg fa-file-signature'></i></button>
											</td>
										@endif
									@endif
									@if(in_array(Auth::user()->UsRol, Permisos::COMERCIALES) || in_array(Auth::user()->UsRol2, Permisos::COMERCIALES))
										<td>
											<button id="{{'buttonCertStatus'.$certificado->SolicitudServicio->SolSerSlug}}" onclick="ModalFacturar('{{$certificado->SolicitudServicio->SolSerSlug}}', '{{$certificado->SolicitudServicio->ID_SolSer}}', '{{in_array($certificado->SolicitudServicio->SolSerStatus, $Status2)}}', 'Facturado', 'Facturar')" {{in_array($certificado->SolicitudServicio->SolSerStatus, $Status2) ? '' :  'disabled'}} style="text-align: center;" class="{{'classFacturarStatus'.$certificado->SolicitudServicio->SolSerSlug}} btn btn-{{in_array($certificado->SolicitudServicio->SolSerStatus, $Status2) ? 'info' : 'default'}}"><i class="fas fa-receipt"></i> {{'Facturar'}}</button>
										</td>
									@endif
									@if(in_array(Auth::user()->UsRol, Permisos::SolSerCertifi) || in_array(Auth::user()->UsRol2, Permisos::SolSerCertifi))
										<td>
											<button id="{{'buttonCertStatus'.$certificado->SolicitudServicio->SolSerSlug}}" onclick="ModalStatus('{{$certificado->SolicitudServicio->SolSerSlug}}', '{{$certificado->SolicitudServicio->ID_SolSer}}', '{{in_array($certificado->SolicitudServicio->SolSerStatus, $Status)}}', 'Certificada', 'certificar')" {{in_array($certificado->SolicitudServicio->SolSerStatus, $Status) ? '' :  'disabled'}} style="text-align: center;" class="{{'classCertStatus'.$certificado->SolicitudServicio->SolSerSlug}} btn btn-{{in_array($certificado->SolicitudServicio->SolSerStatus, $Status) ? 'success' : 'default'}}"><i class="fas fa-certificate"></i> {{trans('adminlte_lang::message.solserstatuscertifi')}}</button>
										</td>
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
<script>
    function renewtoken(token) {
        $('meta[name="csrf-token"]').attr('content', token);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
    }
    function renewTokenAfterError() {
        $.ajax({
            url: "{{url('/renewtokenaftererror')}}",
            method: 'GET',
            data:{},
            success: function(response){
                console.log('renewtokenaftererror OK');
                renewtoken(response);
                console.log(response);
            },
            error: function(xhr, status, error){
                renewtoken('invalid Token');
                console.log('renewtokenaftererror FAIL');
            },
        });
    }
</script>
<script>
	function ModalStatus(slug, id, boolean, value, text){
		if(boolean == 1){
			$('#ModalStatus').empty();
			$('#ModalStatus').append(`
				<div class="modal modal-default fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-body">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<div style="font-size: 5em; color: #f39c12; text-align: center; margin: auto;">
									<i class="fas fa-exclamation-triangle"></i>
									<span style="font-size: 0.3em; color: black;"><p>¿Seguro(a) quiere `+text+` la solicitud <b>N° `+id+`</b>?</p></span>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">No, salir</button>
								<button type="button" id="buttonCertStatusOK`+slug+`" data-dismiss="modal" class='btn btn-success'>Si, acepto</button>
							</div>
						</div>
					</div>
				</div>
			`);
			envsubmit();
			$('#myModal').modal();
			$('#buttonCertStatusOK'+slug).on( "click", function() {
				$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
				});
				$.ajax({
				url: "{{url('/certificarservicio')}}/"+slug,
				method: 'GET',
				data:{},
				beforeSend: function(){
					let buttonsubmit = $('.classCertStatus'+slug);
					buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = true;
								$(this).prop('disabled', true);
							});
					buttonsubmit.empty();
					buttonsubmit.append(`<i class="fas fa-sync fa-spin"></i> Actualizando...`);
				},
				success: function(res){
					let buttonsubmit = $('.classCertStatus'+slug);
					switch (res['code']) {
						case 200:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = true;
								$(this).prop('disabled', true);
							});
							buttonsubmit.prop('class', 'btn btn-default');
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-certificate"></i> Certificado`);

							toastr.success(res['message']);
							break;

						default:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = false;
								$(this).prop('disabled', false);
							});
							buttonsubmit.prop('class', 'btn btn-success classCertStatus'+slug);
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-certificate"></i> Certificar`);

							toastr.error(res['error']);
							break;
					}
				},
				error: function(error){
					let buttonsubmit = $('.classCertStatus'+slug);
					switch (error['responseJSON']['code']) {
						case 400:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = true;
								$(this).prop('disabled', true);
							});
							buttonsubmit.prop('class', 'btn btn-default');
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-certificate"></i> Certificado`);

							break;

						default:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = false;
								$(this).prop('disabled', false);
							});
							buttonsubmit.prop('class', 'btn btn-success classCertStatus'+slug);
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-certificate"></i> Certificar`);

							break;
					}
					toastr.error(error['responseJSON']['message']);
				},
				complete: function(){
					//
				}
				})
			});;
		}
	}
	function ModalFacturar(slug, id, boolean, value, text){
		if(boolean == 1){
			$('#ModalStatus').empty();
			$('#ModalStatus').append(`
				<div class="modal modal-default fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-body">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<div style="font-size: 5em; color: #f39c12; text-align: center; margin: auto;">
									<i class="fas fa-exclamation-triangle"></i>
									<span style="font-size: 0.3em; color: black;"><p>¿Seguro(a) quiere `+text+` la solicitud <b>N° `+id+`</b>?</p></span>
								</div>
                                <form action="/facturarservicio/`+slug+`" class="row" id="facturarservicio`+slug+`">
                                    <div class="form-group col-md-6">
                                        <label for="Costo_transporte">Costo Transporte</label>
                                        <input type="number" name="Costo_transporte" id="Costo_transporte" class="form-control" min="0" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="orden_compra">Orden de Compra</label>
                                        <input type="text" name="orden_compra" id="orden_compra" class="form-control" min="0" maxlength="20">
                                    </div>
                                </form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">No, salir</button>
								<button type="button" id="buttonFacturarStatusOK`+slug+`" data-dismiss="modal" class='btn btn-success'>Si, acepto</button>
							</div>
						</div>
					</div>
				</div>
			`);
			envsubmit();
			$('#myModal').modal();
			$('#buttonFacturarStatusOK'+slug).on( "click", function() {
				$.ajaxSetup({
				headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				});
				$.ajax({
				url: "{{url('/facturarservicio')}}/"+slug,
				method: 'POST',
				data:{
                    ordenCompra:$('#orden_compra').val(),
                    costoTransporte:$('#Costo_transporte').val()
                },
				beforeSend: function(){
					let buttonsubmit = $('.classFacturarStatus'+slug);
					buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = true;
								$(this).prop('disabled', true);
							});
					buttonsubmit.empty();
					buttonsubmit.append(`<i class="fas fa-sync fa-spin"></i> Actualizando...`);
				},
				success: function(res){
                    console.log(res);
					let buttonsubmit = $('.classFacturarStatus'+slug);
					switch (res['code']) {
						case 200:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = true;
								$(this).prop('disabled', true);
							});
							buttonsubmit.prop('class', 'btn btn-default');
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-receipt"></i> Facturado`);

							toastr.success(res['message']);
							break;

                        case 400:
                            buttonsubmit.each(function() {
                                $(this).on('click', function(event) {
                                    event.preventDefault();
                                });
                                $(this).disabled = false;
                                $(this).prop('disabled', false);
                            });
                            buttonsubmit.prop('class', 'btn btn-info classFacturarStatus'+slug);
                            buttonsubmit.empty();
                            buttonsubmit.append(`<i class="fas fa-receipt"></i> Facturar`);
                            console.log('error 400');
                            if (res['message']) {
                                toastr.error(res['message']);
                            }else{
                                toastr.error('Error 400:Petición o Solicitud Incorrecta');
                            }
                            break;

						default:
							buttonsubmit.each(function() {
								$(this).on('click', function(event) {
									event.preventDefault();
								});
								$(this).disabled = false;
								$(this).prop('disabled', false);
							});
							buttonsubmit.prop('class', 'btn btn-info classFacturarStatus'+slug);
							buttonsubmit.empty();
							buttonsubmit.append(`<i class="fas fa-receipt"></i> Facturar`);

							toastr.error(res['error']);
							break;
					}
                    renewtoken(res['new_token']);
				},
				error: function(xhr, status, error){
					let buttonsubmit = $('.classFacturarStatus'+slug);
                    switch (xhr.status) {
                        case 400:
                            console.log('error 400');
                            toastr.error('Error 400:Petición o Solicitud Incorrecta');
                            break;

                        case 401:
                            console.log('error 401');
                            toastr.error('Error 401: usuario no autorizado, inicie sesion e intente de nuevo');
                            break;

                        case 419:
                            console.log('error 419');
                            toastr.error('token CSRF no coincide... Recargue la pagina e intente de nuevo');

                            break;

                        case 422:
                            console.log('error 422');
                            toastr.error('datos invalidos, verifique que esta ingresando la información correctamente');
                            break;

                        default:
                            console.log('error default');
                            toastr.error('error no definido');
                            break;
                    }
                        switch (error['responseJSON']['code']) {
                            case 400:
                                buttonsubmit.each(function() {
                                    $(this).on('click', function(event) {
                                        event.preventDefault();
                                    });
                                    $(this).disabled = true;
                                    $(this).prop('disabled', true);
                                });
                                buttonsubmit.prop('class', 'btn btn-default');
                                buttonsubmit.empty();
                                buttonsubmit.append(`<i class="fas fa-receipt"></i> Facturado`);

                                break;

                            default:
                                buttonsubmit.each(function() {
                                        $(this).on('click', function(event) {
                                            event.preventDefault();
                                        });
                                        $(this).disabled = false;
                                        $(this).prop('disabled', false);
                                    });
                                    buttonsubmit.prop('class', 'btn btn-info classFacturarStatus'+slug);
                                    buttonsubmit.empty();
                                    buttonsubmit.append(`<i class="fas fa-receipt"></i> Facturar`);
                                    $.each(xhr.responseJSON.errors, function(key,value) {
                                        toastr.error(value);
                                    });
                        }
                        renewTokenAfterError();
				},
				complete: function(){
					//
				}
				})
			});;
		}
	}
</script>
@if(in_array(Auth::user()->UsRol, Permisos::SIGNMANIFCERT))
<script>
function renewtoken(token) {
	$('meta[name="csrf-token"]').attr('content', token);
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': token
		}
	});
}
function renewTokenAfterError() {
    $.ajax({
        url: "{{url('/renewtokenaftererror')}}",
        method: 'GET',
        data:{},
        success: function(response){
            console.log('renewtokenaftererror OK');
            renewtoken(response);
            console.log(response);
        },
        error: function(xhr, status, error){
            renewtoken('invalid Token');
            console.log('renewtokenaftererror FAIL');
        },
    });
}
function firmarDocumento(CertSlug){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url: "{{url('/firmarCertificado')}}/"+CertSlug,
		method: 'PUT',
		data:{},
		beforeSend: function(){
			let buttonsubmit = $('#buttonfirmarDoc'+CertSlug);
			buttonsubmit.each(function() {
						$(this).on('click', function(event) {
							event.preventDefault();
						});
						$(this).disabled = true;
						$(this).prop('disabled', true);
					});
			buttonsubmit.empty();
			buttonsubmit.append(`<i class="fas fa-sync fa-spin"></i>`);
		},
		success: function(data, textStatus, jqXHR){
			renewtoken(data.newtoken);
			let buttonsubmit = $('#buttonfirmarDoc'+CertSlug);
			buttonsubmit.each(function() {
				$(this).on('click', function(event) {
					event.preventDefault();
				});
				$(this).disabled = false;
				$(this).prop('disabled', false);
			});
			buttonsubmit.prop('class', 'btn btn-primary');
			buttonsubmit.empty();
			buttonsubmit.append(`<i class="fas fa-lg fa-file-signature"></i>`);

			adireccion = $('#AD'+CertSlug);
			alogisica = $('#AL'+CertSlug);
			aoperaciones = $('#AO'+CertSlug);

			ADfirmaCorrespondiente = "";
			ALfirmaCorrespondiente = "";
			AOfirmaCorrespondiente = "";
			switch (data.Documento['CertAuthDp']) {
				case 0:
				ADfirmaCorrespondiente = `<p>Pendiente</p>`;
					break;

				case 1:
				ADfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Director de Planta</p>`;
					break;

				case 2:
				ADfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Logística</p>`;
					break;

				case 3:
				ADfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Operaciones</p>`;
					break;

				case 4:
				ADfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Supervisor de Turno</p>`;
					break;

				case 5:
				ADfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Ingeniero HSEQ</p>`;
					break;

				case 6:
				ADfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Asistente de Logística</p>`;
					break;

				case 7:
				ADfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Programador</p>`;
					break;

				default:
				ADfirmaCorrespondiente = `<p>Error en Firma Digital</p>`;
					break;
			}

			switch (data.Documento['CertAuthJl']) {
				case 0:
				ALfirmaCorrespondiente = `<p>Pendiente</p>`;
					break;

				case 1:
				ALfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Director de Planta</p>`;
					break;

				case 2:
				ALfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Logística</p>`;
					break;

				case 3:
				ALfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Operaciones</p>`;
					break;

				case 4:
				ALfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Supervisor de Turno</p>`;
					break;

				case 5:
				ALfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Ingeniero HSEQ</p>`;
					break;

				case 6:
				ALfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Asistente de Logística</p>`;
					break;

				case 7:
				ALfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Programador</p>`;
					break;

				default:
				ALfirmaCorrespondiente = `<p>Error en Firma Digital</p>`;
					break;
			}

			switch (data.Documento['CertAuthJo']) {
				case 0:
				AOfirmaCorrespondiente = `<p>Pendiente</p>`;
					break;

				case 1:
				AOfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Director de Planta</p>`;
					break;

				case 2:
				AOfirmaCorrespondiente =`<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Logística</p>`;
					break;

				case 3:
				AOfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Jefe de Operaciones</p>`;
					break;

				case 4:
				AOfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Supervisor de Turno</p>`;
					break;

				case 5:
				AOfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Ingeniero HSEQ</p>`;
					break;

				case 6:
				AOfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Asistente de Logística</p>`;
					break;

				case 7:
				AOfirmaCorrespondiente = `<i class='fas fa-signature fa-lg'></i>
				<p>Programador</p>`;
					break;

				default:
				AOfirmaCorrespondiente = `<p>Error en Firma Digital</p>`;
					break;
			}

			adireccion.empty();
			adireccion.append(ADfirmaCorrespondiente);

			alogisica.empty();
			alogisica.append(ALfirmaCorrespondiente);

			aoperaciones.empty();
			aoperaciones.append(AOfirmaCorrespondiente);

			toastr.success(data['message']);
		},
		error: function(xhr, textStatus, jqXHR){
			renewtoken(xhr.newtoken);
			let buttonsubmit = $('#buttonfirmarDoc'+CertSlug);
			switch (xhr['status']) {
				case 400:
					buttonsubmit.each(function() {
						$(this).on('click', function(event) {
							event.preventDefault();
						});
						$(this).disabled = true;
						$(this).prop('disabled', true);
					});
					buttonsubmit.prop('class', 'btn btn-default');
					buttonsubmit.empty();
					buttonsubmit.append(`<i class="fas fa-lg fa-file-signature"></i>`);
					break;
				case 404:
					buttonsubmit.each(function() {
						$(this).on('click', function(event) {
							event.preventDefault();
						});
						$(this).disabled = true;
						$(this).prop('disabled', true);
					});
					buttonsubmit.prop('class', 'btn btn-danger');
					buttonsubmit.empty();
					buttonsubmit.append(`<i class="fas fa-lg fa-file-signature"></i>`);
					break;

				default:
					buttonsubmit.each(function() {
						$(this).on('click', function(event) {
							event.preventDefault();
						});
						$(this).disabled = false;
						$(this).prop('disabled', false);
					});
					buttonsubmit.prop('class', 'btn btn-default');
					buttonsubmit.empty();
					buttonsubmit.append(`<i class="fas fa-lg fa-file-signature"></i>`);
					break;
			}
			toastr.error(xhr['responseJSON']['message']);
		},
		complete: function(){

			//
		}
	});
}
</script>
@endif
@endsection
