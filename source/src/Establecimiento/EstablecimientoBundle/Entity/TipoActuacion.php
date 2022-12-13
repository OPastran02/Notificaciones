<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoActuacion
 *
 * @ORM\Table(name="tipo_actuacion")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\TipoActuacionRepository")
 * @UniqueEntity("tipoActuacion")
 */
class TipoActuacion
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
     * @ORM\Column(name="tipoActuacion", type="string", length=2, unique=true)
     */
    private $tipoActuacion;


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
     * Set tipoActuacion
     *
     * @param string $tipoActuacion
     *
     * @return TipoActuacion
     */
    public function setTipoActuacion($tipoActuacion)
    {
        $this->tipoActuacion = $tipoActuacion;

        return $this;
    }

    /**
     * Get tipoActuacion
     *
     * @return string
     */
    public function getTipoActuacion()
    {
        return $this->tipoActuacion;
    }

    public function __toString()
    {
        return $this->getTipoActuacion();
    }
}
