<?php

namespace Inspecciones\InspeccionesBundle\Controller;

use Inspecciones\InspeccionesBundle\Form\SubirChecklistType;
use Notificaciones\NotificacionesBundle\Entity\Cedula;
use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Notificaciones\NotificacionesBundle\Entity\Pedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use AppBundle\Service\UsigWS;
use CoreBundle\Logic\JsonValidator;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Establecimiento\EstablecimientoBundle\Entity\Estado;
use Establecimiento\EstablecimientoBundle\Entity\RubroPrincipal;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Inspecciones\InspeccionesBundle\Entity\Denunciante;
use Inspecciones\InspeccionesBundle\Entity\Inspeccion;
use Inspecciones\InspeccionesBundle\Entity\Circuito;
use Inspecciones\InspeccionesBundle\Entity\Resultados;
use Inspecciones\InspeccionesBundle\Entity\MotivosReInspeccion;
use Usuario\UsuarioBundle\Entity\Usuarios;

use Inspecciones\InspeccionesBundle\Form\AnularInspeccionType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionAsignacionType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionCierreVinculadoType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionCierreDesvinculadoType;
use Inspecciones\InspeccionesBundle\Form\EstablecimientoInspeccionType;
use Inspecciones\InspeccionesBundle\Form\InspeccionResultadosType;
use Inspecciones\InspeccionesBundle\Form\MotivosReInspeccionType;
use Inspecciones\InspeccionesBundle\Form\InspectoresSadeType;

use CoreBundle\Logic\encriptador;
use CoreBundle\Logic\Ruidos;
use CoreBundle\Logic\RuidosProtocolo;
use PDFMerger;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;

