<?php

namespace Usuario\UsuarioBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Usuario\UsuarioBundle\Entity\Usuarios;
use Usuario\UsuarioBundle\Form\UsuariosType;
use Usuario\UsuarioBundle\Form\contraseniaType;
use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use CoreBundle\Logic\encriptador;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class UsuarioController extends Controller
{

    public function indexAction()
    {
      $user=$this->getUser();

        if($user){
          if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')){
            return $this->render('UsuarioUsuarioBundle:Usuario:usuarios.html.twig');
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
           }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }


    public function tablaUsuarioAction(Request $request)
    {

      $user=$this->getUser();

        if($user){
          if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')){
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaUsuario.yml"));

            //return new Response($yaml);
            
            $TablaUsuarios=new TablaAjax($request,$em,$yaml);
            $TablaUsuarios->setSpecialConditions("");

            $response = new Response($TablaUsuarios->Initialize());
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

    public function excelUsuarioAction(Request $request)
    {  
      $user=$this->getUser();

        if($user){
          if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')){
            $ruta = realpath($this->get('kernel')->getRootDir().'/../web/temp/');
            $nombreArchivo= 'Usuario'.date("d_m_Y").'.xls';
            $nombresheet="Usuario";
            $data = $request->request->all();
            
            $em = $this->get('doctrine')->getManager();
            $em = $this->get('doctrine')->getManager('default');
            $em = $this->get('doctrine.orm.default_entity_manager');

            $yaml=Yaml::parse(file_get_contents(__DIR__."/../Model/TablaUsuario.yml"));

            //return new Response($yaml);
            
            $TablaUsuarios=new TablaAjax($request,$em,$yaml,$data["arraybusqueda"]);
            $TablaUsuarios->setSpecialConditions("");

            $arrayUsuarios=$TablaUsuarios->getQueryTable();
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $letra="a";
            $numero=1;
            foreach ($arrayUsuarios as $key => $value) {
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


    public function datosUsuarioAction(Request $request, $id='0')
    {
        $user=$this->getUser();

        if($user){
            if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')){
                if ($id != '0'){
                    $id=encriptador::mrcrypt_decrypt($id);
                    $id=(int)$id;
                }else{
                    $id=0;
                }
                $Usuario = new Usuarios();
                $encoderFactory=$this->get('security.encoder_factory');
                $encoder= $encoderFactory->getencoder($Usuario);

                if ($id>0){
                     $repository = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');
                     $Usuario = $repository->findOneByid($id);
                }else{
                  $Usuario->setPassword("a");
                  $Usuario->setAccessToken("E");
                }

                $form = $this->createForm(UsuariosType::class,$Usuario,array('method' => 'POST'));

                $form->handleRequest($request);
                if($request->isMethod('POST') ){
                    if ($form->isValid()) {
                        if ($id==0){
                          $salt = $form["usuario"]->getData(); // this should be different for every user
                          $password = $encoder->encodePassword('123456', $salt);
                          $Usuario->setPassword($password);
                          $Usuario->setUltimaConexion(new \DateTime('now'));
                          $Usuario->setFechaCreado(new \DateTime('now'));
                          $Usuario->setIdUsuarioCreador($this->getUser());
                          $Usuario->setFechaModificado(new \DateTime('now'));
                          $Usuario->setIdUsuarioModificador($this->getUser());
                        }

                        $em = $this->getDoctrine()->getManager();

                        $em->persist($Usuario);
                        $em->flush();
                        $em->getConnection()->close();
                        $em->clear();

                        $this->addFlash("success", "Usuario guardado con éxito");
                        return $this->render('UsuarioUsuarioBundle:Usuario:datos.html.twig' , array('form' => $form->createview()));
                    }else{
                        $this->addFlash("error", "No se pudieron guardar los cambios");
                    }
                }
                return $this->render('UsuarioUsuarioBundle:Usuario:datos.html.twig' , array('form' => $form->createview(),'id'=>$Usuario->getId()  ));
            }else{
                return $this->render('CoreBundle:Default:deniedHome.html.twig');
            }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function changePasswordAction(Request $request)
    {
        $user=$this->getUser();

        if($user) {
            $Usuario = new Usuarios();
            $data = array();
            $form = $this->createForm(contraseniaType::class, $data, array('method' => 'POST'));
            $form->handleRequest($request);
            $id = $this->getUser()->getId();

            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');
                    $Usuario = $em->findOneById($id);

                    $encoderFactory = $this->get('security.encoder_factory');
                    $encoder = $encoderFactory->getencoder($Usuario);

                    $salt = $Usuario->getUsuario();
                    $passwordencodeado = $encoder->encodePassword($form["contrasenia"]->getData(), $salt);

                    $Usuario->setPassword($passwordencodeado);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($Usuario);
                    $em->flush();
                    $em->getConnection()->close();
                    $em->clear();
                    $this->addFlash("success", "Contraseña cambiada exitosamente");
                    return $this->render('UsuarioUsuarioBundle:Usuario:changePassword.html.twig', array('form' => $form->createview()));

                } else {
                    $this->addFlash("error", "No se pudieron guardar los cambios");
                }
            }
            //print_r("asdf");
            return $this->render('UsuarioUsuarioBundle:Usuario:changePassword.html.twig', array('form' => $form->createview(), 'id' => $Usuario->getId()));
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    public function resetPasswordAction(Request $request)
    { 
      $user=$this->getUser();

        if($user){
          if ($this->isGranted('ROLE_NOTIFICACIONES_ADMIN')){
            if ($request->request->get('confirmar')=="true"){
              $id=$request->request->get('id');
              $Usuario = new Usuarios();

              $repository = $this->getDoctrine()->getRepository('UsuarioUsuarioBundle:Usuarios');
              $Usuario = $repository->findOneByid($id);

              if(!$Usuario){
                  throw $this->createNotFoundException('No se encontro el Usuario');
              }

              $encoderFactory = $this->get('security.encoder_factory');
              $encoder = $encoderFactory->getEncoder($Usuario);

              $salt = $Usuario->getUsuario(); // this should be different for every user
              $password = $encoder->encodePassword('123456', $salt);

              $Usuario->setPassword($password);
              $em = $this->getDoctrine()->getManager();    
              $em->persist($Usuario);
              $em->flush();
              $em->getConnection()->close();
              $em->clear();
              return new Response("Password reseteado");
            }else{
              return new Response("cancelado");
            }
          }else{
            return $this->render('CoreBundle:Default:deniedHome.html.twig');
          }
        }else{
            return $this->redirect($this->generateUrl('usuario_usuario_login'));
        }
    }

    //public function loginAction($captcha = false)
    public function loginAction()
    {
        //return $this->redirect($this->generateUrl('connect_facebook_start'));
      $authUtils=$this->get('security.authentication_utils');

      return $this->render('UsuarioUsuarioBundle:Usuario:login.html.twig',array(
          'last_username' => $authUtils->getLastUsername(),
          'error'=>$authUtils->getLastAuthenticationError(),
          //'captcha'=>$captcha
        ));
    }

    public function logoutAction(Request $request)
    {
    }

    public function loginCheckAction(){

    }

}
