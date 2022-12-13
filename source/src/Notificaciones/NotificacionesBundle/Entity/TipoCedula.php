<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoCedula
 *
 * @ORM\Table(name="tipo_cedula")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\TipoCedulaRepository")
 * @UniqueEntity("tipoCedula")
 */
class TipoCedula
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="TipoCedula", type="string", length=50, unique=true)
     */
    private $tipoCedula;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipoCedula
     *
     * @param string $tipoCedula
     *
     * @return TipoCedula
     */
    public function setTipoCedula($tipoCedula)
    {
        $this->tipoCedula = $tipoCedula;

        return $this;
    }

    /**
     * Get tipoCedula
     *
     * @return string
     */
    public function getTipoCedula()
    {
        return $this->tipoCedula;
    }

    public function __toString()
    {
        return $this->getTipoCedula();
    }
}
