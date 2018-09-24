<?php 

	session_start();
	include_once('conexion.php');
	include_once('autenticacion.php');

	$conexion_bd = getConnection();
	checkout($conexion_bd);
	
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


        if($operacion=="doModificarTecnico"){
                $identificacion=$HTTP_GET_VARS["identificacion"];
                $nombre=$HTTP_GET_VARS["nombre"];
                $empresa=$HTTP_GET_VARS["empresa"];
                $ciudad=$HTTP_GET_VARS["ciudad"];
		$oldIdentificacion=$HTTP_GET_VARS["old_identificacion"];		
		

		$sql="update tecnicos set identificacion='$identificacion',nombre='$nombre',empresa='$empresa',ciudad='$ciudad' where identificacion='$oldIdentificacion'";
                //$sql="insert into tecnicos(identificacion,nombre,empresa,ciudad) values ('$identificacion','$nombre',$empresa,'$ciudad');";

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

	if($operacion=="insertarRegistro"){
		
		$pedido=trim($HTTP_GET_VARS["pedido"]);
		$id_tecnico=trim($HTTP_GET_VARS["id_tecnico"]);
		$nombre_de_la_empresa=$HTTP_GET_VARS["nombre_de_la_empresa"];
		$login_del_asesor=trim($HTTP_GET_VARS["login_del_asesor"]);
		$observaciones=$HTTP_GET_VARS["observaciones"];
		$accion=$HTTP_GET_VARS["accion"];
		$tipo_pendiente=$HTTP_GET_VARS["tipo_pendiente"];
		//$piloto=$HTTP_GET_VARS["piloto"];
		$proceso=$HTTP_GET_VARS["proceso"];
		$llamada_id=$HTTP_GET_VARS["llamada_id"];
		$producto=$HTTP_GET_VARS["producto"];
		//$_SESSION['login'] = $login_del_asesor;
		//$_SESSION['piloto'] = $piloto;
		$duracion=$HTTP_GET_VARS["duracion"];
		$fetcha=$HTTP_GET_VARS["fecha"];
		$pruebaintegrada = $HTTP_GET_VARS["pruebaintegrada"];
		$codigofamiliar = $HTTP_GET_VARS["codigofamiliar"];
		$smartplay = $HTTP_GET_VARS["smartplay"];
		$toip = $HTTP_GET_VARS["toip"];
		$inter = $HTTP_GET_VARS["inter"];
		$iptv = $HTTP_GET_VARS["iptv"];
		$telev = $HTTP_GET_VARS["telev"];
		$totdm = $HTTP_GET_VARS["totdm"];

		$allpruebaintegrada="pruebaintegrada=$pruebaintegrada;codigofamiliar=$codigofamiliar;smartplay=$smartplay;toip=$toip;inter=$inter;iptv=$iptv;telev=$telev;totdm=$totdm;";

		//antes de hacer la insercion remuevo caracteres extranos de las observaciones:
		$foreign_chars=array("%","@","'","\"");
		$observaciones=str_replace($foreign_chars,"",$observaciones);
		
		if($proceso=='Reparaciones'){
			$sql="INSERT INTO registros (pedido, id_tecnico, empresa, asesor, observaciones, accion,tipo_pendiente,proceso,producto,duracion,llamada_id,fecha,prueba_integrada,codigo_familiar,smartplay,toip,inter,iptv,telev,totdm) VALUES ('$pedido', '$id_tecnico', '$nombre_de_la_empresa', upper('$login_del_asesor'), '$observaciones', '$accion','$tipo_pendiente','$proceso','$producto','$duracion','$llamada_id','$fetcha','$pruebaintegrada','$codigofamiliar','$smartplay','$toip','$inter','$iptv','$telev','$totdm')";
		}else {
			$sql="INSERT INTO registros (pedido, id_tecnico, empresa, asesor, observaciones, accion,tipo_pendiente,proceso,producto,duracion,llamada_id,fecha) VALUES ('$pedido', '$id_tecnico', '$nombre_de_la_empresa', upper('$login_del_asesor'), '$observaciones', '$accion','$tipo_pendiente','$proceso','$producto','$duracion','$llamada_id','$fetcha')";
		}
		$result = pg_query($sql);
		

		$msg="Registro guardado con EXITO!";

                echo "<script language='javascript'>".
			"var Backlen=history.length;history.go(-Backlen);".
                        "window.location.href='./actividades.php?operacion=mostrarPlantilla&msg=$msg';".
                        "</script>";
		return;
	}

	if($operacion=="mostrarPlantilla"){
		$msg=$HTTP_GET_VARS["msg"];
	}
	
	if($operacion=="doCheckAlarma"){
		$arrayForma=array("ciudad", "proceso", "tecnologia_producto", "accion", "subaccion" );

                $ciudad=$HTTP_GET_VARS["ciudad"];
		$proceso=$HTTP_GET_VARS["proceso"];
		$producto=$HTTP_GET_VARS["producto"];
		$accion=$HTTP_GET_VARS["accion"];
		$subaccion=$HTTP_GET_VARS["subaccion"];

		$nivel=$HTTP_GET_VARS["nivel"];

		if($nivel=="1"){
			$sql="select cantidad_campos,ciudad,proceso,tecnologia_producto,accion,subaccion,nombre_alarma,mensaje from alarmas where ciudad='$ciudad' and proceso='NULL' and tecnologia_producto='NULL' and accion='NULL' and subaccion='NULL'";
		}
		if($nivel=="2"){
                        $sql="select cantidad_campos,ciudad,proceso,tecnologia_producto,accion,subaccion,nombre_alarma,mensaje from alarmas where ((ciudad='$ciudad' and cantidad_campos!='ciudad') or proceso='$proceso') and tecnologia_producto='NULL' and accion='NULL' and subaccion='NULL'";
                }
		if($nivel=="3"){
                        $sql="select cantidad_campos,ciudad,proceso,tecnologia_producto,accion,subaccion,nombre_alarma,mensaje from alarmas where ((ciudad='$ciudad' and cantidad_campos!='ciudad') or (proceso='$proceso' and cantidad_campos!='proceso') or tecnologia_producto='$producto') and accion='NULL' and subaccion='NULL'";
                }
		if($nivel=="4"){
                        $sql="select cantidad_campos,ciudad,proceso,tecnologia_producto,accion,subaccion,nombre_alarma,mensaje from alarmas where ((ciudad='$ciudad' and cantidad_campos!='ciudad') or (proceso='$proceso' and cantidad_campos!='proceso') or (tecnologia_producto='$producto' and cantidad_campos!='tecnologia_producto') or accion='$accion') and subaccion='NULL'";
                }
		if($nivel=="5"){
                        $sql="select cantidad_campos,ciudad,proceso,tecnologia_producto,accion,subaccion,nombre_alarma,mensaje from alarmas where ((ciudad='$ciudad' and cantidad_campos!='ciudad') or (proceso='$proceso' and cantidad_campos!='proceso') or (tecnologia_producto='$producto' and cantidad_campos!='tecnologia_producto') or (accion='$accion' and cantidad_campos!='accion') or subaccion='$subaccion' )";
                }
		
		$arrayForma["ciudad"]=$ciudad;
		$arrayForma["proceso"]=$proceso;
		$arrayForma["tecnologia_producto"]=$producto;
		$arrayForma["accion"]=$accion;
		$arrayForma["subaccion"]=$subaccion;

		$result = pg_query($sql);
                $rows=pg_numrows($result);

                if($rows<=0){
                //devolver sin ningun mensaje
                        echo "NO;$sql;ERROR";
                } else {
			//devolver el mensaje y nombre de la alarma
			//hay que hacer validacion para cada una de las alarmas,
			// para verificar que coinciden con los campos que se seleccionaron en la forma
			$mensaje_alarma="";
			for($k=0;$k<$rows;$k++){
				$cantidad_campos=pg_result($result,$k,0);//deberia venir el listado de campos???
				$arrays=array("ciudad", "proceso", "tecnologia_producto", "accion", "subaccion" );
				$arrays["ciudad"]=pg_result($result,$k,1);
				$arrays["proceso"]=pg_result($result,$k,2);
				$arrays["tecnologia_producto"]=pg_result($result,$k,3);
				$arrays["accion"]=pg_result($result,$k,4);
				$arrays["subaccion"]=pg_result($result,$k,5);
				$nombre_alarma=pg_result($result,$k,6);
				$mensaje=pg_result($result,$k,7);
				
				$camposAlarma=split(",",$cantidad_campos);

				$countCampos=count($camposAlarma);
				$validator=1;
				$msg="";
				for($b=0;$b<$countCampos;$b++){
					//echo "campoalarma: ".$arrays[$camposAlarma[$b]]." vs campoforma: ".$arrayForma[$camposAlarma[$b]];
					if($arrays[$camposAlarma[$b]]==$arrayForma[$camposAlarma[$b]]){
						$validator=0;
					}else{
						$validator=1;
						break;
					}
				}
				if($validator==0){
					$msg="\n$nombre_alarma: $mensaje.";
					$mensaje_alarma=$mensaje_alarma.$msg;
				}

			}
                        //$nombre_alarma=pg_result($result,0,6);
			//$mensaje_alarma=pg_result($result,0,7);
			if($mensaje_alarma==""){
				echo "NO;$sql;ERROR";
			}else{
	                        echo "SI;$mensaje_alarma;";
			}
                }

		
//		echo $sql;

		return;

        }

?>


<HTML><HEAD>

<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Actividades</TITLE>

        <script type="text/javascript" src="./javascript/modalbox/lib/prototype.js"></script>
        <script type="text/javascript" src="./javascript/modalbox/lib/scriptaculous.js?load=effects"></script>

        <script type="text/javascript" src="javascript/modalbox/modalbox.js"></script>
        <link rel="stylesheet" href="javascript/modalbox/modalbox.css" type="text/css" />

        <script type="text/javascript" src="./javascript/jquery.min.js"></script>
        <script type="text/javascript" src="./javascript/jquery.blockUI.js?v2.38"></script>

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

		var id_tecnico = document.getElementById('id_tecnico');
		id_tecnico = id_tecnico.value;

		if(!isNumber(id_tecnico)){
                        alert("Ingrese un dato numerico para la identificacion del tecnico.");
                        document.getElementById('id_tecnico').focus();
                        return;
                }else{//es numerico!
			
			if(id_tecnico.length<6||id_tecnico.length>12){
				
				if(id_tecnico!='0'){
					alert("Ingrese un dato numerico para la identificacion del tecnico, con 6 o mas digitos");
        	                	document.getElementById('id_tecnico').focus();
                	        	return;
				}
			}
		}

		var llamada_id=document.getElementById('llamada_id').value;

		var nombre_de_la_empresa = document.getElementById('nombre_de_la_empresa');
		nombre_de_la_empresa = nombre_de_la_empresa.value;
		var login_del_asesor = document.getElementById('login_del_asesor');
		login_del_asesor = login_del_asesor.value;
		var observaciones = document.getElementById('observaciones');
		observaciones = observaciones.value;
		observaciones = rex(observaciones);
		var accion = document.getElementById('accion');
		accion = accion.options[accion.selectedIndex].value;;
		
		if(accion=="-1"){
			alert("Debe seleccionar una accion.");
			return;
		}
	
		//20-05-2013!
		var fecha=document.getElementById("initFecha").value;

		var producto=document.getElementById('producto');
		producto=producto.options[producto.selectedIndex].value;

		if(producto=="-1"){
			alert("Seleccione un producto");
			return;
		}

		var proceso=document.getElementById('proceso');
		proceso=proceso.options[proceso.selectedIndex].value;


		var codigofamiliar= document.getElementById("codigofamiliar").value;
		var pruebaintegrada=document.getElementById("pruebaintegrada").value;

		var smartplay = document.getElementById("smartplay");
		var toip = document.getElementById("toip");
		var inter = document.getElementById("inter");
		var iptv = document.getElementById("iptv");
		var telev = document.getElementById("telev");
		var totdm = document.getElementById("totdm");


		smartplay = smartplay.options[smartplay.selectedIndex].value;
		toip = toip.options[toip.selectedIndex].value;
		inter = inter.options[inter.selectedIndex].value;
		iptv = iptv.options[iptv.selectedIndex].value;
		telev = telev.options[telev.selectedIndex].value;
		totdm = totdm.options[totdm.selectedIndex].value;


		//codigo nuevo
		var hidden=document.getElementById("initTime");
                var df=new Date().getTime() - hidden.value;
                df=new Date(df);
		var duracion=doubleDigit(df.getHours()-19)+":"+doubleDigit(df.getMinutes())+":"+doubleDigit(df.getSeconds());

                if(duracion=="" ||duracion=="00:00:00"||duracion=="::"){
                        alert("Por favor ingresar la duracion de la llamada estimada en formato: hh:mm:ss");
                        var duracion2=document.getElementById("hora");
                        duracion2.focus();
                        return;
                }
			
		var tipo_pendiente='';
		tipo_pendiente=document.getElementById("tipo_pendiente");
		tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;
		
		if(tipo_pendiente=="-1"){
			alert("Debe seleccionar una sub accion");
			return;
		}

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
		
		if(accion=="-1"){
			alert("Seleccione una accion");
			return;
		}
		
		if(login_del_asesor==""){
			alert("por favor ingrese su login");
			document.getElementById('login_del_asesor').focus();
			return;
		}
		
		if(proceso=="-1"){
			alert("Seleccione el proceso..");
			document.getElementById('proceso').focus();
			return;
		}

		//var piloto=document.getElementById("piloto").value;
		var request='&pedido='+pedido+'&id_tecnico='+id_tecnico+'&nombre_de_la_empresa='+nombre_de_la_empresa+'&login_del_asesor='+login_del_asesor+'&accion='+accion+'&tipo_pendiente='+tipo_pendiente+"&proceso="+proceso+"&producto="+producto+"&duracion="+duracion+'&llamada_id='+llamada_id+'&fecha='+fecha+'&pruebaintegrada='+pruebaintegrada+'&codigofamiliar='+codigofamiliar+'&smartplay='+smartplay+'&toip='+toip+'&inter='+inter+'&iptv='+iptv+'&telev='+telev+'&totdm='+totdm+'&observaciones='+observaciones;
		location.href='./actividades.php?operacion=insertarRegistro'+request;
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
					var ciudad=rta[4];
					var hiddenCity=document.getElementById("hiddenCity");
					hiddenCity.value=ciudad;
					divi.innerHTML=mensaje+" - "+ciudad+" - <a href='javascript:modificarInformacionTecnico();'>editar</a>";
					var nombre_de_la_empresa=document.getElementById("nombre_de_la_empresa");
					nombre_de_la_empresa.value=rta[3];
					checkalarm('1');//para disparar el chequeo de alarmas..
					//document.getElementById("observaciones").focus();

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

		var razonAprovisionamiento=document.getElementById("razonAprovisionamiento");

                movilidad=movilidad.options[movilidad.selectedIndex].value;
		razonAprovisionamiento=razonAprovisionamiento.options[razonAprovisionamiento.selectedIndex].value;
		
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
		observacion+="*Razon: "+razonAprovisionamiento;
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

        function copiarObservacionCambioEquipoNCA(){
                var cuentadomiciliaria=document.getElementById("cuentadomiciliaria").value;
                var idcuenta = document.getElementById("idcuenta").value;
                var serialentra = document.getElementById("serialentra").value;
                var macdatosentra = document.getElementById("macdatosentra").value;
                var macvozentra = document.getElementById("macvozentra").value;

		var selectTipoServicio=document.getElementById("selectTipoServicio");
		selectTipoServicio=selectTipoServicio.options[selectTipoServicio.selectedIndex].value;

                var tipoequipoentra = document.getElementById("tipoequipoentra");
		tipoequipoentra=tipoequipoentra.options[tipoequipoentra.selectedIndex].value;

                var fabricanteentra = document.getElementById("fabricanteentra");
		fabricanteentra=fabricanteentra.options[fabricanteentra.selectedIndex].value;

                var referenciaentra = document.getElementById("referenciaentra").value;
                var puertoentra = document.getElementById("puertoentra").value;

                var serialsale = document.getElementById("serialsale").value;
                var macdatossale = document.getElementById("macdatossale").value;
                var macvozsale = document.getElementById("macvozsale").value;

                var tipoequiposale = document.getElementById("tipoequiposale");
		tipoequiposale=tipoequiposale.options[tipoequiposale.selectedIndex].value;


                var fabricantesale = document.getElementById("fabricantesale");
		fabricantesale=fabricantesale.options[fabricantesale.selectedIndex].value;

                var referenciasale= document.getElementById("referenciasale").value;
		var puertosale = document.getElementById("puertosale").value;

		//var aprovisionomanual = document.getElementById("aprovisionomanual");
		//aprovisionomanual=aprovisionomanual.options[aprovisionomanual.selectedIndex].value;

		//var movilidad=document.getElementById("movilidad");
                //var equipo = document.getElementById("equipo");
                //var configuracion_acceso = "";
                //var realizo_configuracion = document.getElementById("realizo_configuracion");

                //movilidad=movilidad.options[movilidad.selectedIndex].value;

                //if(movilidad=="-1"){
                //        alert("Por favor recuerde preguntarle al tecnico si uso Movilidad.");
                //        return;
                //}

                //equipo=equipo.value;

                //if(equipo==""){
                //        alert("Por favor llenar el campo de equipos.");
                //        return;
                //}


                var observacion="";

                observacion+="*Cuenta Domiciliaria: "+cuentadomiciliaria;
		observacion+="*Tipo de Servicio: "+selectTipoServicio;
                observacion+="*ID Cuenta: "+idcuenta;
                observacion+="*Serial Entra: "+serialentra;
                observacion+="*Mac Datos Entra: "+macdatosentra;
                observacion+="*Mac Voz Entra: "+macvozentra;
                observacion+="*Tipo Equipo Entra: "+tipoequipoentra;
                observacion+="*Fabricante Entra: "+fabricanteentra;
                observacion+="*Referencia Entra: "+referenciaentra;
                observacion+="*Puerto Entra: "+puertoentra;
                observacion+="*Serial Sale: "+serialsale;
                observacion+="*Mac Datos Sale: "+macdatossale;
                observacion+="*Mac Voz Sale: "+macvozsale;
                observacion+="*Tipo Equipo Sale: "+tipoequiposale;
                observacion+="*Fabricante Sale: "+fabricantesale;
                observacion+="*Referencia Sale: "+referenciasale;
                observacion+="*Puerto Sale: "+puertosale;

		//observacion+="*Tecnico Uso Movilidad: "+movilidad;
                //observacion+="*Aprovisiono: "+equipo;
		//observacion+="*Aprovisiono Manual: "+aprovisionomanual;

                //if(realizo_configuracion.checked){
                //        observacion+="*Configuracion Terminal Remota*";
                //}


                var observaciones = document.getElementById("observaciones");
                observaciones.value= observaciones.value+" "+observacion+"*";
                Modalbox.hide();
        }


	//para validar la accion seleccionada, si es un pendiente se despliega la forma de tipos de pendiente

	function comboAprovisionamiento(){
		var movilidad = document.getElementById("movilidad");
		movilidad=movilidad.options[movilidad.selectedIndex].value;
		
		var razonAprovisionamiento=document.getElementById("razonAprovisionamiento");

		var opciones=new Array("");

		if(movilidad=="NO") {
			opciones=new Array("No tiene  PDA","No funciona  el lector de codigo de barras","No tiene datos en la pda en el momento","No es posible el aprovisionamiento Automatico","No da respuesta de aprovisionamiento","No tiene señal por cobertura","Movilidad error de conexión","No  tiene  el pedido Montado  en PDA","Equipos terminal  existentes","Se Bloquea  la PDA");
		}else{
			opciones=new Array("No es posible el aprovisionamiento Automatico","No da respuesta de aprovisionamiento");
		}
		razonAprovisionamiento.length=0;
		for (var i=0;i<opciones.length;i++){
                        var o=document.createElement('option');
                        o.value=opciones[i];
                        o.text=opciones[i];
                        razonAprovisionamiento.options.add(o);
                }
	}

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


		var proceso=document.getElementById("proceso");
                var proceso=proceso.options[proceso.selectedIndex].value;

		//alert(proceso);
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
		
		//plantilla 10-07-2014:

		
		var producto=document.getElementById("producto");
		producto=producto.options[producto.selectedIndex].value;

		if(proceso=="Reparaciones" && acciones=="Cumplir" && producto.indexOf("HFC")!=-1){
			Modalbox.show('formaDSAM.php', {title: 'Forma DSAM',height: 500, width: 400 });
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
			tipo_pendiente.length=0;
	                var acciones2=new Array();

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
		 checkalarm('4');//funcion para checkear las alarmas disponibles en este nivel!!!
        }

	function copiarFormaDSAM(){
		var tiene_dsam =document.getElementById("tiene_dsam");
		tiene_dsam = tiene_dsam.options[tiene_dsam.selectedIndex].value;
		var uso_dsam =document.getElementById("uso_dsam");
		uso_dsam = uso_dsam.options[uso_dsam.selectedIndex].value;
		var id_smnet =document.getElementById("id_smnet").value;
		var ch_2 =document.getElementById("ch_2").value;
		var ch_119 =document.getElementById("ch_119").value;
		var dqi =document.getElementById("dqi").value;
		var ber =document.getElementById("ber").value;
		var mer =document.getElementById("mer").value;
		var ds_snr =document.getElementById("ds_snr").value;
		var pot_up =document.getElementById("pot_up").value;
		var pot_down =document.getElementById("pot_down").value;
		
		var observacion="";

                observacion+="*Tiene DSAM?: "+tiene_dsam;
                observacion+="*Usó el DSAM?: "+uso_dsam;
                observacion+="*ID SMNET: "+id_smnet;
                observacion+="*CH 2: "+ch_2;
                observacion+="*CH 119: "+ch_119;
                observacion+="*DQI: "+dqi;
		observacion+="*BER: "+ber;
		observacion+="*MER: "+mer;
		observacion+="*DS SNR: "+ds_snr;
		observacion+="*POT UP : "+pot_up;
		observacion+="*POT DWN: "+pot_down;

                var observaciones = document.getElementById("observaciones");
                observaciones.value= observaciones.value+" "+observacion+"*";
                Modalbox.hide();
		
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
			
			if(producto=='HFC-Internet' || producto=='HFC-TV Basica' || producto=='HFC-TV Digital' || producto=='HFC-ToIP'){//si el producto tiene hfc entonces valido las ciudades de santanderes para mostrar la nueva forma
				var hiddenCity=document.getElementById("hiddenCity").value;
				
				if(hiddenCity=='BARRANCABERMEJA'||hiddenCity=='BUCARAMANGA'||hiddenCity=='CUCUTA'){
					//muestro nueva forma 2014/03/11
					Modalbox.show('formaCambioEquiposNCA.php?producto='+producto, {title: 'Cambio de Equipo NCA',height: 600, width: 800 });
				}else {
					Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
				}
				
			}else {

	                        Modalbox.show('formaCambioEquipos.php?producto='+producto, {title: 'Observaciones',height: 400, width: 800 });
}
                }
		if(acciones=="REPTEX"){
	                var producto=document.getElementById('producto');
                        producto=producto.options[producto.selectedIndex].value;

                        if(producto=="-1"){
                                alert("Por favor seleccione un producto");
                                return;
                        }

                        Modalbox.show('formaReptex.php', {title: 'REPTEX',height: 400, width: 800 });
               
		}
		if(acciones=="Contingencia(Solo en NCA)"){
			Modalbox.show('formaAprovisionamientoNCA.php', {title: 'Contingencia NCA',height: 600, width: 800 });
		}
		
		var acciones2=document.getElementById("accion");
                acciones2=acciones2.options[acciones2.selectedIndex].value;

		if(acciones2=="Solicita Informacion Tecnica"||acciones2=="Soporte Tecnico"){
			//
			var tipo_pendiente=document.getElementById("tipo_pendiente");
			tipo_pendiente=tipo_pendiente.options[tipo_pendiente.selectedIndex].value;

			if(tipo_pendiente=="Sobre los parametros de red"||tipo_pendiente=="General"||tipo_pendiente=="Configuracion CPE y Wifi"){
				var producto=document.getElementById('producto');
                        	producto=producto.options[producto.selectedIndex].value;

				if(producto.indexOf("HFC")!=-1){
		                        Modalbox.show('formaDSAM.php', {title: 'Forma DSAM',height: 500, width: 400 });
                		}

}
		}

		 checkalarm('5');//funcion para checkear las alarmas disponibles en este nivel!!!
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
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-asign.png' height='111' width='90%'>";

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
			var banner=document.getElementById("bannercentral");
			banner.innerHTML="<IMG src='./img/logo-insta.png' height='111' width='90%'>";

			var divInfoPruebaIntegrada=document.getElementById("divInfoPruebaIntegrada");
                        divInfoPruebaIntegrada.style.visibility="hidden";
                        divInfoPruebaIntegrada.style.position="absolute";

		}else if(procesoValue=="Reparaciones" ||procesoValue=="Outlier"){//acciones de reparaciones
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
                        var banner=document.getElementById("bannercentral");

			if(procesoValue=="Reparaciones"){
				banner.innerHTML="<IMG src='./img/logo-repa.png' height='111' width='90%'>";
			}else{
				banner.innerHTML="<IMG src='./img/logo-outlier.png' height='111' width='90%'>";
			}
                        //ibanner.innerHTML="<IMG src='./img/logo-repa.png' height='111' width='90%'>";
			
			//14-03-2014: se pide agregar una forma al lado derecho de la pantalla para todas las reparaciones
                	
			if(procesoValue=="Reparaciones"){
        
	                	var divInfoPruebaIntegrada=document.getElementById("divInfoPruebaIntegrada");
        	        	divInfoPruebaIntegrada.style.visibility="visible";
                		divInfoPruebaIntegrada.style.position="relative";
			}
                	
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
                                accion.options.add(o);
                        }
                        var banner=document.getElementById("bannercentral");
                        banner.innerHTML="<IMG src='./img/logo-estt.png' height='111' width='90%'>";

                        //14-03-2014: se pide agregar una forma al lado derecho de la pantalla para todas las reparaciones

                        //var divInfoPruebaIntegrada=document.getElementById("divInfoPruebaIntegrada");
                        //divInfoPruebaIntegrada.style.visibility="visible";
                        //divInfoPruebaIntegrada.style.position="relative";
		}

		var tipo_pendiente=document.getElementById("tipo_pendiente");
		tipo_pendiente.length=0;
		checkalarm('2');//funcion para checkear las alarmas disponibles en este nivel!!!
	}

	function checkalarm(nivel){
		//primero obtener todos los valores necesitados..
		//si identifico el momento en que se llama el check..??
		

		var hiddenCity="NULL";
		var proceso="NULL";
		var producto="NULL";
		var accion="NULL";
		var subaccion="NULL";


		hiddenCity=document.getElementById("hiddenCity").value;
		
		if(nivel>=2){
			proceso=document.getElementById('proceso');
			proceso=proceso.options[proceso.selectedIndex].value;
		}
		if(nivel>=3){
                        producto=document.getElementById('producto');
			producto=producto.options[producto.selectedIndex].value;
                }
		if(nivel>=4){
                        accion=document.getElementById('accion');
			accion = accion.options[accion.selectedIndex].value;
                }
		if(nivel>=5){
                        subaccion=document.getElementById("tipo_pendiente");
	                subaccion=subaccion.options[subaccion.selectedIndex].value;
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
		//cambiar, esto es peligroso
                //var url="${pageContext.request.contextPath}/action?accion=gestorReportes&operacion=guardarHorasAjax"+parametros;
                var url="./actividades.php?operacion=doCheckAlarma&ciudad="+hiddenCity+"&proceso="+proceso+"&producto="+producto+"&accion="+accion+"&subaccion="+subaccion+"&nivel="+nivel;
                http_request.onreadystatechange = recuperarRespuestaCheckAlarma;
		//alert("request: "+url);
                http_request.open('GET', url, true);
                http_request.send(null);

	}

	function recuperarRespuestaCheckAlarma(){
                if (http_request.readyState == 4) {
                        if (http_request.status == 200) {
                                var result = http_request.responseText;
				result = result.replace(/(\r\n|\n|\r)/gm,"");
				var result2=result.split(";");
				//alert(">"+result2[0]+"<");
				if(result2[0]=="SI"){
	                                alert(result2[1]);
				}
                        } else {
                                alert('There was a problem with the request.');
                        }
                }

	}

	function selectProducto(){
		 checkalarm('3');//funcion para checkear las alarmas disponibles en este nivel!!!
	}

</script>

<STYLE type="text/css">
        //a:link { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        //a:visited { font-weight: plain; font-size: 16px; color: blue; text-decoration: none }
        //a:hover { font-weight: bold; font-size: 16px; color: blue; text-decoration: none }
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

<SCRIPT language="JavaScript" src="./javascript/calendar.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
</HEAD>

<BODY bgcolor="WHITE">
<input type="hidden" value="" id="initTime">
<input type="hidden" value="" id="initFecha">
<input type="hidden" value="false" id="inicioLlamada">
<input type="hidden" value="" id="hiddenCity">

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

<!--CENTER><H1>Seguimiento de Pedidos</H1></CENTER-->

<CENTER><H2>Forma para la documentación de Pedidos</H2></CENTER>

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
        <TD align="left">ID de Llamada</TD>
        <TD align="center"><INPUT type="text" name="llamada_id" id="llamada_id" size="20" value=""></TD>
        <TD align="center"></TD>
</TR>

<TR>
        <TD align="left">Pedido</TD>
        <TD align="center"><INPUT type="text" name="pedido" size="12" id="pedido" onchange="javascript:hideMsg();"></TD>
        <TD align="center"><input class="btnpurple" type="button" value="Buscar" onclick="javascript:buscarPedido();"></TD>
</TR>

<TR>
	<TD align="left">Identificacion del Tecnico</TD>
	<TD align="center"><INPUT type="text" name="id_tecnico" size="12" id="id_tecnico"></TD>
	<TD align="center">
	<div id="divIngreso"><INPUT class="btnpurple" type="button" name="buscar" id="buscar" value="Buscar" onclick="javascript:buscarTecnico();"></div>
	</TD>
</TR>
<TR>
<TD align='left'>Nombre del Tecnico</TD>
<TD align='center' colspan="2"><font color='red'><div id="nombreTecnico"></div></font></TD>
<!--TD align="center"></TD-->
</TR>

<TR>
	<TD align="left">Nombre de la empresa</TD>
	<TD align="center">
		<input type="text" id="nombre_de_la_empresa" disabled="true" size="12">
	</TD>
	<TD align="center"></TD>
</TR>


<TR>
        <TD align="left"><font color="red"><b>Proceso</b></font></TD>
        <TD align="center"><select id="proceso" onchange="javascript:selectProceso();"><option value="-1">Seleccione: </option>
        <? if($_SESSION['perfil']=='4'){
                echo "<option value='Asignaciones'>Asignaciones </option>";
           }else {
                echo "<option value='Instalaciones'>Instalaciones </option><option value='Reparaciones'>Reparaciones </option><option value='Estudio Tecnico'>Estudio Técnico </option><option value='Outlier'>Outlier </option>";
        }
?>

</select></TD>
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
			<option value="GPON-Internet">GPON-Internet</option>
			<option value="GPON-IPTV">GPON-IPTV</option>
			<option value="GPON-ToIP">GPON-ToIP</option>
                </SELECT>
        </TD>
        <TD align="center"></TD>
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
<TR>
        <TD align="left">Observaciones</TD>
        <TD align="center"> <TEXTAREA name="observaciones" id="observaciones" cols="40" rows="4"></TEXTAREA></TD>
        <TD align="center"></TD>
</TR>

</TBODY></TABLE>

</td>
<td>

<div id="divInfoPruebaIntegrada" style="position:absolute;visibility:hidden;">

<table class='pruebaintegrada'>
<tr>
<td align='left'>Codigo Familiar:</td><td align='center'> <input id='codigofamiliar' size='12'></td>
</tr>
<tr>
<td align='left'>Prueba Integrada:</td><td align='center'> <input id='pruebaintegrada' size='12'></td>
</tr>
<tr>
<td align='center' colspan='2'>Resumen de la Prueba Integrada</td>
</tr>
<tr>
<td align='left'>
TOTDM(Telefonia TDM)
</td><td align='center'>
<select id='totdm'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>
<tr>
<td align='left'>
TELEV(Television HFC)
</td><td align='center'>
<select id='telev'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>

<tr>
<td align='left'>
IPTV(Television Interactiva)
</td><td align='center'>
<select id='iptv'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>

<tr>
<td align='left'>
INTER(Internet Banda Ancha)
</td><td align='center'>
<select id='inter'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>

<tr>
<td align='left'>
TOIP(Telefonia IP)
</td><td align='center'>
<select id='toip'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>

<tr>
<td align='left'>
SmartPlay(Servicios y Equipos)
</td><td align='center'>
<select id='smartplay'>
<option value=""></option>
<option value="Ok">Ok</option>
<option value="Error">Error</option>
<option value="Advertencia">Advertencia</option>
</select>
</td>
</tr>

</table>
</div>

</td>
</tr>
</table>

<BR>
<CENTER><INPUT class="btnpurple" type="button" value="Guardar" onclick="javascript:guardarDatos();">
&nbsp;<INPUT class="btnpurple" type="button" value="Ver Registros" onclick="javascript:formaRegistros();">
</CENTER>

</FORM>
<br><br>

<div id="question" style="display:none; cursor: default">
        <h1>Seguimiento a Pedidos</h1>
        <input class="btnpurple" type="button" id="yes" value="Iniciar Llamada" />
        <INPUT class="btnpurple" type="button" value="Ver Registros" onclick="javascript:formaRegistros();">
	<br><br>&nbsp;<a href="./manageUserAsesor.php?operacion=editUser&idUser=<? echo $_SESSION['idUser']; ?>">Editar Cuenta</a>
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
