<?php
	session_start();
	include_once('../conexion.php');
	$pedido=$HTTP_GET_VARS["pedido"];
	//$operacion=$HTTP_GET_VARS["operacion"];
	
	$conexion_bd = getConnection();
	
	$sql="select pedido,cr_id,estado,estado_final,observaciones,to_char(fecha_ultima_actualizacion::timestamp,'yyyy-mm-dd hh24:mi:ss') from registros_estudio where pedido='$pedido' order by fecha_ultima_actualizacion DESC";

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
<td align='center'><font color="White"><b>CR</b></font></td>
<td align='center'><font color="White"><b>Estado</b></font></td>
<td align='center'><font color="White"><b>Estado Final</b></font></td>
<td align='center'><font color="White"><b>Fecha Ultima Actualizacion</b></font></td>
<td align='center'><font color="White"><b>Observaciones</b></font></td>
</tr>

<?
  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$pedido = pg_result($result,$i,0);
	$cr = pg_result($result,$i,1);
	$estado = pg_result($result,$i,2);
	$estado_final = pg_result($result,$i,3);
	$observaciones = pg_result($result,$i,4);
	$fecha_ultima = pg_result($result,$i,5);
	

	if($observaciones!=""){
		$click="Click!";
	}

	$id=pg_result($result,$i,6);
  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<!--td align='center'>$pedido</td-->";
	echo "<td align='center'>$cr</td>";
	echo "<td align='center'>$estado</td>";
	echo "<td align='center'>$estado_final</td>";
	echo "<td align='center'>$fecha_ultima</td>";
	echo "<td align='center'>$observaciones</td>";
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
