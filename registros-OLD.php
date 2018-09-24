<?php
	session_start();
	include_once('conexion.php');
	include("excelwriter.inc.php");
	$user=$HTTP_GET_VARS["user"];
	$_SESSION['login']=$user;
	$fechaIni=date("Y")."-".date("m")."-".date("d");
	$fechaFin=date("Y")."-".date("m")."-".date("d");	
	$conexion_bd = getConnection();
	$operacion=$HTTP_GET_VARS["operacion"];
	$where="";
	$exportar="NO";
	if($operacion=="exportar"){
		$exportar="TRUE";
		$operacion="buscar";
	}
	
	$fechaIni=$HTTP_GET_VARS["fechaIni"];
        $fechaFin=$HTTP_GET_VARS["fechaFin"];

	if($fechaIni==""||$fechaFin==""){
	       $fechaIni=date("Y")."-".date("m")."-".date("d");
	       $fechaFin=date("Y")."-".date("m")."-".date("d");
	}

	if($operacion=="buscar"){

		$campo=$HTTP_GET_VARS["campo"];
		$valorCampo=$HTTP_GET_VARS["valorCampo"];
		
		if($campo!=""){
			$where=" and a.$campo='$valorCampo' ";
		}
		if($campo=="ciudad"){
                        $where=" and b.$campo='$valorCampo' ";
                }
	}
	
	$sql="select count(*) from registros a,tecnicos b where b.identificacion=a.id_tecnico and a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where";
	//echo $sql;
	$totalRegistros="0";
	
	if($exportar!="TRUE"){
		$result = pg_query($sql);

		$rows=pg_numrows($result);
		$totalRegistros="0";
		if($rows<1){//no trajo registros la consulta
			$totalRegistros="0";
		}else{
			$totalRegistros=pg_result($result,0,0);
		}
	}

	//$sql="select a.pedido,(select nombre from tecnicos where identificacion=a.id_tecnico),a.accion,a.asesor,to_char(a.fecha,'yyyy-mm-dd hh24:mi:ss'),a.observaciones,a.id from registros a where a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where  order by a.fecha DESC";

	$sql="select a.pedido,(select nombre from tecnicos where identificacion=a.id_tecnico),a.accion,a.asesor,to_char(a.fecha,'yyyy-mm-dd hh24:mi:ss'),a.observaciones,a.id,a.duracion,a.fecha,b.ciudad from registros a,tecnicos b where b.identificacion=a.id_tecnico and a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where  order by a.fecha DESC";

	//echo $sql;
	
	if($exportar=="TRUE"){

	$sql="select a.pedido,a.id_tecnico,(select nombre from tecnicos where identificacion=a.id_tecnico),(select ciudad from tecnicos where identificacion=a.id_tecnico) as ciudad,a.empresa,a.asesor,a.observaciones,a.accion,a.tipo_pendiente,to_char(a.fecha,'yyyy-mm-dd hh24:mi:ss') as fecha2, a.proceso, a.producto,a.duracion from registros  a where a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59'";

	//echo $sql;
		$result = pg_query($sql);
		$rows=pg_numrows($result);

		
		$filename="./documentos/documento-$user.xls";

		
		$fh = fopen($filename, 'w') or die("can't open file");
		fclose($fh);

		if (file_exists($filename)) {//borro el archivo
 			unlink($filename);
		}
		$excel=new ExcelWriter("documentos/documento-$user.xls");
		$myArr=array("Proceso","Pedido","Id Tecnico","Nombre Tecnico","Ciudad","Empresa","Asesor","Observaciones","Accion","Tipo de Pendiente","Fecha","Producto","Duracion");
        	$excel->writeLine($myArr);
		
		for($i=0;$i<$rows;$i++){
			$j=$j+1;
			$click="";
			$pedido=pg_result($result,$i,0);
			$id_tecnico=pg_result($result,$i,1);
			$nombre_tecnico=pg_result($result,$i,2);
			$ciudad=pg_result($result,$i,3);
			$empresa=pg_result($result,$i,4);
			$asesor=pg_result($result,$i,5);
			$observaciones=pg_result($result,$i,6);
			$accion=pg_result($result,$i,7);
			$tipo_pendiente=pg_result($result,$i,8);
			$fecha2=pg_result($result,$i,9);
			$proceso=pg_result($result,$i,10);
			$producto=pg_result($result,$i,11);
			$duracion=pg_result($result,$i,12);
			$myArr=array($proceso,$pedido,$id_tecnico,$nombre_tecnico,$ciudad,$empresa,$asesor,$observaciones,$accion,$tipo_pendiente,$fecha2,$producto,$duracion);
			$excel->writeLine($myArr);
		}

		//echo "</table></body></html>";
		$excel->close();
		/*header('Content-type: application/msexcel');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		//readfile($filename);
		header('Location: '.$filename);*/
		echo "<script>location.href='$filename';</script>";
		return;


	}

	$result = pg_query($sql);
	$rows=pg_numrows($result);


	$msg=$HTTP_GET_VARS["msg"];
	
?>

