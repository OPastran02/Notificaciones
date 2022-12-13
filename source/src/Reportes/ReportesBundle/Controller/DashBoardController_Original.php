<?php

namespace Reportes\ReportesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Faltas\FaltasBundle\Entity\ActaUtilizada;
use Faltas\FaltasBundle\Entity\FajaUtilizada;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
//use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;

use Reportes\ReportesBundle\Form\ReportesFiltroType;

class DashBoardController extends Controller
{
	public function DashBoardAction(Request $request)
  	{
  		$cantidadDias = $this->dameDias('2017-12-28', '2018-12-28');
  		var_dump($cantidadDias);
  		exit();
  		$this->dibujarGraficoLineas(2, 'mes');
  		$result;
	    $palTwig = [];
	    $areas = [1 => 'SUR', 2 => 'NORTE', 3 => 'ESTE', 4 => 'OESTE', 5 => 'NOCTURNIDAD', 6 => 'LEGALES', 8 => 'FUENTES MÓVILES', 18 => 'UIT'];
	    $datos = ['programadas', 'inspecciones', 'actas', 'fajas'];

	    //$fechaDada = date_format(date_create('yesterday'), 'Y-m-d');
	    $fechaDada = date_format(date_create('2017-04-04'), 'Y-m-d');
	    $fecha = $this->getFechas($fechaDada);

	    foreach ($areas as $id => $area)
	    {
	    	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');

		    $result[$area]['programadas']['dia'] = $em->programadasEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
		    $result[$area]['programadas']['semana'] = $em->programadasEntreFecha($fecha['semanaInicio'], $fecha['semanaFin'], $id);
		    $result[$area]['programadas']['mes'] = $em->programadasEntreFecha($fecha['mesInicio'], $fecha['mesFin'], $id);

		    $result[$area]['inspecciones']['dia'] = $em->inspeccionesEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
		    $result[$area]['inspecciones']['semana'] = $em->inspeccionesEntreFecha($fecha['semanaInicio'], $fecha['semanaFin'], $id);
		    $result[$area]['inspecciones']['mes'] = $em->inspeccionesEntreFecha($fecha['mesInicio'], $fecha['mesFin'], $id);

		    $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaUtilizada');

		    $result[$area]['actas']['dia'] = $em->utilizadasEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
		    $result[$area]['actas']['semana'] = $em->utilizadasEntreFecha($fecha['semanaInicio'], $fecha['semanaFin'], $id);
		    $result[$area]['actas']['mes'] = $em->utilizadasEntreFecha($fecha['mesInicio'], $fecha['mesFin'], $id);

		    $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Faja');

		    $result[$area]['fajas']['dia'] = $em->utilizadasEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
		    $result[$area]['fajas']['semana'] = $em->utilizadasEntreFecha($fecha['semanaInicio'], $fecha['semanaFin'], $id);
		    $result[$area]['fajas']['mes'] = $em->utilizadasEntreFecha($fecha['mesInicio'], $fecha['mesFin'], $id);
	    }

	    $resultados = $this->arreglar($result);
	    $resultados =  $this->organizarPorDato($resultados);
	    $palTwig['resultados'] = $resultados;

	    foreach ($resultados as $nombreDato => $tiempos)
  		{
  			foreach ($tiempos as $tiempo => $res)
  			{
  				$datosGrafico = [];
  				$gato = [ucfirst($nombreDato), 'Cantidad por '.$tiempo];
  				array_push($datosGrafico, $gato);

  				foreach ($res as $area => $dato)
  					array_push($datosGrafico, [$area, intval($dato)]);

  				$pieChart = new PieChart();
			    $pieChart->getData()->setArrayToDataTable($datosGrafico);

			    if ($tiempo == 'semana')
			    	$pieChart->getOptions()->setTitle(ucfirst($nombreDato).' de la '.$tiempo);
			    else
			    	$pieChart->getOptions()->setTitle(ucfirst($nombreDato).' del '.$tiempo);

			    $pieChart->getOptions()->setPieSliceText('value');
			    $pieChart->getOptions()->getPieSliceTextStyle()->setFontSize(18);
			    $pieChart->getOptions()->setHeight(300);
			    $pieChart->getOptions()->setWidth(600);
			    $pieChart->getOptions()->setBackgroundColor('transparent');
			    $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
			    $pieChart->getOptions()->getTitleTextStyle()->setColor('#578EBE');
			    $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
			    $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
			    $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

			    //PARA ELEGIR LOS COLORES DE LOS GRÁFICOS
			    //$pieSlice1 = new PieSlice();
				//$pieSlice1->setColor('yellow');
				//$pieSlice2 = new PieSlice();
				//$pieSlice2->setColor('transparent');
				//$pieDia->getOptions()->setSlices([$pieSlice1, $pieSlice2]);

			    $palTwig[$nombreDato.ucfirst($tiempo)] = $pieChart;
  			}
  		}

	    /*$line = new LineChart();
		$line->getData()->setArrayToDataTable(
		    [
		    [['label' => 'x', 'type' => 'number'], ['label' => 'values', 'type' => 'number'],
		    ['id' =>'i0', 'type' => 'number', 'role' =>'interval'],
		    ['id' =>'i1', 'type' => 'number', 'role' =>'interval'],
		    ['id' =>'i2', 'type' => 'number', 'role' =>'interval'],
		    ['id' =>'i2', 'type' => 'number', 'role' =>'interval'],
		    ['id' =>'i2', 'type' => 'number', 'role' =>'interval'],
		    ['id' =>'i2', 'type' => 'number', 'role' =>'interval']],
		    [1, 100, 90, 110, 85, 96, 104, 120],
		    [2, 120, 95, 130, 90, 113, 124, 140],
		    [3, 130, 105, 140, 100, 117, 133, 139],
		    [4, 90, 85, 95, 85, 88, 92, 95],
		    [5, 70, 74, 63, 67, 69, 70, 72],
		    [6, 30, 39, 22, 21, 28, 34, 40],
		    [7, 80, 77, 83, 70, 77, 85, 90],
		    [8, 100, 90, 110, 85, 95, 102, 110]
		    ]
		);
		$line->getOptions()->setTitle('Line intervals, default');
		$line->getOptions()->setCurveType('function');
		$line->getOptions()->setLineWidth(4);
		$line->getOptions()->getLegend()->setPosition('none');*/

		//Line Chart (Material)
		//Warning : the Material Charts are in beta.

		//![Material Line Chart](http://static.christophe-meneses.fr/img/google_charts_bundle/matline.png)

		//Últimos 5-6-7-8-..
		//dias - semanas - meses
		//Áreas

		//(para períodos de tiempo más extensos, usar gráfico de barras)

		$line = new LineChart();
		$line->getData()->setArrayToDataTable([
		    ['Month', 'Average Temperature', 'Average Hours of Daylight', 'asdsad'],
		    [date_create('2014-01'),  -.5,  5.7, 6],
		    [date_create('2014-02'),   .4,  8.7, 4],
		    [date_create('2014-03'),   .5,   12, 10],
		    [date_create('2014-04'),  2.9, 15.3, 8],
		    [date_create('2014-05'),  6.3, 18.6, 7],
		    [date_create('2014-06'),    9, 20.9, 8],
		    [date_create('2014-07'), 10.6, 19.8, 12],
		    [date_create('2014-08'), 10.3, 16.6, 15],
		    [date_create('2014-09'),  7.4, 13.3, 13],
		    [date_create('2014-10'),  4.4,  9.9, 14],
		    [date_create('2014-11'),  1.1,  6.6, 16],
		    [date_create('2014-12'),  -.2,  4.5, 18]
		]);

		$line->getOptions()->getChart()
		    ->setTitle('Average Temperatures and Daylight in Iceland Throughout the Year');
		$line->getOptions()
		    ->setHeight(400)
		    ->setWidth(1100)
		    ->setSeries([['axis' => 'Temps'], ['axis' => 'Daylight']])
		    ->setAxes(['y' => ['Temps' => ['label' => 'Temps (Celsius)'], 'Daylight' => ['label' => 'Daylight']]]);

		//-------

		$palTwig['line'] = $line;

	    return $this->render('ReportesReportesBundle:Default:dashboard.html.twig' , $palTwig);
  	}


