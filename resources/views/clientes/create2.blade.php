@extends('layouts.app')
@section('htmlheader_title')
{{ trans('adminlte_lang::message.clientregistertittle') }}
@endsection
@section('contentheader_title')
{{ trans('adminlte_lang::message.clientregistertittle') }}
@endsection
@section('main-content')
<div class="container-fluid spark-screen">
	<div class="row">
		<div class="col-md-16 col-md-offset-0">
			<!-- Default box -->
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Datos Básicos de la empresa</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i></button>
						<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
						<i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="row">
					<!-- left column -->
					<div class="col-md-12">
						<!-- general form elements -->
						<div class="box box-primary">
                            @include('layouts.partials.spinner')
                            <!-- form start -->
							<form role="form" id="myForm1" action="/clientes" method="POST" enctype="multipart/form-data" data-toggle="validator">
                                @csrf
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <p>{{$error}}</p>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="box-body" hidden onload="renderTable()" id="readyTable">
									<div class="tab-pane" id="addRowWizz">
										<p>Añada la información necesaria completando los campos requeridos</p>
										<div class="smartwizard">
											<ul>
												<li><a href="#step-1"><b>Paso 1</b><br /><small>Datos de la Empresa</small></a></li>
												<li><a href="#step-2"><b>Paso 2</b><br /><small>Datos de la sede</small></a></li>
												<li><a href="#step-3"><b>Paso 3</b><br /><small>Datos de la persona de contacto</small></a></li>
											</ul>
											<!-- general form elements -->
								            <div class="row">
                                                
												<div id="step-1" class="tab-pane step-content">
                                                    
                                                    <div id="form-step-0" role="form" data-toggle="validator">
                                                        <div class="form-group col-md-12">
                                                            <label for="ClienteInputNit">NIT</label><small class="help-block with-errors">*</small>
                                                            <input type="text" name="CliNit" class="form-control nit" id="ClienteInputNit" data-minlength="13" data-maxlength="13" placeholder="XXX.XXX.XXX-Y" required>
                                                        </div>
                                                        <div class="col-md-12 form-group">
                                                            <label for="ClienteInputRazon">Razón Social</label><small class="help-block with-errors">*</small>
                                                            <input type="text" name="CliName" class="form-control" id="ClienteInputRazon"  minlength="5"  maxlength="100" placeholder="PROTECCION SERVICIOS AMBIENTALES RESPEL DE COLOMBIA S.A. ESP." required>
                                                        </div>
                                                        <div class="col-md-12 form-group">
                                                            <label for="ClienteInputNombre">Nombre Corto</label><small class="help-block with-errors">*</small>
                                                            <input type="text" name="CliShortname" class="form-control" id="ClienteInputNombre" placeholder="Prosarc" minlength="2"  maxlength="100" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="tipo">Tipo de Empresa</label><small class="help-block with-errors">*</small>
                                                            <select class="form-control" id="tipo" name="CliType" required>
                                                                <option onclick="HiddenOtroType()" value="Organico">Organico</option>
                                                                <option onclick="HiddenOtroType()" value="Biologico">Biologico</option>
                                                                <option onclick="HiddenOtroType()" value="Industrial">Industrial</option>
                                                                <option onclick="HiddenOtroType()" value="Medicamentos">Medicamentos</option>
                                                                <option onclick="OtroType()" value="">Otro</option>
                                                            </select>
                                                            
                                                        </div>
                                                        <div id="otro"class="form-group col-md-6">
                                                        </div>
                                                        @if(Auth::user()->UsRol === "admin")
                                                        <div class="col-md-6">
                                                            <label for="categoria">Categoría</label>
                                                            <select class="form-control" id="categoria" name="CliCategoria" required>
                                                                <option value="">Seleccione...</option>
                                                                <option>Cliente</option>
                                                                <option>Transportador</option>
                                                                <option>Proveedor</option>
                                                            </select>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div id="step-2" class="">
                                                    <div id="form-step-1" role="form" data-toggle="validator">
                                                        <div class="col-md-9">
                                                            <h2>Sede Principal</h2>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="sedeinputname">Nombre</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="sedeinputname" placeholder="Prosarc" name="SedeName" data-maxlength="128" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="sedeinputemail">Email</label><small class="help-block with-errors">*</small>
                                                            <input type="email" class="form-control" id="sedeinputemail" placeholder="Sistemas@prosarc.com" name="SedeEmail" maxlength="128" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="departamento">Departamento</label><small class="help-block with-errors">*</small>
                                                            <select class="form-control" id="departamento" name="departamento" required data-dependent="FK_SedeMun">
                                                                <option onclick="Disabled()" value="">Seleccione...</option>
                                                                @foreach ($Departamentos as $Departamento)		
                                                                    <option value="{{$Departamento->ID_Depart}}" onclick="Enabled()">{{$Departamento->DepartName}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="municipio">Municipio</label>
                                                            <select class="form-control" id="municipio" name="FK_SedeMun"  disabled>
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label for="sedeinputcelular">Celular</label><small class="help-block with-errors"></small>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">(+57)</span>
                                                                <input type="text" class="form-control mobile" id="sedeinputcelular" placeholder="301 414 5321" name="SedeCelular" data-minlength="12">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="sedeinputaddress">Dirección</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="sedeinputaddress" placeholder="cll 23 #11c-03" name="SedeAddress" minlength="5"  maxlength="128" required>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                                <label for="sedeinputphone1">Teléfono</label><small class="help-block with-errors"></small>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">(03)</span>
                                                                <input type="text" class="form-control phone" id="sedeinputphone1" placeholder="1 4123141" name="SedePhone1" data-minlength="9">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                                <label for="sedeinputext1">Extensión</label><small class="help-block with-errors"></small>
                                                            <input type="text" class="form-control extension" id="sedeinputext1" placeholder="155" name="SedeExt1" data-minlength="3" data-maxlength="5">
                                                        </div>
                                                        <div id="telefono2">
                                                        </div>
                                                        <div class="col-md-12" id="tel">
                                                            <div class="box-footer" style="display:flex; justify-content:center">
                                                                <a onclick="Tel()"class="btn btn-info">Otro Teléfono</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="step-3" class="">
                                                    <div id="form-step-2" role="form" data-toggle="validator">
                                                        <h2>Persona de Contacto</h2>
                                                        <div class="form-group col-md-6">
                                                            <label for="AreaName">Area</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="AreaName" placeholder="Nombre del Area" name="AreaName"  maxlength="128" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="CargName">Cargo</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="CargName" placeholder="Nombre del Cargo" name="CargName" maxlength="128" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="PersFirstName">Nombre</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="PersFirstName" placeholder="Nombre de la Persona" name="PersFirstName" maxlength="25" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="PersLastName">Apellido</label><small class="help-block with-errors">*</small>
                                                            <input type="text" class="form-control" id="PersLastName" placeholder="Apellido de la Persona" name="PersLastName" maxlength="64" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="PersEmail">Email</label><small class="help-block with-errors">*</small>
                                                            <input type="email" class="form-control" id="PersEmail" placeholder="Email de la Persona" name="PersEmail" maxlength="255" required>
                                                            
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="PersSecondName">Celular</label><small class="help-block with-errors"></small>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">(+57)</span>
                                                                <input type="text" class="form-control mobile" id="PersSecondName" placeholder="Numero de celular" name="PersCellphone" data-minlength="12"  maxlength="12">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input hidden value="1" name="number">
                                                    <div class="box-footer" style="float:right; margin-right:5%">
                                                        <button type="submit" class="btn btn-primary">Registrar</button>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
						<!-- /.box -->
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!--/.col (right) -->
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</div>
<script>
    function Tel(){
        var Telefono = `<div class="col-md-6 form-group">
                        <label for="sedeinputphone1">Teléfono 2</label><small class="help-block with-errors"></small>
                        <div class="input-group">
                            <span class="input-group-addon">(03)</span>
                            <input type="tel" class="form-control phone" id="sedeinputphone1" placeholder="1 4123141" name="SedePhone1" data-minlength="9"  data-maxlength="9">
                        </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="sedeinputext1">Extensión 2</label><small class="help-block with-errors"></small>
                            <input type="text" class="form-control extension" id="sedeinputext1" placeholder="155" name="SedeExt1" data-minlength="3" maxlength="5">
                        </div>`;
        $('#telefono2').append(Telefono);
        $('#telefono2').validator('update');
        $(document).ready(function() {
            $('.phone').inputmask({mask: "[9] [9][9][9][9][9][9][9]"});
            $('.extension').inputmask({mask: "[9][9][9]][9][9]"});
        });
        $('#tel').remove();
    }
    function Enabled(){
        document.getElementById("municipio").disabled = false;
    }
    function Disabled(){
        document.getElementById("FK_SedeMun").disabled = true;
    }
    function OtroType(){
        var Otro = `<label for="otroType">¿Cuál?</label><small class="help-block with-errors">*</small>
                    <input name="tipoCual" type="text" class="form-control" id="otroType" data-smaxlength="32" required>`;
        $('#otro').append(Otro);
        $('#tipo').prop('required', false);
        $('#myForm1').validator('update');
    }
    function HiddenOtroType(){
        $('#otro').empty();
        $('#myForm1').validator('update');
    }
</script>
@endsection