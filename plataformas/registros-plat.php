<?php
	session_start();
	//ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE);
        require_once '../Phpexcel/Classes/PHPExcel/IOFactory.php';
        require_once '../Phpexcel/Classes/PHPExcel.php';

	include_once('../conexion.php');
	include_once('../autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);


	include("excelwriter.inc.php");
	$user=$HTTP_GET_VARS["user"];
	//$_SESSION['login']=$user;
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

	//$exportar="TRUE";
	//$fechaIni="2013-09-17";
	//$fechaFin="2013-09-19";
	$sql="select count(*) from registros_plataformas a where a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where";
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
	
	//para la paginacion
	$NumeroTotalRows=$totalRegistros;

	//se desplazo la consulta para mas abajo, por el tema de la paginacion
	//$sql="select a.pedido,b.nombre,a.accion,a.asesor,to_char(a.fecha,'yyyy-mm-dd hh24:mi:ss'),a.observaciones,a.id,a.duracion,a.fecha,b.ciudad from registros a,tecnicos b where b.identificacion=a.id_tecnico and a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where  order by a.fecha DESC";

	//echo $sql;
	
	if($exportar=="TRUE"){
	ini_set('max_execution_time', 300);
	//set_time_limit(0);
	ini_set('memory_limit', '-1');
	clearstatcache();
	$sql="select a.pedido,a.id_tecnico,(select nombre from tecnicos where identificacion=a.id_tecnico),(select ciudad from tecnicos where identificacion=a.id_tecnico) as ciudad,a.empresa,a.asesor,a.observaciones,a.accion,a.tipo_pendiente,to_char(a.fecha,'yyyy-mm-dd hh24:mi:ss') as fecha2, a.proceso, a.producto,a.duracion,a.llamada_id, a.prueba_integrada,a.codigo_familiar,a.smartplay,a.toip,a.inter,a.iptv,a.telev,a.totdm from registros  a where a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59'";
	
		//echo $sql;
		$result = pg_query($sql);
		$rows=pg_numrows($result);

		
		$filename="./documentos/documento-$user.xls";

		
		
		$fh = fopen($filename, 'w') or die("can't open file");
		fclose($fh);

		if (file_exists($filename)) {//borro el archivo
 			unlink($filename);
		}
		$phpExcel = new PHPExcel();
	        $sheet = $phpExcel->getActiveSheet();
        	$sheet->setTitle("Datos-Seguimiento-Pedidos");

	        $sheet->setCellValue("A1","Proceso");
        	$sheet->setCellValue("B1","Pedido");
	        $sheet->setCellValue("C1","Id Tecnico");
	        $sheet->setCellValue("D1","Nombre Tecnico");
	        $sheet->setCellValue("E1","Ciudad");
	        $sheet->setCellValue("F1","Empresa");
	        $sheet->setCellValue("G1","Asesor");
	        $sheet->setCellValue("H1","Observaciones");
	        $sheet->setCellValue("I1","Accion");
	        $sheet->setCellValue("J1","Tipo de Pendiente");
	        $sheet->setCellValue("K1","Fecha");
	        $sheet->setCellValue("L1","Producto");
	        $sheet->setCellValue("M1","Duracion");
        	$sheet->setCellValue("N1","ID de Llamada");
        	$sheet->setCellValue("O1","Codigo Familiar");
        	$sheet->setCellValue("P1","Prueba Integrada");
        	$sheet->setCellValue("Q1","Telefonia TDM");
        	$sheet->setCellValue("R1","Television HFC");
        	$sheet->setCellValue("S1","Television Interactiva");
        	$sheet->setCellValue("T1","Internet Banda Ancha");
        	$sheet->setCellValue("U1","Telefonia IP");
        	$sheet->setCellValue("V1","Servicios y Equipos");

		//$excel=new ExcelWriter("documentos/documento-$user.xls");
		//$myArr=array("Proceso","Pedido","Id Tecnico","Nombre Tecnico","Ciudad","Empresa","Asesor","Observaciones","Accion","Tipo de Pendiente","Fecha","Producto","Duracion","ID de Llamada");
        	//$excel->writeLine($myArr);
		
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
			$llamada_id=pg_result($result,$i,13);
			$pruebaintegrada=pg_result($result,$i,14);
			$codigofamiliar=pg_result($result,$i,15);
			$smartplay=pg_result($result,$i,16);
			$toip=pg_result($result,$i,17);
			$inter=pg_result($result,$i,18);
			$iptv=pg_result($result,$i,19);
			$telev=pg_result($result,$i,20);
			$totdm=pg_result($result,$i,21);

			$observaciones=str_replace(";",",",$observaciones);

			$sheet->setCellValue("A".($i+2),utf8_encode($proceso));
			$sheet->setCellValue("B".($i+2),utf8_encode($pedido));
			$sheet->setCellValue("C".($i+2),utf8_encode($id_tecnico));
			$sheet->setCellValue("D".($i+2),utf8_encode($nombre_tecnico));
			$sheet->setCellValue("E".($i+2),utf8_encode($ciudad));
			$sheet->setCellValue("F".($i+2),utf8_encode($empresa));
			$sheet->setCellValue("G".($i+2),utf8_encode($asesor));
			$sheet->setCellValue("H".($i+2),utf8_encode($observaciones));
			$sheet->setCellValue("I".($i+2),utf8_encode($accion));
			$sheet->setCellValue("J".($i+2),utf8_encode($tipo_pendiente));
			$sheet->setCellValue("K".($i+2),utf8_encode($fecha2));
			$sheet->setCellValue("L".($i+2),utf8_encode($producto));
			$sheet->setCellValue("M".($i+2),utf8_encode($duracion));

			$sheet->setCellValue("N".($i+2),utf8_encode('\''.$llamada_id));
			$sheet->getStyle("M".($i+2))->getNumberFormat()->setFormatCode("hh:mm");

			$sheet->setCellValue("O".($i+2),utf8_encode($codigofamiliar));
			$sheet->setCellValue("P".($i+2),utf8_encode($pruebaintegrada));
			$sheet->setCellValue("Q".($i+2),utf8_encode($totdm));
			$sheet->setCellValue("R".($i+2),utf8_encode($telev));
			$sheet->setCellValue("S".($i+2),utf8_encode($iptv));
			$sheet->setCellValue("T".($i+2),utf8_encode($inter));
			$sheet->setCellValue("U".($i+2),utf8_encode($toip));
			$sheet->setCellValue("V".($i+2),utf8_encode($smartplay));

		}
		echo "TERMINO CICLO";
	        //$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
		$objWriter = new PHPExcel_Writer_CSV($phpExcel);
		$objWriter->setDelimiter(';');
		$objWriter->setEnclosure('');
		$objWriter->setLineEnding("\r\n");
		$objWriter->setSheetIndex(0);
		
        	$objWriter->save($filename);
		
		echo "<script language='javascript'>location.href='$filename';</script>";
		//echo "<script>location.href='$filename';</script>";
		return;

	}

	//$result = pg_query($sql);
	//$rows=pg_numrows($result);


	$msg=$HTTP_GET_VARS["msg"];
	
