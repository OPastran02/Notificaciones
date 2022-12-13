<?php

namespace Inspecciones\InspeccionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use CoreBundle\Logic\UsigWS;
use CoreBundle\Logic\JsonValidator;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Establecimiento\EstablecimientoBundle\Entity\Estado;
use Establecimiento\EstablecimientoBundle\Entity\RubroPrincipal;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Inspecciones\InspeccionesBundle\Entity\Inspeccion;
use Inspecciones\InspeccionesBundle\Entity\Circuito;
use Usuario\UsuarioBundle\Entity\Usuarios;

use CoreBundle\Logic\encriptador;

class CierreAutomaticoController extends Controller
{

	public function ejecutarCierreAutomaticoAction(Request $request)
	{
		$user=$this->getUser();

        if($user)
        {
        	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
          	$ordenesInspeccion = $em->inspeccionesSinCerrar();          	
          	$cierres= 0;

          	foreach ($ordenesInspeccion as $orden) {          		
          		if( $this->cierreAutorimaticoAction($orden->getId()) ){
					$cierres++;          			
          		}          		
          	}

          	return new Response($cierres." inspecciones cerradas automaticamente");
        }
	}


	public function cierreAutorimaticoAction($idOrderInspeccion)
	{
		$user=$this->getUser();

        if($user)
        {
        	$direcciones = array();
        	$direccionesAgregar = array();
        	$i=0;
            $ordenInspeccion = new OrdenInspeccion();            
            $CierreAutomatico = false;
            $error = false;
            $motivosEvitarCierreAutomatico = array(2,3,4,18,19,20,21,22,31,32);
            $cierreAutomaticoMotivos = true;

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
          	$ordenInspeccion = $em->findOneById($idOrderInspeccion);

          	if(!$ordenInspeccion)
                throw $this->createNotFoundException('Inspeccion not found');            

            $establecimiento = $ordenInspeccion->getEstablecimiento();

            if(!$establecimiento)
            {//VINCULAR INSPECCION AL ESTABLECIMIENTO
            	$direccionesProvisorias = $ordenInspeccion->getDirecciones();
            	if(count($direccionesProvisorias) > 0)
            	{
            		$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');

	                foreach ($direccionesProvisorias as $direccion) {
	                    $direccionEncontrada = $em->buscarDireccionExacta($direccion);	                    
	                    if($direccionEncontrada){	                    	
	                        $direcciones[$i] = $direccionEncontrada;
	                        $i++;
	                    }else{
	                        array_push($direccionesAgregar, $direccion );
	                    }
	                }
	                if($i > 0)
	                {
	                	$establecimiento = $direcciones[0][0]->getEstablecimiento();
	                    foreach ($direcciones as $direccion) {
	                        if($establecimiento->getId() != $direccion[0]->getEstablecimiento()->getId()){
	                            $error = true;
	                        }
	                    }
	                    if(!$error)
	                    {
	                    	$ordenInspeccion->setEstablecimiento($establecimiento);
	                        $em = $this->getDoctrine()->getManager();
	                        foreach ($direccionesAgregar as $direccion) {
	                            $direccionEstablecimiento = new Direccion();
	                            $direccionEstablecimiento->setEstablecimiento($establecimiento);
	                            $direccionEstablecimiento->setCalle($direccion->getCalle());
	                            $direccionEstablecimiento->setAltura($direccion->getAltura());
	                            $direccionEstablecimiento->setPiso($direccion->getPiso());
	                            $direccionEstablecimiento->setDpto($direccion->getDpto());
	                            $direccionEstablecimiento->setLocal($direccion->getLocal());
	                            $direccionEstablecimiento->setComuna($direccion->getComuna());
	                            $direccionEstablecimiento->setLon($direccion->getLon());
	                            $direccionEstablecimiento->setLat($direccion->getLat());
	                            $direccionEstablecimiento->setSMP($direccion->getSMP());
	                            $direccionEstablecimiento->setPMatriz($direccion->getPMatriz());

	                            $em->persist($direccionEstablecimiento);
	                        }

	                        foreach ($ordenInspeccion->getDirecciones() as $direccion) {
	                            $em->remove($direccion);
	                        }

	                        $ordenInspeccion->setDirecciones(array());
	                    }else{
	                    	$establecimiento = null;
	                    }
	                }

            	}
            }//FIN VINCULAR

            //REVISO SI LA INSPECCION ESTA DENTRO DE LOS MOTIVOS DE CIERRE MANUAL
            $motivoInspeccion = $ordenInspeccion->getMotivoInspeccion();
            if($motivoInspeccion){
            	if(in_array($motivoInspeccion->getId(), $motivosEvitarCierreAutomatico)){
            		$cierreAutomaticoMotivos = false;
            	}
            }
            //FIN REVISION

            if( $establecimiento && !is_null( $ordenInspeccion->getResultados() ) && $cierreAutomaticoMotivos )
            {            	
            	//BUSCA LA INSPECCION A LA QUE SE LE VAN A MODIFICAR LOS DATOS            
	            $ordenesViejas = $establecimiento->getInspecciones();
	            $inspeccionActualKey = null;	            
	            foreach ($ordenesViejas as $key => $inspeccion) 
	            {
	            	if($inspeccion->getChecklist() == $ordenInspeccion->getChecklist())
	            	{
	            		$inspeccionActualKey = $key;
	            	}
	            }
	            //FIN BUSQUEDA

            	if (array_key_exists($inspeccionActualKey + 1, $ordenesViejas))
            	{            		
            		$CierreAutomatico = !$this->esCierreManual($ordenesViejas[$inspeccionActualKey + 1],$ordenInspeccion);
            	}else{            		
            		$CierreAutomatico = !$this->esCierreManual(null,$ordenInspeccion);
            	}
            }

            if($CierreAutomatico && $cierreAutomaticoMotivos)
            {            	
            	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            	$resultado = $em->findOneBy(array('grupo' => 1,'pregunta' => 9,'ordenInspeccion'=>$ordenInspeccion->getId()));	

            	if($resultado){
            		$respuesta = $resultado->getRespuestas();
            		foreach ($respuesta as $value) {					
            			$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal');
            			$rubroPrincipal = $em->findOneById($value->getOriginalId());
						$establecimiento->setRubroPrincipal($rubroPrincipal);
					}
            	}

            	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            	$resultado = $em->findOneBy(array('grupo' => 1,'pregunta' => 8,'ordenInspeccion'=>$ordenInspeccion->getId()));
            	
            	if($resultado){            		
            		$establecimiento->setRubro();
            		$respuesta = $resultado->getRespuestas();            		
            		foreach ($respuesta as $value) {            			
            			$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Rubro');
            			$rubro = $em->findOneById($value->getOriginalId());            			
						$establecimiento->addRubro($rubro);
					}
            	}        	

            	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            	$resultado = $em->findOneBy(array('grupo' => 1,'pregunta' => 7,'ordenInspeccion'=>$ordenInspeccion->getId()));

            	if($resultado){
            		$establecimiento->setRubroExtendido($resultado->getRespuestaLibre());
            	}

            	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            	$resultado = $em->findOneBy(array('grupo' => 1,'pregunta' => 1,'ordenInspeccion'=>$ordenInspeccion->getId()));

            	if($resultado){
            		$respuestas = $resultado->getRespuestas();
            		if($respuestas[0]->getId() == 1){
            			$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado');
            			$estado = $em->findOneById(55);
            			$establecimiento->setEstado($estado);	
            		}else{
            			$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado');
            			$estado = $em->findOneById(56);
            			$establecimiento->setEstado($estado);
            		}            		
            	}


            	$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            	$resultado = $em->findOneBy(array('grupo' => 1,'pregunta' => 352,'ordenInspeccion'=>$ordenInspeccion->getId()));

            	$datetime = date("Y-m-d H:i:s");
            	$fechaRecepcion = new \Datetime($datetime);				

            	$ordenInspeccion->setRealizada(1);
            	$ordenInspeccion->setCerradaAutomaticamente(1);
            	$ordenInspeccion->setFechaCerradaAutomaticamente($fechaRecepcion);
            	$inspecciones = $ordenInspeccion->getInspecciones();

            	if($resultado){            		
					$fechaInspeccion = new \Datetime($resultado->getRespuestaLibre());
					$inspecciones[count($inspecciones) - 1]->setFechaInspeccion($fechaInspeccion);//PONER LA FECHA Q MANDE LA TABLET
            	}            	
            	
            	$inspecciones[count($inspecciones) - 1]->setFechaRecepcion($fechaRecepcion);//PONER LA FECHA Q MANDE LA TABLET
            	
            	$em = $this->getDoctrine()->getManager();
            	$em->persist($ordenInspeccion);
                $em->flush();
                $em->getConnection()->close();
                $em->clear();

                return true;
            }

            return false;

        } 
	}

