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
<tr bgcolor="red">

<td colspan='2'><b><font color='white'>CAMBIO <? if(strpos($producto,"HFC")!== false) echo "CABLEMODEM"; else echo "CPE"; ?></font></b></td>
</tr>

<tr>
<td colspan='2'>El objetivo de esta tarea es validar que descartes ha realizado el técnico antes de cambiar el equipo con las mismas características. El cambio del equipo debería ser el último recurso.</td>
</tr>


<tr>
<td>Motivo del cambio</td><td>
<select id="motivo">
	<option value="Modem quemado">Modem quemado</option>
	<option value="Problemas en la fuente">Problemas en la fuente</option>
	<option value="Problemas WIFI">Problemas WIFI</option>
	<option value="Equipo Obsoleto">Equipo Obsoleto</option>
	<option value="Se reinicia solo">Se reinicia solo</option>
	<option value="Robo Equipo">Robo Equipo</option>
	<option value="Puertos en mal estado">Puertos en mal estado</option>
	<option value="Equipo en Mal estado">Equipo en Mal estado</option>
</select>
</td>
</tr>

<?
	$tipoDatoEquipo="Serial";
	if(strpos($producto,"HFC")!== false){
		echo "<tr><td>Parametros CM con equipo NUEVO</td>";
		echo "<td>";
		echo "DP <input type='text' id='dp' size='5'>&nbsp;";
		echo "DS <input type='text' id='ds' size='5'>&nbsp;";
		echo "UP <input type='text' id='up' size='5'>&nbsp;";
		echo "US <input type='text' id='us' size='5'>&nbsp;";
		echo "</td></tr>";
		$tipoDatoEquipo="MAC";
	}else{//asumo que es adsl
		echo "<tr><td>ID Prueba SMPrO con equipo NUEVO </td>";
		echo "<td><input type='text' id='id_prueba_smpro'></td></tr>";
		$tipoDatoEquipo="Serial";
	}

?>

<tr>
<td>Verifico estado de la fuente?</td><td>
<select id='estado_fuente'>
	<option value='SI'>SI</option>
	<option value='NO'>NO</option>
</select>
</td></tr>

<tr>
<td>Cambio Infraestructura?</td><td>
<select id='cambio_infraestructura'>
        <option value='SI'>SI</option>
        <option value='NO'>NO</option>
</select>
</td></tr>

<tr>
<td><? echo $tipoDatoEquipo; ?> Equipo Saliente</td><td>
<input type="text" id="saliente">
</td></tr>

<tr>
<td><? echo $tipoDatoEquipo; ?> Equipo NUEVO</td><td>
<input type="text" id="entrante">
</td></tr>

<tr>
<td>El equipo saliente tenía el firmware actualizado?</td><td>
<select id='fw_actualizado'>
        <option value='SI'>SI</option>
        <option value='NO'>NO</option>
</select>
</td></tr>

<!--tr>
<td colspan="2">
<div id="divIngresoEmpresa" style="position:absolute;visibility:hidden;">
Empresa:<input type="text" id="nueva_empresa"><input type="button" name="nueva" value="Ingresar" onclick="javascript:ingresarEmpresa();">
</div>
</td>
</tr-->
</table>

<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarObservacion('<? echo $producto; ?>');">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
