<?php
	session_start();
	include_once('conexion.php');
        include_once('autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);
?>

<html>
<head>
<script language="JavaScript">
	function editar(id){
		location.href="./editar.php?id="+id+"&operacion=editarRegistro";
		return;
	}

	function exportar(){

		location.href="./registros.php?operacion=exportar"+request;
	}	

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


<center><h2>Opciones Administrativas</h2></center>

<p align="center"><a href="./">Tabla Dinamica</a></p>
<p align="center"><a href="./">Llamadas por tecnico</a></p>
<p align="center"><a href="./"></a></p>
<p align="center"></p>

</body>
</html>
