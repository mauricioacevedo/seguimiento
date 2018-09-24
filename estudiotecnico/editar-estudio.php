<?php 

	session_start();
	include_once('../conexion.php');
	include_once('../autenticacion.php');

	$conexion_bd = getConnection();
	checkout($conexion_bd);
	
	$operacion=$HTTP_GET_VARS["operacion"];

	if($operacion=="editarRegistro"){
		$id=$HTTP_GET_VARS["id"];

		$sql="select a.pedido,a.estado,a.estado_final,a.asesor,a.observaciones,a.cr_id,a.duracion,a.fecha,a.cr_id from registros_estudio a where a.id=$id";

		$result = pg_query($sql);
                $rows=pg_numrows($result);

                if($rows<1){
                        header("./registros-estudio.php?msg=Ocurrio un error con el registro que se intento editar.");
                        return;
                }

		$pedido=pg_result($result,0,0);
		$estado = pg_result($result,0,1);
		$estado_final = pg_result($result,0,2);
		$asesor = pg_result($result,0,3);
		$observaciones = pg_result($result,0,4);
		$cr_id = pg_result($result,0,5);
		$duracion = pg_result($result,0,6);
		$fecha = pg_result($result,0,7);

		$asesorSession=strtoupper($_SESSION['login']);
                if($asesorSession!=$asesor&&$_SESSION['perfil']!="1"){//el actual usuario no es el creador del registro, deshabilito para cualquier otro usuario excepto los admin
                        $disabled=" disabled='true'";
                }

		
	}

	if($operacion=="actualizarRegistro"){

		$id = $HTTP_GET_VARS["id"];

                $observaciones = $HTTP_GET_VARS["observaciones"];
                $estado = $HTTP_GET_VARS["estado"];
                $estadofinal = $HTTP_GET_VARS["estadofinal"];

                $pedido = $HTTP_GET_VARS["pedido"];
                $cr_id = $HTTP_GET_VARS["cr_id"];
                $crfechaIni = $HTTP_GET_VARS["crfechaIni"];
                $crfechaFin = $HTTP_GET_VARS["crfechaFin"];
                $fecha = $HTTP_GET_VARS["fecha"];
                $duracion = $HTTP_GET_VARS["duracion"];
                //$asesor = $HTTP_GET_VARS["login_del_asesor"];
                $asesor = $_SESSION['login'];//se esta guardando el login de quien lo modifico al final

		if($cr_id==''){
			$cr_id='SIN CR';
		}

		$sql="update registros_estudio set pedido='$pedido',estado='$estado',estado_final='$estadofinal',observaciones='$observaciones',cr_id='$cr_id',asesor='$asesor',duracion=(duracion::time + '0001-01-01 $duracion'::timestamp)::time where id=$id";

		$result = pg_query($sql);
		
		//2. gestion sobre el cr

                if($cr_id!='SIN CR'){

                        //a.primero busco si el pedido/cr_id exite.. si existe lo actualizo, sino inserto!!!
                        $sql="select id from cr_table where cr_id='$cr_id'";
                        $result = pg_query($sql);
                        $rows=pg_numrows($result);

                        if($rows>0){//es un update
                                $id=pg_result($result,0,0);
                                $sql="update cr_table fecha_inicio='$crfechaIni',fecha_final='$crfechaFin' where id=$id";
                                $result = pg_query($sql);

                                //echo "Registro actualizado con exito!!";
                        } else {//nuevo ingreso
                                $sql="insert into cr_table(cr_id,fecha_inicio,fecha_final) values ('$cr_id','$crfechaIni','$crfechaFin')";
                                $result = pg_query($sql);
                                //echo "Registro insertado con exito!!";
                        }
                        //b. inserto registro en la tabla cruzada

                        $sql="insert into crxpedido(pedido,cr_id,fecha,asesor,modulo) values ('$pedido','$cr_id','$fecha','$asesor','Estudio Tecnico')";
                        $result = pg_query($sql);
                }



		$msg='Registro actualizado con exito!!';

                echo "<script language='javascript'>".
                        "var Backlen=history.length;history.go(-Backlen);".
                        "window.location.href='./registros-estudio.php?msg=$msg';".
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

		var observaciones= document.getElementById('observaciones');
                var cr_id = document.getElementById("cr_id").value;
                var crfechaIni = document.getElementById("crfechaIni").value;
                var crfechaFin = document.getElementById("crfechaFin").value;

                if(cr_id==""||cr_id=="-1"){
                        cr_id="SIN CR";
                }

                var estado= document.getElementById('estado');
                var estadofinal= document.getElementById('estadofinal');

                estado = estado.options[estado.selectedIndex].value;
                estadofinal = estadofinal.options[estadofinal.selectedIndex].value;
                observaciones = observaciones.value;

                var fecha=document.getElementById("initFecha").value;

                var login_del_asesor = document.getElementById("login_del_asesor").value;
                var hidden=document.getElementById("initTime");
                var df=new Date().getTime() - hidden.value;
                df=new Date(df);
                var duracion=doubleDigit(df.getHours()-19)+":"+doubleDigit(df.getMinutes())+":"+doubleDigit(df.getSeconds());

		var request='&pedido='+pedido+'&estado='+estado+'&estadofinal='+estadofinal+'&cr_id='+cr_id+'&crfechaIni='+crfechaIni+'&crfechaFin='+crfechaFin+'&duracion='+duracion+'&fecha='+fecha+'&observaciones='+observaciones+'&login_del_asesor='+login_del_asesor;

		location.href='./editar-estudio.php?operacion=actualizarRegistro'+request+'&id=<? echo $id; ?>';
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
                var crfechaIni = document.getElementById("crfechaIni");
                var crfechaFin = document.getElementById("crfechaFin");
                cr_id.value=numerocr;
                crfechaIni.value=fechaIni;
                crfechaFin.value = fechaFin;

		Modalbox.hide();
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
		location.href="./registros-estudio.php";
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
<input type="hidden" value="" id="crfechaIni">
<input type="hidden" value="" id="crfechaFin">

<DIV id="cal" style="position:absolute; z-index:2;">&nbsp;</DIV>


<FORM name="forma1">
<table width="95%">
<tr><td>
<div width="100%" class="bannercentral" id="bannercentral">
<IMG src="../img/logo-estt.png" height="130" width="80%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="../logout.php">Salir</a></font></td></tr>
</table>

<CENTER><H2>Registro de Solicitudes asociadas a Estudio Técnico</H2></CENTER>

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
        <TD align="left">Oferta/Pedido</TD>
        <TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" onchange="javascript:hideMsg();" value="<? echo $pedido; ?>"></TD>
        <TD align="center"><!--input type="button" value="Buscar" onclick="javascript:buscarPedido();"--></TD>
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
        <TD align="left">CR</TD>
        <TD align="center"> <a href='javascript:formaCR();'>Asociar CR</a><? if($cr_id!='SIN CR') echo "($cr_id)"; ?></TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Estado</TD>
        <TD align="center">
                <SELECT name="estado" id="estado">
                        <option value="-1">Seleccione:</option>

                        <option value="AGENDADO" <?if($estado=="AGENDADO") echo "selected";?>>AGENDADO</option>
                        <option value="AUDITORIA" <?if($estado=="AUDITORIA") echo "selected";?>>AUDITORIA</option>
                        <option value="COBERTURA" <?if($estado=="COBERTURA") echo "selected";?>>COBERTURA</option>
                        <option value="CONSTRUCCION" <?if($estado=="CONSTRUCCION") echo "selected";?>>CONSTRUCCION</option>
                        <option value="DISPONIBILIDAD" <?if($estado=="DISPONIBILIDAD") echo "selected";?>>DISPONIBILIDAD</option>
                        <option value="PROSPECCION" <?if($estado=="PROSPECCION") echo "selected";?>>PROSPECCION</option>
                        <option value="RED EXTERNA" <?if($estado=="RED EXTERNA") echo "selected";?>>RED EXTERNA</option>
                        <option value="TV EMPRESARIAL" <?if($estado=="TV EMPRESARIAL") echo "selected";?>>TV EMPRESARIAL</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Estado Final</TD>
        <TD align="center">
                <SELECT name="estadofinal" id="estadofinal">
                        <option value="Sin Gestion">Sin Gestión</option>
                        <option value='En Analisis de Demanda' <?if($estado_final=="En Analisis de Demanda") echo "selected";?>>En Análisis de Demanda</option>
                        <option value='En Factibilizacion Proyecto Exp' <?if($estado_final=="En Factibilizacion Proyecto Exp") echo "selected";?>>En Factibilización Proyecto Exp</option>
                        <option value='En Factibilizacion Proyecto Ampl' <?if($estado_final=="En Factibilizacion Proyecto Ampl") echo "selected";?>>En Factibilización Proyecto Ampl</option>
                        <option value='Fin Prospeccion' <?if($estado_final=="Fin Prospeccion") echo "selected";?>>Fin Prospección</option>
                        <option value='Anulada' <?if($estado_final=="Anulada") echo "selected";?>>Anulada</option>
                        <option value='Auditoria' <?if($estado_final=="Auditoria") echo "selected";?>>Auditoria</option>
                        <option value='Cancelada' <?if($estado_final=="Cancelada") echo "selected";?>>Cancelada</option>
                        <option value='Cobertura' <?if($estado_final=="Cobertura") echo "selected";?>>Cobertura</option>
                        <option value='Construccion' <?if($estado_final=="Construccion") echo "selected";?>>Construcción</option>
                        <option value='Cumplido' <?if($estado_final=="Cumplido") echo "selected";?>>Cumplido</option>
                        <option value='Disponible' <?if($estado_final=="Disponible") echo "selected";?>>Disponible</option>
                        <option value='Incidente' <?if($estado_final=="Incidente") echo "selected";?>>Incidente</option>
                        <option value='Reconfigurado' <?if($estado_final=="Reconfigurado") echo "selected";?>>Reconfigurado</option>
                        <option value='Reservado' <?if($estado_final=="Reservado") echo "selected";?>>Reservado</option>
                        <option value='Prospecto' <?if($estado_final=="Prospecto") echo "selected";?>>Prospecto</option>
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
<script language='javascript'>saveTime();</script>
</BODY>
</HTML>