?>

<html>
<head>
<script language="JavaScript">
	function editar(id){
		location.href="./editar-plat.php?id="+id+"&operacion=editarRegistro";
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
		
		var finisplit=fechaIni.split("-");
		var ffinsplit=fechaFin.split("-");
		

		var fini=new Date(finisplit[0],finisplit[1]-1,finisplit[2]);
		var ffin=new Date(ffinsplit[0],ffinsplit[1]-1,ffinsplit[2]);

		var days=ffin - fini;
		days=Math.round(days/1000/60/60/24);
		
		if(days<0){
			alert("La fecha inicial es mayor a la fecha final, por favor revise las fechas.");
			return;
		}

		if(days > 7){// este es el limite de dias..
			alert("Solo se pueden exportar datos de maximo 7 dias");
                        return;
		}

		var campo=document.getElementById("campo").value;
		var valorCampo=document.getElementById("valorCampo").value;
 
		if(campo=='-1'){
			campo="";
			valorCampo="";
		}		
 
		var request="&fechaIni="+fechaIni+"&fechaFin="+fechaFin+"&campo="+campo+"&valorCampo="+valorCampo+"&user="+user;
		location.href="./registros-plat.php?operacion=exportar"+request;
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
		
		location.href="./registros-plat.php?operacion=buscar"+request;
	}

	function irAPagina(pagina,regXpag) {

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
		
		//var request="&fechaIni="+fechaIni+"&fechaFin="+fechaFin+"&campo="+campo+"&valorCampo="+valorCampo;
                var request="&fechaIni="+fechaIni+"&fechaFin="+fechaFin+"&campo="+campo+"&valorCampo="+valorCampo+"&pagina="+pagina+"&txtRegistrosPagina="+regXpag;

                location.href="./registros-plat.php?operacion=buscar"+request;
                return;
        }


</script>
<script language="JavaScript" src="../javascript/calendar.js" type="text/javascript"></script> 
<script language="JavaScript"> 
<!--
 
addCalendar("DateIni", "calIni", "fechaIni", "forma1");
addCalendar("DateFin", "calFin", "fechaFin", "forma1");
 
//-->
</script> 
<link rel="stylesheet" href="../javascript/actividades.css" type="text/css" />
</head>
<body>


