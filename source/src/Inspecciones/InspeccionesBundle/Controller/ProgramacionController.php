<?php

namespace Inspecciones\InspeccionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use CoreBundle\Logic\JsonValidator;
use AppBundle\Service\UsigWS;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Inspecciones\InspeccionesBundle\Entity\Inspeccion;
use Inspecciones\InspeccionesBundle\Entity\Circuito;
use Usuario\UsuarioBundle\Entity\Usuarios;

use Inspecciones\InspeccionesBundle\Form\ProgramarInspeccionType;
use Inspecciones\InspeccionesBundle\Form\InspeccionType;
use Inspecciones\InspeccionesBundle\Form\ProgramarInspeccionSinEstablecimientoType;

use CoreBundle\Logic\encriptador;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


class ProgramacionController extends Controller
{
    public function indexAction (Request $request)
    {
         $user=$this->getUser();

        if($user){

          if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){  
                if ($request->isMethod('GET')) {
                    $usuario=$this->getUser();
                    $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
                    $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->selectCircuitoTabla();
                    return $this->render('InspeccionesInspeccionesBundle:Default:index.html.twig' , 
                    array(
                    'inspectores'=>$inspectores,
                    'circuito'=>$circuito,
                    'usuario'=>$usuario
                    ));
                }
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } 
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }      
    }

    public function buscarPorRubroPrincipalAction($idRubroPrincipal='0',$fecha = null,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){  
                $establecimientos = array();
                $area = $this->getUser()->getArea()->getArea();

                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                    $establecimientos = $em->findAllByIdRubroPrincipal($idRubroPrincipal,$fecha,$area);
                    $em->clear();

                    for ($i=0; $i < count($establecimientos) ; $i++) { 
                        $establecimientos[$i]['id'] = encriptador::mrcrypt_encrypt($establecimientos[$i]['id']);
                    }

                    $response = new Response(json_encode($establecimientos));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
               }else{           
                    throw $this->createNotFoundException('Page Not found');            
               }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }       */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }   

    public function buscarPorTipoCedulaAction($idTipoCedula,$fecha = null,Request $request)
    {
        $user=$this->getUser();

        if($user){
            
           $checker=$this->getUser()->getRoles();
           $idArea = $this->getUser()->getArea()->getId();
           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){  
                if($this->getUser()->getArea()->getArea() == 'NOCTURNIDAD'){
                    $nocturno = 1;
                }else{
                    $nocturno = 0;    
                }
                
                $establecimientos = array();

                if ($request->isMethod('POST')) {           
                    $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                    $establecimientos = $em->listEstablecimientosConCedulaVencida($idTipoCedula,$nocturno,$fecha,$idArea);
                    $em->clear();

                    for ($i=0; $i < count($establecimientos) ; $i++) { 
                        $establecimientos[$i]['id'] = encriptador::mrcrypt_encrypt($establecimientos[$i]['id']);
                        $establecimientos[$i]['idTipoCedula'] = $establecimientos[$i]['idRubroPrincipal'];
                        unset($establecimientos[$i]['idRubroPrincipal']);
                    }
                    
                    $response = new Response(json_encode($establecimientos));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
               }else{           
                    throw $this->createNotFoundException('Page Not found');            
               }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }   */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        } 
    }

    public function buscarPorProgramadaAreaAction($fecha,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){         
                    $establecimientos = array();
                    $idArea = $this->getUser()->getArea()->getId();

                    if ($request->isMethod('POST')) {           
                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                        $establecimientos = $em->listProgramacionVencida($idArea,$fecha);                        
                        $em->clear();

                        for ($i=0; $i < count($establecimientos); $i++) { 
                            $establecimientos[$i]['id'] = encriptador::mrcrypt_encrypt($establecimientos[$i]['id']);
                            $establecimientos[$i]['tipo']='programada';
                        }

                        $response = new Response(json_encode($establecimientos));                
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                   }else{           
                        throw $this->createNotFoundException('Page Not found');            
                   }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }  */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarPorIntimacionInmediataAction($fecha,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){         
                    $intimaciones = array();
                    $idArea = $this->getUser()->getArea()->getId();

                    if ($request->isMethod('POST')) {           
                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $intimaciones = $em->intimacionInmediataVencida($idArea,$fecha);                        
                        $em->clear();

                        for ($i=0; $i < count($intimaciones); $i++) { 
                            $intimaciones[$i]['id'] = encriptador::mrcrypt_encrypt($intimaciones[$i]['id']);
                            $intimaciones[$i]['tipo']='intimacionInmediata';
                        }

                        $response = new Response(json_encode($intimaciones));                
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                   }else{           
                        throw $this->createNotFoundException('Page Not found');            
                   }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }  */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarPorInspeccionarCadaAction($fecha,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){    
                $establecimientos = array();
                $idArea = $this->getUser()->getArea()->getId();

                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $establecimientos = $em->listInspeccionarCadaVencido($idArea,$fecha);                    
                    $em->clear();

                    for ($i=0; $i < count($establecimientos); $i++) { 
                        $establecimientos[$i]['id'] = encriptador::mrcrypt_encrypt($establecimientos[$i]['id']);
                        $establecimientos[$i]['tipo']='automatica';
                    }
                    
                    $response = new Response(json_encode($establecimientos));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
               }else{
                    throw $this->createNotFoundException('Page Not found');
               }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }  */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarPorNuncaInspeccionadaAction($fecha,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){    
                $establecimientos = array();
                $idArea = $this->getUser()->getArea()->getId();

                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $establecimientos = $em->nuncaInspeccionada($idArea,$fecha);                    
                    $em->clear();

                    for ($i=0; $i < count($establecimientos); $i++) { 
                        $establecimientos[$i]['id'] = encriptador::mrcrypt_encrypt($establecimientos[$i]['id']);
                        $establecimientos[$i]['tipo']='nuncaInspeccionada';
                    }
                    
                    $response = new Response(json_encode($establecimientos));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
               }else{
                    throw $this->createNotFoundException('Page Not found');
               }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }  */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarPorReprogramacionAction($tipo,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){    
                $establecimientos = array();
                $ordenes = array();
                $resultado = array();
                $idArea = $this->getUser()->getArea()->getId();

                if ($request->isMethod('POST')) {
                    if($tipo == 0){
                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                        $establecimientos = $em->listReProgramadaVinculada($idArea);

                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $ordenes = $em->listReProgramadaNoVinculada($idArea);                    
                        $em->clear();
                    }else{
                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                        $establecimientos = $em->listReInspeccionarVinculada($idArea);

                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $ordenes = $em->listReInspeccionarNoVinculada($idArea);                    
                        $em->clear();                        
                    }

                    $resultado = array_merge($establecimientos, $ordenes);

                    for ($i=0; $i < count($resultado) ; $i++) { 
                        $resultado[$i]['id'] = encriptador::mrcrypt_encrypt($resultado[$i]['id']);
                        if($tipo == 0){
                            $resultado[$i]['tipo']='Reprogramar';    
                        }else{
                            $resultado[$i]['tipo']='Reinspeccionar';    
                        }
                        
                    }

                    $response = new Response(json_encode($resultado));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                    
               }else{           
                    throw $this->createNotFoundException('Page Not found');            
               }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }  */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function reprogramarAction($idOrderInspeccion,$crearNueva = true,Request $request)
    {     
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();
           $rubro = '';
           $direcciones='';

           //if ($this->isGranted('ROLE_PROGRAMACION_EDIT')){    
            if ($request->isMethod('POST')) {
                $ordenInspeccion = new OrdenInspeccion();
                $nuevaOrdenInspeccion = new OrdenInspeccion();

                if ($idOrderInspeccion != '0'){
                    $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                    $idOrderInspeccion=(int)$idOrderInspeccion;
                }else{
                    $idOrderInspeccion=0;
                }
                
                $inspecciones = array();
                $inspeccion = new Inspeccion();
                $nuevaInspeccion = new Inspeccion();

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $ordenInspeccion = $em->findOneById($idOrderInspeccion);
                $establecimiento = $ordenInspeccion->getEstablecimiento();
                if($establecimiento){
                    $rubro = $establecimiento->getRubroPrincipal()->getRubroPrincipal();
                    foreach ($establecimiento->getDirecciones() as $direccion) {
                        $direcciones .= $direccion->__toString()."-";
                    }
                }

                if (!$ordenInspeccion) {
                    throw $this->createNotFoundException('Inspeccion not found');
                }

                $inspecciones = $ordenInspeccion->getInspecciones();
                $cantidad = count($inspecciones) ;

                $inspeccion = $inspecciones[$cantidad -1];                
                
                
                if( !is_null($inspeccion) ){                
                    $form = $this->createForm(InspeccionType::class,$inspeccion,array('method' => 'POST'));
                    $form->handleRequest($request);

                    if( $form->isValid() ){
                        if($crearNueva){
                            $nuevaInspeccion->setOrdenInspeccion($nuevaOrdenInspeccion);
                            $nuevaOrdenInspeccion->addInspeccione($nuevaInspeccion);
                            $nuevaInspeccion->setFechaProgramado($inspeccion->getFechaProgramado());
                            $nuevaInspeccion->setInspectores($inspeccion->getInspectores());

                            $nuevaOrdenInspeccion->setObservaciones($ordenInspeccion->getObservaciones());                        
                            $nuevaOrdenInspeccion->setArea($ordenInspeccion->getArea());
                            $nuevaOrdenInspeccion->setCircuito($ordenInspeccion->getCircuito());
                            $nuevaOrdenInspeccion->setEstablecimiento($ordenInspeccion->getEstablecimiento());
                            $nuevaOrdenInspeccion->setModeloCheckList($ordenInspeccion->getModeloCheckList());
                            $nuevaOrdenInspeccion->setAnulada(0);
                            $nuevaOrdenInspeccion->setRealizada(0);
                            $nuevaOrdenInspeccion->setReinspeccionProvenienciaOrdenInspeccion($ordenInspeccion->getId());
                            $nuevaOrdenInspeccion->setInspeccionPorTablet($ordenInspeccion->getInspeccionPorTablet());

                            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:MotivoInspeccion');
                            $motvioInspeccion = $em->findOneById(1);

                            $nuevaOrdenInspeccion->setMotivoInspeccion($motvioInspeccion);
                            $nuevaOrdenInspeccion->setObservacionesMotivoInspeccion($ordenInspeccion->getObservacionesMotivoInspeccion());

                            $nuevaOrdenInspeccion->setDirecciones($ordenInspeccion->getDirecciones());
                            $direcciones = $ordenInspeccion->getDirecciones();
                            foreach ($direcciones as $direccion) {
                                $direccionProv = new DireccionProvisoria();
                                $direccionProv->setAltura($direccion->getAltura());
                                $direccionProv->setPiso($direccion->getPiso());
                                $direccionProv->setDpto($direccion->getDpto());
                                $direccionProv->setLocal($direccion->getLocal());
                                $direccionProv->setComuna($direccion->getComuna());
                                $direccionProv->setLon($direccion->getLon());
                                $direccionProv->setLat($direccion->getLat());
                                $direccionProv->setSMP($direccion->getSMP());
                                $direccionProv->setPMatriz($direccion->getPMatriz());
                                $direccionProv->setCalle($direccion->getCalle());
                                $direccionProv->setOrdenInspeccion($nuevaOrdenInspeccion);
                                $nuevaOrdenInspeccion->addDireccione($direccionProv);                                
                            }

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($nuevaOrdenInspeccion);
                            $em->persist($ordenInspeccion);
                            $em->flush($nuevaOrdenInspeccion);
                            $em->flush($ordenInspeccion);
                            $em->getConnection()->close();
                            $em->clear();
                        }elseif(!$crearNueva && is_null($inspeccion->getFechaInspeccion()) ){
                            $em = $this->getDoctrine()->getManager();                            
                            $em->persist($inspeccion);                            
                            $em->flush($inspeccion);
                            $em->getConnection()->close();
                            $em->clear();
                        }else{
                            $response = new Response(json_encode("No puede reprogramar la inspeccion ya que la misma ha concluido"));                
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;                            
                        }
                        //$ordenInspeccion->setReinspeccionar(0); comentado pq se hace cuando se asigna idsap

                        $response = new Response(json_encode("OK"));                
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    return $this->render('InspeccionesInspeccionesBundle:Default:modalReProgramacion.html.twig' , array('form' => $form->createview(),'crearNueva' => $crearNueva, 'rubro'=> $rubro,'direcciones' => $direcciones) );
                }else{
                    $em->clear();
                    throw $this->createNotFoundException('No hay Reprogramaciones para esta orden');             
                }            
            }else{           
                throw $this->createNotFoundException('Page Not found');            
            }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function programarInspeccionEstablecimientoAction($idEstablecimiento,$idOrden = 0,$edit = 0,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_EDIT')){  
                    $establecimiento = new Establecimiento();
                    if ($idEstablecimiento != '0'){
                        $idEstablecimiento=encriptador::mrcrypt_decrypt($idEstablecimiento);
                        $idEstablecimiento=(int)$idEstablecimiento;
                    }else{
                        $idEstablecimiento=0;
                    }

                    if ($idOrden != '0'){
                        $idOrden=encriptador::mrcrypt_decrypt($idOrden);
                        $idOrden=(int)$idOrden;
                    }else{
                        $idOrden=0;
                    }


                    $ordenInspeccion = new OrdenInspeccion();
                    $inspeccion = new Inspeccion();
                    $ordenInspeccionDatos = array();
                    $fechasInspeccion = array();
                    $actuaciones = array();
                    $notificaciones = array();
                    $disposiciones = array();
                    $inspeccion = new Inspeccion();
                    $archivosSubidos = array();
                    $programado = false;
                    $datos = array();                    
                    $i=0;
                    $errorArchivo = '';
                    $contadorFile=1;
                    $hoy = new \DateTime('now');
                    $hace6Meses = new \DateTime('-6 months');

                    if($idOrden > 0){
                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $ordenInspeccion = $em->findOneById($idOrden);
                    }

                    if ($request->isMethod('POST')) {

                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                        $establecimiento = $em->findOneById($idEstablecimiento);

                        if (!$establecimiento && $edit == 0) {
                            throw $this->createNotFoundException('Establecimiento not found');
                        }
                        if($establecimiento){
                            foreach ($establecimiento->getDirecciones() as $direccion) {
                                $datos['direcciones'][$i] = $direccion->__toString();
                            }

                            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                            $notificaciones = $em->ultimaCedulasPorEstablecimiento($idEstablecimiento);
                            
                            if(count($notificaciones) > 0 ){
                                $datos['cedula'] = 'Ultima Cedula: '.$notificaciones[0]->getCedula()->getNumero();
                                $datos['idCedula'] = $notificaciones[0]->getId();
                            }

                            $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaUtilizada');
                            $arrayActasUtilizadas = $em->findActaUtilizadaByFechaAreaProgramacion($hace6Meses->format("Y-m-d"),$hoy->format("Y-m-d"),0,$idEstablecimiento);

                            foreach ($arrayActasUtilizadas as $acta) {
                                foreach ($acta->getActaMotivo() as $motivo) {
                                    $datos['actas'][$i]['codigo']=$motivo->getMotivo();
                                    $datos['actas'][$i]['motivo']=$motivo->getMotivoCompleto();
                                    $i++;                                    
                                }                                
                            }

                            echo '<script>';
                            echo 'console.log('. json_encode( $arrayActasUtilizadas ) .')';
                            echo '</script>';

                            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:DisposicionClausura');
                            $disposiciones = $em->searchClausura($idEstablecimiento);

                            if(count($disposiciones) > 0 ){
                                $datos['disposicion'] = 'El establecimiento Tiene una Clausura Vigente';                
                            }

                            $actuaciones = $establecimiento->getActuaciones();

                            $datos['rubroPrincipal'] = $establecimiento->getRubroPrincipal()->getRubroPrincipal();
                            if($establecimiento->getEstado()){
                                $datos['Estado'] = $establecimiento->getEstado()->getEstado();
                            }else{
                                $datos['Estado'] = "Debe completar el estado al establecimiento para continuar.";
                            }
                            
                            if(isset($actuaciones[0])){
                                $datos['expediente'] = $actuaciones[0]->__toString();
                            }

                            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                            $ordenInspeccionDatos = $em->findInspeccionesSinAnuladarPorEstablecimiento($establecimiento);
                        
                            if( count( $ordenInspeccionDatos )>0 ){                                
                                
                                $ordenProgramadoMostrar;
                                foreach ($ordenInspeccionDatos as $orden) {
                                    foreach ($orden->getInspecciones() as $fecha) {
                                        $anulada = $orden->getAnulada();
                                        $eliminada = $orden->getEliminada();
                                        if(!$fecha->getFechaInspeccion() && !$anulada && !$eliminada){
                                            $programado = true;
                                            $ordenProgramadoMostrar = $orden;
                                        }
                                    }
                                }

                                $cantidadInspecciones = count($ordenInspeccionDatos);
                                $fechasInspeccion = $ordenInspeccionDatos[$cantidadInspecciones -1]->getInspecciones();
                                if($programado == true){
                                    $datos['programado'] = $ordenProgramadoMostrar->getArea()->getArea().". CH".$ordenProgramadoMostrar->getChecklist()."-";
                                    if(array_key_exists(1,$ordenInspeccionDatos ) ){
                                        $ultima = $ordenInspeccionDatos[1]->getInspecciones();
                                        if(!is_null($ultima[0])){                                            
                                            if( $ultima[0]->getFechaInspeccion() ){
                                                $datos['ultima'] = 'Ultima fecha de inspección: '.$ultima[0]->getFechaInspeccion()->format('d-m-Y').'||'.$ordenInspeccionDatos[1]->__toString();
                                            }
                                        }                                               
                                    }                   
                                }
                                if($programado == false){
                                    $pos = 0;
                                    $salir = false;

                                    while( $salir == false && array_key_exists($pos,$ordenInspeccionDatos) ){
                                        if($ordenInspeccionDatos[$pos]->getAnulada() || $ordenInspeccionDatos[$pos]->getEliminada() ){
                                            $pos++;                                        
                                        }else{
                                            $salir = true;
                                        }
                                    }                                    

                                    $ultima = $ordenInspeccionDatos[$pos]->getInspecciones();                                    
                                    if(!is_null($ultima[0])){
                                        if( !is_null( ($ultima[0]->getFechaInspeccion()) ) ){
                                            $datos['ultima'] = 'Ultima fecha de inspección: '.$ultima[0]->getFechaInspeccion()->format('d-m-Y').'||'.$ordenInspeccionDatos[$pos]->__toString();
                                        }
                                    }           
                                }                   
                                
                            }
                        }     

                        if($idOrden == 0){
                            $ordenInspeccion->setArea($this->getUser()->getArea() );
                            $ordenInspeccion->setEstablecimiento($establecimiento);
                            $ordenInspeccion->addInspeccione($inspeccion);
                            $inspeccion->setOrdenInspeccion($ordenInspeccion);
                            $ordenInspeccion->setAnulada(0);
                            $ordenInspeccion->setRealizada(0);
                        }
                        
                        
                        /*
                        for ($i=0; $i < 10; $i++) { 
                            if(file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$idOrden."-".$i.".pdf")){
                                array_push($archivosSubidos,$idOrden."-".$i);
                            }    
                        }*/
                        
                        
                        for ($i=0; $i < 10; $i++) {   
                            $key             = $this->getParameter('key');
                            $secret          = $this->getParameter('secret_AWS');
                            $region          = $this->getParameter('region');
                            $base_url        = $this->getParameter('base_url');
                            $version         = $this->getParameter('version');
                            $path            = $this->getParameter('path');

                            $s3 = new S3Client([
                                'version' => $version,
                                'region'  => $region,
                                'endpoint' => $base_url,
                                'use_path_style_endpoint' => true,
                                'credentials' => [
                                    'key'    => $key,
                                    'secret' => $secret,
                                ],
                            ]);	
                            
                            try{
                                $fileS3 = $s3->doesObjectExist($path, 'Adjuntos/'.$idOrden."-".$i.".pdf");
                                //var_dump($fileS3);
                            } catch (AwsExceptionInterface $e) {
                                throw Exception\StorageException::putError( $e );
                            }		
 
                            if($fileS3){
                                array_push($archivosSubidos,$idOrden."-".$i);
                            }    
                        }
                        
                        if($establecimiento){
                            $form = $this->createForm(ProgramarInspeccionType::class,$ordenInspeccion,array('method' => 'POST'));    
                        }else{
                            $form = $this->createForm(ProgramarInspeccionSinEstablecimientoType::class,$ordenInspeccion,array('method' => 'POST'));
                        }                       
                        
                        $form->handleRequest($request);                        

                        if($form->isValid()){

                            $contadorFile = 1;
                            $files = $form['adjuntos']->getData();

                            $primerFechaProgramacion = $ordenInspeccion->getPrimerFechaProgramado();

                            if(!$primerFechaProgramacion){                                
                                $ordenInspeccion->setPrimerFechaProgramado($form['inspecciones'][0]['fechaProgramado']->getData());
                            }

                            $denunciantes = $ordenInspeccion->getDenunciantes();
                             $em = $this->getDoctrine()->getManager();
                            foreach ($denunciantes as $denunciante) {

                                $denunciante->setOrdenInspeccion($ordenInspeccion);
                                $em->persist($denunciante);
                            }

                            if($this->getUser()->getArea()->getId() != 7){
                                $ordenInspeccion->setArea($this->getUser()->getArea());
                            }


                            $em = $this->getDoctrine()->getManager();
                            $em->persist($ordenInspeccion);
                            $em->flush();
                            $em->getConnection()->close();
                            $em->clear();
                            
                            
                            
                            /************* */
                            
                            	for ($i=0; $i <count($files) ; $i++) { 
                        		$extension = $files[$i]->guessExtension();		
                        		if($extension == 'pdf'){		
                        		
                        			$key             = $this->getParameter('key');
                        			$secret          = $this->getParameter('secret_AWS');
                        			$region          = $this->getParameter('region');
                        			$base_url        = $this->getParameter('base_url');
                        			$version         = $this->getParameter('version');
                        			$path            = $this->getParameter('path');
                        
                        			$s3 = new S3Client([
                        				'version' => $version,
                        				'region'  => $region,
                        				'endpoint' => $base_url,
                        				'use_path_style_endpoint' => true,
                        				'credentials' => [
                        					'key'    => $key,
                        					'secret' => $secret,
                        				],
                        			]);	
                        			
                        			try{
                        				$fileS3 = $s3->doesObjectExist($path, 'Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf");
                        				//var_dump($fileS3);
                        			} catch (AwsExceptionInterface $e) {
                        				throw Exception\StorageException::putError( $e );
                        			}		
                        		
                        			while ( $fileS3 && $contadorFile < 9 ) {
                        				$contadorFile++;                                    
                        			}
                        			if($fileS3 && $contadorFile == 9){
                        				$errorArchivo .= " - Supero el limite de Archivos(9).";
                        
                        			}else{			 
                        				try{		  
                        					$result = $s3->putObject([
                        							'Bucket' => $path,
                        							'Key' => 'Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf",
                        							'SourceFile' => $files[$i]
                        						]);
                        				 
                        				} catch (AwsExceptionInterface $e) {
                        					throw Exception\StorageException::putError( $e );
                        				} 				
                        				$contadorFile++;                                        
                        			}  
                        			
                        		}else{
                        			$errorArchivo .= "El Archivo: ".$files[$i]->getClientOriginalName()." no se pudo subir ya que no tiene el formato correcto";
                        		}
                        	} 
                            
                            /*
                            for ($i=0; $i <count($files) ; $i++) { 
                                $extension = $files[$i]->guessExtension();
                                if($extension == 'pdf'){
                                    while ( file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf") && $contadorFile < 9 ) {
                                        $contadorFile++;                                    
                                    }

                                    if(file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf") && $contadorFile == 9){

                                        $errorArchivo .= " - Supero el limite de Archivos(9).";

                                    }else{
                                        $files[$i]->move(__DIR__.'/../../../../web/uploads/Adjuntos', $ordenInspeccion->getId()."-".$contadorFile.".pdf");
                                        $contadorFile++;                                        
                                    }                                    
                                }else{
                                    $errorArchivo .= "El Archivo: ".$files[$i]->getClientOriginalName()." no se pudo subir ya que no tiene el formato correcto";
                                }
                            }          */                  

                            $response = new Response(json_encode("GUARDADO ".$errorArchivo));                
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;
                        }

                        $em->clear();
                        return $this->render('InspeccionesInspeccionesBundle:Default:modalProgramacion.html.twig' , array('form' => $form->createview(),'datos' => $datos, 'idOrderInspeccion' => $idOrden,'archivosSubidos' => $archivosSubidos ));

                    }else{
                        throw $this->createNotFoundException('Page Not found');
                    }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        } 
    }

    public function normalizarDireccionProgramarAction($idCalle,$altura,Request $request)
    {
        $usig = $this->get(UsigWS::class);


        $user=$this->getUser();

        if($user){           
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_EDIT')){
                $direccion = new Direccion();
                $calle = new Calles();
                $messageError = '';
                $messageAmbiguedad  = '';

                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles');
                    $calle = $em->findOneById($idCalle);                    
                    $em->clear();

                    $direccion->setCalle($calle);
                    $direccion->setAltura($altura);

                    if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){                       
                        $datos['idCalle'] =  $direccion->getCalle()->getId();
                        $datos['Calle'] = $direccion->getCalle()->getCalle();
                        $datos['Altura'] = $direccion->getAltura();
                        $datos['Lat'] = $direccion->getLat();
                        $datos['Lon'] = $direccion->getLon();
                        $datos['SMP'] = $direccion->getSMP();
                        $datos['error'] = $messageError;
                        $datos['ambiguedad'] = $messageAmbiguedad;

                        $response = new Response(json_encode($datos));                        
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;   
                    }else{
                        $response = new Response(json_encode('ERROR'));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }        
                }else{
                    throw $this->createNotFoundException('Page Not found');
                }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function buscarDireccionAction($idCalle,$altura,$smp,$piso,$dpto,$local,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){            
                $direccion = new Direccion();
                $direcciones = array();
                $razonesSocial = array();
                $filtro = array();
                $listaIdEstablecimientos = array();
                $i = 0;


                if ($request->isMethod('POST')) {

                    $piso = ($piso == '$') ? null : $piso;
                    $dpto = ($dpto == '$') ? null : $dpto;
                    $local = ($local == '$') ? null : $local;               

                    $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');

                    $filtro['idCalle'] = $idCalle;
                    $filtro['altura'] = $altura;

                    if($piso){
                        $filtro['piso'] = $piso;
                    }
                    if($dpto){
                        $filtro['dpto'] = $dpto;
                    }
                    if($local){
                        $filtro['local'] = $local;
                    }

                    $direccion = $em->findOneBy(
                            $filtro
                        );

                    $direcciones = $em->findBy(
                            $filtro
                        );

                    if(!$direccion || count($direcciones) > 1){
                        $direcciones = $em->findBy(
                            array('sMP'=> $smp),
                            array('idEstablecimiento' => 'ASC')
                            );
                        if(count($direcciones) > 0){
                            $datos['Resultado'] = 'Parcela';
                            foreach ($direcciones as $direccion) {
                                array_push($listaIdEstablecimientos,$direccion->getIdEstablecimiento());                            
                            }                        
                            $listaIdEstablecimientos = array_unique ($listaIdEstablecimientos);
                            $datos['Cantidad'] = count($listaIdEstablecimientos);
                            foreach ($listaIdEstablecimientos as $value) {
                                $datos[$i]['idEstablecimiento'] = $value;
                                $datos[$i]['direccion'] = "";
                                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                                $establecimiento = $em->findOneById($value);
                                foreach ($establecimiento->getDirecciones() as $direccionEstablecimiento) {
                                    $datos[$i]['direccion'] .= $direccionEstablecimiento->__toString().' | ';    
                                }
                                $razonesSocial = $establecimiento->getRazonesSociales();
                                if(count($razonesSocial) > 0){
                                    $datos[$i]['razonSocial'] = $razonesSocial[0]->getRazonSocial()->__toString();
                                }
                                $i++;
                            }                        
                        }else{
                            $datos['Resultado'] = 'No Encontrado';
                        }               
                    }else{
                        $datos['Resultado'] = 'Especifico';
                        $datos[0]['idEstablecimiento'] = encriptador::mrcrypt_encrypt($direccion->getEstablecimiento()->getId());
                        $datos[0]['direccion'] = $direccion->__toString();
                        $razonesSocial = $direccion->getEstablecimiento()->getRazonesSociales();
                        if(count($razonesSocial) > 0){
                            $datos[0]['razonSocial'] = $razonesSocial[0]->getRazonSocial()->__toString();
                        }
                    }
                    
                    $em->clear();
                    $response = new Response(json_encode($datos));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;           

                }else{
                    throw $this->createNotFoundException('Page Not found');
                }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }        */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function programarInspeccionSinEstablecimientoAction($idCalle,$altura,$piso,$dpto,$local,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();
           $usig = $this->get(UsigWS::class);
           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){           
                    $ordenInspeccion = new OrdenInspeccion();
                    $inspeccion = new Inspeccion();
                    $calle = new Calles();
                    $direccion = new Direccion();
                    $direccionProv = new DireccionProvisoria();
                    $direccionProvCheck = new DireccionProvisoria();
                    $direccionProvGuardar = new DireccionProvisoria();
                    $messageError = '';
                    $messageAmbiguedad  = '';
                    $datos = array();
                    $fechasInspeccion = array();
                    $programado = false;
                    $errorArchivo = '';

                    $piso = ($piso == '$') ? null : $piso;
                    $dpto = ($dpto == '$') ? null : $dpto;
                    $local = ($local == '$') ? null : $local;       

                    if ($request->isMethod('POST')) {

                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Direccion');
                        $direccion = $em->findOneBy(
                                array('idCalle' => $idCalle, 'altura' => $altura, 'piso' =>$piso, 'dpto' => $dpto,'local'=>$local)              
                            );

                        if(!$direccion){
                            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:DireccionProvisoria');
                            $direccionProvCheck = $em->findOneBy(
                                array('calle' => $idCalle, 'altura' => $altura, 'piso' =>$piso, 'dpto' => $dpto,'local'=>$local)                
                            );

                            if($direccionProvCheck){
                                $fechasInspeccion = $direccionProvCheck->getOrdenInspeccion()->getInspecciones();

                                foreach ($fechasInspeccion as $fechas) {
                                    $hayProgramacion = $fechas->getFechaInspeccion();
                                    $anulada = $direccionProvCheck->getOrdenInspeccion()->getAnulada();
                                    if( !$hayProgramacion && !$anulada){
                                        $programado = true;
                                    }
                                }

                                if($programado == true){
                                    $datos['programado'] = $direccionProvCheck->getOrdenInspeccion()->getArea()->getArea().". CH".$direccionProvCheck->getOrdenInspeccion()->getChecklist()."-";
                                }else{
                                    $ultima = $direccionProvCheck->getOrdenInspeccion()->getInspecciones();
                                    if($ultima[0]->getFechaInspeccion()){
                                        $datos['ultimaFechaInspeccion'] = ''.$ultima[0]->getFechaInspeccion()->format('Y-m-d H:i:s').' '.$direccionProvCheck->getOrdenInspeccion()->__toString();    
                                    }
                                    
                                }
                            }

                            $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles');
                            $calle = $em->findOneById($idCalle);
                            $direccionProv->setCalle($calle);
                            $direccionProv->setAltura($altura);
                            $direccionProv->setPiso($piso);
                            $direccionProv->setDpto($dpto);
                            $direccionProv->setLocal($local);

                            if ($usig->normalizarDireccion($direccionProv,$messageError,$messageAmbiguedad) ){             
                                $ordenInspeccion->addDireccione($direccionProv);
                                $direccionProv->setOrdenInspeccion($ordenInspeccion);
                                $direccionProvGuardar = clone ($direccionProv);
                                $ordenInspeccion->setArea($this->getUser()->getArea() );        
                                $ordenInspeccion->addInspeccione($inspeccion);
                                $inspeccion->setOrdenInspeccion($ordenInspeccion);              
                                $ordenInspeccion->setAnulada(0);
                                $ordenInspeccion->setRealizada(0);
                                
                                $form = $this->createForm(ProgramarInspeccionSinEstablecimientoType::class,$ordenInspeccion,array('method' => 'POST'));
                                $form->handleRequest($request);
                                if($form->isValid()){
                                    $em = $this->getDoctrine()->getManager();

                                    $contadorFile = 1;
                                    $files = $form['adjuntos']->getData();

                                    $inspecciones = $ordenInspeccion->getInspecciones();
                                    
                                    $fechaProgramado = $inspecciones[0]->getFechaProgramado();
                                    $inspectores = $inspecciones[0]->getInspectores();

                                    if($fechaProgramado && count($inspectores) > 0){

                                        $ordenInspeccion->removeDireccione($direccionProv);
                                        $ordenInspeccion->addDireccione($direccionProvGuardar);

                                        $denunciantes = $ordenInspeccion->getDenunciantes();
                                        foreach ($denunciantes as $denunciante) {
                                            $denunciante->setOrdenInspeccion($ordenInspeccion);
                                            $em->persist($denunciante);
                                        }

                                        if($this->getUser()->getArea()->getId() != 7){
                                            $ordenInspeccion->setArea($this->getUser()->getArea());
                                        }

                                        $primerFechaProgramacion = $ordenInspeccion->getPrimerFechaProgramado();

                                         if(!$primerFechaProgramacion){                                
                                            $ordenInspeccion->setPrimerFechaProgramado($form['inspecciones'][0]['fechaProgramado']->getData());
                                        }

                                        $inspecciones = $ordenInspeccion->getInspecciones();
                                        $fechadeProgramado = $inspecciones[0]->getFechaProgramado();
  
                                        $em = $this->getDoctrine()->getManager();
                                        $em->persist($ordenInspeccion);
                                        $em->flush();
                                        $em->getConnection()->close();
                                        $em->clear();                                        
                                        
                                        
                                        /******* */
                                        	for ($i=0; $i <count($files) ; $i++) { 
                                    		$extension = $files[$i]->guessExtension();		
                                    		if($extension == 'pdf'){		
                                    		
                                    			$key             = $this->getParameter('key');
                                    			$secret          = $this->getParameter('secret_AWS');
                                    			$region          = $this->getParameter('region');
                                    			$base_url        = $this->getParameter('base_url');
                                    			$version         = $this->getParameter('version');
                                    			$path            = $this->getParameter('path');
                                    
                                    			$s3 = new S3Client([
                                    				'version' => $version,
                                    				'region'  => $region,
                                    				'endpoint' => $base_url,
                                    				'use_path_style_endpoint' => true,
                                    				'credentials' => [
                                    					'key'    => $key,
                                    					'secret' => $secret,
                                    				],
                                    			]);	
                                    			
                                    			try{
                                    				$fileS3 = $s3->doesObjectExistV2($path, 'Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf");
                                    				//var_dump($fileS3);
                                    			} catch (AwsExceptionInterface $e) {
                                    				throw Exception\StorageException::putError( $e );
                                    			}		
                                    		
                                    			while ( $fileS3 && $contadorFile < 9 ) {
                                    				$contadorFile++;                                    
                                    			}
                                    			if($fileS3 && $contadorFile == 9){
                                    				$errorArchivo .= " - Supero el limite de Archivos(9).";
                                    
                                    			}else{			 
                                    				try{		  
                                    					$result = $s3->putObject([
                                    							'Bucket' => $path,
                                    							'Key' => 'Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf",
                                    							'SourceFile' => $files[$i]
                                    						]);
                                    				 
                                    				} catch (AwsExceptionInterface $e) {
                                    					throw Exception\StorageException::putError( $e );
                                    				} 				
                                    				$contadorFile++;                                        
                                    			}  
                                    			
                                    		}else{
                                    			$errorArchivo .= "El Archivo: ".$files[$i]->getClientOriginalName()." no se pudo subir ya que no tiene el formato correcto";
                                    		}
                                    	} 

                                        /*
                                        for ($i=0; $i <count($files) ; $i++) { 
                                            $extension = $files[$i]->guessExtension();
                                            if($extension == 'pdf'){
                                                while ( file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf") && $contadorFile < 9 ) {
                                                    $contadorFile++;                                    
                                                }
                                                if(file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$ordenInspeccion->getId()."-".$contadorFile.".pdf") && $contadorFile == 9){

                                                    $errorArchivo .= " - Supero el limite de Archivos(9).";

                                                }else{
                                                    $files[$i]->move(__DIR__.'/../../../../web/uploads/Adjuntos', $ordenInspeccion->getId()."-".$contadorFile.".pdf");
                                                    $contadorFile++;                                        
                                                }                                    
                                            }else{
                                                $errorArchivo .= "El Archivo: ".$files[$i]->getClientOriginalName()." no se pudo subir ya que no tiene el formato correcto";
                                            }
                                        } */

                                        /*foreach ($files as $file) {
                                            $extension = $file->guessExtension();
                                            if($extension == 'pdf'){
                                                $file->move(__DIR__.'/../../../../web/uploads/Adjuntos', $ordenInspeccion->getId()."-".$contadorFile.".pdf");
                                                $contadorFile++;    
                                            }
                                        }*/

                                        $results['result']="OK";
                                        $results['details'] = "GUARDADO ".$errorArchivo;
                                        $response = new Response(json_encode( $results ));
                                        $response->headers->set('Content-Type', 'application/json');
                                        return $response;
                                    }else{                                        
                                        $em->clear();
                                        $results['result']="ERROR";
                                        $results['details'] = "Debe completar todos los campos";
                                        $response = new Response(json_encode( $results ));
                                        $response->headers->set('Content-Type', 'application/json');
                                        return $response;
                                    }
                                }
                            }else{                                    
                                    $em->clear();
                                    $results['result']="ERROR";
                                    $results['details'] = "La direccion no se pudo normalizar";
                                    $response = new Response(json_encode( $results ));
                                    $response->headers->set('Content-Type', 'application/json');
                                    return $response;
                            }
                            return $this->render('InspeccionesInspeccionesBundle:Default:modalProgramacionSinEstablecimiento.html.twig' , array('form' => $form->createview() ,'datos' => $datos ));
                        }else{                            
                            $em->clear();
                            $response = new Response(json_encode("La dirección ya existe en la base de datos, use el método correspondiente"));
                            $response->headers->set('Content-Type', 'application/json');
                            return $response;
                        }
                    }else{
                        throw $this->createNotFoundException('Page Not found');
                    }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }    

    public function editProgramacionAction($idOrderInspeccion,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();           

           //if ($this->isGranted('ROLE_PROGRAMACION_EDIT')){          
                $ordenInspeccion = new OrdenInspeccion();
                if ($idOrderInspeccion != '0'){
                    $idOrderInspeccion=encriptador::mrcrypt_decrypt($idOrderInspeccion);
                    $idOrderInspeccion=(int)$idOrderInspeccion;
                }else{
                    $idOrderInspeccion=0;
                }
                    
                $establecimiento;
                $datos = '';

                $denunciantesListaOriginal = array();       
                $denunciantesLista = array();
                $archivosSubidos = array();
                $contadorFile= 1;

                if ($request->isMethod('POST')) {
                    $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                    $ordenInspeccion = $em->findOneBy(array('id' => $idOrderInspeccion, 'idSap' => null));          
                    if(!$ordenInspeccion){
                        throw $this->createNotFoundException('Inspeccion not found');
                    }

                    for ($i=0; $i < 10; $i++) { 
                        if(file_exists('/opt/lampp/htdocs/Notificaciones/web/uploads/Adjuntos/'.$idOrderInspeccion."-".$i.".pdf")){
                            array_push($archivosSubidos,$idOrderInspeccion."-".$i);
                        }    
                    }
                    

                    foreach ($ordenInspeccion->getDenunciantes() as $denunciante) {
                        array_push($denunciantesListaOriginal,$denunciante);
                    }

                    $establecimiento = $ordenInspeccion->getEstablecimiento();

                    if($establecimiento){
                        $form = $this->createForm(ProgramarInspeccionType::class,$ordenInspeccion,array('method' => 'POST'));                
                    }else{
                        $form = $this->createForm(ProgramarInspeccionSinEstablecimientoType::class,$ordenInspeccion,array('method' => 'POST'));
                    }

                    $form->handleRequest($request);
                    if($form->isValid()){
                        foreach ($ordenInspeccion->getDenunciantes() as $denunciante) {
                            array_push($denunciantesLista,$denunciante);
                        }

                        $ordenInspeccion->setDenunciantes(array());

                        foreach ($denunciantesListaOriginal as $denunciante) {
                            if(!in_array($denunciante,$denunciantesLista,true) ){
                                $em->remove($denunciante);
                            }
                        }

                        foreach ($denunciantesLista as $denunciante) {
                            $denunciante->setOrdenInspeccion($ordenInspeccion);
                            $em->persist($denunciante);
                        }                        

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ordenInspeccion);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $results['result']="OK";
                        $results['details'] = "GUARDADO";
                        $response = new Response(json_encode( $results ));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }                    
                    $em->clear();
                    return $this->render('InspeccionesInspeccionesBundle:Default:modalProgramacion.html.twig' , array('form' => $form->createview() ,'datos' => $datos, 'idOrderInspeccion' => $idOrderInspeccion,'archivosSubidos' => $archivosSubidos ));       
                }else{
                    throw $this->createNotFoundException('Page Not found');
                }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }    

    public function guardarProgramacionConIdSapAction($idOrderInspeccion,$idSap,Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_EDIT')){
                    $idOrderInspeccion=(int)$idOrderInspeccion;
                    $idSap=(int)$idSap;       

                    if ($idSap==0){
                        return "CERO";          
                    }

                    $ordenInspeccion = new OrdenInspeccion();
                    $validadorOrdenInspeccion = new OrdenInspeccion();
                    $ordenAnterior = null;


                    if ($request->isMethod('POST')) {                         
                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        
                        if($idSap < 2500000000){
                            $idSap = $idSap + 2500000000;
                        }

                        $validadorOrdenInspeccion = $em->findOneById($idOrderInspeccion);
                        if(!$validadorOrdenInspeccion){                            
                            $em->clear();
                            return "ERROR";
                        }

                        $validadorOrdenInspeccion->setIdSap($idSap);

                        if($em->existeIdSap($validadorOrdenInspeccion)){                            
                            $em->clear();
                            return "EXISTE";
                        }
                        
                        $ordenInspeccion = $em->findOneBy(array('id' => $idOrderInspeccion, 'idSap' => null));
                        if(!$ordenInspeccion){                            
                            $em->clear();
                            return "ERROR";
                        }

                        $ordenInspeccion->setIdSap($idSap);

                        $reinspeccionar = $ordenInspeccion->getReinspeccionProvenienciaOrdenInspeccion();
                        
                        if($reinspeccionar){
                            $ordenAnterior = $em->findOneById($reinspeccionar);
                            if($ordenAnterior){
                                $ordenAnterior->setReinspeccionar(0);                                    
                            }                            
                        }
                        
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ordenInspeccion);
                        if($ordenAnterior){
                            $em->persist($ordenAnterior);    
                        }                        
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();
                        
                        return "OK";                        

                    }else{
                        throw $this->createNotFoundException('Page Not found');
                    }/*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function tablaReprogramacionAction(Request $request){
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){   

                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaProgramacionArea.yml"));        
                 
                $TablaAsignacion=new TablaAjax($request,$em,$yaml,null,$checker);
                $TablaAsignacion->setNativeQuery('SELECT * FROM (
                                                SELECT DISTINCT 
                                                oi.id,
                                                oi.id_sap,
                                                GROUP_CONCAT(concat(COALESCE(u.apellido,""),", ",COALESCE(u.nombre,""))) as nombre,
                                                ci.circuito,
                                                fnEstablecimientoDomicilios(es.id) as direcciones,
                                                i.fecha_programado
                                                FROM orden_inspeccion as oi
                                                JOIN inspeccion as i ON i.orden_inspeccion_id = oi.id
                                                JOIN inspeccion_usuario as iu on iu.inspeccion_id = i.id
                                                JOIN usuarios as u on u.id=iu.usuario_id
                                                JOIN circuito as ci on ci.id=oi.circuito_id
                                                JOIN establecimiento as es ON oi.establecimiento_id=es.id
                                                WHERE oi.id_sap is null and (oi.eliminada = 0 or oi.eliminada is null ) and oi.area_id='.$this->getUser()->getIdArea().'
                                                GROUP BY oi.id,oi.id_sap,ci.circuito,fnEstablecimientoDomicilios(es.id),i.fecha_programado

                                                UNION

                                                SELECT 
                                                oi.id,
                                                oi.id_sap,
                                                GROUP_CONCAT(concat(COALESCE(u.apellido,""),", ",COALESCE(u.nombre,""))) as nombre,
                                                ci.circuito,
                                                GROUP_CONCAT(CONCAT(c.calle," ",dp.altura," ",COALESCE(dp.piso,"")," ",COALESCE(dp.dpto,"")," ",COALESCE(dp.local,""))) as direcciones,
                                                i.fecha_programado
                                                FROM orden_inspeccion as oi
                                                JOIN inspeccion as i ON i.orden_inspeccion_id = oi.id
                                                JOIN inspeccion_usuario as iu on iu.inspeccion_id = i.id
                                                JOIN usuarios as u on u.id=iu.usuario_id
                                                JOIN circuito as ci on ci.id=oi.circuito_id
                                                JOIN direccion_provisoria as dp on dp.orden_inspeccion_id=oi.id
                                                JOIN calles as c on c.id=dp.calle_id
                                                WHERE oi.id_sap is null and (oi.eliminada = 0 or oi.eliminada is null ) and oi.area_id='.$this->getUser()->getIdArea().
                                                ' GROUP BY oi.id,oi.id_sap,ci.circuito,i.fecha_programado) as a
                                                WHERE 1=1 ');
                                         // $TablaAsignacion->setSpecialConditions("1 = 1");
                $response = new Response($TablaAsignacion->Initialize());
                $response->headers->set('Content-Type', 'application/json');                
                $em->clear();
                return $response; /*
           }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */

        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        } 
    }

    public function programarInspeccionDecisionAction(Request $request)
    {
        $establecimientos =$request->getContent();
        

        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){   
                return $this->render('InspeccionesInspeccionesBundle:Default:modalDecision.html.twig', array('establecimientos' => $establecimientos));
           /*}else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }     */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function asignarIdSapAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
           $checker=$this->getUser()->getRoles();

           //if ($this->isGranted('ROLE_PROGRAMACION_VIEW')){         
                 $valores = json_decode($request->request->get('valores'));
                 $ids = json_decode($request->request->get('ids'));
                 $return="";

                 for ($i=0; $i <=count($valores)-1 ; $i++) { 
                    if ($valores[$i]!="" or $valores[$i]!=null){
                        $respuesta=$this->guardarProgramacionConIdSapAction($ids[$i],$valores[$i],$request);
                        if ($respuesta=="OK"){
                            $return.="La orden con id sap ".$valores[$i]." se ha enviado correctamente\n";
                        }elseif($respuesta=="EXISTE"){
                            $return.="el id sap ".$valores[$i]." ya existe en la base de datos\n";
                        }else{
                            $return.="se ha producido un error, con el id sap".$valores[$i]."intente de nuevo mas tarde\n";
                        }                
                    }
                 }

                 return new Response($return);/*
           }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
           } */
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }  
    }

    public function pdfAdjuntoAction($archivo,Request $request)
    {
        if(file_exists(__DIR__.'/../../../../web/uploads/Adjuntos/'.$archivo.".pdf")){
            $mi_pdf2 = __DIR__.'/../../../../web/uploads/Adjuntos/'.$archivo.".pdf";

            header('Content-type: application/pdf');
            readfile($mi_pdf2) or die("File not found.");
        
        
        }else{             
            return new Response("no se ah encontrado el archivo");
        }              
    }

    public function borrarPdfAdjuntoAction($archivo,Request $request){
        $nombreArchivo = encriptador::mrcrypt_decrypt($archivo);
        $nombreArchivo = strval(str_replace("\0", "", $nombreArchivo));

        $path = __DIR__.'/../../../../web/uploads/Adjuntos/'.$nombreArchivo.".pdf";


        if(file_exists($path)){
            unlink($path);

            return new Response("OK");
        }else{
            return new Response("El archivo no existe");
        }

        
    }

}