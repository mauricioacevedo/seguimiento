<?php 

	session_start();
	include_once('conexion.php');
        include_once('autenticacion.php');

        $conexion_bd = getConnection();
        checkout($conexion_bd);
	

	if($_SESSION["perfil"]=="3"){//deshabilitar forma
		$disabled=" disabled='true'";
	}

	//$conexion_bd = getConnection();
	$operacion=$HTTP_GET_VARS["operacion"];
	


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
	if($operacion=="ingresarTecnico"){
		$identificacion=$HTTP_GET_VARS["identificacion"];
		$nombre=$HTTP_GET_VARS["nombre"];
		$empresa=$HTTP_GET_VARS["empresa"];
		$ciudad=$HTTP_GET_VARS["ciudad"];

		$sql="insert into tecnicos(identificacion,nombre,empresa,ciudad) values ('$identificacion','$nombre',$empresa,'$ciudad');";

		$result = pg_query($sql);
		
		$sql="select nombre from empresas where id=$empresa";
		$result = pg_query($sql);
		$rows=pg_numrows($result);
		
		if($rows<=0){
                //devolver a pagina inicial con mensaje
                	echo "NO;$sql;ERROR";
        	} else {
          		$nombre_empresa=pg_result($result,0,0);
                	echo "SI;$nombre;$identificacion;$nombre_empresa;$ciudad";
        	}

		//echo "SI;$nombre;$identificacion;$empresa";
		return;
	}
	
	if($operacion=="ingresarEmpresa"){
		$nombre=$HTTP_GET_VARS["nombre"];
		
		$sql="insert into empresas(nombre) values ('$nombre');";

		$result = pg_query($sql);

		echo "OK;$nombre";
		return;
	}

	if($operacion=="actualizarRegistro"){	
		$pedido=trim($HTTP_GET_VARS["pedido"]);
		$id_tecnico=trim($HTTP_GET_VARS["id_tecnico"]);
		$nombre_de_la_empresa=$HTTP_GET_VARS["nombre_de_la_empresa"];
		$login_del_asesor=trim($HTTP_GET_VARS["login_del_asesor"]);
		$observaciones=$HTTP_GET_VARS["observaciones"];

		$accion=$HTTP_GET_VARS["accion"];
		$tipo_pendiente=$HTTP_GET_VARS["tipo_pendiente"];
		$id=$HTTP_GET_VARS["id"];
                //$segmento=$HTTP_GET_VARS["segmento"];
                $producto=$HTTP_GET_VARS["producto"];
		$duracion=$HTTP_GET_VARS["duracion"];
		$llamada_id=$HTTP_GET_VARS["llamada_id"];
		//$_SESSION['login'] = $login_del_asesor;
		
		$sql="update registros set pedido='$pedido', id_tecnico='$id_tecnico',empresa='$nombre_de_la_empresa', asesor=upper('$login_del_asesor'), observaciones='$observaciones', accion='$accion', tipo_pendiente='$tipo_pendiente',producto='$producto',duracion='$duracion',llamada_id='$llamada_id' where id=$id";
		
		$result = pg_query($sql);
		$msg="Registro actualizado con EXITO!";

                echo "<script language='javascript'>".
                        "var Backlen=history.length;history.go(-Backlen);".
                        "window.location.href='./registros.php?msg=$msg';".
                        "</script>";
		return;
	}

	if($operacion=="mostrarPlantilla"){
		$msg=$HTTP_GET_VARS["msg"];
	}
	if($operacion=="editarRegistro"){
		$id=$HTTP_GET_VARS["id"];
		
		$sql="select a.pedido,a.id_tecnico,(select nombre from tecnicos where identificacion=a.id_tecnico),(select ciudad from tecnicos where identificacion=a.id_tecnico) as ciudad,a.empresa,a.asesor,a.observaciones,a.accion,a.tipo_pendiente,a.proceso,a.producto,a.duracion,a.llamada_id from registros  a where a.id=$id";

		//echo $sql;
		$result = pg_query($sql);
		$rows=pg_numrows($result);
		
		if($rows<1){
			header("./registros.php?msg=Ocurrio un error con el registro que se intento editar.");
			return;
		}

		$pedido=pg_result($result,0,0);
		$id_tecnico=pg_result($result,0,1);
		$nombre_tecnico=pg_result($result,0,2);
		$ciudad=pg_result($result,0,3);
		$empresa=pg_result($result,0,4);
		$asesor=pg_result($result,0,5);
		$observaciones=pg_result($result,0,6);
		$accion=pg_result($result,0,7);
		$tipo_pendiente=pg_result($result,0,8);
		$proceso=pg_result($result,0,9);
		$producto=pg_result($result,0,10);
		$duracion=pg_result($result,0,11);
		$llamada_id=pg_result($result,0,12);

		//echo "pedido: $pedido";
		$asesorSession=strtoupper($_SESSION['login']);
		if($asesorSession!=$asesor&&$_SESSION['perfil']!="1"){//el actual usuario no es el creador del registro, deshabilito para cualquier otro usuario excepto los admin
			$disabled=" disabled='true'";
		}
	}

?>

