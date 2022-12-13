<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * EstablecimientoRazonSocial
 *
 * @ORM\Table(name="establecimientos_razonessociales")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\EstablecimientoRazonSocialRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"establecimiento", "razonSocial"},
 *     errorPath="razonSocial",
 *     message="La razon social ya esta vinculada."
 *)
 */
class EstablecimientoRazonSocial
{   

    

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="razonesSociales")     
     */
    protected $establecimiento;    

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="RazonSocial", inversedBy="establecimientos")     
     */
    protected $razonSocial; 

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     * @ORM\Column(name="fecha_inicio", type="date")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     * @ORM\Column(name="fecha_fin", type="date")
     */
    private $fechaFin;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     * @ORM\Column(name="fecha_creado", type="date")
     */
    private $fechaCreado;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="id_usuariocreador", type="integer")
     */
    private $idUsuariocreador;


    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return EstablecimientoRazonSocial
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return EstablecimientoRazonSocial
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return EstablecimientoRazonSocial
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Get fechaCreado
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Set idUsuariocreador
     *
     * @param integer $idUsuariocreador
     *
     * @return EstablecimientoRazonSocial
     */
    public function setIdUsuariocreador($idUsuariocreador)
    {
        $this->idUsuariocreador = $idUsuariocreador;

        return $this;
    }

    /**
     * Get idUsuariocreador
     *
     * @return integer
     */
    public function getIdUsuariocreador()
    {
        return $this->idUsuariocreador;
    }

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return EstablecimientoRazonSocial
     */
    public function setEstablecimiento(\Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento)
    {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\Establecimiento
     */
    public function getEstablecimiento()
    {
        return $this->establecimiento;
    }

    /**
     * Set razonSocial
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\RazonSocial $razonSocial
     *
     * @return EstablecimientoRazonSocial
     */
    public function setRazonSocial(\Establecimiento\EstablecimientoBundle\Entity\RazonSocial $razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\RazonSocial
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->idUsuariocreador = 1;        
    }    
}
