<?php

namespace Laboratorio\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Laboratorio\PedidoBundle\Entity\Muestra;
use Laboratorio\PedidoBundle\Entity\CargaResultados;
use Laboratorio\PedidoBundle\Entity\MuestraConcluciones;
use Laboratorio\PedidoBundle\Entity\HistorialCargaResultados;

use Laboratorio\PedidoBundle\Form\MuestraType;

use CoreBundle\Logic\encriptador;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;

class MuestraController extends Controller
{
    public function derivadorMuestraAction(Request $request, $idMuestra)
    {
        $user = $this->getUser();

        if ($user)
        {
            $idAreaUsuario = $user->getArea()->getId();

            if ($idAreaUsuario < 19 || $idAreaUsuario > 22)
                return $this->redirectToRoute('laboratorio_pedido_muestra', array('idMuestra'=>$idMuestra, 'idArea'=>encriptador::mrcrypt_encrypt(19)));
            else
            {
                //return $this->redirectToRoute('laboratorio_pedido_muestra', array('idMuestra'=>$idMuestra, 'idArea'=>encriptador::mrcrypt_encrypt($idAreaUsuario)));
                $muestra = new Muestra();
                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
                $muestra = $em->findOneByIdMuestra((int)encriptador::mrcrypt_decrypt($idMuestra));

                if ($muestra)
                {
                    //Buscar todas las áreas de la muestra y chequear que exista el área del usuario
                    $existeElArea = false;
                    foreach ($muestra->getEstados() as $estado)
                        if ($estado->getArea()->getId() == $idAreaUsuario)
                            $existeElArea = true;

                    if ($existeElArea)
                        return $this->redirectToRoute('laboratorio_pedido_muestra', array('idMuestra'=>$idMuestra, 'idArea'=>encriptador::mrcrypt_encrypt($idAreaUsuario)));
                    else
                    {
                        if ($user->getId() == 285) #PABLO
                            return $this->redirectToRoute('laboratorio_pedido_muestra', array('idMuestra'=>$idMuestra, 'idArea'=>encriptador::mrcrypt_encrypt(19)));
                        else
                            return $this->redirectToRoute('laboratorio_pedido_muestra_area');
                    }
                }
                else
                    throw $this->createNotFoundException('Muestra no encontrada');
            }
        }
        else
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
    }

    public function areaNoParticipanteAction (Request $request)
    {
        return $this->render('LaboratorioPedidoBundle:Default:areaNoParticipante.html.twig');
    }

