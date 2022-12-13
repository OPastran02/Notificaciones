<?php

namespace Notificaciones\NotificacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use CoreBundle\Logic\Excel;
use AppBundle\Service\UsigWS;

use Notificaciones\NotificacionesBundle\Entity\Cedula;
use Notificaciones\NotificacionesBundle\Entity\Pedido;
use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Notificaciones\NotificacionesBundle\Entity\TipoCedula;
use Notificaciones\NotificacionesBundle\Entity\Cuerpo;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;

use Notificaciones\NotificacionesBundle\Form\CargaMasivaType;

use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Logic\encriptador;


class CargaMasivaController extends Controller
{
   public function cargarExcelAction(Request $request){
      $user=$this->getUser();

      if($user){
   		$direccionLegal = new Direccion();
   		$direccionCompAnterio = new Direccion();
   		$direccionCompPosterior = new Direccion();   		
   		$direccionBuscada;   		
   		$calle = new Calles();
   		$calleLegal = new Calles();
   		$pedido = new Pedido();
   		$datos = array();
   		$respuesta = array();
   		$notificaciones = array();
   		$filename;
   		$extencion;
   		$excel;
   		$messageError= '';
   		$messageAmbiguedad = '';
   		$notificacion;
   		$cedula;
         $usig = $this->get(UsigWS::class);

   		$form = $this->createForm(CargaMasivaType::class,$datos,array('method' => 'POST', 'action' => $this->generateUrl('notificaciones_notificaciones_carga_masiva') ));

   		if ($request->isMethod('POST')) {   	
   			$form->handleRequest($request);		
   			if ($form->isSubmitted() && $form->isValid()) {   						
   				$extencion = $form['archivo']->getData()->guessExtension();
   				$filename = $this->getUser()->getUsuario().rand(1, 99999);

   				if($extencion != 'xls' && $extencion != 'xlsx' && $extencion != 'csv'){
   					$respuesta['result'] = 'ERROR';
   					$respuesta['data'] = 'El archivo tiene un formato incorrecto';
   					$response = new Response(json_encode($respuesta));                
	                $response->headers->set('Content-Type', 'application/json');
	                return $response;
   				}else{
   					$form['archivo']->getData()->move(__DIR__.'/../../../CoreBundle/Temporary/',$filename.'.'.$extencion );
   					$excel = Excel::excelToArray(__DIR__.'/../../../CoreBundle/Temporary/'.$filename.'.'.$extencion);
   					unlink(__DIR__.'/../../../CoreBundle/Temporary/'.$filename.'.'.$extencion);

   				}   				

   				if($excel[0][0][0] == 'DOMICILIO REAL' && $excel[0][0][6] == 'DOMICILIO LEGAL' && $excel[1][0][0] == 'CALLE' && $excel[1][0][1] == 'ALTURA' && $excel[1][0][2] == 'PISO' && $excel[1][0][3] == 'DPTO' && $excel[1][0][4] == 'LOCAL' && $excel[1][0][5] == 'ENVIAR' && $excel[1][0][6] == 'RAZON SOCIAL' && $excel[1][0][7] == 'CALLE' && $excel[1][0][8] == 'ALTURA' && $excel[1][0][9] == 'PISO' && $excel[1][0][10] == 'DPTO' && $excel[1][0][11] == 'LOCAL' && $excel[1][0][12] == 'ENVIAR'){

   					$em = $this->getDoctrine()->getManager();
   					$pedido->setUsuarioAutorizador($this->getUser());
            		$pedido->setFechaAutorizado(new \DateTime());
   					$em->persist($pedido);


					for ($i=2; $i < count($excel) ; $i++) {   						
            			$calle = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles')->findOneByCalle($excel[$i][0][0]);
            			//DOMICILIO REAL
            			if(!$calle || !is_numeric($excel[$i][0][1]) ){
            				$excel[$i]['result']['REAL'] = "ERROR";
            				$excel[$i]['detail']['REAL'] = "CALLE O ALTURA REAL INCORRECTA";            				
            			}else{
            				$direccion = new Direccion();
            				$direccion->setCalle($calle);
            				$direccion->setAltura(trim($excel[$i][0][1]));
            				$direccion->setPiso(trim($excel[$i][0][2]));
            				$direccion->setDpto(trim($excel[$i][0][3]));
            				$direccion->setLocal(trim($excel[$i][0][4]));
            				if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){
            					$direccionBuscada = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion')->buscarDireccionExacta($direccion);

            					if(count($direccionBuscada)==0){
            						$em1 = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
            						$establecimientos = $em1->buscarPorSMP($direccion);

            						if(count($establecimientos) == 0 || count($establecimientos) == 1){
            							$error = '';
            							$error2 = '';
            							$direccionCompAnterio = clone $direccion;
            							$direccionCompPosterior = clone $direccion;
            							$direccionCompAnterio->setAltura($direccionCompAnterio->getAltura()-2);
            							$direccionCompPosterior->setAltura($direccionCompPosterior->getAltura()+2);

            							if($usig->normalizarDireccion($direccionCompAnterio,$error,$error2)){
            								$establecimientos2 = $em1->buscarPorSMP($direccion);
            							}

            							if($usig->normalizarDireccion($direccionCompPosterior,$error,$error2)){
            								$establecimientos3 = $em1->buscarPorSMP($direccion);
            							}

            							if(count($establecimientos2) > 0){
            								$establecimientos = array_merge($establecimientos, $establecimientos2);
            							}
            							if(count($establecimientos3) > 0){
            								$establecimientos = array_merge($establecimientos, $establecimientos3);
            							}

            						}

            						$establecimientos = array_unique($establecimientos);

            						if(count($establecimientos) > 0){
            							$excel[$i]['result']['REAL'] = "MULTIPLE";
            							$excel[$i]['detail']['REAL'] = 'Debe seleccionar un establecimiento para asociar la cédula';
            							
            							$j=0;            							
            							foreach ($establecimientos as $establecimiento) {
            								$direcciones = '';
                                    //$excel[$i]['data']['REAL']['MULTIPLE'][$j]['id']=encriptador::mrcrypt_encrypt($establecimiento->getId());
                                    $excel[$i]['data']['REAL']['MULTIPLE'][$j]['id']=$establecimiento->getId();

            								foreach ($establecimiento->getDirecciones() as $direccion) {
            									$direcciones .= $direccion->__toString(). " | ";
            								}

            								$excel[$i]['data']['REAL']['MULTIPLE'][$j]['Direccion'] = $direcciones;

            								$razonesSocial = $establecimiento->getRazonesSociales();
   									    	if(count($razonesSocial) > 0){
   									    		$excel[$i]['data']['REAL']['MULTIPLE'][$j]['razonSocial'] = $razonesSocial[0]->getRazonSocial()->__toString();
   									    	}
            								$j++;
            							}            							

            						}elseif(count($establecimientos) == 0){            							
            							$excel[$i]['result']['REAL'] = "OK";
            							$excel[$i]['detail']['REAL'] = 'Direccion Nueva';
            							$establecimiento = new Establecimiento();
            							$establecimiento->setEstado($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado')->findOneById(1));
            							$establecimiento->setExEESS(0);

            							$establecimiento->setRubroPrincipal($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal')->findOneById(1));
	            				
            							$direccion->setEstablecimiento($establecimiento);

            							$em->persist($establecimiento);
            							$em->flush($establecimiento);
            							$em->persist($direccion);
            							$em->flush($direccion);

            						}else{
            							$excel[$i]['result']['REAL'] = "ERROR";
            							$excel[$i]['detail']['REAL'] = "Algo extraño ha sucedido";
            						}									

            					}else{
            						$direccion->setEstablecimiento($direccionBuscada[0]->getEstablecimiento());
            						$direccionBuscada=null;
            						$excel[$i]['result']['REAL'] = "OK";
            						$excel[$i]['detail']['REAL'] = 'Direccion Correcta';
            					}

            				}else{
            					if($messageError != ''){
            						$excel[$i]['result']['REAL'] = "ERROR";
            						$excel[$i]['detail']['REAL'] = $messageError;            						
            					}else{
            						$excel[$i]['result']['REAL'] = "AMBIGUEDAD";
            						$excel[$i]['detail']['REAL'] = $messageAmbiguedad;
            					}
            				}
            			}
            			//DOMICILIO LEGAL
            			if(!empty($excel[$i][0][7])){
            				$calleLegal = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles')->findOneByCalle($excel[$i][0][7]);
            				if(!$calleLegal || !is_numeric($excel[$i][0][8])){
            					$excel[$i]['result'] = "ERROR";
            					$excel[$i]['detail'] = "CALLE O ALTURA LEGAL INCORRECTA";            					
            				}else{
            					$direccionLegal->setCalle($calleLegal);
            					$direccionLegal->setAltura($excel[$i][0][8]);
            					$direccionLegal->setPiso($excel[$i][0][9]);
            					$direccionLegal->setDpto($excel[$i][0][10]);
            					$direccionLegal->setLocal($excel[$i][0][11]);
            					if($usig->normalizarDireccion($direccionLegal,$messageError,$messageAmbiguedad)){
            						$excel[$i]['result']['LEGAL'] = "OK";
            						$excel[$i]['detail']['LEGAL'] = 'Direccion Correcta';
            					}else{
            						if($messageError != ''){
	            						$excel[$i]['result']['LEGAL'] = "ERROR";
	            						$excel[$i]['detail']['LEGAL'] = $messageError;	            						
	            					}else{
	            						$excel[$i]['result']['LEGAL'] = "AMBIGUEDAD";
	            						$excel[$i]['detail']['LEGAL'] = $messageAmbiguedad;
	            					}
            					}
            				}
            			}else{
            				$excel[$i]['result']['LEGAL'] = "NA";
            				$excel[$i]['detail']['LEGAL'] = 'Sin Direccion';
            			}

            			$messageError = '';
            			$messageAmbiguedad = '';

            			if($excel[$i]['result']['REAL'] == "OK" && ($excel[$i]['result']['REAL'] == "OK" || $excel[$i]['result']['REAL'] == "NA") ){
            				if($excel[$i][0][5] == "SI"){            					
            					$notificacion = new Notificacion;
	            				$cedula = new Cedula;

	            				$idEstablecimiento = $direccion->getEstablecimiento()->getId();		            				
    							$establecimiento = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento')->findOneById($idEstablecimiento);

    							if(!$establecimiento){
    								throw $this->createNotFoundException('No se pudo encontrar el establecimiento');
    							}		            				

	            				$notificacion->setCedula($cedula);
	            				$notificacion->setEstablecimiento($establecimiento);	            				
	            				$notificacion->setPlazo1($form['plazo1']->getData());
	            				$notificacion->setPlazo2($form['plazo2']->getData());
	            				$notificacion->setCitacion($form['citacion']->getData());
	            				$notificacion->setNocturnidad($form['nocturnidad']->getData());
	            				$notificacion->setDireccionNotificada($direccion->__toString());
	            				$notificacion->setLon($direccion->getLon());
	            				$notificacion->setLat($direccion->getLat());
	            				$notificacion->setComunaNotificada($direccion->getComuna());
	            				$notificacion->setTipoDomicilioNotificada('e');	            				
	            				$estado = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->findOneById(1);
	            				$notificacion->setEstado($estado);

	            				$cedula->setNotificacion($notificacion);
	            				$cedula->setTipo($form['tipo']->getData());
	            				$cedula->setNombreDestinatario($excel[$i][0][6]);
	            				$cedula->setFojas($form['fojas']->getData());
	            				$cedula->setVencer($form['vencer']->getData());	            				
	            				$cedula->setCuerpo($form['modelo']->getData()->getCuerpo());
	            				
	            				$repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
	            				$cedula->setRepository($repository);
                    			$cedula->setContador(0);                    			

	            				$notificacion->setPedidoNot($pedido);
	            				$notificacion->setCedula(null);
                           $em->persist($notificacion);
	            				$em->flush($notificacion);	            				

                           $notificacion->setCedula($cedula);
                           $cedula->setMaxNumero();
                           $em->persist($cedula);
                           $em->flush($cedula);                                                    

	            				$notificacion = null;
	            				$cedula = null;
            				}

            				if($excel[$i][0][12] == "SI"){
            					$notificacion = new Notificacion;
	            				$cedula = new Cedula;

	            				$idEstablecimiento = $direccion->getEstablecimiento()->getId();		            				
    							$establecimiento = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento')->findOneById($idEstablecimiento);

    							if(!$establecimiento){
    								throw $this->createNotFoundException('No se pudo encontrar el establecimiento');
    							}

	            				$notificacion->setCedula($cedula);
	            				$notificacion->setEstablecimiento($establecimiento);	            				
	            				$notificacion->setPlazo1($form['plazo1']->getData());
	            				$notificacion->setPlazo2($form['plazo2']->getData());
	            				$notificacion->setCitacion($form['citacion']->getData());
	            				$notificacion->setNocturnidad($form['nocturnidad']->getData());
	            				$notificacion->setDireccionNotificada($direccionLegal->__toString());
	            				$notificacion->setLon($direccionLegal->getLon());
	            				$notificacion->setLat($direccionLegal->getLat());
	            				$notificacion->setComunaNotificada($direccionLegal->getComuna());
	            				$notificacion->setTipoDomicilioNotificada('r');	            				
	            				$estado = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->findOneById(1);
	            				$notificacion->setEstado($estado);

	            				$cedula->setNotificacion($notificacion);
	            				$cedula->setTipo($form['tipo']->getData());
	            				$cedula->setNombreDestinatario($excel[$i][0][6]);
	            				$cedula->setFojas($form['fojas']->getData());
	            				$cedula->setVencer($form['vencer']->getData());	            				
	            				$cedula->setCuerpo($form['modelo']->getData()->getCuerpo());
	            				$repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
	            				$cedula->setRepository($repository);
                    			$cedula->setContador(0);
	            				
	            				$notificacion->setPedidoNot($pedido);		
                           $notificacion->setCedula(null);            				
	            				$em->persist($notificacion);
	            				$em->flush($notificacion);

                           $notificacion->setCedula($cedula);
                           $cedula->setMaxNumero();
                           $em->persist($cedula);
                           $em->flush($cedula);

	            				$notificacion = null;
	            				$cedula = null;
            				}

            				$excel[$i]['result']['GENERAL'] = "OK";
            			}else{
            				$excel[$i]['result']['GENERAL'] = "ERROR";
            			}
            		}            		

					$respuesta['result'] = 'OK';
					$respuesta['data'] = $excel;

					$respuesta['form']['pedido'] = encriptador::mrcrypt_encrypt($pedido->getId());					
					$respuesta['form']['plazo1'] = $form['plazo1']->getData();
					$respuesta['form']['plazo2'] = $form['plazo2']->getData();
					$respuesta['form']['citacion'] = $form['citacion']->getData();
					$respuesta['form']['nocturnidad'] = $form['nocturnidad']->getData();
					$respuesta['form']['tipo'] = $form['tipo']->getData()->getId();
					$respuesta['form']['fojas'] = $form['fojas']->getData();
					$respuesta['form']['vencer'] = $form['vencer']->getData();
					$respuesta['form']['modelo'] = $form['modelo']->getData()->getId();

					return $this->render('NotificacionesNotificacionesBundle:Default:resultadosCargaMasiva.html.twig' , array('data' => $respuesta ));

   				}else{
   					$respuesta['result'] = 'ERROR';
   					$respuesta['data'] = 'El archivo enviado no es el que corresponde para realizar una carga masiva';
   					$response = new Response(json_encode($respuesta));                
	                $response->headers->set('Content-Type', 'application/json');
	                return $response;
   				}
   			}
   		}

   		return $this->render('NotificacionesNotificacionesBundle:Default:cargaMasiva.html.twig' , array('form' => $form->createview() ));
      }else{
         return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
   }

   public function cargarCorregidasAction($idEstablecimiento,$idPedido,$plazo1,$plazo2,$citacion,$nocturnidad,$idTipo,$fojas,$vencer,$idModelo,$calleR,$alturaR,$pisoR,$dptoR,$localR,$enviarR,$destinatario,$calleL,$alturaL,$pisoL,$dptoL,$localL,$enviarL){

      $user=$this->getUser();

      if($user){
      		$establecimiento = new Establecimiento();
      		$pedido = new Pedido();   		
      		$tipo = new TipoCedula();
      		$modelo = new Cuerpo();
      		$calle = new Calles();
      		$calle2 = new Calles();
      		$notificacionR = new Notificacion;
      		$cedulaR = new Cedula;
      		$notificacionL = new Notificacion;
      		$cedulaL = new Cedula;
      		$direccion = new Direccion();
      		$direccionLegal = new Direccion();
      		$messageError ='';
      		$messageAmbiguedad ='';
            $em = $this->getDoctrine()->getManager();
            $usig = $this->get(UsigWS::class);

      		if($idEstablecimiento != 0){
      			$idEstablecimiento = encriptador::mrcrypt_decrypt($idEstablecimiento);
      			$establecimiento = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento')->findOneById($idEstablecimiento);
      			if(!$establecimiento){
      				throw $this->createNotFoundException('Establecimiento inexistente');
      			}
      		}
      		if($idPedido != 0){
      			$idPedido = encriptador::mrcrypt_decrypt($idPedido);
      			$pedido =$this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Pedido')->findOneById($idPedido);
      			if(!$pedido){
      				throw $this->createNotFoundException('Pedido inexistente');
      			}
      		}   		
      		if($idTipo > 0){   			
      			$tipo =$this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->findOneById($idTipo);
      			if(!$tipo){
      				throw $this->createNotFoundException('Tipo de cedula no encontrada');
      			}
      		}else{
      			throw $this->createNotFoundException('Tipo de cedula no encontrada');
      		}
      		if($idModelo > 0){   			
      			$modelo =$this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cuerpo')->findOneById($idModelo);
      			if(!$modelo){
      				throw $this->createNotFoundException('Modelo no encontrado');
      			}
      		}else{
      			throw $this->createNotFoundException('Modelo no encontrado');
      		}
   		$calle = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles')->findOneByCalle($calleR);   		
   		if(!$calle || !is_numeric($alturaR) ){
   			$respuesta['result'] = 'ERROR';
      			$respuesta['data'] = 'Calle o altura incorrecta';
      			$response = new Response(json_encode($respuesta));                
   	        $response->headers->set('Content-Type', 'application/json');
   	        return $response;
   		}


   		$direccion->setCalle($calle);
   		$direccion->setAltura($alturaR);
   		$direccion->setPiso($pisoR);
   		$direccion->setDpto($dptoR);
   		$direccion->setLocal($localR);

   		if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){

   			if(strtoupper($enviarL) == 'SI' ){
   				$calle2 = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles')->findOneByCalle($calleR);   		
   				if(!$calle2 || !is_numeric($alturaL) ){
   					$respuesta['result'] = 'ERROR';
   		   			$respuesta['data'] = 'Calle o altura incorrecta';
   		   			$response = new Response(json_encode($respuesta));                
   			        $response->headers->set('Content-Type', 'application/json');
   			        return $response;
   				}
   				$direccionLegal->setCalle($calle2);
   				$direccionLegal->setAltura($alturaL);
   				$direccionLegal->setPiso($pisoL);
   				$direccionLegal->setDpto($dptoL);
   				$direccionLegal->setLocal($localL);

   				if(!$usig->normalizarDireccion($direccionLegal,$messageError,$messageAmbiguedad)){
   					$respuesta['result'] = 'ERROR';
   		   			$respuesta['data'] = $messageError.$messageAmbiguedad;
   		   			$response = new Response(json_encode($respuesta));                
   			        $response->headers->set('Content-Type', 'application/json');
   			        return $response;
   				}
   			}
   			

   			if($idEstablecimiento == 0){			 	
   				$establecimiento->setEstado($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado')->findOneById(1));
   				$establecimiento->setExEESS(0);
   				$establecimiento->setRubroPrincipal($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal')->findOneById(1));						
   			}
   			$direccion->setEstablecimiento($establecimiento);
   			$em->persist($establecimiento);
   			$em->flush($establecimiento);
   			$em->persist($direccion);
   			$em->flush($direccion);

   			if(strtoupper($enviarR) == 'SI' ){
   				$notificacionR->setCedula($cedulaR);
   				$notificacionR->setEstablecimiento($establecimiento);				
   				$notificacionR->setPlazo1($plazo1);
   				$notificacionR->setPlazo2($plazo2);
   				$notificacionR->setCitacion($citacion);
   				$notificacionR->setNocturnidad($nocturnidad);
   				$notificacionR->setDireccionNotificada($direccion->__toString());
   				$notificacionR->setLon($direccion->getLon());
   				$notificacionR->setLat($direccion->getLat());
   				$notificacionR->setComunaNotificada($direccion->getComuna());
   				$notificacionR->setTipoDomicilioNotificada('e');	            				
   				$estado = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->findOneById(1);
   				$notificacionR->setEstado($estado);

   				$cedulaR->setNotificacion($notificacionR);
   				$cedulaR->setTipo($tipo);
   				$cedulaR->setNombreDestinatario($destinatario);
   				$cedulaR->setFojas($fojas);
   				$cedulaR->setVencer($vencer);	            				
   				$cedulaR->setCuerpo($modelo);
   				
   				$repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
   				$cedulaR->setRepository($repository);
   				$cedulaR->setContador(0);                    			

   				$notificacionR->setPedidoNot($pedido);
               $pedido->addNotificacione($notificacionR);

               $notificacionR->setCedula(null);

               $pedido->setUsuarioAutorizador($this->getUser());
               $pedido->setFechaAutorizado(new \DateTime());
               $em->persist($pedido);
   				$em->persist($notificacionR);
   				$em->flush($notificacionR);

               $notificacionR->setCedula($cedulaR);
               $cedulaR->setMaxNumero();
               $em->persist($cedulaR);
               $em->flush($cedulaR);

   			}
   			if(strtoupper($enviarL) == 'SI' ){
   				$notificacionL->setCedula($cedulaL);
   				$notificacionL->setEstablecimiento($establecimiento);				
   				$notificacionL->setPlazo1($plazo1);
   				$notificacionL->setPlazo2($plazo2);
   				$notificacionL->setCitacion($citacion);
   				$notificacionL->setNocturnidad($nocturnidad);
   				$notificacionL->setDireccionNotificada($direccionLegal->__toString());
   				$notificacionL->setLon($direccionLegal->getLon());
   				$notificacionL->setLat($direccionLegal->getLat());
   				$notificacionL->setComunaNotificada($direccionLegal->getComuna());
   				$notificacionL->setTipoDomicilioNotificada('r');	            				
   				$estado = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->findOneById(1);
   				$notificacionL->setEstado($estado);

   				$cedulaL->setNotificacion($notificacionL);
   				$cedulaL->setTipo($tipo);
   				$cedulaL->setNombreDestinatario($destinatario);
   				$cedulaL->setFojas($fojas);
   				$cedulaL->setVencer($vencer);	            				
   				$cedulaL->setCuerpo($modelo);
   				
   				$repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
   				$cedulaL->setRepository($repository);
   				$cedulaL->setContador(0);                    			

               $notificacionL->setCedula(null);

               $pedido->addNotificacione($notificacionL);
   				$notificacionL->setPedidoNot($pedido);
               $pedido->setUsuarioAutorizador($this->getUser());
               $pedido->setFechaAutorizado(new \DateTime());
               $em->persist($pedido);

   				$em->persist($notificacionL);
   				$em->flush($notificacionL);

               $notificacionL->setCedula($cedulaL);
               $cedulaL->setMaxNumero();
               $em->persist($cedulaL);
               $em->flush($cedulaL);

   			}

   			$respuesta['result'] = 'OK';
      			$respuesta['data'] = "Cedulas Creadas";
      			$response = new Response(json_encode($respuesta));                
   	        $response->headers->set('Content-Type', 'application/json');
   	        return $response;

   		}else{
   			$respuesta['result'] = 'ERROR';
      			$respuesta['data'] = $messageError.$messageAmbiguedad;
      			$response = new Response(json_encode($respuesta));                
   	        $response->headers->set('Content-Type', 'application/json');
   	        return $response;
   		}

      }else{
         return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  

   }


   public function decidirQueEstablecimientoAction($establecimientos)
   {
      $user=$this->getUser();

      if($user){
         //SAQUÉ EL undefined
         //$establecimientos = substr($establecimientos, 9);
         
         //SEPARÉ TODOS LOS POSIBLES ESTABLECIMIENTOS
         $establecimientos = explode("||||||||", $establecimientos);
         //ELIMINÉ EL ÚLTIMO ELEMENTO
         array_pop($establecimientos);
         //RECORRRO EL ARRAY DE ESTABLECIMIENTOS PARA SEPARAR CADA UNO DE LOS STRINGS QUE CORRESPONDEN A CADA ESTABLECIMIENTO
         for ($i=0; $i < count($establecimientos); $i++) {
            //SEPARO id (0), razonSocial (1) Y Direccion(2)
            $e = explode("||||", $establecimientos[$i]);
            $establecimientos[$i] = $e;
            //ENCRIPTO id
            $establecimientos[$i][0] = encriptador::mrcrypt_encrypt($establecimientos[$i][0]);
            //RETOCO Direccion. ELIMINO LA ÚLTIMA BARRA VERTICAL Y SEPARO TODAS LAS DIRECCIONES
            $establecimientos[$i][2] = substr($establecimientos[$i][2], 0, -2);
            $establecimientos[$i][2] = explode(" | ", $establecimientos[$i][2]);
         }

         return $this->render('NotificacionesNotificacionesBundle:Default:modalDecision.html.twig', array('establecimientos' => $establecimientos));
      }else{
         return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
   }


}
