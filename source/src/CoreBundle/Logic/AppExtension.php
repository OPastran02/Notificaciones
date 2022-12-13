<?php
namespace CoreBundle\Logic;

use CoreBundle\Logic\encriptador;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('encriptar', array($this, 'encriptar')),
        );
    }

    public function encriptar($data)
    {
       $valor=encriptador::mrcrypt_encrypt($data);

        return $valor;
    }
}

?>
