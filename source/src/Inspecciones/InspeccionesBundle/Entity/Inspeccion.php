<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Inspeccion
 *
 * @ORM\Table(name="inspeccion")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\InspeccionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Inspeccion
{

    /**     
     * @ORM\ManyToMany(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     * @ORM\JoinTable(name="inspeccion_usuario",
     *      joinColumns={@ORM\JoinColumn(name="inspeccion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     *      )
     */
    protected $inspectores;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenInspeccion", inversedBy="inspecciones")
     * @ORM\JoinColumn(name="orden_inspeccion_id", referencedColumnName="id")     
     */
    protected $ordenInspeccion;    

    /**
     * @var int
     *   
     * @Assert\NotBlank()  
     * @ORM\Column(name="orden_inspeccion_id", type="integer",nullable=false)
     */
    private $idOrdenInspeccion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="fecha_programado", type="date", nullable=true)
     */
    private $fechaProgramado;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_inspeccion", type="datetime", nullable=true)
     */
    private $fechaInspeccion;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_recepcion", type="date", nullable=true)
     */
    private $fechaRecepcion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Fecha_Creado", type="datetime")
     */
    private $fechaCreado;

    /**
     * @var int
     *          
     * @ORM\Column(name="Id_Usuario_Creador", type="integer")
     */
    private $idUsuarioCreador;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Fecha_Modificado", type="datetime")
     */
    private $fechaModificado;    

    /**
     * @var int
     *          
     * @ORM\Column(name="Id_Usuario_Modificador", type="integer")
     */
    private $idUsuarioModificador;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->inspectores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaProgramado
     *
     * @param \DateTime $fechaProgramado
     *
     * @return Inspeccion
     */
    public function setFechaProgramado($fechaProgramado)
    {
        $this->fechaProgramado = $fechaProgramado;

        return $this;
    }

    /**
     * Get fechaProgramado
     *
     * @return \DateTime
     */
    public function getFechaProgramado()
    {
        return $this->fechaProgramado;
    }

    /**
     * Set fechaInspeccion
     *
     * @param \DateTime $fechaInspeccion
     *
     * @return Inspeccion
     */
    public function setFechaInspeccion($fechaInspeccion)
    {
        $this->fechaInspeccion = $fechaInspeccion;

        return $this;
    }

    /**
     * Get fechaInspeccion
     *
     * @return \DateTime
     */
    public function getFechaInspeccion()
    {
        return $this->fechaInspeccion;
    }

    /**
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return Inspeccion
     */
    public function setFechaRecepcion($fechaRecepcion)
    {
        $this->fechaRecepcion = $fechaRecepcion;

        return $this;
    }

    /**
     * Get fechaRecepcion
     *
     * @return \DateTime
     */
    public function getFechaRecepcion()
    {
        return $this->fechaRecepcion;
    }    

    /**
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return Inspeccion
     */
    public function setOrdenInspeccion(\Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion = null)
    {
        $this->ordenInspeccion = $ordenInspeccion;

        return $this;
    }

    /**
     * Get ordenInspeccion
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion
     */
    public function getOrdenInspeccion()
    {
        return $this->ordenInspeccion;
    }

    /**
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->setIdUsuarioCreador(1);        
    }

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setFechaModificadoValue()
    {
        $this->fechaModificado = new \DateTime();        
        //$this->setIdUsuarioModificador(1);
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Inspeccion
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
     * @return Inspeccion
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
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return Inspeccion
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Get fechaModificado
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Set idUsuarioModificador
     *
     * @param integer $idUsuarioModificador
     *
     * @return Inspeccion
     */
    public function setIdUsuarioModificador($idUsuarioModificador)
    {
        $this->idUsuarioModificador = $idUsuarioModificador;

        return $this;
    }

    /**
     * Get idUsuarioModificador
     *
     * @return integer
     */
    public function getIdUsuarioModificador()
    {
        return $this->idUsuarioModificador;
    }

    /**
     * Add inspectore
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $inspectore
     *
     * @return Inspeccion
     */
    public function addInspectore(\Usuario\UsuarioBundle\Entity\Usuarios $inspectore)
    {
        $this->inspectores[] = $inspectore;

        return $this;
    }

    /**
     * Remove inspectore
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $inspectore
     */
    public function removeInspectore(\Usuario\UsuarioBundle\Entity\Usuarios $inspectore)
    {
        $this->inspectores->removeElement($inspectore);
    }

    /**
     * Get inspectores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInspectores()
    {
        return $this->inspectores;
    }
    
    public function setInspectores($inspectore)
    {
        $this->inspectores = $inspectore;
    }

    public function __toString()
    {
        return 'id: '.$this->id;
    }


}
