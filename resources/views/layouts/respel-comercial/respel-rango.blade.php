<div class="col-md-3" id="rango`+opcion+``+last+`">
	<label style="font-size: 0.8em;">Desde `+rango+`</label><a onclick="EliminarRango(`+opcion+`,`+last+`)" id="minusrangeButton`+contadorRango[contador]+`"><i style="color:red; margin: 0; padding: 0; margin-top: 0.25em; cursor: pointer;" class="fa fa-trash-alt pull-right"></i></a>
	<input name="Tratamiento[TarifasPrecio[`+contador+`][]]" type="text" class="form-control">
	<input name="Tratamiento[TarifasDesde[`+contador+`][]]" hidden value="`+rango+`">
</div>