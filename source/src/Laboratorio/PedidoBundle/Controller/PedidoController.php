<?php

namespace Laboratorio\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Laboratorio\PedidoBundle\Entity\Pedido;
use Laboratorio\PedidoBundle\Entity\Muestra;
use Laboratorio\PedidoBundle\Entity\MuestraEstados;

use Laboratorio\PedidoBundle\Form\PedidoType;

use CoreBundle\Logic\encriptador;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use CoreBundle\Logic\Excel;


class PedidoController extends Controller
{
    public function pedidoAction($id = '0',$idEstablecimiento = '0', Request $request)
    {
        if ($this->isGranted('ROLE_LABORATORIO_VIEW'))
        {
            $pedido = new Pedido();

            if ($id != '0')
            {
                $id = (int)encriptador::mrcrypt_decrypt($id);

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Pedido');
                $pedido = $em->findOneById($id);
                $bloquear = false;

                if (!$pedido)
                    throw $this->createNotFoundException('Pedido no encontrado');
                else
                {
                    //EDITAR PEDIDO
                    $anulado = $pedido->getAnulado();

                    if($pedido->getEstadoPedido()->getId() == 3 || $anulado == 1)
                        $bloquear = true;

                    $form = $this->createForm(PedidoType::class,$pedido,array('method' => 'POST'));

                    if ($request->isMethod('POST'))
                    {
                        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
                        {
                            $form->handleRequest($request);

                            if ($form->isValid() && $bloquear == false)
                            {
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($pedido);
                                $em->flush();
                                $this->addFlash('success', 'GUARDADO');

                                $em->getConnection()->close();
                                $em->clear();

                                //REDIRIGIR?
                                return $this->redirectToRoute('laboratorio_pedido_pedido', array(
                                        'id' => encriptador::mrcrypt_encrypt($pedido->getId())
                                    )
                                );
                            }
                            else
                            {
                                if ($bloquear)
                                {
                                    if ($anulado == 1)
                                        $this->addFlash('error',"No se pudo guardar el pedido. El mismo está anulado");
                                    else
                                        $this->addFlash('error',"No se pudo guardar el pedido. El mismo está terminado");
                                }
                                else
                                    $this->addFlash('error',"ERROR. REVISE LOS ERRORES");
                            }
                        }
                    }
                }
            }
            else
            {
                //CREAR PEDIDO
                if($idEstablecimiento == '0'){
                    throw $this->createNotFoundException('Establecimiento no encontrado');
                }
                $idEstablecimiento = (int)encriptador::mrcrypt_decrypt($idEstablecimiento);

                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Establecimiento');
                $establecimiento = $em->findOneById($idEstablecimiento);

                if (!$establecimiento)
                    throw $this->createNotFoundException('Establecimiento no encontrado');
                else
                {
                    //ENCONTRÓ ESTABLECIMIENTO
                    $pedido->setEstablecimiento($establecimiento);
                    //setPrioridad
                    $pr = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Prioridad');
                    $prioridad = $pr->findOneById(3); //se asume que existe y se hace porque se quiere asignar un valor por defecto
                    $pedido->setPrioridad($prioridad);

                    $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoPedido');
                    $estado = $em->findOneById(1);
                    $pedido->setEstadoPedido($estado);
                    $pedido->setAnulado(0);
                    $pedido->setEliminado(0);
                    $pedido->setAutorizado(0);

                    $form = $this->createForm(PedidoType::class, $pedido, array('method' => 'POST'));

                    if ($request->isMethod('POST'))
                    {
                        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
                        {
                            $form->handleRequest($request);

                            if ($form->isValid())
                            {
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($pedido);
                                $em->flush();
                                $this->addFlash('success', 'GUARDADO');

                                $em->getConnection()->close();
                                $em->clear();

                                return $this->redirectToRoute('laboratorio_pedido_pedido', array(
                                        'id'=> encriptador::mrcrypt_encrypt($pedido->getId())
                                    )
                                );
                            }
                            else
                                $this->addFlash('error', "ERROR, REVISE LOS ERRORES");
                        }
                    }
                }
            }
            return $this->render('LaboratorioPedidoBundle:Default:pedido.html.twig', array(
                    'form' => $form->createview()
                )
            );
        }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
        }
    }


    public function autorizarPedidoAction($id, $numeroMuestra, Request $request)
    {
        if ($this->isGranted('ROLE_LABORATORIO_MANAGER') || $this->isGranted('ROLE_LABORATORIO_SUPERVISOR') || $this->isGranted('ROLE_LABORATORIO_INCHARGE'))
        {
            if ($id != '0')
            {
                $id = (int)encriptador::mrcrypt_decrypt($id);

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Pedido');
                $pedido = $em->findOneById($id);

                if (!$pedido)
                    throw $this->createNotFoundException('Pedido no encontrado');
                else
                {
                    if ($request->isMethod('POST'))
                    {
                        if (count($pedido->getMuestras()) == 0)
                        {
                            if (!$pedido->getEliminado())
                            {
                                $m = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
                                $muestra = $m->findMuestraByNumero($numeroMuestra);

                                if (!$muestra || ($muestra && date_format(($muestra[0])->getFechaTomaMuestra(), 'Y') != date_format($pedido->getFechaProgramacion(), 'Y')))
                                {
                                    //AGREGAR ÁREAS AL PEDIDO Y, POR ENDE, A LA MUESTRA QUE SE ESTÁ POR CREAR
                                    $ar = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area');
                                    $array = array(19, 20, 21, 22);
                                    $areasLabo = array();
                                    foreach ($array as $idAreaLabo)
                                    {
                                        $areaLabo = $ar->findOneById($idAreaLabo);
                                        array_push($areasLabo, $areaLabo);
                                    }

                                    $idPrograma = $pedido->getPrograma()->getId();
                                    if ($idPrograma == 1 || $idPrograma == 17)
                                    {
                                        foreach ($areasLabo as $area)
                                            if ($area->getId() != 21)
                                                $pedido->addArea($area);
                                    }
                                    else
                                    {
                                        if ($idPrograma == 21 or $idPrograma == 14 or $idPrograma == 15 or $idPrograma == 16) # chequear info (?)
                                        {
                                            foreach ($areasLabo as $area)
                                                if ($area->getId() != 20)
                                                    $pedido->addArea($area);
                                        }
                                        else
                                        {
                                            foreach ($areasLabo as $area)
                                                $pedido->addArea($area);
                                        }
                                    }

                                    //1 - Natatorios - C - FQ - B
                                    //17 - Juegos de Agua - C - FQ - B

                                    //CREAR MUESTRA
                                    $muestra = new Muestra();
                                    $muestra->setPedido($pedido);
                                    $muestra->setSupervisado(0);
                                    $muestra->setAutorizado(0);
                                    $muestra->setAnulada(0);
                                    $muestra->setNumeroMuestra($numeroMuestra);
                                    $muestra->setFechaTomaMuestra($pedido->getFechaProgramacion()); //*

                                    //CAMBIAR ESTADO DEL PEDIDO ('En proceso')
                                    $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoPedido');
                                    $estado = $em->findOneById(2);
                                    $pedido->setEstadoPedido($estado);
                                    $pedido->setAutorizado(1);
                                    $pedido->setUsuarioAutorizado($this->getUser());

                                    //--------------------------------

                                    $ep = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                                    $estado = $ep->findOneById(1);

                                    foreach ($pedido->getAreas() as $area)
                                    {
                                        $muestraEstado = new MuestraEstados();
                                        $muestraEstado->setMuestra($muestra);
                                        $muestraEstado->setArea($area);
                                        $muestraEstado->setEstado($estado);
                                        $muestra->addEstado($muestraEstado);
                                    }

                                    $em = $this->getDoctrine()->getManager();
                                    $em->persist($muestra);
                                    $em->flush();
                                    $em->getConnection()->close();
                                    $em->clear();

                                    //return new Response('PEDIDO AUTORIZADO. Se generó la muestra número '.$muestra->getId());
                                    return new Response('PEDIDO AUTORIZADO. Se generó la muestra número '.$numeroMuestra);
                                }
                                else
                                    return new Response('No se puede crear la muestra, ya existe una con ese número');
                            }
                            else
                                return new Response('El pedido no puede ser autorizado porque el mismo fue eliminado');
                        }
                        else
                            return new Response('El pedido ya fue autorizado con anterioridad');
                    }
                }
            }
            else
                throw $this->createNotFoundException('No existe el pedido especificado');
        }else{
            return new Response('No tiene permisos para realizar esta acción');
        }
    }


    public function eliminarPedidoAction($id, Request $request)
    {
        if ($this->isGranted('ROLE_LABORATORIO_MANAGER') || $this->isGranted('ROLE_LABORATORIO_SUPERVISOR') || $this->isGranted('ROLE_LABORATORIO_INCHARGE'))
        {
            if ($id != '0')
            {
                $id = (int)encriptador::mrcrypt_decrypt($id);

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Pedido');
                $pedido = $em->findOneById($id);

                if (!$pedido)
                    throw $this->createNotFoundException('Pedido no encontrado');
                else
                {
                    if (count($pedido->getMuestras()) > 0)
                        return new Response('Este pedido tiene muestras asociadas, no se puede eliminar');
                    else
                    {
                        $pedido->setEliminado(1);
                        $pedido->setUsuarioEliminador($this->getUser());

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($pedido);
                        $em->flush();
                        $em->clear();

                        return new Response('ELIMINADO');
                    }
                }
            }
            else
                throw $this->createNotFoundException('No existe el pedido especificado');
        }else
            return new Response('No tiene permisos para realizar esta acción');
    }


    public function anularPedidoAction($id, Request $request)
    {
        if ($this->isGranted('ROLE_LABORATORIO_MANAGER') || $this->isGranted('ROLE_LABORATORIO_SUPERVISOR') || $this->isGranted('ROLE_LABORATORIO_INCHARGE'))
        {
            if ($id != '0')
            {
                $id = (int)encriptador::mrcrypt_decrypt($id);

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Pedido');
                $pedido = $em->findOneById($id);

                if (!$pedido)
                    throw $this->createNotFoundException('Pedido no encontrado');
                else
                {
                    $pedido->setAnulado(1);
                    $pedido->setUsuarioAnulador($this->getUser());

                    $muestras = $pedido->getMuestras();

                    foreach ($muestras as $muestra) {
                        $muestra->setAnulada(1);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($pedido);
                    $em->flush();
                    $em->clear();

                    return new Response('ANULADO');
                }
            }
            else
                throw $this->createNotFoundException('No existe el pedido especificado');
        }else
            return new Response('No tiene permisos para realizar esta acción');
    }

    public function tablaPedidoPageAction(Request $request){

        return $this->render('LaboratorioPedidoBundle:Tablas:tablaPedido.html.twig');

    }

    public function tablaPedidoAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            if ($this->isGranted('ROLE_LABORATORIO_VIEW')){
                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaPedido.yml"));

                $TablaCedulas=new TablaAjax($request,$em,$yaml);
                $TablaCedulas->setSpecialConditions("p.anulado <> 1 AND p.eliminado <> 1");
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

    public function excelPedidoAction(Request $request)
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

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaPedido.yml"));

                //return new Response($yaml);

                $TablaCedulas=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
                $TablaCedulas->setSpecialConditions("p.anulado <> 1 AND p.eliminado <> 1");

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
                $ruta=str_replace("C:\\wamp64\\www", "\\\\".$request->server->get('SERVER_ADDR'), $ruta);
                $em->clear();

                return new response($ruta."/".$nombreArchivo);
            }else{
                return $this->render('CoreBundle:Default:denied.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function decidirAutorizacionAction()
    {
        return $this->render('LaboratorioPedidoBundle:Default:modalAutorizar.html.twig');
    }

    public function premuestreoAction()
    {
        return $this->render('LaboratorioPedidoBundle:Default:modalPremuestreo.html.twig');
    }

    public function crearPremuestraAction($numeroMuestra, $fechaMuestra, $idPrograma, Request $request)
    {
        $fechaMuestra = explode(',', $fechaMuestra);
        $fechaMuestra = implode('-', $fechaMuestra);
        $fechaMuestra = date_create($fechaMuestra);
        //OK
        //$parametros = array($numeroMuestra, $fechaMuestra, $idPrograma);

        //desde autorizarPedidoAction ↓
        $m = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
        $muestra = $m->findMuestraByNumero($numeroMuestra);

        if (!$muestra || ($muestra && date_format(($muestra[0])->getFechaTomaMuestra(), 'Y') != date_format($fechaMuestra, 'Y')))
        {
            $pedido = new Pedido();

            //AGREGAR ÁREAS AL PEDIDO Y, POR ENDE, A LA MUESTRA QUE SE ESTÁ POR CREAR
            $ar = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Area');
            $array = array(19, 20, 21, 22);
            $areasLabo = array();
            foreach ($array as $idAreaLabo)
            {
                $areaLabo = $ar->findOneById($idAreaLabo);
                array_push($areasLabo, $areaLabo);
            }

            //$idPrograma = $pedido->getPrograma()->getId();
            if ($idPrograma == 1 || $idPrograma == 17)
            {
                foreach ($areasLabo as $area)
                    if ($area->getId() != 21)
                        $pedido->addArea($area);
            }
            else
            {
                if ($idPrograma == 21)
                {
                    foreach ($areasLabo as $area)
                        if ($area->getId() != 20)
                            $pedido->addArea($area);
                }
                else
                {
                    foreach ($areasLabo as $area)
                        $pedido->addArea($area);
                }
            }

            //1 - Natatorios - C - FQ - B
            //17 - Juegos de Agua - C - FQ - B

            //setPrioridad
            $pr = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Prioridad');
            $prioridad = $pr->findOneById(3); //se asume que existe
            $pedido->setPrioridad($prioridad);
            //setTipoPedido
            $tp = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:TipoPedido');
            $tipoPedido = $tp->findOneById(1); //se asume que existe
            $pedido->setTipoPedido($tipoPedido);
            //setEstadoPedido ('En proceso')
            $ep = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoPedido');
            $estado = $ep->findOneById(2);
            $pedido->setEstadoPedido($estado);
            //setPrograma
            $rp = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Programa');
            $programa = $rp->findOneById($idPrograma);
            $pedido->setPrograma($programa);

            $pedido->setFechaProgramacion($fechaMuestra);
            $pedido->setAnulado(0);
            $pedido->setEliminado(0);
            $pedido->setAutorizado(1);
            $pedido->setUsuarioAutorizado($this->getUser());
            //$pedido->setEstablecimiento($establecimiento);

            //CREAR MUESTRA
            $muestra = new Muestra();
            $muestra->setPedido($pedido);
            $muestra->setSupervisado(0);
            $muestra->setAutorizado(0);
            $muestra->setAnulada(0);
            $muestra->setNumeroMuestra($numeroMuestra);
            $muestra->setFechaTomaMuestra($fechaMuestra);

            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
            $estado = $em->findOneById(1);

            foreach ($pedido->getAreas() as $area)
            {
                $muestraEstado = new MuestraEstados();
                $muestraEstado->setMuestra($muestra);
                $muestraEstado->setArea($area);
                $muestraEstado->setEstado($estado);
                $muestra->addEstado($muestraEstado);
            }

            $ema = $this->getDoctrine()->getManager();
            $ema->persist($pedido);
            $ema->persist($muestra);
            $ema->flush();
            $ema->getConnection()->close();
            $ema->clear();

            //return new Response('PEDIDO AUTORIZADO. Se generó la muestra número '.$numeroMuestra);
            //return new Response('Muestra guardada');
            /*return new RedirectResponse($this->generateUrl(
                'laboratorio_pedido_muestra',
                array(
                    'idMuestra' => encriptador::mrcrypt_encrypt($muestra->getIdMuestra()),
                    'idArea' => encriptador::mrcrypt_encrypt(19)
                )
            ));*/

            /*return $this->redirectToRoute(
                'laboratorio_pedido_muestra',
                array(
                    'idMuestra' => encriptador::mrcrypt_encrypt($muestra->getIdMuestra()),
                    'idArea' => encriptador::mrcrypt_encrypt(19)
                )
            );*/

            $url = $this->generateUrl(
                'laboratorio_pedido_muestra',
                array(
                    'idMuestra' => encriptador::mrcrypt_encrypt($muestra->getIdMuestra()),
                    'idArea' => encriptador::mrcrypt_encrypt(19)
                )
            );
            return new Response($url);
        }
        else
            return new Response('No se puede crear la muestra, ya existe una con ese número');
    }
}
