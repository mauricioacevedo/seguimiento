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
<tr bgcolor="red" align='center'>

<td colspan='2'><b><font color='white'>CIERRE POR CONTINGENCIA</font></b></td>
</tr>

<tr>
<td colspan='2'>Por favor llenar el formato de acuerdo a los productos solo para los casos que es necesario cumplir por contingencia.</td>
</tr>
</table>


<table align='center'>
<tr>
<td colspan='2'><b>Aprovisionado por GTC o Contingencia</b></td>
<td align="center"><b>MAC</b></td>
</tr>


<tr>
<td>Internet</td><td>
<select id="internetSelect">
	<option value="vacio">Vacio</option>
	<option value="GTC">GTC</option>
	<option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="internetMAC"></td>
</tr>

<tr>
<td>ToIP</td><td>
<select id="toipSelect">
        <option value="vacio">Vacio</option>
        <option value="GTC">GTC</option>
        <option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="toipMAC"></td>
</tr>

<tr>
<td>TV Digital (1)</td><td>
<select id="tv1Select">
        <option value="vacio">Vacio</option>
        <option value="GTC">GTC</option>
        <option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="tv1MAC"></td>
</tr>

<tr>
<td>TV Digital (2)</td><td>
<select id="tv2Select">
        <option value="vacio">Vacio</option>
        <option value="GTC">GTC</option>
        <option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="tv2MAC"></td>
</tr>

<tr>
<td>TV Digital (3)</td><td>
<select id="tv3Select">
        <option value="vacio">Vacio</option>
        <option value="GTC">GTC</option>
        <option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="tv3MAC"></td>
</tr>

<tr>
<td>TV Digital (4)</td><td>
<select id="tv4Select">
        <option value="vacio">Vacio</option>
        <option value="GTC">GTC</option>
        <option value="Contingencia">Contingencia</option>
</select>
</td>
<td><input type="text" id="tv4MAC"></td>
</tr>

<tr>
<td>Queda Pendiente: </td>
<td colspan='2'><textarea cols="45" rows="4" id="quedaPendiente"></textarea> </td>
</tr>
</table>

<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarObservacionAprovNCA();">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
