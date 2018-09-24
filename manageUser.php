<?php
        session_start();
        include_once('conexion.php');
        include_once('autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);

	$operacion=$_GET["operacion"];

	if($operacion=="insertUser"){
		$nombre=$_GET["nombre"];
		$identificacion=$_GET["identificacion"];
		$login=$_GET["login"];
		$password=$_GET["password"];
		$perfil=$_GET["perfil"];

		$sql="insert into usuarios(nombre,identificacion,username,password,perfil) values('$nombre','$identificacion','$login','$password',$perfil)";
		$result = pg_query($sql);
	        //$rows=pg_numrows($result);
		
		$msg="Usuario ingresado exitosamente!";

                echo "<script language='javascript'>";
                echo "location.href='./adminUsers.php?msg=$msg'";
                echo "</script>";
		return;
	}
        if($operacion=="editUser"){
		$id=$_GET["id"];

		$sql="select nombre,identificacion,username,password,perfil from usuarios where id=$id";

		$result = pg_query($sql);
		$rows=pg_numrows($result);
		
		if($rows>0){
			$nombre=pg_result($result,0,0);
	                $identificacion=pg_result($result,0,1);
        	        $login=pg_result($result,0,2);
                	$password=pg_result($result,0,3);
                	$perfil=pg_result($result,0,4);
		}
		
	}

	if($operacion=="deleteUser"){
                $id=$_GET["id"];

                $sql="delete from usuarios where id=$id";

                $result = pg_query($sql);
		
		$msg="Se elimino el usuario con exito!";

                echo "<script language='javascript'>";
                echo "location.href='./adminUsers.php?msg=$msg'";
                echo "</script>";
                return;


        }

        if($operacion=="doEditUser"){
                $nombre=$_GET["nombre"];
                $identificacion=$_GET["identificacion"];
                $login=$_GET["login"];
                $password=$_GET["password"];
                $perfil=$_GET["perfil"];
		$id=$_GET["id"];

                $sql="update usuarios set nombre='$nombre',identificacion='$identificacion',username='$login',password='$password',perfil=$perfil where id=$id";
		//echo $sql;
		//return;
                $result = pg_query($sql);
                //$rows=pg_numrows($result);

                $msg="Usuario Actualizado exitosamente!";

                echo "<script language='javascript'>";
                echo "location.href='./adminUsers.php?msg=$msg'";
                echo "</script>";
                return;
        }

	

?>


<html>
<head>
<script language='javascript'>

	function guardar(){
		var nombre=document.getElementById("nombre").value;
		var identificacion=document.getElementById("identificacion").value;
		var login =document.getElementById("login").value;
		var password =document.getElementById("password").value;
		var perfil=document.getElementById("perfil");
		perfil=perfil.options[perfil.selectedIndex].value;
		
		var request="&nombre="+nombre+"&identificacion="+identificacion+"&login="+login+"&perfil="+perfil+"&password="+password;
	        location.href="./manageUser.php?operacion=insertUser"+request;
	}

        function actualizar(){
                var nombre=document.getElementById("nombre").value;
                var identificacion=document.getElementById("identificacion").value;
                var login =document.getElementById("login").value;
                var password =document.getElementById("password").value;
                var perfil=document.getElementById("perfil");
                perfil=perfil.options[perfil.selectedIndex].value;

                var request="&nombre="+nombre+"&identificacion="+identificacion+"&login="+login+"&perfil="+perfil+"&password="+password;
		var id="<? echo "$id";?>";
                location.href="./manageUser.php?operacion=doEditUser"+"&id="+id+request;
        }

	function cancelar(){
		location.href="./adminUsers.php";
	}


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

<center><H2>Creacion/Edicion de Usuario</h2></center>
<table align="center">
<tr>
<td align='center'><b>Variable</b></td><td align='center'><b>Valor</b></td>
</tr>
<tr>
<td>Nombre</td><td><input type="text" id="nombre" value="<? echo $nombre; ?>"></td>
</tr>
<tr>
<td>Identificacion</td><td><input type="text" id="identificacion" size='10' value="<? echo $identificacion; ?>"></td>
</tr>
<tr>
<td>Login</td><td><input type="text" id="login" size='10' value="<? echo $login; ?>"></td>
</tr>

<tr>
<td>Password</td><td><input type="password" id="password" size='10' value="<? echo $password; ?>" ></td>
</tr>

<tr>
<td>Perfil</td>
<td>
<select id="perfil">
<option value="2" <? if($perfil=="2") echo "selected";?>>Asesor Mesa</option>
<option value="3" <? if($perfil=="3") echo "selected";?>>Consulta</option>
<option value="4" <? if($perfil=="4") echo "selected";?>>Asignaciones</option>
<option value="1" <? if($perfil=="1") echo "selected";?>>Administrador</option>
</select>
</td>
</tr>

</table>
<center>
<? $metodo="guardar"; if($id!="") $metodo="actualizar";?>
<input type="button" value="Aceptar" onclick="javscript:<? echo $metodo; ?>();">
<input type="button" value="Cancelar" onclick="javscript:cancelar();">
</center>

</body>
</html>
