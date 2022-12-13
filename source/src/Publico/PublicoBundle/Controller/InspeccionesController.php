<?php

namespace Publico\PublicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class InspeccionesController extends Controller
{
    public function inspeccionesAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
           $area = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area')->selectAreaTabla();
           $inspectores = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios')->selectUsuariosTabla();
           $circuito = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:Circuito')->selectCircuitoTabla();
            return $this->render('PublicoPublicoBundle:Default:index.html.twig' ,
              array(
                'area'=>$area,
                'inspectores'=>$inspectores,
                'circuito'=>$circuito
                ));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function tablaInspeccionAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
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


            $params = Array();
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/tablaInspeccion.yml"));

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
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function excelInspeccionAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            $i =1;
            $hayWhere = false;

            set_time_limit(0);
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);

            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'excelInspeccion'.date("d_m_Y").'.xls';
            $nombresheet="excelInspeccion";
            $data = $request->request->all();

            foreach ($data as $value) {
              foreach ($value as $value2) {
                if(strlen( $value2[0])){
                  $hayWhere = true;
                }
              }
            }

            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/tablaInspeccion.yml"));
            $TablaInspecciones=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);

            if($hayWhere){
              $TablaInspecciones->setNativeQuery("SELECT * FROM view_inspecciones as i WHERE ");
            }else{
              $TablaInspecciones->setNativeQuery("SELECT 0 # ");
            }


            $TablaInspecciones->setSpecialConditions("1=1");

            $arrayTablaInspecciones=$TablaInspecciones->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayTablaInspecciones as $key => $value) {
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
}
