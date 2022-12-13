<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faja
 *
 * @ORM\Table(name="faja")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\FajaRepository")
 * @UniqueEntity("numero")
 * @ORM\HasLifecycleCallbacks()
 */
class Faja
{

    /**
     * @ORM\ManyToOne(targetEntity="EstadoFaja")
     * @ORM\JoinColumn(name="id_estado", referencedColumnName="id")
     */
    protected $estado;

    /**
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Area")
     * @ORM\JoinColumn(name="id_area", referencedColumnName="id")
     */
    protected $area;

    /**
     * @ORM\ManyToOne(targetEntity="TipoClausura")
     * @ORM\JoinColumn(name="id_tipo_clausura", referencedColumnName="id")
     */
    protected $tipoClausura;

    /**
     * @ORM\ManyToOne(targetEntity="\Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion", inversedBy="fajas")
     * @ORM\JoinColumn(name="id_inspeccion", referencedColumnName="id")
     */
    protected $ordenInspeccion;

    /**
     * @ORM\OneToMany(targetEntity="AsignacionFaja", mappedBy="faja")
     */
    protected $asignaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=9999999999)
     * @ORM\Column(name="numero", type="integer", unique=true)
     */
    private $numero;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="id_estado", type="integer")
     */
    private $idEstado;

    /**
     * @var int
     *
     * @ORM\Column(name="id_sap", type="bigint", nullable=true)
     */
    private $idSap;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="checklist", type="integer", nullable=true)
     */
    private $checklist;

    /**
     * @var int
     *     
     * @ORM\Column(name="id_area", type="smallint", nullable=true)
     */
    private $idArea;

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
     * @ORM\Column(name="fecha_inspeccion", type="date", nullable=true)
     */
    private $fechaInspeccion;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tipo_clausura", type="integer", nullable=true)
     */
    private $idTipoClausura;

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
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="Fecha_Modificado", type="date")
     */
    private $fechaModificado;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Modificador", type="integer")
     */
    private $idUsuarioModificador;



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
     * Set numero
     *
     * @param integer $numero
     *
     * @return Faja
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set idEstado
     *
     * @param integer $idEstado
     *
     * @return Faja
     */
    public function setIdEstado($idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado
     *
     * @return int
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idSap
     *
     * @param integer $idSap
     *
     * @return Faja
     */
    public function setIdSap($idSap)
    {
        $this->idSap = $idSap;

        return $this;
    }

    /**
     * Get idSap
     *
     * @return int
     */
    public function getIdSap()
    {
        return $this->idSap;
    }

    /**
     * Set checklist
     *
     * @param integer $checklist
     *
     * @return Faja
     */
    public function setChecklist($checklist)
    {
        $this->checklist = $checklist;

        return $this;
    }

    /**
     * Get checklist
     *
     * @return int
     */
    public function getChecklist()
    {
        return $this->checklist;
    }

    /**
     * Set idArea
     *
     * @param integer $idArea
     *
     * @return Faja
     */
    public function setIdArea($idArea)
    {
        $this->idArea = $idArea;

        return $this;
    }

    /**
     * Get idArea
     *
     * @return int
     */
    public function getIdArea()
    {
        return $this->idArea;
    }

    /**
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return Faja
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
     * Set fechaInspeccion
     *
     * @param \DateTime $fechaInspeccion
     *
     * @return Faja
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
     * Set idTipoClausura
     *
     * @param integer $idTipoClausura
     *
     * @return Faja
     */
    public function setIdTipoClausura($idTipoClausura)
    {
        $this->idTipoClausura = $idTipoClausura;

        return $this;
    }

    /**
     * Get idTipoClausura
     *
     * @return int
     */
    public function getIdTipoClausura()
    {
        return $this->idTipoClausura;
    }

    /**
     * Set estado
     *
     * @param \Faltas\FaltasBundle\Entity\EstadoFaja $estado
     *
     * @return Faja
     */
    public function setEstado(\Faltas\FaltasBundle\Entity\EstadoFaja $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Faltas\FaltasBundle\Entity\EstadoFaja
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return Faja
     */
    public function setArea(\Usuario\UsuarioBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Usuario\UsuarioBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set tipoClausura
     *
     * @param \Faltas\FaltasBundle\Entity\TipoClausura $tipoClausura
     *
     * @return Faja
     */
    public function setTipoClausura(\Faltas\FaltasBundle\Entity\TipoClausura $tipoClausura = null)
    {
        $this->tipoClausura = $tipoClausura;

        return $this;
    }

    /**
     * Get tipoClausura
     *
     * @return \Faltas\FaltasBundle\Entity\TipoClausura
     */
    public function getTipoClausura()
    {
        return $this->tipoClausura;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Faja
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
     * @return Faja
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
     * @return Faja
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
     * @return Faja
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
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return Faja
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
     * Constructor
     */
    public function __construct()
    {
        $this->asignaciones = new \Doctrine\Common\Collections\ArrayCollection();        
    }

    /**
     * Add asignacione
     *
     * @param \Faltas\FaltasBundle\Entity\AsignacionFaja $asignacione
     *
     * @return Faja
     */
    public function addAsignacione(\Faltas\FaltasBundle\Entity\AsignacionFaja $asignacione)
    {
        $this->asignaciones[] = $asignacione;

        return $this;
    }

    /**
     * Remove asignacione
     *
     * @param \Faltas\FaltasBundle\Entity\AsignacionFaja $asignacione
     */
    public function removeAsignacione(\Faltas\FaltasBundle\Entity\AsignacionFaja $asignacione)
    {
        $this->asignaciones->removeElement($asignacione);
    }

    /**
     * Get asignaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAsignaciones()
    {
        return $this->asignaciones;
    }

    public function __toString()
    {
        return $this->id;
    }

}
