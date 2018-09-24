<?php 

	session_start();
	include_once('conexion.php');
	include_once('autenticacion.php');

	$conexion_bd = getConnection();
	checkout($conexion_bd);
	
?>

<HTML><HEAD>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Nueva Alarma</TITLE>

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
		var nombre_alarma = document.getElementById("nombre_alarma").value;
		var mensaje_alarma = document.getElementById("mensaje_alarma").value;
		var ciudad = document.getElementById("ciudad");
		var proceso = document.getElementById("proceso");
		var producto = document.getElementById("producto");
		var accion = document.getElementById("accion");
		var tipo_pendiente = document.getElementById("tipo_pendiente");
		var campos="";
		ciudad=ciudad.options[ciudad.selectedIndex].value;
		proceso=proceso.options[proceso.selectedIndex].value;
		producto=producto.options[producto.selectedIndex].value;
	
		//alert(accion.length+" - "+accion.options[accion.selectedIndex].value);
		
		if(accion.length!=0){
                        accion=accion.options[accion.selectedIndex].value;
                }else{
                        accion="-1";
                }

		//accion=accion.options[accion.selectedIndex].value;
	
		//alert(">"+producto+"<");
	
		var sep="";
		
		if(tipo_pendiente.length!=0){
			tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;
		}else{
			tipo_pendiente="-1";
		}

		if(ciudad=="" || ciudad=="-1"){
			ciudad="NULL";
		}else{
			campos=sep+"ciudad";
			sep=",";
		}

		if(proceso=="" || proceso=="-1"){
                        proceso="NULL";
                }else{
			campos=campos+sep+"proceso";
			sep=",";
		}
		if(producto=="" || producto=="-1"){
                        producto="NULL";
                }else{
			campos=campos+sep+"tecnologia_producto";
			sep=",";
		}
		if(accion=="" || accion=="-1"){
                        accion="NULL";
                }else{
			campos=campos+sep+"accion";
			sep=",";
		}
		if(tipo_pendiente=="" || tipo_pendiente=="-1"){
                        tipo_pendiente="NULL";
                }else{
			campos=campos+sep+"subaccion";
			sep=",";
		}


		//var piloto=document.getElementById("piloto").value;
		var request='&nombre_alarma='+nombre_alarma+'&mensaje_alarma='+mensaje_alarma+'&ciudad='+ciudad+'&proceso='+proceso+'&accion='+accion+'&tipo_pendiente='+tipo_pendiente+'&campos='+campos+"&producto="+producto;
		//alert(request);
		location.href='./listadoAlarmas.php?operacion=insertarAlarma'+request;
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
      		} else  if (window.ActiveXObject) { // IE
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

		
		var url="./actividades.php?operacion=buscarTecnico&identificacion="+identificacion;
                http_request.onreadystatechange = recuperarRespuestaBusquedaTecnico;
      		
      		http_request.open('GET', url, true);
      		http_request.send(null);

		
	}
	
	function modificarInformacionTecnico(){
		var identificacion=document.getElementById("id_tecnico").value;
		Modalbox.show('modificarTecnico.php?id_tecnico='+identificacion, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
		
	}

	function ingresarTecnico(){

		var nombre=document.getElementById("nombre_tecnico").value;
		var identificacion=document.getElementById("id_tecnico_ingreso").value;


		if(!isNumber(identificacion)){
			alert("Ingrese un dato numerico para la identificacion del tecnico, con 6 o mas digitos");
                	document.getElementById('id_tecnico_ingreso').focus();
	       		return;

		}
		
		if(nombre==''){
			alert("El campo nombre del tecnico no puede ir en blanco.");
                        document.getElementById('nombre_tecnico').focus();
                        return;
		}


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
                var url="./actividades.php?operacion=ingresarTecnico&identificacion="+identificacion+"&nombre="+nombre+"&empresa="+empresa+"&ciudad="+ciudad;
                http_request.onreadystatechange = recuperarRespuestaIngresoTecnico;

                http_request.open('GET', url, true);
                http_request.send(null);

	}

	function doModificarTecnico(old_identificacion){
		
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
                var url="./actividades.php?operacion=doModificarTecnico&identificacion="+identificacion+"&nombre="+nombre+"&empresa="+empresa+"&ciudad="+ciudad+"&old_identificacion="+old_identificacion;
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
            			rta2=rta2.replace(/(\r\n|\n|\r)/gm,"");
				

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
            			rta2=rta2.replace(/(\r\n|\n|\r)/gm,"");//para quitarle los enter a la cadena
				
				//alert("rta: "+rta2+" men: "+mensaje);

            			//en el array si es un NO viene aparte de la respuesta la identificacion.
				//si es un si viene aparte de la respuesta nombre e identificacion
				if(rta2=="NO"){
					Modalbox.show('ingresoTecnico.php?id_tecnico='+mensaje, {title: 'Ingreso de Tecnicos',height: 400, width: 500 });
					
					//ingresarTecnico();
					/*
            				var nombre=prompt("El tecnico con identificacion "+mensaje+" No existe en la base de datos, si desea ingresarlo por favor ingrese el nombre:");
					if(nombre!=""&&nombre!=null&&nombre!="null"){
						ingresarTecnico(nombre,mensaje);
					}*/
            				return;
            			} else {
					var divi=document.getElementById("nombreTecnico");
					var ciudad=rta[4]
					divi.innerHTML=mensaje+" - "+ciudad+" - <a href='javascript:modificarInformacionTecnico();'>editar</a>";
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
                var url="./actividades.php?operacion=ingresarEmpresa&nombre="+nombre;
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


        function copiarObservacionReptex(){
                var obsoleto=document.getElementById("obsoleto");
                var tipotap = document.getElementById("tipotap");
                var canalesbajos = document.getElementById("canalesbajos").value;
                var canalesaltos = document.getElementById("canalesaltos").value;
                var macdme = document.getElementById("macdme").value;
                var dp = document.getElementById("dp").value;
                var ds = document.getElementById("ds").value;
                var up = document.getElementById("up").value;
                var us = document.getElementById("us").value;
                var tapestado = document.getElementById("tapestado");
                var asociadofallamasiva = document.getElementById("asociadofallamasiva");

		if(macdme.length==12){//aparentemente una mac sin las separaciones
			macdme=macdme.substring(0,2)+":"+macdme.substring(2,4)+":"+macdme.substring(4,6)+":"+macdme.substring(6,8)+":"+macdme.substring(8,10)+":"+macdme.substring(10,12);
		}else{
			//esperar que dice andres julian de la condicion...
		}

		//alert("LA MAC: "+macdme);


		//var regex = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/;
	
		
	
		//if(!regex.test(macdme)){
			//alert("La MAC Address no tiene el formato correcto: pares de datos separados por dos puntos(:) o guion (-) ");
			//return;
		//}
		
                obsoleto=obsoleto.options[obsoleto.selectedIndex].value;
		tipotap=tipotap.options[tipotap.selectedIndex].value;
      
	        if(tapestado.checked){
                        tapestado="* TAP en mal estado ";
                }else
			tapestado="";
		if(asociadofallamasiva.checked){
                        asociadofallamasiva="* Asociado a Falla Masiva ";
                }else
			asociadofallamasiva="";


		var observacion="";

		if(obsoleto=="SI"){
			alert("No se puede efectuar esta operacion si el Cable Modem esta obsoleto.");
			return;
		}else{
			observacion+="* Se verifico que el cablemodem no es obsoleto ";
		}

                observacion+="* Tipo de TAP: "+tipotap;
		observacion+="* Parametros RF: Canales Bajos: "+canalesbajos+" - Canales Altos: "+canalesaltos;
		observacion+="* MAC DME: "+macdme;
		observacion+="* Parámetros CM : DP: "+dp+" - DS: "+ds+" - UP: "+up+" - US: "+us;
		observacion+=tapestado;
		observacion+=asociadofallamasiva;

                var observaciones = document.getElementById("observaciones");
                observaciones.value= observaciones.value+" "+observacion+"*";
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

	//	return;

		var producto=document.getElementById("producto");
		producto=producto.options[producto.selectedIndex].value;
		
		if(producto=='CDMA'||producto=='WIMAX'||producto=='TO'){
			return;
		}

		//producto=producto.options[producto.selectedIndex].value;
		if(acciones=="Cambio de Equipo"){
			//Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
		}

		if(acciones=="Aprovisionar"){

			if(producto=="HFC-TV Basica" || producto =="Telefonia Basica"){
				return;
			}

                        //Modalbox.show('formaDocAprovisionamiento.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }
	}
	
        function validarAccion2(){

                var acciones=document.getElementById("accion");
		acciones=acciones.options[acciones.selectedIndex].value;


		var proceso=document.getElementById("proceso");
                var proceso=proceso.options[proceso.selectedIndex].value;


		var tipo_pendiente=document.getElementById("tipo_pendiente");
		//tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;

		var divi=document.getElementById("divPendientes");

		if(proceso=="Instalaciones"){

		tipo_pendiente.length=0;
		var acciones2=new Array();
		if(acciones=="Cumplir"){

			var producto=document.getElementById("producto");
	                producto=producto.options[producto.selectedIndex].value;
			
			var hfc="";
			acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion","Contingencia(Solo en NCA)");
			//acciones2=new Array("Cumple parametros de Instalacion","No cumple parametros de instalacion");
			
		}else if(acciones=="Pendiente"){
                        acciones2=new Array("O-01: Red Pendiente en Edificios y Urbanizaciones","O-06: Pendiente Por Gestion de Instalaciones","O-09: Pendiente por Posteria Madera","O-13: Red Pendiente en Exteriores","O-15: Pendiente por Mala Asignacion","O-24: Pendiente Llegada de Equipos","O-32: Pendiente Antena Outdoor","O-53: Inconsistencia de la Informacion","O-93: Errores en Fenix","O-95: Pendiente Siebel Ventas","O-101: Renumerar o reconfigurar Oferta","O-112: Pendiente por reparacion de red","O-DEV: Estudio Fraude");
                }else if(acciones=="Aprovisionar"){
			acciones2=new Array(" ");
		}else if(acciones=="Desaprovisonar"){
                        acciones2=new Array(" ");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General");
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
			
		}else if(proceso=="Reparaciones" || proceso=="RETEN-TE"){

                tipo_pendiente.length=0;
                var acciones2=new Array();
                if(acciones=="Cumplir"){
                        acciones2=new Array("Cumple parametros de Reparacion","No cumple parametros de reparacion");
                }else if(acciones=="Solicita Informacion Tecnica"){
                        acciones2=new Array("Sobre el estado pedido","Sobre el producto","Sobre la infaestructura de red","Sobre los parametros de red","Otro");
                }else if(acciones=="Soporte Tecnico"){
                        acciones2=new Array("Cambio de Equipo","General");
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

                        //Modalbox.show('formaDocAprovisionamiento.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }
		
        }


	function checkAccion2(){
                var acciones=document.getElementById("tipo_pendiente");
                acciones=acciones.options[acciones.selectedIndex].value;
		


		if(acciones=="Cambio de Equipo"){
	                var producto=document.getElementById('producto');
                	producto=producto.options[producto.selectedIndex].value;
			
			if(producto=="-1"){ 
				alert("Por favor seleccione un producto");
				return;
			}

                        //Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
                }
		if(acciones=="REPTEX"){
	                var producto=document.getElementById('producto');
                        producto=producto.options[producto.selectedIndex].value;

                        if(producto=="-1"){
                                alert("Por favor seleccione un producto");
                                return;
                        }

                        //Modalbox.show('formaReptex.php', {title: 'REPTEX',height: 400, width: 800 });
               
		}
		if(acciones=="Contingencia(Solo en NCA)"){
			//Modalbox.show('formaAprovisionamientoNCA.php', {title: 'Contingencia NCA',height: 600, width: 800 });
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
		
		//if(user==""){
		//	user=prompt("Ingrese su nombre de usuario.");
		//	if(user==""||user=="null"||user==null){
		//		alert("No se puede llevar a cabo la operacion, debe ingresar su nombre de usuario.");
		//		return;
		//	}
		//}
		location.href="./registros.php";
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


	function selectProceso(){
		var proceso=document.getElementById("proceso");
		var procesoValue=proceso.options[proceso.selectedIndex].value;

		if(procesoValue=="-1"){
			alert("Seleccione una opcion para el proceso.");
			return;
		}
		var accion=document.getElementById("accion");
		if(procesoValue=="Asignaciones"){
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
                                accion.options.add(o);
                        }
                        //var banner=document.getElementById("bannercentral");
                        //banner.innerHTML="<IMG src='./img/logo-asign.png' height='111' width='90%'>";

		} else if(procesoValue=="Instalaciones"){
			accion.length=0;
			var accionesInsta=new Array("Cumplir","Pendiente","Aprovisionar","Desaprovisonar","Soporte Tecnico","Solicita Informacion Tecnica","Cambio / Actualizar Red","Llamada sin gestion");

			var o=document.createElement('option');
                         o.value="-1";
                         o.text="Seleccione:";
                         accion.options.add(o);
	
			for (i=0;i<accionesInsta.length;i++){
				var o=document.createElement('option');
				o.value=accionesInsta[i];
				o.text=accionesInsta[i];
				accion.options.add(o);
			}
			//var banner=document.getElementById("bannercentral");
			//banner.innerHTML="<IMG src='./img/logo-insta.png' height='111' width='90%'>";
		}else if(procesoValue=="Reparaciones" ||procesoValue=="RETEN-TE"){//acciones de reparaciones
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
                                accion.options.add(o);
                        }
                        //var banner=document.getElementById("bannercentral");
                        //banner.innerHTML="<IMG src='./img/logo-repa.png' height='111' width='90%'>";
		} else if(procesoValue=="Pymes Voz y Datos"){//pymes voz y datos
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
                                accion.options.add(o);
                        }
                        //var banner=document.getElementById("bannercentral");
                        //banner.innerHTML="<IMG src='./img/logo-pvyd.png' height='111' width='90%'>";
		}

		var tipo_pendiente=document.getElementById("tipo_pendiente");
		tipo_pendiente.length=0;
	}

</script>

<STYLE type="text/css">
        a:link { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:visited { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        a:hover { font-weight: bold; font-size: 16px; color: blue; text-decoration: none }
</STYLE>

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

<SCRIPT language="JavaScript" src="./javascript/calendar.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
</HEAD>

<BODY bgcolor="WHITE">
<input type="hidden" value="" id="initTime">
<input type="hidden" value="" id="initFecha">
<input type="hidden" value="false" id="inicioLlamada">

<DIV id="cal" style="position:absolute; z-index:2;">&nbsp;</DIV>


<FORM name="forma1">
<table width="95%">
<tr><td>
<div width="100%" class="bannercentral" id="bannercentral">
<IMG src="./img/logo-inicial.png" height="111" width="90%">
</div>
</td></tr>
<tr><td align="right"><b><font color="blue"><? echo $_SESSION["nombre"];?></font></b>&nbsp;&nbsp;<font color="red"><a href="./logout.php">Salir</a></font></td></tr>
</table>

<CENTER><H2>Nueva Alarma</H2></CENTER>

<BR>
<TABLE align="center">

<TBODY>

<TR>
<TD align='left'>Nombre de la alarma</TD>
<TD align='center' colspan="2"><input type="text" id="nombre_alarma" size="20"></TD>
</TR>

<TR>
	<TD align="left">Mensaje</TD>
	<TD align="center" colspan='2'>
		<TEXTAREA name="mensaje_alarma" id="mensaje_alarma" cols="40" rows="4"></textarea>
	</TD>
</TR>

<TR>
        <TD align="left">Ciudad</TD>
        <TD align="center" colspan='2'>
                <SELECT name="ciudad" id="ciudad">
                        <option value="-1">Seleccione:</option>
			        <option value="MEDELLIN">MEDELLIN</option>
			        <option value="ARMENIA">ARMENIA</option>
			        <option value="BARRANCABERMEJA">BARRANCABERMEJA</option>
			        <option value="BARRANQUILLA">BARRANQUILLA</option>
			        <option value="BOGOTA">BOGOTA</option>
			        <option value="BUCARAMANGA">BUCARAMANGA</option>
			        <option value="BUGA">BUGA</option>
			        <option value="CALI">CALI</option>
			        <option value="CARTAGENA">CARTAGENA</option>
			        <option value="CUCUTA">CUCUTA</option>
			        <option value="IBAGE">IBAGE</option>
			        <option value="MANIZALES">MANIZALES</option>
			        <option value="NEIVA">NEIVA</option>
			        <option value="PALMIRA">PALMIRA</option>
			        <option value="PASTO">PASTO</option>
			        <option value="PEREIRA">PEREIRA</option>
			        <option value="POPAYAN">POPAYAN</option>
			        <option value="SANTAMARTA">SANTAMARTA</option>
			        <option value="TUNJA">TUNJA</option>
			        <option value="VILLAVICENCIO">VILLAVICENCIO</option>
                </SELECT>
        </TD>
</TR>

<TR>
        <TD align="left"><font color="red"><b>Proceso</b></font></TD>
        <TD align="center" colspan='2'><select id="proceso" onchange="javascript:selectProceso();"><option value="-1">Seleccione: </option>
        <? if($_SESSION['perfil']=='4'){
                echo "<option value='Asignaciones'>Asignaciones </option>";
           }else {
                echo "<option value='Instalaciones'>Instalaciones </option><option value='Reparaciones'>Reparaciones </option><!--option value='RETEN-TE'>RETEN-TE </option-->";
        }
?>

</select></TD>
</TR>


<TR>
        <TD align="left">Producto</TD>
        <TD align="center" colspan='2'>
                <SELECT name="producto" id="producto">
			<option value="-1">Seleccione:</option>
			
			<option value="ADSL-Internet">ADSL-Internet</option>
			<option value="HFC-Internet">HFC-Internet</option>
			<option value="ADSL-IPTV">ADSL-IPTV</option>
			<option value="HFC-TV Basica">HFC-TV Básica</option>
			<option value="HFC-TV Digital">HFC-TV Digital</option>
			<option value="Telefonia Basica">Telefonia Basica</option>
			<option value="Smart-Play">Smart-Play</option>
			<option value="ADSL-ToIP">ADSL-ToIP</option>
			<option value="HFC-ToIP">HFC-ToIP</option>
			<option value="4GLTE-Internet">4GLTE-Internet</option>
			<option value="4GLTE-ToIP">4GLTE-ToIP</option>
			<option value="WIMAX">WIMAX</option>
			<option value="GPON">GPON</option>
                </SELECT>
        </TD>
</TR>

<TR>
	<TD align="left">Accion</TD>
	<TD align="center">
		<SELECT name="accion" id="accion" onchange="javascript:validarAccion2();">
		</SELECT>
	</TD>
	<TD align="center">
		<div id="divPendientes" style="position:absolute;visibility:hidden;">
			<select id="tipo_pendiente" onchange="javascript:checkAccion2();">
				<option value="Ninguno">No tiene Pendiente</option>

				<option value="O-01 (Red Pendiente en Edificios y Urbanizaciones)"> O-01 (Red Pendiente en Edificios y Urbanizaciones)</option>
				<option value="O-06 (Pendiente Gestión de Instalaciones)"> O-06 (Pendiente Gestión de Instalaciones)</option>
				<option value="O-07 (Pendiente Inmueble Cerrado)"> O-07 (Pendiente Inmueble Cerrado)</option>
				<option value="O-50 (Cliente ilocalizado)"> O-50 (Cliente ilocalizado)</option>
				<option value="O-08 (Pendiente Orden del Suscriptor)"> O-08 (Pendiente Orden del Suscriptor)</option>
				<option value="O-09 (Pendiente Portería de Madera)"> O-09 (Pendiente Portería de Madera)</option>
				<option value="O-101 (Renumerar o reconfigurar Oferta)"> O-101 (Renumerar o reconfigurar Oferta)</option>
				<option value="O-112 (Pendiente Por Reparación de Red)"> O-112 (Pendiente Por Reparación de Red)</option>
				<option value="O-13 (Red Pendiente en Exteriores)"> O-13 (Red Pendiente en Exteriores)</option>
				<option value="O-15 (Pendiente Mala Asignación)"> O-15 (Pendiente Mala Asignación)</option>
				<option value="O-23 (Pendiente Cliente no autoriza/ No contestan)"> O-23 (Pendiente Cliente no autoriza/ No contestan)</option>
				<option value="O-25 (Pendiente Llegada de Equipos)"> O-25 (Pendiente Llegada de Equipos)</option>
				<option value="O-32 (Pendiente antena outdoor)"> O-32 (Pendiente antena outdoor)</option>
				<option value="O-40 (Pendiente Por Orden Público)"> O-40 (Pendiente Por Orden Público)</option>
				<option value="O-53 (Inconsistencia de la información)"> O-53 (Inconsistencia de la información)</option>
				<option value="O-92 (Pendiente Reformas del Suscriptor)"> O-92 (Pendiente Reformas del Suscriptor)</option>
				<option value="O-300 (Errores no reintentables)"> O-300 (Errores no reintentables)</option>
				<option value="O-238 (No desea el Servicio)"> O-238 (No desea el Servicio)</option>
				<option value="O-93 (Instalado pendiente de cumplido)"> O-93 (Instalado pendiente de cumplido)</option>
				<option value="O-85 (pendiente de redes)"> O-85 (pendiente de redes)</option>
				<option value="O-86 (pendiente de nodo XDSL)"> O-86 (pendiente de nodo XDSL)</option>
			
			</select>
		</div>
	</TD>
</TR>

</TBODY></TABLE>
<BR>
<CENTER><INPUT type="button" value="Guardar" onclick="javascript:guardarDatos();">
&nbsp;<INPUT type="button" value="Cancelar" onclick="javascript:location.href='./listadoAlarmas.php';">
</CENTER>



</FORM>

</BODY>

</HTML>
