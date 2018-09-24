<?php
	session_start();
	//ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE);
        require_once 'Phpexcel/Classes/PHPExcel/IOFactory.php';
        require_once 'Phpexcel/Classes/PHPExcel.php';

	include_once('conexion.php');
	include_once('autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);


	//include("excelwriter.inc.php");
	$user=$HTTP_GET_VARS["user"];
	$conexion_bd = getConnection();
	$operacion=$HTTP_GET_VARS["operacion"];
	
	
	if($operacion=="eliminarAlarma"){
		$id=$HTTP_GET_VARS["id"];
		$sql="delete from alarmas where id=$id";
		$result = pg_query($sql);
		$msg="Registro Eliminado con exito.";
	}

	 if($operacion=="insertarAlarma"){
                $id=$HTTP_GET_VARS["id"];

                $nombre_alarma = $HTTP_GET_VARS["nombre_alarma"];
                $mensaje_alarma = $HTTP_GET_VARS["mensaje_alarma"];
                $ciudad = $HTTP_GET_VARS["ciudad"];
                $proceso = $HTTP_GET_VARS["proceso"];
                $producto = $HTTP_GET_VARS["producto"];
                $accion = $HTTP_GET_VARS["accion"];
                $tipo_pendiente = $HTTP_GET_VARS["tipo_pendiente"];
		$campos=$HTTP_GET_VARS["campos"];

                $sql="insert into alarmas(nombre_alarma,mensaje,ciudad,proceso,tecnologia_producto,accion,subaccion,cantidad_campos) values('$nombre_alarma','$mensaje_alarma','$ciudad','$proceso','$producto','$accion','$tipo_pendiente','$campos')";

		//echo "insert: $sql";

                $result = pg_query($sql);
                $msg="Registro Ingresado con exito.";
        }

	//$result = pg_query($sql);
	//$rows=pg_numrows($result);


	//$msg=$HTTP_GET_VARS["msg"];
	
?>

<html>
<head>
<script language="JavaScript">
	function editar(id){
		location.href="./editarAlarma.php?id="+id+"&operacion=editarRegistro";
		return;
	}

	function eliminar(id){
		if(confirm("A continuacion se eliminara la alarma con ID "+id+" Desea continuar?")){
			location.href="./listadoAlarmas.php?id="+id+"&operacion=eliminarAlarma";
			return;
		}
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

                location.href="./registros.php?operacion=buscar"+request;
                return;
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
<IMG src="./img/logo-inicial-index.png" height="111" width="90%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="./logout.php">Salir</a></font></td></tr>
</table>


<center><h2>Listado de Alarmas</h2></center>

<center><font color="red"><? echo $msg; ?></font></center>
<center><a href="./nuevaAlarma.php">Agregar Alarma</a></center>
<table width="100%" align="center">
<tr>
<td colspan="8" align="right">

<?
        //include_once('./paginacion.php');
?>

</td>
</tr>
<tr bgcolor="Black">
<td align='center'><font color="White"><b>Nombre Alarma</b></font></td>
<td align='center'><font color="White"><b>Mensaje</b></font></td>

<td align='center'><font color="White"><b>Ciudad</b></font></td>
<td align='center'><font color="White"><b>Proceso</b></font></td>
<td align='center'><font color="White"><b>Producto</b></font></td>
<td align='center'><font color="White"><b>Accion</b></font></td>
<td align='center'><font color="White"><b>Subaccion</b></font></td>


<td align='center'><font color="White"><b>Opcion</b></font></td>
</tr>

<?

$sql="select nombre_alarma, mensaje,id,ciudad,proceso,tecnologia_producto,accion,subaccion from alarmas order by id asc";

$result = pg_query($sql);
$rows=pg_numrows($result);

  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$nombre_alarma = pg_result($result,$i,0);
	$mensaje = pg_result($result,$i,1);
	$id=pg_result($result,$i,2);
	$ciudad=pg_result($result,$i,3);
	$proceso=pg_result($result,$i,4);
	$producto=pg_result($result,$i,5);
	$accion=pg_result($result,$i,6);
	$subaccion=pg_result($result,$i,7);

  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	echo "<tr bgcolor='".$bg."'>";
	echo "<td align='center'>$nombre_alarma</td>";

	echo "<td align='center'>$mensaje</td>";


	echo "<td align='center'>$ciudad</td>";
	echo "<td align='center'>$proceso</td>";
	echo "<td align='center'>$producto</td>";
	echo "<td align='center'>$accion</td>";
	echo "<td align='center'>$subaccion</td>";

	echo "<td align='center'><a href='javascript:eliminar(\"$id\");'>Eliminar</a></td>";
	echo "</tr>";

}
?>

</table>
<br>
<!--input type="button" name="Ingresar" value="Ingresar" onclick="javascript:ingresarTecnico();"-->
<center><input type="button" name="regresar" value="Regresar" onclick="javascript:location.href='./actividades.php';"></center>
<!--a href="#" title="close" onclick="javascript:Modalbox.hide();return false;">close me</a>
<a href="#" title="close" onclick="javascript:hi();">Hi man!!!!</a-->
<script language="JavaScript">
 document.getElementById("nombre_tecnico").focus();
</script>
</body>
</html>
