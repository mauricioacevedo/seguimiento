<?php 

	session_start();
	include_once('../conexion.php');
	include_once('../autenticacion.php');

	$conexion_bd = getConnection();
	checkout($conexion_bd);
	
	$operacion=$HTTP_GET_VARS["operacion"];

	if($operacion=="editarRegistro"){
		$id=$HTTP_GET_VARS["id"];

		$sql="select a.pedido,a.gestion,a.producto,a.asesor,a.observaciones,a.transaccion,a.aplicativo,a.tarea,a.cr_id,a.duracion,a.fecha,a.resultadogestion from registros_plataformas a where a.id=$id";

		$result = pg_query($sql);
                $rows=pg_numrows($result);

                if($rows<1){
                        header("./registros-plat.php?msg=Ocurrio un error con el registro que se intento editar.");
                        return;
                }

		$pedido=pg_result($result,0,0);
		$gestion = pg_result($result,0,1);
		$producto = pg_result($result,0,2);
		$asesor = pg_result($result,0,3);
		$observaciones = pg_result($result,0,4);
		$transaccion = pg_result($result,0,5);
		$aplicativo = pg_result($result,0,6);
		$tarea = pg_result($result,0,7);
		$cr_id = pg_result($result,0,8);
		$duracion = pg_result($result,0,9);
		$fecha = pg_result($result,0,10);
		$resultadogestion = pg_result($result,0,11);

		$asesorSession=strtoupper($_SESSION['login']);
                if($asesorSession!=$asesor&&$_SESSION['perfil']!="1"){//el actual usuario no es el creador del registro, deshabilito para cualquier otro usuario excepto los admin
                        $disabled=" disabled='true'";
                }

		
	}

	if($operacion=="buscarTecnico"){
		$identificacion=$HTTP_GET_VARS["identificacion"];
		$sql="select nombre,(select a.nombre from empresas a where a.id=empresa) as empresa,ciudad from tecnicos where identificacion='$identificacion'";
		$result = pg_query($sql);
	        $rows=pg_numrows($result);
		
	        if($rows<=0){
                //devolver a pagina inicial con mensaje
                	echo "NO;$identificacion;$sql";
        	} else {
          		$nombre=pg_result($result,0,0);
			$nombre_empresa=pg_result($result,0,1);
			$ciudad=pg_result($result,0,2);
			//echo $nombre_empresa;
                	echo "SI;$nombre;$identificacion;$nombre_empresa;$ciudad";
        	}
		return;

	}
	if($operacion=="actualizarRegistro"){

		$id = $HTTP_GET_VARS["id"];
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

		$sql="update registros_plataformas set pedido='$pedido',gestion='$gestion',producto='$producto',transaccion='$transaccion',aplicativo='$aplicativo',tarea='$tarea',observaciones='$observaciones',cr_id='$cr_id',asesor='$asesor',resultadogestion='$resultadogestion' where id=$id";

		$result = pg_query($sql);
		$msg='Registro actualizado con exito!!';

                echo "<script language='javascript'>".
                        "var Backlen=history.length;history.go(-Backlen);".
                        "window.location.href='./registros-plat.php?msg=$msg';".
                        "</script>";

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
		var resultadogestion = document.getElementById('resultadogestion');

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
		

		var request='&pedido='+pedido+'&observaciones='+observaciones+'&tarea='+tarea+'&aplicativo='+aplicativo+'&transaccion='+transaccion+'&producto='+producto+'&gestion='+gestion+'&fecha='+fecha+'&duracion='+duracion+'&login_del_asesor='+login_del_asesor+'&resultadogestion='+resultadogestion;
		location.href='./editar-plat.php?operacion=actualizarRegistro'+request+'&id=<? echo $id; ?>';
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

	//AJAX

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
<input type="hidden" id="cr_id" value='<? echo $cr_id; ?>'>
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
                        <option value="Amarillos" <? if($gestion=="Amarillos") echo "selected"; ?>>Amarillos</option>
                        <option value="Colas" <? if($gestion=="Colas") echo "selected"; ?>>Colas</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>


<TR>
        <TD align="left">Pedido</TD>
        <TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" onchange="javascript:hideMsg();" value="<? echo $pedido; ?>"></TD>
        <TD align="center"><a href='javascript:formaCR();'>Asociar CR</a><!--input type="button" value="Buscar" onclick="javascript:buscarPedido();"--></TD>
</TR>


<TR>
        <TD align="left"></TD>
         <TD align="center"><INPUT type="hidden" disabled="true" name="login_del_asesor" id="login_del_asesor" size="12" value="<? echo $_SESSION['login']; ?>"></TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Asesor</TD>
        <TD align="center"><INPUT type="text" name="asesor" size="12" id="asesor" value="<? echo $asesor; ?>" disabled="true"></TD>
        <TD align="center"><!--input type="button" value="Buscar" onclick="javascript:buscarPedido();"--></TD>
</TR>



<TR>
        <TD align="left">Producto</TD>
        <TD align="center">
                <SELECT name="producto" id="producto" onchange="">
			<option value="-1">Seleccione:</option>
                        <option value="HFC-Internet" <? if($producto=="HFC-Internet") echo "selected"; ?>>HFC-Internet</option>
                        <option value="HFC-TV Basica" <? if($producto=="HFC-TV Basica") echo "selected"; ?>>HFC-TV Basica</option>
                        <option value="HFC-ToIP" <? if($producto=="HFC-ToIP") echo "selected"; ?>>HFC-ToIP</option>
	
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Transacción</TD>
        <TD align="center">
                <SELECT name="transaccion" id="transaccion">
                        <option value="-1">Seleccione:</option>

                        <option value="Nuevo" <? if($transaccion=="Nuevo") echo "selected"; ?>>Nuevo</option>
                        <option value="Cambio" <? if($transaccion=="Cambio") echo "selected"; ?>>Cambio</option>
                        <option value="Retiro" <? if($transaccion=="Retiro") echo "selected"; ?>>Retiro</option>
                        <option value="Traslado" <? if($transaccion=="Traslado") echo "selected"; ?>>Traslado</option>
                        <option value="Movimiento Interno" <? if($transaccion=="Movimiento Interno") echo "selected"; ?>>Movimiento Interno</option>
                        <option value="Suspension" <? if($transaccion=="Suspension") echo "selected"; ?>>Suspension</option>
                        <option value="Reconexion" <? if($transaccion=="Reconexion") echo "selected"; ?>>Reconexion</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Aplicativo</TD>
        <TD align="center">
                <SELECT name="aplicativo" id="aplicativo">
                        <option value="-1">Seleccione:</option>
        
                        <option value="GTC" <? if($aplicativo=="GTC") echo "selected"; ?>>GTC</option>
                      	<option value="Inventario MSS" <? if($aplicativo=="Inventario MSS") echo "selected"; ?>>Inventario MSS</option>
                      	<option value="Activador" <? if($aplicativo=="Activador") echo "selected"; ?>>Activador</option>
                      	<option value="OSM" <? if($aplicativo=="OSM") echo "selected"; ?>>OSM</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Tarea</TD>
        <TD align="center">
                <SELECT name="tarea" id="tarea">
                        <option value="-1">Seleccione:</option>
			<option value='Activar buzon' <? if($tarea=="Activar buzon") echo "selected"; ?>>Activar buzon</option>
			<option value='Activar discados' <? if($tarea=="Activar discados") echo "selected"; ?>>Activar discados</option>
			<option value='Activar linea' <? if($tarea=="Activar linea") echo "selected"; ?>>Activar linea</option>
			<option value='Actualizar inventario completar' <? if($tarea=="Actualizar inventario completar") echo "selected"; ?>>Actualizar inventario completar</option>
			<option value='Actualizar inventario iniciar' <? if($tarea=="Actualizar inventario iniciar") echo "selected"; ?>>Actualizar inventario iniciar</option>
			<option value='Actualizar inventario modificar' <? if($tarea=="Actualizar inventario modificar") echo "selected"; ?>>Actualizar inventario modificar</option>
			<option value='Asociar login' <? if($tarea=="Asociar login") echo "selected"; ?>>Asociar login</option>
			<option value='Cambiar estado de login' <? if($tarea=="Cambiar estado de login") echo "selected"; ?>>Cambiar estado de login</option>
			<option value='Cambio de plan' <? if($tarea=="Cambio de plan") echo "selected"; ?>>Cambio de plan</option>
			<option value='Consulta datos gestion orden' <? if($tarea=="Consulta datos gestion orden") echo "selected"; ?>>Consulta datos gestion orden</option>
			<option value='Consulta de catalogo' <? if($tarea=="Consulta de catalogo") echo "selected"; ?>>Consulta de catalogo</option>
			<option value='Consultar cuenta domiciliaria' <? if($tarea=="Consultar cuenta domiciliaria") echo "selected"; ?>>Consultar cuenta domiciliaria</option>
			<option value='Consultar informacion pedido' <? if($tarea=="Consultar informacion pedido") echo "selected"; ?>>Consultar informacion pedido</option>
			<option value='Desaprovisionar equipo' <? if($tarea=="Desaprovisionar equipo") echo "selected"; ?>>Desaprovisionar equipo</option>
			<option value='Desaprovisionar linea' <? if($tarea=="Desaprovisionar linea") echo "selected"; ?>>Desaprovisionar linea</option>
			<option value='Enviar orden a GTC' <? if($tarea=="Enviar orden a GTC") echo "selected"; ?>>Enviar orden a GTC</option>
			<option value='Retirar buzon' <? if($tarea=="Retirar buzon") echo "selected"; ?>>Retirar buzon</option>
			<option value='Retiro EID' <? if($tarea=="Retiro EID") echo "selected"; ?>>Retiro EID</option>
			<option value='Retiro paquete canales' <? if($tarea=="Retiro paquete canales") echo "selected"; ?>>Retiro paquete canales</option>
			<option value='SuspenderBA' <? if($tarea=="SuspenderBA") echo "selected"; ?>>SuspenderBA</option>
			<option value='Suspender TOIP' <? if($tarea=="Suspender TOIP") echo "selected"; ?>>Suspender TOIP</option>
			<option value='Suspender TV' <? if($tarea=="Suspender TV") echo "selected"; ?>>Suspender TV</option>
			<option value='DOM' <? if($tarea=="DOM") echo "selected"; ?>>DOM</option>

                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Resultado de la Gestion</TD>
        <TD align="center">
                <SELECT name="resultadogestion" id="resultadogestion">
                        <option value="-1">Seleccione:</option>
                        <option value='CR' <? if($resultadogestion=="CR") echo "selected"; ?>>CR</option>
                        <option value='OK' <? if($resultadogestion=="OK") echo "selected"; ?>>OK</option>
                        <option value='Repetido' <? if($resultadogestion=="Repetido") echo "selected"; ?>>Repetido</option>
                        <option value='CR inventario' <? if($resultadogestion=="CR inventario") echo "selected"; ?>>CR inventario</option>
                        <option value='CR agenda' <? if($resultadogestion=="CR agenda") echo "selected"; ?>>CR agenda</option>
                        <option value='Sin excepcion' <? if($resultadogestion=="Sin excepcion") echo "selected"; ?>>Sin excepcion</option>
                        <option value='Cancelado' <? if($resultadogestion=="Cancelado") echo "selected"; ?>>Cancelado</option>
                        <option value='Grupo soporte OE' <? if($resultadogestion=="Grupo soporte OE") echo "selected"; ?>>Grupo soporte OE</option>
                        <option value='Retiro linea' <? if($resultadogestion=="Retiro linea") echo "selected"; ?>>Retiro línea</option>
                        <option value='Limpiar EID' <? if($resultadogestion=="Limpiar EID") echo "selected"; ?>>Limpiar EID</option>
                        <option value='TT' <? if($resultadogestion=="TT") echo "selected"; ?>>TT</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Observaciones</TD>
        <TD align="center"> <TEXTAREA name="observaciones" id="observaciones" cols="40" rows="4"><? echo $observaciones; ?></TEXTAREA></TD>
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

</BODY>
</HTML>
