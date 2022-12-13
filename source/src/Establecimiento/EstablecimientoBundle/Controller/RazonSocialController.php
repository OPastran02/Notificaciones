<?php

namespace Establecimiento\EstablecimientoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Doctrine\Common\Collections\ArrayCollection;

use Establecimiento\EstablecimientoBundle\Entity\RazonSocial;
use Establecimiento\EstablecimientoBundle\Form\RazonSocialType;

use AppBundle\Service\UsigWS;
use CoreBundle\Logic\encriptador;

class RazonSocialController extends Controller
{
    public function razonSocialAction( $id = '0', Request $request)
    {
        $user=$this->getUser();

        if($user){
            if ($id != '0'){             
                $id=encriptador::mrcrypt_decrypt($id);            
                $id=(int)$id;            
            }else{
                $id=0;
            }
            $usig = $this->get(UsigWS::class);
            $razonSocial = new RazonSocial();
            $direccionesListaOriginales = array();
            $direccionesLista = array();

            if($id > 0){
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RazonSocial');
                $razonSocial = $em->findOneById($id);

                if (!$razonSocial) {
                    throw $this->createNotFoundException('No razon social found');
                }
                foreach ($razonSocial->getDirecciones() as $direccion) {
                    array_push($direccionesListaOriginales,$direccion);
                }
            }

            $form = $this->createForm(RazonSocialType::class,$razonSocial,array('method' => 'POST'));

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    if( $this->validarCuit($razonSocial->getCuit()) == false ){
                        $this->addFlash('error','CUIT INCORRECTO');
                        return $this->render('EstablecimientoEstablecimientoBundle:Default:razonSocial.html.twig' , array('form' => $form->createview() ));
                    }

                    
                    $em = $this->getDoctrine()->getManager();                
                    
                        foreach ($razonSocial->getDirecciones() as $direccion) {
                            array_push($direccionesLista,$direccion);
                        }

                        $razonSocial->setDirecciones(array());

                        if($id==0){
                            $em->persist($razonSocial);
                        }

                        if($id > 0){
                            foreach ($direccionesListaOriginales as $direccion) {
                                if(!in_array($direccion,$direccionesLista) ){
                                    $em->remove($direccion);
                                }
                            }
                        }

                        foreach ($direccionesLista as $key => $direccion) {
                            if($usig->normalizarDireccion($direccion,$messageError,$messageAmbiguedad)){
                                $direccion->setRazonSocial($razonSocial);
                                $em->persist($direccion);
                            }
                        }

                        $em->persist($razonSocial);
                        $em->flush();

                        $this->addFlash('success','GUARDADO');
                        $em->getConnection()->close();
                        $em->clear();
                        return $this->redirectToRoute('establecimiento_establecimiento_razonsocial', array('id'=> encriptador::mrcrypt_encrypt($razonSocial->getId())));
                    }

                }
            if($id > 0){
                $em->clear();
            }
            
            return $this->render('EstablecimientoEstablecimientoBundle:Default:razonSocial.html.twig' , array('form' => $form->createview() ));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }

        
    }
    
    public function buscarRazonSocialAction($cuit,$id){
        $user=$this->getUser();

        if($user){

            $id=encriptador::mrcrypt_decrypt($id);
            $id=(int)$id;

            $razonSocial = new RazonSocial();

            if($cuit != false){
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RazonSocial');
                $razonSocial = $em->findOneByCuit($cuit);   
                $em->clear();
                if (!$razonSocial) {
                    throw $this->createNotFoundException('No adress found');
                }
            }

            if($id != false){
                $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RazonSocial');
                $razonSocial = $em->findOneById($id);   
                $em->clear();
                if (!$razonSocial) {
                    throw $this->createNotFoundException('No adress found');
                }
            }        

            $response = new Response(json_encode($razonSocial));                
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    private function validarCuit( $cuit ){
        $cuit = preg_replace( '/[^\d]/', '', (string) $cuit );
        if( strlen( $cuit ) != 11 ){
            return false;
        }
        $acumulado = 0;
        $digitos = str_split( $cuit );
        $digito = array_pop( $digitos );

        for( $i = 0; $i < count( $digitos ); $i++ ){
            $acumulado += $digitos[ 9 - $i ] * ( 2 + ( $i % 6 ) );
        }
        $verif = 11 - ( $acumulado % 11 );
        $verif = $verif == 11? 0 : $verif;

        return $digito == $verif;
    }

    public function getRazonesSocialesAction(Request $request,$search)
    {
        $user=$this->getUser();

        if($user){
            $respuesta = array();

            $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RazonSocial');
            $rs = $em->findRazonesSocialesFront($search);        

            return new Response(json_encode($rs));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function getRazonesSocialesTableAction(Request $request,$search)
    {
        $user=$this->getUser();

        if($user){
            $respuesta = array();

            $em = $this->getDoctrine()->getRepository('EstablecimientoEstablecimientoBundle:RazonSocial');
            $rs = $em->findRazonesSocialesTableFront($search);        

            return new Response(json_encode($rs));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

}

