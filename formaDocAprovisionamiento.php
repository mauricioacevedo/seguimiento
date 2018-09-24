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
<table align="center">
<tr bgcolor="red">

<td colspan='2'><b><font color='white'>APROVISIONAMIENTO <? if(strpos($producto,"HFC")!== false) echo "EQUIPOS"; else echo "EQUIPOS"; ?></font></b></td>
</tr>

<tr>
<td>Técnico uso movilidad?</td><td>
<select id="movilidad" onchange="javascript:comboAprovisionamiento();">
	<option value="-1">Seleccione:</option>
	<option value="SI">SI</option>
	<option value="NO">NO</option>
</select>
</td>
</tr>
<!--div id='divaprov' style="position: absolute;visibility=hidden;"-->
<tr>
<td>Razon</td><td>
<select id="razonAprovisionamiento">
</select>
</td>
</tr>
<!--/div-->

<tr>
<td>Equipo</td><td>
<input type="text" id="equipo">
</td></tr>


<?
	$tipoDatoEquipo="Serial";
	if(strpos($producto,"HFC")!== false){
	}else{//asumo que es adsl
		echo "<tr><td>Configuración de acceso </td>";
		echo "<td><input type='checkbox' id='configuracion_acceso'></td></tr>";
	}

?>

<tr>
<td>Realizo Configuración terminal</td><td>
<input type="checkbox" id="realizo_configuracion">
</td></tr>

</table>

<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarObservacionAprovisionamiento('<? echo $producto; ?>');">
<!--input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;"-->
</center>
</body>
</html>
