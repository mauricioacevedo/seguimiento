<?php 

	session_start();
	include_once('../conexion.php');
	include_once('../autenticacion.php');

	$conexion_bd = getConnection();
	checkout($conexion_bd);
	
	$operacion=$HTTP_GET_VARS["operacion"];

	if($operacion=="insertarRegistro"){

		$tarea = $HTTP_GET_VARS["tarea"];
		$observaciones = $HTTP_GET_VARS["observaciones"];
		$aplicativo = $HTTP_GET_VARS["aplicativo"];
		$transaccion = $HTTP_GET_VARS["transaccion"];
		$producto = $HTTP_GET_VARS["producto"];
		$gestion = $HTTP_GET_VARS["gestion"];
		$pedido = $HTTP_GET_VARS["pedido"];
		$cr_id = $HTTP_GET_VARS["cr_id"];
		$fecha = $HTTP_GET_VARS["fecha"];
		$duracion = $HTTP_GET_VARS["duracion"];
		$asesor = $HTTP_GET_VARS["login_del_asesor"];
		$resultadogestion = $HTTP_GET_VARS["resultadogestion"];

		if($cr_id==''){
			$cr_id='SIN CR';
		}

		$sql="insert into registros_plataformas(pedido,gestion,producto,transaccion,aplicativo,tarea,observaciones,cr_id,duracion,fecha,asesor,resultadogestion) values ('$pedido','$gestion','$producto','$transaccion','$aplicativo','$tarea','$observaciones','$cr_id','$duracion','$fecha','$asesor','$resultadogestion');";

		$result = pg_query($sql);
		$msg='Registro ingresado con exito!!';

                echo "<script language='javascript'>".
                        "var Backlen=history.length;history.go(-Backlen);".
                        "window.location.href='./gestion.php?msg=$msg';".
                        "</script>";

		return;
	}

	if($operacion=="ingresarCR"){
		$cr_id = $HTTP_GET_VARS["cr_id"];
		$pedido = $HTTP_GET_VARS["pedido"];
		$fechaIni = $HTTP_GET_VARS["fechaIni"];
		$fechaFin = $HTTP_GET_VARS["fechaFin"];
		//1. primero busco si el pedido/cr_id exite.. si existe lo actualizo, sino inserto!!!
		$sql="select id from crxpedido where pedido='$pedido' and cr_id='$cr_id'";
		$result = pg_query($sql);
		$rows=pg_numrows($result);

		if($rows>0){//es un update
			$id=pg_result($result,0,0);
			$sql="update crxpedido set pedido='$pedido',cr_id='$cr_id',fecha_inicio='$fechaIni',fecha_final='$fechaFin' where id=$id";
			$result = pg_query($sql);
			echo "Registro actualizado con exito!!";
		} else {//nuevo ingreso
			$sql="insert into crxpedido(pedido,cr_id,fecha_inicio,fecha_final) values ('$pedido','$cr_id','$fechaIni','$fechaFin')";
			$result = pg_query($sql);
			echo "Registro insertado con exito!!";
		}
		return;
	}

?>


<HTML><HEAD>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Actividades</TITLE>

        <script type="text/javascript" src="../javascript/modalbox/lib/prototype.js"></script>
        <script type="text/javascript" src="../javascript/modalbox/lib/scriptaculous.js?load=effects"></script>

        <script type="text/javascript" src="../javascript/modalbox/modalbox.js"></script>
        <link rel="stylesheet" href="../javascript/modalbox/modalbox.css" type="text/css" />

        <script type="text/javascript" src="../javascript/jquery.min.js"></script>
        <script type="text/javascript" src="../javascript/jquery.blockUI.js?v2.38"></script>

        <script>
             jQuery.noConflict();
        </script>

        <style type="text/css" media="screen">
                html, body {
                        width: 100%;
                        height: 100%;
                }
                #MB_loading {
                        font-size: 13px;
                }
                #errmsg {
                        margin: 1em;
                        padding: 1em;
                        color: #C30;
                        background-color: #FCC;
                        border: 1px solid #F00;
                }
        </style>


<script language="javascript">
function rex(stringInput){

        var specialChars = "!$^&%()=[]\/{}|<>?#";
        for (var i = 0; i < specialChars.length; i++) {
                stringInput = stringInput.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
        }
        return stringInput;
}


