<?php
	session_start();
	include_once('../conexion.php');

	$cr_id=$HTTP_GET_VARS["cr_id"];
	$pedido=$HTTP_GET_VARS["pedido"];
	
	$fechaIni=date("Y")."-".date("m")."-".date("d");
	//echo "<br><br>$cr_id - $pedido";
	if($cr_id!=''&&$pedido!=''){
		$conexion_bd = getConnection();
		$sql="select fecha_inicio,fecha_final from crxpedido where cr_id='$cr_id' and pedido='$pedido'";

		$result = pg_query($sql);
	        $rows=pg_numrows($result);
		echo $rows;
		if($rows>0){
			$fechaIni = pg_result($result,0,0);
			$fechaFin = pg_result($result,0,1);
		}
	}
	
?>


<html>
<head>
<script language='javascript'>
	var producto=document.getElementById('producto');
        producto=producto.value;
</script>
<link rel="stylesheet" href="../javascript/actividades.css" type="text/css" />
</head>
<body>
<br>
<h2>Asociar CR a Pedido: <? echo $pedido; ?></h2>
<form name="forma1">
<table>
<tr>
<td>Numero de CR:</td><td><input type='text' id='numerocr' size="10" value="<? echo $cr_id; ?>"></td>
</tr>


<tr>
<td>Fecha Inicial:</td>
<td>
<input type="text" name="fechaIni" id="fechaIni" value="<? echo $fechaIni; ?>" maxlength="10" size="10" style="background-color: rgb(255, 255, 160);">

</td>
</tr>

<tr>
<td>Fecha Respuesta:</td>
<td>
<input type="text" name="fechaFin" id="fechaFin" value="<? echo $fechaFin; ?>" maxlength="10" size="10" style="background-color: rgb(255, 255, 160);">
</td>
</tr>

</table>
<center>
<input type="button" name="Ingresar" value="Copiar" onclick="javascript:guardarCR();">
<input type="button" name="cancelar" value="Cancelar" onclick="javascript:Modalbox.hide();return false;">
</center>
</body>
</html>
