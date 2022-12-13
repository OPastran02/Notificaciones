<?php

namespace Notificaciones\NotificacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Notificaciones\NotificacionesBundle\Form\NewCedulaType;
use Notificaciones\NotificacionesBundle\Form\EditCedulaType;

use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Notificaciones\NotificacionesBundle\Entity\Cedula;
use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Notificaciones\NotificacionesBundle\Entity\Pedido;
use Notificaciones\NotificacionesBundle\Entity\Estado;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PedidoCedulaController extends Controller
{
    public function newPedidoCedulaAction($idEstablecimiento, Request $request)
    {
        $user=$this->getUser();

        if($user){
            if ($idEstablecimiento != '0'){
                $idEstablecimiento=encriptador::mrcrypt_decrypt($idEstablecimiento);
                $idEstablecimiento=(int)$idEstablecimiento;
            }else{
                $idEstablecimiento=0;
            }

        	$pedido = new Pedido();    	
        	$establecimiento = new Establecimiento();
        	$razonesSociales = array();
        	$dataform = array();    	
        	$data = array();
        	$arrayNotificaciones = array();    	
        	$i = 0;        
            $hayDirecciones = false;         
            
        	$em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
            $establecimiento = $em->findOneById($idEstablecimiento);

            if (!$establecimiento) {
                throw $this->createNotFoundException('Establecimiento Not found');
            }            

            foreach ($establecimiento->getRazonesSociales() as $razonSocial) {
            	array_push($razonesSociales,$razonSocial->getRazonSocial());
            }


            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cuerpo');
            $cuerpos = $em->findAllCuerpos();

         	$form = $this->createForm(NewCedulaType::class,$dataform,array('method' => 'POST', 'action' => $this->generateUrl('notificaciones_notificaciones_new_cedula',array('idEstablecimiento' => encriptador::mrcrypt_encrypt($idEstablecimiento) )),'razonesSociales' => $razonesSociales,'establecimiento' => $establecimiento ));
                   

            if ($request->isMethod('POST')) {
            	$form->handleRequest($request);
            	if ($form->isValid()) {        		
            		$em = $this->getDoctrine()->getManager();
                    
                    while( !empty($form['direccion'.$i]) && $hayDirecciones == false  ){                 
                        if($form['enviar'.$i]->getData() == true){
                            $hayDirecciones=true;
                        }
                        $i++;
                    } 

                    $em->getConnection()->beginTransaction();

                    $i = 0;
                    if($hayDirecciones==true){
                        try {
                            $em->persist($pedido);
                            while(!empty($form['direccion'.$i]) ){
                                if($form['enviar'.$i]->getData() == true){
                                    if($form['cedula']['tipo']->getData()){
                                        $notificacion = new Notificacion();
                                        $cedula = new Cedula();                     
                                        $notificacion->setEstablecimiento($establecimiento);
                                        $notificacion->setPedidoNot($pedido);
                                        $pedido->addNotificacione($notificacion);                       

                                        $cedula->setNombreDestinatario($form['razonsocial'.$i]->getData());
                                        $cedula->setVencer($form['cedula']['vencer']->getData());
                                        $cedula->setFojas($form['cedula']['fojas']->getData());
                                        $cedula->setCuerpo($form['cedula']['cuerpo']->getData());
                                        $cedula->setActuacion($form['actuacion']->getData());
                                        $cedula->setTipo($form['cedula']['tipo']->getData());

                                        $notificacion->setPlazo1($form['cedula']['notificacion']['plazo1']->getData());
                                        $notificacion->setPlazo2($form['cedula']['notificacion']['plazo2']->getData());
                                        $notificacion->setLon($form['lon'.$i]->getData());
                                        $notificacion->setLat($form['lat'.$i]->getData());
                                        $notificacion->setComunaNotificada($form['comuna'.$i]->getData());
                                        $notificacion->setTipoDomicilioNotificada($form['tipoDomicilio'.$i]->getData());

                                        
                                        $notificacion->setCitacion($form['cedula']['notificacion']['citacion']->getData());
                                        $notificacion->setNocturnidad($form['cedula']['notificacion']['nocturnidad']->getData());
                                        $notificacion->setDireccionNotificada($form['direccion'.$i]->getData());

                                        
                                        $em->persist($notificacion);  
                                        $em->flush();

                                        $notificacion->setCedula($cedula);
                                        $cedula->setNotificacion($notificacion);
                                        $em->persist($notificacion);
                                        $em->persist($cedula);
                                        $em->flush();
                                    }                                    
                                }                   
                                $i++;                   
                            }                    
                            
                            $em->persist($pedido);
                            $em->flush();                            

                            $em->getConnection()->commit();
                            $em->getConnection()->close();
                            $em->clear();
                            $this->addFlash('success','GUARDADO');
                            return $this->redirectToRoute('notificaciones_notificaciones_edit_cedula', array('id'=> encriptador::mrcrypt_encrypt($pedido->getId())));
                        }
                        catch (Exception $e) {
                            $em->getConnection()->rollBack();
                            $em->getConnection()->close();
                            $em->clear();
                            throw $e;
                            $this->addFlash('error','ERROR No se ha podido guardar el pedido');                        
                        }
                    }else{
                        $this->addFlash('error','DEBE SELECCIONAR AL MENOS UNA DIRECCION');
                    }                
            	}
            }            
            $em->clear();
            return $this->render('NotificacionesNotificacionesBundle:Default:index.html.twig' , array('form' => $form->createview() , 'cuerpo' => $cuerpos ));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function autorizarPedidoAction($id, Request $request)
    {    
        $user=$this->getUser();

        if($user){
            if ($id != '0'){
                $id=encriptador::mrcrypt_decrypt($id);
                $id=(int)$id;
            }else{
                $id=0;
            }
        	$pedido = new Pedido();
            $estado = new Estado();
            $notificaciones = array();
            $i = 0;                

        	if ($request->isMethod('POST')) {

                $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
                $estado = $em->findOneById(1);

        		$em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Pedido');
                $pedido = $em->findOneById($id);            

                if (!$pedido) {
                    throw $this->createNotFoundException('Pedido Not found');
                }
                if(!empty($pedido->getUsuarioAutorizador())){
                    throw $this->createNotFoundException('El pedido ya esta autorizado');
                }           

                $pedido->setUsuarioAutorizador($this->getUser());
                $pedido->setFechaAutorizado(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {               

                    foreach ($pedido->getNotificaciones() as $notificacion) {
                        $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
                        $cedula = new Cedula();
                        $cedula = $notificacion->getCedula();
                        $cedula->setRepository($repository);                    
                        $cedula->setContador($i);
                        $notificacion->setEstado($estado);
                        $cedula->setMaxNumero();
                        $em->persist($cedula);       
                        $em->flush();
                        $i++;         
                    }
                    
                    $em->persist($pedido);
        			$em->flush();
        			
                    $em->getConnection()->commit();
                    $em->getConnection()->close();
                    $em->clear();
        			$response = new Response(json_encode("OK"));                
        	        $response->headers->set('Content-Type', 'application/json');
        	        return $response;
                }catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    $em->getConnection()->close();
                    $em->clear();
                    throw $e;
                    $response = new Response(json_encode("ERROR No se ha podido autorizar el pedido"));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
        	}else{
        		$response = new Response(json_encode("ERROR"));                
    	        $response->headers->set('Content-Type', 'application/json');
    	        return $response;
        	}
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

   	public function editPedidoAction($id, Request $request)
   	{
        $user=$this->getUser();

        if($user){
            if ($id != '0'){
                $id=encriptador::mrcrypt_decrypt($id);
                $id=(int)$id;
            }else{
                $id=0;
            }
       		$pedido = new Pedido();
       		$cedula = new Cedula();
       		$establecimiento = new Establecimiento();
       		$razonSocialEstablecimiento;
       		$razonesSociales = array();
       		$Notificaciones = array();   		
       		$enviadas = array();
       		$direccionesEncontradas = array();
       		$i = 0;
       		$encontrada = false;
            $hayDirecciones = false;

       		$em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Pedido');
            $pedido = $em->findOneById($id);

            if (!$pedido) {
                throw $this->createNotFoundException('Pedido Not found');
            }

            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cuerpo');
            $cuerpos = $em->findAllCuerpos();

            //CREAR FORM
            $Notificaciones = $pedido->getNotificaciones();
            $establecimiento = $Notificaciones[0]->getEstablecimiento();
            $cedula = $Notificaciones[0]->getCedula();

            foreach ($Notificaciones as $notificacion) {
            	$enviadas[$i] = array();
            	$enviadas[$i]['direcion'] = $notificacion->getDireccionNotificada();
                $enviadas[$i]['lon'] = $notificacion->getLon();
                $enviadas[$i]['lat'] = $notificacion->getLat();
            	$enviadas[$i]['razonSocial'] = $notificacion->getCedula()->getNombreDestinatario();            
                $enviadas[$i]['comuna'] = $notificacion->getComunaNotificada();
                $enviadas[$i]['tipoDomicilio'] = $notificacion->getTipoDomicilioNotificada();
            	$enviadas[$i]['enviar'] = true;
                $enviadas[$i]['legal'] = false;
            	$i++;
            }        



            $razonesSociales = $establecimiento->getRazonesSociales();
            if(isset($razonesSociales[0])){            
                if($razonesSociales[0]->getRazonSocial()->getTipo() == "F"){
                    $razonSocialEstablecimiento = $razonesSociales[0]->getRazonSocial()->getNombre1().' '.$razonesSociales[0]->getRazonSocial()->getNombre2();
                }else{
                    $razonSocialEstablecimiento = $razonesSociales[0]->getRazonSocial()->getNombre1();
                }            
            }else{
                $razonSocialEstablecimiento = '';
            }
            foreach ($razonesSociales as $razonSocial) {        	
            	foreach ($razonSocial->getRazonSocial()->getDirecciones() as $direccion) {        		
            		foreach ($enviadas as $enviada) {
            			if( $enviada['direcion'] == trim($direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal() )){
            				$encontrada = true;
                            $enviada['legal'] = true;
                            $enviada['comuna'] = $direccion->getComuna();
                            $enviada['tipoDomicilio'] = 'r';
                            
            				break;
            			}
            		}
            		if($encontrada == false){
            			$enviadas[$i]['direcion'] = trim($direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal());
                        $enviadas[$i]['lon'] = $direccion->getLon();
                        $enviadas[$i]['lat'] = $direccion->getLat();
                        $enviadas[$i]['comuna'] = $direccion->getComuna();
                        $enviadas[$i]['tipoDomicilio'] = 'r';
                        
                        if($razonSocial->getRazonSocial()->getTipo() == 'F'){
                            $enviadas[$i]['razonSocial'] = $razonSocial->getRazonSocial()->getNombre1().' '.$razonSocial->getRazonSocial()->getNombre2();
                        }else{
                            $enviadas[$i]['razonSocial'] = $razonSocial->getRazonSocial()->getNombre1();
                        }

            			
            			$enviadas[$i]['enviar'] = false;
                        $enviadas[$i]['legal'] = true;
                        
            			$i++;
            		}

            		$encontrada = false;
            	}        	
            }        
            foreach ($establecimiento->getDirecciones() as $direccion) {
            	foreach ($enviadas as $enviada) {
        			if( $enviada['direcion'] == trim($direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal() )){
        				$encontrada = true;
                        $enviada['legal'] = false;
                        $enviada['comuna'] = $direccion->getComuna();
                        $enviada['tipoDomicilio'] = 'e';
                        
        				break;
        			}
        		}
        		if($encontrada == false){
        			$enviadas[$i]['direcion'] = trim($direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal());
                    $enviadas[$i]['lon'] = $direccion->getLon();
                    $enviadas[$i]['lat'] = $direccion->getLat();
                    $enviadas[$i]['comuna'] = $direccion->getComuna();
                    $enviadas[$i]['tipoDomicilio'] = 'e';
        			$enviadas[$i]['razonSocial'] = $razonSocialEstablecimiento;
        			$enviadas[$i]['enviar'] = false;
                    $enviadas[$i]['legal'] = false;
                    
        			$i++;
        		}
        		$encontrada = false;
            }  
                  
            $actuacionEnviar = $cedula->getActuacion();
            if($actuacionEnviar){
                $actuacionEnviar = explode("-", $actuacionEnviar);
                if(count($actuacionEnviar) == 5){
                    $actuacionEnviar[2] = $actuacionEnviar[2]."-".$actuacionEnviar[3];
                    $actuacionEnviar[3] = $actuacionEnviar[4];
                    $actuacionEnviar[4] = null;
                }
                
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:TipoActuacion');            
                $actuacionEnviar[0] = $em->findOneByTipoActuacion($actuacionEnviar[0]);            
                $actuacionEnviar[0] = $actuacionEnviar[0]->getId();
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Reparticion');            
                $actuacionEnviar[2] = $em->findOneByreparticion($actuacionEnviar[2]);            
                $actuacionEnviar[2] = $actuacionEnviar[2]->getId();            

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Actuacion');            
                $actuacionEnviar = $em->findOneBy(array('tipo' => $actuacionEnviar[0], 'numero' => $actuacionEnviar[1], 'reparticion' => $actuacionEnviar[2], 'anio' => $actuacionEnviar[3])); 
            }

            $form = $this->createForm(EditCedulaType::class,$cedula,array('method' => 'POST','eviadas' => $enviadas,'idEstablecimiento' => $establecimiento->getId(),'actuacion' => $actuacionEnviar ));

            //FIN CREAR FORM

            if ($request->isMethod('POST')) {
       			$form->handleRequest($request);
            	if ($form->isValid()) {
            		$em = $this->getDoctrine()->getManager();

                    $i = 0;
                    while( !empty($form['direccion'.$i]) && $hayDirecciones == false  ){                 
                        if($form['enviar'.$i]->getData() == true){
                            $hayDirecciones=true;                        
                        }
                        $i++;
                    }                

                    if($hayDirecciones==true){
                        foreach ($Notificaciones as $notificacion) {
                            $i = 0;                 
                            $encontrada = false;                    
                            while(!empty($form['direccion'.$i]) && $encontrada == false ){                          
                                if($form['direccion'.$i]->getData() == $notificacion->getDireccionNotificada()){
                                    if($form['enviar'.$i]->getData() == true){                              
                                        $cedula = $notificacion->getCedula();

                                        $cedula->setNombreDestinatario($form['razonsocial'.$i]->getData());
                                        $cedula->setVencer($form['cedula']['vencer']->getData());
                                        $cedula->setFojas($form['cedula']['fojas']->getData());
                                        $cedula->setCuerpo($form['cedula']['cuerpo']->getData());
                                        $cedula->setActuacion($form['actuacion']->getData());
                                        $cedula->setTipo($form['cedula']['tipo']->getData());

                                        $notificacion->setLon($form['lon'.$i]->getData());
                                        $notificacion->setLat($form['lat'.$i]->getData());
                                        $notificacion->setComunaNotificada($form['comuna'.$i]->getData());
                                        $notificacion->setTipoDomicilioNotificada($form['tipoDomicilio'.$i]->getData());

                                        $notificacion->setPlazo1($form['cedula']['notificacion']['plazo1']->getData());
                                        $notificacion->setPlazo2($form['cedula']['notificacion']['plazo2']->getData());
                                        $notificacion->setCitacion($form['cedula']['notificacion']['citacion']->getData());
                                        $notificacion->setNocturnidad($form['cedula']['notificacion']['nocturnidad']->getData());
                                        $notificacion->setDireccionNotificada($form['direccion'.$i]->getData());
                                        
                                        $em->persist($notificacion);                                
                                    }else{
                                        $cedula = $notificacion->getCedula();
                                        $em->remove($cedula);
                                        $em->remove($notificacion);
                                    }
                                    
                                    $encontrada = true;
                                    $direccionesEncontradas[$i] = true;
                                }
                                $i++;
                            }
                            if($encontrada == false){
                                $cedula = $notificacion->getCedula();
                                $em->remove($cedula);
                                $em->remove($notificacion);
                            }                   
                        }
                        $i = 0;
                        while(!empty($form['direccion'.$i])){
                            if(isset($direccionesEncontradas[$i]) == false && $form['enviar'.$i]->getData() == true){
                                $newNotificacion = new Notificacion();
                                $newCedula = new Cedula();                      
                                $newNotificacion->setEstablecimiento($establecimiento);
                                $newNotificacion->setPedidoNot($pedido);
                                $pedido->addNotificacione($newNotificacion);                        

                                $newCedula->setNombreDestinatario($form['razonsocial'.$i]->getData());
                                $newCedula->setVencer($form['cedula']['vencer']->getData());
                                $newCedula->setFojas($form['cedula']['fojas']->getData());
                                $newCedula->setCuerpo($form['cedula']['cuerpo']->getData());
                                $newCedula->setActuacion($form['actuacion']->getData());
                                $newCedula->setTipo($form['cedula']['tipo']->getData());

                                $newNotificacion->setLon($form['lon'.$i]->getData());
                                $newNotificacion->setLat($form['lat'.$i]->getData());
                                $newNotificacion->setComunaNotificada($form['comuna'.$i]->getData());
                                $newNotificacion->setTipoDomicilioNotificada($form['tipoDomicilio'.$i]->getData());
                                
                                $newNotificacion->setPlazo1($form['cedula']['notificacion']['plazo1']->getData());
                                $newNotificacion->setPlazo2($form['cedula']['notificacion']['plazo2']->getData());                            
                                $newNotificacion->setCitacion($form['cedula']['notificacion']['citacion']->getData());
                                $newNotificacion->setNocturnidad($form['cedula']['notificacion']['nocturnidad']->getData());
                                $newNotificacion->setDireccionNotificada($form['direccion'.$i]->getData());                     

                                $em->persist($newNotificacion);
                                $em->flush();
                                $newNotificacion->setCedula($newCedula);

                                $newCedula->setNotificacion($newNotificacion);
                                $em->persist($newCedula);                            
                                $em->flush();

                            }
                            $i++;
                        }

                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();
                        $this->addFlash('success','GUARDADO');

                        return $this->redirectToRoute('notificaciones_notificaciones_edit_cedula', array('id'=> encriptador::mrcrypt_encrypt($pedido->getId())));

                    }else{
                        $this->addFlash('error','DEBE SELECCIONAR AL MENOS UNA DIRECCION');
                    }				

            	}

       		}            
            $em->clear();
            return $this->render('NotificacionesNotificacionesBundle:Default:index.html.twig' , array('form' => $form->createview() , 'cuerpo' => $cuerpos ));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
   	}

    public function listAutorizarAction(Request $request){
        $user=$this->getUser();

        if($user){
            $checker=$this->container->get('security.authorization_checker');

            if ($this->isGranted('ROLE_PEDIDOS_ADMIN')){   
                $tipoCedula = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->selectTipoCedulaTabla();
                $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
                return $this->render('NotificacionesNotificacionesBundle:Default:tablaAutorizar.html.twig',
                  array('inspectores'=>$inspectores,
                         'tipoCedula'=>$tipoCedula)
                 );
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }    
    }

    public function tablaAutorizarAction(Request $request){

        $user=$this->getUser();

        if($user){
            $checker=$this->container->get('security.authorization_checker');

            if ($this->isGranted('ROLE_PEDIDOS_ADMIN')){    
                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAutorizar.yml"));        
                 
                $TablaAutorizar=new TablaAjax($request,$em,$yaml);
                $TablaAutorizar->setNativeQuery('SELECT p.id,tc.TipoCedula,concat(u.apellido,", ",u.nombre) as Creador, p.fecha_creado,group_concat(n.direccion_notificada) as Direcciones FROM pedido as p INNER JOIN notificacion as n on n.id_pedido = p.id INNER JOIN cedula as c on n.id = c.id INNER JOIN tipo_cedula as tc on c.tipo_id = tc.id INNER JOIN usuarios as u on p.usuario_creador = u.id WHERE p.usuario_autorizador_id is null and n.usuario_eliminador is null GROUP BY p.id,tc.TipoCedula,Creador,p.fecha_creado HAVING ');
                
                $TablaAutorizar->setSpecialConditions("1 = 1");

                $response = new Response($TablaAutorizar->Initialize());
                $response->headers->set('Content-Type', 'application/json');                
                $em->clear();
                return $response; 
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }       
    }


    public function excelAutorizarAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            $checker=$this->container->get('security.authorization_checker');

            if ($this->isGranted('ROLE_PEDIDOS_ADMIN')){    
                  $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
                  $nombreArchivo= 'excelAutorizar'.date("d_m_Y").'.xls';
                  $nombresheet="excelAutorizar";
                  $data = $request->request->all();

                  $em = $this->get('doctrine')->getManager();
                  $em = $this->get('doctrine')->getManager('default');
                  $em = $this->get('doctrine.orm.default_entity_manager');

                  $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAutorizar.yml"));

                  //return new Response($yaml);
                  
                  $TablaAutorizar=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
                  $TablaAutorizar->setNativeQuery('SELECT p.id,tc.TipoCedula,concat(u.apellido,", ",u.nombre) as Creador, p.fecha_creado,group_concat(n.direccion_notificada) as Direcciones FROM pedido as p INNER JOIN notificacion as n on n.id_pedido = p.id INNER JOIN cedula as c on n.id = c.id INNER JOIN tipo_cedula as tc on c.tipo_id = tc.id INNER JOIN usuarios as u on p.usuario_creador = u.id WHERE p.usuario_autorizador_id is null and n.usuario_eliminador is null GROUP BY p.id,tc.TipoCedula,Creador,p.fecha_creado HAVING ');
                  $TablaAutorizar->setSpecialConditions("1 = 1");

                  $arrayTablaAutorizar=$TablaAutorizar->getQueryTable();
                 /* print_r($arrayTablaAutorizar);
                  exit();*/
                  $objPHPExcel = new \PHPExcel();

                  $objPHPExcel->setActiveSheetIndex(0);
                  $letra="a";
                  $numero=1;
                  foreach ($arrayTablaAutorizar as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                      if($numero==1){
                        $objPHPExcel->getActiveSheet()->setCellValue($letra.$numero, $key1);
                        $objPHPExcel->getActiveSheet()->setCellValue($letra.($numero+1), $value1);
                        $letra++;
                      }else{
                        $objPHPExcel->getActiveSheet()->setCellValue($letra.($numero+1), $value1);
                        $letra++;
                      }
                    }
                    $letra="a";
                    $numero++;         
                  }

                  $objPHPExcel->getActiveSheet()->setTitle($nombresheet);
                  $objPHPExcel->setActiveSheetIndex(0);


                  header('Content-Type: application/vnd.ms-excel');
                  header('Content-Disposition: attachment;filename="'. $nombreArchivo.'"');
                  header('Cache-Control: max-age=0');
                  $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                  $objWriter->save($ruta."/".$nombreArchivo);
                  $ruta = 'temp';
                  $em->clear();
                  return new BinaryFileResponse($ruta."/".$nombreArchivo);
            }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }     
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }       
    }

    public function eliminarPedidoAction(Request $request, $id){
        $user=$this->getUser();

        if($user){
            $checker=$this->container->get('security.authorization_checker');

            if ($this->isGranted('ROLE_PEDIDOS_ADMIN')){  
                if ($id != '0'){
                    $id=encriptador::mrcrypt_decrypt($id);
                    $id=(int)$id;
                }else{
                    $id=0;
                }
                $pedido = new Pedido();
                $estado = new Estado();
                $notificaciones = array();        

                if ($request->isMethod('POST')) {

                    $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
                    $estado = $em->findOneById(4);

                    $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Pedido');
                    $pedido = $em->findOneById($id);            

                    if (!$pedido) {
                        throw $this->createNotFoundException('Pedido Not found');
                    }
                    if(!empty($pedido->getUsuarioAutorizador())){
                        throw $this->createNotFoundException('El pedido ya esta autorizado');
                    }
                    
                    $em = $this->getDoctrine()->getManager();            

                    foreach ($pedido->getNotificaciones() as $notificacion) {
                        $notificacion->setEstado($estado);
                        $notificacion->setUsuarioEliminador($this->getuser()->getId());
                        $em->persist($notificacion);
                        $em->flush();
                    }                    
                    
                    $em->getConnection()->close();
                    $em->clear();

                    $response = new Response(json_encode("ELIMINADO"));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                    
                }else{
                    $response = new Response(json_encode("ERROR"));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
          }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function listMisPedidosAction(Request $request){
        $user=$this->getUser();

        if($user){
            $tipoCedula = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->selectTipoCedulaTabla();
          
            return $this->render('NotificacionesNotificacionesBundle:Default:tablaMisPedidos.html.twig',
                 array('tipoCedula'=>$tipoCedula)
                );
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function tablaMisPedidosAction(Request $request){
        $user=$this->getUser();

        if($user){
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaMisPedidos.yml"));        
             
            $TablaAutorizar=new TablaAjax($request,$em,$yaml);
            $TablaAutorizar->setNativeQuery('SELECT p.id,tc.TipoCedula,concat(u.nombre," ",u.apellido) as Creador, p.fecha_creado,group_concat(n.direccion_notificada) as Direcciones,p.fecha_autorizado FROM pedido as p INNER JOIN notificacion as n on n.id_pedido = p.id INNER JOIN cedula as c on n.id = c.id INNER JOIN tipo_cedula as tc on c.tipo_id = tc.id INNER JOIN usuarios as u on p.usuario_creador = u.id WHERE (p.usuario_autorizador_id is null or n.fecha_vuelta_firma is null) and n.usuario_eliminador is null and p.usuario_creador = :idCreador  GROUP BY p.id,tc.TipoCedula,Creador,p.fecha_creado HAVING ');
            
            $params[0][0] = 'idCreador';
            $params[0][1] = $this->getUser()->getId();
            $TablaAutorizar->setNativeQueryParam($params);
            $TablaAutorizar->setSpecialConditions("1 = 1");

            $response = new Response($TablaAutorizar->Initialize());
            $response->headers->set('Content-Type', 'application/json');            
            $em->clear();
            return $response; 
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function convertirAPdfPedidoAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            $checker=$this->container->get('security.authorization_checker');

            if ($this->isGranted('ROLE_PEDIDOS_ADMIN')){  
                $cuerpo = $request->request->get('cuerpo');
                $pdf=Array();

                $cuerpo=str_replace("[[[", "/", $cuerpo);
                $cuerpo=str_replace("'<[[[", "'</", $cuerpo);
                $cuerpo=(utf8_decode($cuerpo));
                
                $pdf["NroCedula"]="Nro Cedula: 00000000";
                $pdf["cuerpo"]=utf8_encode($cuerpo);
                $pdf["Destinatario"]="Nombre del Destinatario";
                $pdf["Domicilio"]="Domicilio del Destinatario";
                $pdf["Comuna"]="";
                $pdf["TipoDomicilio"]="DENUNCIADO o CONSTITUIDO";
                $pdf["Actuacion"]="Actuacion";        
                $pdf["Plazo"]="plazo";
                $pdf["Plazo2"]="plazo2";
                $pdf["CopiaEnFojas"]="Fojas";

               $html = $this->renderView('NotificacionesNotificacionesBundle:Default:pdfpedido.html.twig', array('array' => $pdf
                 ));

                return new Response(
                        $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                        200,
                        array(
                            'Content-Type'          => 'application/pdf',
                            'Content-Disposition'   => 'filename="file.pdf"'
                        )
                );
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }

    }
   	
}