function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

	function guardarDatos(){
		var pedido = document.getElementById('pedido');
		pedido = pedido.value;

		if(!isNumber(pedido)){
			alert("Ingrese un dato numerico para el pedido");
			document.getElementById('pedido').focus();
			return;
		}
		
		var tarea = document.getElementById('tarea');
		var observaciones= document.getElementById('observaciones');
		var aplicativo= document.getElementById('aplicativo');
		var transaccion= document.getElementById('transaccion');
		var producto= document.getElementById('producto');
		var gestion= document.getElementById('gestion');
		var login_del_asesor = document.getElementById('login_del_asesor').value;
		var resultadogestion = 	document.getElementById('resultadogestion');	
		var cr_id= document.getElementById('cr_id').value;		


		tarea = tarea.options[tarea.selectedIndex].value;
		observaciones = observaciones.value;
		aplicativo = aplicativo.options[aplicativo.selectedIndex].value;
		transaccion = transaccion.options[transaccion.selectedIndex].value;
		producto = producto.options[producto.selectedIndex].value;
		gestion = gestion.options[gestion.selectedIndex].value;
		resultadogestion = resultadogestion.options[resultadogestion.selectedIndex].value;

		var fecha=document.getElementById("initFecha").value;

		var hidden=document.getElementById("initTime");
		var df=new Date().getTime() - hidden.value;
		df=new Date(df);
		var duracion=doubleDigit(df.getHours()-19)+":"+doubleDigit(df.getMinutes())+":"+doubleDigit(df.getSeconds());


		var request='&pedido='+pedido+'&observaciones='+observaciones+'&tarea='+tarea+'&aplicativo='+aplicativo+'&transaccion='+transaccion+'&producto='+producto+'&gestion='+gestion+'&fecha='+fecha+'&duracion='+duracion+'&login_del_asesor='+login_del_asesor+'&resultadogestion='+resultadogestion+'&cr_id='+cr_id;
		location.href='./gestion.php?operacion=insertarRegistro'+request;
	}

	function formaCR(){
		var cr_id=document.getElementById("cr_id").value;
		var pedido = document.getElementById("pedido").value;

		if(pedido==''){
			alert("Primero debe ingresar un numero de pedido valido");
			return;
		}

		Modalbox.show('formaCR.php?cr_id='+cr_id+'&pedido='+pedido, {title: 'Forma de CR',height: 400, width: 500 });
	}

	function guardarCR(){
		var numerocr = document.getElementById("numerocr").value;
		var fechaIni = document.getElementById("fechaIni").value;
		var fechaFin = document.getElementById("fechaFin").value;
		var pedido = document.getElementById("pedido").value;

		var cr_id = document.getElementById("cr_id");
		cr_id.value=numerocr;
	
		Modalbox.hide();
	
		http_request = false;
                if (window.XMLHttpRequest) { // Mozilla, Safari,...
                        http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                // set type accordingly to anticipated content type
                //http_request.overrideMimeType('text/xml');
                        http_request.overrideMimeType('text/html');
                }
                } else if (window.ActiveXObject) { // IE
                        try {
                                http_request = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {}
                        }
                }
                if (!http_request) {
                        alert('Cannot create XMLHTTP instance');
                        return false;
                }


                var url="./gestion.php?operacion=ingresarCR&pedido="+pedido+"&cr_id="+numerocr+"&fechaIni="+fechaIni+"&fechaFin="+fechaFin;
                http_request.onreadystatechange = respuestaIngresarCR;

                http_request.open('GET', url, true);
                http_request.send(null);
	}

	//AJAX

	function respuestaIngresarCR(){
                if (http_request.readyState == 4) {
                        if (http_request.status == 200) {
                                //alert(http_request.responseText);
                                var result = http_request.responseText;
				
				alert(result);
				
                        } else {
                                alert('There was a problem with the request.');
                        }
            	}
	}

	//////final funciones pagina de ingreso de tecnico
	
	function copiarObservacion(producto){
		var motivo=document.getElementById("motivo");
		var estado_fuente = document.getElementById("estado_fuente");
		var cambio_infraestructura = document.getElementById("cambio_infraestructura");
		var saliente = document.getElementById("saliente").value;
		var entrante = document.getElementById("entrante").value;
		var fw_actualizado = document.getElementById("fw_actualizado");
		
		motivo=motivo.options[motivo.selectedIndex].value;
		estado_fuente=estado_fuente.options[estado_fuente.selectedIndex].value;
		cambio_infraestructura=cambio_infraestructura.options[cambio_infraestructura.selectedIndex].value;
		fw_actualizado=fw_actualizado.options[fw_actualizado.selectedIndex].value;

		var dp='';
		var ds='';
		var up='';
		var us='';
		var id_prueba_smpro='';

		var observacion="";

		if(producto.indexOf("HFC")>=0){
			dp=document.getElementById("dp").value;
			ds=document.getElementById("ds").value;
			up=document.getElementById("up").value;
			us=document.getElementById("us").value;
	
			//observacion+="*Parámetros CM : DP "+dp+" - DS "+ds+" - UP: "+up+" - US: "+us;
		}else{
			id_prueba_smpro=document.getElementById("id_prueba_smpro").value;
			observacion+="*ID SMPRO: "+id_prueba_smpro;
		}
		
		observacion+="*Motivo: "+motivo;
		observacion+="*Verifico Fuente: "+estado_fuente;
		observacion+="*Cambio Infraestructura: "+cambio_infraestructura;
		observacion+="*Equipo Saliente: "+saliente;
		observacion+="*Equipo Nuevo: "+entrante;
		observacion+="*Firmware Actualizado: "+fw_actualizado;
		
		var observaciones = document.getElementById("observaciones");
		observaciones.value= observaciones.value+" "+observacion+"*";
		Modalbox.hide();
	}


	function hideMsg(){

		var divMensajeCentral=document.getElementById("divMensajeCentral");
		if(divMensajeCentral.style.visibility=="hidden") return;
		else {
			//sleep(3000);
			divMensajeCentral.style.position="absolute";
			divMensajeCentral.style.visibility="hidden";
		}
	}

	function buscarPedido(){
		var pedido=document.getElementById("pedido");
		if(pedido.value==""){
			alert("Ingrese un numero de pedido.");
			pedido.focus();
			return;
		}
		Modalbox.show('validacionPedido.php?pedido='+pedido.value, {title: 'Busqueda de Pedidos',height: 400, width: 800 });
	}


	///inicio funciones pagina de validacion de pedidos
	function mostrarObservacion(id){
		//alert("llegue");
		var divi=document.getElementById("div"+id);
		if(divi.innerHTML==""||divi.innerHTML=="null"||divi.innerHTML==null){
			return;
		}else{
			alert("OBSERVACIONES:\n\n"+divi.innerHTML);
		}
		//divi.style.visibility="visible";
		//divi.style.position="relative";
		return;
	}


	function formaRegistros(){
		
		//if(user==""){
		//	user=prompt("Ingrese su nombre de usuario.");
		//	if(user==""||user=="null"||user==null){
		//		alert("No se puede llevar a cabo la operacion, debe ingresar su nombre de usuario.");
		//		return;
		//	}
		//}
		location.href="./registros-plat.php";
	}


        function saveTime(){
                var hidden=document.getElementById("initTime");
                var hiddenFecha=document.getElementById("initFecha");
                hidden.value=new Date().getTime();
                var hidden2=document.getElementById("inicioLlamada");
                hidden2.value="true";
		var now = new Date(); 
  		var then = now.getFullYear()+'-'+doubleDigit(now.getMonth()+1)+'-'+doubleDigit(now.getDate()); 
		then += ' '+doubleDigit(now.getHours())+':'+doubleDigit(now.getMinutes())+':'+doubleDigit(now.getSeconds());
		

		hiddenFecha.value=then;
		//alert(then);
        }
        function doubleDigit(num){//estamos asumiendo que no hay cantidades negativas!!!
		
		if(num<0){
			num=0;
		}
		
                if(num<=9){
                        return "0"+num;
                }
                return num;
        }

