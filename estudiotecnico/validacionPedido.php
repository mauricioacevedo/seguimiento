<?php
	session_start();
	include_once('../conexion.php');
	$pedido=$HTTP_GET_VARS["pedido"];
	//$operacion=$HTTP_GET_VARS["operacion"];
	
	$conexion_bd = getConnection();
	
	$sql="select pedido,cr_id,gestion,producto,transaccion,aplicativo from registros_plataformas where pedido='$pedido' order by fecha DESC";
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
<td align='center'><font color="White"><b>CR</b></font></td>
<td align='center'><font color="White"><b>Gestión</b></font></td>
<td align='center'><font color="White"><b>Producto</b></font></td>
<td align='center'><font color="White"><b>Transacción</b></font></td>
<td align='center'><font color="White"><b>aplicativo</b></font></td>
</tr>

<?
  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$pedido = pg_result($result,$i,0);
	$cr = pg_result($result,$i,1);
	$gestion = pg_result($result,$i,2);
	$producto = pg_result($result,$i,3);
	$transaccion = pg_result($result,$i,4);
	$aplicativo = pg_result($result,$i,5);

	if($observaciones!=""){
		$click="Click!";
	}

	$id=pg_result($result,$i,6);
  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<!--td align='center'>$pedido</td-->";
	echo "<td align='center'>$cr</td>";
	echo "<td align='center'>$gestion</td>";
	echo "<td align='center'>$producto</td>";
	echo "<td align='center'>$transaccion</td>";
	echo "<td align='center'>$aplicativo</td>";
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
