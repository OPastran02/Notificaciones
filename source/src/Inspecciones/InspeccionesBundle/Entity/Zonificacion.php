<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Zonificacion
 *
 * @ORM\Table(name="zonificacion")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\ZonificacionRepository")
 */
class Zonificacion
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
     * @ORM\Column(name="zonificacion", type="string", length=45, nullable=true)
     */
    private $zonificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="ASATIPO", type="string", length=10, nullable=true)
     */
    private $asatipo;

    /**
     * @var string
     *
     * @ORM\Column(name="ASAETIPO", type="string", length=10, nullable=true)
     */
    private $asaetipo;

    /**
     * @var int
     *
     * @ORM\Column(name="DIURNO_HABITABLE", type="integer",nullable=true)
     */
    private $diurnoHabitable;

    /**
     * @var int
     *
     * @ORM\Column(name="DIURNO_SERVICIO", type="integer",nullable=true)
     */
    private $diurnoServicio;

    /**
     * @var int
     *
     * @ORM\Column(name="NOCTURNO_HABITABLE", type="integer",nullable=true)
     */
    private $nocturnoHabitable;

    /**
     * @var int
     *
     * @ORM\Column(name="NOCTURNO_SERVICIO", type="integer",nullable=true)
     */
    private $nocturnoServicio;
    
    /**
     * @var int
     *
     * @ORM\Column(name="DIURNO_EXTERIOR", type="integer",nullable=true)
     */
    private $diurnoExterior;

    /**
     * @var int
     *
     * @ORM\Column(name="NOCTURNO_EXTERIOR", type="integer",nullable=true)
     */
    private $nocturnoExterior;


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
     * Set zonificacion
     *
     * @param string $zonificacion
     *
     * @return Zonificacion
     */
    public function setZonificacion($zonificacion)
    {
        $this->zonificacion = $zonificacion;
    
        return $this;
    }

    /**
     * Get zonificacion
     *
     * @return string
     */
    public function getZonificacion()
    {
        return $this->zonificacion;
    }

    /**
     * Set asatipo
     *
     * @param string $asatipo
     *
     * @return Zonificacion
     */
    public function setAsatipo($asatipo)
    {
        $this->asatipo = $asatipo;
    
        return $this;
    }

    /**
     * Get asatipo
     *
     * @return string
     */
    public function getAsatipo()
    {
        return $this->asatipo;
    }

    /**
     * Set asaetipo
     *
     * @param string $asaetipo
     *
     * @return Zonificacion
     */
    public function setAsaetipo($asaetipo)
    {
        $this->asaetipo = $asaetipo;
    
        return $this;
    }

    /**
     * Get asaetipo
     *
     * @return string
     */
    public function getAsaetipo()
    {
        return $this->asaetipo;
    }

    /**
     * Set diurnoHabitable
     *
     * @param integer $diurnoHabitable
     *
     * @return Zonificacion
     */
    public function setDiurnoHabitable($diurnoHabitable)
    {
        $this->diurnoHabitable = $diurnoHabitable;
    
        return $this;
    }

    /**
     * Get diurnoHabitable
     *
     * @return integer
     */
    public function getDiurnoHabitable()
    {
        return $this->diurnoHabitable;
    }

    /**
     * Set diurnoServicio
     *
     * @param integer $diurnoServicio
     *
     * @return Zonificacion
     */
    public function setDiurnoServicio($diurnoServicio)
    {
        $this->diurnoServicio = $diurnoServicio;
    
        return $this;
    }

    /**
     * Get diurnoServicio
     *
     * @return integer
     */
    public function getDiurnoServicio()
    {
        return $this->diurnoServicio;
    }

    /**
     * Set nocturnoHabitable
     *
     * @param integer $nocturnoHabitable
     *
     * @return Zonificacion
     */
    public function setNocturnoHabitable($nocturnoHabitable)
    {
        $this->nocturnoHabitable = $nocturnoHabitable;
    
        return $this;
    }

    /**
     * Get nocturnoHabitable
     *
     * @return integer
     */
    public function getNocturnoHabitable()
    {
        return $this->nocturnoHabitable;
    }

    /**
     * Set nocturnoServicio
     *
     * @param integer $nocturnoServicio
     *
     * @return Zonificacion
     */
    public function setNocturnoServicio($nocturnoServicio)
    {
        $this->nocturnoServicio = $nocturnoServicio;
    
        return $this;
    }

    /**
     * Get nocturnoServicio
     *
     * @return integer
     */
    public function getNocturnoServicio()
    {
        return $this->nocturnoServicio;
    }

    /**
     * Set diurnoExterior
     *
     * @param integer $diurnoExterior
     *
     * @return Zonificacion
     */
    public function setDiurnoExterior($diurnoExterior)
    {
        $this->diurnoExterior = $diurnoExterior;
    
        return $this;
    }

    /**
     * Get diurnoExterior
     *
     * @return integer
     */
    public function getDiurnoExterior()
    {
        return $this->diurnoExterior;
    }

    /**
     * Set nocturnoExterior
     *
     * @param integer $nocturnoExterior
     *
     * @return Zonificacion
     */
    public function setNocturnoExterior($nocturnoExterior)
    {
        $this->nocturnoExterior = $nocturnoExterior;
    
        return $this;
    }

    /**
     * Get nocturnoExterior
     *
     * @return integer
     */
    public function getNocturnoExterior()
    {
        return $this->nocturnoExterior;
    }
}