</script>

<STYLE type="text/css">
        a:link { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:visited { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:hover { font-weight: bold; font-size: 16px; color: blue; text-decoration: none }
</STYLE>

<style type="text/css">
table.pruebaintegrada {
	border-width: 4px;
	border-spacing: 2px;
	border-style: groove;
	border-color: yellow;
	border-collapse: separate;
	background-color: white;
}
table.pruebaintegrada th {
	border-width: 0px;
	padding: 1px;
	border-style: inset;
	border-color: yellow;
	background-color: white;
	-moz-border-radius: ;
}
table.pruebaintegrada td {
	border-width: 0px;
	padding: 1px;
	border-style: inset;
	border-color: yellow;
	background-color: white;
	-moz-border-radius: ;
}
</style>

        <style type="text/css" media="screen">
                html, body {
                        width: 100%;
                        height: 100%;
                }
                #MB_loading {
                        font-size: 13px;
                }
                #errmsg {
                        margin: 1em;
                        padding: 1em;
                        color: #C30;
                        background-color: #FCC;
                        border: 1px solid #F00;
                }
        </style>

<SCRIPT language="JavaScript" src="../javascript/calendar.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" href="../javascript/actividades.css" type="text/css" />
</HEAD>

<BODY bgcolor="WHITE">
<input type="hidden" value="" id="initTime">
<input type="hidden" value="" id="initFecha">
<input type="hidden" value="false" id="inicioLlamada">
<input type="hidden" value="" id="cr_id">

