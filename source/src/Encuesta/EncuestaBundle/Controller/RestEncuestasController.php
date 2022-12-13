<?php

namespace Encuesta\EncuestaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Inspecciones\InspeccionesBundle\Entity\Resultados;

use AppBundle\Service\UsigWS;
use AppBundle\Controller\ApiController;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;

use Encuesta\EncuestaBundle\Entity\ModeloEncuesta;
use Inspecciones\InspeccionesBundle\Entity\ResultadosFotos;
 
use Symfony\Component\HttpFoundation\Session\Session;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use AppBundle\Service\JwtAuth;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class RestEncuestasController extends FOSRestController
{     
    /**
     * Get a encuesta by ID     
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/conexion",)
     */
    public function getConexion(Request $request){

        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		if($authCheck['status'] == 'OK'){ 
            return new Response("1");          
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/modelos")
    */
    public function getEncuestasAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:ModeloEncuesta');        

        $modelos = $em->findAll();
        $serializer = $this->container->get('jms_serializer');
        $reports = $serializer->serialize($modelos, 'json');
        $em->clear();
        unset($serializer);
        return new Response($reports);
    }

    /**
     * Get a encuesta by ID     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/modelos/{id}",)
     */
    public function getEncuestaAction($id,Request $request)
    {       
        $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:ModeloEncuesta');        
        $encuesta = $em->findOneById($id);

        if(!$encuesta){
            $em->clear();
            return new Response('Error 404 - Encuesta no encontrada');        
        }
        
        $serializer = $this->container->get('jms_serializer');
        $reports = $serializer->serialize($encuesta, 'json');
        $em->clear();
        return new Response($reports);
    }

    /**
     * Get a encuesta by ID     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/modelosPorId/{id}",)
     */
    public function getEncuestaPorIdAction($id, Request $request)
    {
        set_time_limit(0);
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);


        if($authCheck['status'] == 'OK'){
            $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:ModeloEncuesta');

            $encuesta = $em->findOneById($id);

            if(!$encuesta){
                $em->clear();
                return new Response('Error 404 - Encuesta no encontrada');
            }

            $grupoPreguntas=[];
            $grupoPreguntas["id"]=$encuesta->getId();
            $grupoPreguntas["nombre_encuesta"]=$encuesta->getNombreEncuesta();
            $grupoPreguntas["grupo_preguntas"]=[];

            $iteracionGrupo = 0; //grupo_
            foreach($encuesta->getGrupoPreguntas() as $orden){
                $iteracionPregunta = 0; //pregunta
                $contadorPregunta = 0;
                $grupoPreguntas["grupo_preguntas"][$iteracionGrupo]["id"]=$orden->getId();
                $grupoPreguntas["grupo_preguntas"][$iteracionGrupo]["nombre_grupo"]=$orden->getNombreGrupo();
                $grupoPreguntas["grupo_preguntas"][$iteracionGrupo]["preguntas"]=array();

                foreach($orden->getRequisitos() as $pregunta){
                    $pregunta->setGrupo(null);

                    if($pregunta->getMostrarInicio()){
                        if($pregunta->getVistaPreguntaTablet()){
                            $grupoPreguntas["grupo_preguntas"][$iteracionGrupo]["preguntas"][$contadorPregunta]=$pregunta->getVistaPreguntaTablet();

                            $contadorPregunta++;
                        }
                    }
                    $iteracionPregunta++;
                }
                $iteracionGrupo++;
            }

            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($grupoPreguntas, 'json');
            $em->clear();

            return new Response($reports);
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a ambientes
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/ambientes",)
     */
    public function getAmbientesAction(Request $request)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);

        if($authCheck['status'] == 'OK'){
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Ambientes');
            $modelos = $em->findAll();
            $serializer = $this->container->get('jms_serializer');
            //$modelos['objeto'] = json_decode(json_encode($authCheck['obj']), true);
            $reports = $serializer->serialize($modelos, 'json');
            $em->clear();
            unset($serializer);
            return new Response($reports);
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a correccionRuidos
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/correccionruidos")
     */
    public function getCorreccionRuidosAction(Request $request)
    {        
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){ 
		
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:CorrecionRuidos');
            $modelos = $em->findAll();
            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($modelos, 'json');
            $em->clear();
            unset($serializer);
            return new Response($reports);
        }else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a zonificacion
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/zonificacion")
     */
    public function getZonificacionAction(Request $request)
    {       
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){  
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Ruidos');
            $modelos = $em->findAllRuidos();
            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($modelos, 'json');
            $em->clear();
            unset($serializer);
            return new Response($reports);
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a vibracion
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/vibraciones")
     */
    public function getVibracionesAction(Request $request)
    { 
        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Vibraciones');
        $modelos = $em->findAllVibracion();
        $serializer = $this->container->get('jms_serializer');
        $reports = $serializer->serialize($modelos, 'json');
        $em->clear();
        unset($serializer);
        return new Response($reports);
    }

    /**
     * Get a ordenesinspecciones by ID     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/inspecciones/{id}",)
     */
    public function getOrdenesInspeccion($id,Request $request)
    {

        $i = 0;
        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
        $inspector = $em->listaOrdenesParaRealizar($id);

        if(!$inspector){
            $em->clear();
            return new Response('No se encontraron inspecciones');
        }else{
            return new Response($inspector);
        }

        foreach ($inspector as $orden) {           
            
            $orden->setFajas(null);
            $orden->setActas(null);
            $orden->setFechaModificado(null);
            $orden->setIdUsuarioModificador(null);
            $orden->setFechaCreado(null);
            $orden->setIdUsuarioCreador(null);   

            $motivoClasificado = $orden->getMotivoInspeccion()->getMotivo();
            $motivoTexto = 'MOTIVO INSPECCION: ';
            $motivoRevision = '';

            $motivoOrden = $orden->getObservacionesMotivoInspeccion();

            if($motivoOrden){
                $motivoTexto .= $motivoOrden;
            }else{
                $motivoTexto = "Sin Motivo";
            }
            

            if($orden->getRevisionTablet() == 1){
                $motivoRevision = "MOTIVO REVISION: ".$orden->getRevisionObs()."\n\n--------------------------------------------------\n\n";
                //$motivoRevision = "MOTIVO REVISION: ".$orden->getRevisionObs();
            }
            
            $orden->setObservacionesMotivoInspeccion($motivoRevision.$motivoClasificado.': '.$motivoTexto);            

            //$orden->setEstablecimiento(null);
            $orden->setResultados(null);
            $establecimiento = $orden->getEstablecimiento();

            if($establecimiento){
                $establecimiento->setResultadosUltimaInspeccion(null);
                $establecimiento->setInspeccione(null);                
                $establecimiento->setIdEstado(null);
                $establecimiento->setFechaEstado(null);
                $establecimiento->setIdRubroPrincipal(null);
                $establecimiento->setFechaCreado(null);
                $establecimiento->setIdUsuarioCreador(null);
                $establecimiento->setFechaModificado(null);
                $establecimiento->setIdUsuarioModificador(null);

                $direcciones = $establecimiento->getDirecciones();

                foreach ($direcciones as $direccion) {
                    if(strpos( $direccion->getCalle()->getCalle() ,"'" )){
                        $direccion->getCalle()->setCalle( str_replace( "'","-", $direccion->getCalle()->getCalle() ) );
                    }
                }

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $inspector["Documentos"][$i]['Inspecciones'] = $em->numeroCheckInspeccionesRealizadasxEstablecimiento($establecimiento->getId());
                $inspector["Documentos"][$i]['Actas'] = $em->actasLabradasxEstablecimiento($establecimiento->getId());
                

                $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
                $cedulas = $em->cedulasxEstablecimiento($establecimiento->getId());
                $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
                $disposiciones = $em->disposicionesxEstablecimiento($establecimiento->getId());

                $inspector["Documentos"][$i]['Notificaciones'] = array();

                foreach ($cedulas as $cedula) {
                    if($this->getDocumento($cedula->getNotificacion()->getId() != 'Error 404 - No se enconto el documento' )){
                        array_push($inspector["Documentos"][$i]['Notificaciones'], $cedula->getNotificacion());
                    }                    
                }

                foreach ($disposiciones as $disposicion) {
                    if($this->getDocumento($disposicion != 'Error 404 - No se enconto el documento' )){
                        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                        $notificacion = $em->notificacionxId($disposicion['id']);
                        array_push($inspector["Documentos"][$i]['Notificaciones'], $notificacion);
                    }                    
                }
                                
                foreach ($inspector["Documentos"][$i]['Notificaciones'] as $value) {
                    
                    $value->setPedidoNot(null);
                    $value->setEstablecimiento(null);
                    $value->setNotificador(null);
                    $cedula = $value->getCedula();
                    if($cedula){
                        $cedula->setCuerpo(null);
                    }
                    $value->setDisposicion(null);
                                        
                                       
                }               

                $razonesSociales = $establecimiento->getRazonesSociales();

                foreach ($razonesSociales as $key => $value) {
                    $razonSocial = $value->getRazonSocial();
                    $value->setFechaCreado(null);
                    $value->setIdUsuariocreador(null);

                    if($razonSocial){
                        $razonSocial->setEstablecimiento(null);
                        $razonSocial->setFechaCreado(null);
                        $razonSocial->setIdUsuarioCreador(null);
                        $razonSocial->setFechaModificado(null);
                        $razonSocial->setIdUsuarioModificador(null);
                    }
                }
            }

            $direccionesPv = $orden->getDirecciones();

            foreach ($direccionesPv as $direccion) {
                if(strpos( $direccion->getCalle()->getCalle() ,"'" )){
                    $direccion->getCalle()->setCalle( str_replace( "'","-", $direccion->getCalle()->getCalle() ) );
                }
            }

            /**************ESTE CODIGO HAY Q QUITARLO CUANDO SE ARREGLE EN LA TABLET***************************************************/
            $inspecciones = $orden->getInspecciones();
            $fechaPro = $inspecciones[0]->getFechaProgramado();
            $hoy = new \DateTime("now");
            $diff=$fechaPro->diff($hoy);            
            $simbolo = $diff->format('%R');
            $diferencia = (int)$diff->format('%a');            
            if($simbolo == '+' && $diferencia >0 ){
                $fechaPro = $fechaPro->format('Y-m-d');
                $fechaPro = date("Y-m-d",strtotime("$fechaPro   -1 day"));
                $fechaPro = new \DateTime($fechaPro);
                $inspecciones[0]->setFechaProgramado($fechaPro);
            }
            /**************ESTE CODIGO HAY Q QUITARLO CUANDO SE ARREGLE EN LA TABLET***************************************************/


            $inspectores = $inspecciones[0]->getInspectores();

            foreach ($inspectores as $key => $value) {
                
                    $value->setPassword(null);
                    $value->setUltimaConexion(null);
                    $value->setFechaCreado(null);
                    $value->setIdUsuarioCreador(null);
                    $value->setFechaModificado(null);
                    $value->setIdArea(null);
                    $value->setIpUsuario(null);
                    $value->setIdUsuarioModificador(null);    
                
                
            }


            $modeloEncuesta = $orden->getModeloCheckList();
            
            if($modeloEncuesta){
                $modeloEncuesta->setGrupoPregunta(null);    
                $modeloEncuesta->setPreguntasDesHabilitada(null);
            }
            
            $i++;
        }

        $serializer = $this->container->get('jms_serializer');
        $reports = $serializer->serialize($inspector, 'json');
        $em->clear();
        return new Response($reports);

        //return $inspector;
    }

    /**
     * Get a ordenesinspecciones by ID Modificado para el nuevo sistema    
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/inspeccionesPorInspector/{id}",)
     */

    public function getOrdenesInspeccionDos(Request $request,$id)
    {   
        
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){

            set_time_limit(0);
            ini_set('max_execution_time', 0);
            $i = 0;
            $j = 0;//para documentos;
            $k = 0; //para orden de inspeccion;
            $em = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');
            $idUsuario = $em->findIdBycuit($id);
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $documentos = $em->listaOrdenesParaRealizarTabletNuevo($idUsuario);
            $adjuntos=array();

            $arrayAMostrar=array();
            if(!$inspector && !$documentos){
                $em->clear();
                return new Response('No se encontraron inspecciones');
            }

            foreach ($documentos as $orden) {

                //AGREGAR DATOS BASICOS
                $arrayAMostrar[$i]=$orden->getVistaInspecciones();
                $arrayAMostrar[$i]["orden_inspeccion"]=$orden->getInspecciones();
                $orderInspecciones=$orden->getInspecciones();
                foreach($orderInspecciones as $orderInspeccion){
                    $orderInspeccion->setOrdenInspeccion();
                    $inspectores=$orderInspeccion->getInspectores();
                    foreach($inspectores as $inspector){
                        $inspector->setPassword(null);
                        $inspector->setSistemaNotificaciones(null);
                        $inspector->setPedidos(null);
                        $inspector->setInbox(null);
                        $inspector->setAntecedentes(null);
                        $inspector->setProgramacion(null);
                        $inspector->setDocumentacion(null);
                        $inspector->setActasYFajas(null);
                        $inspector->setEstablecimientos(null);
                        $inspector->setIpUsuario(null);
                        $inspector->setRni(null);
                        $inspector->setAccessToken(null);
                    }
                }

                foreach ($arrayAMostrar[$i]["direcciones"] as $direccion){
                    $direccion->setOrdenInspeccion(null);
                }

                if ($arrayAMostrar[$i]["checklist_blanco"] == false){
                    //AGREGAR ADJUNTOS
                    $arrayAMostrar[$i]["Adjuntos"]=[];
                    $adjuntos=[];
                    for ($k=0; $k < 11; $k++) {
                        $rutaPdf = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Adjuntos/'.$orden->getId()."-".$k.'.pdf';
                        if(file_exists($rutaPdf)){
                            $adjuntos["checklist"]=$orden->getChecklist();
                            $adjuntos["orden"]=$k;
                            array_push($arrayAMostrar[$i]["Adjuntos"], $adjuntos);
                        }
                    }
                    if(empty($arrayAMostrar[$i]["Adjuntos"])){
                        $arrayAMostrar[$i]["Adjuntos"]=array();
                    }

                    //AGREGAR CERTIFICADOS
                    $arrayAMostrar[$i]["Certificaciones"]=[];
                    $certificados=[];
                    for ($k=0; $k < 5; $k++) {
                        $rutaPdf = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Adjuntos/'.$orden->getId()."-".$k.'.pdf';
                        if(file_exists($rutaPdf)){
                            $certificados["checklist"]=$orden->getChecklist();
                            $certificados["orden"]=$k;
                            array_push($arrayAMostrar[$i]["Certificaciones"], $certificados);
                        }
                    }

                    if(empty($arrayAMostrar[$i]["Certificaciones"])){
                        $arrayAMostrar[$i]["Certificaciones"]=array();
                    }
                }else{
                    $arrayAMostrar[$i]["Adjuntos"]=array();
                    $arrayAMostrar[$i]["Certificaciones"]=array();
                }
                //ARMAR ESTABLECIMIENTOS SI HAY
                $establecimiento = $orden->getEstablecimiento();
                if($establecimiento){

                    $arrayAMostrar[$i]["establecimiento"]=$establecimiento->getVistasEstablecimientosTablet();
                    $establecimiento->setResultadosUltimaInspeccion(null);
                    $establecimiento->setInspeccione(null);
                    $establecimiento->setIdEstado(null);
                    $establecimiento->setFechaEstado(null);
                    $establecimiento->setIdRubroPrincipal(null);
                    $establecimiento->setFechaCreado(null);
                    $establecimiento->setIdUsuarioCreador(null);
                    $establecimiento->setFechaModificado(null);
                    $establecimiento->setIdUsuarioModificador(null);
                    $arrayAMostrar[$i]["establecimiento"]["foto_establecimiento"]=$establecimiento->getFotoEstablecimientoTablet();
                    $direcciones = $establecimiento->getDirecciones();

                     foreach ($direcciones as $direccion) {
                        if(strpos( $direccion->getCalle()->getCalle() ,"'" )){
                            $direccion->getCalle()->setCalle( str_replace( "'","-", $direccion->getCalle()->getCalle() ) );
                        }
                    }

                    if ($arrayAMostrar[$i]["checklist_blanco"] == false){
                        //ARMAR INSPECCIONES
                        $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                        $arrayAMostrar[$i]["inspecciones"] = $em->numeroCheckInspeccionesRealizadasxEstablecimiento($establecimiento->getId());

                        //ARMAS CEDULAS
                        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
                        $cedulas = $em->cedulasxEstablecimiento($establecimiento->getId());
                        $arrayAMostrar[$i]["Cedulas"] = array();
                        $x=1;
                        foreach ($cedulas as $cedula) {
                            if($this->getDocumento($cedula->getNotificacion()->getId() != 'Error 404 - No se enconto el documento' )){
                                $viewCedula=array();
                                $viewCedula["checklist"]=$cedula->getNotificacion()->getId();
                                $viewCedula["orden"]=$x;
                                array_push($arrayAMostrar[$i]["Cedulas"], $viewCedula);
                                $x++;
                            }
                        }

                        if(empty($arrayAMostrar[$i]["Cedulas"])){
                            $arrayAMostrar[$i]["Cedulas"]=array();
                        }

                        //ARMAR DISPOSICIONES
                        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
                        $disposiciones = $em->disposicionesxEstablecimiento($establecimiento->getId());
                        $arrayAMostrar[$i]["Disposiciones"] = array();
                        $x=1;
                        foreach ($disposiciones as $disposicion) {
                            if($this->getDocumento($disposicion != 'Error 404 - No se enconto el documento' )){
                                $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                                $notificacion = $em->notificacionxId($disposicion['id']);
                                if($notificacion){
                                    $viewDispo=array();
                                    $viewDispo["checklist"]=$notificacion->getId();
                                    $viewDispo["orden"]=$x;
                                    array_push($arrayAMostrar[$i]["Disposiciones"], $viewDispo);
                                    $x++;
                                }
                            }
                        }

                        if(empty($arrayAMostrar[$i]["Disposiciones"])){
                            $arrayAMostrar[$i]["Disposiciones"]=array();
                        }
                    }else{
                        $arrayAMostrar[$i]["inspecciones"]=array();
                        $arrayAMostrar[$i]["Cedulas"]=array();
                        $arrayAMostrar[$i]["Disposiciones"]=array();
                    }

                    //ARMAR DIRECCIONES EN ESTABLECIMIENTO
                    $direcciones=$establecimiento->getDirecciones();
                    $arrayAMostrar[$i]["establecimiento"]["direcciones"]=array();
                    foreach($direcciones as $direccion){
                        $direccion->setEstablecimiento(null);
                        $direccion->setIdEstablecimiento(null);
                        array_push($arrayAMostrar[$i]["establecimiento"]["direcciones"],$direccion);
                    }

                    //ARMAR ACTUACIONES EN ESTABLECIMIENTO
                    $actuaciones=$establecimiento->getActuaciones();
                    $arrayAMostrar[$i]["establecimiento"]["actuaciones"]=array();
                    foreach($actuaciones as $actuacion){
                        $actuacion->setEstablecimiento(null);
                        $actuacion->setIdEstablecimiento(null);
                        array_push($arrayAMostrar[$i]["establecimiento"]["actuaciones"],$actuacion);
                    }

                    $razonesSociales = $establecimiento->getRazonesSociales();
                    $arrayAMostrar[$i]["establecimiento"]["razones_sociales"]=array();
                    foreach ($razonesSociales as $razonSocial) {
                        $razonSocial2 = $razonSocial->getRazonSocial();
                        $razonSocial->setFechaCreado(null);
                        $razonSocial->setIdUsuariocreador(null);

                        if($razonSocial2){
                            $razonSocial2->setEstablecimiento(null);
                            $razonSocial2->setFechaCreado(null);
                            $razonSocial2->setIdUsuarioCreador(null);
                            $razonSocial2->setFechaModificado(null);
                            $razonSocial2->setIdUsuarioModificador(null);
                        }
                        array_push($arrayAMostrar[$i]["establecimiento"]["razones_sociales"],$razonSocial);
                    }

                    if($arrayAMostrar[$i]["checklist_blanco"]){
                        $arrayAMostrar[$i]["establecimiento"]= new \stdClass;
                    }

                }else{
                    $arrayAMostrar[$i]["establecimiento"]= new \stdClass;
                }
                $i++;

            }
            //AUTORIZADORES
            $serializer = $this->container->get('jms_serializer');
            $inspecciones = $serializer->serialize($arrayAMostrar, 'json');

            $em->clear();

            return new Response(
                $inspecciones
            );
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a Documentos by IDordenesinspecciones     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/documentos/{id}",)
     */
    public function getDocumento($id)
    {
            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
            $cedula = $em->findOneByNotificacion($id);

            $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
            $disposicion = $em->disposicionesxId($id);

            if(!$cedula && !$disposicion ){
                $em->clear();
                return new Response('Error 404 - No se enconto el documento3');
            }

            if($cedula){
                // webs/uploads/Notificaciones
                $nameFile = 'CE'.$cedula->getNumero().'.pdf';
                $file=$this->readObject($nameFile, 'Notificaciones');
                return new RedirectResponse($file);
                $em->clear();

            }elseif($disposicion){
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Reparticion');
                $reparticion = $em->findOneById($disposicion[0]['reparticion_id']);
                $nameFile = 'DI-'.$disposicion[0]['numero']."-".$reparticion->getReparticion()."-".$disposicion[0]['anio'].'.pdf';
                $file=$this->readObject($nameFile, 'Notificaciones');
                return new RedirectResponse($file);
                $em->clear();
            }else{
                $em->clear();
                return 'Error 404 - No se enconto el documento2';
            }
            $em->clear();
    }

    /**
     * Get a Documentos by IDordenesinspecciones
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/documentos/checklist/{checklist}",)
     */
    public function getCheckListInspeccion(Request $request,$checklist)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);

		if($authCheck['status'] == 'OK'){

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $orderInspeccion = $em->findOneByChecklist($checklist);

            if($orderInspeccion){
                $establecimiento = $orderInspeccion->getEstablecimiento();
                if($establecimiento){
                    $ordenes = $establecimiento->getInspecciones();
                    $completa = $ordenes[0]->getCompleta();
                    if(!$completa){
                        $nameFile = 'CH'.$checklist.".pdf";
                        $file=$this->readObject($nameFile, 'Inspecciones');
                        return new RedirectResponse($file);
                        $em->clear();
                    }
                }else{
                    $nameFile = 'CH'.$checklist.".pdf";
                    $file=$this->readObject($nameFile, 'Inspecciones');
                    return new RedirectResponse($file);
                    $em->clear();

                }
            }else{
                // webs/uploads/inspecciones
                $nameFile = 'CH'.$checklist.".pdf";
                $file=$this->readObject($nameFile, 'Inspecciones');
                return new RedirectResponse($file);
                $em->clear();
            }
            return new Response("no tiene checklist asociado");
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }

    }

    /**
     * Get a Documentos by IDordenesinspecciones
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/documentos/most/{checklist}",)
     */
    public function getMostDocuments(Request $request,$checklist)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){ 
		
            $datos=[];
            $datos["idMost"]="1";
            $datos["pdf"]="pdf";
            $datos["orden"]="0";
            return new Response(json_encode($datos));

                    
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/usuarios")
    */
    public function getUsuariosAction(Request $request)
    {

        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){ 

            $em = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');  
            $users=  $em->findAll();
            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($users, 'json');
            $em->clear();
            return new Response($reports);
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }

    }

    /**
     * Get a encuesta by ID     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/usuarios/{id}",)
     */
    public function getUsuarioAction(Request $request,$id)
    {       
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){ 

            $em = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');        
            $usuario = $em->findOneById($id);

            if(!$usuario){
                $em->clear();
                return new Response('Error 404 - Usuario no encontrada');
            }
            
            //return $usuario;
            $modelos = $em->findAll();
            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($usuario, 'json');
            $em->clear();
            return new Response($reports); 
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a encuesta by ID     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/datosutiles/{calle}/{altura}/{checklist}",)
     */
    public function getDatosUtiles(Request $request,$calle,$altura,$checklist)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){

            $usig = $this->get(UsigWS::class);
            $datos = $usig->getDatosUtiles($calle,$altura,$checklist);
            
            $serializer = $this->container->get('jms_serializer');
            $reports = $serializer->serialize($datos, 'json');
            return new Response($reports);

        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }

    /**
     * Get a ordenesinspecciones by ID     
     * @return array
     *
     * @Rest\Post("/encuesta/setInspecciones")
     */
    public function setInspecciones(Request $request)
    {
        
        $error = '';
        $arrayPrueba = array();
        $arrayChecklist = array();
        $arrayResultadosNoEliminar = array();
        
        //$inspeccionesPost = json_decode($inspeccion, TRUE);
        //$inspecciones = $inspeccionesPost;

        /*$file = fopen("archivo2.txt", "a");        

        fwrite($file, $request->getContent() . PHP_EOL);

        fclose($file);*/

        $arrayFotos = array();

        $inspeccionesPost = json_decode($request->getContent(),TRUE);
        $inspecciones = $inspeccionesPost['respuestas'];
        $inspecciones = json_decode($inspecciones, TRUE);

        if( !array_key_exists(0,$inspecciones) ){                    
            return new Response("Sin Actualizaciones");
        }

        $arrayPrueba[0] = $inspecciones[0];
        $arrayPrueba[0]['respuestas'] = array();

        if($inspecciones[0]['respuesta_id']){            
            array_push($arrayPrueba[0]['respuestas'], $inspecciones[0]['respuesta_id']);    
        }
        
        $i = 0;
        foreach ($inspecciones as $inspeccion) {
            if($i == 1){                
                $encontrado = false;
                $j = 0;
                
                while(!$encontrado && $j < count($arrayPrueba)){
                    if($arrayPrueba[$j]['checklist'] == $inspeccion['checklist'] && $arrayPrueba[$j]['grupo_id'] == $inspeccion['grupo_id'] && $arrayPrueba[$j]['pregunta_id'] == $inspeccion['pregunta_id']){
                        
                        array_push($arrayPrueba[$j]['respuestas'], $inspeccion['respuesta_id']);

                        $encontrado = true;
                    }
                    $j++;
                }
                
                if(!$encontrado){
                    $inspeccion['respuestas'] = array();                    
                    if($inspeccion['respuesta_id']){
                        array_push($inspeccion['respuestas'], $inspeccion['respuesta_id']);    
                    }
                    array_push($arrayPrueba,$inspeccion);
                }
            }

            $i=1;
        }

        //return new Response(json_encode($arrayPrueba));

        foreach ($arrayPrueba as $inspeccion) {
            array_push($arrayChecklist, $inspeccion['checklist']);
        }

        $arrayChecklist = array_unique($arrayChecklist);
        
        foreach ($arrayPrueba as $inspeccion) {
          
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $orderInspeccion = $em->findOneByChecklist($inspeccion['checklist']);

            if($orderInspeccion){
                $id = $orderInspeccion->getId();
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
                $resultado = $em->findOneBy(array('ordenInspeccion'=>$id,'grupo'=>$inspeccion['grupo_id'],'pregunta'=>$inspeccion['pregunta_id']));

                $completa = $orderInspeccion->getCompleta();               
                
                $revisadoTablet = $inspeccion['revisado'];
                $completaTablet = $inspeccion['completo'];
                
                if(!$inspeccion['fecha_fin'] || $inspeccion['fecha_fin'] == 0){
                    $fecha = null;
                }else{
                    $fecha = new \DateTime($inspeccion['fecha_fin']);
                }

                $fechaAnterior = $orderInspeccion->getFechaFinTablet();               
                
                
                $ems = $this->getDoctrine()->getManager();
                $ems->getConnection()->beginTransaction();
                
                //si la fecha de fin es mayor a la anteriro actualizo el revicion
                try{
                    if($fecha > $fechaAnterior || is_null($fechaAnterior) || $fechaAnterior == ''){
                        if($revisadoTablet == '1' && !$completa && $completaTablet == '1'){
                            $orderInspeccion->setRevisionTablet(false);
                            $orderInspeccion->setCompleta(true);
                            $orderInspeccion->setFechaInspeccionCompleta(new \DateTime());
                        }elseif($completaTablet == '1' && !$completa){                    
                            $orderInspeccion->setCompleta(true);
                            $orderInspeccion->setFechaInspeccionCompleta(new \DateTime());
                        }elseif($completaTablet == '1' && $completa){
                        }else{                    
                            $orderInspeccion->setCompleta(false);
                            $orderInspeccion->setFechaInspeccionCompleta(null);
                        }    

                        $orderInspeccion->setFechaFinTablet($fecha);               
                        
                        $ems->persist($orderInspeccion);
                        $ems->flush($orderInspeccion);
                    }                                
                    
                    if(!$resultado){
                        $resultado = new Resultados();
                        $resultado->setOrdenInspeccion($orderInspeccion);                    

                        $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:GrupoPreguntas');
                        $grupo = $em->findOneById($inspeccion['grupo_id']);
                        $resultado->setGrupo($grupo);

                        $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:Pregunta');
                        $pregunta = $em->findOneById($inspeccion['pregunta_id']);
                        $resultado->setPregunta($pregunta);
                    }
                    
                    if($inspeccion['RespuestaLibre'])
                    {                        
                        if( strpos($inspeccion['RespuestaLibre'],"undefin") === false ){

                            $tipoPregunta = $resultado->getPregunta()->getTipo()->getTipoPregunta();
                            if($tipoPregunta == 'Time' || $tipoPregunta == 'Date' || $tipoPregunta == 'DateTime'){                                
                                $fecha = substr($inspeccion['RespuestaLibre'],0,strpos($inspeccion['RespuestaLibre'],"(") -1);
                                $timeZone = new \DateTimeZone('EST');
                                $fecha = new \DateTime($fecha,$timeZone);
                                switch ($tipoPregunta) {
                                    case 'Time':
                                        $resultado->setRespuestaLibre($fecha->format('H:i'));
                                    break;
                                    case 'Date':
                                            $resultado->setRespuestaLibre($fecha->format('Y-m-d'));
                                        break;
                                    case 'DateTime':
                                            $resultado->setRespuestaLibre($fecha->format('Y-m-d H:i'));
                                        break;
                                }                                
                            }else{
                                $resultado->setRespuestaLibre($inspeccion['RespuestaLibre']);
                            }
                        }

                    }

                    if($inspeccion['orden'])
                    {
                        $resultado->setOrden($inspeccion['orden']);
                    }else{
                        $resultado->setOrden(0);
                    }                

                    $respuestas = $resultado->setRespuestas(array());
                    $ems->persist($resultado);
                    $ems->flush($resultado);

                    $inspeccion['respuestas'] = array_unique($inspeccion['respuestas']);                
                    foreach ($inspeccion['respuestas'] as $id) {
                        $em = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:Respuestas');
                        $respuesta = $em->findOneById($id);

                        if($respuesta){
                            $resultado->addRespuesta($respuesta);                        
                        }
                    }

                    $ems->persist($resultado);
                    $ems->flush($resultado);

                    $ems->getConnection()->commit();
                    $ems->getConnection()->close();
                    $ems->clear();

                }catch (Exception $e) {                    
                    $ems->getConnection()->rollBack();
                    $ems->getConnection()->close();
                    $ems->clear();
                    throw $e;                                                                                   
                }
                
                $em->clear();
                

                //var_dump($resultado->getId());
                array_push($arrayResultadosNoEliminar, $resultado->getId());
            }           
                        
        }

        //var_dump($arrayResultadosNoEliminar);
        $arrayResultadosNoEliminar = array_unique($arrayResultadosNoEliminar);

        if(count($arrayResultadosNoEliminar)>0){
            foreach ($arrayChecklist as $checklist) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $orderInspeccion = $em->findOneByChecklist($checklist);

                $resultados = $orderInspeccion->getResultados();

                $em = $this->getDoctrine()->getManager();

                foreach ($resultados as $resultado) {                
                    if( !in_array($resultado->getId(), $arrayResultadosNoEliminar) ){                    
                        $fotos = $resultado->getFotos();
                        foreach ($fotos as $foto) {
                            $resultado->removeFoto($foto);
                            $ems->remove($foto);
                        }
                        $ems->remove($resultado);
                        $orderInspeccion->removeResultado($resultado);                    
                    }
                }
                $em->persist($orderInspeccion);
                $em->flush($orderInspeccion);
                
            }  
        }
        
        
        
        return new Response("ok Inspecciones");
    }

    /**
     * Get a ordenesinspecciones by ID     
     * @return array
     *
     * @Rest\Post("/encuesta/setInspeccion")
     */
    public function setInspeccion(Request $request)
    { 
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		if($authCheck['status'] == 'OK'){
          /*  $arrayPrueba = array();
            $arrayChecklist = array();
            $arrayResultadosNoEliminar = array();

            $inspeccionesPost = json_decode($request->getContent(),TRUE);

            $arrayPrueba= $inspeccionesPost["jsonLargo"]["data_falta"]["grupo_respuestas"]["preguntas_respondidas"];

            if($inspecciones[0]['respuesta_id']){
                array_push($arrayPrueba[0]['respuestas'], $inspecciones[0]['respuesta_id']);
            }*/

            return new Response(json_encode($authCheck["status"]));
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }

    }

    /**
     * Get a ordenesinspecciones by ID     
     * @return array
     *
     * @Rest\Post("/encuesta/setInspeccionesFotos/{a}")
     */
    public function setInspeccionesFotos($a,Request $request)
    {        
        $error = '';
        $arrayFotos = array();

        $inspeccionesPost = json_decode($request->getContent(),TRUE);        
        $inspecciones = $inspeccionesPost['respuestas'];        

        $inspecciones = json_decode($inspecciones, TRUE);

        foreach ($inspecciones as $inspeccion)
        {

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $orderInspeccion = $em->findOneByChecklist($inspeccion['checklist']);

            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
            $resultado = $em->findOneBy(array('ordenInspeccion'=> $orderInspeccion->getId(),'pregunta' =>$inspeccion['pregunta_id'] ,'grupo'=> $inspeccion['grupo_id']));

            if($resultado)
            {                
                //var_dump($resultado->getId());
                if(!array_key_exists($resultado->getId(),$arrayFotos)){
                    $arrayFotos[$resultado->getId()] = array();                    
                }  

                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:ResultadosFotos');
                $foto = $em->findOneBy(array('resultados'=>$resultado->getId(),'orden'=>$inspeccion['orden']));

                if(!$foto){                    
                    $foto = new ResultadosFotos();
                    $foto->setFoto($inspeccion['foto']);
                    $foto->setOrden($inspeccion['orden']);                    

                    //$resultado->addFoto($foto);
                }else{                    

                    $foto->setFoto($inspeccion['foto']);                
                }

                array_push($arrayFotos[$resultado->getId()],$foto);                
            }

        }        

        $ems = $this->getDoctrine()->getManager();
        $ems->getConnection()->beginTransaction();

        try {            
            foreach ($arrayFotos as $key => $fotos) {
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Resultados');
                $resultado = $em->findOneById($key);

                if($resultado){                    
                    $fotosABorrar = $resultado->getFotos();
                    foreach ($fotosABorrar as $fotoABorrar) {
                        $resultado->removeFoto($fotoABorrar);
                        $ems->remove($fotoABorrar);
                    }

                    foreach ($fotos as $foto) {
                        $resultado->addFoto($foto);
                        $ems->persist($foto);
                        $foto->setResultados($resultado);
                    }

                    $ems->persist($resultado);
                    $ems->flush();    
                }
                
            }

            $ems->getConnection()->commit();
            $ems->getConnection()->close();
            $ems->clear();            

            return new Response("ok Foto");
            

        }catch (Exception $e) {
            $ems->getConnection()->rollBack();
            $ems->getConnection()->close();            
            $ems->clear();            
            
            return new Response("error Foto");    
        }        
        
    }

    /**
     * Get a ordenesinspecciones by ID     
     * @return array
     *
     * @Rest\Post("/encuesta/estado/inspecciones/{idInspector}")     
     */
    public function getEstadoInspecciones(Request $request,$idInspector)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){ 

            $inspeccionesPost = json_decode($request->getContent(),TRUE);
            $inspecciones = $inspeccionesPost['inspecciones'];        
            $inspecciones = json_decode($inspecciones, TRUE);
            $respuesta = array();        

            foreach ($inspecciones as $inspeccion) {
                $checkEstado = array();
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                $orderInspeccion = $em->findOneByChecklist($inspeccion);

                if(!$orderInspeccion){
                    $checkEstado['checklist'] = $inspeccion;
                    $checkEstado['estado'] = -1;
                }else{

                    $inspeccionesCheckInspector = $orderInspeccion->getInspecciones();                
                    if(count($inspeccionesCheckInspector) > 0 ){
                        $inspectores = $inspeccionesCheckInspector[0]->getInspectores();
                        foreach ($inspectores as $inspector) {
                            if($inspector->getId() == $idInspector){
                                
                                $completa = $orderInspeccion->getCompleta();
                                $revision = $orderInspeccion->getRevisionTablet();
                                $anulada = $orderInspeccion->getAnulada();
                                $checklistBlanco= $orderInspeccion->getChecklistBlanco();

                                /*ACA VA LA LOGICA DE VENCIDAS ETC*/
                                $cedulasVencidas= $orderInspeccion->getCedulasVencidas();
                                $clausurasVigentes= $orderInspeccion->getClausurasVigentes();
                                
                                if($revision){
                                    $checkEstado['checklist'] = $orderInspeccion->getChecklist();
                                    $checkEstado['estado'] = 2;
                                }elseif($completa || $anulada){
                                    $checkEstado['checklist'] = $orderInspeccion->getChecklist();
                                    $checkEstado['estado'] = 1;
                                }else{
                                    $checkEstado['checklist'] = $orderInspeccion->getChecklist();
                                    $checkEstado['estado'] = 0;
                                }

                            }else{
                                $checkEstado['checklist'] = $orderInspeccion->getChecklist();
                                $checkEstado['estado'] = 1;
                            }
                        }
                    }else{
                        $checkEstado['checklist'] = $orderInspeccion->getChecklist();
                        $checkEstado['estado'] = 1;
                    }                    
                }
                $em->clear();

                array_push($respuesta,$checkEstado);

            }

            return new Response(json_encode($respuesta));
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }


    /**
     * Get a Documentos by IDordenesinspecciones     
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/adjuntos/{checklist}/{orden}",)
     */
    public function getAdjuntos(Request $request, $checklist,$orden)
    {
        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){
            $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->findOneByChecklist($checklist);
            $em->clear();
            $nameFile = $ordenInspeccion->getId()."-".$orden.'.pdf';

            if($ordenInspeccion){
                $completa = $ordenInspeccion->getCompleta();
                if($completa){
                    $em->clear();
                    return new Response('La inspeccion ya fue cerrada');
                }else{
                    $file=$this->readObject($nameFile, 'Adjuntos');
                    return new RedirectResponse($file);
                    $em->clear();
                }
            }else{
                $em->clear();
                return new Response('No hay adjunto asociado');
            }
            $em->clear();
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }
    /**
     * Get a Documentos by IDordenesinspeccionescert   
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/encuesta/certificaciones/{checklist}/{orden}",)
     */
    public function getCertificaciones(Request $request,$checklist,$orden)
    {

        $token = $request->headers->get('Authorization');
        $jwt_auth = new JwtAuth();
        $authCheck = $jwt_auth->validarToken($token);
		
		if($authCheck['status'] == 'OK'){
           $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
            $ordenInspeccion = $em->findOneByChecklist($checklist);

            if($ordenInspeccion){
                $completa = $ordenInspeccion->getCompleta();
                if($completa){
                    $em->clear();
                    return new Response('La inspeccion ya fue cerrada');
                }else{
                    $nameFile = $ordenInspeccion->getId()."-".$orden.'.pdf';
                    $file=$this->readObject($nameFile, 'Adjuntos');
                    return new RedirectResponse($file);
                    $em->clear();
                }
            }else{
                $em->clear();
                return new Response('No hay orden de inspeccion asociada');
            }
        } else {
            $res = json_encode($authCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return new Response($res);
        }
    }
    /**
     * Get a Documentos by IDordenesinspeccionescert
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/encuesta/certificacionesaws/",)
     */
    public function getCertificacionesAWS($filename)
    {
        return new Response($this->readObject('carlos'));
    }

    public function readObject(string $filename, string $prefix )
    {
        $key		 = $this->getParameter('key');
        $secret 	 = $this->getParameter('secret');
        $region 	 = $this->getParameter('region');
        $base_url 	 = $this->getParameter('base_url');
        $version 	 = $this->getParameter('version');
        $path		 = $this->getParameter('path');


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
                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $path,
                    'Key' => $prefix.'/'.$filename
                ]);
                $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                $presignedUrl = (string)$request->getUri();
                return $presignedUrl;

        } catch (AwsExceptionInterface $e) {
            throw Exception\StorageException::putError( $e );
        }
    }
}
