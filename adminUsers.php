<?php
	session_start();
	include_once('conexion.php');
	include_once('autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);


	include("excelwriter.inc.php");
	$user=$HTTP_GET_VARS["user"];
	//$_SESSION['login']=$user;
	$conexion_bd = getConnection();
	$operacion=$HTTP_GET_VARS["operacion"];
	$where="";
	$exportar="NO";
	if($operacion=="exportar"){
		$exportar="TRUE";
		$operacion="buscar";
	}
	
	if($operacion=="buscar"){

		$campo=$HTTP_GET_VARS["campo"];
		$valorCampo=$HTTP_GET_VARS["valorCampo"];
		
		if($campo!=""){
			$where=" where a.$campo='$valorCampo' ";
		}
	}
	
	$sql="select count(*) from usuarios a $where";
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

	$msg=$HTTP_GET_VARS["msg"];
	
?>

<html>
<head>
<script language="JavaScript">
	function editar(id){
		location.href="./manageUser.php?id="+id+"&operacion=editUser";
		return;
	}

	function eliminar(id,nombre){
		if(confirm("Se borrara el usuario "+nombre+"\nDesea continuar? ")){
	                location.href="./manageUser.php?id="+id+"&operacion=deleteUser";
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

		var campo=document.getElementById("campo").value;
		var valorCampo=document.getElementById("valorCampo").value;
 
		if(campo=='-1'){
			campo="";
			valorCampo="";
		}		
 
		var request="&campo="+campo+"&valorCampo="+valorCampo;
		
		location.href="./adminUsers.php?operacion=buscar"+request;
	}

	function irAPagina(pagina,regXpag) {

                var campo=document.getElementById("campo").value;
                var valorCampo=document.getElementById("valorCampo").value;

                if(campo=='-1'){
                        campo="";
                        valorCampo="";
                }
		
                var request="&campo="+campo+"&valorCampo="+valorCampo+"&pagina="+pagina+"&txtRegistrosPagina="+regXpag;

                location.href="./adminUsers.php?operacion=buscar"+request;
                return;
        }


</script>
<script language="JavaScript" src="javascript/calendar.js" type="text/javascript"></script> 
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


<center><h2>Usuarios</h2></center>

<form name="forma1"> 
<table width="100%" border=0 bgcolor="#ff0000" cellspacing="2"> 
<tr> 
    <td> 
 
        <font color="white"><b>Buscar</b> </font> 
	<select id="campo" > 
		<option value="-1">Ninguno</option> 
		<option value='nombre' <? if($campo=="nombre") echo "selected"; ?> >Nombre</option>
		<option value='username' <? if($campo=="username") echo "selected"; ?> >login</option>
		<!--option value='perfil' <? if($campo=="perfil") echo "selected"; ?> >Perfil</option-->
	</select> 
        <input name="valorCampo" id="valorCampo" type="text" size="15" value="<? echo $valorCampo; ?>" style="background-color: rgb(255, 255, 160);">
	<input type="button" value="Aceptar" style="background:#f7ff1c" onclick="javascript:buscar();"> 
    </td>
</tr> 
</table> 
</form> 

<center><font color="red"><? echo $msg; ?></font></center>

<table width="100%" align="center">
<tr>
<td colspan="8" align="right">

<?
        include_once('./paginacion.php');
?>

</td>
</tr>
<tr>
<td colspan="8" align="center">
<a href="./manageUser.php">Agregar Usuario</a>
</td>
</tr>

<tr bgcolor="Black">
<td align='center' width="400" ><font color="White"><b>Nombre</b></font></td>
<td align='center'><font color="White"><b>Identificacion</b></font></td>
<td align='center'><font color="White"><b>Login</b></font></td>
<td align='center' width="150"><font color="White"><b>Perfil</b></font></td>
<td align='center'><font color="White"><b>Opcion</b></font></td>
</tr>

<?

$sql="select a.nombre,a.identificacion,a.username,a.perfil,a.id from usuarios a $where order by nombre ASC limit $registrosPagina offset $offset";

//echo $sql;
$result = pg_query($sql);
$rows=pg_numrows($result);

  $j=1;
  $bg="#CCCCCC";

for($i=0;$i<$rows;$i++){
	$j=$j+1;
	$click="";
	$nombre = utf8_encode(pg_result($result,$i,0));
	$identificacion = pg_result($result,$i,1);
	$login = pg_result($result,$i,2);
	$perfil = pg_result($result,$i,3);
	$id = pg_result($result,$i,4);

	//$duracion=$duracion / 60;	

  	if( $j % 2 == 0 ){ $bg="#EFEFEF";}
	else { $bg="#FFFFFF";}

	if($perfil=="1")$perfil="Administrador";
	if($perfil=="2")$perfil="Asesor Mesa";
	if($perfil=="3")$perfil="Consulta";
	if($perfil=="4")$perfil="Asignaciones";

	echo "<tr bgcolor='".$bg."'>";
	echo "<td align='center'  width='50'>$nombre</td>";
	echo "<td align='center'>$identificacion</td>";
	echo "<td align='center'>$login</td>";
	echo "<td align='center' width='60'>$perfil</td>";
	echo "<td align='center'><a href='javascript:editar(\"$id\");'>Editar</a> <br> <a href='javascript:eliminar(\"$id\",\"$nombre\");'>Eliminar</a></td>";
	echo "</tr>";

}
?>

</table>
<br>
</body>
</html>
