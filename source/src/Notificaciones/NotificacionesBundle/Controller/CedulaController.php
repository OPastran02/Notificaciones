<?php
namespace Notificaciones\NotificacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\DateTime;

use Notificaciones\NotificacionesBundle\Form\DistanciaNotificacionType;
use Notificaciones\NotificacionesBundle\Form\ListaNotificacionType;
use Notificaciones\NotificacionesBundle\Form\CedulaComplitType;

use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Notificaciones\NotificacionesBundle\Entity\Cedula;
use Notificaciones\NotificacionesBundle\Entity\Disposicion;
use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Notificaciones\NotificacionesBundle\Entity\Estado;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CedulaController extends Controller
{
    public function firmCedulaAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $fechasFirmaEnvioCompletas = false;
        $fechasFirmaVueltaCompletas = false;

        
          $estado = new Estado();

          $fechaEnvio = $request->get('fechaEnvioFirma');

          if($fechaEnvio){
            $fechaEnvio = str_replace("/", "-", $fechaEnvio);
            $fechaEnvio= new \DateTime($fechaEnvio);
            $fechasFirmaEnvioCompletas = true;
          }        

          $fechaDevolucion = $request->get('fechaDevolucion');
          if($fechaDevolucion){
            $fechaDevolucion = str_replace("/", "-", $fechaDevolucion);
            $fechaDevolucion= new \DateTime($fechaDevolucion);
            $fechasFirmaVueltaCompletas = true;
          }

         

          $observaciones=$request->get('observaciones');

          $idNotificacion=$request->get('id');

          if ($request->isMethod('POST')) {
              $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
              
              if($fechasFirmaEnvioCompletas &&  $fechasFirmaVueltaCompletas){
                $estado = $em->findOneById(2);
              }else{
                $estado = $em->findOneById(1);
              }            

              $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
              $notificacion = $em->findOneById($idNotificacion);

              if (!$notificacion) {
                  throw $this->createNotFoundException('Notificacion Not found');
              }

              if($fechaEnvio > $fechaDevolucion && $fechaDevolucion != ''){
                  $response = new Response(json_encode("ERROR FECHAS"));                
                  $response->headers->set('Content-Type', 'application/json');
                  return $response;
              }

              if( !empty($notificacion->getPedidoNot()->getUsuarioAutorizador()) ){
                  if($fechaEnvio != ''){
                    $notificacion->setFechaEnvioFirma($fechaEnvio);
                  }
                  if($fechaDevolucion != ''){
                    $notificacion->setFechaVueltaFirma($fechaDevolucion);  
                  }                
                  $notificacion->setObservaciones($observaciones);
                  $notificacion->setEstado($estado);

                  $em = $this->getDoctrine()->getManager();
                  $em->persist($notificacion);
                  $em->flush();
                  $em->getConnection()->close();
                  $em->clear();

                  $response = new Response(json_encode("OK"));                
                  $response->headers->set('Content-Type', 'application/json');
                  return $response;    
              }else{
                  $em->clear();
                  $response = new Response(json_encode("ERROR NO AUTORIZADA"));                
                  $response->headers->set('Content-Type', 'application/json');
                  return $response;
              }            

          }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      } 

    }

    public function listAsignarNotificadorAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_INBOX_VIEW')){
          $tipoCedula = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->selectTipoCedulaTabla();
        
          return $this->render('NotificacionesNotificacionesBundle:Default:listaNotificacionesProgramacion.html.twig',
               array('tipoCedula'=>$tipoCedula)
              );        
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }      

    }

    public function tablaListAsignarNotificadorAction(Request $request){
        $user=$this->getUser();

        if($user){

          $checker=$this->container->get('security.authorization_checker');

          if ($this->isGranted('ROLE_INBOX_VIEW')){
              $em = $this->get('doctrine')->getManager();
              $em = $this->get('doctrine')->getManager('default');
              $em = $this->get('doctrine.orm.default_entity_manager');

              $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaNotificaciones.yml"));        
               
              $TablaUsuarios=new TablaAjax($request,$em,$yaml);
              $TablaUsuarios->setSpecialConditions("((n.fechaVueltaFirma is not null and n.notificador is null and n.estado <> 4 and n.estado <> 11) or n.estado = 2)");

              $response = new Response($TablaUsuarios->Initialize());
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

    public function tablaListAsignarNotificadorDisposicionAction(Request $request){
        $user=$this->getUser();

        if($user){
          $checker=$this->container->get('security.authorization_checker');

          if ($this->isGranted('ROLE_INBOX_VIEW')){
              $em = $this->get('doctrine')->getManager();
              $em = $this->get('doctrine')->getManager('default');
              $em = $this->get('doctrine.orm.default_entity_manager');

              $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaNotificacionesDispo.yml"));        
               
              $TablaUsuarios=new TablaAjax($request,$em,$yaml);
              $TablaUsuarios->setSpecialConditions("((n.fechaVueltaFirma is not null and n.notificador is null and n.estado <> 4 and n.estado <> 11) or n.estado = 2)");

              $response = new Response($TablaUsuarios->Initialize());
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

    public function excelListAsignarNotificadorAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_INBOX_VIEW')){      
          $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
          $nombreArchivo= 'excelListAsignarNotificador'.date("d_m_Y").'.xls';
          $nombresheet="excelListAsignarNotificador";
          $data = $request->request->all();

          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaNotificaciones.yml"));

          //return new Response($yaml);
          
          $TablaNotificaciones=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
          $TablaNotificaciones->setSpecialConditions("((n.fechaVueltaFirma is not null and n.notificador is null and n.estado <> 4) or n.estado = 2)");


          $arrayTablaNotificaciones=$TablaNotificaciones->getQueryTable();
          $objPHPExcel = new \PHPExcel();

          $objPHPExcel->setActiveSheetIndex(0);
          $letra="a";
          $numero=1;
          foreach ($arrayTablaNotificaciones as $key => $value) {
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

    public function excelListAsignarNotificadorDisposicionAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_INBOX_VIEW')){      
          $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
          $nombreArchivo= 'excelListAsignarNotificador'.date("d_m_Y").'.xls';
          $nombresheet="excelListAsignarNotificador";
          $data = $request->request->all();

          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaNotificacionesDispo.yml"));

          //return new Response($yaml);
          
          $TablaNotificaciones=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
          $TablaNotificaciones->setSpecialConditions("((n.fechaVueltaFirma is not null and n.notificador is null and n.estado <> 4) or n.estado = 2)");


          $arrayTablaNotificaciones=$TablaNotificaciones->getQueryTable();
          $objPHPExcel = new \PHPExcel();

          $objPHPExcel->setActiveSheetIndex(0);
          $letra="a";
          $numero=1;
          foreach ($arrayTablaNotificaciones as $key => $value) {
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

    public function listAsignarDistanciaNotificadorAction($idNotificacion,$cuadras, Request $request){
      $user=$this->getUser();
      if($user){
        if ($idNotificacion != '0'){
            $idNotificacion=encriptador::mrcrypt_decrypt($idNotificacion);
            $idNotificacion=(int)$idNotificacion;
        }else{
            $idNotificacion=0;
        }
        $estado = new Estado();
        $data = array();
        $rows = array();
        $i = 0;
        if ($request->isMethod('POST')) {
            
            $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Disposicion');
            $disposicion = $repository->findOneByNotificacion($idNotificacion);

            if (!$disposicion) {
              $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
              $notificacion = $repository->findOneById($idNotificacion);    
            }else{              
              $notificacion = $disposicion->getNotificacion();
            }           

            if (!$notificacion) {
                throw $this->createNotFoundException('Notificacion Not found');
            }

            if( !empty($notificacion->getFechaVueltaFirma())  && empty($notificacion->getNotificador() ) ){
                $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                $rows = $repository->notificacionesCercanas($notificacion->getLon(),$notificacion->getLat(),$cuadras);
                $form = $this->createForm(DistanciaNotificacionType::class,$data,array('rows' => $rows, 'action' => $this->generateUrl('notificaciones_notificaciones_distancia_notificacion_programacion', array('idNotificacion' => encriptador::mrcrypt_encrypt($idNotificacion), 'cuadras' => $cuadras))));
            
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid() ){
                    $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
                    $estado = $em->findOneById(3);


                    $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
                    $em = $this->getDoctrine()->getManager();
                    while( !empty($form['Numero'.$i]) ){                 
                        if(!empty($form['Notificador'.$i])){
                            $notificacion = new Notificacion();
                            
                            $notificacion = $repository->findOneById( $form['Id'.$i]->getData() );
                            
                            if( !empty($form['Notificador'.$i]) && ($notificacion->getDisposicion() || $notificacion->getCedula() )  ){
                                $notificacion->setNotificador( $form['Notificador'.$i]->getData() );
                                $notificacion->setFechaEntrega(new \DateTime());
                                $notificacion->setEstado($estado);
                                $em->persist($notificacion);
                                $em->flush($notificacion);
                            }                       
                        }
                        $i++;
                    }
                    
                    $em->getConnection()->close();
                    $em->clear();

                    $response = new Response(json_encode("OK"));                
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }            

                return $this->render('NotificacionesNotificacionesBundle:Default:programacionNotificacionUsuario.html.twig' , array('form' => $form->createview() ));

            }else{
                $response = new Response(json_encode("ERROR NO FIRMADA"));                
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

    public function saveCedulaAction($idCedula,Request $request){
      $user=$this->getUser();


      if($user){
        $cedula = new Cedula();
        if ($idCedula != '0'){
            $idCedula=encriptador::mrcrypt_decrypt($idCedula);
            $idCedula=(int)$idCedula;
        }else{
            $idCedula=0;
        }
        $notificacion = new Notificacion();
        $vencimiento = array();


        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
        /** @var Cedula $cedula */
        $cedula = $em->findOneByNotificacion($idCedula);

        if (!$cedula) {
            throw $this->createNotFoundException('Cedula Not found');
        }
        
        $notificacion = $cedula->getNotificacion();

        $fecha_notificacion = $notificacion->getFechaNotificacion();        

        if($fecha_notificacion){
          $fecha = clone($fecha_notificacion);  
        }else{
          $fecha = null;
        }

        if(isset($fecha)){
            $fecha2 = clone $fecha;
            $fecha->add(new \DateInterval('P'.$notificacion->getPlazo1().'D'));        
            $fecha2->add(new \DateInterval('P'.$notificacion->getPlazo2().'D'));
            $vencimiento['primer'] = $fecha;
            $vencimiento['segundo'] = $fecha2;
        }else{
            $vencimiento['primer'] = null;
            $vencimiento['segundo'] = null;
        }

          if ($this->isGranted('ROLE_INBOX_ADMIN')){
              $form = $this->createForm(CedulaComplitType::class,$cedula,array('vencimientos' => $vencimiento,'adjunto'=>1));
          }else{
              $form = $this->createForm(CedulaComplitType::class,$cedula,array('vencimientos' => $vencimiento));
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
                            $file->move(__DIR__.'/../../../../web/uploads/Notificaciones', "CE".$cedula->getNumero().".pdf");
                        }else{
                            $errorArchivo = true;
                        }
                    }
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($cedula);
                $em->flush();
                $em->getConnection()->close();
                $em->clear();
                $this->addFlash('success','GUARDADO');
                if($errorArchivo){
                    $this->addFlash('error','"El Archivo no se pudo subir ya que no tiene el formato correcto"');
                }
            }
        }

        return $this->render('NotificacionesNotificacionesBundle:Default:cedula.html.twig' , array('form' => $form->createview() ));
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }        
    }

    public function eliminarNotificacionAction(Request $request){
      $user=$this->getUser();

      if($user){
      /*if(false === $this->isGranted('PEPE')){
        $response = new Response(json_encode("no tiene los permisos correspondientes"));                
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }else{*/
        $notificacion = new Notificacion();
        $estado = new Estado();

        $idNotificacion=$request->get('id');

        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado');
        $estado = $em->findOneById(4);

        $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Notificacion');
        $notificacion = $repository->findOneById($idNotificacion);

        if (!$notificacion) {
            throw $this->createNotFoundException('Notificacion Not found');
        }

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            $notificacion->setEstado($estado);
            $notificacion->setUsuarioEliminador(1);
            $em->persist($notificacion);
            $em->flush();
            $em->getConnection()->close();
            $em->clear();

            $response = new Response(json_encode("OK"));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            $response = new Response(json_encode("ERROR"));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
      //}
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }

    }

    /**PROTEGIDO**/
    public function inboxAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_INBOX_VIEW')){
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          return $this->render('NotificacionesNotificacionesBundle:Default:inbox.html.twig',
            array(
               'inspectores'=>$inspectores
              ));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }     
    }
    /**Protegido**/     
    public function tablaInboxAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_INBOX_VIEW')){  
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInbox.yml"));

            //return new Response($yaml);
            
            $TablaInbox=new TablaAjax($request,$em,$yaml,null,$checker);
            $TablaInbox->setSpecialConditions("ee.id = 1");

            $response = new Response($TablaInbox->Initialize());
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

    public function tablaRemitoCedulasAction(Request $request){
      $user=$this->getUser();

      if($user){
        $data = $request->request->all();
        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
        $arrayActasUtilizadas = $em->findremitoCedulas($data["fechaInicial"],$data["fechaFinal"]);        
        $em->clear();
        return $this->render('NotificacionesNotificacionesBundle:Default:remitoscedulastabla.html.twig' , array('arrayremitos' => $arrayActasUtilizadas ));
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }        
    }
    
    /**Protegido**/     
    public function excelInboxAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){        
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelEstablecimiento'.date("d_m_Y").'.xls';
            $nombresheet="excelEstablecimiento";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInbox.yml"));

            //return new Response($yaml);
            
            $tablaInbox=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);         
            
            $tablaInbox->setSpecialConditions("ee.id = 1");
            $arrayInbox=$tablaInbox->getQueryTable();

            $objPHPExcel = new \PHPExcel();


            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayInbox as $key => $value) {
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
            return $this->render('CoreBundle:Default:denied.html.twig');
        }  
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function imprimirInboxAction(Request $request,$id)
    {
      $user=$this->getUser();

      if($user){        
        if ($id != '0'){
            $id=encriptador::mrcrypt_decrypt($id);
            $id=(int)$id;
        }else{
            $id=0;
        }
        $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Cedula');
        $Cedula = $repository->findOneByNotificacion($id);
        $pdf=Array();        
        $TipoDomicilio=$Cedula->getNotificacion()->getTipoDomicilioNotificada();

        $cuerpo=$Cedula->getCuerpo();
        
        $pdf["NroCedula"]="Nro. Cedula: ".$Cedula->getNumero();
        $pdf["cuerpo"]=$cuerpo;
        $pdf["Destinatario"]=$Cedula->getNombreDestinatario();
        $pdf["Domicilio"]=$Cedula->getNotificacion()->getDireccionNotificada();
        
        $pdf["Comuna"]=$Cedula->getNotificacion()->getComunaNotificada();
        if($TipoDomicilio=='e'){
           $pdf["TipoDomicilio"]="DENUNCIADO";
        }else{
           $pdf["TipoDomicilio"]="CONSTITUIDO";
        } 
        $pdf["Actuacion"]=$Cedula->getActuacion(); 
        
        if($Cedula->getNotificacion()->getPlazo1() == '0' ||  $Cedula->getNotificacion()->getPlazo1() == '99999'){
          $pdf["Plazo"]= "-";
        }else{
          $pdf["Plazo"]=$Cedula->getNotificacion()->getPlazo1();   
        }  

        if($Cedula->getNotificacion()->getPlazo2() == '0' || $Cedula->getNotificacion()->getPlazo2() == '99999'){
          $pdf["Plazo2"]= "-";
        }else{
          $pdf["Plazo2"]=$Cedula->getNotificacion()->getPlazo2();     
        } 

        if($Cedula->getFojas() == '0'){
          $pdf["CopiaEnFojas"] = '-';
        }else{
          $pdf["CopiaEnFojas"]=$Cedula->getFojas();
        }

        

       $html = $this->renderView('NotificacionesNotificacionesBundle:Default:pdfpedido.html.twig', array('array' => $pdf
         ));

        return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'page-height' => 29,
                    'page-width'  => 21,
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'filename="file.pdf"'
                )
        );
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function imprimirInboxAutorizarAction(Request $request,$id)
    {
      $user=$this->getUser();

      if($user){        
        if ($id != '0'){
            $id=encriptador::mrcrypt_decrypt($id);                        
        }else{
            $id=0;
        }
        $repository = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Pedido');
        $Pedido = $repository->findOneById($id);
        $pdf=Array();

        $Notificaciones = $Pedido->getNotificaciones();
        $Cedula = $Notificaciones[0]->getCedula();

        

        $cuerpo= utf8_decode($Cedula->getCuerpo());
        
        $pdf["NroCedula"]="Nro. Cedula: ".$Cedula->getNumero();
        $pdf["cuerpo"]= utf8_encode( $cuerpo);
        $pdf["Destinatario"]= $Cedula->getNombreDestinatario() ;
        $pdf["Domicilio"]=$Cedula->getNotificacion()->getDireccionNotificada();
        $pdf["Comuna"]=$Cedula->getNotificacion()->getComunaNotificada();
        
        if($Cedula->getNotificacion()->getTipoDomicilioNotificada() == 'e'){
          $pdf["TipoDomicilio"]="DENUNCIADO";  
        }else{
          $pdf["TipoDomicilio"]="CONSTITUIDO";  
        }        
        $pdf["Actuacion"]=$Cedula->getActuacion();       

        if($Cedula->getNotificacion()->getPlazo1() == '0' || $Cedula->getNotificacion()->getPlazo1() == '99999'){
          $pdf["Plazo"] = '-';          
        }else{
          $pdf["Plazo"] = $Cedula->getNotificacion()->getPlazo1();
        }

        if($Cedula->getNotificacion()->getPlazo2() == '0' || $Cedula->getNotificacion()->getPlazo2() == '99999'){
          $pdf["Plazo2"]='-';
        }else{
          $pdf["Plazo2"]=$Cedula->getNotificacion()->getPlazo2();
        }        
           
        if($Cedula->getFojas() == '0'){
          $pdf["CopiaEnFojas"] = '-';
        }else{
          $pdf["CopiaEnFojas"]=$Cedula->getFojas();
        }

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
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function pdfCedulaAction($cedula,Request $request){
      $user=$this->getUser();
      if($user){
          $route = $this->container->getParameter('kernel.root_dir').'/../web/uploads/Notificaciones/';

          if (strpos($cedula, '-') !== false) {            
            $cedula = str_replace("-","",str_replace("ch-", "", strtolower($cedula)));
            if(file_exists($route.'CH'.$cedula.".pdf")){
                $mi_pdf2 = $route.'CH'.$cedula.".pdf";

                header('Content-type: application/pdf');
                readfile($mi_pdf2) or die("File not found.");
            }else{              
              if(file_exists($route.'ch'.$cedula.".pdf")){
                  $mi_pdf2 = $route.'ch'.$cedula.".pdf";

                  header('Content-type: application/pdf');
                  readfile($mi_pdf2) or die("File not found.");
              }
            }
            
            return $this->redirectToRoute('inspecciones_inspecciones_pdfinspeccion', ['checklist' => strtolower($cedula)]);
          }
        

        if(file_exists($route.'CE'.$cedula.".pdf")){
              $mi_pdf2 = $route.'CE'.$cedula.".pdf";

              header('Content-type: application/pdf');
              readfile($mi_pdf2) or die("File not found.");
        }else{
          if(file_exists($route.'ce'.$cedula.".pdf")){
              $mi_pdf2 = $route.'ce'.$cedula.".pdf";

              header('Content-type: application/pdf');
              readfile($mi_pdf2) or die("File not found.");
          }else{
            return new Response("Archivo no encontrado");
          }
        }

      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }
}
