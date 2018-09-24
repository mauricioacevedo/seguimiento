
<html>
<head>
</head>
<body>
<table align="center">

<tr>
<td>Tiene DSAM?</td><td>
<select id="tiene_dsam">
	<option value="SI">SI</option>
	<option value="NO">NO</option>
</select>
</td>
</tr>

<tr>
<td>Usó el DSAM?</td><td>
<select id="uso_dsam">
        <option value="SI">SI</option>
        <option value="NO">NO</option>
</select>
</td>
</tr>

<tr>
<td> ID SMNET</td><td><input type="text" id="id_smnet">
</td></tr>

<tr>
<td> CH 2</td><td><input type="text" id="ch_2">
</td></tr>

<tr>
<td> CH 119</td><td><input type="text" id="ch_119">
</td></tr>

<tr>
<td> DQI</td><td><input type="text" id="dqi">
</td></tr>

<tr>
<td> BER</td><td><input type="text" id="ber">
</td></tr>

<tr>
<td> MER</td><td><input type="text" id="mer">
</td></tr>

<tr>
<td> DS SNR</td><td><input type="text" id="ds_snr">
</td></tr>

<tr>
<td> POT UP</td><td><input type="text" id="pot_up">
</td></tr>

<tr>
<td> POT DOWN</td><td><input type="text" id="pot_down">
</td></tr>

</table>

<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:copiarFormaDSAM();">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
