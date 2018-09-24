<?php

function checkout($conexion_bd) {
	$nombre=$_SESSION['nombre'];
        $perfil=$_SESSION['perfil'];
        $user=$user2;
        $pwd=$_SESSION['password'];


	if($nombre=="" || $perfil==""){
		echo "<script language='javascript'>";
                $msg="Usuario desconocido.";
                echo "location.href='/seguimiento/index.php?msg=$msg'";
                echo "</script>";
		return;
	}
	
	$script = basename($_SERVER['SCRIPT_NAME']);

	$sql="select * from pantallas where nombre='$script' and perfiles like '%$perfil%'";
	$result = pg_query($sql);
        $rows=pg_numrows($result);

                if($rows<=0){
		//$user=$_SESSION['login'];
		//$pwd=$_SESSION['password'];

		

                //devolver a pagina login para un full checkout
                        echo "<script language='javascript'>alert('Esta ingresando a un recurso no permitido. [".$script."]');location.href='./login.php?operacion=checkUser';</script>";
			return;
                } else {//usuario puede seguir en la pagina
			//ñaña
                }
                return;

}

?>

