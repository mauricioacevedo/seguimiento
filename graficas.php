<?php

	include_once('conexion.php');

	$conexion_bd = getConnection();
	
        $fechaIni=date("Y")."-".date("m")."-01";
        $fechaFin=date("Y")."-".date("m")."-".date("d");
	
	$sql="select count(*),to_char(fecha,'YYYY-MM-DD') as fecha from registros where fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' group by 2 order by 2 asc";
	
	$result = pg_query($sql);

	$rows=pg_numrows($result);

	if($rows>0){
		$datos="";
		$sep="";
		for($i=0;$i<$rows;$i++){
			$contador=pg_result($result,$i,0);
			$dia=pg_result($result,$i,1);
			$datos=$datos.$sep." { \"Dia\": \"$dia\", \"Llamadas\": $contador } ";
			$sep=",";
		}

		// ahora la consulta para el detalle por ciudad!!!

		$sqlcrosstab="select * from crosstab(\$\$select to_char(a.fecha,'YYYY-MM-DD'),b.ciudad,count(*) from registros a, tecnicos b where a.fecha between '$fechaIni 00:00:00' and '$fechaFin 23:59:59' and a.id_tecnico=b.identificacion   group by 1,2 order by 1,2".
"\$\$::text,\$\$VALUES ('MEDELLIN'::text),('BOGOTA'::text),('MANIZALES'::text),('BARRANQUILLA'::text),('BUCARAMANGA'::text),('CALI'::text),('CARTAGENA'::text),('BUGA'::text),('ARMENIA'::text),('BARRANCABERMEJA'::text),('CUCUTA'::text),('PALMIRA'::text),('TUNJA'::text),('PASTO'::text),('IBAGE'::text),('SANTAMARTA'::text),('POPAYAN'::text),('VILLAVICENCIO'::text),('PEREIRA'::text),('NEIVA'::text) \$\$) as ct".
"(\"fecha\" text,\"MEDELLIN\" text,\"BOGOTA\" text,\"MANIZALES\" text,\"BARRANQUILLA\" text,\"BUCARAMANGA\" text,\"CALI\" text,\"CARTAGENA\" text,\"BUGA\" text,\"ARMENIA\" text,\"BARRANCABERMEJA\" text,\"CUCUTA\" text,\"PALMIRA\" text,\"TUNJA\" text,\"PASTO\" text,\"IBAGE\" text,\"SANTAMARTA\" text,\"POPAYAN\" text,\"VILLAVICENCIO\" text,\"PEREIRA\" text,\"NEIVA\" text)";
		
		$result = pg_query($sqlcrosstab);

		$rows=pg_numrows($result);

		$datosDetalle[]=array();
		$listaFechas="";
                $sep="";
                for($i=0;$i<$rows;$i++){
			
                        $fecha=pg_result($result,$i,0);
			$datosDetalle[$fecha]["MEDELLIN"]=pg_result($result,$i,1);
			$datosDetalle[$fecha]["BOGOTA"]=pg_result($result,$i,2);
			$datosDetalle[$fecha]["MANIZALES"]=pg_result($result,$i,3);
			$datosDetalle[$fecha]["BARRANQUILLA"]=pg_result($result,$i,4);
			$datosDetalle[$fecha]["BUCARAMANGA"]=pg_result($result,$i,5);
			$datosDetalle[$fecha]["CALI"]=pg_result($result,$i,6);
			$datosDetalle[$fecha]["CARTAGENA"]=pg_result($result,$i,7);
			$datosDetalle[$fecha]["BUGA"]=pg_result($result,$i,8);
			$datosDetalle[$fecha]["ARMENIA"]=pg_result($result,$i,9);
			$datosDetalle[$fecha]["BARRANCABERMEJA"]=pg_result($result,$i,10);
			$datosDetalle[$fecha]["CUCUTA"]=pg_result($result,$i,11);
			$datosDetalle[$fecha]["PALMIRA"]=pg_result($result,$i,12);
			$datosDetalle[$fecha]["TUNJA"]=pg_result($result,$i,13);
			$datosDetalle[$fecha]["PASTO"]=pg_result($result,$i,14);
			$datosDetalle[$fecha]["IBAGE"]=pg_result($result,$i,15);
			$datosDetalle[$fecha]["SANTAMARTA"]=pg_result($result,$i,16);
			$datosDetalle[$fecha]["POPAYAN"]=pg_result($result,$i,17);
			$datosDetalle[$fecha]["VILLAVICENCIO"]=pg_result($result,$i,18);
			$datosDetalle[$fecha]["PEREIRA"]=pg_result($result,$i,19);
			$datosDetalle[$fecha]["NEIVA"]=pg_result($result,$i,20);

			$listaFechas=$listaFechas.$sep.$fecha;
                        //$dia=pg_result($result,$i,1);
                        //$datos=$datos.$sep." { \"Dia\": \"$dia\", \"Llamadas\": $contador } ";
                        $sep=",";
                }
		

	}else{
		//no hay datos
	}

