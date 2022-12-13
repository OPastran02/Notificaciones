<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoResiduos
 *
 * @ORM\Table(name="tipo_residuos")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\TipoResiduosRepository")
 * @UniqueEntity("codigoResiduo")
 * @UniqueEntity("tipoResiduo")
 */
class TipoResiduos
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
     * @ORM\Column(name="codigoResiduo", type="string", length=5, unique=true)
     */
    private $codigoResiduo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="tipoResiduo", type="string", length=150,unique=true)
     */
    private $tipoResiduo;


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
     * Set codigoResiduo
     *
     * @param string $codigoResiduo
     *
     * @return TipoResiduos
     */
    public function setCodigoResiduo($codigoResiduo)
    {
        $this->codigoResiduo = $codigoResiduo;

        return $this;
    }

    /**
     * Get codigoResiduo
     *
     * @return string
     */
    public function getCodigoResiduo()
    {
        return $this->codigoResiduo;
    }

    /**
     * Set tipoResiduo
     *
     * @param string $tipoResiduo
     *
     * @return TipoResiduos
     */
    public function setTipoResiduo($tipoResiduo)
    {
        $this->tipoResiduo = $tipoResiduo;

        return $this;
    }

    /**
     * Get tipoResiduo
     *
     * @return string
     */
    public function getTipoResiduo()
    {
        return $this->tipoResiduo;
    }

    public function __toString()
    {
        return $this->getCodigoResiduo().'|'.$this->getTipoResiduo();
    }
}
