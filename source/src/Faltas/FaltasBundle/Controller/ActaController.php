<?php

namespace Faltas\FaltasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Faltas\FaltasBundle\Entity\Acta;
use Faltas\FaltasBundle\Entity\ActaUtilizada;
use Usuario\UsuarioBundle\Entity\Usuarios;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Faltas\FaltasBundle\Form\RemitoActaType;
use Faltas\FaltasBundle\Form\RemitoActaTablaType;
use Faltas\FaltasBundle\Form\ActaType;
use Doctrine\ORM;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ActaController extends Controller
{
    public function tablaActasAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){        

        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){ 
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaActa.yml"));
            
            $TablaUsuarios=new TablaAjax($request,$em,$yaml);
            $TablaUsuarios->setNativeQuery("SELECT a.id,a.Serie,a.Numero,b.fecha,CONCAT(u.apellido,', ',u.nombre) as inspector,e.estado,o.checklist,o.id_sap,i.fecha_recepcion,i.fecha_inspeccion FROM acta as a INNER JOIN acta_asignacion as b on a.id = b.acta_id INNER JOIN usuarios as u on b.inspector_id = u.id INNER JOIN acta_estado as e on a.estado_id = e.id LEFT JOIN orden_inspeccion as o on a.id_inspeccion = o.id LEFT JOIN inspeccion as i on o.id = i.orden_inspeccion_id WHERE ");

            $TablaUsuarios->setSpecialConditions(" 1=1 ");

            $TablaUsuarios->setQueryCount("SELECT count(*) as count FROM acta as a INNER JOIN acta_asignacion as b on a.id = b.acta_id INNER JOIN usuarios as u on b.inspector_id = u.id INNER JOIN acta_estado as e on a.estado_id = e.id LEFT JOIN orden_inspeccion as o on a.id_inspeccion = o.id LEFT JOIN inspeccion as i on o.id = i.orden_inspeccion_id WHERE ");

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


    public function excelActasAction(Request $request)
    {
      $user=$this->getUser();

      if($user){

        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelActas'.date("d_m_Y").'.xls';
            $nombresheet="excelActas";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaActa.yml"));

            //return new Response($yaml);
            
            $TablaActas=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);    
            $TablaActas->setNativeQuery("SELECT a.id,a.Serie,a.Numero,b.fecha,CONCAT(u.apellido,',',u.nombre) as inspector,e.estado,o.checklist,o.id_sap,i.fecha_recepcion,i.fecha_inspeccion FROM acta as a INNER JOIN acta_asignacion as b on a.id = b.acta_id INNER JOIN usuarios as u on b.inspector_id = u.id INNER JOIN acta_estado as e on a.estado_id = e.id LEFT JOIN orden_inspeccion as o on a.id_inspeccion = o.id LEFT JOIN inspeccion as i on o.id = i.orden_inspeccion_id WHERE ");        
            $TablaActas->setSpecialConditions("");

            $arrayTablaCedulas=$TablaActas->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayTablaCedulas as $key => $value) {
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


    public function actasAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        
        if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){       
            $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa')->selectEstadosTabla();
            $inspector = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();      
            return $this->render('FaltasFaltasBundle:Default:Actas.html.twig', array('estado'=>$estado,'inspectores'=>$inspector));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }     
    }

    public function datosActasAction(Request $request,$id = '0')
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
       
          $Acta = new Acta();
          $ActaUtilizada=new ActaUtilizada();

              if ($id>0){
                   $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Acta');
                   $Acta = $repository->findOneByid($id);

                   if(!$Acta){
                       throw $this->createNotFoundException('No se encontro el acta');
                   }
              
                   $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:AsignacionActa');
                   $AsignacionActa = $repository->findOneByActa($Acta->getId());

                   $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaUtilizada');
                   $ActaUtilizada = $repository->findOneByActa($Acta->getid());
                
              }               
             
              $Acta->setActaUtilizada($ActaUtilizada);            
              $form = $this->createForm(ActaType::class,$Acta,array('method' => 'POST'));

              if($request->isMethod('POST') ){
                  $form->handleRequest($request);
                  if ($form->isValid() && $this->isGranted('ROLE_ACTASFAJAS_EDIT') ) {

                      if ( ($Acta->getActaUtilizada()->getSap()==null or $Acta->getActaUtilizada()->getSap()==0 )
                       and ($Acta->getActaUtilizada()->getChecklist()==null or $Acta->getActaUtilizada()->getChecklist()==0) ){
                         $this->addFlash("error", "Ingrese id Sap o Numero De checklist");  
                       }else{             
                          $repository = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');

                          if($Acta->getActaUtilizada()->getSap()==null or $Acta->getActaUtilizada()->getSap()==0){
                            $idsap = -1;
                          }else{
                            $idsap = $Acta->getActaUtilizada()->getSap();
                          }

                          if ($Acta->getActaUtilizada()->getChecklist()==null or $Acta->getActaUtilizada()->getChecklist()==0) {
                            $checklist=-1;
                          }else{
                            $checklist=$Acta->getActaUtilizada()->getChecklist();
                          }

                          $OrdenInspeccion = $repository->findOneByChecklist($checklist);    
                          if (!$OrdenInspeccion){
                              $OrdenInspeccion = $repository->findOneByIdSap($idsap); 
                              if(!$OrdenInspeccion){
                                $this->addFlash("error", "no hay inspeccion asociada al id sap o checklist asociado");  
                              }
                          }
                       
                          if($OrdenInspeccion){
                            $Acta->setOrdenInspeccion($OrdenInspeccion);

                            $ActaUtilizada= $Acta->getActaUtilizada();
                            $ActaUtilizada->setActa($Acta);

                            $em = $this->getDoctrine()->getManager();                    
                            $em->persist($Acta);


                            $em->persist($OrdenInspeccion);
                            $em->flush();
                            $em->getConnection()->close();
                            $em->clear();
                            $this->addFlash("success", "acta guardada con exito!!");
                           
                          }
                          return $this->render('FaltasFaltasBundle:Default:datosActa.html.twig' , array('form' => $form->createview(), 'inspector' => $AsignacionActa->getInspector()->getApellido().", ".$AsignacionActa->getInspector()->getNombre(), 'FechaAsignacion'=>$AsignacionActa->getFecha() ));
                       }


                  }else{
                    if($this->isGranted('ROLE_ACTASFAJAS_EDIT')==false){
                      $this->addFlash("error", "No tiene los permisos");   
                    }else{
                      $this->addFlash("error", "No se pudieron guardar los cambios");        
                    }
           
                  }
              }   
             // var_dump($AsignacionActa);
              return $this->render('FaltasFaltasBundle:Default:datosActa.html.twig' , array('form' => $form->createview(), 'inspector' => $AsignacionActa->getInspector()->getApellido().", ".$AsignacionActa->getInspector()->getNombre(), 'FechaAsignacion'=>$AsignacionActa->getFecha() ));
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
          }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      } 

    }

    public function remitoActasAction(Request $request){
       $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){
          $datos1 = array();
        
           $form = $this->createForm(RemitoActaType::class);

           if($request->isMethod('POST'))
           {
              $datos1 = $request->request->all();            
              $datos1 = $datos1['faltas_faltasbundle_remitoacta'];

              $form->handleRequest($request);
              if ($form->isValid()) {
                $datos = array();
                $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaUtilizada');

                $arrayActasUtilizadas = $em->findActaUtilizadaByFechaArea($datos1["fechaInicial"],$datos1["fechaFinal"],$datos1["areas"]);

                if(count($arrayActasUtilizadas) > 0){
                  $repository = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa');
                  $estado = $repository->findOneByid(4);

                  for ($i=0; $i < count($arrayActasUtilizadas) ; $i++) {
                    $id = $arrayActasUtilizadas[$i]->getActa()->getId();
                    $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Acta');
                    $arrayActasUtilizadas[$i] = $em->findOneById($id);
                    $arrayActasUtilizadas[$i]->setEstado($estado);
                  }

                  $form = $this->createCreateForm($datos,$arrayActasUtilizadas);                
                  
                  $em->clear();
                  return $this->render('FaltasFaltasBundle:Default:remitoActasTabla.html.twig' , array('form' => $form->createview() ));
                }else{
                  return new Response ("No se encontraron registros");
                }
              }
           }

          return $this->render('FaltasFaltasBundle:Default:remitoActas.html.twig', array('form' => $form->createview() ));
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
          }  
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }


    public function createCreateForm($datos, $arrayActasUtilizadas){
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){      
          $cantidad = count($arrayActasUtilizadas);

          $form = $this->createForm(RemitoActaTablaType::class,$datos,array(
            'rows' => $arrayActasUtilizadas,
            'action' => $this->generateUrl('faltas_faltas_renitoActas_saveboldt', array('cantidad' => $cantidad)),
            'method' => 'POST'
            ));

          return $form;
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }

    }    

    public function saveBoldtAction(Request $request, $cantidad){
      $user=$this->getUser();

      if($user){     
        if ($this->isGranted('ROLE_ACTASFAJAS_VIEW')){
          for ($i=0; $i <$cantidad ; $i++) { 
            $arrayActasUtilizadas[$i] = new Acta();
            $arrayActasUtilizadas[$i]->setActaUtilizada(new ActaUtilizada ());
          }

          $datos = array();
          $form = $this->createCreateForm($datos,$arrayActasUtilizadas);
          $form->handleRequest($request);


          if( $form->isValid() ){
              $em = $this->getDoctrine()->getManager();
              for ($i=0; $i <= $cantidad-1; $i++) {
                if ($form["Id".$i]->getData()){
                  if($form["estado".$i]->getData()->getId()==4){
                    $acta=new Acta();
                    $acta = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Acta')->findOneByid($form["Id".$i]->getData());
                    $acta->setEstado($form["estado".$i]->getData());                    
                    $em->persist($acta);
                    $em->flush();                    
                  }
                }
              }

              $em->getConnection()->close();
              $em->clear();

            $this->addFlash("success", "correcto");
            $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa')->selectEstadosTabla();
            $inspector = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
            $motivos = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaMotivo')->selectMotivosTabla();
            return $this->render('FaltasFaltasBundle:Default:Actas.html.twig', array('estado'=>$estado,'inspectores'=>$inspector,'Motivos'=>$motivos));
          }else{
            $this->addFlash("error", "error");
            $estado = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa')->selectEstadosTabla();
            $inspector = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
            $motivos = $this->getDoctrine()->getRepository('FaltasFaltasBundle:ActaMotivo')->selectMotivosTabla();
            return $this->render('FaltasFaltasBundle:Default:Actas.html.twig', array('estado'=>$estado,'inspectores'=>$inspector,'Motivos'=>$motivos));
          }
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }    
    }

    public function anularActaAction(Request $request,$id)
    {
      $user=$this->getUser();

      if($user){
        if ($this->isGranted('ROLE_ACTASFAJAS_EDIT')){    
          $id=encriptador::mrcrypt_decrypt($id);            
          

          $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:Acta');
          $acta=$em->findOneById($id);

          if(!$acta){
            $response = new Response(json_encode("El Acta NO EXISTE"));                
            $response->headers->set('Content-Type', 'application/json');
            return $response;
          }

          $em = $this->getDoctrine()->getRepository('FaltasFaltasBundle:EstadoActa');
          $estadoActa=$em->findOneById(3);

          $acta->setEstado($estadoActa);

          $em = $this->getDoctrine()->getManager();
          $em->persist($acta);
          $em->flush();
          $em->getConnection()->close();
          $em->clear();

          $response = new Response(json_encode("ANULADA"));                
          $response->headers->set('Content-Type', 'application/json');
          return $response;
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

}