//consulta de registros por dia:
//select count(*),to_char(fecha,'YYYY-MM-DD') as fecha from registros where fecha between '2014-01-01 00:00:00' and '2014-01-16 23:59:59' group by 2 order by 2 asc



?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="jchartfx/styles/jchartfx.css" />
    <script type="text/javascript" src="jchartfx/js/jchartfx.system.js"></script>

    <script type="text/javascript" src="jchartfx/js/jchartfx.coreVector.js"></script>
    <script type="text/javascript" src="jchartfx/js/jchartfx.coreBasic.js"></script>
    <script type="text/javascript" src="jchartfx/js/jchartfx.advanced.js"></script>
    <script type="text/javascript" src="jchartfx/js/jchartfx.animation.js"></script>
	<link rel="stylesheet" href="javascript/actividades.css" type="text/css" />
	<style type="text/css">

	.jchartfx .AxisX_Minor {
      stroke: #DBDBD9;
}

.jchartfx .AxisX_Interlaced {
      fill: #ECEBE8;
}

.jchartfx .AxisX_Line {
      stroke: #DBDBD9;
      stroke-width: 2;
}

.jchartfx .PointLabel {
      fill: #666666;
}

.chartToolTip,.jchartfxToolTip, .jchartfxToolTipHidden {
    background-color: white;
    color: red;
    padding: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    pointer-events: none;
    border-style: solid;
    border-width: 1px;
    border-color: black;
}

	</style>
</head>
<body onload="onLoadDoc()">
<table width="95%">
<tr><td>
<div width="100%" class="bannercentral">
<IMG src="./img/logo-inicial.png" height="111" width="90%">
</div>
</td></tr>
</table>

<br><br>
<br><center><h2>Indicadores Mesa de Ayuda</h2></center><br>
<br><br>
<center>
<div id="myChartDiv" style="width:1000px;height:400px;display:inline-block" align="center" valign="center"></div>

<div id="tipChartInfo" class="tooltipPop" style="display:none;">
                <div id="detailsChart" style="width:800px;height:300px;
                  display:inline-block">       
                </div>
        </div>
</center>
<script type="text/javascript" language="javascript">

    	var chart1;
	var chartDetails;
	var posX;
	var posY;
	var divInTooltip = null;
	
	function onLoadDoc() {
    		chart1 = new cfx.Chart();chart1.getAnimations().getLoad().setEnabled(true);
		chart1.setGallery(cfx.Gallery.Bar);
    
        	chart1.getDataGrid().setVisible(true);
    		chart1.getLegendBox().setVisible(true);
        
       
		 chart1.getAllSeries().getPointLabels().setFormat("%v");
    		chart1.getAllSeries().getPointLabels().setVisible(true);
    //chart1.getAllSeries().getPointLabels().setAlignment(cfx.StringAlignment.NEAR)
 
    		doTitle(chart1, "Distribución de llamadas por dia");
    		doDataPopulation();
    
        	var allSeries = chart1.getAllSeries();
    		allSeries.setMarkerShape(cfx.MarkerShape.Rect);
    
        	var chartDiv = document.getElementById('myChartDiv');
        	chart1.create(chartDiv);

        	chart1.on("gettip", onGetTipDiv);
	}

	function onGetTipDiv(args) {
    		if (args.getHitType() == cfx.HitType.Point) {
        		if (divInTooltip === null) {
            			divInTooltip = document.getElementById('tipChartInfo');
            			args.tooltipDiv.appendChild(divInTooltip);
            			divInTooltip.style.visibility = "hidden";
            			divInTooltip.style.display = "block";

            			chartDetails = new cfx.Chart();
            			var divHolder = document.getElementById('detailsChart');
            			chartDetails.setGallery(cfx.Gallery.Pie);
            			//pieChart(chartDetails);
				var arr=args.getText().split("<br/>");

        			chartDetails.getLegendBox().setVisible(true);
				chartDetails.getAllSeries().getPointLabels().setVisible(true);

            			doTitle(chartDetails, "Total llamadas el dia "+arr[1]+": "+arr[2]);
		                //chart1.setGallery(cfx.Gallery.Pie);
		                chartDetails.getView3D().setEnabled(true);
                		//chartDetails.getView3D().setAngleX(45);
		                chartDetails.getView3D().setCluster(true);

            			updateDetailsChart(args);

            			chartDetails.create(divHolder);
            			divInTooltip.style.visibility = "inherit";
        		} else {
				//divInTooltip=null;
				//onGetTipDiv(args);
				updateDetailsChart(args);
				var arr=args.getText().split("<br/>");
                                
                                doTitle(chartDetails, "Total llamadas el dia "+arr[1]+": "+arr[2]);

			}

        		args.replaceDiv = false;
    		}
	}

	function updateDetailsChart(args) {
    		doDataYearDetails(args.getText(), chartDetails);
	}

	//Chart title settings
	function doTitle(chart, text) {
    		var td;
		td = new cfx.TitleDockable();
    		td.setText(text);
    		td.setDock(cfx.DockArea.Top);
		//var tit=chart.getTitles();

		//var funcs = "";
		//for(var name in chart.getTitles()) {
		//    if(typeof chart.getTitles()[name] === 'function') {
		//        funcs=funcs+name+",";
		//    }
		//}
		chart.getTitles().removeAt(0);
		//alert(funcs);
    		chart.getTitles().add(td);
	}

	//Main Chart Data Information
	function doDataPopulation() {
    		var items = [<? echo $datos; ?>];
    		chart1.setDataSource(items);
	}


	//Detailed data per year
