<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Actuacion
 *
 * @ORM\Table(name="actuacion")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\ActuacionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"tipo", "numero","reparticion","anio"},
 *     errorPath="numero",
 *     message="La actuación ya existe en el sistema."
 *)
 */
class Actuacion
{    

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="actuaciones")
     * @ORM\JoinColumn(name="Id_Establecimiento", referencedColumnName="id")
     */
    protected $establecimiento;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="Id_Establecimiento", type="integer")
     */
    private $idEstablecimiento;

    /**          
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TipoActuacion")
     * @ORM\Id
     */
    protected $tipo;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=9999999999)
     * @ORM\Column(name="numero", type="integer")
     * @ORM\Id
     */
    private $numero;

    /**
     * @Assert\NotBlank()     
     * @ORM\ManyToOne(targetEntity="Reparticion")
     * @ORM\Id
     */
    protected $reparticion;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=2020)
     * @ORM\Column(name="anio", type="integer")
     * @ORM\Id
     */
    private $anio;

    /**
     * @ORM\ManyToOne(targetEntity="ActuacionTipo")
     */
    protected $clasificacionActuacion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="Fecha_Creado", type="date")
     */
    private $fechaCreado;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Creador", type="integer")
     */
    private $idUsuarioCreador;
    

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Actuacion
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set anio
     *
     * @param integer $anio
     *
     * @return Actuacion
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return integer
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set tipo
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoActuacion $tipo
     *
     * @return Actuacion
     */
    public function setTipo(\Establecimiento\EstablecimientoBundle\Entity\TipoActuacion $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\TipoActuacion
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set reparticion
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Reparticion $reparticion
     *
     * @return Actuacion
     */
    public function setReparticion(\Establecimiento\EstablecimientoBundle\Entity\Reparticion $reparticion)
    {
        $this->reparticion = $reparticion;

        return $this;
    }

    /**
     * Set clasificacionActuacion
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\ActuacionTipo $tipo
     *
     * @return Actuacion
     */
    public function setClasificacionActuacion(\Establecimiento\EstablecimientoBundle\Entity\ActuacionTipo $clasificacionActuacion)
    {
        $this->clasificacionActuacion = $clasificacionActuacion;

        return $this;
    }

    /**
     * Get clasificacionActuacion
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\ActuacionTipo
     */
    public function getClasificacionActuacion()
    {
        return $this->clasificacionActuacion;
    }


    /**
     * Get reparticion
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\Reparticion
     */
    public function getReparticion()
    {
        return $this->reparticion;
    }    

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return Actuacion
     */
    public function setEstablecimiento(\Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento = null)
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
     * Set idEstablecimiento
     *
     * @param integer $idEstablecimiento
     *
     * @return Actuacion
     */
    public function setIdEstablecimiento($idEstablecimiento)
    {
        $this->idEstablecimiento = $idEstablecimiento;

        return $this;
    }

    /**
     * Get idEstablecimiento
     *
     * @return integer
     */
    public function getIdEstablecimiento()
    {
        return $this->idEstablecimiento;
    }    

    public function __toString()
    {
        return $this->getTipo()->getTipoActuacion()."-".$this->getNumero()."-".$this->getReparticion()->getReparticion()."-".$this->getAnio();
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Actuacion
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
     * Set idUsuarioCreador
     *
     * @param integer $idUsuarioCreador
     *
     * @return Actuacion
     */
    public function setIdUsuarioCreador($idUsuarioCreador)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

        return $this;
    }

    /**
     * Get idUsuarioCreador
     *
     * @return integer
     */
    public function getIdUsuarioCreador()
    {
        return $this->idUsuarioCreador;
    }

    /**
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();        
    }
}
