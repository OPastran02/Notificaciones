<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * RubroPrincipal
 *
 * @ORM\Table(name="rubro_principal")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\RubroPrincipalRepository")
 * @UniqueEntity("rubroPrincipal")
 */
class RubroPrincipal
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
     * @ORM\Column(name="RubroPrincipal", type="string", length=50, unique=true)
     */
    private $rubroPrincipal;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=1095)
     * @ORM\Column(name="Frecuencia", type="smallint")
     */
    private $frecuencia;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     * @Assert\Type("bool")
     * @ORM\Column(name="Habilitado", type="boolean")
     */
    private $habilitado;


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
     * Set rubroPrincipal
     *
     * @param string $rubroPrincipal
     *
     * @return RubroPrincipal
     */
    public function setRubroPrincipal($rubroPrincipal)
    {
        $this->rubroPrincipal = $rubroPrincipal;

        return $this;
    }

    /**
     * Get rubroPrincipal
     *
     * @return string
     */
    public function getRubroPrincipal()
    {
        return $this->rubroPrincipal;
    }

    /**
     * Set frecuencia
     *
     * @param integer $frecuencia
     *
     * @return RubroPrincipal
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;

        return $this;
    }

    /**
     * Get frecuencia
     *
     * @return int
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * Set habilitado
     *
     * @param boolean $habilitado
     *
     * @return RubroPrincipal
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return bool
     */
    public function getHabilitado()
    {
        return $this->habilitado;
    }

    public function __toString()
    {
        return $this->getRubroPrincipal();
    }
}