    public function muestraAction(Request $request, $idMuestra, $idArea)
    {
        if ($this->isGranted('ROLE_LABORATORIO_VIEW'))
        {
            $muestra = new Muestra();

            $idArea = encriptador::mrcrypt_decrypt($idArea);
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);

            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                //En caso de ser de laboratorio, si no está el área que se está recibiendo por parámetro, derivar a campo
                $i = 0;
                while ($i < count($muestra->getPedido()->getAreas()) && ($muestra->getPedido()->getAreas()[$i])->getId() != $idArea)
                    $i++;

                if ($i >= count($muestra->getPedido()->getAreas()))
                    $idArea = 19;

                $rd = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Determinacion');
                $determinaciones = $rd->getDeterminacionesForResultados($muestra->getId(), $muestra->getPedido()->getPrograma()->getId(), $idArea);

                foreach ($determinaciones as $determinacion)
                {
                    $resultado = new CargaResultados();
                    $resultado->setDeterminacion($determinacion);
                    $resultado->setMuestra($muestra);
                    $muestra->addResultado($resultado);
                }

                //Acá es donde se tendría que ver la manera de ordenar las determinaciones que van a ser cargadas por los usuarios
                //El ordenamiento de las mismas se realizaría antes de la creación del formulario
                //\Doctrine\Common\Util\Debug::dump('Holi');
                //exit();

                $form = $this->createForm(MuestraType::class, $muestra, array(
                        'method' => 'POST',
                        'idArea' => $idArea,
                        'idPrograma'=>$muestra->getPedido()->getPrograma()->getId(),
                        'supervisado'=>$muestra->getSupervisado(),
                        'autorizado'=>$muestra->getAutorizado(),
                        'anulado'=>$muestra->getPedido()->getAnulado(),
                        'areaUsuarioActual'=>$this->getUser()->getArea()->getId()
                    )
                );

                if ($request->isMethod('POST'))
                {
                    if (($this->isGranted('ROLE_LABORATORIO_EDIT') && $this->getUser()->getArea()->getId() == $idArea) || $this->isGranted('ROLE_LABORATORIO_MANAGER'))
                    {
                        $form->handleRequest($request);

                        if ($form->isValid())
                        {
                            //ACORDARSE DE AGREGAR LAS VALIDACIONES!!!!

                            $resultados = $muestra->getResultados();
                            $resultadoModificado = false;
                            $edm = $this->getDoctrine()->getManager();

                            foreach ($resultados as $resultado)
                            {
                                if ($resultado->getDeterminacion()->getArea()->getId() == $idArea)
                                {
                                    if ($resultado->getResultado())
                                    {
                                        //Falta agregar validación de fechas
                                        /*$rhcg = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:HistorialCargaResultados');
                                        $ultimoHistorial=$rhcg->findOneBy(array('idResultado'=>$resultado->getId()),array('fechaCreado'=>'DESC'));

                                        if(!$ultimoHistorial || $resultado->__toString() != $ultimoHistorial->__toString())
                                        {
                                            $historial = new HistorialCargaResultados();*/

                                        if ($resultado->getResultado() == -1)
                                            $resultado->setResultado(0);

                                        /*$historial->setResultado($resultado->getResultado());
                                        $historial->setFechaInicioAnalisis($resultado->getFechaInicioAnalisis());
                                        $historial->setFechaFinAnalisis($resultado->getFechaFinAnalisis());
                                        $historial->setDeterminacion($resultado->getDeterminacion());
                                        $historial->setLegislacion($resultado->getLegislacion());
                                        $historial->setLegislacionSinContacto($resultado->getLegislacionSinContacto());
                                        $historial->setLegislacionPasivo($resultado->getLegislacionPasivo());
                                        $historial->setUsuario($resultado->getUsuario());
                                        $historial->setIdResultado($resultado);
                                        $resultado->addHistorial($historial);
                                    }*/

                                        $resultado->setBloqueado(1);
                                        $edm->persist($resultado);
                                        $edm->flush($resultado);
                                        $resultadoModificado = true;
                                    }
                                    else
                                        $muestra->removeResultado($resultado);
                                }
                            }

                            $rme = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:MuestraEstados'); #Italo
                            $muestraEstado = $rme->findOneBy(array('muestra'=>$idMuestra, 'area'=>$idArea)); #Italo

                            if($resultadoModificado)
                            {
                                /*$rme = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:MuestraEstados');
                                $muestraEstado = $rme->findOneBy(array('muestra'=>$idMuestra,'area'=>$idArea));*/ #Italo

                                if($muestraEstado->getEstado()->getId() == 1)
                                {
                                    $rem = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                                    $estadoMuestra = $rem->findOneById(2);
                                    $muestraEstado->setEstado($estadoMuestra);

                                    /*$edm->persist($muestraEstado);
                                    $edm->flush($muestraEstado);*/ #Italo
                                }
                            }

                            $edm->persist($muestraEstado); #Italo
                            $edm->flush($muestraEstado); #Italo

                            if($idArea == 19)
                            {
                                $auxiliares = $muestra->getAuxiliares();

                                //NO SÉ SI HACE FALTA ↓ (porque ya tenemos la muestra en $muestra)
                                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
                                $muestra = $em->findOneByIdMuestra($muestra->getId());
                                //NO SÉ SI HACE FALTA ↑ (porque ya tenemos la muestra en $muestra)

                                $muestra->setAuxiliares($auxiliares);
                                $edm->persist($muestra);
                                $edm->flush($muestra);
                            }

                            //¿NO FALTA UN persist Y UN flush DE LA MUESTRA? (Italo)
                            /*$edm->persist($muestra);
                            $edm->flush($muestra);*/
                            // ↓→ no?

                            $this->addFlash('success','GUARDADO');

                            $em->clear();
                            $edm->getConnection()->close();
                            $edm->clear();

                            return $this->redirectToRoute('laboratorio_pedido_muestra', array('idMuestra'=>encriptador::mrcrypt_encrypt($idMuestra),'idArea'=>encriptador::mrcrypt_encrypt($idArea) ));
                        }
                    }
                }
            }

