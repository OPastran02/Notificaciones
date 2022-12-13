<?php

namespace Encuesta\EncuestaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EncuestaEncuestaBundle:Default:index.html.twig');
    }
}
