<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Usuario\UsuarioBundle\Entity\Usuarios;


class AccessControllerListener
{

    private $requestStack;
    protected $container;
    protected $router;

    public function __construct(ContainerInterface $container,RequestStack $requestStack,Router $router)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->router = $router;        
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        //ini_set('memory_limit', '-1');     
        $securityContext = $this->container->get('security.token_storage');
        /** @var PostAuthenticationGuardToken $token */
        $token = $securityContext->getToken();
        $request = $this->requestStack->getCurrentRequest();

        if($token){
            $esInspector = true;
            /** @var Usuarios $usuario */
            $usuario = $token->getUser(); 
            if($usuario && $usuario != 'anon.'){
                $currentRoute = $request->attributes->get('_route');
                if(($usuario->getHabilitado() == 0 || $usuario->getHabilitado() == false) && $currentRoute!= 'usuario_usuario_log_out'){
                    $response = new Response($this->container->get('templating')->render('UsuarioUsuarioBundle:Usuario:accessDenied.html.twig',[]));
                    /*$url = $this->router->generate('usuario_usuario_log_out');
                    $response = new RedirectResponse($url);*/
                    $event->setResponse($response);
                }else{
                    $actual_link = "$_SERVER[REQUEST_URI]";
                    $request->getPathInfo();

                    $roles = $usuario->getTipoUsuario();

                    foreach ($roles as $rol) {
                        if($rol->getId() != 2){
                            $esInspector = false;
                        }
                    }
                    if($esInspector){
                        switch ($currentRoute) {
                            case 'inspecciones_inspecciones_page_inspectores_sade_table':
                            case 'inspecciones_inspecciones_inspectores_sade_table':
                            case 'inspecciones_inspecciones_excel_inspectores_sade_table':
                            case 'inspecciones_inspecciones_save_inspectores_sade_table':
                            case 'usuario_usuario_changePassword':
                            case 'inspecciones_inspecciones_alertainspecciones':
                            case 'inspecciones_inspecciones_alertareinspecciones':
                            case 'usuario_usuario_log_out':
                            case 'inspecciones_inspecciones_pdfchecklist':
                                break;
                            default:
                                $url = $this->router->generate('inspecciones_inspecciones_page_inspectores_sade_table');
                                $response = new RedirectResponse($url);
                                $event->setResponse($response);
                                break;
                       }
                    }
                }
            }
            
        }
        //var_dump($actual_link);        
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {        
        $request = $this->requestStack->getCurrentRequest();        
        $pos = strpos($_SERVER["REQUEST_URI"],"excel");        
        $archivosPendientes = array();

        //$file = stat(__DIR__."/../../../web/temp/excelInspeccion02_06_2020 - copia.xls");

        if($pos !== false ){
            $files = glob(__DIR__."/../../../web/temp/*.xls");
            foreach($files as $file){                
                if($this->readyToRead($file)){
                    try {
                        unlink($file);
                    } catch (\Throwable $e) {
                        array_push($archivosPendientes,$file);
                    }
                }             
            }            
        }
    }

    private function readyToRead($file){
       return ((time() - filemtime($file)) > 1000 ) ? true : false;
   }
}
