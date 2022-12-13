<?php

namespace Reportes\ReportesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Encuesta\EncuestaBundle\Entity\GrupoPreguntas;
use Establecimiento\EstablecimientoBundle\Entity\Rubro;
use Establecimiento\EstablecimientoBundle\Entity\RubroPrincipal;
use Establecimiento\EstablecimientoBundle\Entity\Estado;

use Reportes\ReportesBundle\Form\ReportesFiltroType;

class ReportesController extends Controller
{
    public function filtroReportesAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
        	$datos = array();
            $grupoPreguntas = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:GrupoPreguntas')->findall();
            $rubro = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Rubro')->findall();
            $rubroPrincipal = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal')->findall();
            $estados = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado')->findall();

           /* var_dump($grupoPreguntas);
            exit();*/

            $form = $this->createForm(ReportesFiltroType::class,$datos,array('method' => 'POST','gruposPregunta' => $grupoPreguntas,'action' => $this->generateUrl('reportes_reportes_homepage') ));
            $form->handleRequest($request);

            return $this->render('ReportesReportesBundle:Default:index.html.twig' , array('form' => $form->createview()));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }


    }

    public function filtroReportesSeleccionAction(Request $request)
    {
        $user=$this->getUser();

        if($user){
            $datos = array();
            $grupoPreguntas = $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:GrupoPreguntas')->findall();
            $rubro = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Rubro')->findall();
            $rubroPrincipal = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal')->findall();
            $estados = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Estado')->findall();

           /* var_dump($grupoPreguntas);
            exit();*/

            $form = $this->createForm(ReportesFiltroType::class,$datos,array('method' => 'POST','gruposPregunta' => $grupoPreguntas,'action' => $this->generateUrl('reportes_reportes_homepage') ));
            $form->handleRequest($request);

            return $this->render('ReportesReportesBundle:Default:seleccion.html.twig' , array('form' => $form->createview()));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }
}