<DIV id="cal" style="position:absolute; z-index:2;">&nbsp;</DIV>


<FORM name="forma1">
<table width="95%">
<tr><td>
<div width="100%" class="bannercentral" id="bannercentral">
<IMG src="../img/logo-plata.png" height="130" width="80%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="./logout.php">Salir</a></font></td></tr>
</table>

<CENTER><H2>Registro de Solicitudes asociadas a Plataformas</H2></CENTER>

<center>
<div id="divMensajeCentral" <? if($msg!="") echo " style=\"position:relative;visibility:visible;background-color: #FFFEBE;border:2px solid #FFFE88;\""; else echo " style=\"position:absolute;visibility:hidden;\"" ?>>
<font color="red"><b><? echo $msg;?></b></font>
</div>
</center>
<BR>

<TABLE align="center">

<tr>
<td>

<!-- forma de ingreso normal -->

<TABLE align="center">

<TBODY>

<TR>
        <TD align="left">Tipo de Gestión</TD>
        <TD align="center">
                <SELECT name="gestion" id="gestion">
                        <option value="Amarillos">Amarillos</option>
                        <option value="Colas">Colas</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>


<TR>
        <TD align="left">Pedido</TD>
        <TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" onchange="javascript:hideMsg();"></TD>
        <TD align="center"><a href='javascript:formaCR();'>Asociar CR</a><input type="button" value="Buscar" onclick="javascript:buscarPedido();"></TD>
</TR>


<TR>
        <TD align="left"></TD>
         <TD align="center"><INPUT type="hidden" disabled="true" name="login_del_asesor" id="login_del_asesor" size="12" value="<? echo $_SESSION['login']; ?>"></TD>
        <TD align="center"></TD>
</TR>




