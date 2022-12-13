<?php

namespace Faltas\FaltasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Faltas\FaltasBundle\Entity\AsignacionActa;
use Faltas\FaltasBundle\Entity\Acta;
use Usuario\UsuarioBundle\Entity\Usuarios;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Faltas\FaltasBundle\Form\AsignacionActaType;

use Faltas\FaltasBundle\Form\ACtaType;
use Faltas\FaltasBundle\Form\ModalAsignacionActaType;
use Doctrine\ORM;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AsignacionActaController extends Controller
{
    public function indexAction()
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){ 
          return $this->render('FaltasFaltasBundle:Default:index.html.twig');
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

    public function asignacionActasAction(Request $request,$id = '0')
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){ 
          if ($id != '0'){
              $id=encriptador::mrcrypt_decrypt($id);
              $id=(int)$id;
          }else{
              $id=0; 
          }

          $form = $this->createForm(AsignacionActaType::class);
          $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa')->selectEstadosTabla();
          $inspector = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
              if($request->isMethod('POST') ){
                  $form->handleRequest($request);

                  if ($form->isValid() and strlen($form["Serie"]->getData()) <= 2 and $form["NumeroUno"]->getData() <= $form["NumeroDos"]->getData() and $this->isGranted('ROLE_ACTASFAJAS_EDIT') ) {
                     
                      $cantidad=$form["NumeroDos"]->getData()-$form["NumeroUno"]->getData();
                      if($cantidad>=0 and $cantidad<26){
                        for ($i=$form["NumeroUno"]->getData();$i<=$form["NumeroDos"]->getData();$i++){
                             $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionActa');
                             $existe=$em->findBySerieAndNumero($form["Serie"]->getData(), $i);
                             if($existe==1){
                                $this->addFlash("error", "ya existe un acta con la serie ".$form["Serie"]->getData()." y el numero ".$i);
                             }else{
      
                                 $Acta = new Acta(); 
                                 $AsignacionActa = new AsignacionActa();                               

                                 $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa');
                                 $estadoActa=$em->findOneById(1);
                                 $Acta->setEstado($estadoActa);
                                 $Acta->setSerie($form["Serie"]->getData());
                                 $Acta->setNumero($i);
                                 $em = $this->getDoctrine()->getManager();
                                 $em->persist($Acta);
                                 $em->flush();
                                                            
                                 
                                 $repository = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');
                                 $Usuario = $repository->findOneByid($form["usuario"]->getData()->getId());
                                 $AsignacionActa->setInspector($Usuario);
                                 $AsignacionActa->setFecha($form["fecha"]->getData());   
                                 $AsignacionActa->setActa($Acta); 
                                 $em = $this->getDoctrine()->getManager();                                             
                                 $em->persist($AsignacionActa);
                                 $em->flush();
                                 $em->getConnection()->close();
                                 $em->clear();
                             }
                        }  

                         $this->addFlash("success", "actas creadas con éxito");
                      }else{
                         $this->addFlash("error", "No se pudieron guardar los cambios");    
                      }
                      return $this->render('FaltasFaltasBundle:Default:asignacionActa.html.twig' , array('form' => $form->createview(),'estado'=>$estado,'inspector'=>$inspector));     
                  }else{
                    if($this->isGranted('ROLE_ACTASFAJAS_EDIT')==false){
                      $this->addFlash("error", "No tiene los permisos necesarios");   
                    }else{
                      $this->addFlash("error", "No se pudieron guardar los cambios");
                    }              
                  }
              } 
           return $this->render('FaltasFaltasBundle:Default:asignacionActa.html.twig' , array('form' => $form->createview(),'estado'=>$estado,'inspector'=>$inspector));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      } 
    }

    public function tablaAsignacionActasAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){ 
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignacionActa.yml"));

            //return new Response($yaml);
            
            $TablaUsuarios=new TablaAjax($request,$em,$yaml);
            $TablaUsuarios->setNativeQuery("SELECT a.id,a.serie,a.numero,b.fecha,e.estado,CONCAT(us.apellido,',',us.nombre) as inspector,e.estado,b.Id_Usuario_Creador FROM acta as a INNER JOIN acta_asignacion as b ON a.id = b.acta_id INNER JOIN acta_estado as e on a.estado_id = e.id INNER JOIN usuarios as us on us.id = b.inspector_id WHERE ");

            $TablaUsuarios->setSpecialConditions("");

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

    public function excelAsignacionActasAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){

        set_time_limit(0);       
        ini_set('max_execution_time', 0);
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){       
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'AsignacionActas'.date("d_m_Y").'.xls';
            $nombresheet="AsignacionActas";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignacionActa.yml"));

            //return new Response($yaml);
            
            $tablaAsignacion=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $tablaAsignacion->setNativeQuery("SELECT a.id,a.serie,a.numero,b.fecha,e.estado,CONCAT(us.apellido,',',us.nombre) as inspector,e.estado,b.Id_Usuario_Creador FROM acta as a INNER JOIN acta_asignacion as b ON a.id = b.acta_id INNER JOIN acta_estado as e on a.estado_id = e.id INNER JOIN usuarios as us on us.id = b.inspector_id WHERE ");
            $tablaAsignacion->setSpecialConditions("");

            $arraytablaAsignacion=$tablaAsignacion->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arraytablaAsignacion as $key => $value) {
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

    public function actasAsignadasAction(Request $request,$id)
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){  
          if ($id != '0'){
              $id=encriptador::mrcrypt_decrypt($id);
              $id=(int)$id;
          }else{
              $id=0;
          }
          
          $actasAsignadas=array();
          $asignacionActa = new AsignacionActa();
          $asignacionesActas = array();

          $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionActa');
          $asignacionFaja=$em->findOneByActa($id);
          if(!$asignacionFaja){
              
          }else{
              $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionActa');
              $asignacionesActas=$em->findNombreApellidobyIdActa($id); 

              for ($i=0; $i<=count($asignacionesActas)-1; $i++){
                $actasAsignadas[$i]=array();
                array_push($actasAsignadas[$i], $asignacionesActas[$i]["Nombre"]);
                array_push($actasAsignadas[$i], $asignacionesActas[$i]["fecha"]);
              }
          }


          $asignacionActa = new AsignacionActa();        
          
          $form = $this->createForm(ModalAsignacionActaType::class,$asignacionActa,array('method'=> 'POST', 'action'=>$this->generateUrl('faltas_faltas_actasasignadas', array('id' => encriptador::mrcrypt_encrypt($id)) )));

          if($request->isMethod('POST') ){
              $form->handleRequest($request);
              if ( $form->isValid() && $form->isSubmitted() && $this->isGranted('ROLE_ACTASFAJAS_EDIT') ) {
                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Acta');
                  $acta=$em->findOneById($id);

                  if (!$acta) {
                      throw $this->createNotFoundException('No se encontró el acta');
                  }

                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa');
                  $estadoActa=$em->findOneById(1);

                  $asignacionActa->setActa($acta);
                  $acta->setEstado($estadoActa);
                  $acta->addAsignacionActum($asignacionActa);   

                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionActa');
                  $existe = $em->findOneBy(array(
                      'fecha'=>$asignacionActa->getFecha(),
                      'acta'=>$acta->getId(),
                      'inspector'=>$asignacionActa->getInspector()->getId()
                  )); 

                  if(!$existe){
                      $em = $this->getDoctrine()->getManager();                
                      $em->persist($acta);
                      $em->persist($asignacionActa);
                      $em->flush();
                      $em->getConnection()->close();
                      $em->clear();
                  }else{
                      $em->clear();
                      $response = new Response(json_encode("REASIGNACION YA EXISTENTE"));                
                      $response->headers->set('Content-Type', 'application/json');
                      return $response;   
                  }
               
                
                  $response = new Response(json_encode("OK"));                
                  $response->headers->set('Content-Type', 'application/json');
                  return $response;
              }else{
                  $em->clear();
                  if($this->isGranted('ROLE_ACTASFAJAS_EDIT')==false){
                    $this->addFlash("error", "No tiene los permisos");   
                  }else{
                     return $this->render('FaltasFaltasBundle:Default:ModalAsignacionActa.html.twig' , array('form' => $form->createview(), 'arrayasignaciones'=> $actasAsignadas));       
                  }
              }
          } 

        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        } 
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }    

}
