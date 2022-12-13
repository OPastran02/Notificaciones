<?php

namespace Faltas\FaltasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Faltas\FaltasBundle\Entity\AsignacionFaja;
use Faltas\FaltasBundle\Entity\Faja;
use Faltas\FaltasBundle\Entity\EstadoFaja;
use Usuario\UsuarioBundle\Entity\Usuarios;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Faltas\FaltasBundle\Form\AsignacionFajaType;
use Faltas\FaltasBundle\Form\ModalAsignacionType;
use Doctrine\ORM;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AsignacionFajaController extends Controller
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

    public function asignacionFajaAction(Request $request,$id='0')
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
          
          $asignacionFaja = new AsignacionFaja();
          $faja = new Faja();        
          $estadoFaja = new EstadoFaja();
          $asignacionFaja->setFaja($faja);
          $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoFaja')->selectEstadosTabla();  

          $form = $this->createForm(AsignacionFajaType::class,$asignacionFaja,array('method' => 'POST'));

          if($request->isMethod('POST') ){
              $form->handleRequest($request);
              if ($form->isValid() && $this->isGranted('ROLE_ACTASFAJAS_EDIT')) {
                  $existe = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Faja')->existeFajaxNumero($asignacionFaja->getFaja()->getNumero());  
                  if(!$existe){
                      $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoFaja');
                      $estadoFaja=$em->findOneById(1);

                      $em = $this->getDoctrine()->getManager();
                      $faja->setEstado($estadoFaja);
                      
                      $em->persist($faja);
                      $em->persist($asignacionFaja);
                      $em->flush();
                      $em->getConnection()->close();
                      $em->clear();

                      $this->addFlash("success", "Asignación guardada con éxito");
                  }else{
                      $this->addFlash("error", "El número ingresado ya existe en la base de datos");
                  }            
                  return $this->render('FaltasFaltasBundle:Default:asignacionFaja.html.twig' , array('form' => $form->createview(),'estado'=>$estado));     
              }else{
                if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){ 
                  $this->addFlash("error", "No se pudieron guardar los cambios"); 
                }else{
                  $this->addFlash("error", "mo tiene los permisos necesarios");                     
                }              
              }
          }
          return $this->render('FaltasFaltasBundle:Default:asignacionFaja.html.twig' , array('form' => $form->createview(),'estado'=>$estado));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }       
    }

    public function tablaAsignacionFajaAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){      
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignacionFaja.yml"));

          //return new Response($yaml);
          
          $TablaUsuarios=new TablaAjax($request,$em,$yaml);
          
          $TablaUsuarios->setNativeQuery("SELECT f.id,f.numero,a.fecha_asignacion,concat(u.apellido,',',u.nombre) as inspector,e.estado FROM faja as f INNER JOIN faja_asignacion as a ON f.id = a.id_faja INNER JOIN usuarios as u on a.id_usuario_inspector_id = u.id INNER JOIN faja_estado as e on f.id_estado = e.id WHERE ");

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

    public function exceltablaAsignacionFajaAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){
        set_time_limit(0);       
        ini_set('max_execution_time', 0);
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){      
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'tablaAsignacionFaja'.date("d_m_Y").'.xls';
            $nombresheet="tablaAsignacionFaja";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignacionFaja.yml"));

            //var_dump($data["arraybusqueda"]);
         
            //return new Response(json_encode($data["arraybusqueda"]));
            
            $tablaAsignacion=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $tablaAsignacion->setNativeQuery("SELECT f.id,f.numero,a.fecha_asignacion,concat(u.apellido,',',u.nombre) as inspector,e.estado FROM faja as f INNER JOIN faja_asignacion as a ON f.id = a.id_faja INNER JOIN usuarios as u on a.id_usuario_inspector_id = u.id INNER JOIN faja_estado as e on f.id_estado = e.id WHERE ");
            
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


    public function fajasAsignadasAction(Request $request,$id)
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){      
          if ($id != '0'){
              $id=encriptador::mrcrypt_decrypt($id);
              $id=(int)$id;
          }else{
              $id=0;
          }
           
          $fajasAsignadas=array();
          $asignacionFaja = new AsignacionFaja();
          $asignacionesFajas = array();

          $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionFaja');
          $asignacionFaja=$em->findOneByIdFaja($id);

          if(!$asignacionFaja){
              
          }else{
              $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionFaja');
              $asignacionesFajas=$em->findNombreApellidobyIdFaja($id);
              
              for ($i=0; $i<=count($asignacionesFajas)-1; $i++){
                $fajasAsignadas[$i]=array();
                array_push($fajasAsignadas[$i], $asignacionesFajas[$i]["Nombre"]);
                array_push($fajasAsignadas[$i], $asignacionesFajas[$i]["fechaAsignacion"]);
              }
          }

          $asignacionFaja = new AsignacionFaja();        
          
          $form = $this->createForm(ModalAsignacionType::class,$asignacionFaja,array('method'=> 'POST'));

          if($request->isMethod('POST') ){
              $form->handleRequest($request);
              if ( $form->isValid() && $form->isSubmitted() ) {
                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Faja');
                  $faja=$em->findOneById($id);

                  if (!$faja) {
                      throw $this->createNotFoundException('No faja found');
                  }

                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoFaja');
                  $estadoFaja=$em->findOneById(1);
                  $asignacionFaja->setFaja($faja);
                  $faja->setEstado($estadoFaja);
                  $faja->addAsignacione($asignacionFaja);   

                  $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionFaja');
                  $existe = $em->findOneBy(array(
                      'fechaAsignacion'=>$asignacionFaja->getFechaAsignacion(),
                      'idFaja'=>$faja->getId(),
                      'idUsuarioInspector'=>$asignacionFaja->getIdUsuarioInspector()->getId()
                  )); 

                  if(!$existe){
                    $em = $this->getDoctrine()->getManager();                
                    $em->persist($faja);
                    $em->persist($asignacionFaja);
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
                return $this->render('FaltasFaltasBundle:Default:ModalAsignacion.html.twig' , array('form' => $form->createview(), 'arrayasignaciones'=> $fajasAsignadas)); 
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
