<?php

        session_start();//$_SESSION['user_nombre'] = $nombre;
        include_once('conexion.php');
	$conexion_bd = getConnection();

	$operacion=$_GET["operacion"];


        $login=$_GET["user"];
        $pwd=$_GET["pwd"];

	if($operacion=="checkUser"){//hacer un check al usuario actual de session, trato de ingresar a una pagina no valida.
	        $login=$_SESSION['login'];
	        $pwd=$_SESSION['password'];
	}

        $rows=-1;

        $sql="select nombre,perfil,id from usuarios where username='$login' and password='$pwd'";
        //echo $sql;
        $result = pg_query($sql);
        $rows=pg_numrows($result);
        //echo "rows: $rows";
        if($rows<=0){
                //devolver a pagina inicial con mensaje
                echo "<script language='javascript'>";
                $msg="Usuario o contraseña incorrecto, por favor verifique su informacion.";
                echo "location.href='./index.php?msg=$msg'";
                echo "</script>";
        } else {
                $nombre = pg_result($result,0,0);
		$perfil = pg_result($result,0,1);
		$idUser = pg_result($result,0,2);
                $_SESSION['nombre'] = utf8_encode($nombre);
		$_SESSION['perfil'] = $perfil;
		$_SESSION['login'] = strtoupper($login);
		$_SESSION['password'] = $pwd;
		$_SESSION['idUser'] = $idUser;

		$url="";
		if($perfil=="1"){//administrador
			$url="./opciones.php";
		}if($perfil=="2"){//asesor
			$url="./actividades.php";
                }if($perfil=="3"){//consulta
			$url="./registros.php";
                }if($perfil=="4"){//asignaciones/grupo inconsistencias
                        $url="./actividades.php";
                }if($perfil=="5"){//Estudio tecnico
                        $url="./estudiotecnico/gestion.php";
                }if($perfil=="6"){//plataformas
                        $url="./plataformas/gestion.php";
                }

                echo "<script language='javascript'>";
                echo "location.href='$url'";
                echo "</script>";
        }

?>