function doDataYearDetails(year, chart) {

	year=year.split("<br/>");
	var fecha=year[1];
	var items;
<?
	//esto va a quedar terriblemente enredado..
	
	$listaFechas=explode(",",$listaFechas);
	$k=count($listaFechas);

	for($i=0;$i<$k;$i++){
		$fecha=$listaFechas[$i];
		
		echo " if (fecha=='$fecha'){";
		$arrayCity=$datosDetalle[$fecha];

		$keys=array_keys($arrayCity);

		$h=count($keys);
		$sep="";
		$datos="";
		for($y=0;$y<$h;$y++){
			$city=$keys[$y];
			$value=$arrayCity[$city];
			
			if($value=="")$value="0";
			//echo "$fecha => ".$city." => $value\n";

                        //$dia=pg_result($result,$i,1);
                        $datos=$datos.$sep." { \"Ciudad\": \"$city\", \"Llamadas\": $value } ";
                        $sep=",";
		}
		echo "items=[$datos];\n";
		echo "}\n\n";
	}


?>
    chart.setDataSource(items);
}

    //Setting the tooltip as Pie chart
    function pieChart(chart) {
        var myPie;
        myPie = (chart.getGalleryAttributes());
        myPie.setStacked(true);
        myPie.setShadows(true);
        myPie.setShowLines(true);
        myPie.setDoughnutThickness(15);

        var series;
        series = chart.getSeries().getItem(0);
        series.setVolume(110);            

        series = chart.getSeries().getItem(1);
        series.setGallery(cfx.Gallery.Pie);
        series.setVolume(80);

        chart.getLegendBox().setDock(cfx.DockArea.Right);
        
        chart.getLegendBox().setContentLayout(cfx.ContentLayout.Center);
        chart.getLegendBox().setVisible(true);
        chart.getAllSeries().getPointLabels().setVisible(true);
        chart.getAxisY().getLabelsFormat().setDecimals(2);
    }

	function onGetToolTip(args){
		//args.getText()
		//alert(args);
	}

	function loadChart3d() {
		chart1 = new cfx.Chart();
		PopulateProductSales(chart1);
		var titles = chart1.getTitles();
		var title = new cfx.TitleDockable();
		title.setText("Wine Sales by Type");
		titles.add(title);
		chart1.getAxisY().getLabelsFormat().setFormat(cfx.AxisFormat.Currency);
		//chart1.setGallery(cfx.Gallery.Bar);
		//chart1.getView3D().setEnabled(true);
		//chart1.getView3D().setAngleX(45);
		//chart1.getView3D().setCluster(true);
	}

	function PopulateProductSales(chart1) {
    var items = [{
        "Month": "Jan",
        "White": 12560,
        "Red": 23400,
        "Sparkling": 34500
    }, {
        "Month": "Feb",
        "White": 13400,
        "Red": 21000,
        "Sparkling": 38900
    }, {
        "Month": "Mar",
        "White": 16700,
        "Red": 17000,
        "Sparkling": 42100
    }, {
        "Month": "Apr",
        "White": 12000,
        "Red": 19020,
        "Sparkling": 43800
    }, {
        "Month": "May",
        "White": 15800,
        "Red": 26500,
        "Sparkling": 37540
    }, {
        "Month": "Jun",
        "White": 9800,
        "Red": 27800,
        "Sparkling": 32580
    }, {
        "Month": "Jul",
        "White": 17800,
        "Red": 29820,
        "Sparkling": 34000
    }, {
        "Month": "Aug",
        "White": 19800,
        "Red": 17800,
        "Sparkling": 38000
    }, {
        "Month": "Sep",
        "White": 23200,
        "Red": 32000,
        "Sparkling": 41300
    }, {
        "Month": "Oct",
        "White": 16700,
        "Red": 26500,
        "Sparkling": 46590
    }, {
        "Month": "Nov",
        "White": 11800,
        "Red": 23000,
        "Sparkling": 48700
    }, {
        "Month": "Dec",
        "White": 13400,
        "Red": 15400,
        "Sparkling": 49100
    }];

    chart1.setDataSource(items);
}
</script>
</body>
</html>