	private function esCierreManual($ordenVieja,$ordenNueva)
	{
		if($this->campoObligatorioCierreManual($ordenNueva)){			
			return true;
		}else{			
			if(!$ordenVieja){
				return false;
			}else{
				//por ahora lo dejo en false siempre, tengo q analizar los campos q se comparan.
				return false;
				//return $this->compararCamposCierreManual($ordenVieja,$ordenNueva);
			}
			
		}
	}

	private function campoObligatorioCierreManual($ordenNueva)
	{
		$preguntas = array();
		$cantidadPreguntas = 2;///REEMPLAZAR ESTE NUMERO POR LA CANTIDAD DE PREGUNTAS A VERIFICAR.
		$cierreManual = false;
		
		$j = 0;
		for ($i=0; $i < count($cantidadPreguntas) ; $i++) { 
			$preguntas[$i] = array();
		}
		$preguntas[0]['grupo'] = 18;
		$preguntas[0]['pregunta'] = 347;
		$preguntas[0]['respuesta'] = 1;
		$preguntas[1]['grupo'] = 18;
		$preguntas[1]['pregunta'] = 348;
		$preguntas[1]['respuesta'] = 1;
		$preguntas[2]['grupo'] = 1;
		$preguntas[2]['pregunta'] = 353;
		$preguntas[2]['respuesta'] = 2;
		$preguntas[3]['grupo'] = 1;
		$preguntas[3]['pregunta'] = 354;
		$preguntas[3]['respuesta'] = "not null";

		$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
		while ( $j < count($preguntas) && !$cierreManual) {
			$resultado = $em->findOneBy(array(
				'grupo' => $preguntas[$j]['grupo'],
				'pregunta' => $preguntas[$j]['pregunta'],
				'ordenInspeccion'=>$ordenNueva->getId()
				));			
			if($resultado && $preguntas[$j]['respuesta'] == "not null" ){
				$cierreManual=true;
			}elseif( !$resultado && is_null($preguntas[$j]['respuesta']) ){
				$cierreManual=true;
			}elseif ($resultado) {
				$respuesta = $resultado->getRespuestas();
				foreach ($respuesta as $value) {					
					if($value->getId() == $preguntas[$j]['respuesta']){						
						$cierreManual=true;
					}
				}
				if($preguntas[$j]['respuesta'] == $resultado->getRespuestaLibre() && is_null($respuesta)){
					$cierreManual=true;
				}
			}
			$j++;
		}
		return $cierreManual;        
	}

