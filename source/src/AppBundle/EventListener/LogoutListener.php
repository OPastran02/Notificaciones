<?php


namespace AppBundle\EventListener;


use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface {


    public function logout(Request $request, Response $response, TokenInterface $token) {
        /*$authorization = "Authorization: Bearer ".$token->getUser()->getAccessToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER , true);
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2-server-hml.apps.buenosaires.gob.ar/oidc/logout');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , 2);
        $result = curl_exec($ch);
        curl_close($ch);*/

        /*$client = new Client(['base_uri' => 'https://oauth2-server-hml.apps.buenosaires.gob.ar']);

        $headers = [
            'Authorization' => 'Bearer ' . $token->getUser()->getAccessToken(),
            'Accept'        => 'application/json',
        ];
        $asd = $client->request('POST', '/oidc/logout', [
            $headers
        ]);*/
    }
}
