<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoTratamiento
 *
 * @ORM\Table(name="tipo_tratamiento")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\TipoTratamientoRepository")
 * @UniqueEntity("tipoTratamiento")
 */
class TipoTratamiento
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
     * @ORM\Column(name="tipoTratamiento", type="string", length=50, unique=true)
     */
    private $tipoTratamiento;


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
     * Set tipoTratamiento
     *
     * @param string $tipoTratamiento
     *
     * @return TipoTratamiento
     */
    public function setTipoTratamiento($tipoTratamiento)
    {
        $this->tipoTratamiento = $tipoTratamiento;

        return $this;
    }

    /**
     * Get tipoTratamiento
     *
     * @return string
     */
    public function getTipoTratamiento()
    {
        return $this->tipoTratamiento;
    }

    public function __toString()
    {
        return $this->getTipoTratamiento();
    }
}
