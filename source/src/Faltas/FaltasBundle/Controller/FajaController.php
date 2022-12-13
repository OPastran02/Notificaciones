<?php

namespace Faltas\FaltasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Faltas\FaltasBundle\Entity\Faja;
use Usuario\UsuarioBundle\Entity\Usuarios;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Faltas\FaltasBundle\Form\FajaType;
use Doctrine\ORM;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FajaController extends Controller
{
    public function indexAction()
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){ 
           return $this->render('FaltasFaltasBundle:Default:index.html.twig', array('form' => $form->createview(),'estado'=>$estado,'inspector'=>$inspector));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }    
    }

    public function datosFajaAction(Request $request,$id = '0')
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
       
          $Faja = new Faja();

          if ($id>0){
            $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Faja');
            $Faja = $repository->findOneByid($id);

            if(!$Faja){
                throw $this->createNotFoundException('No se encontro la Faja');
            }
        
            $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionFaja');
            $AsignacionFaja = $repository->findOneByFaja($Faja->getid()); 
          }               

          $form = $this->createForm(FajaType::class,$Faja,array('method' => 'POST'));

          if($request->isMethod('POST') ){
              $form->handleRequest($request);
              if ($form->isValid() && $this->isGranted('ROLE_ACTASFAJAS_EDIT')) {

                  if ( ($Faja->getIdSap()==null or $Faja->getIdSap()==0 ) and ($Faja->getChecklist()==null or $Faja->getChecklist()==0) ){
                     $this->addFlash("error", "Ingrese id Sap o Numero De checklist");  
                   }else{             
                      $repository = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');

                      if($Faja->getIdSap()==null or $Faja->getIdSap()==0){
                        $idsap = -1;
                      }else{
                        $idsap = $Faja->getIdSap();
                      }

                      if ($Faja->getChecklist()==null or $Faja->getChecklist()==0) {
                        $checklist=-1;
                      }else{
                        $checklist=$Faja->getChecklist();
                      }

                      $OrdenInspeccion = $repository->findOneByChecklist($checklist);    
                      if (!$OrdenInspeccion){
                          $OrdenInspeccion = $repository->findOneByIdSap($idsap); 
                          if(!$OrdenInspeccion){
                            $this->addFlash("error", "no hay inspeccion asociada al id sap o checklist asociado");  
                          }
                      }

                      if($OrdenInspeccion){
                        $Faja->setOrdenInspeccion($OrdenInspeccion);

                        $em = $this->getDoctrine()->getManager();                    
                        $em->persist($Faja);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $this->addFlash("success", "Usuario guardado con éxito");
                       
                      }
                      return $this->render('FaltasFaltasBundle:Default:datos.html.twig' , array('form' => $form->createview(), 'inspector' => $AsignacionFaja->getIdUsuarioInspector()->getApellido().", ".$AsignacionFaja->getIdUsuarioInspector()->getNombre(), 'FechaAsignacion'=>$AsignacionFaja->getFechaAsignacion() ));
                   }

              }else{

                if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){ 
                  $this->addFlash("error", "No se pudieron guardar los cambios"); 
                }else{
                  $this->addFlash("error", "mo tiene los permisos necesarios");                     
                }               
              }
          }    

          return $this->render('FaltasFaltasBundle:Default:datos.html.twig' , array('form' => $form->createview(), 'inspector' => $AsignacionFaja->getIdUsuarioInspector()->getApellido().", ".$AsignacionFaja->getIdUsuarioInspector()->getNombre(), 'FechaAsignacion'=>$AsignacionFaja->getFechaAsignacion() ));
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
          }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

    public function tablaFajasAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){     
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaFaja.yml"));

          //return new Response($yaml);
          
          $TablaUsuarios=new TablaAjax($request,$em,$yaml);
          $TablaUsuarios->setNativeQuery("SELECT f.id,f.numero,f.fecha_inspeccion,f.checklist,a.fecha_asignacion,concat(u.apellido,',',u.nombre) as inspector,e.estado FROM faja as f INNER JOIN faja_asignacion as a ON f.id = a.id_faja INNER JOIN usuarios as u on a.id_usuario_inspector_id = u.id INNER JOIN faja_estado as e on f.id_estado = e.id WHERE ");

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

    public function excelFajasAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        set_time_limit(0);       
        ini_set('max_execution_time', 0);
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){    
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'Fajas'.date("d_m_Y").'.xls';
            $nombresheet="Fajas";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaFaja.yml"));
            
            $TablaFajas=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaFajas->setNativeQuery("SELECT f.id,f.numero,f.fecha_inspeccion,f.checklist,a.fecha_asignacion,concat(u.apellido,',',u.nombre) as inspector,e.estado FROM faja as f INNER JOIN faja_asignacion as a ON f.id = a.id_faja INNER JOIN usuarios as u on a.id_usuario_inspector_id = u.id INNER JOIN faja_estado as e on f.id_estado = e.id WHERE ");
            $TablaFajas->setSpecialConditions("");

            $arrayTablaFajas=$TablaFajas->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayTablaFajas as $key => $value) {
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

    public function FajasAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){    
            $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoFaja')->selectEstadosTabla();
            $inspector = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
            return $this->render('FaltasFaltasBundle:Default:Fajas.html.twig', array('estado'=>$estado,'inspector'=>$inspector));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }     
    }
           

}
