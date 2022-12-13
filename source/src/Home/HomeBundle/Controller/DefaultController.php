<?php

namespace Home\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use CoreBundle\Logic\Excel;

class DefaultController extends Controller
{
    public function homeAction()
    {
      set_time_limit(0);
      ini_set('max_execution_time', 0);

      $user=$this->getUser();

      if($user){
        
        $tipoUsuario = $user->getTipoUsuario();

        if( $tipoUsuario[0]->getTipoUsuario() == 'INSPECTOR'){
          return $this->redirect($this->generateUrl('inspecciones_inspecciones_page_inspectores_sade_table'));
        }


        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
         $estado = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado')->selectEstadosTabla();
         $RubroPrincipal = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal')->selectRubrosTabla();
         $Rubro = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Rubro')->selectRubrosTabla();
         $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->selectAreaTabla();
         $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
         $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->selectCircuitoTabla();
         $tipoCedula = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula')->selectTipoCedulaTabla();
         $tipoDisposicion = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoDispo')->selectTipoDisposicionTabla();
         $estadoCedula = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:Estado')->selectEstadoTabla();
          return $this->render('HomeHomeBundle:Default:index.html.twig' , 
            array('estado'=>$estado,
              'RubroPrincipal'=>$RubroPrincipal,
              'Rubro'=>$Rubro,              
              'area'=>$area,
              'inspectores'=>$inspectores,
              'circuito'=>$circuito,
              'tipoCedula'=>$tipoCedula,
              'tipoDispo'=>$tipoDisposicion,
              'estadoCedula'=>$estadoCedula
              ));

          }else{
            return $this->render('CoreBundle:Default:denied.html.twig');
          }  
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }
    }

    public function tablaEstablecimientoAction(Request $request)
    {      
      $i =1;
      $hayWhere = false;

      while(!is_null($request->request->get('order_'.$i)) ){
        if(strlen($request->request->get('order_'.$i)) > 0){
          $hayWhere = true;          
        }        
        $i++;
      }      

      $user=$this->getUser();
      set_time_limit(0);
      ini_set('max_execution_time', 0);

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/tablaEstablecimiento.yml"));
          
          $tablaEstablecimiento=new TablaAjax($request,$em,$yaml);
          
          
          if($hayWhere){
            $tablaEstablecimiento->setNativeQuery("SELECT * FROM view_establecimientos as e WHERE");
            $tablaEstablecimiento->setQueryCount("SELECT count(*) as count FROM view_establecimientos as e WHERE ");
          }else{
            $tablaEstablecimiento->setNativeQuery("SELECT * FROM view_establecimientos as e WHERE 1=2 AND ");
            $tablaEstablecimiento->setQueryCount("SELECT 0 as count #");
          }          
          
          $tablaEstablecimiento->setSpecialConditions("1=1");          


          $response = new Response($tablaEstablecimiento->Initialize());
          $response->headers->set('Content-Type', 'application/json');
          $em->clear();

          return $response;

        }else{
          return $this->render('CoreBundle:Default:denied.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }   
    }

    public function tablaCedulaAction(Request $request)
    {
      $i =1;
      $hayWhere = false;

      while(!is_null($request->request->get('order_'.$i)) ){
        if(strlen($request->request->get('order_'.$i)) > 0){
          $hayWhere = true;
        }
        $i++;
      }

      if(strlen($request->request->get('order_3_2')) > 0){
        $hayWhere = true;
      }

      if(strlen($request->request->get('order_1')) > 0){
          $value= $request->request->get('order_1');
          $value = str_replace("-","",strtolower($value));
          $value = str_replace("ch","-",strtolower($value));
          $request->request->set('order_1',$value);
      }

      $user=$this->getUser();
      set_time_limit(0);           
      ini_set('max_execution_time', 0); 

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaCedula.yml"));        
             
            $TablaCedulas=new TablaAjax($request,$em,$yaml);

            if($hayWhere){
              $TablaCedulas->setNativeQuery("SELECT c.id,CASE WHEN c.numero < 0 THEN CONCAT('CH',c.numero) ELSE c.numero END as numero,tc.TipoCedula,n.fecha_notificacion,n.direccion_notificada,n.comuna_notificada,n.nocturnidad,e.estado,CONCAT(u.apellido, ',', u.nombre) AS notificador,d.comuna FROM cedula AS c INNER JOIN notificacion AS n ON c.id = n.id INNER JOIN tipo_cedula AS tc ON c.tipo_id = tc.id INNER JOIN estado_notificacion AS e ON e.id = n.estado_id INNER JOIN usuarios AS u ON u.id = n.notificador_id INNER JOIN view_direcciones AS d ON d.Id_Establecimiento = n.establecimiento_id WHERE ");

              $TablaCedulas->setQueryCount("SELECT count(*) as count FROM cedula as c INNER JOIN notificacion as n on c.id = n.id INNER JOIN tipo_cedula as tc on c.tipo_id = tc.id INNER JOIN estado_notificacion as e ON e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN view_direcciones as d on d.Id_Establecimiento = n.establecimiento_id WHERE ");
            }else{
              $TablaCedulas->setNativeQuery("SELECT c.id,CASE WHEN c.numero < 0 THEN CONCAT('CH',c.numero) ELSE c.numero END as numero,tc.TipoCedula,n.fecha_notificacion,n.direccion_notificada,n.comuna_notificada,n.nocturnidad,e.estado,CONCAT(u.apellido, ',', u.nombre) AS notificador,d.comuna FROM cedula AS c INNER JOIN notificacion AS n ON c.id = n.id INNER JOIN tipo_cedula AS tc ON c.tipo_id = tc.id INNER JOIN estado_notificacion AS e ON e.id = n.estado_id INNER JOIN usuarios AS u ON u.id = n.notificador_id INNER JOIN view_direcciones AS d ON d.Id_Establecimiento = n.establecimiento_id WHERE 1=2 and ");

              $TablaCedulas->setQueryCount("SELECT 0 as count #");
            }

            $TablaCedulas->setSpecialConditions("c.numero is not null and (n.usuario_eliminador is null or n.usuario_eliminador = 0)");        
            
            $response = new Response($TablaCedulas->Initialize());
            $response->headers->set('Content-Type', 'application/json');
            $em->clear(); 
            return $response;
        }else{
          return $this->render('CoreBundle:Default:denied.html.twig');
        } 
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }             
    }

    public function tablaDisposicionAction(Request $request)
    { 
      $i =1;
      $hayWhere = false;

      while(!is_null($request->request->get('order_'.$i)) ){
        if(strlen($request->request->get('order_'.$i)) > 0){
          $hayWhere = true;          
        }
        $i++;
      }

      if(strlen($request->request->get('order_3_2')) > 0){
        $hayWhere = true;
      }

      $user=$this->getUser();

      if($user){
        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){     
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaDisposicion.yml"));        
             
            $TablaDisposiciones=new TablaAjax($request,$em,$yaml);
            
            if($hayWhere){
              $TablaDisposiciones->setNativeQuery("SELECT d.id,CONCAT(IF(t.id = 11,'IF-','DI-'),d.numero,'-',r.reparticion,'-',d.anio) as numero,t.tipo_dispo,n.fecha_notificacion,n.direccion_notificada,e.estado,CONCAT(u.apellido,',',u.nombre) as notificador,dc.levantada FROM disposicion as d INNER JOIN notificacion as n on d.id = n.id INNER JOIN tipo_dispo as t on d.tipo_id = t.id INNER JOIN estado_notificacion as e on e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN reparticion as r on d.reparticion_id = r.id LEFT JOIN disposicion_clausura as dc on d.id = dc.id WHERE ");

              $TablaDisposiciones->setQueryCount("SELECT count(*) as count FROM disposicion as d INNER JOIN notificacion as n on d.id = n.id INNER JOIN tipo_dispo as t on d.tipo_id = t.id INNER JOIN estado_notificacion as e on e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN reparticion as r on d.reparticion_id = r.id LEFT JOIN disposicion_clausura as dc on d.id = dc.id WHERE ");
            }else{
              $TablaDisposiciones->setNativeQuery("SELECT d.id,CONCAT(IF(t.id = 11,'IF-','DI-'),d.numero,'-',r.reparticion,'-',d.anio) as numero,t.tipo_dispo,n.fecha_notificacion,n.direccion_notificada,e.estado,CONCAT(u.apellido,',',u.nombre) as notificador,dc.levantada FROM disposicion as d INNER JOIN notificacion as n on d.id = n.id INNER JOIN tipo_dispo as t on d.tipo_id = t.id INNER JOIN estado_notificacion as e on e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN reparticion as r on d.reparticion_id = r.id LEFT JOIN disposicion_clausura as dc on d.id = dc.id WHERE 1=2 AND ");

              $TablaDisposiciones->setQueryCount("SELECT 0 as count #");
            }

            
            
            $TablaDisposiciones->setSpecialConditions("(n.usuario_eliminador is null or n.usuario_eliminador = 0)");

            $response = new Response($TablaDisposiciones->Initialize());
            $response->headers->set('Content-Type', 'application/json');
            $em->clear(); 
            return $response; 
        }else{
            return $this->render('CoreBundle:Default:denied.html.twig');
        }
      }else{
        return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }             
    }

    public function tablaInspeccionAction(Request $request)
    {
      $i =1;
      $hayWhere = false;

      while(!is_null($request->request->get('order_'.$i)) ){
        if(strlen($request->request->get('order_'.$i)) > 0){
          $hayWhere = true;          
        }
        $i++;
      }

      if(strlen($request->request->get('order_3_2')) > 0){
          $hayWhere = true;
        }

      $user=$this->getUser();      
      if($user){        
        set_time_limit(0);           
        ini_set('max_execution_time', 0);

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
          $em = $this->get('doctrine')->getManager();
          $em = $this->get('doctrine')->getManager('default');
          $em = $this->get('doctrine.orm.default_entity_manager');

          $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspeccion.yml"));        
           
          $TablaInspecciones=new TablaAjax($request,$em,$yaml);
          
          $TablaInspecciones->setSpecialConditions(" 1 = 1");

          if($hayWhere){
            $TablaInspecciones->setQueryCount("SELECT COUNT(*) as count FROM view_inspecciones as i WHERE ");
            $TablaInspecciones->setNativeQuery("SELECT * FROM view_inspecciones as i WHERE ");
          }else{
            $TablaInspecciones->setNativeQuery("SELECT * FROM view_inspecciones as i WHERE 1=2 AND ");
            $TablaInspecciones->setQueryCount("SELECT 0 as count #") ;
          }

          $response = new Response($TablaInspecciones->Initialize());
          $response->headers->set('Content-Type', 'application/json');
          $em->clear(); 
          return $response; 
        }else{
            return $this->render('CoreBundle:Default:denied.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }            
    }

    public function excelEstablecimientoAction(Request $request)
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

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/tablaEstablecimiento.yml"));

            //return new Response($yaml);
            
            $tablaEstablecimiento=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);

           $tablaEstablecimiento->setNativeQuery("SELECT * FROM view_establecimientos as e WHERE ");            
            
            $tablaEstablecimiento->setSpecialConditions("1=1");      
            $arrayEstablecimiento=$tablaEstablecimiento->getQueryTable();

            $objPHPExcel = new \PHPExcel();


            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayEstablecimiento as $key => $value) {
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

            //$ruta = 'temp';
            $em->clear(); 

            
            //return new response($ruta."/".$nombreArchivo);
            return new BinaryFileResponse($ruta."/".$nombreArchivo);
            

        }else{
            return $this->render('CoreBundle:Default:denied.html.twig');
        }
      }else{
          return $this->redirect($this->generateUrl('usuario_usuario_login'));
      }               
    }

    public function excelCedulaAction(Request $request)
    {
      $user=$this->getUser();

      if($user){

        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelCedula'.date("d_m_Y").'.xls';
            $nombresheet="excelCedula";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaCedula.yml"));

            //return new Response($yaml);
            
            $TablaCedulas=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaCedulas->setNativeQuery("SELECT c.id,c.numero,tc.TipoCedula,n.fecha_notificacion,n.direccion_notificada,n.comuna_notificada,n.nocturnidad,e.estado,concat(u.apellido,',',u.nombre) as notificador,d.comuna FROM cedula as c INNER JOIN notificacion as n on c.id = n.id INNER JOIN tipo_cedula as tc on c.tipo_id = tc.id INNER JOIN estado_notificacion as e ON e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN view_direcciones as d on d.Id_Establecimiento = n.establecimiento_id WHERE ");

            $TablaCedulas->setSpecialConditions("c.numero is not null and (n.usuario_eliminador is null or n.usuario_eliminador = 0)");

            $arrayTablaCedulas=$TablaCedulas->getQueryTable();
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


    public function excelDisposicionAction(Request $request)
    {

      $user=$this->getUser();

      if($user){

        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){        
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelDisposicion'.date("d_m_Y").'.xls';
            $nombresheet="excelDisposicion";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaDisposicion.yml"));        
            
            $TablaDisposiciones=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaDisposiciones->setNativeQuery("SELECT d.id,CONCAT(IF(t.id = 11,'IF-','DI-'),d.numero,'-',r.reparticion,'-',d.anio) as numero,t.tipo_dispo,n.fecha_notificacion,n.direccion_notificada,e.estado,CONCAT(u.apellido,',',u.nombre) as notificador,dc.levantada FROM disposicion as d INNER JOIN notificacion as n on d.id = n.id INNER JOIN tipo_dispo as t on d.tipo_id = t.id INNER JOIN estado_notificacion as e on e.id = n.estado_id INNER JOIN usuarios as u on u.id = n.notificador_id INNER JOIN reparticion as r on d.reparticion_id = r.id LEFT JOIN disposicion_clausura as dc on d.id = dc.id WHERE ");
            
            $TablaDisposiciones->setSpecialConditions("(n.usuario_eliminador is null or n.usuario_eliminador = 0)");

            $arrayTablaDisposiciones=$TablaDisposiciones->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayTablaDisposiciones as $key => $value) {
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

    public function excelInspeccionAction(Request $request)
    {
      $user=$this->getUser();

      if($user){

        $checker=$this->container->get('security.authorization_checker');

        if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){        
            set_time_limit(0); 
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);

            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelInspeccion'.date("d_m_Y").'.xls';
            $nombresheet="excelInspeccion";
            $data = $request->request->all();

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaInspeccion.yml"));        
            $TablaInspecciones=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaInspecciones->setNativeQuery("SELECT * FROM view_inspecciones as i WHERE ");
            $TablaInspecciones->setSpecialConditions("1=1");

            $arrayTablaInspecciones=$TablaInspecciones->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            $active_sheet = 0;
            foreach ($arrayTablaInspecciones as $key => $value) {
              foreach ($value as $key1 => $value1) {
                if($numero==1 || $numero == 65000){
                  if($numero == 65000){
                    $active_sheet++;
                    $objPHPExcel->createSheet();
                    $objPHPExcel->setActiveSheetIndex($active_sheet);
                    $numero=1;
                  }

                  $objPHPExcel->getActiveSheet()->setCellValue($letra.$numero, $key1);
                  $objPHPExcel->getActiveSheet()->setCellValue($letra.($numero+1), $value1);
                  $letra++;
                  $objPHPExcel->getActiveSheet()->setTitle($nombresheet.($active_sheet+1));
                }else{
                  $objPHPExcel->getActiveSheet()->setCellValue($letra.($numero+1), $value1);
                  $letra++;
                }
              }
              $letra="a";
              $numero++;        
            }
            
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

    public function pruebaAction(Request $request)
    { 
        return Excel::excelToArray("fajas.xlsx");
    }


}