<?php

namespace Usuario\UsuarioBundle\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use ReCaptcha\ReCaptcha;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LoginListener
{
  private $router;

  public function __construct(RouterInterface $router)
  {
      $this->router = $router;
  }


  public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
  {
        $usuario=$event->getAuthenticationToken()->getUser();
        $usuario->setUltimaConexion(new \DateTime());
  }

  public function onKernelRequest(GetResponseEvent $event)
  {
    /*$request = $event->getRequest();
    $route = $request->get('_route');      
    
    if($route == "usuario_usuario_login_check"){
      $recaptcha = new ReCaptcha('6LeOE60UAAAAABeloWNCwpmK_ZrSq7Kp4tg37A9b');
      $captcha = $request->request->get('g-recaptcha-response');
      $clientIp = $request->getClientIp();
      $resp = $recaptcha->verify($captcha,$clientIp);

      if (!$resp->isSuccess()) {
          $url = $this->router->generate('usuario_usuario_login', array('captcha'=> true));
          $event->setResponse(new RedirectResponse($url));
      }
    }*/
  }
}