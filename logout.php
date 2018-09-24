<?php

        session_start();//$_SESSION['user_nombre'] = $nombre;
        $_SESSION['nombre'] = "";
        $_SESSION['perfil'] = "";
        $_SESSION['login'] = "";
        $_SESSION['password'] = "";

	session_unset();
    	session_destroy();
    	session_write_close();
    	setcookie(session_name(),'',0,'/');
	session_regenerate_id(true);

	echo "<script language='javascript'>";
        echo "location.href='./index.php'";
	echo "</script>";

?>
