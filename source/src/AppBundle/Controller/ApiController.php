<?php

namespace AppBundle\Controller;

use AppBundle\Provider\AsiProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Inspecciones\InspeccionesBundle\Entity\Resultados;

use AppBundle\Service\UsigWS;
 
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;

use Encuesta\EncuestaBundle\Entity\ModeloEncuesta;
use Inspecciones\InspeccionesBundle\Entity\ResultadosFotos;  

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
  
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;   
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Session\Session;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Exception\ClientException;

use AppBundle\Service\JwtAuth;
use Usuario\UsuarioBundle\Entity\Usuarios;
  
class ApiController extends FOSRestController
{ 
     /**
     * Get a getConexion    
     * @return array
     *
     * @Rest\View()
     * @Rest\Get("/token/conexion",)
     */
    public function getConexion(){ 

        return new Response("Conexion exitosa");
    }    

    /**
    * Get a login   
    * @return array
    *
    * @Rest\View()     
    * @Rest\Post("/token/login",) 
    */
   public function login(Request $request, JwtAuth $jwt_auth)
   {
        try {
           $response = (new \GuzzleHttp\Client)->post('https://servicios-hml.gcba.gob.ar/ad/v1.2/cuentas/'.$request->get('cuit').'/validar', [
               'form_params' => [
                    'password' => $request->get('password')
                ],
                'headers' => [
                    'client_id' => $this->getParameter('client_id'),
                    'client_secret' => $this->getParameter('client_secret'),
                ]
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $token = $jwt_auth->crearToken($request->get('cuit'));
            $response['token'] = $token;
            $status= Response::HTTP_OK;
            $response['cuit'] = $request->get('cuit');

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('UsuarioUsuarioBundle:Usuarios')->findOneBy(['usuario' => $request->get('cuit')]);

            if (!$usuario) {
                $response['status'] = 'ERROR';
                $status= Response::HTTP_NOT_FOUND;
                $response['message'] = 'No existe el usuario';
                return new Response(json_encode($response));
            }

            $usuario->setAccessToken_apk($token);
            $em->flush();
              
        } catch (ClientException $e) {
            //$response = $e->getResponse();
            //$response = json_decode($response->getBody()->getContents(), true);
            $response['status'] = 'ERROR';
            // $status= Response::HTTP_INTERNAL_SERVER_ERROR;
            $response['message'] = $e->getMessage();
            $status = $e->getResponse()->getStatusCode();
        }

        // return new Response(json_encode($response));
        $res = new Response(json_encode($response));
        $res->setStatusCode($status);
        if($status != Response::HTTP_OK) {
            $res->headers->set('Error',$status);
        }
        return $res;
   }   
    
   /**
   * Get a validarToken    
   * @return array
   *
   * @Rest\View()
   * @Rest\Get("/token/validar",)
   */
   public function validarToken(Request $request, JwtAuth $jwt_auth)
   {
        $token = $request->headers->get('Authorization');
        $authCheck = $jwt_auth->validarToken($token);

        if($authCheck['status'] == 'OK'){
            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('UsuarioUsuarioBundle:Usuarios')->findOneBy(['access_token_apk' => $token]);
            if($usuario){
                $response['status'] = 'OK';
                $status= Response::HTTP_OK;
                $response['message'] = 'Token válido';
            }else {
                $response['status'] = 'ERROR';
                $status= Response::HTTP_NOT_FOUND;
                $response['message'] = 'Token no encontrado';
            }
        } else {
            $response['status'] = 'ERROR';
            $status= Response::HTTP_FORBIDDEN;
            $response['message'] = 'El token es inválido';
            $response['detalle'] = $authCheck['msg'];
        }
        
        $res = new Response(json_encode($response));
        $res->setStatusCode($status);
        if($status != Response::HTTP_OK) {
            $res->headers->set('Error',$status);
        }
        return $res;
   }

   /**
   * Get a validarToken    
   * @return array
   *
   * @Rest\View()
   * @Rest\Get("/token/actualizar",)
   */
  public function actualizarToken(Request $request, JwtAuth $jwt_auth)
    {
        $token = $request->headers->get('Authorization');
        $authCheck = $jwt_auth->validarToken($token);

        if($authCheck['status'] == 'OK'){

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('UsuarioUsuarioBundle:Usuarios')->findOneBy(['access_token_apk' => $token]);

            if ($usuario) {
                $new_token = $jwt_auth->crearToken($authCheck['obj']->cuit);
                $response['status'] = 'OK';
                $status= Response::HTTP_OK;
                $response['message'] = 'Token actualizado correctamente';
                $response['new_token'] = $new_token;
                //$response['obj'] = json_decode(json_encode($authCheck['obj']), true);

                $usuario->setAccessToken_apk($new_token);
                $em->flush();

            } else {                 
                $response['status'] = 'ERROR';
                $status= Response::HTTP_NOT_FOUND;
                $response['message'] = 'Token no encontrado';
            }       
        } else {
            $response['status'] = 'ERROR';
            $status= Response::HTTP_FORBIDDEN;
            $response['message'] = 'El token es inválido';            
            $response['detalle'] = $authCheck['msg'];
        }
        
        $res = new Response(json_encode($response));
        $res->setStatusCode($status);
        if($status != Response::HTTP_OK) {
            $res->headers->set('Error',$status);
        }
        return $res;
   }
    
    /**
     * Get a logout   
     * @return array
     *
     * @Rest\View()     
     * @Rest\Get("/token/logout",) 
     */
    public function logout(Request $request, JwtAuth $jwt_auth)
    {   
        $token = $request->headers->get('Authorization');
        $authCheck = $jwt_auth->validarToken($token);

        if($authCheck['status'] == 'OK'){  

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('UsuarioUsuarioBundle:Usuarios')->findOneBy(['access_token_apk' => $token]);

            if ($usuario) { 
                $usuario->setAccessToken_apk('');
                $em->flush();
                $response['status'] = 'OK';
                $status= Response::HTTP_OK;
                $response['message'] = 'Cierre de sesión';
            } else {                 
                $response['status'] = 'ERROR';
                $status= Response::HTTP_NOT_FOUND;
                $response['message'] = 'Token no encontrado';
            } 
 
        } else {
            $response['status'] = 'ERROR';
            $status= Response::HTTP_FORBIDDEN;
            $response['message'] = 'El token es inválido';
            $response['detalle'] = $authCheck['msg'];
        }
        
        $res = new Response(json_encode($response));
        $res->setStatusCode($status);
        if($status != Response::HTTP_OK) {
            $res->headers->set('Error',$status);
        }
        return $res;
    }    
}


