<?php
namespace AppBundle\Service;
use Firebase\JWT\JWT;
use  Firebase\JWT\Key;

class JwtAuth{

    public $key = 'GCBA_SIGA_INDRA_2022_TKT_VF1cy9N0MUSAdx9D6NpqNw';
    
    public function __construct(){

    }

    public function crearToken($cuit){
        $token = [
            'cuit' => $cuit,
            'iat' => time(),
            'exp' => time() + (4 * 60 * 60)
        ];
        $jwt = JWT::encode($token, $this->key, 'HS256');
        return $jwt;
    }

    public function validarToken($token){
        $arRes = array();
        $arRes['status'] = 'ERROR';
        try{
            $obj = JWT::decode($token, new Key($this->key, 'HS256'));
        }catch(\Firebase\JWT\ExpiredException $e){
            $arRes['status'] = 'ERROR';
            $arRes['msg'] = 'El token ha expirado.';
        }catch(\Firebase\JWT\SignatureInvalidException $e){
            $arRes['status'] = 'ERROR';
            $arRes['msg'] = 'El token es inválido.';
        }catch(\UnexceptedValueException $e){
            $arRes['status'] = 'ERROR';
            $arRes['msg'] = $e->getMessage();
        }catch(\DomainException $e){
            $arRes['status'] = 'ERROR';
            $arRes['msg'] = $e->getMessage();
        }

        if(isset($obj) && !empty($obj) && is_object($obj) && isset($obj->cuit)){
            $arRes['status'] = 'OK';
            $arRes['obj'] = $obj;
        } else {
            $arRes['status'] = 'ERROR';
            $arRes['msg'] = 'Token inválido';
        }
        return $arRes;
    }
}
 