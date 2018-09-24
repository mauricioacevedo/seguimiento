<?php

	//$producto=$HTTP_GET_VARS["producto"];
	
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

<td colspan='2'><b><font color='white'>ENRUTAR / PENDIENTE - REPTEX</font></b></td>
</tr>

<tr>
<td colspan='2'>Todos los datos deben ser tomados desde la boca del TAP.</td>

</tr>

<tr>
<td>CableModem es obsoleto?</td><td>
<select id='obsoleto'>
        <option value='SI'>SI</option>
        <option value='NO'>NO</option>
</select>
</td></tr>

<tr>
<td>Tipo de TAP:</td><td>
<select id="tipotap">

<option value="8X29">8X29</option>
<option value="8X26">8X26</option>
<option value="8X23">8X23</option>
<option value="8X20">8X20</option>
<option value="8X17">8X17</option>
<option value="8X14">8X14</option>
<option value="8X11">8X11</option>
<option value="4X29">4X29</option>
<option value="4X26">4X26</option>
<option value="4X23">4X23</option>
<option value="4X20">4X20</option>
<option value="4X17">4X17</option>
<option value="4X14">4X14</option>
<option value="4X11">4X11</option>
<option value="4X8">4X8</option>
<option value="2X29">2X29</option>
<option value="2X26">2X26</option>
<option value="2X23">2X23</option>
<option value="2X20">2X20</option>
<option value="2X17">2X17</option>
<option value="2X14">2X14</option>
<option value="2X11">2X11</option>
<option value="2X8">2X8</option>
<option value="2X4">2X4</option>
<option value="No identificado">No identificado</option>

</select>
</td>
</tr>

<tr><td>Parametros RF</td>
<td>
Canales bajos <input type='text' id='canalesbajos' size='5'>
&nbsp;
Canales altos <input type='text' id='canalesaltos' size='5'>
</td></tr>

<tr>
<td>MAC DME:</td><td>
<input type="text" id="macdme">
</td></tr>

<tr><td>Par&aacute;metros desde DME</td>
<td>
DP <input type='text' id='dp' size='5'>&nbsp;
DS <input type='text' id='ds' size='5'>&nbsp;
UP <input type='text' id='up' size='5'>&nbsp;
US <input type='text' id='us' size='5'>&nbsp;
</td></tr>

<tr><td>TAP en mal estado</td>
<td>
<input type='checkbox' id='tapestado'>
</td></tr>

<tr><td>Asociado a falla masiva:</td>
<td>
<input type='checkbox' id='asociadofallamasiva'>
</td></tr>
</table>
<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarObservacionReptex();">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
