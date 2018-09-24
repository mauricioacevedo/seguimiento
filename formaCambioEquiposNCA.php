<?php

	$producto=$HTTP_GET_VARS["producto"];
	
?>


<html>
<head>
<script language='javascript'>
	var producto=document.getElementById('producto');
        producto=producto.value;
</script>
</head>
<body>
<table>
<tr>
<td>CUENTA DOMICILIARIA <input id="cuentadomiciliaria"></td>
<td>ID CUENTA <input id="idcuenta"></td>
<td> Tipo de Servicios: 
<select id='selectTipoServicio'>
	<option value='BA'>BA</option>
	<option value='TOIP'>TOIP</option>
	<option value='BA Y TOIP'>BA Y TOIP</option>
	<option value='TV DIG'>TV DIG</option>
</select>
</td>
</tr>


<tr>
<td>Serial Entra <br> <input id="serialentra"></td>
<td>Mac Datos Entra <br> <input id="macdatosentra"></td>
<td>Mac Voz Entra <br> <input id="macvozentra"></td>
<td>Tipo Equipo Entra <br> <select id='tipoequipoentra'>
<option value='CABLE_MODEM'>CABLE_MODEM</option>
<option value='MTA'>MTA</option>
<option value='ATA'>ATA</option>
<option value='ROUTER'>ROUTER</option>
<option value='DECO'>DECO</option>
<option value='SET_TOP_BOX'>SET_TOP_BOX</option>
</select>
</td>

</tr>

<tr>
<td>Fabricante Entra<br> <select id="fabricanteentra">
<option value='AMBIT'>AMBIT</option>
<option value='ARRIS'>ARRIS</option>
<option value='CISCO'>CISCO</option>
<option value='HITRON TECHNOLOGIES'>HITRON TECHNOLOGIES</option>
<option value='MOTOROLA'>MOTOROLA</option>
<option value='SCIENTIFIC ATLANTA'>SCIENTIFIC ATLANTA</option>
<option value='THOMSON'>THOMSON</option>
<option value='TECHNICOLOR'>TECHNICOLOR</option>
<option value='DLINK'>DLINK</option>
<option value='GRANDSTREAM'>GRANDSTREAM</option>
<option value='TAINET'>TAINET</option>
<option value='COSHIP'>COSHIP</option>
<option value='GENERAL INSTRUMENTS'>GENERAL INSTRUMENTS</option>
<option value='OPENTECH'>OPENTECH</option>
<option value='SAGEM'>SAGEM</option>
<option value='SKYWORTH'>SKYWORTH</option>
<option value='AMINO'>AMINO</option>
</select>
</td>
<td>Referencia Entra<br> <input id="referenciaentra"></td>
<td>Puerto Entra<br> <input id="puertoentra"></td>

</tr>


<tr>
<td>Serial Sale <br> <input id="serialsale"></td>
<td>Mac Datos Sale <br> <input id="macdatossale"></td>
<td>Mac Voz Sale <br> <input id="macvozsale"></td>
<td>Tipo Equipo Sale <br> <select id='tipoequiposale'>
<option value='CABLE_MODEM'>CABLE_MODEM</option>
<option value='MTA'>MTA</option>
<option value='ATA'>ATA</option>
<option value='ROUTER'>ROUTER</option>
<option value='DECO'>DECO</option>
<option value='SET_TOP_BOX'>SET_TOP_BOX</option>
</select>
</td>

</tr>

<tr>
<td>Fabricante Sale<br> <select id="fabricantesale">
<option value='AMBIT'>AMBIT</option>
<option value='ARRIS'>ARRIS</option>
<option value='CISCO'>CISCO</option>
<option value='HITRON TECHNOLOGIES'>HITRON TECHNOLOGIES</option>
<option value='MOTOROLA'>MOTOROLA</option>
<option value='SCIENTIFIC ATLANTA'>SCIENTIFIC ATLANTA</option>
<option value='THOMSON'>THOMSON</option>
<option value='TECHNICOLOR'>TECHNICOLOR</option>
<option value='DLINK'>DLINK</option>
<option value='GRANDSTREAM'>GRANDSTREAM</option>
<option value='TAINET'>TAINET</option>
<option value='COSHIP'>COSHIP</option>
<option value='GENERAL INSTRUMENTS'>GENERAL INSTRUMENTS</option>
<option value='OPENTECH'>OPENTECH</option>
<option value='SAGEM'>SAGEM</option>
<option value='SKYWORTH'>SKYWORTH</option>
<option value='AMINO'>AMINO</option>
</select>
</td>
<td>Referencia Sale<br> <input id="referenciasale"></td>
<td>Puerto Sale<br> <input id="puertosale"></td>

</tr>
</table>
<br>
<table>

<!--tr>
<td>Aprovisiono Manual?</td><td>
<select id="aprovisionomanual">
        <option value="-1">Seleccione:</option>
        <option value="SI">SI</option>
        <option value="NO">NO</option>
</select>
</td>
</tr>

<tr>
<td>Técnico uso movilidad?</td><td>
<select id="movilidad">
        <option value="-1">Seleccione:</option>
        <option value="SI">SI</option>
        <option value="NO">NO</option>
</select>
</td>
</tr>
<tr>
<td>Equipo</td><td>
<input type="text" id="equipo">
</td></tr>
<tr>
<td>Realizo Configuración terminal</td><td>
<input type="checkbox" id="realizo_configuracion">
</td></tr-->

</table>

<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarObservacionCambioEquipoNCA();">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
