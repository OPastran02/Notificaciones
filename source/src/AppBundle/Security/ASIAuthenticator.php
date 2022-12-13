<?php


namespace AppBundle\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;




class ASIAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_asi_check';
    }

    public function getCredentials(Request $request)
    {

        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        if (!$this->supports($request)) {
            return null;
        }else{
            return $this->fetchAccessToken($this->getClient());
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var AccessToken $credentials */
        $token = $credentials->getToken();
        $valores = $credentials->getValues();

        $existingUser = $this->em->getRepository('UsuarioUsuarioBundle:Usuarios')
            ->findOneBy(['usuario' => $valores['user_id']]);

        if ($existingUser) {
            $existingUser->setAccessToken($token);
            $this->em->persist($existingUser);
            $this->em->flush();
            return $existingUser;
        }

    }

    private function getClient()
    {
        return $this->clientRegistry
            // "facebook_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('asi_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app

        $targetUrl = $this->router->generate('home_home_homepage');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //$message = strtr($exception->getMessageKey(), $exception->getMessageData());

        //return new Response($message, Response::HTTP_FORBIDDEN);
        $targetUrl = $this->router->generate('connect_asi_ad');

        return new RedirectResponse($targetUrl);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/secured/login/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

}