class InspeccionController extends Controller
{
    public function anularInspeccionAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){      
            $ordenInspeccion = new OrdenInspeccion();
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                $idOrderInspeccion=(int)$idOrderInspeccion;
            }else{
                $idOrderInspeccion=0;
            }
            
            if ($request->isMethod('POST')) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                
                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $form = $this->createForm(AnularInspeccionType::class,$ordenInspeccion,array('method' => 'POST', 'action' => $this->generateUrl('inspecciones_inspecciones_anular', array('idOrderInspeccion' => encriptador::mrcrypt_encrypt($idOrderInspeccion)) )));

                $form->handleRequest($request);
                if ($form->isValid()) { 
                    $ordenInspeccion->setAnulada(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ordenInspeccion);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();
                    
                    $response = new Response(json_encode("ANULADA"));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                $em->clear();
                return $this->render('InspeccionesInspeccionesBundle:Default:modalAnularInspeccion.html.twig' , array('form' => $form->createview() ));

            }else{
                throw $this->createNotFoundException('Page Not found');
            }
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function eliminarInspeccionAction($idOrderInspeccion,Request $request)
    {
         $user=$this->getUser();

        if($user){
            $ordenInspeccion = new OrdenInspeccion();
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                $idOrderInspeccion=(int)$idOrderInspeccion;
            }else{
                $idOrderInspeccion=0;
            } 
          $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
          $orden = $em->findOneById($idOrderInspeccion);
          $idSap=$orden->getIdSap();
          $reinspeccion = $orden->getReinspeccionProvenienciaOrdenInspeccion();

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN') || (is_null($idSap) && is_null($reinspeccion))){
                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                    
                    if(!$ordenInspeccion){
                        throw $this->createNotFoundException('Inspeccion not found');
                    }
                     
                    $ordenInspeccion->setEliminada(1);
                    $ordenInspeccion->setIdUsuarioEliminador($this->getUser()->getId());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ordenInspeccion);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();

                    $response = new Response(json_encode("ELIMINADA"));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;

                }else{
                    throw $this->createNotFoundException('Page Not found');
                }
           }else{
                $em->clear();
                if($reinspeccion){
                    $mensaje = "NO SE PUEDE ELIMINAR\n\n\n Esta orden es una reinspeccion, para eliminarla consultar en gofa\n\n";
                }else{
                    $mensaje="SIN PERMISOS";
                }

                $response = new Response(json_encode($mensaje));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }
    //DESCOMENTAR CUANDO PASE A TABLET
    //public function saveCheckListAction($idOrderInspeccion,Request $request)
    public function saveCheckListAction($idOrderInspeccion,$checkList,Request $request)//quitar este
    { 
        $user=$this->getUser();

        if($user){   
          $checker=$this->getUser()->getRoles();
          $resultadosUltimaInspeccion = null;

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){  
            if ($request->isMethod('POST')) {
                
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                $ordenInspeccion->setRepository($em);
                
                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }
                
                //DESCOMENTAR CUANDO PASE A TABLET
                //$ordenInspeccion->setMaxNumero();
                $ordenInspeccion->setChecklist($checkList);//quitar este
                $ordenInspeccion->setAutorizacion(1);//quitar este
                $ordenInspeccion->setIdUsuarioAutorizador($this->getUser()->getId());//quitar este
                $ordenInspeccion->setPrimerFechaProgramado(new \DateTime('now'));
                $ordenInspeccion->setVinculado(0);
                
                $establecimiento = $ordenInspeccion->getEstablecimiento();

                if($establecimiento){
                    $resultadosUltimaInspeccion = $establecimiento->getResultadosUltimaInspeccion();
                    if($resultadosUltimaInspeccion){
                        $resultadosUltimaInspeccion->setProximaInspeccion(null);
                    }
                }

                $validator = $this->get('validator');
                $errors = $validator->validate($ordenInspeccion);                

                if(count($errors) > 0){
                    $mostrarError = '';                    
                    foreach ($errors as $error) {
                        $mostrarError .=$error->getPropertyPath()." ".$error->getMessage().'<br>';
                    }

                    $response = new Response(json_encode("Datos invalidos <br>".$mostrarError));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }else{
                    if($em->existeCheck($ordenInspeccion)){ 
                        $em->clear();               
                        $response = new Response(json_encode("El checklist ya existe"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;                
                    }else{                
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ordenInspeccion);
                        if($resultadosUltimaInspeccion){
                            $em->persist($resultadosUltimaInspeccion);                        
                        }
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();
                        
                        $response = new Response(json_encode("Numero de CheckList: ". $ordenInspeccion->getChecklist()));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;               
                    }
                }                

            }else{
                throw $this->createNotFoundException('Page Not found');
            }
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } 
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        } 
    }

    public function editInspeccionAsignacionAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();
          $usig = $this->get(UsigWS::class);
          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){  
            $ordenInspeccion = new OrdenInspeccion();
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);            
            }else{
                $idOrderInspeccion=0;
            }
            $establecimiento = new Establecimiento();
            $direccionesEstablecimiento = array();
            $direccionesProvisorias = array();
            $direccion = '';
            $messageError = '';
            $messageAmbiguedad = '';
            $datos = array();
            $results = array();
            $results['result']="";
            $results['details'] = "";

            $direccionesProvisoriasListaOriginales = array();
            $direccionesProvisoriasLista = array();      
            $resultadosUltimaInspeccion = null;  

            if ($request->isMethod('POST')) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                
                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $establecimiento = $ordenInspeccion->getEstablecimiento();

                foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                    array_push($direccionesProvisoriasListaOriginales,$direccion);
                }

                if($establecimiento){
                    $resultadosUltimaInspeccion = $establecimiento->getResultadosUltimaInspeccion();
                    if($resultadosUltimaInspeccion){
                        $resultadosUltimaInspeccion->setProximaInspeccion(null);
                    }

                    $direccionesEstablecimiento = $establecimiento->getDirecciones();
                    foreach ($direccionesEstablecimiento as $value) {
                        $direccion .= $value->__toString(); 
                    }
                    $datos['direcciones'] = $direccion;
                    $datos['idEstalblecimiento'] = $establecimiento->getId();
                }

                $form = $this->createForm(EditInspeccionAsignacionType::class,$ordenInspeccion,array('method' => 'POST'));

                $form->handleRequest($request);            
                if ($form->isValid()) {   
                             
                    $idSap = $ordenInspeccion->getIdSap();
                    if($idSap < 2500000000){
                        $ordenInspeccion->setIdSap($idSap + 2500000000);
                    }

                    if($em->existeCheck($ordenInspeccion)){                    
                        $results['result']="ERROR";
                        $results['details'] = "El checklist ya existe";
                        $response = new Response(json_encode( $results ));
                        $response->headers->set('Content-Type', 'application/json');
                        $em->clear();
                        return $response;
                    }

                    if($em->existeIdSap($ordenInspeccion)){                    
                        $results['result']="ERROR";
                        $results['details'] = "El Id Sap ya existe";
                        $response = new Response(json_encode( $results ));
                        $response->headers->set('Content-Type', 'application/json');
                        $em->clear();
                        return $response;
                    }

                    $em = $this->getDoctrine()->getManager();
                    
                    $direccionesProvisorias = $ordenInspeccion->getDirecciones();
                    if(count($direccionesProvisorias) > 0){
                        $ordenInspeccion->setEstablecimiento(null);
                        foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                            array_push($direccionesProvisoriasLista,$direccion);                        
                        }
                        $ordenInspeccion->setDirecciones(array());
                        foreach ($direccionesProvisoriasListaOriginales as $direccion) {
                            if(!in_array($direccion,$direccionesProvisoriasLista,true) ){
                                $em->remove($direccion);
                            }
                        }
                        foreach ($direccionesProvisoriasLista as $key => $direccion) {
                            if( is_null( $direccion->getId() ) ){
                                if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){                            
                                    $direccion->setOrdenInspeccion($ordenInspeccion);                                    
                                    $em->persist($direccion);                                                              
                                }
                            }
                        }
                    }

                    if($messageError || $messageAmbiguedad){
                        $em->clear();
                        $results['result']="ERROR";
                        $results['details'] = $messageError." ".$messageAmbiguedad;
                        $response = new Response(json_encode( $results ));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }else{
                        
                        if($resultadosUltimaInspeccion){
                            $em->persist($resultadosUltimaInspeccion);
                        }
                        $em->persist($ordenInspeccion);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $results['result']="OK";
                        $results['details'] = "GUARDADO";
                        $response = new Response(json_encode($results));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;                        
                    }                    
                                    
                }
                $em->clear();

                return $this->render('InspeccionesInspeccionesBundle:Default:modalEditInspeccionAsignacion.html.twig' , array('form' => $form->createview(), 'datos' => $datos ));
            }else{
                throw $this->createNotFoundException('Page Not found');
            }
           }else{
            return new Response ("acceso denegado");
           }  
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function saveInspeccionCierreAction($idOrderInspeccion,$desvilcular = 0,Request $request)
    {
         $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();
          $usig = $this->get(UsigWS::class);

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){  
            $ordenInspeccion = new OrdenInspeccion();
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                $idOrderInspeccion=(int)$idOrderInspeccion;
            }else{
                $idOrderInspeccion=0;
            }


            $establecimiento = new Establecimiento();
            $direccionesProvisorias = array();
            $messageError = '';
            $messageAmbiguedad = '';
            $errorCheckIdSap = false;

            $inspeccionesListaOriginales = array();
            $inspeccionesLista = array();

            $direccionesListaOriginales = array();
            $direccionesLista = array();
            $direccionesProvisoriasListaOriginales = array();
            $direccionesProvisoriasLista = array();
            $results['details'] = "";
            $intimacionInmediata = true;
            $fechaInspeccion;
            $comuna = null;

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ListaActas = $em->listaActas($idOrderInspeccion);

            if ($request->isMethod('POST')) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);

                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $establecimiento = $ordenInspeccion->getEstablecimiento();

                foreach ($ordenInspeccion->getInspecciones() as $inspeccion) {
                    array_push($inspeccionesListaOriginales,$inspeccion);
                }


                if($establecimiento && $desvilcular == 0){
                    foreach ($establecimiento->getDirecciones() as $direccion) {
                        array_push($direccionesListaOriginales,$direccion);
                    }
                    $form = $this->createForm(EditInspeccionCierreVinculadoType::class,$ordenInspeccion,array('method' => 'POST'));
                }else{
                    if($desvilcular == 1){
                        $ordenInspeccion->setEstablecimiento(null);
                    }
                    foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                        array_push($direccionesProvisoriasListaOriginales,$direccion);
                    }
                    $form = $this->createForm(EditInspeccionCierreDesvinculadoType::class,$ordenInspeccion,array('method' => 'POST'));
                }

            
                $form->handleRequest($request);
                if ($form->isValid()) {                

                    $idSap = $ordenInspeccion->getIdSap();
                    if($idSap < 2500000000){
                        $ordenInspeccion->setIdSap($idSap + 2500000000);
                    }

                    if($em->existeCheck($ordenInspeccion)){
                        $results['result'] = "ERROR";
                        $results['details'] .= " - El checklist ya existe -";
                        
                        $errorCheckIdSap = true;
                    }

                    if($em->existeIdSap($ordenInspeccion)){                    
                        $results['result'] = "ERROR";
                        $results['details'] .= " - El Id Sap ya existe -";
                        $errorCheckIdSap = true;
                    }

                    if(!$errorCheckIdSap){

                        foreach ($ordenInspeccion->getInspecciones() as $inspeccion) {
                            array_push($inspeccionesLista,$inspeccion);                        
                        }

                        if(count($inspeccionesLista) < 1){
                            $results['result'] = "ERROR";
                            $results['details'] .= " - Debe tener al menos una inspeccion -";
                            $response = new Response(json_encode($results));
                            $response->headers->set('Content-Type', 'application/json');
                            return $response; 
                        } 


                        $ordenInspeccion->setInspecciones(array());
                        $em = $this->getDoctrine()->getManager();
                        foreach ($inspeccionesListaOriginales as $inspeccion) {
                            if(!in_array($inspeccion,$inspeccionesLista,true) ){
                                $em->remove($inspeccion);
                            }
                        }

                        foreach ($inspeccionesLista as $inspeccion) {
                            $inspeccion->setOrdenInspeccion($ordenInspeccion);
                            $em->persist($inspeccion);

                            if(!$inspeccion->getFechaInspeccion()){
                                $intimacionInmediata = false;
                            }
                            $fechaInspeccion = $inspeccion->getFechaInspeccion();

                            $inspectores = $inspeccion->getInspectores();

                            foreach ($inspectores as $inspector){
                                $notificador = $inspector;
                            }
                        }

                        $direccionesProvisorias = $ordenInspeccion->getDirecciones();
                        if(count($direccionesProvisorias) < 1 && !$establecimiento){
                            $results['result'] = "ERROR";
                            $results['details'] .= " - Debe tener al menos una direccion provisoria -";
                            $response = new Response(json_encode($results));
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;                       
                        }else{
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($ordenInspeccion);
                            
                            if($establecimiento && $desvilcular == 0){                               
                                foreach ($establecimiento->getDirecciones() as $direccion) {
                                    array_push($direccionesLista,$direccion);                        
                                }
                                $establecimiento->setDirecciones(array());                        
                                foreach ($direccionesLista as $key => $direccion) {
                                    if(!$comuna){
                                        $comuna=$direccion->getComuna();
                                        $direccionNotificada = $direccion->__toString();
                                    }

                                    if( is_null( $direccion->getId() ) ){                                
                                        if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){                            
                                            $direccion->setEstablecimiento($establecimiento);
                                            if($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion')->checkDireccionExiste($direccion)){
                                                $messageError .= "La direccion ".$direccion->getCalle()->getCalle()." ".$direccion->getAltura()." Piso:".$direccion->getPiso()." Dpto:".$direccion->getDpto()." Local:".$direccion->getLocal(). " ya existe en la base de datos. No se guardara <br>";                                    
                                            }else{
                                                $em->persist($direccion);
                                                
                                            }                            
                                        }
                                    }
                                }

                                $em->persist($establecimiento);
                                
                            }else{
                                foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                                    array_push($direccionesProvisoriasLista,$direccion);
                                }
                                $ordenInspeccion->setDirecciones(array());

                                foreach ($direccionesProvisoriasListaOriginales as $direccion) {
                                    if(!in_array($direccion,$direccionesProvisoriasLista,true) ){
                                        $em->remove($direccion);
                                    }
                                }
                                foreach ($direccionesProvisoriasLista as $key => $direccion) {
                                    if(!$comuna){
                                        $comuna=$direccion->getComuna();
                                        $direccionNotificada = $direccion->__toString();
                                    }
                                    if( is_null( $direccion->getId() ) ){                                
                                        if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){                            
                                            $direccion->setOrdenInspeccion($ordenInspeccion);                                    
                                            $em->persist($direccion);
                                        }
                                    }
                                }
                            }                                            

                            if($messageError || $messageAmbiguedad){
                                $em->clear();
                                $results['result']="ERROR";
                                $results['details'] .=" - ". $messageError." ".$messageAmbiguedad." - ";
                                $response = new Response(json_encode( $results ));
                                $response->headers->set('Content-Type', 'application/json');
                                return $response;
                            }else{
                                if(!$establecimiento || $desvilcular != 0){
                                    $intimacionInmediata =false;
                                }
                                
                                if($intimacionInmediata){
                                    $OrdenRepository = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                                    $crearIntimacion = $OrdenRepository->crearIntimacionInmediata($ordenInspeccion->getId(),$ordenInspeccion->getChecklist());

                                    if($crearIntimacion){
                                        $pedido = new Pedido();
                                        $notificacion = new Notificacion();
                                        $cedula = new Cedula();
                                        $pedido->setFechaAutorizado(new \DateTime('now'));
                                        $pedido->setUsuarioAutorizador($user);
                                        $em->persist($pedido);
                                        $notificacion->setEstablecimiento($establecimiento);
                                        $notificacion->setPedidoNot($pedido);
                                        $pedido->addNotificacione($notificacion);

                                        $tipoRepository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula');
                                        $tipoCedula = $tipoRepository->findOneById(34);

                                        $cedula->setNombreDestinatario("");
                                        $cedula->setVencer(1);
                                        $cedula->setFojas(1);
                                        $cedula->setCuerpo("");
                                        $cedula->setActuacion("");
                                        $cedula->setTipo($tipoCedula);
                                        $cedula->setNumero($ordenInspeccion->getChecklist() * (-1));

                                        $notificacion->setPlazo1(30);
                                        $notificacion->setPlazo2(30);
                                        $notificacion->setLon(1);
                                        $notificacion->setLat(1);
                                        $notificacion->setTipoDomicilioNotificada('e');
                                        $notificacion->setFechaNotificacion($fechaInspeccion);
                                        $notificacion->setNotificador($notificador);
                                        $notificacion->setComunaNotificada($comuna);
                                        $notificacion->setDireccionNotificada($direccionNotificada);

                                        $estadoRepository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
                                        $estadoNotificacion = $estadoRepository->findOneById(9);
                                        $notificacion->setEstado($estadoNotificacion);

                                        $notificacion->setCitacion(0);

                                        if($ordenInspeccion->getArea()->getId() == 5){
                                            $notificacion->setNocturnidad(1);
                                        }else{
                                            $notificacion->setNocturnidad(0);
                                        }

                                        $em->persist($notificacion);
                                        $em->flush();

                                        $notificacion->setCedula($cedula);
                                        $cedula->setNotificacion($notificacion);
                                        $em->persist($notificacion);
                                        $em->persist($cedula);
                                        $em->flush();
                                        $em->getConnection()->close();
                                        $em->clear();

                                        $results['details'] = "GUARDADO Y SE HA CREADO INTIMACIÓN INMEDIATA";
                                    }else{
                                        $em->flush();
                                        $em->getConnection()->close();
                                        $em->clear();
                                        $results['details'] = "GUARDADO";
                                    }

                                }else{
                                    $em->flush();
                                    $em->getConnection()->close();
                                    $em->clear();
                                    $results['details'] = "GUARDADO";
                                }

                                $results['result']="OK";

                                $response = new Response(json_encode($results));
                                $response->headers->set('Content-Type', 'application/json');
                                return $response;
                            }
                        }                        
                    }else{
                        $em->clear();
                        $response = new Response(json_encode($results));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }
                $em->clear();
                if($establecimiento && $desvilcular == 0){
                    return $this->render('InspeccionesInspeccionesBundle:Default:modalInspeccionCierreVinculada.html.twig' , array('form' => $form->createview(), 'ListaActas' => $ListaActas));
                }else{
                    return $this->render('InspeccionesInspeccionesBundle:Default:modalInspeccionCierreDesvinculada.html.twig' , array('form' => $form->createview(),'ListaActas' => $ListaActas));

                }
            }else{
                throw $this->createNotFoundException('Page Not found');
            } 
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }        
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarParaVincularAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){  
            if ($request->isMethod('POST')) {
                    $ordenInspeccion = new OrdenInspeccion();
                    if ($idOrderInspeccion != '0'){
                        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                        $idOrderInspeccion=(int)$idOrderInspeccion;
                    }else{
                        $idOrderInspeccion=0;
                    }
                $establecimiento = new Establecimiento();
                $direcciones = array();
                $direccionesAgregar = array();
                $results = array();
                $establecimientos = array();
                $direccionEncontrada;
                $direccionesEncontradas;
                $error = false;
                $i=0;
                $datos = array();

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);

                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');

                foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                    $direccionEncontrada = $em->buscarDireccionExacta($direccion);
                    if($direccionEncontrada){                        
                        $direcciones[$i] = $direccionEncontrada;
                        $i++;
                    }else{
                        array_push($direccionesAgregar, $direccion );
                    }
                }


                if($i > 0){
                    $establecimiento = $direcciones[0][0]->getEstablecimiento();
                    foreach ($direcciones as $direccion) {
                        if($establecimiento != $direccion[0]->getEstablecimiento()){
                            $datos['details'] = 'Error de Establecimiento Multiple';
                            $datos['result'] = 'ERROR';
                            $error = true;
                        }
                    }
                    if(!$error){
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

                        $em->persist($ordenInspeccion);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();
                        $datos['result'] = 'OK';
                        $datos['details'] = 'Vinculada';

                    }                    
                }else{
                    foreach ($ordenInspeccion->getDirecciones() as $direccion ) {
                        $direccionesEncontradas = $em->findBySMP($direccion->getSMP());
                        if( count($direccionesEncontradas) > 0 ){
                            foreach ($direccionesEncontradas as $value) {
                                if(!in_array($value->getEstablecimiento(), $establecimientos)){
                                    array_push($establecimientos, $value->getEstablecimiento() );
                                }
                            }
                        }
                    }

                    if( count($establecimientos) == 0 ){
                        $datos['result'] = 'NOENCONTRADO';
                    }else{
                        $datos['result'] = 'ENCONTRADOS';
                        $i = 0;
                        foreach ($establecimientos as $establecimiento) {
                            $results[$i]['id'] = encriptador::mrcrypt_encrypt($establecimiento->getId());
                            $results[$i]['direcciones'] = '';
                            foreach ($establecimiento->getDirecciones() as $direccion) {
                                $results[$i]['direcciones'] .= $direccion->__toString() . ' | ';
                            }
                            $razonesSociales = $establecimiento->getRazonesSociales();
                            if(count($razonesSociales) > 0){
                                $results[$i]['razonSocial'] = $razonesSociales[0]->getRazonSocial()->__toString();
                            }else{
                                $results[$i]['razonSocial'] = '';
                            }
                            $i++;
                        }
                        $em->clear();
                        return  $this->render('InspeccionesInspeccionesBundle:Default:establecimientosVincular.html.twig' , array('datos' => $results, 'idOrden' => encriptador::mrcrypt_encrypt($ordenInspeccion->getId()) ));                    
                    }
                }
                $em->clear();
                $response = new Response(json_encode($datos));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else{
                throw $this->createNotFoundException('Page Not found');
            }
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }    
    }    

    public function vincularOrdenInspeccionEstablecimientoAction($idOrderInspeccion,$idEstalblecimiento,Request $request){
        $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){  
            if ($request->isMethod('POST')) {

                $ordenInspeccion = new OrdenInspeccion();
                if ($idOrderInspeccion != '0'){
                    $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                    $idOrderInspeccion=(int)$idOrderInspeccion;
                }else{
                    $idOrderInspeccion=0;
                }

                $establecimiento = new Establecimiento();
                if ($idEstalblecimiento != '0'){
                    $idEstalblecimiento=encriptador::mrcrypt_decrypt($idEstalblecimiento);
                    $idEstalblecimiento=(int)$idEstalblecimiento;
                }else{
                    $idEstalblecimiento=0;
                }

                $direccionesAgregar = array();
                $direccionEncontrada;

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);

                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                $establecimiento = $em->findOneById($idEstalblecimiento);

                if(!$establecimiento){
                    throw $this->createNotFoundException('Establecimiento not found');
                }

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');

                foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                    $direccionEncontrada = $em->buscarDireccionExacta($direccion);
                    if(!$direccionEncontrada){
                        array_push($direccionesAgregar, $direccion );
                    }
                }

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

                $ordenInspeccion->setEstablecimiento($establecimiento);
                $ordenInspeccion->setDirecciones(array());

                $em->persist($ordenInspeccion);
                $em->flush();
                $em->getConnection()->close();
                $em->clear();       

                $response = new Response(json_encode("OK"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else{
                throw $this->createNotFoundException('Page Not found');
            }
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }             
    }

    public function OrdenInspeccionNuevoEstablecimientoAction($idOrderInspeccion,Request $request){
        $user=$this->getUser();

        if($user){

        $checker=$this->getUser()->getRoles();

            if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){  
                if ($request->isMethod('POST')) {

                    $ordenInspeccion = new OrdenInspeccion();
                    if ($idOrderInspeccion != '0'){
                        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                        $idOrderInspeccion=(int)$idOrderInspeccion;
                    }else{
                        $idOrderInspeccion=0;
                    }
                    
                    $establecimiento = new Establecimiento();
                    $estado = new Estado();
                    $rubroPrincial = new RubroPrincipal();
                    $error = false;
                    $direccionEncontrada;

                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado');
                    $estado = $em->findOneById(1);        
                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal');
                    $rubroPrincial = $em->findOneById(1);

                    $em = $this->getDoctrine()->getManager();
                    $establecimiento->setEstado($estado);
                    $establecimiento->setRubroPrincipal($rubroPrincial);
                    $establecimiento->setExEESS(0);

                    $em->persist($establecimiento);

                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $ordenInspeccion = $em->findOneById($idOrderInspeccion);

                    if(!$ordenInspeccion){
                        throw $this->createNotFoundException('Inspeccion not found');
                    }

                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');

                    foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');
                        $direccionEncontrada = $em->buscarDireccionExacta($direccion);
                        if($direccionEncontrada){
                           $error = true;
                        }else{
                            $em = $this->getDoctrine()->getManager();
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
                    }

                    if($error == true){
                        $em->clear();
                        $response = new Response(json_encode("ERROR DE EXISTENCIA, USER EL METODO CORRESPONDINETE"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }else{
                        foreach ($ordenInspeccion->getDirecciones() as $direccion) {
                            $em->remove($direccion);
                        }

                        $ordenInspeccion->setEstablecimiento($establecimiento);
                        $ordenInspeccion->setDirecciones(array());

                        $em->persist($ordenInspeccion);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $response = new Response(json_encode("OK"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }

                }
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function visualizarinspeccionesAction($idOrdenInspeccion,Request $request)
    { 
        $user=$this->getUser();
        $intimacionInmediata = 0;

        if($user){
          $checker=$this->getUser()->getRoles();

          if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){  
            if ($idOrdenInspeccion != '0'){
                $idOrdenInspeccion=encriptador::mrcrypt_decrypt($idOrdenInspeccion);
                $idOrdenInspeccion=(int)$idOrdenInspeccion;
            }else{
                throw $this->createNotFoundException('Inspeccion not found');
            }

            $denunciantesInicial= array();
            $resultsDenunciantes= array();

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->listaOrdenDeInspeccionesxId($idOrdenInspeccion);

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $inspecciones = $em->listaInspeccionesxId($idOrdenInspeccion);

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ListaActas = $em->listaActas($idOrdenInspeccion);

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $Denunciante = $em->findOneById($idOrdenInspeccion);
            
            if(!$Denunciante){
                throw $this->createNotFoundException('Inspeccion not found');
            }

            foreach ($Denunciante->getDenunciantes() as $denunciante) {
                array_push($denunciantesInicial,$denunciante);
            }

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            $resultado = $em->tieneCedulaInmediata($idOrdenInspeccion);

            if($resultado){
                $respuestas = getRespuestas();
                foreach ($respuestas as $key => $value) {
                    if($value->getId()==1){
                        $intimacionInmediata = 1;
                    }
                }
                
            }

            $form = $this->createForm(EstablecimientoInspeccionType::class,$Denunciante,array('method' => 'POST'));

            if ($request->isMethod('POST')) {            
                $form->handleRequest($request);

                $cumplio = $form['cumplioIntimacion']->getData();                
                if($cumplio == true){
                    $estalblecimiento = $Denunciante->getEstablecimiento();
                    if($estalblecimiento){
                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $em->cumplirIntimacionesAnteriores($estalblecimiento->getId(),$Denunciante->getChecklist());
                    }
                }

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();

                    foreach ($form['denunciantes']->getData() as $denunciante) {
                        array_push($resultsDenunciantes,$denunciante);
                    }
                    
                    foreach ($denunciantesInicial as $denunciante) {
                        if(!in_array($denunciante,$resultsDenunciantes) ){
                            $em->remove($denunciante);
                        }
                    }

                    foreach ($resultsDenunciantes as $denunciante) {                    
                        if(is_null($denunciante->getOrdenInspeccion())){                                                        
                            $denuncianteNuevo = new Denunciante();
                            $denuncianteNuevo->setOrdenInspeccion($Denunciante);
                            $denuncianteNuevo->setNombre($denunciante->getNombre());
                            $denuncianteNuevo->setApellido($denunciante->getApellido());
                            $denuncianteNuevo->setDireccion($denunciante->getDireccion());
                            $denuncianteNuevo->setTelefono($denunciante->getTelefono());
                            $em->persist($denuncianteNuevo);
                        }                    
                    }

                    $em->persist($Denunciante);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();

                    
                    $this->addFlash('success','GUARDADO');


                    return $this->redirectToRoute('inspecciones_inspecciones_visualizar_inspecciones', array('idOrdenInspeccion'=> encriptador::mrcrypt_encrypt($Denunciante->getId())));

                }else{
                    $this->addFlash('error','Revise los errores');
                }
            }
            $em->clear();
            return $this->render('InspeccionesInspeccionesBundle:Default:visualizarinspecciones.html.twig' , array(
                'form' => $form->createview(),
                'ordenInspeccion' => $ordenInspeccion,
                'inspecciones' => $inspecciones,
                'ListaActas' => $ListaActas,
                'intimacionInmediata' => $intimacionInmediata ));
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function verResultadosInspeccionAction($idOrdenInspeccion,Request $request){
        $user=$this->getUser();

        if($user){
          $checker=$this->getUser()->getRoles();

          if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){  
            if ($idOrdenInspeccion != '0'){
                $idOrdenInspeccion=encriptador::mrcrypt_decrypt($idOrdenInspeccion);
                $idOrdenInspeccion=(int)$idOrdenInspeccion;
            }else{
                throw $this->createNotFoundException('Inspeccion not found');
            }

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            $resultados = $em->findByOrdenInspeccion($idOrdenInspeccion,array('grupo' => 'ASC','pregunta' => 'ASC'));
            $em->clear();
            
            if(count($resultados) == 0){
                $this->addFlash('error','El checklist no esta disponible');
                return $this->redirectToRoute('inspecciones_inspecciones_visualizar_inspecciones', array('idOrdenInspeccion'=> encriptador::mrcrypt_encrypt($idOrdenInspeccion)));
            }

            return $this->render('InspeccionesInspeccionesBundle:Default:visualizarreesultados.html.twig' , array('resultados' => $resultados ));
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function autorizarInspeccionAction($idOrderInspeccion,Request $request)
    {
         $user=$this->getUser();

        if($user){
            $ordenInspeccion = new OrdenInspeccion();
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                $idOrderInspeccion=(int)$idOrderInspeccion;
            }else{
                $idOrderInspeccion=0;
            } 
          $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
          $orden = $em->findOneById($idOrderInspeccion);
          $idSap=$orden->getIdSap();

          if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN') || (!$this->isGranted('ROLE_REPORTEDIARIO_ADMIN') && is_null($idSap))){
                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                    
                    if(!$ordenInspeccion){
                        throw $this->createNotFoundException('Inspeccion not found');
                    }
                     
                    $ordenInspeccion->setAutorizacion(1);
                    $ordenInspeccion->setIdUsuarioAutorizador($this->getUser()->getId());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ordenInspeccion);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();

                    $response = new Response(json_encode("AUTORIZADA"));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;

                }else{
                    throw $this->createNotFoundException('Page Not found');
                }
           }else{
                $em->clear();
                $response = new Response(json_encode("SIN PERMISOS"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function verResultadoInspeccionAction($idOrderInspeccion,Request $request)
    {
        $ordenInspeccion = new OrdenInspeccion();

        
        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
        $idOrderInspeccion=(int)$idOrderInspeccion;       


        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
        $ordenInspeccion = $em->findOneById($idOrderInspeccion);
        
        $form = $this->createForm(InspeccionResultadosType::class,$ordenInspeccion,array('method' => 'POST'));

        return $this->render('InspeccionesInspeccionesBundle:Default:resultadosInspeccion.html.twig' , array('form' => $form->createview() ));
    }

    public function pdfResultadosChecklistAction($idOrderInspeccion,Request $request)
    {

        $ordenInspeccion = new OrdenInspeccion();
        $datos = array();
        $checks = array();
        $fotosInicio = array();
        $rutasBorrar = array();
        $i = 0;
        $ruidos = array();
        
        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
        $idOrderInspeccion=(int)$idOrderInspeccion;

        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
        $ordenInspeccion = $em->findOneById($idOrderInspeccion);

        if($ordenInspeccion){
            $resultados = $ordenInspeccion->getResultados();
        }else{
            throw $this->createNotFoundException('Inspeccion not found');
        }

        $establecimiento= $ordenInspeccion->getEstablecimiento();
        $inspecciones = $ordenInspeccion->getInspecciones();
        $datos['Inspector'] = '';

        if(count($inspecciones)>0){
            $inspectores = $inspecciones[count($inspecciones)-1]->getInspectores();
            foreach ($inspectores as $key => $value) {
                $datos['Inspector'] .= $value->getApellido().", ".$value->getNombre().". ";
            }
        }

        $datos['Establecimiento'] = array();
        if($establecimiento)
        {
            $actuacionAPdf=$establecimiento->getActuaciones()[0];
            $actuacionesEstablecimiento = $establecimiento->getActuaciones();

            foreach ($actuacionesEstablecimiento as $actuacion){
               if($actuacionAPdf->getReparticion()->getPrioridad()  > $actuacion->getReparticion()->getPrioridad() ){
                   $actuacionAPdf = $actuacion;
               }
            }

            $datos['Actuacion']= $actuacionAPdf;
            $datos['Establecimiento']['direcciones'] = '';            
            $direcciones = $establecimiento->getDirecciones();
            foreach ($direcciones as $value) {
                $datos['Establecimiento']['direcciones'] .= $value->__toString()." - ";
            }
            $datos['Establecimiento']['direcciones'] = substr($datos['Establecimiento']['direcciones'], 0, -2);
            $datos['Establecimiento']['comuna'] = $direcciones[0]->getComuna();
            $datos['Establecimiento']['smp'] = $direcciones[0]->getSMP();

            $razonSocial = $establecimiento->getRazonesSociales();

            if(count($razonSocial) > 0){                
                $datos['Establecimiento']['razonSocial'] = $razonSocial[0]->getRazonSocial()->getNombre1()." ".$razonSocial[0]->getRazonSocial()->getNombre2();
                $datos['Establecimiento']['cuit'] = $razonSocial[0]->getRazonSocial()->getCuit();
            }else{
                $datos['Establecimiento']['razonSocial'] ="";
                $datos['Establecimiento']['cuit'] ="";
            }


        }else{
            $datos['Actuacion']= '';
            $datos['Establecimiento']['direcciones'] = '';
            $datos['Establecimiento']['comuna'] = "";
            $datos['Establecimiento']['smp'] = "";
            $datos['Establecimiento']['razonSocial'] ="";
            $datos['Establecimiento']['cuit'] ="";
        }

        $datos['Numero'] = $ordenInspeccion->getChecklist();
        $datos['Sap'] = $ordenInspeccion->getIdSap();
        $datos['Motivo'] = $ordenInspeccion->getMotivoInspeccion()->getMotivo();
        $datos['Fecha'] = "";
        $datos['Hora Inicio'] = "";
        $datos['Hora Fin'] = "";

        foreach ($resultados as $key => $value) 
        {
            $idPregunta= $value->getPregunta()->getId();
            if($idPregunta < 365 || $idPregunta > 648){
                if( $idPregunta != 352 && $idPregunta != 357 && $idPregunta != 358 && $idPregunta != 353 && $idPregunta != 354){
                    $checks[$i] = array();
                    $checks[$i]['Grupo']= $value->getGrupo()->getNombreGrupo();
                    $checks[$i]['Pregunta']= $value->getPregunta()->getPregunta();
                    $tipoPregunta = $value->getPregunta()->getTipo()->getTipoPregunta();

                    if($tipoPregunta == "Date"){
                        $dateArray = explode("-",$value->getRespuestaLibre());
                        $checks[$i]['RespuestaLibre']= $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
                    }else{
                        $checks[$i]['RespuestaLibre']= $value->getRespuestaLibre();
                    }

                    
                    $checks[$i]['Respuesta'] = array();
                    $checks[$i]['fotos']=array();
                    $respuestas = $value->getRespuestas();
                    $fotos = $value->getFotos();

                    foreach ($respuestas as $respuesta) {
                        array_push($checks[$i]['Respuesta'], $respuesta->getRespuesta());
                    }

                    foreach ($fotos as $foto) {
                        array_push($checks[$i]['fotos'], $foto->getFoto());
                    }

                    $i++;
                }else{
                    if($this->getUser()->getArea()->getId() == 7 && ($idPregunta == 353 ||  $idPregunta == 354)){
                       $checks[$i] = array();
                        $checks[$i]['Grupo']= $value->getGrupo()->getNombreGrupo();
                        $checks[$i]['Pregunta']= $value->getPregunta()->getPregunta();
                        $tipoPregunta = $value->getPregunta()->getTipo()->getTipoPregunta();

                        if($tipoPregunta == "Date"){
                            $dateArray = explode("-",$value->getRespuestaLibre());
                            $checks[$i]['RespuestaLibre']= $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
                        }else{
                            $checks[$i]['RespuestaLibre']= $value->getRespuestaLibre();
                        }

                        
                        $checks[$i]['Respuesta'] = array();
                        $checks[$i]['fotos']=array();
                        $respuestas = $value->getRespuestas();
                        $fotos = $value->getFotos();

                        foreach ($respuestas as $respuesta) {
                            array_push($checks[$i]['Respuesta'], $respuesta->getRespuesta());
                        }

                        foreach ($fotos as $foto) {
                            array_push($checks[$i]['fotos'], $foto->getFoto());
                        }

                        $i++; 
                    }


                    switch ($idPregunta) {
                        case 352:
                            $dateArray = explode("-",$value->getRespuestaLibre());
                            $datos['Fecha'] = $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
                            break;
                        case 357:
                            $datos['Hora Inicio'] = $value->getRespuestaLibre();
                            break;
                        case 358:
                            $datos['Hora Fin'] = $value->getRespuestaLibre();
                            break;
                    }                
                }
            }else{
                $ruidos[$idPregunta] = array();
                $ruidos[$idPregunta]['respuesta'] = $value->getRespuestaLibre();
                $respuestas = $value->getRespuestas();
                foreach ($respuestas as $respuesta) {                    
                    $ruidos[$idPregunta]['respuesta'] = $respuesta->getRespuesta();

                    $ruidos[$idPregunta]['id'] =$respuesta->getOriginalId();
                }                
            }
        }

        $repoZonificacion = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Zonificacion');
        $repoAmbientes = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Ambientes');
        $repoCorrrecion = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:CorrecionRuidos');
        $repoEquipos = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:EquipoRuidos');

        if(count($ruidos) > 0){
            $ruidos = RuidosProtocolo::matriz($ruidos,$repoZonificacion,$repoAmbientes,$repoCorrrecion,$repoEquipos);    
        }
        

        $datos['Fecha'] = $datos['Fecha']." ".$datos['Hora Inicio']."-".$datos['Hora Fin'];

        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
        $resultadoFoto = $em->findOneBy(array('ordenInspeccion'=>$ordenInspeccion->getId(),'pregunta'=>1,'grupo'=>1));
        if($resultadoFoto){
            $fotosInicio = $resultadoFoto->getFotos();    
        }
        if(count($fotosInicio) >0){
            $fotoInicio = $fotosInicio[0]->getFoto();
        }else{
            $fotoInicio = 0;
        }
        
        $pdfs = new PDFMerger();

        $area = $ordenInspeccion->getArea()->getId();

        $html = $this->renderView('InspeccionesInspeccionesBundle:Default:pdfChecklist.html.twig', array('respuestas' => $checks, 'datos' => $datos,'fotoInicio'=>$fotoInicio,'pdfs' => $pdfs,'area'=>$area, 'ruidos'=>$ruidos));

        $rutaPdfTemp = $this->container->getParameter('kernel.root_dir').'/../var/cache/prod/temp'.$ordenInspeccion->getId().strtotime('now').'.pdf';
                
        $this->get('knp_snappy.pdf')->generateFromHtml($html, $rutaPdfTemp);        
        $pdfs->addPDF($rutaPdfTemp, 'all');

        for ($i=1; $i < 10 ; $i++) { 
            $rutaPdf = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Adjuntos/'.$ordenInspeccion->getId()."-".$i.'.pdf';
            if(file_exists($rutaPdf)){
                $rutaPdfTemp2 = $this->container->getParameter('kernel.root_dir').'/../var/cache/prod/temp'.$ordenInspeccion->getId().strtotime('now').$i.'.pdf';

                $command = new GhostscriptConverterCommand();
                $filesystem = new Filesystem();
                $converter = new GhostscriptConverter($command, $filesystem);
                $converter->convert($rutaPdf, '1.4',$rutaPdfTemp2);

                if(file_exists($rutaPdfTemp2)){
                    $pdfs->addPDF($rutaPdfTemp2, 'all');
                    array_push($rutasBorrar, $rutaPdfTemp2);
                }else{
                    $pdfs->addPDF($rutaPdf, 'all');    
                }
            }
        }

        $rutaPdf = $this->container->getParameter('kernel.root_dir').'/../var/cache/prod/temp'.$ordenInspeccion->getId().strtotime('now').'.pdf';

        $elpdf = $pdfs->merge('file', $rutaPdf);

        unlink($rutaPdfTemp);
        foreach ($rutasBorrar as $ruta) {
            unlink($ruta);
        }

        //return new BinaryFileResponse($rutaPdf);
        return (new BinaryFileResponse($rutaPdf))->deleteFileAfterSend(true);
        
    }

    public function reInspeccionarAction($idOrderInspeccion,Request $request)
    {
         $user=$this->getUser();         

        if($user){
            $ordenInspeccion = new OrdenInspeccion();
            $motivo = new MotivosReInspeccion();
            
            $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
            $idOrderInspeccion=(int)$idOrderInspeccion;
            

            if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN') || (!$this->isGranted('ROLE_REPORTEDIARIO_ADMIN') ) ){
                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                    
                    if(!$ordenInspeccion){
                        throw $this->createNotFoundException('Inspeccion not found');
                    }

                    $reinspeccion = $ordenInspeccion->getReinspeccionar();

                    if($reinspeccion == 1){
                        $response = new Response(json_encode("Ya se ha solicitado la Re-Inspeccion"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }

                    $motivo->setOrdenInspeccion($ordenInspeccion);

                    $form = $this->createForm(MotivosReInspeccionType::class,$motivo,array('method' => 'POST', 'action' => $this->generateUrl('inspecciones_inspecciones_reinspeccionar', array('idOrderInspeccion' => encriptador::mrcrypt_encrypt($idOrderInspeccion)) )));

                    $form->handleRequest($request);
                    if ($form->isValid()){

                        $ordenInspeccion->setReinspeccionar(1);
                        $ordenInspeccion->setReinspeccionarUsuario($this->getUser()->getId());
                        $motivo->setGofa(true);
                        $motivo->setIdUsuarioCreador($this->getUser());
                        $motivo->setDesestimarReinspeccion(false);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ordenInspeccion);
                        $em->persist($motivo);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $response = new Response(json_encode("Enviado"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    
                    return $this->render('InspeccionesInspeccionesBundle:Default:reInspeccionModal.html.twig' , array('form' => $form->createview(), 'idOrden' =>$idOrderInspeccion ));                    

                }else{
                    throw $this->createNotFoundException('Page Not found');
                }
           }else{
                $em->clear();
                $response = new Response(json_encode("SIN PERMISOS"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function replyReInspeccionAction($idMotivoReInspeccion,Request $request)
    {        
        $user=$this->getUser();         

        if($user){
            $ordenInspeccion = new OrdenInspeccion();
            $motivo = new MotivosReInspeccion();
            $reply = new MotivosReInspeccion();
            
            $idMotivoReInspeccion=encriptador::mrcrypt_decrypt($idMotivoReInspeccion);
            $idMotivoReInspeccion=(int)$idMotivoReInspeccion;
            
        
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:MotivosReInspeccion');
            $motivo = $em->findOneById($idMotivoReInspeccion);
            
            if(!$motivo){
                throw $this->createNotFoundException('Inspeccion not found');
            }

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->findOneById($motivo->getOrdenInspeccion()->getId());

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:MotivosReInspeccion');
            $motivosAnteriores = $em->findByOrdenInspeccion($motivo->getOrdenInspeccion()->getId());
            
            $reply->setOrdenInspeccion($ordenInspeccion);

            $form = $this->createForm(MotivosReInspeccionType::class,$reply,array('method' => 'POST', 'action' => $this->generateUrl('inspecciones_inspecciones_reinspeccionarpage', array('idMotivoReInspeccion' => encriptador::mrcrypt_encrypt($idMotivoReInspeccion)) )));

            if($this->getUser()->getArea()->getId() == 7){
                $botonDesestimar = 1;
            }else{
                $botonDesestimar = 0;
            }

            if ($request->isMethod('POST')) {                
                $form->handleRequest($request);
                if ($form->isValid()){

                    if($this->getUser()->getArea()->getId() == 7){                        
                        $reply->setGofa(true);
                    }else{                        
                        $reply->setGofa(false);
                    }
                    
                    $reply->setIdUsuarioCreador($this->getUser());
                    $reply->setDesestimarReinspeccion(false);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ordenInspeccion);
                    $em->persist($reply);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();

                    return $this->redirect($this->generateUrl('inspecciones_inspecciones_reinspeccionarpage', array('idMotivoReInspeccion' => encriptador::mrcrypt_encrypt($idMotivoReInspeccion))));
                }

            }
            return $this->render('InspeccionesInspeccionesBundle:Default:reInspeccionModalPage.html.twig' , array('form' => $form->createview(), 'idOrden' =>$ordenInspeccion->getId(),'motivosAnteriores' => $motivosAnteriores,'idMotivoReInspeccion'=>$idMotivoReInspeccion,'botonDesestimar'=>$botonDesestimar ));
           
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function replyDesestimarAction($idMotivoReInspeccion,Request $request){
        $user=$this->getUser();         

        if($user){
            $idMotivoReInspeccion=encriptador::mrcrypt_decrypt($idMotivoReInspeccion);
            $idMotivoReInspeccion=(int)$idMotivoReInspeccion;

            if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN') || (!$this->isGranted('ROLE_REPORTEDIARIO_ADMIN') ) ){
                $motivo = new MotivosReInspeccion();
            
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:MotivosReInspeccion');
                $motivo = $em->findOneById($idMotivoReInspeccion);

                if(!$motivo){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($motivo->getOrdenInspeccion()->getId());

                $ordenInspeccion->setReinspeccionar(0);


                if ($request->isMethod('POST')) {
                    $motivo->setDesestimarReinspeccion(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($motivo);
                    $em->persist($ordenInspeccion);
                    $em->flush($motivo);
                    $em->flush($ordenInspeccion);
                    $em->getConnection()->close();
                    $em->clear();
                }

                $em->clear();
                $response = new Response(json_encode("OK"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;

            }else{
                $em->clear();
                $response = new Response(json_encode("SIN PERMISOS"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
           }

        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function verObservacionesMotivoInspeccionAction($idOrderInspeccion,Request $request)
    {
        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
        $idOrderInspeccion=(int)$idOrderInspeccion;

        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
        $ordenInspeccion = $em->findOneById($idOrderInspeccion);
        
        if(!$ordenInspeccion){
            throw $this->createNotFoundException('Inspeccion not found');
        }

        return new Response($ordenInspeccion->getObservacionesMotivoInspeccion());
    }

    public function enviarARevisionAction($idOrderInspeccion,$motivo,Request $request)
    {
        $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
        $idOrderInspeccion=(int)$idOrderInspeccion;

        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
        $ordenInspeccion = $em->findOneById($idOrderInspeccion);
        
        if(!$ordenInspeccion){
            throw $this->createNotFoundException('Inspeccion not found');
        }

        if($ordenInspeccion->getCompleta()){
            $fechaCompletado = $ordenInspeccion->getFechaInspeccionCompleta();
            $revision = $ordenInspeccion->getRevisionTablet();

            if($revision){
                return new Response("ya se ha enviado a revision con anterioridad");
            }else{
                $datetime2 = new \DateTime('now');
                $interval = $fechaCompletado->diff($datetime2);
                $signo = $interval->format('%R');
                $dias = $interval->format('%a');
                
                if($signo == "+" && (int)$dias <= 7 ){
                    $ordenInspeccion->setCompleta(false);
                    $ordenInspeccion->setRevisionTablet(true);
                    $ordenInspeccion->setRevisionObs($motivo);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ordenInspeccion);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();
                    return new Response("Enviada.");
                }else{
                    return new Response("Ya se han superado los dias para poder realizar la revisión por el inspector.");
                }

            }            
        }

        return new Response("No se puede enviar a revision ya que la inspeccion no ha completado aun.");


    }

    public function pdfChecklistAction($checklist,Request $request)
    {
        if(file_exists($this->container->getParameter('kernel.root_dir').'/../web/uploads/Inspecciones/CH'.$checklist.'.pdf')){
            $mi_pdf2 = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Inspecciones/CH'.$checklist.'.pdf';

            header('Content-type: application/pdf');
            readfile($mi_pdf2) or die("File not found.");            

        }elseif(file_exists($this->container->getParameter('kernel.root_dir').'/../web/uploads/Inspecciones/ch'.$checklist.'.pdf')){
            $mi_pdf2 = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Inspecciones/ch'.$checklist.'.pdf';

            header('Content-type: application/pdf');
            readfile($mi_pdf2) or die("File not found.");
        
        }else{             
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->findOneByChecklist($checklist);
            $cerrada = true;
            if($ordenInspeccion){
                $resultados = $ordenInspeccion->getResultados();
                if(count($resultados)>0){
                    $user=$this->getUser();
                    if($user)
                    {
                        
                            return $this->pdfResultadosChecklistAction(encriptador::mrcrypt_encrypt($ordenInspeccion->getId()),$request);                    
                        
                    }
                    else{                       
                        $fechas = $ordenInspeccion->getInspecciones();
                        foreach ($fechas as $fecha) {
                            if( is_null( $fecha->getFechaInspeccion() ) ){
                                $cerrada = false;
                            }
                        }

                        if( ($cerrada && !is_null( $ordenInspeccion->getEstablecimiento() ) ) || $ordenInspeccion->getCerradaAutomaticamente() == 1 ){
                            return $this->pdfResultadosChecklistAction(encriptador::mrcrypt_encrypt($ordenInspeccion->getId()),$request);
                        }else{
                            return new Response("La inspeccion aun no ha sido cerrada");
                        }
                    }
                }else{
                    return new Response("La inspeccion aun no ha sido entregada por el inspector o no ha sido escaneada");
                }
            }else{
                return new Response("Inspeccion no encontrada");
            }
        }              
    }

    public function saveInspectorSadeAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
            $checker=$this->getUser()->getRoles();         
            
            if ($idOrderInspeccion != '0'){
                $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                $idOrderInspeccion=(int)$idOrderInspeccion;
            }

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->findOneById($idOrderInspeccion);

            if(!$ordenInspeccion){
                throw $this->createNotFoundException('Inspeccion not found');
            }

            if ($request->isMethod('POST')) {                                

                $form = $this->createForm(InspectoresSadeType::class,$ordenInspeccion,array('method' => 'POST'));
                $form->handleRequest($request);
                
                if ($form->isValid()) {

                    $file = $form['adjunto']->getData();
                    $ifGra = $form['ifGra']->getData();

                    if(!$ifGra){
                        $response = new Response(json_encode("DEBE COMPLETAR EL CAMPO INFORME GRAFICO!!"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }

                    $extension = $file->guessExtension();
                    if($extension == 'pdf'){
                        /*$ruta = __DIR__.'/../../../../web/uploads/Checks';
                        $file->move($ruta, "PO-".$ordenInspeccion->getId().".pdf");

                        copy($ruta.'/'."PO-".$ordenInspeccion->getId().".pdf", "/opt/lampp/htdocs/Notificaciones/web/uploads/Inspecciones/CH".$ordenInspeccion->getChecklist().".pdf");*/

                        $ruta = __DIR__.'/../../../../web/uploads/Inspecciones';
                        $file->move($ruta, "CH".$ordenInspeccion->getChecklist().".pdf");                        

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ordenInspeccion);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();
                        
                        $response = new Response(json_encode("GUARDADO"));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                        
                    }else{
                        $response = new Response(json_encode("EL ARCHIVO SELECCIONADO NO ES COMPATIBLE.\n DEBE SER UN PDF."));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }                    
                }
                $em->clear();
                return $this->render('InspeccionesInspeccionesBundle:Default:saveInspectorSade.html.twig' , array('form' => $form->createview() ));

            }else{
                throw $this->createNotFoundException('Page Not found');
            }           
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function vincularIfgraInspeccionAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
            $ordenInspeccion = new OrdenInspeccion();
          
            if ($request->isMethod('POST')) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                
                if(!$ordenInspeccion){
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $vincular = $ordenInspeccion->getVinculado();

                if($vincular == 1){
                    $vincular = 0;
                }else{
                    $vincular = 1;
                }
                 
                $ordenInspeccion->setVinculado($vincular);
                $ordenInspeccion->setFechaVinculado(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($ordenInspeccion);
                $em->flush();
                $em->getConnection()->close();
                $em->clear();

                $response = new Response(json_encode("Guardado"));
                $response->headers->set('Content-Type', 'application/json');
                return $response;

            }else{
                throw $this->createNotFoundException('Page Not found');
            } 
        }          
         
    }

    public function rechazarPdfInspeccionAction($idOrderInspeccion,Request $request)
    {
        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');        

        if ($idOrderInspeccion != '0'){
            $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
            $idOrderInspeccion=(int)$idOrderInspeccion;            

            $ordenInspeccion = $em->findOneById($idOrderInspeccion);

            $ordenInspeccion->setIfGra(null);    

            $em = $this->getDoctrine()->getManager();        
            $em->persist($ordenInspeccion);
            $em->flush();
            $em->getConnection()->close();
            $em->clear();

            $rutaPdf = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Inspecciones/CH'.$ordenInspeccion->getChecklist().'.pdf';
            if(file_exists($rutaPdf)){
                unlink($rutaPdf);
            }

            $response = new Response(json_encode("Ok"));
        }else{

            $response = new Response(json_encode("Error"));
        }


        return $response;
    }

    public function subirCheckListAction(Request $request){
        $user=$this->getUser();

        if($user){
            if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){

                $form = $this->createForm(SubirChecklistType::class);

                if ($request->isMethod('POST')) {
                    $form->handleRequest($request);
                    if ($form->isValid() ){
                        /** @var File $file */
                        $file = $form['adjuntos']->getData();
                        $numero = $form['numero']->getData();

                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $ordenInspeccion = $em->findOneByChecklist($numero);

                        if($ordenInspeccion){
                            if($file){
                                $extension = $file->guessExtension();
                                if($extension == 'pdf'){
                                    $file->move(__DIR__.'/../../../../web/uploads/Inspecciones', "CH".$ordenInspeccion->getChecklist().".pdf");
                                    $this->addFlash('success', 'PDF subido correctamente');
                                }else{
                                    $this->addFlash('error', "Extencion de archivo inválida, solo se permite pdf");
                                }
                            }
                        }else{
                            $this->addFlash('error', "Inspección no encontrada");
                        }
                    }
                }

                return $this->render('InspeccionesInspeccionesBundle:Default:subirChecklist.html.twig' , array('form' => $form->createview() ));

            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

}