            return $this->render('LaboratorioPedidoBundle:Default:muestra.html.twig' , array(
                'form' => $form->createview(),
                'numeroMuestraCompleto' => $muestra->getNumeroMuestraCompleto(),
                'number'=> $idArea
            ));
        }
        else
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
    }

    public function autorizarAction(Request $request, $idMuestra)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $muestra->setAutorizado(1);
                $muestra->setUsuarioAutorizador($this->getUser());

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoPedido');
                $estadoPedido = $em->findOneById(3);

                $pedido = $muestra->getPedido();
                $pedido->setEstadoPedido($estadoPedido);

                $em = $this->getDoctrine()->getManager();
                $em->persist($pedido);
                $em->flush($pedido);
                $em->persist($muestra);
                $em->flush($muestra);
                $em->getConnection()->close();
                $em->clear();

                return new Response("Muestra autorizada");
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function desAutorizarAction(Request $request, $idMuestra)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $muestra->setAutorizado(0);
                $muestra->setUsuarioAutorizador($this->getUser());

                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoPedido');
                $estadoPedido = $em->findOneById(2);

                $pedido = $muestra->getPedido();
                $pedido->setEstadoPedido($estadoPedido);


                $em = $this->getDoctrine()->getManager();
                $em->persist($pedido);
                $em->flush($pedido);
                $em->persist($muestra);
                $em->flush($muestra);
                $em->getConnection()->close();
                $em->clear();

                return new Response("Autorización anulada");
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function supervisarAction(Request $request, $idMuestra)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $muestra->setSupervisado(1);
                $muestra->setUsuarioSupervisador($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($muestra);
                $em->flush($muestra);
                $em->getConnection()->close();
                $em->clear();

                return new Response("Muestra aprobada por el DT");
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function desSupervisarAction(Request $request, $idMuestra)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $muestra->setSupervisado(0);
                $muestra->setUsuarioSupervisador($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($muestra);
                $em->flush($muestra);
                $em->getConnection()->close();
                $em->clear();

                return new Response("Aprobación anulada");
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function finalizarMuestraAction(Request $request, $idMuestra, $idArea)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idArea  = encriptador::mrcrypt_decrypt($idArea);
            $idMuestra = encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $estados = $muestra->getEstados();

                $i = 0;
                while ($i < count($estados) && $estados[$i]->getArea()->getId() != $idArea)
                    $i++;

                if ($estados[$i]->getEstado()->getId() < 3 && ($estados[$i]->getArea()->getId() == $idArea || $this->isGranted('ROLE_LABORATORIO_ADMIN')))
                {
                    $estado = $estados[$i];
                    $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                    $estadoMuestra = $em->findOneById(3);
                    $estado->setEstado($estadoMuestra);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($estado);
                    $em->flush($estado);
                    $em->getConnection()->close();
                    $em->clear();

                    return new Response("Muestra aprobada por el subgerente");
                }
                else
                    return new Response("No se puede aprobar la muestra");

                /*foreach ($estados as $estado)
                {
                    if(($estado->getEstado()->getId() == 2 && $estado->getArea()->getId() == $idArea) || $this->isGranted('ROLE_LABORATORIO_ADMIN'))
                    {
                        $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                        $estadoMuestra = $em->findOneById(3);
                        $estado->setEstado($estadoMuestra);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($estado);
                        $em->flush($estado);
                        $em->getConnection()->close();
                        $em->clear();

                        return new Response("Muestra finalizada");
                    }
                    else
                        return new Response("No se puede finalizar la muestra");
                }*/
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");

    }

    public function desFinalizarMuestraAction(Request $request, $idMuestra, $idArea)
    {
        if ($this->isGranted('ROLE_LABORATORIO_EDIT'))
        {
            $idArea  = encriptador::mrcrypt_decrypt($idArea);
            $idMuestra = encriptador::mrcrypt_decrypt($idMuestra);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            if (!$muestra)
                throw $this->createNotFoundException('Muestra no encontrada');
            else
            {
                $estados = $muestra->getEstados();

                $i = 0;
                while ($i < count($estados) && $estados[$i]->getArea()->getId() != $idArea)
                    $i++;

                if ($estados[$i]->getEstado()->getId() == 3 && ($estados[$i]->getArea()->getId() == $idArea || $this->isGranted('ROLE_LABORATORIO_ADMIN')))
                {
                    $estado = $estados[$i];
                    $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                    $estadoMuestra = $em->findOneById(2);
                    $estado->setEstado($estadoMuestra);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($estado);
                    $em->flush($estado);
                    $em->getConnection()->close();
                    $em->clear();

                    return new Response("Aprobación anulada");
                }
                else
                    return new Response("No se puede deshacer la aprobación de la muestra");

                /*foreach ($estados as $estado)
                {
                    if($estado->getEstado()->getId() == 3 && $estado->getArea()->getId() == $idArea)
                    {
                        $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:EstadoMuestra');
                        $estadoMuestra = $em->findOneById(2);
                        $estado->setEstado($estadoMuestra);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($estado);
                        $em->flush($estado);
                        $em->getConnection()->close();
                        $em->clear();

                        return new Response("Muestra en proceso");
                    }else{
                        return new Response("No se puede des-finalizar la muestra");
                    }
                }*/
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function BloquearResultadoAction(Request $request, $idResultado,$bloquear)
    {
        if ($this->isGranted('ROLE_LABORATORIO_MANAGER') || $this->isGranted('ROLE_LABORATORIO_DIRECTOR') || $this->isGranted('ROLE_LABORATORIO_SUPERVISOR') )
        {
            $idResultado = (int)encriptador::mrcrypt_decrypt($idResultado);
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:CargaResultados');
            $resultado = $em->findOneById($idResultado);

            if (!$resultado)
                throw $this->createNotFoundException('Resultado no encontrado');
            else
            {
                if($bloquear == 0 || $bloquear == 1)
                {
                    $resultado->setBloqueado($bloquear);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($resultado);
                    $em->flush($resultado);
                    $em->getConnection()->close();
                    $em->clear();

                    return new Response("OK");
                }
                else
                    return new Response("Parámetro incorrecto");
            }
        }
        else
            return new Response("No tiene permisos para realizar esta acción");
    }

    public function tablaMuestraPageAction(Request $request){
        return $this->render('LaboratorioPedidoBundle:Tablas:tablaMuestra.html.twig');
    }

    public function tablaMuestraAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            if ($this->isGranted('ROLE_LABORATORIO_VIEW')){
                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaMuestra.yml"));

                $TablaMuestra=new TablaAjax($request,$em,$yaml);

                $TablaMuestra->setNativeQuery("SELECT m.id, m.numeroMuestra, tp.tipo, pro.Programa, m.fecha_toma_muestra, CASE m.supervisado WHEN 1 THEN 'Aprobada' ELSE 'Pendiente' END AS supervisado,CASE m.autorizado WHEN 1 THEN 'AUTORIZADA' ELSE 'PENDIENTE' END as autorizado,(SELECT e.estado FROM laboratoio_estado_muestra as e  INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 19 and me.muestra_id = m.id) as CAMPO,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 22 and me.muestra_id = m.id) as FISICOQUIMICA,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 20 and me.muestra_id = m.id) as BIOLOGICO,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 21 and me.muestra_id = m.id) as INSTRUMENTAL FROM laboratorio_muestra as m INNER JOIN laboratorio_pedido AS p ON p.id = pedido_id INNER JOIN laboratorio_programa AS pro ON pro.id = p.programa_id INNER JOIN laboratorio_tipo_pedido AS tp ON tp.id = p.tipo_pedido_id WHERE");

                $TablaMuestra->setQueryCount("SELECT count(*) as count  FROM laboratorio_muestra as m INNER JOIN laboratorio_pedido AS p ON p.id = pedido_id INNER JOIN laboratorio_programa AS pro ON pro.id = p.programa_id INNER JOIN laboratorio_tipo_pedido AS tp ON tp.id = p.tipo_pedido_id WHERE");
                $TablaMuestra->setSpecialConditions("1=1");

                $response = new Response($TablaMuestra->Initialize());
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

    public function excelMuestraAction(Request $request)
    {
        $user=$this->getUser();

        if($user){

            if ($this->isGranted('ROLE_NOTIFICACIONES_VIEW')){
                $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
                $nombreArchivo= 'excelCedula'.date("d_m_Y").'.xls';
                $nombresheet="excelCedula";
                $data = $request->request->all();

                $em = $this->get('doctrine')->getManager();
                $em = $this->get('doctrine')->getManager('default');
                $em = $this->get('doctrine.orm.default_entity_manager');

                $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaMuestra.yml"));

                //return new Response($yaml);

                $TablaMuestra=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
                $TablaMuestra->setNativeQuery("SELECT m.id,m.id, (SELECT pr.Programa FROM laboratorio_muestra AS mu INNER JOIN laboratorio_pedido AS pe ON mu.pedido_id = pe.id INNER JOIN laboratorio_programa AS pr ON pr.id = pe.programa_id WHERE mu.id = m.id) as programa,m.fecha_toma_muestra,CASE m.supervisado WHEN 1 THEN 'SUPERVISADA' ELSE 'PENDIENTE' END AS supervisado,CASE m.autorizado WHEN 1 THEN 'AUTORIZADA' ELSE 'PENDIENTE' END as autorizado,(SELECT e.estado FROM laboratoio_estado_muestra as e  INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 19 and me.muestra_id = m.id) as CAMPO,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 22 and me.muestra_id = m.id) as FISICOQUIMICA,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 20 and me.muestra_id = m.id) as BIOLOGICO,(SELECT e.estado FROM laboratoio_estado_muestra as e INNER JOIN laboratorio_muestra_estados as me  on e.id = me.estado_id WHERE me.area_id = 21 and me.muestra_id = m.id) as INSTRUMENTAL FROM laboratorio_muestra as m WHERE ");

                $TablaMuestra->setQueryCount("SELECT count(*) as count FROM laboratorio_muestra as m WHERE");
                $TablaMuestra->setSpecialConditions("1=1");

                $arrayTablaCedulas=$TablaMuestra->getQueryTable();
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

    public function pendientesAction (Request $request, $anio)
    {
        $user=$this->getUser();

        if($user) {

            $muestras = $muestrasPendientes = $muestrasPendientesSE = array();

            $rm = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');

            if (isset($anio) && $anio != '')
                $muestras = $rm->findMuestrasPendientes($anio);
            else
                $muestras = $rm->findMuestrasPendientes(date('Y'));

            foreach ($muestras as $muestra)
                array_push($muestrasPendientesSE, $muestra->crearMuestraPendiente());

            // Encriptar los id's
            foreach ($muestrasPendientesSE as $muestra) {
                $muestra['idMuestra'] = encriptador::mrcrypt_encrypt($muestra['idMuestra']);
                array_push($muestrasPendientes, $muestra);
            }

            return $this->render('LaboratorioPedidoBundle:Default:pendientes.html.twig', array(
                'muestras' => $muestrasPendientes
            ));
        }
    }

    public function autorizarTodasLasPendientesAction (Request $request)
    {
        $user=$this->getUser();

        if($user) {
            $estados = array();
            $muestrasPendientes = $request->request->get('muestrasPendientes');

            /*
            if (count($muestrasPendientes) == 1)
                var_dump($muestrasPendientes[0]['idMuestra']);
            else
                foreach ($muestrasPendientes as $muestra)
                    var_dump($muestra['idMuestra']);

            exit();
            */

            // Recorrer las muestras pendientes y autorizar cada una de las muestras
            if (count($muestrasPendientes) == 1)
                array_push($estados, $this->autorizarAction($request, $muestrasPendientes[0]['idMuestra'])); // Retornar estado
            else
                foreach ($muestrasPendientes as $muestra)
                    array_push($estados, $this->autorizarAction($request, $muestra['idMuestra'])); // Retornar estado

            // Chequear cuántas muestras fueron autorizadas
            $i = 0;
            while ($estados[$i] == 'Muestra autorizada' && $i < count($estados))
                $i++;

            // 'return'
            if ($i == count($estados))
                return new Response('Todas las muestras fueron autorizadas');
            elseif ($i < count($estados))
                return new Response('No se pudieron autorizar todas las muestras');
            else
                return new Response('No se autorizó ninguna muestra');
        }
    }
}