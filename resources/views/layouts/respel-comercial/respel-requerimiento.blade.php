<div id="requerimiento`+contador+`Container" class="panel panel-default" style="display: inline-block; overflow: hidden; width:100%; background-color:#FAFAFF;">
		<div style="padding: 0.25em; background-color: #222d32; color: #b8c7ce" class="panel-heading">
		  <h5 class="panel-title">Tratamiento:<b style="color: #E8E8E8" id="pretratamiento`+contador+`TratName"> Seleccione un Tratamiento...</b>{{-- 	<small>Subtext for header</small> --}}</h5>
		</div>
		<div style="margin-top: 0.25em;">
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Foto-Descargue</b>" data-content="<p> Se requiere registro fotografico del proceso de descargue de los residuos en las instalaciones de Prosarc S.A. ESP</p>"> Foto Descargue
				<input value="1" type="checkbox" class="fotoswitch" name="Opcion[`+contador+`][ReqFotoDescargue]"/>
				</label>
			</div>
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Foto-Tratamiento</b>" data-content="<p> Se requiere registro fotografico del Tratamiento de los residuos en las instalaciones de Prosarc S.A. ESP</p>">  Foto Tratamiento
				<input value="1" type="checkbox" class="fotoswitch" name="Opcion[`+contador+`][ReqFotoDestruccion]"/>
				</label>
			</div>
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Video-Descargue</b>" data-content="<p> Se requiere video del proceso de Descargue de los residuos en las instalaciones de Prosarc S.A. ESP</p>"> Video Descargue
				<input value="1" type="checkbox" class="videoswitch" name="Opcion[`+contador+`][ReqVideoDescargue]"/>
				</label>
			</div>
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Video-Tratamiento</b>" data-content="<p> Se requiere registro fotografico del Tratamiento de los residuos en las instalaciones de Prosarc S.A. ESP</p>"> Video Tratamiento
				<input value="1" type="checkbox" class="videoswitch" name="Opcion[`+contador+`][ReqVideoDestruccion]"/>
				</label>
			</div>
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Devolución de embalaje</b>" data-content="<p> Se requiere que los embalajes sean devueltos al Cliente/Generador</p>"> Devolver embalaje
				<input value="1" type="checkbox" class="embalajeswitch" name="Opcion[`+contador+`][ReqDevolucion]"/>
				</label>
			</div>
			<div class="col-md-2 col-xs-6">
				<label data-trigger="hover" data-toggle="popover" title="<b>Tratamiento Auditable</b>" data-content="<p> Se requiere que el tratamiento del residuo sea auditado por personal del Cliente/Generador</p>"> Tratamiento Auditable
				<input value="1" type="checkbox" class="auditoriaswitch" name="Opcion[`+contador+`][ReqAuditoria]"/>
				</label>
			</div>
		</div>
</div>
