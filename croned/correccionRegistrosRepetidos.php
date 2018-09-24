<?php 

	session_start();
	include_once('/var/www/html/seguimiento/conexion.php');
        $conexion_bd = getConnection();
	error_reporting(E_ERROR | E_PARSE);
	
	$fecha=date("Y")."-".date("m")."-".date("d");

	//$sql0="select  distinct fecha::timestamp::date from registros order by 1 asc";

	//$result0 = pg_query($sql0);
        //$rows0=pg_numrows($result0);
	//$tot=0;
	//if($rows0>0){
	//	for($j=0;$j<$rows0;$j++){

	//		$fecha=pg_result($result0,$j,0);


	//$fecha='2013-10-21';
	
	echo "\n\r[INICIO BORRADO AUTOMATICO($fecha)]\n\r";
	
	$sql1="select pedido,id_tecnico, fecha,count(*) from registros where fecha between '$fecha 00:00:00' and '$fecha 23:59:59' group by pedido,id_tecnico,fecha having count(*) >= 2";

	$result = pg_query($sql1);
	$rows=pg_numrows($result);

	if($rows>0){//encontro registros con pedidos repetidos.

		for($i=0;$i<$rows;$i++){

                        $pedido=pg_result($result,$i,0);
			$fecha2=pg_result($result,$i,2);//fecha en la que se repiten los pedidos

			//busco todas las ocurrencias de este pedido en la fecha exacta
			$sql2="select id from registros where pedido='$pedido' and fecha='$fecha2' order by id asc";
			//echo $sql2."\n\r";
			$result2 = pg_query($sql2);
			
			$rows2=pg_numrows($result2);
			//echo "$rows2\n\r";
			if($rows2>0){//creo el sql de borrado con un in para los oid y dejo solo el primer registro
				$ids="";
				$sep="";
				$counter=0;
				for($k=1;$k<$rows2;$k++){
					$id=pg_result($result2,$k,0);
					$ids=$ids."$sep'$id'";
					$sep=",";
					$counter=$counter+1;
				}
				$sqldelete="delete from registros where id in ($ids)";
				//echo $sqldelete;
				pg_query($sqldelete);
				echo "Se borraron $counter registros del pedido $pedido en la fecha $fecha2\n\r";
				$tot=$tot+$counter;
			}
			

		}

		
	} else {
		echo "No se encontraron registros repedidos.. yahooo!!\n\r";
	}

//}
//}
//echo "\n\r\n\rTOTAL REGISTROS ELIMINADOS: $tot\n\r";
?>