<HTML><HEAD>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Edicion de Registros</TITLE>


        <script type="text/javascript" src="./javascript/modalbox/lib/prototype.js"></script>
        <script type="text/javascript" src="./javascript/modalbox/lib/scriptaculous.js?load=effects"></script>

        <script type="text/javascript" src="javascript/modalbox/modalbox.js"></script>
        <link rel="stylesheet" href="javascript/modalbox/modalbox.css" type="text/css" />

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

        var specialChars = "!$^&%()=[]\/{}|<>?";
        for (var i = 0; i < specialChars.length; i++) {
                stringInput = stringInput.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
        }
        return stringInput;
}


	function guardarDatos(){
		var pedido = document.getElementById('pedido');
		pedido = pedido.value;
		var id_tecnico = document.getElementById('id_tecnico');
		id_tecnico = id_tecnico.value;

		var nombre_de_la_empresa = document.getElementById('nombre_de_la_empresa');
		nombre_de_la_empresa = nombre_de_la_empresa.value;
		var login_del_asesor = document.getElementById('login_del_asesor');
		login_del_asesor = login_del_asesor.value;
		var observaciones = document.getElementById('observaciones');
		observaciones = observaciones.value;
		observaciones = rex(observaciones);
		var accion = document.getElementById('accion');
		accion = accion.value;
	
                var producto=document.getElementById('producto');
                producto=producto.value;
		
		if(producto=="-1"){
			alert("Seleccione un producto.");
			return;
		}

		var duracion=document.getElementById("duracion").value;
		var llamada_id=document.getElementById("llamada_id").value;

		var tipo_pendiente='';
		tipo_pendiente=document.getElementById("tipo_pendiente");
		tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;

		//validaciones sobre campos
		if(accion!="Llamada Perdida"){
			if(pedido==""){
				alert("Por favor ingrese un numero de pedido.");
				document.getElementById('pedido').focus();
				return;
			}
			if(id_tecnico==""){
				alert("por favor ingrese la identificacion del tecnico");
				document.getElementById('id_tecnico').focus();
				return;
			}
			if(nombre_de_la_empresa==""){
				alert("Recuerde que debe seleccionar un tecnico de la base de datos o crearlo si no existe.");
				document.getElementById('id_tecnico').focus();
				return;
			}
		}else{
			//pedido="";
			//id_tecnico="";
			//nombre_de_la_empresa="";
		}

		if(login_del_asesor==""){
			alert("por favor ingrese su login");
			document.getElementById('login_del_asesor').focus();
			return;
		}

		var request='&pedido='+pedido+'&id_tecnico='+id_tecnico+'&nombre_de_la_empresa='+nombre_de_la_empresa+'&login_del_asesor='+login_del_asesor+'&accion='+accion+'&tipo_pendiente='+tipo_pendiente+"&producto="+producto+"&duracion="+duracion+'&llamada_id='+llamada_id+'&observaciones='+observaciones;
		location.href='./editar.php?operacion=actualizarRegistro'+request+'&id=<? echo $id; ?>';
		return;
	}

	//AJAX
	function buscarTecnico(){
		//hideMsg();
		var identificacion=document.getElementById("id_tecnico").value;
		if(identificacion==""){
			alert("Por favor ingrese identificacion del tecnico.");
			document.getElementById("id_tecnico").focus();
			return;
		}
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

		
		var url="./editar.php?operacion=buscarTecnico&identificacion="+identificacion;
                http_request.onreadystatechange = recuperarRespuestaBusquedaTecnico;
      		
      		http_request.open('GET', url, true);
      		http_request.send(null);

		
	}


	function ingresarTecnico(){

		var nombre=document.getElementById("nombre_tecnico").value;
		var identificacion=document.getElementById("id_tecnico_ingreso").value;
		var empresa=document.getElementById("empresa_ingreso").value;
		nombre=nombre.toUpperCase();
		var ciudad=document.getElementById("ciudad").value;
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


                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=ingresarTecnico&identificacion="+identificacion+"&nombre="+nombre+"&empresa="+empresa+"&ciudad="+ciudad;
                http_request.onreadystatechange = recuperarRespuestaIngresoTecnico;

                http_request.open('GET', url, true);
                http_request.send(null);

	}

	function recuperarRespuestaIngresoTecnico(){
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var nombre=rta[1];
            			rta2=rta2.replace('\n','');
				

				if(rta2=="SI"){
					var divi=document.getElementById("nombreTecnico");
					var id_tecnico=document.getElementById("id_tecnico");
					var nombre_de_la_empresa=document.getElementById("nombre_de_la_empresa");
					var ciudad=rta[4];
					divi.innerHTML=nombre+" - "+ciudad;
					id_tecnico.value=rta[2];
					nombre_de_la_empresa.value=rta[3];
					document.getElementById("observaciones").focus();
					
            			} else {

					alert("Ocurrio un error al momento de insertar el registro en la base de datos..");
            				
				}
            
           			 
            			//si llego aca se hizo bien la transaccion, ahora se debe calcular de nuevo la extension del turno
				//alert(horarioNuevo+" - "+idturno);
			
         		} else {
            			alert('There was a problem with the request.');
         		}
			Modalbox.hide();
      		}
	}

	function recuperarRespuestaBusquedaTecnico(){
		
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var mensaje=rta[1];
            			rta2=rta2.replace('\n','');
            			//en el array si es un NO viene aparte de la respuesta la identificacion.
				//si es un si viene aparte de la respuesta nombre e identificacion
				if(rta2=="NO"){
					Modalbox.show('ingresoTecnico.php?id_tecnico='+mensaje, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
            				return;
            			} else {
					var divi=document.getElementById("nombreTecnico");
					var ciudad=rta[4]
					divi.innerHTML=mensaje+" - "+ciudad;
					var nombre_de_la_empresa=document.getElementById("nombre_de_la_empresa");
					nombre_de_la_empresa.value=rta[3];
					document.getElementById("observaciones").focus();

				}
            
           			 
            			//si llego aca se hizo bien la transaccion, ahora se debe calcular de nuevo la extension del turno
				//alert(horarioNuevo+" - "+idturno);
			
         		} else {
            			alert('There was a problem with the request.');
         		}
      		}
	}

	//funciones de la pagina de ingreso
	function mostrarFormaIngreso(){
		var divi=document.getElementById('divIngresoEmpresa');
		

		if(divi.style.visibility == "visible"){
			divi.style.visibility="hidden"
			divi.style.position="absolute";
		} else {
			divi.style.visibility="visible"
			divi.style.position="relative";
		}
		
	}

	function ingresarEmpresa(){
		var nombre=document.getElementById("nueva_empresa").value;
		nombre=nombre.toUpperCase();
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

                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./editar.php?operacion=ingresarEmpresa&nombre="+nombre;
                http_request.onreadystatechange = recuperarRespuestaIngresarEmpresa;

                http_request.open('GET', url, true);
                http_request.send(null);
	}


	function recuperarRespuestaIngresarEmpresa(){
		
		if (http_request.readyState == 4) {
         		if (http_request.status == 200) {
            			//alert(http_request.responseText);
            			var result = http_request.responseText;
            			//alert(result);
            			var rta = result.split(";");
            			var rta2=rta[0];
            			var mensaje=rta[1];
            			rta2=rta2.replace('\n','');
				
				//Modalbox.hide();
				var id_tecnico=document.getElementById("id_tecnico_ingreso").value;
				Modalbox.show('ingresoTecnico.php?id_tecnico='+id_tecnico, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
			
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

        function copiarObservacionAprovisionamiento(producto){
                var movilidad=document.getElementById("movilidad");
                var equipo = document.getElementById("equipo");
                var configuracion_acceso = "";
                var realizo_configuracion = document.getElementById("realizo_configuracion");
                
                movilidad=movilidad.options[movilidad.selectedIndex].value;

		if(movilidad=="-1"){
                        alert("Por favor recuerde preguntarle al tecnico si uso Movilidad.");
                        return;
                }


                equipo=equipo.value;

		if(equipo==""){
                        alert("Por favor llenar el campo de equipos.");
                        return;
                }




                if(producto.indexOf("HFC")>=0){

                }else{
                        configuracion_acceso=document.getElementById("configuracion_acceso");
                }
                var observacion="";
                observacion+="*Tecnico Uso Movilidad: "+movilidad;
                observacion+="*Aprovisiono: "+equipo;

                if(configuracion_acceso!=""){
                        if(configuracion_acceso.checked){
                                observacion+="*Configura Tarjeta";
                        }
                }
                if(realizo_configuracion.checked){
                        observacion+="*Configuracion Terminal Remota*";
                }

                var observaciones = document.getElementById("observaciones");
                observaciones.value= observaciones.value+" "+observacion+"*";
                Modalbox.hide();


        }
	
        function copiarObservacionAprovNCA(){

                var internetSelect=document.getElementById("internetSelect");
                var internetMAC = document.getElementById("internetMAC");

                var toipSelect = document.getElementById("toipSelect");
                var toipMAC = document.getElementById("toipMAC");

                var tv1Select = document.getElementById("tv1Select");
                var tv1MAC = document.getElementById("tv1MAC");

                var tv2Select = document.getElementById("tv2Select");
                var tv2MAC = document.getElementById("tv2MAC");

                var tv3Select = document.getElementById("tv3Select");
                var tv3MAC = document.getElementById("tv3MAC");

                var tv4Select = document.getElementById("tv4Select");
                var tv4MAC = document.getElementById("tv4MAC");

                var quedaPendiente=document.getElementById("quedaPendiente").value;

                internetSelect=internetSelect.options[internetSelect.selectedIndex].value;
                internetMAC=internetMAC.value;

                toipSelect=toipSelect.options[toipSelect.selectedIndex].value;
                toipMAC=toipMAC.value;

                tv1Select=tv1Select.options[tv1Select.selectedIndex].value;
                tv1MAC=tv1MAC.value;

                tv2Select=tv2Select.options[tv2Select.selectedIndex].value;
                tv2MAC=tv2MAC.value;

                tv3Select=tv3Select.options[tv3Select.selectedIndex].value;
                tv3MAC=tv3MAC.value;

                tv4Select=tv4Select.options[tv4Select.selectedIndex].value;
                tv4MAC=tv4MAC.value;

                var observacion="";

                observacion+="Se aprovisiona ";

                if(internetSelect!="vacio"&&internetMAC==""){
                        alert("Falta diligenciar la MAC en Internet");
                        return;
                }

                if(toipSelect!="vacio"&&toipMAC==""){
                        alert("Falta diligenciar la MAC en Toip");
                        return;
                }

                if(tv1Select!="vacio"&&tv1MAC==""){
                        alert("Falta diligenciar la MAC en Television Digital(1)");
                        return;
                }

                if(tv2Select!="vacio"&&tv2MAC==""){
                        alert("Falta diligenciar la MAC en Television Digital(2)");
                        return;
                }

                if(tv3Select!="vacio"&&tv3MAC==""){
                        alert("Falta diligenciar la MAC en Television Digital(3)");
                        return;
                }

                if(tv4Select!="vacio"&&tv4MAC==""){
                        alert("Falta diligenciar la MAC en Television Digital(4)");
                        return;
                }

                if(internetSelect!="vacio"){
                        observacion+="-Internet por "+internetSelect+" con la MAC "+internetMAC;
                }
                if(toipSelect!="vacio"){
                        observacion+="-ToIP por "+toipSelect+" con la MAC "+toipMAC;
                }
                if(tv1Select!="vacio"){
                        observacion+="-Deco TV Digital por "+tv1Select+" con la MAC "+tv1MAC;
                }
                if(tv2Select!="vacio"){
                        observacion+="-Deco TV Digital por "+tv2Select+" con la MAC "+tv2MAC;
                }
                if(tv3Select!="vacio"){
                        observacion+="-Deco TV Digital por "+tv3Select+" con la MAC "+tv3MAC;
                }
               if(tv4Select!="vacio"){
                        observacion+="-Deco TV Digital por "+tv4Select+" con la MAC "+tv4MAC;
                }

                if(quedaPendiente!=""){
                        observacion+="-Queda en pediente: "+quedaPendiente;
                }

                var observaciones = document.getElementById("observaciones");
                observaciones.value= observaciones.value+" "+observacion+"-";
                Modalbox.hide();
        }

	//para validar la accion seleccionada, si es un pendiente se despliega la forma de tipos de pendiente
	function validarAccion(){
		
		var acciones=document.getElementById("accion").value;

		var divi=document.getElementById("divPendientes");
		if(acciones=="Pendiente"){//muestre el combo de tipos de pendiente
			divi.style.visibility="visible";
			divi.style.position="relative";
		}else{//para cualquier otra opcion oculto el combo
			divi.style.position="absolute";
			divi.style.visibility="hidden";
		}

                if(acciones=="Cambio de Equipo"){
                        var producto=document.getElementById('producto');
                        producto=producto.options[producto.selectedIndex].value;
			
			if(producto == "-1"){
				alert("Seleccione un producto");
				return;
			}

                        Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }
                if(acciones=="Aprovisionar"){

                        if(producto=="HFC-TV Basica" || producto =="Telefonia Basica"){
                                return;
                        }

                        Modalbox.show('formaDocAprovisionamiento.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }

	}
	

	       function validarAccion2(){
                
		var acciones=document.getElementById("accion");
                acciones=acciones.options[acciones.selectedIndex].value;

                var proceso="<? echo $proceso; ?>";

                var tipo_pendiente=document.getElementById("tipo_pendiente");
                //tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;

                var divi=document.getElementById("divPendientes");

                if(proceso=="Instalaciones"){

                tipo_pendiente.length=0;
                var acciones2=new Array();
                if(acciones=="Cumplir"){

			var producto=document.getElementById("producto");
                        producto=producto.options[producto.selectedIndex].value;

                        acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion","Contingencia(Solo en NCA)");
                        //acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion");

                        //acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion");
                }else if(acciones=="Pendiente"){
                        acciones2=new Array("O-01: Red Pendiente en Edificios y Urbanizaciones","O-06: Pendiente Por Gestion de Instalaciones","O-09: Pendiente por Posteria Madera","O-13: Red Pendiente en Exteriores","O-15: Pendiente por Mala Asignacion","O-24: Pendiente Llegada de Equipos","O-32: Pendiente Antena Outdoor","O-53: Inconsistencia de la Informacion","O-93: Errores en Fenix","O-95: Pendiente Siebel Ventas","O-101: Renumerar o reconfigurar Oferta","O-112: Pendiente por reparacion de red","O-DEV: Estudio Fraude");
                }else if(acciones=="Aprovisionar"){
                        acciones2=new Array(" ");
                }else if(acciones=="Desaprovisonar"){
                        acciones2=new Array(" ");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General","Configuracion CPE y Wifi","Configuracion Puerto Dslam CPE y Wifi","Se envian registros al Equipo Terminal","Configuracion puerto Dslam");
                }else if(acciones=="Solicita Informacion Tecnica"){
                        acciones2=new Array("Sobre el estado pedido","Sobre el producto","Sobre la red","Otro");
                }else if(acciones=="Cambio / Actualizar Red"){
                        acciones2=new Array("Dictar Puente (Cambio En la central)","Llamada enrutada a 2-7","Normalizar Telefono","Despues de cumplido el servicio","Asociar No. A DSLAM","Auxiliar del distribuidor con listado","Cambio de infraestructura");
                }else if(acciones=="Llamada sin gestion"){
                        acciones2=new Array("Sin Sistema","Llamada Caida/Tecnico Cuelga","Redireccionar llamada.");
                }

                if(acciones!="Desaprovisonar" && acciones!="Aprovisionar") {
                        var o=document.createElement('option');
                        o.value="-1";
                        o.text="Seleccione:";
                        tipo_pendiente.options.add(o);
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }else{//no tengo que mostrar el combo
                        divi.style.position="absolute";
                        divi.style.visibility="hidden";

                }

                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];
                        tipo_pendiente.options.add(o);
                }

                }else if(proceso=="Reparaciones" || proceso=="Outlier"){

                tipo_pendiente.length=0;
                var acciones2=new Array();
                if(acciones=="Cumplir"){
                        acciones2=new Array("Cumple parametros de Reparacion","No cumple parametros de reparacion");
                }else if(acciones=="Solicita Informacion Tecnica"){
                        acciones2=new Array("Sobre el estado pedido","Sobre el producto","Sobre la infaestructura de red","Sobre los parametros de red","Otro");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General","Configuracion CPE y Wifi","Configuracion Puerto Dslam CPE y Wifi","Se envian registros al Equipo Terminal","Configuracion puerto Dslam");
                }else if(acciones=="Llamada sin gestion"){
                        acciones2=new Array("Sin Sistema","Llamada Caida/Tecnico No responde","Redireccionar llamada.");
                }else if(acciones=="Cambio / Actualizar Red"){
                        acciones2=new Array("Dictar Puente (Cambio En la central)","Llamada enrutada a 2-7","Asociar No. A DSLAM","Cambio de infraestructura");
                }else if(acciones=="Enrutar/Pendiente"){
                        acciones2=new Array("O-80 Pendiente seguimiento de datos","O-65 Concretar agenda con el cliente","BADEMAN","DIST","PLAIP","PLATCOMU","PLATFOR","PLATRAS","PLATWIMAX","PNOC","READSL","REIP","REPCA","REPCE","REPLI","REPLTE","REPTEX","TSTDA","TVCAB","PFALLASM");
                }else if(acciones=="Manejo del tecnico"){
                        acciones2=new Array(" ");
                }else if(acciones=="Mal escalado"){
                        acciones2=new Array(" ");
                }


                if(acciones!="Manejo del tecnico"&&acciones!="Mal escalado") {
                        var o=document.createElement('option');
                        o.value="-1";
                        o.text="Seleccione:";
                        tipo_pendiente.options.add(o);
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }else{//no tengo que mostrar el combo
                        divi.style.position="absolute";
                        divi.style.visibility="hidden";
                }

                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];
                        tipo_pendiente.options.add(o);
                }

                } else if(proceso=="Asignaciones"){
                        tipo_pendiente.length=0;
                var acciones2=new Array();
                divi.style.position="absolute";
                divi.style.visibility="hidden";

                if(acciones=="Pendiente"){
                        acciones2=new Array("O-01 (Red Pendiente en Edificios y Urbanizaciones)","O-06 (Pendiente Gestion de Instalaciones)","O-07 (Pendiente Inmueble Cerrado)","O-50 (Cliente ilocalizado)","O-08 (Pendiente Orden del Suscriptor)","O-09 (Pendiente Porteria de Madera)","O-101 (Renumerar o reconfigurar Oferta)","O-112 (Pendiente Por Reparacion de Red)","O-13 (Red Pendiente en Exteriores)","O-15 (Pendiente Mala Asignacion)","O-23 (Pendiente Cliente no autoriza/ No contestan)","O-25 (Pendiente Llegada de Equipos)","O-32 (Pendiente antena outdoor)","O-40 (Pendiente Por Orden Publico)","O-53 (Inconsistencia de la informacion)","O-92 (Pendiente Reformas del Suscriptor)","O-300 (Errores no reintentables)","O-238 (No desea el Servicio)","O-93 (Instalado pendiente de cumplido)","O-85 (pendiente de redes)","O-86 (pendiente de nodo XDSL)");
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }else{
			acciones2=new Array(" ");
		}


                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];
                        tipo_pendiente.options.add(o);
                }

                }else if(proceso=="Estudio Tecnico"){
                        //alert("entro");
                        acciones2=new Array("En Análisis de Demanda","En Factibilización Proyecto Exp","En Factibilización Proyecto Ampl","Fin Prospección","Anulada","Auditoria","Cancelada","Cobertura","Construcción","Cumplido","disponible","Incidente","Reconfigurado","Reservado","Prospecto");
                        for (var i=0;i<acciones2.length;i++){
                                var o=document.createElement('option');
                                o.value=acciones2[i];
                                o.text=acciones2[i];
                                tipo_pendiente.options.add(o);
                        }

                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }

                //divi.style.visibility="visible";
                //divi.style.position="relative";

                var producto=document.getElementById("producto");
                producto=producto.options[producto.selectedIndex].value;


                if(acciones=="Aprovisionar"){

                        if(producto=="HFC-TV Basica" || producto =="Telefonia Basica"){
                                return;
                        }
                        if(producto=="-1"){
                                alert("Debe seleccionar un producto!");
                                acciones=document.getElementById("accion");
                                acciones.selectedIndex=0;


                                return;
                        }

                        Modalbox.show('formaDocAprovisionamiento.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }

        }

	//esta validacion es para la carga solamente.
       function validarAccion3(){

                var acciones=document.getElementById("accion");
                acciones=acciones.options[acciones.selectedIndex].value;

		var proceso="<? echo $proceso; ?>";

                var tipo_pendiente=document.getElementById("tipo_pendiente");
                //tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;

                var divi=document.getElementById("divPendientes");

		var pendienteSelected="<? echo $tipo_pendiente; ?>";

                if(proceso=="Instalaciones"){

                tipo_pendiente.length=0;
                var acciones2=new Array();
                if(acciones=="Cumplir"){
			var producto=document.getElementById("producto");
                        producto=producto.options[producto.selectedIndex].value;

			if(producto=="HFC-Internet"){
                                acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion","Contingencia(Solo en NCA)");
                        }else{
                                acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion");
                        }

                        //acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion");
                }else if(acciones=="Pendiente"){
                        acciones2=new Array("O-01: Red Pendiente en Edificios y Urbanizaciones","O-06: Pendiente Por Gestion de Instalaciones","O-09: Pendiente por Posteria Madera","O-13: Red Pendiente en Exteriores","O-15: Pendiente por Mala Asignacion","O-24: Pendiente Llegada de Equipos","O-32: Pendiente Antena Outdoor","O-53: Inconsistencia de la Informacion","O-93: Errores en Fenix","O-95: Pendiente Siebel Ventas","O-101: Renumerar o reconfigurar Oferta","O-112: Pendiente por reparacion de red","O-DEV: Estudio Fraude");
                }else if(acciones=="Aprovisionar"){
                        acciones2=new Array(" ");
                }else if(acciones=="Desaprovisonar"){
                        acciones2=new Array(" ");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General","Configuracion CPE y Wifi","Configuracion Puerto Dslam CPE y Wifi","Se envian registros al Equipo Terminal","Configuracion puerto Dslam");
                }else if(acciones=="Solicita Informacion Tecnica"){
                        acciones2=new Array("Sobre el estado pedido","Sobre el producto","Sobre la red","Otro");
                }else if(acciones=="Cambio / Actualizar Red"){
                        acciones2=new Array("Dictar Puente (Cambio En la central)","Llamada enrutada a 2-7","Normalizar Telefono","Despues de cumplido el servicio","Asociar No. A DSLAM","Auxiliar del distribuidor con listado","Cambio de infraestructura");
                }else if(acciones=="Llamada sin gestion"){
                        acciones2=new Array("Sin Sistema","Llamada Caida/Tecnico Cuelga","Redireccionar llamada.");
                }

                if(acciones!="Desaprovisonar" && acciones!="Aprovisionar") {
                        var o=document.createElement('option');
                        o.value="-1";
                        o.text="Seleccione:";
                        tipo_pendiente.options.add(o);
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }else{//no tengo que mostrar el combo
                        divi.style.position="absolute";
                        divi.style.visibility="hidden";

                }

                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];
			//alert( "acciones2 "+acciones2[i]+" "+pendienteSelected);	
                        if(acciones2[i]==pendienteSelected){
                        	o.selected=true;
                        }

                        tipo_pendiente.options.add(o);
                }

                }else if(proceso=="Reparaciones" || proceso=="Outlier"){

                tipo_pendiente.length=0;
                var acciones2=new Array();
                if(acciones=="Cumplir"){
                        acciones2=new Array("Cumple parametros de Reparacion","No cumple parametros de reparacion");
                }else if(acciones=="Solicita Informacion Tecnica"){
                        acciones2=new Array("Sobre el estado pedido","Sobre el producto","Sobre la infaestructura de red","Sobre los parametros de red","Otro");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General","Configuracion CPE y Wifi","Configuracion Puerto Dslam CPE y Wifi","Se envian registros al Equipo Terminal","Configuracion puerto Dslam");
                }else if(acciones=="Llamada sin gestion"){
                        acciones2=new Array("Sin Sistema","Llamada Caida/Tecnico No responde","Redireccionar llamada.");
                }else if(acciones=="Cambio / Actualizar Red"){
                        acciones2=new Array("Dictar Puente (Cambio En la central)","Llamada enrutada a 2-7","Asociar No. A DSLAM","Cambio de infraestructura");
                }else if(acciones=="Enrutar/Pendiente"){
                        acciones2=new Array("O-80 Pendiente seguimiento de datos","O-65 Concretar agenda con el cliente","BADEMAN","DIST","PLAIP","PLATCOMU","PLATFOR","PLATRAS","PLATWIMAX","PNOC","READSL","REIP","REPCA","REPCE","REPLI","REPLTE","REPTEX","TSTDA","TVCAB");
                }else if(acciones=="Manejo del tecnico"){
                        acciones2=new Array(" ");
                }

                if(acciones!="Manejo del tecnico") {
                        var o=document.createElement('option');
                        o.value="-1";
                        o.text="Seleccione:";
                        tipo_pendiente.options.add(o);
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }else{//no tengo que mostrar el combo
                        divi.style.position="absolute";
                        divi.style.visibility="hidden";
                }

                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];
			if(acciones2[i]==pendienteSelected){
                                o.selected=true;
                        }

			
                        tipo_pendiente.options.add(o);
                }

                } else if(proceso=="Asignaciones"){
                        tipo_pendiente.length=0;
                var acciones2=new Array();
                divi.style.position="absolute";
                divi.style.visibility="hidden";

                if(acciones=="Pendiente"){
                        acciones2=new Array("O-01 (Red Pendiente en Edificios y Urbanizaciones)","O-06 (Pendiente Gestion de Instalaciones)","O-07 (Pendiente Inmueble Cerrado)","O-50 (Cliente ilocalizado)","O-08 (Pendiente Orden del Suscriptor)","O-09 (Pendiente Porteria de Madera)","O-101 (Renumerar o reconfigurar Oferta)","O-112 (Pendiente Por Reparacion de Red)","O-13 (Red Pendiente en Exteriores)","O-15 (Pendiente Mala Asignacion)","O-23 (Pendiente Cliente no autoriza/ No contestan)","O-25 (Pendiente Llegada de Equipos)","O-32 (Pendiente antena outdoor)","O-40 (Pendiente Por Orden Publico)","O-53 (Inconsistencia de la informacion)","O-92 (Pendiente Reformas del Suscriptor)","O-300 (Errores no reintentables)","O-238 (No desea el Servicio)","O-93 (Instalado pendiente de cumplido)","O-85 (pendiente de redes)","O-86 (pendiente de nodo XDSL)");
                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }


                for (var i=0;i<acciones2.length;i++){
                        var o=document.createElement('option');
                        o.value=acciones2[i];
                        o.text=acciones2[i];

			if(acciones2[i]==pendienteSelected){
                                o.selected=true;
                        }

                        tipo_pendiente.options.add(o);
                }

                }else if(proceso=="Estudio Tecnico"){
			tipo_pendiente.length=0;
                	var acciones2=new Array();
                        //alert("entro");
                        acciones2=new Array("En Análisis de Demanda","En Factibilización Proyecto Exp","En Factibilización Proyecto Ampl","Fin Prospección","Anulada","Auditoria","Cancelada","Cobertura","Construcción","Cumplido","disponible","Incidente","Reconfigurado","Reservado","Prospecto");
                        for (var i=0;i<acciones2.length;i++){
                                var o=document.createElement('option');
                                o.value=acciones2[i];
                                o.text=acciones2[i];

				if(acciones2[i]==pendienteSelected){
	                                o.selected=true;
        	                }
                                tipo_pendiente.options.add(o);
                        }

                        divi.style.visibility="visible";
                        divi.style.position="relative";

                }

                //divi.style.visibility="visible";
                //divi.style.position="relative";

                var producto=document.getElementById("producto");
                producto=producto.options[producto.selectedIndex].value;


                if(acciones=="Aprovisionar"){

                        if(producto=="HFC-TV Basica" || producto =="Telefonia Basica"){
                                return;
                        }
                        if(producto=="-1"){
                                alert("Debe seleccionar un producto!");
                                acciones=document.getElementById("accion");
                                acciones.selectedIndex=0;


                                return;
                        }

                        Modalbox.show('formaDocAprovisionamiento.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }

        }


        function checkAccion2(){
                var acciones=document.getElementById("tipo_pendiente");
                acciones=acciones.options[acciones.selectedIndex].value;

                if(acciones=="Cambio de Equipo"){
                        var producto=document.getElementById('producto');
                        producto=producto.options[producto.selectedIndex].value;

                        Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }

		if(acciones=="Contingencia(Solo en NCA)"){
                        Modalbox.show('formaAprovisionamientoNCA.php', {title: 'Contingencia NCA',height: 400, width: 800 });
                }

		
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
		location.href="./registros.php";
	}

</script>

<STYLE type="text/css">
        a:link { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:visited { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:hover { font-weight: bold; font-size: 16px; color: blue; text-decoration: none }
</STYLE>

<SCRIPT language="JavaScript" src="./javascript/calendar.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
</HEAD>

<BODY bgcolor="WHITE">

<DIV id="cal" style="position:absolute; z-index:2;">&nbsp;</DIV>


<FORM name="forma1">
<table width="95%">
<tr><td>

<?
	if($proceso=="Reparaciones")
		$imagen="logo-repa";
	else if($proceso=="Outlier")
                $imagen="logo-outlier";
        else if($proceso=="Asignaciones")
                $imagen="logo-asign";
	else if($proceso=="Estudio Tecnico")
                $imagen="logo-estt";
	else if($proceso=="Plataformas")
                $imagen="logo-plata";
        else
		$imagen="logo-insta";
?>

<div width="100%" class="bannercentral">
<IMG src="./img/<? echo $imagen;?>.png" height="111" width="90%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="./logout.php">Salir</a></font></td></tr>
</table>
<!--CENTER><H1>Seguimiento de Pedidos</H1></CENTER-->

<CENTER><H2>Edicion de Registros</H2></CENTER>

<center>
<div id="divMensajeCentral" <? if($msg!="") echo " style=\"position:relative;visibility:visible;background-color: #FFFEBE;border:2px solid #FFFE88;\""; else echo " style=\"position:absolute;visibility:hidden;\"" ?>>
<font color="red"><b><? echo $msg;?></b></font>
</div>
</center>
<BR>
<TABLE align="center">

<TBODY>

<TR>
        <TD align="left">ID de Llamada</TD>
        <TD align="center"><INPUT type="text" name="llamada_id" id="llamada_id" size="20" value="<? echo $llamada_id; ?>" <? echo $disabled;?>></TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Pedido</TD>
        <TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" value="<? echo $pedido; ?>" onchange="javascript:hideMsg();" <? echo $disabled;?>></TD>
        <TD align="center"></TD>
</TR>

<TR>
	<TD align="left">Identificacion del Tecnico</TD>
	<TD align="center"><INPUT type="text" name="id_tecnico" size="12" id="id_tecnico" value="<? echo $id_tecnico; ?>" <? echo $disabled;?>></TD>
	<TD align="center">
	<div id="divIngreso"><INPUT class="btnpurple" type="button" name="buscar" id="buscar" value="Buscar" onclick="javascript:buscarTecnico();" <? echo $disabled;?>></div>
	</TD>
</TR>
<TR>
<TD align='left'>Nombre del Tecnico</TD>
<TD align='center' colspan="2"><font color='red'><div id="nombreTecnico"><? echo $nombre_tecnico." - ".$ciudad; ?></div></font></TD>
<!--TD align="center"></TD-->
</TR>

<TR>
	<TD align="left">Nombre de la empresa</TD>
	<TD align="center">
		<input type="text" id="nombre_de_la_empresa" disabled="true" value="<? echo $empresa; ?>" size="12">
	</TD>
	<TD align="center"></TD>
</TR>
<TR>
	<TD align="left">Login del asesor</TD>
	<TD align="center"><INPUT type="text" disabled="true" name="login_del_asesor" id="login_del_asesor" size="12" value="<? echo $asesor; ?>"></TD>
	<TD align="center"></TD>
</TR>

<TR>
     <TD align="left"><font color="red"><b>Proceso</b></font></TD>
        <TD align="center"><b><? echo $proceso; ?></b></TD>
</TR>


<TR>
        <TD align="left">Producto</TD>
        <TD align="center">
                <SELECT name="producto" id="producto" <? echo $disabled;?>>
			<option value="-1">Seleccione:</option>
                        <option value="ADSL-Internet" <? if($producto=="ADSL-Internet") echo "selected"; ?>>ADSL-Internet</option>
                        <option value="HFC-Internet" <? if($producto=="HFC-Internet") echo "selected"; ?>>HFC-Internet</option>
                        <option value="ADSL-IPTV" <? if($producto=="ADSL-IPTV") echo "selected"; ?>>ADSL-IPTV</option>
                        <option value="HFC-TV Basica" <? if($producto=="HFC-TV Basica") echo "selected"; ?>>HFC-TV Basica</option>
			<option value="HFC-TV Digital" <? if($producto=="HFC-TV Digital") echo "selected"; ?>>HFC-TV Digital</option>
                        <option value="Telefonia Basica" <? if($producto=="Telefonia Basica") echo "selected"; ?>>Telefonia Basica</option>
                        <option value="Smart-Play" <? if($producto=="Smart-Play") echo "selected"; ?>>Smart-Play</option>
                        <option value="ADSL-ToIP" <? if($producto=="ADSL-ToIP") echo "selected"; ?>>ADSL-ToIP</option>
                        <option value="HFC-ToIP" <? if($producto=="HFC-ToIP") echo "selected"; ?>>HFC-ToIP</option>
                        <option value="4GLTE-Internet" <? if($producto=="4GLTE-Internet") echo "selected"; ?>>4GLTE-Internet</option>
                        <option value="4GLTE-ToIP" <? if($producto=="4GLTE-ToIP") echo "selected"; ?>>4GLTE-ToIP</option>

                        <option value="WIMAX" <? if($producto=="WIMAX") echo "selected"; ?>>WIMAX</option>
			<option value="GPON-Internet" <? if($producto=="GPON-Internet") echo "selected"; ?>>GPON-Internet</option>
			<option value="GPON-IPTV" <? if($producto=="GPON-IPTV") echo "selected"; ?>>GPON-IPTV</option>
			<option value="GPON-ToIP" <? if($producto=="GPON-ToIP") echo "selected"; ?>>GPON-ToIP</option>

                </SELECT>
        </TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Duracion Llamada ACD</TD>
        <TD align="center"><INPUT type="text" name="duracion" disabled="true" id="duracion" size="12" value="<? echo $duracion; ?>" <? echo $disabled;?>></TD>
        <TD align="center"></TD>
</TR>

<TR>
	<TD align="left">Accion</TD>
	<TD align="center">
		<SELECT name="accion" id="accion" onchange="javascript:validarAccion2();" <? echo $disabled;?>>
		</SELECT>
	</TD>
	<TD align="center">
		<div id="divPendientes" <? if($accion!="Pendiente") echo "style='position:absolute;visibility:hidden;'"; ?>>
			<select id="tipo_pendiente" <? echo $disabled;?> onchange="javascript:checkAccion2();">
				<option value="Ninguno"  <? if($tipo_pendiente=="Ninguno") echo "selected"; ?>>No tiene Pendiente</option>

				<option value="O-01 (Red Pendiente en Edificios y Urbanizaciones)" <? if($tipo_pendiente=="O-01 (Red Pendiente en Edificios y Urbanizaciones)") echo "selected"; ?>> O-01 (Red Pendiente en Edificios y Urbanizaciones)</option>
				<option value="O-06 (Pendiente Gestión de Instalaciones)" <? if($tipo_pendiente=="O-06 (Pendiente Gestión de Instalaciones)") echo "selected"; ?>> O-06 (Pendiente Gestión de Instalaciones)</option>
				<option value="O-07 (Pendiente Inmueble Cerrado)" <? if($tipo_pendiente=="O-07 (Pendiente Inmueble Cerrado)") echo "selected"; ?>> O-07 (Pendiente Inmueble Cerrado)</option>
				<option value="O-50 (Cliente ilocalizado)" <? if($tipo_pendiente=="O-50 (Cliente ilocalizado)") echo "selected"; ?>> O-50 (Cliente ilocalizado)</option>
				<option value="O-08 (Pendiente Orden del Suscriptor)" <? if($tipo_pendiente=="O-08 (Pendiente Orden del Suscriptor)") echo "selected"; ?>> O-08 (Pendiente Orden del Suscriptor)</option>
				<option value="O-09 (Pendiente Portería de Madera)" <? if($tipo_pendiente=="O-09 (Pendiente Portería de Madera)") echo "selected"; ?>> O-09 (Pendiente Portería de Madera)</option>
				<option value="O-101 (Renumerar o reconfigurar Oferta)" <? if($tipo_pendiente=="O-101 (Renumerar o reconfigurar Oferta)") echo "selected"; ?>> O-101 (Renumerar o reconfigurar Oferta)</option>
 
 
 
				<option value="O-112 (Pendiente Por Reparación de Red)" <? if($tipo_pendiente=="O-112 (Pendiente Por Reparación de Red)") echo "selected"; ?>> O-112 (Pendiente Por Reparación de Red)</option>
				<option value="O-13 (Red Pendiente en Exteriores)" <? if($tipo_pendiente=="O-13 (Red Pendiente en Exteriores)") echo "selected"; ?>> O-13 (Red Pendiente en Exteriores)</option>
				<option value="O-15 (Pendiente Mala Asignación)" <? if($tipo_pendiente=="O-15 (Pendiente Mala Asignación)") echo "selected"; ?>> O-15 (Pendiente Mala Asignación)</option>
				<option value="O-23 (Pendiente Cliente no autoriza/ No contestan)" <? if($tipo_pendiente=="O-23 (Pendiente Cliente no autoriza/ No contestan)") echo "selected"; ?>> O-23 (Pendiente Cliente no autoriza/ No contestan)</option>
				<option value="O-25 (Pendiente Llegada de Equipos)" <? if($tipo_pendiente=="O-25 (Pendiente Llegada de Equipos)") echo "selected"; ?>> O-25 (Pendiente Llegada de Equipos)</option>
				<option value="O-32 (Pendiente antena outdoor)" <? if($tipo_pendiente=="O-32 (Pendiente antena outdoor)") echo "selected"; ?>> O-32 (Pendiente antena outdoor)</option>
				<option value="O-40 (Pendiente Por Orden Público)" <? if($tipo_pendiente=="O-40 (Pendiente Por Orden Público)") echo "selected"; ?>> O-40 (Pendiente Por Orden Público)</option>
				<option value="O-53 (Inconsistencia de la información)" <? if($tipo_pendiente=="O-53 (Inconsistencia de la información)") echo "selected"; ?>> O-53 (Inconsistencia de la información)</option>
				<option value="O-92 (Pendiente Reformas del Suscriptor)" <? if($tipo_pendiente=="O-92 (Pendiente Reformas del Suscriptor)") echo "selected"; ?>> O-92 (Pendiente Reformas del Suscriptor)</option>
				<option value="O-300 (Errores no reintentables)" <? if($tipo_pendiente=="O-300 (Errores no reintentables)") echo "selected"; ?>> O-300 (Errores no reintentables)</option>
				<option value="O-238 (No desea el Servicio)" <? if($tipo_pendiente=="O-238 (No desea el Servicio)") echo "selected"; ?>> O-238 (No desea el Servicio)</option>
				<option value="0-93 (Instalado pendiente de cumplido)" <? if($tipo_pendiente=="0-93 (Instalado pendiente de cumplido)") echo "selected"; ?>> 0-93 (Instalado pendiente de cumplido)</option>
                                <option value="O-85" <? if($tipo_pendiente=="O-85 (pendiente de redes)") echo "selected"; ?>> O-85 (pendiente de redes)</option>
                                <option value="O-86" <? if($tipo_pendiente=="O-86 (pendiente de nodo XDSL)") echo "selected"; ?>> O-86 (pendiente de nodo XDSL)</option>

			
			</select>
		</div>
	</TD>
</TR>

<TR>
        <TD align="left">Observaciones</TD>
        <TD align="center"> <TEXTAREA name="observaciones" id="observaciones" cols="40" rows="4" <? echo $disabled;?>><? echo $observaciones; ?></TEXTAREA></TD>
        <TD align="center"></TD>
</TR>


</TBODY></TABLE>
<BR>
<CENTER><INPUT class="btnpurple" type="button" value="Guardar" onclick="javascript:guardarDatos();" <? echo $disabled;?>>
&nbsp;<INPUT class="btnpurple" type="button" value="Ver Registros" onclick="javascript:formaRegistros();">
</CENTER>

</FORM>
<br><br>
</BODY>
<script language="javascript">
                var procesoValue="<? echo $proceso; ?>";
		
                var accion=document.getElementById("accion");

		var accioned="<? echo $accion; ?>";

                if(procesoValue=="Instalaciones"){
                        accion.length=0;
                        var accionesInsta=new Array("Cumplir","Pendiente","Aprovisionar","Desaprovisonar","Soporte Tecnico","Solicita Informacion Tecnica","Cambio / Actualizar Red","Llamada sin gestion");

                        for (i=0;i<accionesInsta.length;i++){
                                var o=document.createElement('option');
                                o.value=accionesInsta[i];
                                o.text=accionesInsta[i];

				if(accionesInsta[i]==accioned){
					o.selected=true;
				}

                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-insta.png' height='111' width='90%'>";
                }else if(procesoValue=="Reparaciones" || procesoValue=="Outlier"){
			accion.length=0;
                        var accionesRepa=new Array("Cumplir","Solicita Informacion Tecnica","Soporte Tecnico","Llamada sin gestion","Cambio / Actualizar Red","Enrutar/Pendiente","Manejo del tecnico","Mal escalado");
                        var o=document.createElement('option');
                         o.value="-1";
                         o.text="Seleccione:";
                         accion.options.add(o);

                        for (i=0;i<accionesRepa.length;i++){
                                var o=document.createElement('option');
                                o.value=accionesRepa[i];
                                o.text=accionesRepa[i];

                                if(accionesRepa[i]==accioned){
                                        o.selected=true;
                                }


                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-repa.png' height='111' width='90%'>";
		} else if(procesoValue=="Asignaciones"){
			accion.length=0;

                        var tipo_pendiente=document.getElementById("tipo_pendiente");
                        tipo_pendiente.length=0;

                         var accionesAsig=new Array("Asociar No. a Dslam","Cambio / Act de Armario","Cambio / Act de Pares Primarios","Cambio / Act de Pares Secundarios","Cambio / Act de Posicion Fisica","Cambio / Act Dslam","Cambio / Act Red HFC","Estudio Tecnico","Pendiente","Redireccionar Llamada","Soporte");
                        var o=document.createElement('option');
                         o.value="-1";
                         o.text="Seleccione:";
                         accion.options.add(o);

                        for (i=0;i<accionesAsig.length;i++){
                                var o=document.createElement('option');
                                o.value=accionesAsig[i];
                                o.text=accionesAsig[i];

                                if(accionesAsig[i]==accioned){
                                        o.selected=true;
                                }


                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-asign.png' height='111' width='90%'>";

		}else if(procesoValue=="Pymes Voz y Datos"){//pymes voz y datos
                        accion.length=0;
                        var accionesRepa=new Array("Cumplir","Solicita Informacion Tecnica","Soporte Tecnico","Llamada sin gestion","Cambio / Actualizar Red","Enrutar/Pendiente","Manejo del tecnico");
                        var o=document.createElement('option');
                         o.value="-1";
                         o.text="Seleccione:";
                         accion.options.add(o);

                        for (i=0;i<accionesRepa.length;i++){
                                var o=document.createElement('option');
                                o.value=accionesRepa[i];
                                o.text=accionesRepa[i];

                                if(accionesRepa[i]==accioned){
                                        o.selected=true;
                                }

                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-pvyd.png' height='111' width='90%'>";
                }else if(procesoValue=="Estudio Tecnico"){

                        accion.length=0;
                        var accionesRepa=new Array("Agendado","Auditoria","Cobertura","Construcción","Disponibilidad","Prospección","Red Externa","TV Empresarial");
                        var o=document.createElement('option');
                         o.value="-1";
                         o.text="Seleccione:";
                         accion.options.add(o);

                        for (i=0;i<accionesRepa.length;i++){
                                var o=document.createElement('option');
                                o.value=accionesRepa[i];
                                o.text=accionesRepa[i];
				if(accionesRepa[i]==accioned){
                                        o.selected=true;
                                }
                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-repa.png' height='111' width='90%'>";

                        //14-03-2014: se pide agregar una forma al lado derecho de la pantalla para todas las reparaciones

                        //var divInfoPruebaIntegrada=document.getElementById("divInfoPruebaIntegrada");
                        //divInfoPruebaIntegrada.style.visibility="visible";
                        //divInfoPruebaIntegrada.style.position="relative";
                }


</script>
<script language="javascript">validarAccion3();</script>
</HTML>
