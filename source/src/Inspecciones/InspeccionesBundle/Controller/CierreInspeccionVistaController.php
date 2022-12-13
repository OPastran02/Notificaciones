<?php

namespace Inspecciones\InspeccionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use CoreBundle\Logic\UsigWS;
use CoreBundle\Logic\JsonValidator;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Establecimiento\EstablecimientoBundle\Entity\Estado;
use Establecimiento\EstablecimientoBundle\Entity\RubroPrincipal;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Inspecciones\InspeccionesBundle\Entity\Inspeccion;
use Inspecciones\InspeccionesBundle\Entity\Circuito;
use Usuario\UsuarioBundle\Entity\Usuarios;

use Inspecciones\InspeccionesBundle\Form\AnularInspeccionType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionAsignacionType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionCierreVinculadoType;
use Inspecciones\InspeccionesBundle\Form\EditInspeccionCierreDesvinculadoType;

use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CierreInspeccionVistaController extends Controller
{

  public function pageAsignacionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();
          return $this->render('InspeccionesInspeccionesBundle:Default:asignacionCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,            
              ));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

    public function tablaAsignacionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignarCheckList.yml"));        
           
          $TablaAsignacion=new TablaAjax($request,$em,$yaml,null,$checker);
          $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,a.area, group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores, CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion,CASE WHEN inspeccionPorTablet = 1 THEN "SI" ELSE "NO" END as inspeccionPorTablet FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id and i.id = (SELECT max(id) from inspeccion where orden_inspeccion_id = o.id) INNER JOIN inspeccion_usuario as iu on i.id=iu.inspeccion_id INNER JOIN usuarios as u on iu.usuario_id = u.id INNER JOIN circuito as c on o.circuito_id = c.id INNER JOIN area as a on o.area_id = a.id WHERE (o.checklist is null or o.checklist = 0) and (o.eliminada is null or o.eliminada = 0) and (o.anulada = 0 or o.anulada is null) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,a.area,Direccion HAVING ');        
          
          $idArea = $this->getUser()->getArea()->getId();

          if($idArea != 7){
            $TablaAsignacion->setSpecialConditions("area = '".$this->getUser()->getArea()->getArea()."'");
          }else{
            $TablaAsignacion->setSpecialConditions("1 = 1");
          }        

          $response = new Response($TablaAsignacion->Initialize());
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

    public function excelAsignacionAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelAsignacion'.date("d_m_Y").'.xls';
            $nombresheet="excelAsignacion";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAsignarCheckList.yml"));

            //return new Response($yaml);
            
            $TablaAsignacion=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,i.fecha_programado,mot.motivo,c.circuito,a.area, group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores, REPLACE(CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")),"<br>","") as Direccion,fnOrdenInspeccionDenunciantes(o.id) as Denunciante,fnEstablecimientoActuaciones(o.establecimiento_id) as Actuaciones,CASE WHEN o.establecimiento_id IS NULL THEN fnOrdenInspeccionComuna(o.id) ELSE fnEstablecimientoComuna(o.establecimiento_id) END  as Comuna, o.observaciones, CASE o.inspeccionPorTablet WHEN 1 THEN "SI" ELSE "NO" END AS Tablet FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id and i.id = (SELECT max(id) from inspeccion where orden_inspeccion_id = o.id) INNER JOIN inspeccion_usuario as iu on i.id=iu.inspeccion_id INNER JOIN usuarios as u on iu.usuario_id = u.id INNER JOIN circuito as c on o.circuito_id = c.id INNER JOIN motivo_inspeccion as mot on mot.id = o.motivo_inspeccion_id INNER JOIN area as a on o.area_id = a.id WHERE (o.checklist is null or o.checklist = 0) and (o.eliminada is null or o.eliminada = 0) and (o.anulada = 0 or o.anulada is null) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,a.area,Direccion HAVING ');        
            
            $idArea = $this->getUser()->getArea()->getId();

            if($idArea != 7){
              $TablaAsignacion->setSpecialConditions("area = '".$this->getUser()->getArea()->getArea()."'");
            }else{
              $TablaAsignacion->setSpecialConditions("1 = 1");
            } 


            $arraytablaAsignacion=$TablaAsignacion->getQueryTable();
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

    public function pageAutorizacionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $motivos = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:MotivoInspeccion')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();
          return $this->render('InspeccionesInspeccionesBundle:Default:autorizacionCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,
              'motivos'=>$motivos,
              ));
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }
    ///quitar id sap, agregar razon social y rubro.
    public function tablaAutorizacionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAutorizacionCheckList.yml"));        
           
          $TablaAsignacion=new TablaAjax($request,$em,$yaml,null,$checker);
          $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,m.motivo,a.area, group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores, CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion,fnOrdenInspeccionDenunciantes(o.id) as Denunciante FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id and i.id = (SELECT max(id) from inspeccion where orden_inspeccion_id = o.id) INNER JOIN inspeccion_usuario as iu on i.id=iu.inspeccion_id INNER JOIN usuarios as u on iu.usuario_id = u.id INNER JOIN motivo_inspeccion as m on o.motivo_inspeccion_id = m.id INNER JOIN circuito as c on o.circuito_id = c.id INNER JOIN area as a on o.area_id = a.id WHERE (o.autorizacion is null or o.autorizacion = 0) and (o.checklist is not null or o.checklist != 0) and (o.eliminada is null or o.eliminada = 0) and (o.anulada = 0 or o.anulada is null) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,a.area,Direccion HAVING ');        
          
          $idArea = $this->getUser()->getArea()->getId();

          if($idArea != 7){
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
            $TablaAsignacion->setSpecialConditions("area = '".$this->getUser()->getArea()->getArea()."'");
          }else{
            $TablaAsignacion->setSpecialConditions("1 = 1");
          }        

          $response = new Response($TablaAsignacion->Initialize());
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

    public function excelAutorizacionAction(Request $request)
    { 
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelAsignacion'.date("d_m_Y").'.xls';
            $nombresheet="excelAsignacion";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAutorizacionCheckList.yml"));

            //return new Response($yaml);
            
            $TablaAsignacion=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,m.motivo,a.area, group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores, REPLACE(CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")),"<br>","") as Direccion,fnOrdenInspeccionDenunciantes(o.id) as Denunciante,fnEstablecimientoActuaciones(o.establecimiento_id) as Actuaciones,CASE WHEN o.establecimiento_id IS NULL THEN fnOrdenInspeccionComuna(o.id) ELSE fnEstablecimientoComuna(o.establecimiento_id) END  as Comuna, o.observaciones FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id and i.id = (SELECT max(id) from inspeccion where orden_inspeccion_id = o.id) INNER JOIN inspeccion_usuario as iu on i.id=iu.inspeccion_id INNER JOIN usuarios as u on iu.usuario_id = u.id INNER JOIN motivo_inspeccion as m on o.motivo_inspeccion_id = m.id INNER JOIN circuito as c on o.circuito_id = c.id INNER JOIN area as a on o.area_id = a.id WHERE (o.checklist is null or o.checklist = 0) and (o.eliminada is null or o.eliminada = 0) and (o.anulada = 0 or o.anulada is null) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,i.fecha_programado,c.circuito,a.area,Direccion HAVING ');        
            
            $idArea = $this->getUser()->getArea()->getId();

            if($idArea != 7){
              $TablaAsignacion->setSpecialConditions("area = '".$this->getUser()->getArea()->getArea()."'");
            }else{
              $TablaAsignacion->setSpecialConditions("1 = 1");
            } 


            $arraytablaAsignacion=$TablaAsignacion->getQueryTable();
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

    public function pageCierreAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){ 
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();
          return $this->render('InspeccionesInspeccionesBundle:Default:cierreCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,            
              ));
         }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }    
    }

    public function tablaCierreAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){          
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaCierreCheckList.yml"));        
           
          $TablaCierre=new TablaAjax($request,$em,$yaml,null,$checker);
          $TablaCierre->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,c.circuito,a.area,group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores,o.Fecha_Inspeccion_Completa,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion,CASE WHEN inspeccionPorTablet = 1 THEN "SI" ELSE "NO" END as inspeccionPorTablet,CASE o.revisionTablet WHEN 1 THEN "SI" ELSE "NO" end as Revision FROM orden_inspeccion AS o INNER JOIN circuito AS c on o.circuito_id = c.id INNER JOIN area AS a on o.area_id = a.id INNER JOIN inspeccion AS i on o.id = i.orden_inspeccion_id LEFT JOIN inspeccion_usuario AS iu on i.id=iu.inspeccion_id LEFT JOIN usuarios AS u on iu.usuario_id = u.id WHERE  o.checklist > 0 and (o.eliminada is null or o.eliminada = 0) AND (o.anulada = 0 or o.anulada is null) AND (i.fecha_inspeccion is null OR o.establecimiento_id is null or o.establecimiento_id = 0) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,c.circuito,a.area,o.Fecha_Inspeccion_Completa,Direccion HAVING ');
          
          $TablaCierre->setSpecialConditions("1 = 1");

          $response = new Response($TablaCierre->Initialize());
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

    public function excelCierreAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){           
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelCierre'.date("d_m_Y").'.xls';
            $nombresheet="excelCierre";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaCierreCheckList.yml"));

            //return new Response($yaml);
            
            $TablaCierre=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaCierre->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,c.circuito,a.area,group_concat(concat(u.apellido,", ",u.nombre)) as Inspectores,fnCantidadInspeccionesPorOrden(o.id) as CantindadInspecciones,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion FROM orden_inspeccion AS o INNER JOIN circuito AS c on o.circuito_id = c.id INNER JOIN area AS a on o.area_id = a.id INNER JOIN inspeccion AS i on o.id = i.orden_inspeccion_id LEFT JOIN inspeccion_usuario AS iu on i.id=iu.inspeccion_id LEFT JOIN usuarios AS u on iu.usuario_id = u.id WHERE  o.checklist > 0 and (o.eliminada is null or o.eliminada = 0) AND (o.anulada = 0 or o.anulada is null) AND (i.fecha_inspeccion is null OR o.establecimiento_id is null or o.establecimiento_id = 0) and id_sap is not null GROUP BY o.id,o.id_sap,o.checklist,c.circuito,a.area,Direccion HAVING ');  
            $TablaCierre->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaCierre->getQueryTable();
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

    public function pageFinalAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){        
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();

          return $this->render('InspeccionesInspeccionesBundle:Default:finalCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,            
              ));
         }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
         }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function tablaFinalAction(Request $request){
      $user=$this->getUser();
      set_time_limit(0);           
      ini_set('max_execution_time', 0);

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){        
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaFinalCheckList.yml"));        
             
            $TablaFinal=new TablaAjax($request,$em,$yaml,null,$checker);        
            $TablaFinal->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,o.circuito as circuito_id,o.area as area_id, o.Inspectores, o.fecha_inspeccion as FechaInspeccion,o.Direcciones as Direccion FROM view_inspecciones as o WHERE o.checklist > 0  AND (o.anulada = 0 or o.anulada is null) and o.fecha_inspeccion is not null and o.establecimiento_id is not null and ');
            //$TablaFinal->setQueryCount('SELECT count(*) as count FROM orden_inspeccion AS o INNER JOIN circuito AS c on o.circuito_id = c.id INNER JOIN area AS a on o.area_id = a.id WHERE o.checklist > 0 and (o.eliminada is null or o.eliminada = 0) AND (o.anulada = 0 or o.anulada is null) AND fnCantidadInspeccionesPorOrdenNulas(o.id) = 0 and o.establecimiento_id is not null and ');
            
            $TablaFinal->setSpecialConditions("1 = 1");

            $response = new Response($TablaFinal->Initialize());
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

    public function excelFinalAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){        
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelFinal'.date("d_m_Y").'.xls';
            $nombresheet="excelFinal";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaFinalCheckList.yml"));

            //return new Response($yaml);
            
            $TablaFinal=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaFinal->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,o.circuito as circuito_id,o.area as area_id, o.Inspectores, o.fecha_inspeccion as FechaInspeccion,o.Direcciones as Direccion FROM view_inspecciones as o WHERE o.checklist > 0  AND (o.anulada = 0 or o.anulada is null) and o.fecha_inspeccion is not null and o.establecimiento_id is not null and ');
              
            $TablaFinal->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaFinal->getQueryTable();
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

    public function pageAnuladosAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){     
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();

          return $this->render('InspeccionesInspeccionesBundle:Default:anuladosCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,            
              ));
         }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
         }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function tablaAnuladosAction(Request $request){  
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){         
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAnuladosCheckList.yml"));        
           
          $TablaAsignacion=new TablaAjax($request,$em,$yaml,null,$checker);
          $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,c.circuito as circuito_id,a.area as area_id,fnUltimosInspectoresPorOrden(o.id) as Inspectores,fnUltimaFechaInspeccionPorOrden(o.id) as FechaInspeccion,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""), COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion FROM orden_inspeccion AS o INNER JOIN circuito AS c on o.circuito_id = c.id INNER JOIN area AS a on o.area_id = a.id WHERE ');
          
          
          $TablaAsignacion->setSpecialConditions("o.anulada = 1");

          $response = new Response($TablaAsignacion->Initialize());
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


    public function excelAnuladosAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){       
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelAnulados'.date("d_m_Y").'.xls';
            $nombresheet="excelAnulados";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaAnuladosCheckList.yml"));

            //return new Response($yaml);
            
            $TablaAsignacion=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaAsignacion->setNativeQuery('SELECT o.id,o.id_sap,o.checklist,c.circuito as circuito_id,a.area as area_id,fnUltimosInspectoresPorOrden(o.id) as Inspectores,fnUltimaFechaInspeccionPorOrden(o.id) as FechaInspeccion,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""), COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion FROM orden_inspeccion AS o INNER JOIN circuito AS c on o.circuito_id = c.id INNER JOIN area AS a on o.area_id = a.id WHERE ');
              
            $TablaAsignacion->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaAsignacion->getQueryTable();
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

    public function pageInspeccionesPendientesAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          
          return $this->render('InspeccionesInspeccionesBundle:Default:controlInspecciones.html.twig');
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

    public function inspeccionesPendientesAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          set_time_limit(0); 
          ignore_user_abort(true);
          ini_set('max_execution_time', 0);
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspeccionesPendientes.yml"));        
           
          $TablaControl=new TablaAjax($request,$em,$yaml,null,$checker);
          if($this->getUser()->getArea()->getId() != 7){
            $area = ' and o.area_id = '.$this->getUser()->getArea()->getId();
          }else{
            $area = '';
          }          
          $hoy = date("Y-m-d");
          //$principioMes = strtotime ( '-'.(int)date("d")-1.' day' , strtotime ( $hoy ) ) ;
          $hace30Dias = strtotime ( '-30 day' , strtotime ( $hoy ) ) ;
          $hace30Dias = date ( 'Y-m-d' , $hace30Dias );
          
          $dosSemanasDespues = strtotime ( '+14 day' , strtotime ( $hoy ) ) ;
          $dosSemanasDespues = date ( 'Y-m-d' , $dosSemanasDespues );

          $TablaControl->setNativeQuery('SELECT o.establecimiento_id,o.id,o.checklist,o.reinspeccionar,m.motivo,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion,GROUP_CONCAT(CONCAT(u.apellido,", ",u.nombre)) as Inspectores,i.fecha_programado,i.fecha_inspeccion, CASE WHEN o.checklist is null THEN "PROGRAMADA" WHEN o.Primer_Fecha_Programado < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND i.fecha_inspeccion IS NULL THEN "ANULAR" WHEN i.fecha_programado < DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND i.fecha_inspeccion IS NULL THEN "VENCIDA" WHEN i.fecha_inspeccion IS NOT NULL AND o.ifGra IS NULL THEN "FIRMAR" WHEN i.fecha_inspeccion IS NOT NULL AND o.ifGra IS NOT NULL THEN "COMPLETA" WHEN i.fecha_inspeccion IS NULL AND o.completa = 1 THEN "VIGENTE-GOFA"    WHEN o.inspeccionPorTablet = 1 THEN "VIGENTE-TABLET" ELSE "VIGENTE-PAPEL" END AS "estado",o.ifGra,(SELECT CASE irr.respuesta_id WHEN 1 THEN "SI"  ELSE "NO" END AS Respuesta FROM inspecciones_resultados as iir INNER JOIN inspecciones_respuestas as irr ON iir.id = irr.resultado_id WHERE pregunta_id = 355 and grupo_id = 18 and orden_inspeccion_id = o.id ) AS Intimo,CASE WHEN inspeccionPorTablet = 1 THEN "SI" ELSE "NO" END as inspeccionPorTablet,o.vinculado,CASE o.vinculado WHEN 1 THEN "checked" ELSE "unchecked" END AS "checked", CASE WHEN au.id > 0 THEN "SI" ELSE "NO" END AS Acta FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id INNER JOIN motivo_inspeccion as m on m.id = o.motivo_inspeccion_id LEFT JOIN inspeccion_usuario AS iu on i.id=iu.inspeccion_id LEFT JOIN usuarios AS u on iu.usuario_id = u.id LEFT JOIN acta as a on a.id_inspeccion = o.id LEFT JOIN acta_utilizada as au on a.id = au.id where (o.anulada is null or o.anulada = 0) and (o.eliminada is null or o.eliminada = 0) and (o.Primer_Fecha_Programado between "'.$hace30Dias.'" and "'.$dosSemanasDespues.'" or i.fecha_programado between "'.$hace30Dias.'" and "'.$dosSemanasDespues.'" OR (o.vinculado is null or o.vinculado = 0)) '.$area.' GROUP BY o.id,o.checklist , Direccion,i.fecha_programado,i.fecha_inspeccion HAVING ');
          
          $TablaControl->setSpecialConditions("1 = 1");
          

          $response = new Response($TablaControl->Initialize());
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

    public function excelInspeccionesPendientesAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);       

            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelPendientes'.date("d_m_Y").'.xls';
            $nombresheet="excelPendientes";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspeccionesPendientes.yml"));            
            
            $TablaControl=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            if($this->getUser()->getArea()->getId() != 7){
              $area = ' and o.area_id = '.$this->getUser()->getArea()->getId();
            }else{
              $area = '';
            }          
            $hoy = date("Y-m-d");
            //$principioMes = strtotime ( '-'.(int)date("d")-1.' day' , strtotime ( $hoy ) ) ;
            $hace30Dias = strtotime ( '-30 day' , strtotime ( $hoy ) ) ;
            $hace30Dias = date ( 'Y-m-d' , $hace30Dias );

            $dosSemanasDespues = strtotime ( '+14 day' , strtotime ( $hoy ) ) ;
            $dosSemanasDespues = date ( 'Y-m-d' , $dosSemanasDespues );

            $TablaControl->setNativeQuery('SELECT o.establecimiento_id,o.id,o.checklist,o.autorizacion,o.reinspeccionar,CONCAT(COALESCE(fnEstablecimientoDomicilios(o.establecimiento_id),""),COALESCE(fnOrdenInspeccionDomiciliosProvisorios(o.id),"")) as Direccion,GROUP_CONCAT(CONCAT(u.apellido,", ",u.nombre)) as Inspectores,i.fecha_programado,i.fecha_inspeccion, CASE WHEN o.checklist is null THEN "PROGRAMADA" WHEN o.Primer_Fecha_Programado < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND i.fecha_inspeccion IS NULL THEN "ANULAR" WHEN i.fecha_programado < DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND i.fecha_inspeccion IS NULL THEN "VENCIDA" WHEN i.fecha_inspeccion IS NOT NULL AND o.ifGra IS NULL THEN "FIRMAR" WHEN i.fecha_inspeccion IS NOT NULL AND o.ifGra IS NOT NULL THEN "COMPLETA" WHEN i.fecha_inspeccion IS NULL AND o.completa = 1 THEN "VIGENTE-GOFA"    WHEN o.inspeccionPorTablet = 1 THEN "VIGENTE-TABLET" ELSE "VIGENTE-PAPEL" END AS "estado",o.ifGra,(SELECT CASE irr.respuesta_id WHEN 1 THEN "SI"  ELSE "NO" END AS Respuesta FROM inspecciones_resultados as iir INNER JOIN inspecciones_respuestas as irr ON iir.id = irr.resultado_id WHERE pregunta_id = 355 and grupo_id = 18 and orden_inspeccion_id = o.id ) AS Intimo,CASE WHEN inspeccionPorTablet = 1 THEN "SI" ELSE "NO" END as inspeccionPorTablet,o.vinculado,CASE o.vinculado WHEN 1 THEN "checked" ELSE "unchecked" END AS "checked" FROM orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id LEFT JOIN inspeccion_usuario AS iu on i.id=iu.inspeccion_id LEFT JOIN usuarios AS u on iu.usuario_id = u.id where (o.anulada is null or o.anulada = 0) and (o.eliminada is null or o.eliminada = 0) and (o.Primer_Fecha_Programado between "'.$hace30Dias.'" and "'.$dosSemanasDespues.'" or i.fecha_programado between "'.$hace30Dias.'" and "'.$dosSemanasDespues.'" OR (o.vinculado is null or o.vinculado = 0)) '.$area.' GROUP BY o.id,o.checklist , Direccion,i.fecha_programado,i.fecha_inspeccion HAVING ');
            
            $TablaControl->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaControl->getQueryTable();
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

    public function pageReInspeccionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();
             
          $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->findall();
          $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
          $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->findall();

          return $this->render('InspeccionesInspeccionesBundle:Default:reInspeccionCheckList.html.twig', array(
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,            
              ));
         
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function tablaReInspeccionAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

            $idArea = $user->getArea()->getId();
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $idArea = $user->getArea()->getId();
            if($idArea == 7){
              $extra = "( ( (m.gofa is null or m.gofa = 0) and desestimar_reinspeccion = 0 ) or (m.gofa = 1 and desestimar_reinspeccion = 0 and o.area_id = 7 ) )";
            }else{
              $extra = "m.gofa = 1 and desestimar_reinspeccion = 0 and o.area_id =".$idArea;
            }

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaReInspeccionarCheckList.yml"));
             
            $TablaFinal=new TablaAjax($request,$em,$yaml,null,$checker);        
            $TablaFinal->setNativeQuery('SELECT m.id,o.id_sap,o.checklist,c.circuito AS circuito_id,a.area AS area_id,    FNULTIMOSINSPECTORESPORORDEN(o.id) AS Inspectores,FNULTIMAFECHAINSPECCIONPORORDEN(o.id) AS FechaInspeccion,CONCAT(COALESCE(FNESTABLECIMIENTODOMICILIOS(o.establecimiento_id),""),COALESCE(FNORDENINSPECCIONDOMICILIOSPROVISORIOS(o.id),"")) AS Direccion FROM orden_inspeccion AS o INNER JOIN circuito AS c ON o.circuito_id = c.id INNER JOIN area AS a ON o.area_id = a.id INNER JOIN motivos_re_inspeccion as m on m.orden_inspeccion_id = o.id and m.id = (SELECT max(id) FROM motivos_re_inspeccion WHERE orden_inspeccion_id = o.id) WHERE o.reinspeccionar = 1 AND o.checklist > 0 AND (o.eliminada IS NULL OR o.eliminada = 0) AND (o.anulada = 0 OR o.anulada IS NULL) AND (m.desestimar_reinspeccion = 0 OR m.desestimar_reinspeccion IS NULL) and '.$extra.' and ');
            $TablaFinal->setQueryCount('SELECT COUNT(*) AS count FROM orden_inspeccion AS o INNER JOIN circuito AS c ON o.circuito_id = c.id INNER JOIN area AS a ON o.area_id = a.id INNER JOIN motivos_re_inspeccion as m on m.orden_inspeccion_id = o.id and m.id = (SELECT max(id) FROM motivos_re_inspeccion WHERE orden_inspeccion_id = o.id) WHERE o.reinspeccionar = 1 AND o.checklist > 0 AND (o.eliminada IS NULL OR o.eliminada = 0) AND (o.anulada = 0 OR o.anulada IS NULL) AND (m.desestimar_reinspeccion = 0 OR m.desestimar_reinspeccion IS NULL) and '.$extra.' and  ');
            
            
            $TablaFinal->setSpecialConditions("1 = 1");

            $response = new Response($TablaFinal->Initialize());
            $response->headers->set('Content-Type', 'application/json');
            $em->clear();
            return $response;
          
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function excelReInspeccionAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

            $idArea = $user->getArea()->getId();
            if($idArea == 7){
              $extra = "( ( (m.gofa is null or m.gofa = 0) and desestimar_reinspeccion = 0 ) or (m.gofa = 1 and desestimar_reinspeccion = 0 and o.area_id = 7 ) )";
            }else{
              $extra = "m.gofa = 1 and desestimar_reinspeccion = 0 and o.area_id =".$idArea;
            }
              
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelReInspeccion'.date("d_m_Y").'.xls';
            $nombresheet="excelReInspeccion";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaFinalCheckList.yml"));

            //return new Response($yaml);
            
            $TablaFinal=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaFinal->setNativeQuery('SELECT m.id,o.id_sap,o.checklist,c.circuito AS circuito_id,a.area AS area_id,    FNULTIMOSINSPECTORESPORORDEN(o.id) AS Inspectores,FNULTIMAFECHAINSPECCIONPORORDEN(o.id) AS FechaInspeccion,CONCAT(COALESCE(FNESTABLECIMIENTODOMICILIOS(o.establecimiento_id),""),COALESCE(FNORDENINSPECCIONDOMICILIOSPROVISORIOS(o.id),"")) AS Direccion FROM orden_inspeccion AS o INNER JOIN circuito AS c ON o.circuito_id = c.id INNER JOIN area AS a ON o.area_id = a.id INNER JOIN motivos_re_inspeccion as m on m.orden_inspeccion_id = o.id and m.id = (SELECT max(id) FROM motivos_re_inspeccion WHERE orden_inspeccion_id = o.id) WHERE o.reinspeccionar = 1 AND o.checklist > 0 AND (o.eliminada IS NULL OR o.eliminada = 0) AND (o.anulada = 0 OR o.anulada IS NULL) AND (m.desestimar_reinspeccion = 0 OR m.desestimar_reinspeccion IS NULL) and '.$extra.' and ');
              
            $TablaFinal->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaFinal->getQueryTable();
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
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }              
    }

    public function pageInspectoresSadeAction(Request $request){
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          
          return $this->render('InspeccionesInspeccionesBundle:Default:inspectoresSade.html.twig');
        }else{
          return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }  
    }

    public function inspectoresSadeAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){ 
          set_time_limit(0); 
          ignore_user_abort(true);
          ini_set('max_execution_time', 0);
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspectoresSade.yml"));        
           
          $TablaControl=new TablaAjax($request,$em,$yaml,null,$checker);          

          $TablaControl->setNativeQuery("SELECT o.id,o.checklist,CONCAT(COALESCE(FNESTABLECIMIENTODOMICILIOS(o.establecimiento_id),''),COALESCE(FNORDENINSPECCIONDOMICILIOSPROVISORIOS(o.id),'')) AS Direccion,i.fecha_programado,i.fecha_inspeccion FROM orden_inspeccion AS o INNER JOIN inspeccion AS i ON o.id = i.orden_inspeccion_id INNER JOIN inspeccion_usuario AS iu ON i.id = iu.inspeccion_id INNER JOIN usuarios AS u ON iu.usuario_id = u.id WHERE o.inspeccionPorTablet = 1 AND (o.anulada IS NULL OR o.anulada = 0) AND o.checklist > 0 AND (o.eliminada IS NULL OR o.eliminada = 0) AND i.fecha_inspeccion is not null AND ifGra is null and u.id = ".$user->getId()." AND (o.vinculado is null or o.vinculado = 0) AND " );
          
          $TablaControl->setSpecialConditions("1 = 1");
          

          $response = new Response($TablaControl->Initialize());
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

    public function excelInspectoresSadeAction(Request $request)
    {
      $user=$this->getUser();

      if($user){
        $checker=$this->getUser()->getRoles();

        if ($this->isGranted('ROLE_REPORTEDIARIO_ADMIN')){
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);       

            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelInspectoresSade'.date("d_m_Y").'.xls';
            $nombresheet="excelInspectoresSade";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspectoresSade.yml"));            
            
            $TablaControl=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);            

            $TablaControl->setNativeQuery("SELECT o.id,o.checklist,CONCAT(COALESCE(FNESTABLECIMIENTODOMICILIOS(o.establecimiento_id),''),COALESCE(FNORDENINSPECCIONDOMICILIOSPROVISORIOS(o.id),'')) AS Direccion,i.fecha_programado,i.fecha_inspeccion FROM orden_inspeccion AS o INNER JOIN inspeccion AS i ON o.id = i.orden_inspeccion_id INNER JOIN inspeccion_usuario AS iu ON i.id = iu.inspeccion_id INNER JOIN usuarios AS u ON iu.usuario_id = u.id WHERE o.inspeccionPorTablet = 1 AND (o.anulada IS NULL OR o.anulada = 0) AND o.checklist > 0 AND (o.eliminada IS NULL OR o.eliminada = 0) AND i.fecha_inspeccion is not null AND ifGra is null and u.id = ".$this->getUser()->getId()." AND");          
            
            $TablaControl->setSpecialConditions("1 = 1");

            $arraytablaAsignacion=$TablaControl->getQueryTable();
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
}