<html>
<head>
<script language="JavaScript">
	function editar(id){
		location.href="./editar.php?id="+id+"&operacion=editarRegistro";
		return;
	}

	function exportar(){
		var fechaIni=document.getElementById("fechaIni").value;
		var fechaFin=document.getElementById("fechaFin").value;
		
		var user="<? $user=$_SESSION['login'];echo $user;?>";
		if(user==""){
			var us=prompt("Por favor ingrese su login");
			if(us==""||us=="null"||us==null){
				alert("Debe ingresar su login para continuar con la operacion");
				return;
			}
			user=us;
		}

		if(fechaIni==""||fechaFin==""){
			alert("Verifique las fechas.");
			return;
		}

		var campo=document.getElementById("campo").value;
		var valorCampo=document.getElementById("valorCampo").value;
 
		if(campo=='-1'){
			campo="";
			valorCampo="";
		}		
 
		var request="&fechaIni="+fechaIni+"&fechaFin="+fechaFin+"&campo="+campo+"&valorCampo="+valorCampo+"&user="+user;
		
		location.href="./registros.php?operacion=exportar"+request;
	}	


	function buscar(){
		var fechaIni=document.getElementById("fechaIni").value;
		var fechaFin=document.getElementById("fechaFin").value;
		
		if(fechaIni==""||fechaFin==""){
			alert("Verifique las fechas.");
			return;
		}

		var campo=document.getElementById("campo").value;
		var valorCampo=document.getElementById("valorCampo").value;
 
		if(campo=='-1'){
			campo="";
			valorCampo="";
		}		
 
		var request="&fechaIni="+fechaIni+"&fechaFin="+fechaFin+"&campo="+campo+"&valorCampo="+valorCampo;
		
		location.href="./registros.php?operacion=buscar"+request;
	}

</script>
<script language="JavaScript" src="javascript/calendar.js" type="text/javascript"></script> 
<script language="JavaScript"> 
<!--
 
addCalendar("DateIni", "calIni", "fechaIni", "forma1");
addCalendar("DateFin", "calFin", "fechaFin", "forma1");
 
//-->
</script> 
<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
</head>
<body>


<table width="95%">
<tr><td>
<div width="100%" class="bannercentral">
<IMG src="./img/logo-inicial.png" height="111" width="90%">
</div>
</td></tr>
</table>


<center><h2>Registros de Pedidos</h2></center>

<form name="forma1"> 
<table width="100%" border=0 bgcolor="#ff0000" cellspacing="2"> 
<tr> 
    <td> 
 
        <font color="white"><b>Buscar</b> </font> 
	<select id="campo" > 
		<option value="-1">Ninguno</option> 
		<option value='pedido' <? if($campo=="pedido") echo "selected"; ?> >Pedido</option>
		<option value='asesor' <? if($campo=="asesor") echo "selected"; ?> >Asesor</option>
		<option value='accion' <? if($campo=="accion") echo "selected"; ?> >Accion</option>
		<option value='piloto' <? if($campo=="piloto") echo "selected"; ?> >Piloto</option>
                <option value='ciudad' <? if($campo=="ciudad") echo "selected"; ?> >Ciudad</option>
	</select> 
        <input name="valorCampo" id="valorCampo" type="text" size="15" value="<? echo $valorCampo; ?>" style="background-color: rgb(255, 255, 160);"> 
    </td> 
 
    <td align="center"> 
		<font color="white"><b>Desde:</b></font> 
         <input type="text" name="fechaIni" id="fechaIni" value="<? echo $fechaIni; ?>" maxlength="10" size="10" style="background-color: rgb(255, 255, 160);"> 
        <span title="Click Para Abrir El Calendario"><a href="javascript:showCal('DateIni', 5, 5)" style="color: white;">(aaaa-mm-dd)</a></span> 
 
        <div id="calIni" style="position:relative; visibility: hidden;"> 
    </td> 
 
    <td align="center"> 
		<font color="white"><b>Hasta:</b></font> 
        <input type="text" name="fechaFin" id="fechaFin" value="<? echo $fechaFin; ?>" maxlength="10" size="10" style="background-color: rgb(255, 255, 160);"> 
         <span title="Click Para Abrir El Calendario"><a href="javascript:showCal('DateFin', 5, 5)"  style="color: white;">(aaaa-mm-dd)</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
        <input type="button" value="Aceptar" style="background:#f7ff1c" onclick="javascript:buscar();">
	<input type="button" value="Exportar" style="background:#f7ff1c" onclick="javascript:exportar();">  
        <div id="calFin" style="position:relative; visibility: hidden;"> 
 
    </td> 
</tr> 
</table> 
</form> 

<center><font color="red"><? echo $msg; ?></font></center>

<table width="100%" align="center">
<tr>
<td colspan="7" align="right">
<b>Total Registros: <? echo $totalRegistros;  ?></b>
</td>
</tr>
<tr bgcolor="Black">
<td align='center' width="50" ><font color="White"><b>Pedido</b></font></td>
<td align='center'><font color="White"><b>Tecnico</b></font></td>
<td align='center'><font color="White"><b>Accion</b></font></td>
<td align='center' width="60"><font color="White"><b>Asesor</b></font></td>
<td align='center'><font color="White"><b>Fecha</b></font></td>
<td align='center'><font color="White"><b>Duracion<br>(HH:MM:SS)</b></font></td>
<td align='center' width="200"><font color="White"><b>Observaciones</b></font></td>
<td align='center'><font color="White"><b>Opcion</b></font></td>
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
	$id=pg_result($result,$i,6);
	$duracion=pg_result($result,$i,7);

  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<td align='center'  width='50'>$pedido</td>";
	echo "<td align='center'>$tecnico</td>";
	echo "<td align='center'>$accion</td>";
	echo "<td align='center' width='60'>$asesor</td>";
	echo "<td align='center'>$fecha</td>";
        echo "<td align='center'>$duracion</td>";
	echo "<td align='center'  width='200'><font size='2'>$observaciones</font></td>";
	echo "<td align='center'><a href='javascript:editar(\"$id\");'>Editar</a></td>";
	echo "</tr>";

}
?>

</table>
<br>
<!--input type="button" name="Ingresar" value="Ingresar" onclick="javascript:ingresarTecnico();"-->
<center><input type="button" name="regresar" value="Regresar" onclick="javascript:location.href='./actividades.php';"></center>
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
</body>
</html>
