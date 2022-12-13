<?php

namespace Notificaciones\NotificacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\DateTime;

use Notificaciones\NotificacionesBundle\Form\EditDisposicionType;
use Notificaciones\NotificacionesBundle\Form\EditDisposicionClausuraType;
use Notificaciones\NotificacionesBundle\Form\NewDisposicionType;
use Notificaciones\NotificacionesBundle\Form\NewDisposicionClausuraType;

use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Notificaciones\NotificacionesBundle\Entity\Pedido;
use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Notificaciones\NotificacionesBundle\Entity\Estado;
use Notificaciones\NotificacionesBundle\Entity\Disposicion;
use Notificaciones\NotificacionesBundle\Entity\DisposicionClausura;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Logic\encriptador;

class DisposicionController extends Controller
{
    public function estaClausuradoAction($idEstablecimiento, Request $request){
        $user=$this->getUser();

        if($user){
            $clausura = array();
            if ($idEstablecimiento != '0'){
                $idEstablecimiento=encriptador::mrcrypt_decrypt($idEstablecimiento);
                $idEstablecimiento=(int)$idEstablecimiento;
            }else{
                $idEstablecimiento=0;
            }
            if ($request->isMethod('POST')) {
                $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:DisposicionClausura');
                $clausura = $em->searchClausura($idEstablecimiento);                
                $em->clear();

                if($clausura){
                    $result['result']='SI';
                    $result['data'] = encriptador::mrcrypt_encrypt($clausura[0]->getDisposicion()->getNotificacion()->getId());

                    $response = new Response(json_encode($result));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }else{
                    $result['result']='NO';
                    $result['data']='sin clausura vigente';
                    $response = new Response(json_encode($result));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function newDisposicionAction($idTipo,$idEstablecimiento, Request $request){
        $user=$this->getUser();

        if($user){
            $idEstablecimiento=encriptador::mrcrypt_decrypt($idEstablecimiento);
            $idEstablecimiento=(int)$idEstablecimiento;           

            $pedido = new Pedido();     
            $notificacion = new Notificacion();
            $disposicion = new Disposicion();
            $disposicionClausura = new DisposicionClausura();
            $direcciones = array();
            $estado = new Estado();        

            $establecimiento = new Establecimiento();        
            
            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
            $estado = $em->findOneById(2);

            $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
            $establecimiento = $em->findOneById($idEstablecimiento);

            if (!$establecimiento) {
                throw $this->createNotFoundException('Establecimiento Not found');
            }

            $direcciones = $establecimiento->getDirecciones();

            $notificacion->setDireccionNotificada($direcciones[0]->__toString());
            $notificacion->setLat($direcciones[0]->getLat());
            $notificacion->setLon($direcciones[0]->getLon());        
            $notificacion->setComunaNotificada($direcciones[0]->getComuna());        
            $notificacion->setTipoDomicilioNotificada("r");        


            $notificacion->setEstablecimiento($establecimiento);
            $notificacion->setDisposicion($disposicion);
            $disposicion->setNotificacion($notificacion);
            if($idTipo == 1){
                
                $form = $this->createForm(NewDisposicionType::class,$notificacion,array('method' => 'POST'));
            }elseif($idTipo == 2){            

                $disposicion->setClausura($disposicionClausura);
                $disposicionClausura->setDisposicion($disposicion);
                $notificacion->setPlazo1(99999);
                $notificacion->setPlazo2(99999);
                $form = $this->createForm(NewDisposicionClausuraType::class,$notificacion,array('method' => 'POST'));            
            }else{
                $response = new Response(json_encode("ERROR"));                
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }        

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);            
                if ($form->isValid() ){
                    $pedido->setFechaAutorizado(new \DateTime());
                    $pedido->setUsuarioAutorizador($this->getUser());

                    $notificacion->setFechaEnvioFirma(new \DateTime());
                    $notificacion->setFechaVueltaFirma(new \DateTime());               
                    
                    $notificacion->setPedidoNot($pedido);
                    $notificacion->setEstado($estado);
                    $notificacion->setDisposicion(null);
                    $em = $this->getDoctrine()->getManager();
                    $em->getConnection()->beginTransaction();
                    try {
                        $em->persist($pedido);
                        $em->persist($notificacion);                
                        $em->flush();

                        $notificacion->setDisposicion($disposicion);
                        $disposicion->setNotificacion($notificacion);
                        if($idTipo == 1){                        
                            $disposicion->setClausura(null);
                            $em->persist($disposicion);
                            $em->flush();    
                        }else{                        
                            $disposicion->setClausura(null);
                            $em->persist($disposicion);
                            $em->flush();
                            $disposicionClausura->setDisposicion($disposicion);
                            $disposicion->setClausura($disposicionClausura);
                            $em->persist($disposicionClausura);
                            $em->flush();
                        }
                        
                        $em->getConnection()->commit();
                        $em->getConnection()->close();
                        $em->clear();
                        $this->addFlash('success','GUARDADO');
                        
                        return $this->redirectToRoute('notificaciones_notificaciones_edit_disposicion', array('idDisposicion'=> encriptador::mrcrypt_encrypt($notificacion->getId())));
                    }
                    catch (Exception $e) {                        
                        $em->getConnection()->rollBack();
                        $em->getConnection()->close();
                        $em->clear();
                        throw $e;
                        $this->addFlash('error','ERROR No se ha podido guardar el pedido');                        
                    }

                }
            }            
            $em->clear();
            if($idTipo == 1){
                return $this->render('NotificacionesNotificacionesBundle:Default:newDispo.html.twig' , array('form' => $form->createview() ));
            }elseif($idTipo == 2){
                return $this->render('NotificacionesNotificacionesBundle:Default:newDispoClausura.html.twig' , array('form' => $form->createview() ));
            }else{
                $response = new Response(json_encode("ERROR"));                
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }

    }

    public function editDisposicionAction($idDisposicion, Request $request){
        $user=$this->getUser();

        if($user){
            if ($idDisposicion != '0'){
                $idDisposicion=encriptador::mrcrypt_decrypt($idDisposicion);
                $idDisposicion=(int)$idDisposicion;
            }else{
                $idDisposicion=0;
            }
            $notificacion = new Notificacion();
            $disposicion = new Disposicion();
            $vencimiento = array();

            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
            /** @var Disposicion $disposicion */
            $disposicion = $em->findOneByNotificacion($idDisposicion);

            if (!$disposicion) {
                throw $this->createNotFoundException('Disposicion Not found');
            }

            $notificacion = $disposicion->getNotificacion();                       
            
            $fechaNotificacion = $notificacion->getFechaNotificacion();

            if(isset($fechaNotificacion)){
                $fecha2 = clone $fechaNotificacion;
                $fecha = clone $fechaNotificacion;
                $fecha->add(new \DateInterval('P'.$notificacion->getPlazo1().'D'));        
                $fecha2->add(new \DateInterval('P'.$notificacion->getPlazo2().'D'));
                $vencimiento['primer'] = $fechaNotificacion;
                $vencimiento['segundo'] = $fecha2;
            }else{
                $vencimiento['primer'] = null;
                $vencimiento['segundo'] = null;
            }

            if($disposicion->getClausura()){
                if ($this->isGranted('ROLE_INBOX_ADMIN')) {
                    $form = $this->createForm(EditDisposicionClausuraType::class, $disposicion, array('method' => 'POST','adjunto'=> 1));
                }else{
                    $form = $this->createForm(EditDisposicionClausuraType::class, $disposicion, array('method' => 'POST'));
                }
            }else{
                if ($this->isGranted('ROLE_INBOX_ADMIN')) {
                    $form = $this->createForm(EditDisposicionType::class, $disposicion, array('method' => 'POST', 'vencimientos' => $vencimiento,'adjunto'=> 1));
                }else{
                    $form = $this->createForm(EditDisposicionType::class, $disposicion, array('method' => 'POST', 'vencimientos' => $vencimiento));
                }
            }

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);                
                if ($form->isValid() ){
                    $errorArchivo = false;
                    if ($this->isGranted('ROLE_INBOX_ADMIN')){
                        $file = $form['adjuntos']->getData();

                        if($file){
                            $extension = $file->guessExtension();
                            if($extension == 'pdf'){
                                if($disposicion->getTipo()->getId() ==11){
                                    $dispoString = str_replace("DI", "IF", $disposicion->__toString());
                                }else{
                                    $dispoString = $disposicion->__toString();
                                }
                                $file->move(__DIR__.'/../../../../web/uploads/Notificaciones', $dispoString.".pdf");
                            }else{
                                $errorArchivo = true;
                            }
                        }
                    }

                    $em = $this->getDoctrine()->getManager();
                    $notificacion = $disposicion->getNotificacion();
                    $em->persist($notificacion);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();
                    $this->addFlash('success','GUARDADO');
                }
            }            
            $em->clear();
            if($notificacion->getDisposicion()->getClausura()){
                return $this->render('NotificacionesNotificacionesBundle:Default:EditDispoClausura.html.twig' , array('form' => $form->createview() ));
            }else{
                return $this->render('NotificacionesNotificacionesBundle:Default:EditDispo.html.twig' , array('form' => $form->createview() ));
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }        
    }



    public function pdfDispoAction($dispo){
        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
        $disposicion = $em->findOneByNotificacion($dispo);
        $em->clear();
        if (!$disposicion) {
            throw $this->createNotFoundException('Establecimiento Not found');
        }

        $route = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Notificaciones/';

        if(file_exists($route.$disposicion->__toString().".pdf")){
            $mi_pdf2 = $route.$disposicion->__toString().".pdf";

            header('Content-type: application/pdf');
            readfile($mi_pdf2) or die("File not found.");
        }else{
            if(file_exists($route.str_replace("DI", "IF", $disposicion->__toString()).".pdf")){
                $mi_pdf2 = $route.str_replace("DI", "IF", $disposicion->__toString()).".pdf";

                header('Content-type: application/pdf');
                readfile($mi_pdf2) or die("File not found.");
            }else{
                return new Response('Pdf no encontrado');
            }
        }
    }
    
}