	private function compararCamposCierreManual($ordenVieja,$ordenNueva)
	{
		$preguntas = array();
		$cantidadPreguntas = 14;
		$cierreManual = false;

		$j = 0;
		for ($i=0; $i < count($cantidadPreguntas) ; $i++) { 
			$preguntas[$i] = array();
		}
		$preguntas[0]['grupo'] = 3;
		$preguntas[0]['pregunta'] = 42;
		$preguntas[1]['grupo'] = 4;
		$preguntas[1]['pregunta'] = 43;
		$preguntas[2]['grupo'] = 5;
		$preguntas[2]['pregunta'] = 69;
		$preguntas[3]['grupo'] = 6;
		$preguntas[3]['pregunta'] = 92;
		$preguntas[4]['grupo'] = 7;
		$preguntas[4]['pregunta'] = 129;
		$preguntas[5]['grupo'] = 8;
		$preguntas[5]['pregunta'] = 137;
		$preguntas[6]['grupo'] = 9;
		$preguntas[6]['pregunta'] = 349;
		$preguntas[7]['grupo'] = 10;
		$preguntas[7]['pregunta'] = 142;
		$preguntas[8]['grupo'] = 11;
		$preguntas[8]['pregunta'] = 190;
		$preguntas[9]['grupo'] = 12;
		$preguntas[9]['pregunta'] = 199;
		$preguntas[10]['grupo'] = 13;
		$preguntas[10]['pregunta'] = 245;
		$preguntas[11]['grupo'] = 14;
		$preguntas[11]['pregunta'] = 223;
		$preguntas[12]['grupo'] = 15;
		$preguntas[12]['pregunta'] = 312;
		$preguntas[13]['grupo'] = 16;
		$preguntas[13]['pregunta'] = 317;
		$preguntas[14]['grupo'] = 17;
		$preguntas[14]['pregunta'] = 325;

		$em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
		while ( $j < count($preguntas) && !$cierreManual) {
			$resultadoViejo = $em->findOneBy(array(
				'grupo' => $preguntas[$j]['grupo'],
				'pregunta' => $preguntas[$j]['pregunta'],
				'ordenInspeccion'=>$ordenVieja->getId()
				));

			$resultadoNuevo = $em->findOneBy(array(
				'grupo' => $preguntas[$j]['grupo'],
				'pregunta' => $preguntas[$j]['pregunta'],
				'ordenInspeccion'=>$ordenNueva->getId()
				));

			if($resultadoViejo->getRespuestaLibre() != $resultadoNuevo->getRespuestaLibre() || $resultadoViejo->getRespuestas() != $resultadoNuevo->getRespuestas()){
				$cierreManual=true;
			}

			$j++;
		}
	}
}