  	private function getFechas ($fecha)
  	{
  		$diaInicio = $fecha.' 00:00';
	    $diaFin = $fecha.' 23:59';

	    $semana = $this->inicio_fin_semana($fecha);
	    $semanaInicio = $semana['fechaInicio'].' 00:00';
	    $semanaFin = $semana['fechaFin'].' 23:59';

  		$date = date_create($fecha);
	    $mesInicio = date_format($date->modify('first day of this month'), 'Y-m-d').' 00:00';
	    $mesFin = date_format($date->modify('last day of this month'), 'Y-m-d').' 23:59';

	    return array('diaInicio' => $diaInicio, 'diaFin' => $diaFin, 'semanaInicio' => $semanaInicio, 'semanaFin' => $semanaFin, 'mesInicio' => $mesInicio, 'mesFin' => $mesFin);
  	}


  	private function inicio_fin_semana ($fecha)
  	{
	    $diaInicio = "Monday";
	    $diaFin = "Sunday";

	    $strFecha = strtotime($fecha);

	    $fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));
	    $fechaFin = date('Y-m-d',strtotime('next '.$diaFin,$strFecha));

	    if(date("l",$strFecha)==$diaInicio){
	        $fechaInicio= date("Y-m-d",$strFecha);
	    }
	    if(date("l",$strFecha)==$diaFin){
	        $fechaFin= date("Y-m-d",$strFecha);
	    }
	    return Array("fechaInicio"=>$fechaInicio,"fechaFin"=>$fechaFin);
	}


	/** Actual month last day **/
  	private function _data_last_month_day()
  	{
      	$month = date('m');
      	$year = date('Y');
      	$day = date("d", mktime(0,0,0, $month+1, 0, $year));

      	return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
  	}


  	/** Actual month first day **/
  	private function _data_first_month_day()
  	{
      	$month = date('m');
      	$year = date('Y');

      	return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
  	}


  	private function arreglar ($resultados)
  	{
  		$definitivo = [];

  		foreach ($resultados as $area => $dato)
  		{
  			foreach ($dato as $nombreDato => $tiempos)
  			{
  				foreach ($tiempos as $tiempo => $res)
  				{
  					$definitivo[$area][$nombreDato][$tiempo] = current($res[0]);
  				}
  			}
  		}

  		return $definitivo;
  	}


  	private function organizarPorDato ($resultados)
  	{
  		$definitivo = [];

  		foreach ($resultados as $area => $dato)
  		{
  			foreach ($dato as $nombreDato => $tiempos)
  			{
  				foreach ($tiempos as $tiempo => $res)
  				{
  					$definitivo[$nombreDato][$tiempo][$area] = $res;
  				}
  			}
  		}

  		return $definitivo;
  	}


  	private function dibujarGraficoLineas ($cantidad, $periodo/*, $areas*/)
  	{
  		$areas = [1 => 'SUR', 2 => 'NORTE', 3 => 'ESTE', 4 => 'OESTE', 5 => 'NOCTURNIDAD', 6 => 'LEGALES', 8 => 'FUENTES MÓVILES', 18 => 'UIT'];
  		$datosGrafico = [];
  		$gato = [];

  		//SACAR LA FECHA ACTUAL (SEA DIA, SEMANA, MES)
		//DIA -> fecha de ayer
		//SEMANA -> fecha correspondiente al primer día de la semana
		//MES -> fecha correspondiente al primer día del mes (se saca fácil poniendo la fecha 'Y-m')
		$fechaActual = '';
		//$fechaActual = $ayer;
		//MERAMENTE ILUSTRATIVO

		/*var_dump(date_create('2017-04 -12 month'));
	    exit();*/
	    $ayer = date_create('yesterday');
	    $date = $ayer; //aux
	    $fechas = [];
	    $par = [];

  		//CAMBIAR LOS PERÍODOS (ayer, desde ayer hasta 7 días antes y desde ayer hasta 30 días atrás)
  		//SI ELEGÍS UNA FECHA QUE NO CORRESPONDE A ESTA SEMANA O ESTE MES ()
  		switch ($periodo)
  		{
  			case 'dia':
  				array_push($gato, 'Day');

  				for ($i=0; $i < $cantidad; $i++)
  				{
  					$inicio = date_format($date, 'Y-m-d').' 00:00';
  					$fin = date_format($date, 'Y-m-d').' 23:59';
  					array_push($fechas, [$inicio, $fin]);

  					$date->modify(date_format($date, 'Y-m-d').' -1 day');
  				}

  				$fechas = array_reverse($fechas);
  				break;

  			case 'semana':
  				array_push($gato, 'Week');

	    		//SI $ayer PERTENECE A LA SEMANA PASADA (domingo), SÍ VALE LO DE ARRIBA
	    		//SI $ayer PERTENECE A ESTA SEMANA, SACAR RESULTADO EN BASE A LOS ÚTLIMOS 7 DÍAS
	    		if (date_format($ayer, 'l') == 'Sunday')
	    		{
	    			$semana = $this->inicio_fin_semana(date_format($ayer, 'Y-m-d'));

	    			for ($i=0; $i < $cantidad; $i++)
	    			{
		    			$semana = $this->inicio_fin_semana(date_format($date->modify(date_format($date, 'Y-m-d').' -'.($i*7).' days'), 'Y-m-d'));

		    			$inicio = date_format(date_create($semana['fechaInicio']), 'Y-m-d').' 00:00';
	  					$fin = date_format(date_create($semana['fechaFin']), 'Y-m-d').' 23:59';
	  					array_push($fechas, [$inicio, $fin]);
	    			}

	    			$fechas = array_reverse($fechas);
	    		}
	    		else
	    		{
	    			for ($i=0; $i < $cantidad; $i++)
	    			{
	    				$fin = date_format($date->modify(date_format($date, 'Y-m-d').' -'.($i*7).' days'), 'Y-m-d').' 23:59';
						$inicio = date_create();
	    				$inicio = date_format($inicio->modify(date_format($inicio, 'Y-m-d').' -'.(($i+1)*7).' days'), 'Y-m-d').' 00:00';

	    				array_push($fechas, [$inicio, $fin]);
	    			}

	    			$fechas = array_reverse($fechas);
	    		}
  				break;

  			case 'mes':
  				array_push($gato, 'Month');
  				//$date = $ayer;
			    //$fechaActual = $date->modify('first day of this month');
			    //MERAMENTE ILUSTRATIVO

			    //PREGUNTO POR HOY, POR SI ES 1° (primero)
			    //$hoy = date_create();
			    $hoy = date_create('2018-01-01'); //ejemplo
			    //$ayer NO IRÍA
			    $ayer = date_create('2017-12-31'); //ejemplo

			    if (date_format($hoy, 'j') == 1)
			    {
			    	//MES ENTERO
				    $date = date_create(date_format($ayer, 'Y').'-'.date_format($ayer, 'n'));

				    for ($i=0; $i < $cantidad; $i++)
				    {
				    	$inicio = date_format($date->modify(date_format($date, 'Y-m-d').' -'.$i.' months'), 'Y-m-d').' 00:00';
				    	$fin = date_format($date->modify('last day of this month'), 'Y-m-d').' 23:59';
				    	array_push($fechas, [$inicio, $fin]);
				    	$date = date_create($inicio);
				    }

				    $fechas = array_reverse($fechas);
			    }
			    else
			    {
			    	//30 días atrás
			    }
  				break;

  			default:
  				break;
  		}

  		var_dump($fechas);
  		exit();

		$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
  		foreach ($areas as $id => $area)
  		{
  			array_push($gato, $area);

  			$result;
			$result[$area] = $em->inspeccionesEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
  		}

		array_push($datosGrafico, $gato);

		//TRAER DATOS
  		//(desde la actualidad hasta la fecha indicada)

	    $result;
	    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
  		foreach ($areas as $id => $area)
	    {
		    $result[$area]['inspecciones']['dia'] = $em->inspeccionesEntreFecha($fecha['diaInicio'], $fecha['diaFin'], $id);
		    $result[$area]['inspecciones']['semana'] = $em->inspeccionesEntreFecha($fecha['semanaInicio'], $fecha['semanaFin'], $id);
		    $result[$area]['inspecciones']['mes'] = $em->inspeccionesEntreFecha($fecha['mesInicio'], $fecha['mesFin'], $id);
	    }

	    $resultados = $this->arreglar($result);
	    $resultados =  $this->organizarPorDato($resultados);
	    $palTwig['resultados'] = $resultados;

		foreach ($res as $area => $dato)
			array_push($datosGrafico, [$area, intval($dato)]);

		$pieChart = new PieChart();
    	$pieChart->getData()->setArrayToDataTable($datosGrafico);

  		$chart = new LineChart();
  		//$chart->getData()->setArrayToDataTable($datosGrafico);
		$chart->getData()->setArrayToDataTable([
		    ['Month', 'Average Temperature', 'Average Hours of Daylight', 'asdsad'],
		    [date_create('2014-01'),  -.5,  5.7, 6],
		    [date_create('2014-02'),   .4,  8.7, 4],
		    [date_create('2014-03'),   .5,   12, 10],
		    [date_create('2014-04'),  2.9, 15.3, 8],
		    [date_create('2014-05'),  6.3, 18.6, 7],
		    [date_create('2014-06'),    9, 20.9, 8],
		    [date_create('2014-07'), 10.6, 19.8, 12],
		    [date_create('2014-08'), 10.3, 16.6, 15],
		    [date_create('2014-09'),  7.4, 13.3, 13],
		    [date_create('2014-10'),  4.4,  9.9, 14],
		    [date_create('2014-11'),  1.1,  6.6, 16],
		    [date_create('2014-12'),  -.2,  4.5, 18]
		]);

		$chart->getOptions()->getChart()
		    ->setTitle('Average Temperatures and Daylight in Iceland Throughout the Year');
		$chart->getOptions()
		    ->setHeight(400)
		    ->setWidth(1100)
		    ->setSeries([['axis' => 'Temps'], ['axis' => 'Daylight']])
		    ->setAxes(['y' => ['Temps' => ['label' => 'Temps (Celsius)'], 'Daylight' => ['label' => 'Daylight']]]);

		return $chart;
  	}


  	private function dameDias ($fechaInicio, $fechaFin)
  	{
  		$dias = [];
  		$fecha = date_create($fechaInicio);
  		array_push($dias, date_format($fecha, 'Y-m-d'));
  		$fechaFin = date_create($fechaFin);

  		while ($fecha != $fechaFin)
  		{
  			$fecha->modify(date_format($fecha, 'Y-m-d').' +1 day');
  			array_push($dias, date_format($fecha, 'Y-m-d'));
  		}

  		/*var_dump($dias);
  		exit();*/

  		/*for ($i=0; $i < count($dias); $i++)
  			if(date_format(date_create($dias[$i]), 'l') == 'Sunday' || date_format(date_create($dias[$i]), 'l') == 'Saturday')
  				unset($dias[$i]);*/

  		/*foreach ($dias as $key)
  		{
  			$key = date_create($key);
  			var_dump(date_format($key, 'l'));
  		}
  		exit();*/


  		//AGREGAR ACÁ LAS RESTRICCIONES DE LAS DEMÁS FUNCIONES

  		return count($dias);
  	}
}
