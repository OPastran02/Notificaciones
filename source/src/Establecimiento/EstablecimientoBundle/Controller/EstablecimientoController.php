<?php

namespace Establecimiento\EstablecimientoBundle\Controller;

use Establecimiento\EstablecimientoBundle\Form\UnirEstablecimientoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Establecimiento\EstablecimientoBundle\Entity\ResultadosUltimaInspeccion;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\EstablecimientoRazonSocial;
use Establecimiento\EstablecimientoBundle\Entity\RazonSocial;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

use Establecimiento\EstablecimientoBundle\Form\EstablecimientoType;
use Establecimiento\EstablecimientoBundle\Form\EstablecimientoExitenteType;
use AppBundle\Service\UsigWS;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use CoreBundle\Logic\encriptador;

class EstablecimientoController extends Controller
{
    public function establecimientoAction( $id = '0', Request $request)
    { 
        $user=$this->getUser();

        if($user){
            $checker=$this->getUser()->getRoles();       
            $usig = $this->get(UsigWS::class);
            if ($this->isGranted('ROLE_ESTABLECIMIENTO_VIEW')){     
                    if ($id != '0'){
                        $id=encriptador::mrcrypt_decrypt($id);
                        $id=(int)$id;
                    }else{
                        $id=0;
                    }

                    $tipoDisposicion = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoDispo')->selectTipoDisposicionTabla();
                    $estadoCedula= $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->selectEstadoTabla();
                    $tipoCedula =  $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->selectTipoCedulaTabla();
                    $area= $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->selectAreaTabla();
                    $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();


                    $establecimiento = new Establecimiento();
                    $resultadoUltimaInspeccion = new ResultadosUltimaInspeccion();        
                    $resultadoUltimaInspeccion->setEstablecimiento($establecimiento);
                    $establecimiento->setResultadosUltimaInspeccion($resultadoUltimaInspeccion);
                    $direccionesListaOriginales = array();
                    $direccionesLista = array();
                    $actuacionesListaOriginales = array();
                    $actuacionesLista = array();
                    $razonSocialListaOriginales = array();
                    $razonSocialLista = array();
                    $direccionNormalizada = array();
                    $auxRazonSocial = array();
                    $messageError = '';
                    $messageAmbiguedad  = '';
                    $historial = array();

                    if($id > 0){
                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                        $establecimiento = $em->findOneById($id);

                        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Historial');
                        $historial = $em->getHistorial($id,'Establecimiento\EstablecimientoBundle\Entity\Establecimiento');

                        if (!$establecimiento) {
                            throw $this->createNotFoundException('No adress found');
                        }
                        foreach ($establecimiento->getDirecciones() as $direccion) {
                            array_push($direccionesListaOriginales,$direccion);
                        }
                        foreach ($establecimiento->getActuaciones() as $actuacion) {                
                            array_push($actuacionesListaOriginales,$actuacion);
                        }
                        foreach ($establecimiento->getRazonesSociales() as $razonSocial) {
                            array_push($razonSocialListaOriginales,$razonSocial);
                        }
                    }

                    if($id > 0){
                        $form = $this->createForm(EstablecimientoExitenteType::class,$establecimiento,array('method' => 'POST',"usuario" => $this->getUser(),"rz"=>$razonSocialListaOriginales));    
                    }else{
                        $form = $this->createForm(EstablecimientoType::class,$establecimiento,array('method' => 'POST',"usuario" => $this->getUser()));
                    }                    

                    if ($request->isMethod('POST')) {
                        
                        $form->handleRequest($request);
                        if ($form->isValid()) {                            
                            if(count($establecimiento->getDirecciones()) == 0){
                                $this->addFlash('error',"Debe Agregar al menos una direccion"); 
                                return $this->render('EstablecimientoEstablecimientoBundle:Default:index.html.twig' , array(
                                    'form' => $form->createview(),
                                    'tipoDisposicion' => $tipoDisposicion,
                                    'estadoCedula' => $estadoCedula,
                                    'tipoCedula' => $tipoCedula,
                                    'area' => $area,
                                    'inspectores' => $inspectores ));   
                            }             

                            $em = $this->getDoctrine()->getManager();
                            $em->getConnection()->beginTransaction();

                            try {

                                foreach ($establecimiento->getDirecciones() as $direccion) {
                                    array_push($direccionesLista,$direccion);                        
                                }                    

                                foreach ($establecimiento->getActuaciones() as $actuacion) {
                                    $actuacion->setEstablecimiento($establecimiento);
                                    $actuacion->setIdEstablecimiento($establecimiento->getId());                        
                                    array_push($actuacionesLista,$actuacion);                        
                                }                    

                                
                                foreach ($establecimiento->getRazonesSociales() as $razonSocial) {
                                    array_push($razonSocialLista,$razonSocial);
                                    $razonSocial->setEstablecimiento($establecimiento);
                                    
                                }

                                $establecimiento->setDirecciones(array());
                                $establecimiento->setActuaciones(array());
                                $establecimiento->setRazonesSociales(array());
                                
                                if($id==0){
                                    $em->persist($establecimiento);
                                }

                                if($id > 0){
                                    foreach ($direccionesListaOriginales as $direccion) {
                                        if(!in_array($direccion,$direccionesLista,true) ){
                                            $em->remove($direccion);
                                        }
                                    }
                                    foreach ($actuacionesListaOriginales as $actuacion) {
                                        if(!in_array($actuacion,$actuacionesLista) ){
                                            $em->remove($actuacion);
                                        }
                                    }
                                    
                                    foreach ($razonSocialListaOriginales as $razonSocial) {
                                        if( !in_array($razonSocial,$razonSocialLista,true) ){
                                            $em->remove($razonSocial);                                
                                        }
                                    }
                                }

                                foreach ($direccionesLista as $direccion) {
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

                                foreach ($actuacionesLista as $actuacion) {
                                    if($this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Actuacion')->checkActuacionExciste($actuacion)){
                                        $messageError .= "La Actuacion ".$actuacion->__toString()." ya existe en la base de datos";
                                    }else{                            
                                        $em->persist($actuacion);
                                    }
                                }

                                foreach ($razonSocialLista as $key => $razonSocial) {
                                    $razonSocial->setEstablecimiento($establecimiento);
                                    $em->persist($razonSocial);
                                }
                                

                                $em->persist($establecimiento);
                                $em->flush();

                                $resultadosUltimaInspeccion = $establecimiento->getResultadosUltimaInspeccion();
                                $resultadosUltimaInspeccion->setEstablecimiento($establecimiento);

                                $em->persist($resultadosUltimaInspeccion);
                                $em->flush();


                                $this->addFlash('success','GUARDADO');
                                if($messageError){
                                    $this->addFlash('error',$messageError);    
                                }
                                if($messageAmbiguedad){
                                    $this->addFlash('alert',$messageAmbiguedad);    
                                }                    

                                $em->getConnection()->commit();
                                $em->getConnection()->close();
                                $em->clear();


                                return $this->redirectToRoute('establecimiento_establecimiento_homepage', array('id'=> encriptador::mrcrypt_encrypt($establecimiento->getId())));

                            }catch (Exception $e) {
                                $this->addFlash('error',"NO GUARDO");
                                $em->getConnection()->rollBack();
                                $em->getConnection()->close();
                                $em->clear();
                                throw $e;                    
                                return $this->render('EstablecimientoEstablecimientoBundle:Default:index.html.twig' , array(
                                    'form' => $form->createview(),
                                    'tipoDisposicion' => $tipoDisposicion,
                                    'estadoCedula' => $estadoCedula,
                                    'tipoCedula' => $tipoCedula,
                                    'area' => $area,
                                    'inspectores' => $inspectores,
                                    'historial'=>$historial   ));                                                                
                            }
                        }
                        $this->addFlash('error','NO GUARDO VEA LOS ERRORES');
                    }                     
                    if($id > 0){                        
                        $em->clear(); 
                    }                  
                    return $this->render('EstablecimientoEstablecimientoBundle:Default:index.html.twig' , array(
                        'form' => $form->createview(),
                        'tipoDisposicion' => $tipoDisposicion,
                        'estadoCedula' => $estadoCedula,
                        'tipoCedula' => $tipoCedula,
                        'area' => $area,
                        'inspectores' => $inspectores,
                        'historial'=>$historial 
                          ));
                }else{                    
                    return $this->render('CoreBundle:Default:deniedHome.html.twig');
                }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }        
    }

    public function historialCambiosAction(Request $request, $idUsuario){
        
        $id=encriptador::mrcrypt_decrypt($idUsuario);
        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Historial');
        $historial = $em->getHistorialUsuario($id);
        $em->clear();


        return $this->render('EstablecimientoEstablecimientoBundle:Default:historialCambios.html.twig',array
            (
                'historial' =>$historial
            )
        );  
    }

    public function tablaListCedulasAction(Request $request, $id){
        $user=$this->getUser();

        if($user){
            $checker=$this->getUser()->getRoles();

             if ($this->isGranted('ROLE_ESTABLECIMIENTO_VIEW')){    
                $id=encriptador::mrcrypt_decrypt($id);
                $id=(int)$id;

                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaCedulas.yml"));        
                 
                $TablaCedulas=new TablaAjax($request,$em,$yaml);
                $TablaCedulas->setSpecialConditions("p.usuarioAutorizador is not null and (n.usuarioEliminador is null or n.usuarioEliminador = 0) and n.establecimiento = ".$id);

                $response = new Response($TablaCedulas->Initialize());
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

    public function tablaListDisposicionesAction(Request $request, $id){
        $user=$this->getUser();

        if($user){
            $checker=$this->getUser()->getRoles();

            if ($this->isGranted('ROLE_ESTABLECIMIENTO_VIEW')){                    

                $id=encriptador::mrcrypt_decrypt($id);
                $id=(int)$id;

                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaDisposiciones.yml"));        
                 
                $TablaDisposiciones=new TablaAjax($request,$em,$yaml);
                $TablaDisposiciones->setNativeQuery("SELECT d.id,CONCAT(IF(t.id = 11,'IF-','DI-'),d.numero,'-',r.reparticion,'-',d.anio) as numero,t.tipo_dispo,n.fecha_notificacion,n.direccion_notificada,e.estado,CONCAT(u.apellido,',',u.nombre) as notificador,dc.levantada FROM disposicion as d INNER JOIN notificacion as n on d.id = n.id INNER JOIN tipo_dispo as t on d.tipo_id = t.id INNER JOIN estado_notificacion as e on e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN reparticion as r on d.reparticion_id = r.id LEFT JOIN disposicion_clausura as dc on d.id = dc.id WHERE ");

                $TablaDisposiciones->setSpecialConditions("(n.usuario_eliminador is null or n.usuario_eliminador = 0) and n.establecimiento_id = ".$id);
                //$TablaDisposiciones->setSpecialConditions("");

                $response = new Response($TablaDisposiciones->Initialize());
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

    public function tablaListInspeccionesAction(Request $request, $id){
        $user=$this->getUser();

        if($user){
            $checker=$this->getUser()->getRoles();


            if ($this->isGranted('ROLE_ESTABLECIMIENTO_VIEW')){                    
                $id=encriptador::mrcrypt_decrypt($id);
                $id=(int)$id;

                $params = Array();
                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspecciones.yml"));        
                 
                $TablaInspecciones=new TablaAjax($request,$em,$yaml);
                $TablaInspecciones->setNativeQuery('SELECT o.id,o.id_sap,o.checklist, i.fecha_inspeccion,a.area, GROUP_CONCAT(u.apellido) as Inspectores FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id and (CASE WHEN i.fecha_inspeccion is NULL THEN i.fecha_inspeccion is null ELSE i.fecha_inspeccion = (SELECT max(fecha_inspeccion) from inspeccion where orden_inspeccion_id = o.id) END) INNER JOIN area as a on o.area_id = a.id INNER JOIN inspeccion_usuario as iu on i.id = iu.inspeccion_id INNER JOIN usuarios as u on iu.usuario_id = u.id WHERE o.establecimiento_id = :id and (o.eliminada IS NULL OR o.eliminada = 0) and o.checklist > 0 GROUP BY o.id,o.id_sap,o.checklist, i.fecha_inspeccion,a.area HAVING 1=1 and');
                $params[0][0] = 'id';
                $params[0][1] = $id;
                $TablaInspecciones->setNativeQueryParam($params);
                $TablaInspecciones->setSpecialConditions("1 = 1");

                $response = new Response($TablaInspecciones->Initialize());
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

    public function buscarPorSMPAction(Request $request, $idCalle,$altura,$idEstablecimiento){
        $user=$this->getUser();
        $usig = $this->get(UsigWS::class);
        if($user){
            if ($this->isGranted('ROLE_ESTABLECIMIENTO_VIEW')){           
                $idEstablecimiento=encriptador::mrcrypt_decrypt($idEstablecimiento);
                $idEstablecimiento=(int)$idEstablecimiento;
                $result['result'] = "NO";

                $direccion = new Direccion();
                $calle = new Calles();
                $establecimiento = new Establecimiento();
                $resultadoEstablecimiento = array();
                
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles');        
                $calle = $em->findOneById($idCalle);

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                $establecimiento = $em->findOneById($idEstablecimiento);
                $direccion->setCalle($calle);
                $direccion->setAltura($altura);
                $direccion->setEstablecimiento($establecimiento);
                $usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad);                

                if($messageError){
                    $result['result'] = "ERROR";
                    $result['data'] =$messageError;
                    $response = new Response(json_encode($result));                
                    $response->headers->set('Content-Type', 'application/json');
                    $em->clear();
                    return $response;
                }else{
                    $establecimientos = $em->buscarPorSMP($direccion);
                    $em->clear();
                    if(count($establecimientos) > 0){                        
                        $i = 0;
                        foreach ($establecimientos as $establecimiento) {
                            if($establecimiento->getId() != $idEstablecimiento){
                                $result['result'] = "SI";
                                $razonesSociales = $establecimiento->getRazonesSociales();
                                $resultadoEstablecimiento[$i]['id'] = $establecimiento->getId();
                                if($razonesSociales[0]){
                                    $resultadoEstablecimiento[$i]['razonSocial'] = $razonesSociales[0]->getRazonSocial()->__toString();    
                                }else{
                                    $resultadoEstablecimiento[$i]['razonSocial'] = '';
                                }                    
                                $i++;    
                            }                            
                        }

                        if(count($resultadoEstablecimiento) > 0){
                            return $this->render('EstablecimientoEstablecimientoBundle:Default:modalDireccionesEncontradas.html.twig' , array('establecimientos' => $resultadoEstablecimiento ));    
                        }
                        
                    }else{
                        $result['result'] = "NO";    
                    }

                    $response = new Response(json_encode($result));                
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
    public function unirEstablecimientoAction(Request $request )
    {
        $user=$this->getUser();

        if($user){
            if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')) {
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');


                $form = $this->createForm(UnirEstablecimientoType::class);
                if ($request->isMethod('POST')) {

                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();

                        $establecimiento1 = $em->findOneById($data['establecimiento1']);
                        $establecimiento2 = $em->findOneById($data['establecimiento2']);

                        if ($establecimiento1 && $establecimiento2) {
                            $result = $em->unirEstablecimiento($data['establecimiento1'], $data['establecimiento2']);
                            if ($result = '000000') {
                                $this->addFlash('success', 'Unidos correctamente');
                            } else {
                                $this->addFlash('error', "Error al unir los establecimientos");
                            }

                        } else {
                            $this->addFlash('error', "Establecimientos ingresados invalidos");
                        }

                    }

                }

                return $this->render('EstablecimientoEstablecimientoBundle:Default:unirEstablecimiento.html.twig', array(
                    'form' => $form->createview()
                ));
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }
}
