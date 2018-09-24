<?php

	$msg=$_GET["msg"];
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->

<head>
<title>::Aplicación de Seguimiento a Pedidos::</title>
<meta name="description" content="OHMY!  HTML5 website ready!!!">
<meta name="author" content="LoganC.">
<script language='javascript'>

function login(){
	var usuario=document.getElementById("usuario").value;
	var contrasena=document.getElementById("contrasena").value;
	
	contrasena=contrasena.replace("+","%2B");
	
	var url="./login.php?user="+usuario+"&pwd="+contrasena;
        location.href=url;

}

</script>
<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
</head>
<body>
<br>
<div width="100%" class="bannercentral">
<IMG src="./img/bbanner.png" height="130" width="80%">
</div>

<br>
<br>
<br>
<br>

<center><h2>Aplicacion de registro y seguimiento a pedidos.</h2></center>

<table align='center' width='350' height='300'><tr><td>
<div class="logindiv">
<form action="javascript:login();">
<table align='center'>
<tr>
<td>&nbsp;</td><td>&nbsp;</td>
</tr>
<tr>
<td><h3>Ingreso:</h3></td><td>&nbsp;</td>
</tr>

<tr>
<td><b>Usuario:</b></td><td><input type='text' name='usuario' id='usuario' size='15'></td>
</tr>
<tr>
<td><b>Contraseña:</b></td><td><input type='password' name='contrasena' id='contrasena' size='15'></td>
</tr>
<tr>
<td colspan="2" align='center'><input type='button' value='Entrar' onclick='javascript:login();' style="background:#FFF"></td>
</tr>
<tr>
<td>&nbsp;</td><td>&nbsp;</td>
</tr>

</div>
</table>

</form>
</td></tr></table>
<br><br><center><b><font color="red"><? echo $msg; ?></font></b></center>


	<div class="footer" align="right" style="">
           <h3 style="background:#DDD;"><font color="red">UNE</font>&nbsp;&nbsp;Subdirección Soporte Técnico del Servicio (2014)</h3>
        </div>
</body>
</html>
