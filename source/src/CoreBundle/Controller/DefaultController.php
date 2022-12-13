<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    public function rubrosPrincipalesAction(Request $request){
        $data = array();
        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RubroPrincipal');
        $data = $em->findAllRubroPrincipalArray();
        $em->clear();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function tiposCedulasAction(Request $request){
        $data = array();
        $em = $this->getDoctrine()->getRepository('NotificacionesNotificacionesBundle:TipoCedula');
        $data = $em->findAllTiposArray();
        $em->clear();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function callesAction(Request $request){
        $data = array();
        $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:Calles');
        $data = $em->findAllCallesArray();
        $em->clear();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function deniedAction(Request $request){
        return $this->render('CoreBundle:Default:denied.html.twig');
    }

    public function deniedHomeAction(Request $request){
        return $this->render('CoreBundle:Default:deniedHome.html.twig');
    }

}