<table width="95%">
<tr><td>
<div width="100%" class="bannercentral">
<IMG src="../img/logo-plata.png" height="130" width="80%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="./logout.php">Salir</a></font></td></tr>
</table>


<center><h2>Registros de Pedidos</h2></center>

<form name="forma1"> 
<table width="100%" border=0 bgcolor="#aaaaaa" cellspacing="2"> 
<tr> 
    <td> 
 
        <font color="white"><b>Buscar</b> </font> 
	<select id="campo" > 
		<option value="-1">Ninguno</option> 
		<option value='pedido' <? if($campo=="pedido") echo "selected"; ?> >Pedido</option>
		<option value='asesor' <? if($campo=="asesor") echo "selected"; ?> >Asesor</option>
		<option value='gestion' <? if($campo=="gestion") echo "selected"; ?> >Gestión</option>
		<option value='transaccion' <? if($campo=="transaccion") echo "selected"; ?> >Transacción</option>
		<option value='aplicativo' <? if($campo=="aplicativo") echo "selected"; ?> >Aplicativo</option>
		<option value='cr_id' <? if($campo=="cr_id") echo "selected"; ?> >Numero de CR</option>
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
        <input type="button" class="btnpurple" value="Aceptar" onclick="javascript:buscar();">
	<!--input type="button" class="btnpurple" value="Exportar" onclick="javascript:exportar();"-->  
        <div id="calFin" style="position:relative; visibility: hidden;"> 
 
    </td> 
</tr> 
</table> 
</form> 

<center><font color="red"><? echo $msg; ?></font></center>

<table width="100%" align="center">
<tr>
<td colspan="8" align="right">

<?
        include_once('../paginacion.php');
?>

</td>
</tr>
<tr bgcolor="Black">
<td align='center' width="50" ><font color="White"><b>Pedido</b></font></td>
<td align='center'><font color="White"><b>Gestion</b></font></td>
<td align='center'><font color="White"><b>Aplicativo</b></font></td>
<td align='center' width="200"><font color="White"><b>Tarea</b></font></td>
<td align='center'><font color="White"><b>Asesor</b></font></td>
<td align='center'><font color="White"><b>Duracion<br>(HH:MM:SS)</b></font></td>
<td align='center'><font color="White"><b>Resultado Gestion</b></font></td>
<td align='center'><font color="White"><b>Numero de CR</b></font></td>
<td align='center'><font color="White"><b>Observaciones</b></font></td>
<td align='center'><font color="White"><b>Opcion</b></font></td>
</tr>

<?

$sql="select a.pedido,a.gestion,a.aplicativo,to_char(a.fecha::timestamp,'yyyy-mm-dd hh24:mi:ss'),a.observaciones,a.id,a.duracion,a.fecha,a.asesor,a.tarea,a.resultadogestion,a.cr_id from registros_plataformas a where  a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' $where  order by a.fecha DESC limit $registrosPagina offset $offset";

$result = pg_query($sql);
//echo $sql;
$rows=pg_numrows($result);

  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$pedido = pg_result($result,$i,0);
	$gestion = pg_result($result,$i,1);
	$aplicativo = pg_result($result,$i,2);
	$fecha = pg_result($result,$i,3);
	$observaciones = pg_result($result,$i,4);
	$id = pg_result($result,$i,5);
	$duracion=pg_result($result,$i,6);
	$fecha_inicio=pg_result($result,$i,7);
	$asesor=pg_result($result,$i,8);
	$tarea =pg_result($result,$i,9);	
	$resultadogestion = pg_result($result,$i,10);
	$cr_id = pg_result($result,$i,11);

	//$duracion=$duracion / 60;	

  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<td align='center'  width='50'>$pedido</td>";
	echo "<td align='center'>$gestion</td>";
	echo "<td align='center'>$aplicativo</td>";
	echo "<td align='center' width='60'>$tarea</td>";
	echo "<td align='center'>$asesor</td>";
	echo "<td align='center'>$duracion</td>";
	echo "<td align='center'>$resultadogestion</td>";
	echo "<td align='center'>$cr_id</td>";
	echo "<td align='center' width='200'><font size='2'>$observaciones</font></td>";
	echo "<td align='center'><a href='javascript:editar(\"$id\");'>Editar</a></td>";
	echo "</tr>";
}
?>

</table>
<br>
<!--input type="button" name="Ingresar" value="Ingresar" onclick="javascript:ingresarTecnico();"-->
<center><input type="button" name="regresar" value="Regresar" onclick="javascript:location.href='./gestion.php';"></center>
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
<script language="JavaScript">
 document.getElementById("nombre_tecnico").focus();
</script>
</body>
</html>
