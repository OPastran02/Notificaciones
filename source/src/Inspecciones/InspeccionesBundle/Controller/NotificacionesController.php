<?php

namespace Inspecciones\InspeccionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use CoreBundle\Logic\UsigWS;
use CoreBundle\Logic\JsonValidator;

use Establecimiento\EstablecimientoBundle\Entity\Direccion;
use Establecimiento\EstablecimientoBundle\Entity\Calles;
use Establecimiento\EstablecimientoBundle\Entity\Establecimiento;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;
use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Inspecciones\InspeccionesBundle\Entity\Inspeccion;
use Inspecciones\InspeccionesBundle\Entity\Circuito;
use Usuario\UsuarioBundle\Entity\Usuarios;

use Inspecciones\InspeccionesBundle\Form\ProgramarInspeccionType;
use Inspecciones\InspeccionesBundle\Form\InspeccionType;
use Inspecciones\InspeccionesBundle\Form\ProgramarInspeccionSinEstablecimientoType;

use CoreBundle\Logic\encriptador;

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;



class NotificacionesController extends Controller
{
    public function alteraInspeccionVencidaAction(Request $request)
    {
        $user=$this->getUser();

        if($user){      
            if ($request->isMethod('POST')) {
                $idArea = $this->getUser()->getArea()->getId();            
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                if($idArea == 7){
                    $ordenesInspeccion = $em->inspeccionesSinDevolver();
                }else{
                    $ordenesInspeccion = $em->inspeccionesSinDevolver($idArea);
                }   
                $em->clear();                     
                return $this->render('InspeccionesInspeccionesBundle:Default:alertaInspeccionesVencidas.html.wtig.twig', array('ordenesInspeccion' => $ordenesInspeccion) );
            }else{
                throw $this->createNotFoundException('Page Not found');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function alteraReInspeccionAction(Request $request)
    {
        $user=$this->getUser();

        if($user){      
            if ($request->isMethod('POST')) {
                $idArea = $this->getUser()->getArea()->getId();            
                $em = $this->getDoctrine()->getRepository('InspeccionesInspeccionesBundle:OrdenInspeccion');
                
                $ordenesInspeccion = $em->alertaReInspeccion($idArea);
                
                $em->clear();                     
                return $this->render('InspeccionesInspeccionesBundle:Default:alertaReInspeccion.html.twig', array('ordenesInspeccion' => $ordenesInspeccion) );
            }else{
                throw $this->createNotFoundException('Page Not found');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }
}

