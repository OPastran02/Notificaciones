<?php
namespace AppBundle\Controller;

use AppBundle\Provider\AsiProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Session\Session;


class ASIController extends Controller
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/secured/login", name="connect_asi_ad")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    { 
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');
        $clientRegistry = $this->get('knpu.oauth2.registry');
        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('asi_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                 // the scopes you want to access
            ])
            ;
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/secured/check", name="connect_asi_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)
        /*var_dump($request->request->all());
        exit();*/

        /** @var \KnpU\OAuth2ClientBundle\Client\OAuth2Client $client */
        $client = $clientRegistry->getClient('asi_main');

        $token = $client->getAccessToken();
        $valores = $token->getValues();

        //$provider = $client->getOAuth2Provider();
        //var_dump($provider->getResourceOwner($token));

        /*
        $client2 = new Client(['base_uri' => 'https://oauth2-server-hml.apps.buenosaires.gob.ar/']);

        $resp = $client2->request('GET', '/oidc/userinfo', [
            $token
        ]);

        var_dump($resp);
        exit();*/


        try {
            // the exact class depends on which provider you're using
            //var_dump($token);

            //$user = $client->fetchUser();


            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            return true;
            var_dump($user); exit();
            // ...
        } catch (IdentityProviderException $e) {

            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}