<TR>
        <TD align="left">Producto</TD>
        <TD align="center">
                <SELECT name="producto" id="producto" onchange="javascript:selectProducto();">
			<option value="-1">Seleccione:</option>
			
			<option value="HFC-Internet">HFC-Internet</option>
			<option value="HFC-TV Basica">HFC-TV Básica</option>
			<option value="HFC-ToIP">HFC-ToIP</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Transacción</TD>
        <TD align="center">
                <SELECT name="transaccion" id="transaccion">
                        <option value="-1">Seleccione:</option>

                        <option value="Nuevo">Nuevo</option>
                        <option value="Cambio">Cambio</option>
                        <option value="Retiro">Retiro</option>
                        <option value="Traslado">Traslado</option>
                        <option value="Movimiento Interno">Movimiento Interno</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Reconexion">Reconexion</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Aplicativo</TD>
        <TD align="center">
                <SELECT name="aplicativo" id="aplicativo">
                        <option value="-1">Seleccione:</option>
        
                        <option value="GTC">GTC</option>
                      	<option value="Inventario MSS">Inventario MSS</option>
                      	<option value="Activador">Activador</option>
                      	<option value="OSM">OSM</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Tarea</TD>
        <TD align="center">
                <SELECT name="tarea" id="tarea">
                        <option value="-1">Seleccione:</option>
			<option value='Activar buzon'>Activar buzon</option>
			<option value='Activar discados'>Activar discados</option>
			<option value='Activar linea'>Activar linea</option>
			<option value='Actualizar inventario completar'>Actualizar inventario completar</option>
			<option value='Actualizar inventario iniciar'>Actualizar inventario iniciar</option>
			<option value='Actualizar inventario modificar'>Actualizar inventario modificar</option>
			<option value='Asociar login'>Asociar login</option>
			<option value='Cambiar estado de login'>Cambiar estado de login</option>
			<option value='Cambio de plan'>Cambio de plan</option>
			<option value='Consulta datos gestion orden'>Consulta datos gestion orden</option>
			<option value='Consulta de catalogo'>Consulta de catalogo</option>
			<option value='Consultar cuenta domiciliaria'>Consultar cuenta domiciliaria</option>
			<option value='Consultar informacion pedido'>Consultar informacion pedido</option>
			<option value='Desaprovisionar equipo'>Desaprovisionar equipo</option>
			<option value='Desaprovisionar linea'>Desaprovisionar linea</option>
			<option value='Enviar orden a GTC'>Enviar orden a GTC</option>
			<option value='Retirar buzon'>Retirar buzon</option>
			<option value='Retiro EID'>Retiro EID</option>
			<option value='Retiro paquete canales'>Retiro paquete canales</option>
			<option value='SuspenderBA'>SuspenderBA</option>
			<option value='Suspender TOIP'>Suspender TOIP</option>
                        <option value='Suspender TV'>Suspender TV</option>
                        <option value='DOM'>DOM</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Resultado de la Gestion</TD>
        <TD align="center">
                <SELECT name="resultadogestion" id="resultadogestion">
                        <option value="-1">Seleccione:</option>
                        <option value='CR'>CR</option>
                        <option value='OK'>OK</option>
                        <option value='Repetido'>Repetido</option>
                        <option value='CR inventario'>CR inventario</option>
                        <option value='CR agenda'>CR agenda</option>
                        <option value='Sin excepcion'>Sin excepcion</option>
                        <option value='Cancelado'>Cancelado</option>
                        <option value='Grupo soporte OE'>Grupo soporte OE</option>
			<option value='Retiro linea'>Retiro línea</option>
                        <option value='Limpiar EID'>Limpiar EID</option>
                        <option value='TT'>TT</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>


<TR>
        <TD align="left">Observaciones</TD>
        <TD align="center"> <TEXTAREA name="observaciones" id="observaciones" cols="40" rows="4"></TEXTAREA></TD>
        <TD align="center"></TD>
</TR>

</TBODY></TABLE>

</td>
<td>

</td>
</tr>
</table>

<BR>
<CENTER><INPUT type="button" value="Guardar" onclick="javascript:guardarDatos();">
&nbsp;<INPUT type="button" value="Ver Registros" onclick="javascript:formaRegistros();">
</CENTER>

</FORM>
<br><br>

<div id="question" style="display:none; cursor: default">
        <h1>Seguimiento a Pedidos<br><font color='red'>Plataformas</font></h1>
        <input type="button" id="yes" value="Iniciar Registro" />
        <INPUT type="button" value="Ver Registros" onclick="javascript:formaRegistros();">
	<br><br>&nbsp;<a href="../manageUserAsesor.php?operacion=editUser&idUser=<? echo $_SESSION['idUser']; ?>">Editar Cuenta</a>
</div>
<div class="blockUI" style="display:none"></div>
<div class="blockUI blockOverlay" style="z-index: 1000; border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none; border-width: initial; border-color: initial; border-image: initial; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background-color: rgb(0, 0, 0); cursor: wait; position: fixed; opacity: 0.7; "></div>


</BODY>

<script type="text/javascript">
        //se cambio el modificador de jquery "$" por su definicion completa, debido a unos problemas de compatibilidad entre jquery, prototype y blockui

jQuery(document).ready(function($) {

        //jQuery.blockUI({ message: $('#question'), css: { width: '525px' } });
        $.blockUI({ message: $('#question'), css: { width: '525px' } });

        $('#yes').click(function() {
        //jQuery('#yes').click(function() {
        // update the block message
                $.unblockUI({ fadeOut: 500 });
                //jQuery.unblockUI({ fadeOut: 500 });
                saveTime();
        //$.blockUI({ message: '<h1>Remote call in progress...</h1>' });

        });

});
</script>

</HTML>
