<?php
	session_start();
	include_once('conexion.php');
	$pedido=$HTTP_GET_VARS["pedido"];
	//$operacion=$HTTP_GET_VARS["operacion"];
	
	$conexion_bd = getConnection();
	$fechaIni=date("Y")."-".date("m")."-".date("d");
	
	$sql="select pedido,(select nombre from tecnicos where identificacion=id_tecnico),accion,asesor,to_char(fecha,'yyyy-mm-dd hh24:mi:ss'),observaciones,id from registros where fecha between '$fechaIni 00:00:00' and '$fechaIni 23:59:59' and pedido='$pedido' order by fecha DESC";
	$result = pg_query($sql);
	$rows=pg_numrows($result);
	
	
?>

<html>
<head>
</head>
<body>
<h2>Registro de Ingresos para el pedido <font color="red"> <? echo $pedido; ?></font></h2>
<table width="100%" align="center">
<tr bgcolor="Black">
<!--td align='center' ><font color="White"><b>Pedido</b></font></td-->
<td align='center'><font color="White"><b>Tecnico</b></font></td>
<td align='center'><font color="White"><b>Accion</b></font></td>
<td align='center'><font color="White"><b>Asesor</b></font></td>
<td align='center'><font color="White"><b>Fecha</b></font></td>
<td align='center'><font color="White"><b>Observaciones</b></font></td>
</tr>

<?
  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$pedido = pg_result($result,$i,0);
	$tecnico = pg_result($result,$i,1);
	$accion = pg_result($result,$i,2);
	$asesor = pg_result($result,$i,3);
	$fecha = pg_result($result,$i,4);
	$observaciones = pg_result($result,$i,5);

	if($observaciones!=""){
		$click="Click!";
	}

	$id=pg_result($result,$i,6);
  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<!--td align='center'>$pedido</td-->";
	echo "<td align='center'>$tecnico</td>";
	echo "<td align='center'>$accion</td>";
	echo "<td align='center'>$asesor</td>";
	echo "<td align='center'>$fecha</td>";
	echo "<td align='center' onclick='javascript:mostrarObservacion(\"$id\");'><div id='div$id' style='position:absolute;visibility:hidden;'>$observaciones</div>$click</td>";
	echo "</tr>";

}
?>

</table>
<br>
<!--input type="button" name="Ingresar" value="Ingresar" onclick="javascript:ingresarTecnico();"-->
<center><input type="button" name="cancelar" value="Cerrar" onclick="javascript:Modalbox.hide();return false;"></center>
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
<script language="JavaScript">
 document.getElementById("nombre_tecnico").focus();
</script>
</body>
</html>
