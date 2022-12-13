<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inspeccion
 *
 * @ORM\Table(name="orden_inspeccion")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\OrdenInspeccionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OrdenInspeccion
{
    private $repository;

    /**     
     * @ORM\OneToMany(targetEntity="Denunciante", mappedBy="ordenInspeccion",cascade={"persist"})
     */
    protected $denunciantes;

    /**
     * @ORM\OneToMany(targetEntity="DireccionProvisoria", mappedBy="ordenInspeccion",cascade={"persist"})
     */
    protected $direcciones;

    /**
     * @ORM\OneToMany(targetEntity="Inspeccion", mappedBy="ordenInspeccion" ,cascade={"persist"})
     * @ORM\OrderBy({"fechaInspeccion" = "DESC","fechaProgramado" = "DESC", "fechaRecepcion" = "DESC"})
     */
    protected $inspecciones;

    /**
     * @ORM\OneToMany(targetEntity="\Faltas\FaltasBundle\Entity\Faja", mappedBy="ordenInspeccion")
     */
    protected $fajas;

    /**
     * @ORM\OneToMany(targetEntity="\Faltas\FaltasBundle\Entity\Acta", mappedBy="ordenInspeccion")
     */
    protected $actas;

    /**     
     * @ORM\ManyToOne(targetEntity="\Encuesta\EncuestaBundle\Entity\ModeloEncuesta")     
     */
    protected $modeloCheckList;

    /**
     * @ORM\OneToMany(targetEntity="Resultados", mappedBy="ordenInspeccion")
     * @ORM\OrderBy({"grupo" = "ASC","orden" = "ASC","id" = "ASC"})
     */
    protected $resultados;

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
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="checklist", type="integer", nullable=true)
     */
    private $checklist;

    /**
     * @var string
     *     
     * @ORM\Column(name="id_sap", type="bigint", nullable=true)
     */
    private $idSap;

    /**     
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Area")     
     */
    protected $area;

    /**     
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Circuito")     
     */
    protected $circuito;

    /**     
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="MotivoInspeccion")     
     */
    protected $motivoInspeccion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacionesMotivoInspeccion", type="string", length=2500, nullable=true)
     */
    private $observacionesMotivoInspeccion;

    /**     
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Establecimiento", inversedBy="inspecciones",)
     */
    protected $establecimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=2500, nullable=true)
     */
    private $observaciones;

    /**
     * @var bool
     *
     * @ORM\Column(name="anulada", type="boolean")
     */
    private $anulada;

    /**
     * @var bool
     *
     * @ORM\Column(name="realizada", type="boolean")
     */
    private $realizada;

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
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="eliminada", type="boolean", nullable=true)
     */
    private $eliminada;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Eliminador", type="integer", nullable=true)
     */
    private $idUsuarioEliminador;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="generaPeligrosos", type="boolean", nullable=true)
     */
    private $generaPeligrosos;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="generaPatogenicos", type="boolean", nullable=true)
     */
    private $generaPatogenicos;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="generaAvus", type="boolean", nullable=true)
     */
    private $generaAvus;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="generaEfluentesLiquidos", type="boolean", nullable=true)
     */
    private $generaEfluentesLiquidos;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="generaEmisionesGaseosas", type="boolean", nullable=true)
     */
    private $generaEmisionesGaseosas;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="inscriptoRac", type="boolean", nullable=true)
     */
    private $inscriptoRac;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="tieneTanquesCombustible", type="boolean", nullable=true)
     */
    private $tieneTamquesCombustible;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="inscriptoRegLavanderiaTintoreria", type="boolean", nullable=true)
     */
    private $inscriptoRegLavanderiaTintoreria;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="sinActividadImpactoAmbiental", type="boolean", nullable=true)
     */
    private $sinActividadImpactoAmbiental;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1)
     * @ORM\Column(name="ruido", type="integer", nullable=true)
     */
    private $ruido;

    /**
     * @var int
     *
     * @ORM\Column(name="suaci", type="string", length=11, nullable=true)
     */
    private $suaci;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1)
     * @ORM\Column(name="olores", type="integer", nullable=true)
     */
    private $olores;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1)
     * @ORM\Column(name="ctrlCedula", type="integer", nullable=true)
     */
    private $ctrlCedula;

    /**
     * @var bool
     *
     * @ORM\Column(name="autorizacion", type="boolean",nullable=true)
     */
    private $autorizacion;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Autorizador", type="integer", nullable=true)
     */
    private $idUsuarioAutorizador;

    /**
     * @var bool
     *
     * @ORM\Column(name="completa", type="boolean", nullable=true)
     */
    private $completa;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Fecha_Inspeccion_Completa", type="datetime", nullable=true)
     */
    private $fechaInspeccionCompleta;

    /**
     * @var bool
     *
     * @ORM\Column(name="revisionTablet", type="boolean", nullable=true)
     */
    private $revisionTablet;

    /**
     * @var bool
     *
     * @ORM\Column(name="cerradaAutomaticamente", type="boolean", nullable=true)
     */
    private $cerradaAutomaticamente;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Fecha_Cerrada_Automaticamente", type="datetime",nullable=true)
     */
    private $fechaCerradaAutomaticamente;

    /**
     * @var bool
     *
     * @ORM\Column(name="reinspeccionar", type="boolean", nullable=true,options={"default" : 0})
     */
    private $reinspeccionar;

    /**
     * @var int
     *          
     * @ORM\Column(name="reinspeccionarUsuario", type="integer", nullable=true)
     */
    private $reinspeccionarUsuario;

    /**
     * @var int
     *
     * @ORM\Column(name="reinspeccionProvenienciaOrdenInspeccion", type="integer", nullable=true)
     */
    private $reinspeccionProvenienciaOrdenInspeccion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Primer_Fecha_Programado", type="datetime", nullable=true)
     */
    private $primerFechaProgramado;

    /**
     * @var string
     *     
     * @ORM\Column(name="ifGra", type="string", length=100, nullable=true)
     */
    private $ifGra;

    /**
     * @var bool
     *
     * @ORM\Column(name="cumplioIntimacion", type="boolean", nullable=true)
     */
    private $cumplioIntimacion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="fechaInicioTablet", type="datetime", nullable=true)
     */
    private $fechaInicioTablet;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="fechaFinTablet", type="datetime", nullable=true)
     */
    private $fechaFinTablet;

    /**
     * @var bool
     *
     * @ORM\Column(name="vinculado", type="boolean", nullable=true, options={"default" = 0})
     */
    private $vinculado;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionObs", type="string", length=2500, nullable=true)
     */
    private $revisionObs;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="inspeccionPorTablet", type="boolean", nullable=true)
     */
    private $inspeccionPorTablet;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()     
     * @ORM\Column(name="Fecha_Vinculado", type="datetime")
     */
    private $fechaVinculado;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="Checklist_blanco", type="boolean", nullable=true)
     */
    private $checklistBlanco;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="cedulas_vencidas", type="boolean", nullable=true)
     */
    private $cedulasVencidas;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="clausuras_vigentes", type="boolean", nullable=true)
     */
    private $clausurasVigentes;
   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->direcciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->inspecciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->denunciantes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set checklist
     *
     * @param integer $checklist
     *
     * @return OrdenInspeccion
     */
    public function setChecklist($checklist)
    {
        $this->checklist = $checklist;

        return $this;
    }

    /**
     * Get checklist
     *
     * @return integer
     */
    public function getChecklist()
    {
        return $this->checklist;
    }

    /**
     * Set idSap
     *
     * @param integer $idSap
     *
     * @return OrdenInspeccion
     */
    public function setIdSap($idSap)
    {
        $this->idSap = $idSap;

        return $this;
    }

    /**
     * Get idSap
     *
     * @return integer
     */
    public function getIdSap()
    {
        return $this->idSap;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return OrdenInspeccion
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set revisionObs
     *
     * @param string $revisionObs
     *
     * @return RevisionObs
     */
    public function setRevisionObs($revisionObs)
    {
        $this->revisionObs = $revisionObs;

        return $this;
    }

    /**
     * Get revisionObs
     *
     * @return string
     */
    public function getRevisionObs()
    {
        return $this->revisionObs;
    }

    /**
     * Set anulada
     *
     * @param boolean $anulada
     *
     * @return OrdenInspeccion
     */
    public function setAnulada($anulada)
    {
        $this->anulada = $anulada;

        return $this;
    }

    /**
     * Get anulada
     *
     * @return boolean
     */
    public function getAnulada()
    {
        return $this->anulada;
    }

    /**
     * Set completa
     *
     * @param boolean $completa
     *
     * @return OrdenInspeccion
     */
    public function setCompleta($completa)
    {
        $this->completa = $completa;

        return $this;
    }

    /**
     * Get completa
     *
     * @return boolean
     */
    public function getCompleta()
    {
        return $this->completa;
    }

    /**
     * Set realizada
     *
     * @param boolean $realizada
     *
     * @return OrdenInspeccion
     */
    public function setRealizada($realizada)
    {
        $this->realizada = $realizada;

        return $this;
    }

    /**
     * Get realizada
     *
     * @return boolean
     */
    public function getRealizada()
    {
        return $this->realizada;
    }

    /**
     * Add direccione
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria $direccione
     *
     * @return OrdenInspeccion
     */
    public function addDireccione(\Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria $direccione)
    {
        $this->direcciones[] = $direccione;

        return $this;
    }

    /**
     * Remove direccione
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria $direccione
     */
    public function removeDireccione(\Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria $direccione)
    {
        $this->direcciones->removeElement($direccione);
    }

    /**
     * Get direcciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDirecciones()
    {
        return $this->direcciones;
    }

    public function setDirecciones($direcciones)
    {
        $this->direcciones = $direcciones;
    }

    /**
     * Add inspeccione
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Inspeccion $inspeccione
     *
     * @return OrdenInspeccion
     */
    public function addInspeccione(\Inspecciones\InspeccionesBundle\Entity\Inspeccion $inspeccione)
    {
        $this->inspecciones[] = $inspeccione;

        return $this;
    }

    /**
     * Remove inspeccione
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Inspeccion $inspeccione
     */
    public function removeInspeccione(\Inspecciones\InspeccionesBundle\Entity\Inspeccion $inspeccione)
    {
        $this->inspecciones->removeElement($inspeccione);
    }

    /**
     * Get inspecciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInspecciones()
    {
        return $this->inspecciones;
    }

    public function setInspecciones($inspecciones)
    {
        $this->inspecciones = $inspecciones;
    }

    /**
     * Set area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return OrdenInspeccion
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
     * Set circuito
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Circuito $circuito
     *
     * @return OrdenInspeccion
     */
    public function setCircuito(\Inspecciones\InspeccionesBundle\Entity\Circuito $circuito = null)
    {
        $this->circuito = $circuito;

        return $this;
    }

    /**
     * Get circuito
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\Circuito
     */
    public function getCircuito()
    {
        return $this->circuito;
    }

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return OrdenInspeccion
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
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime('-3 hours');
    }

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setFechaModificadoValue()
    {
        $this->fechaModificado = new \DateTime('-3 hours');
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return OrdenInspeccion
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
     * @return OrdenInspeccion
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
     * @return OrdenInspeccion
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
     * @return OrdenInspeccion
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

    public function __toString()
    {
        return 'CheckList: '.$this->checklist.' IdSap: '.$this->idSap;
    }

    /**
     * Add faja
     *
     * @param \Faltas\FaltasBundle\Entity\Faja $faja
     *
     * @return OrdenInspeccion
     */
    public function addFaja(\Faltas\FaltasBundle\Entity\Faja $faja)
    {
        $this->fajas[] = $faja;

        return $this;
    }

    public function setFajas($fajas)
    {
        $this->fajas = $fajas;
    }

    /**
     * Remove faja
     *
     * @param \Faltas\FaltasBundle\Entity\Faja $faja
     */
    public function removeFaja(\Faltas\FaltasBundle\Entity\Faja $faja)
    {
        $this->fajas->removeElement($faja);
    }

    /**
     * Get fajas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFajas()
    {
        return $this->fajas;
    }

    public function setActas($actas)
    {
        $this->actas = $actas;
    }

    /**
     * Add acta
     *
     * @param \Faltas\FaltasBundle\Entity\Acta $acta
     *
     * @return OrdenInspeccion
     */
    public function addActa(\Faltas\FaltasBundle\Entity\Acta $acta)
    {
        $this->actas[] = $acta;

        return $this;
    }

    /**
     * Remove acta
     *
     * @param \Faltas\FaltasBundle\Entity\Acta $acta
     */
    public function removeActa(\Faltas\FaltasBundle\Entity\Acta $acta)
    {
        $this->actas->removeElement($acta);
    }

    /**
     * Get actas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActas()
    {
        return $this->actas;
    }

    /**
     * Set eliminada
     *
     * @param boolean $eliminada
     *
     * @return OrdenInspeccion
     */
    public function setEliminada($eliminada)
    {
        $this->eliminada = $eliminada;

        return $this;
    }

    /**
     * Get eliminada
     *
     * @return boolean
     */
    public function getEliminada()
    {
        return $this->eliminada;
    }

    /**
     * Set idUsuarioEliminador
     *
     * @param integer $idUsuarioEliminador
     *
     * @return OrdenInspeccion
     */
    public function setIdUsuarioEliminador($idUsuarioEliminador)
    {
        $this->idUsuarioEliminador = $idUsuarioEliminador;

        return $this;
    }

    /**
     * Get idUsuarioEliminador
     *
     * @return integer
     */
    public function getIdUsuarioEliminador()
    {
        return $this->idUsuarioEliminador;
    }

    /**
     * Add denunciante
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Denunciante $denunciante
     *
     * @return OrdenInspeccion
     */
    public function addDenunciante(\Inspecciones\InspeccionesBundle\Entity\Denunciante $denunciante)
    {
        $this->denunciantes[] = $denunciante;

        return $this;
    }

    /**
     * Remove denunciante
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Denunciante $denunciante
     */
    public function removeDenunciante(\Inspecciones\InspeccionesBundle\Entity\Denunciante $denunciante)
    {
        $this->denunciantes->removeElement($denunciante);
    }

    /**
     * Get denunciantes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDenunciantes()
    {
        return $this->denunciantes;
    }

    public function setDenunciantes($denunciantes)
    {
        $this->denunciantes = $denunciantes;
    }

    /**
     * Set generaPeligrosos
     *
     * @param boolean $generaPeligrosos
     *
     * @return OrdenInspeccion
     */
    public function setGeneraPeligrosos($generaPeligrosos)
    {
        $this->generaPeligrosos = $generaPeligrosos;

        return $this;
    }

    /**
     * Get generaPeligrosos
     *
     * @return boolean
     */
    public function getGeneraPeligrosos()
    {
        return $this->generaPeligrosos;
    }

    /**
     * Set generaPatogenicos
     *
     * @param boolean $generaPatogenicos
     *
     * @return OrdenInspeccion
     */
    public function setGeneraPatogenicos($generaPatogenicos)
    {
        $this->generaPatogenicos = $generaPatogenicos;

        return $this;
    }

    /**
     * Get generaPatogenicos
     *
     * @return boolean
     */
    public function getGeneraPatogenicos()
    {
        return $this->generaPatogenicos;
    }

    /**
     * Set generaAvus
     *
     * @param boolean $generaAvus
     *
     * @return OrdenInspeccion
     */
    public function setGeneraAvus($generaAvus)
    {
        $this->generaAvus = $generaAvus;

        return $this;
    }

    /**
     * Get generaAvus
     *
     * @return boolean
     */
    public function getGeneraAvus()
    {
        return $this->generaAvus;
    }

    /**
     * Set generaEfluentesLiquidos
     *
     * @param boolean $generaEfluentesLiquidos
     *
     * @return OrdenInspeccion
     */
    public function setGeneraEfluentesLiquidos($generaEfluentesLiquidos)
    {
        $this->generaEfluentesLiquidos = $generaEfluentesLiquidos;

        return $this;
    }

    /**
     * Get generaEfluentesLiquidos
     *
     * @return boolean
     */
    public function getGeneraEfluentesLiquidos()
    {
        return $this->generaEfluentesLiquidos;
    }

    /**
     * Set generaEmisionesGaseosas
     *
     * @param boolean $generaEmisionesGaseosas
     *
     * @return OrdenInspeccion
     */
    public function setGeneraEmisionesGaseosas($generaEmisionesGaseosas)
    {
        $this->generaEmisionesGaseosas = $generaEmisionesGaseosas;

        return $this;
    }

    /**
     * Get generaEmisionesGaseosas
     *
     * @return boolean
     */
    public function getGeneraEmisionesGaseosas()
    {
        return $this->generaEmisionesGaseosas;
    }

    /**
     * Set inscriptoRac
     *
     * @param boolean $inscriptoRac
     *
     * @return OrdenInspeccion
     */
    public function setInscriptoRac($inscriptoRac)
    {
        $this->inscriptoRac = $inscriptoRac;

        return $this;
    }

    /**
     * Get inscriptoRac
     *
     * @return boolean
     */
    public function getInscriptoRac()
    {
        return $this->inscriptoRac;
    }

    /**
     * Set tieneTamquesCombustible
     *
     * @param boolean $tieneTamquesCombustible
     *
     * @return OrdenInspeccion
     */
    public function setTieneTamquesCombustible($tieneTamquesCombustible)
    {
        $this->tieneTamquesCombustible = $tieneTamquesCombustible;

        return $this;
    }

    /**
     * Get tieneTamquesCombustible
     *
     * @return boolean
     */
    public function getTieneTamquesCombustible()
    {
        return $this->tieneTamquesCombustible;
    }

    /**
     * Set inscriptoRegLavanderiaTintoreria
     *
     * @param boolean $inscriptoRegLavanderiaTintoreria
     *
     * @return OrdenInspeccion
     */
    public function setInscriptoRegLavanderiaTintoreria($inscriptoRegLavanderiaTintoreria)
    {
        $this->inscriptoRegLavanderiaTintoreria = $inscriptoRegLavanderiaTintoreria;

        return $this;
    }

    /**
     * Get inscriptoRegLavanderiaTintoreria
     *
     * @return boolean
     */
    public function getInscriptoRegLavanderiaTintoreria()
    {
        return $this->inscriptoRegLavanderiaTintoreria;
    }

    /**
     * Set sinActividadImpactoAmbiental
     *
     * @param boolean $sinActividadImpactoAmbiental
     *
     * @return OrdenInspeccion
     */
    public function setSinActividadImpactoAmbiental($sinActividadImpactoAmbiental)
    {
        $this->sinActividadImpactoAmbiental = $sinActividadImpactoAmbiental;

        return $this;
    }

    /**
     * Get sinActividadImpactoAmbiental
     *
     * @return boolean
     */
    public function getSinActividadImpactoAmbiental()
    {
        return $this->sinActividadImpactoAmbiental;
    }

    /**
     * Set ruido
     *
     * @param integer $ruido
     *
     * @return OrdenInspeccion
     */
    public function setRuido($ruido)
    {
        $this->ruido = $ruido;

        return $this;
    }

    /**
     * Get ruido
     *
     * @return integer
     */
    public function getRuido()
    {
        return $this->ruido;
    }

    /**
     * Set suaci
     *
     * @param integer $suaci
     *
     * @return OrdenInspeccion
     */
    public function setSuaci($suaci)
    {
        $this->suaci = $suaci;

        return $this;
    }

    /**
     * Get suaci
     *
     * @return integer
     */
    public function getSuaci()
    {
        return $this->suaci;
    }

    /**
     * Set olores
     *
     * @param integer $olores
     *
     * @return OrdenInspeccion
     */
    public function setOlores($olores)
    {
        $this->olores = $olores;

        return $this;
    }

    /**
     * Get olores
     *
     * @return integer
     */
    public function getOlores()
    {
        return $this->olores;
    }

    /**
     * Set ctrlCedula
     *
     * @param integer $ctrlCedula
     *
     * @return OrdenInspeccion
     */
    public function setCtrlCedula($ctrlCedula)
    {
        $this->ctrlCedula = $ctrlCedula;

        return $this;
    }

    /**
     * Get ctrlCedula
     *
     * @return integer
     */
    public function getCtrlCedula()
    {
        return $this->ctrlCedula;
    }

    /**
     * Set modeloCheckList
     *
     * @param \Encuesta\EncuestaBundle\Entity\ModeloEncuesta $modeloCheckList
     *
     * @return OrdenInspeccion
     */
    public function setModeloCheckList(\Encuesta\EncuestaBundle\Entity\ModeloEncuesta $modeloCheckList = null)
    {
        $this->modeloCheckList = $modeloCheckList;

        return $this;
    }

    /**
     * Get modeloCheckList
     *
     * @return \Encuesta\EncuestaBundle\Entity\ModeloEncuesta
     */
    public function getModeloCheckList()
    {
        return $this->modeloCheckList;
    }

    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
    
    public function setMaxNumero()
    {    
        $number = $this->repository->maxCheckList();
        $this->setChecklist($number[0][1]+1);        
    }

    /**
     * Set autorizacion
     *
     * @param boolean $autorizacion
     *
     * @return OrdenInspeccion
     */
    public function setAutorizacion($autorizacion)
    {
        $this->autorizacion = $autorizacion;

        return $this;
    }

    /**
     * Get autorizacion
     *
     * @return boolean
     */
    public function getAutorizacion()
    {
        return $this->autorizacion;
    }

    /**
     * Add resultado
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Resultados $resultado
     *
     * @return OrdenInspeccion
     */
    public function addResultado(\Inspecciones\InspeccionesBundle\Entity\Resultados $resultado)
    {
        $this->resultados[] = $resultado;

        return $this;
    }

    /**
     * Remove resultado
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Resultados $resultado
     */
    public function removeResultado(\Inspecciones\InspeccionesBundle\Entity\Resultados $resultado)
    {
        $this->resultados->removeElement($resultado);
    }

    /**
     * Get resultados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultados()
    {
        return $this->resultados;
    }

    public function setResultados($resultados)
    {
        $this->resultados = $resultados;
    }

    /**
     * Set idUsuarioAutorizador
     *
     * @param integer $idUsuarioAutorizador
     *
     * @return OrdenInspeccion
     */
    public function setIdUsuarioAutorizador($idUsuarioAutorizador)
    {
        $this->idUsuarioAutorizador = $idUsuarioAutorizador;

        return $this;
    }

    /**
     * Get idUsuarioAutorizador
     *
     * @return integer
     */
    public function getIdUsuarioAutorizador()
    {
        return $this->idUsuarioAutorizador;
    }

    /**
     * Set reinspeccionar
     *
     * @param boolean $reinspeccionar
     *
     * @return OrdenInspeccion
     */
    public function setReinspeccionar($reinspeccionar)
    {
        $this->reinspeccionar = $reinspeccionar;

        return $this;
    }

    /**
     * Get reinspeccionar
     *
     * @return boolean
     */
    public function getReinspeccionar()
    {
        return $this->reinspeccionar;
    }

    /**
     * Set reinspeccionarUsuario
     *
     * @param integer $reinspeccionarUsuario
     *
     * @return OrdenInspeccion
     */
    public function setReinspeccionarUsuario($reinspeccionarUsuario)
    {
        $this->reinspeccionarUsuario = $reinspeccionarUsuario;

        return $this;
    }

    /**
     * Get reinspeccionarUsuario
     *
     * @return integer
     */
    public function getReinspeccionarUsuario()
    {
        return $this->reinspeccionarUsuario;
    }

    /**
     * Set reinspeccionProvenienciaOrdenInspeccion
     *
     * @param integer $reinspeccionProvenienciaOrdenInspeccion
     *
     * @return OrdenInspeccion
     */
    public function setReinspeccionProvenienciaOrdenInspeccion($reinspeccionProvenienciaOrdenInspeccion)
    {
        $this->reinspeccionProvenienciaOrdenInspeccion = $reinspeccionProvenienciaOrdenInspeccion;

        return $this;
    }

    /**
     * Get reinspeccionProvenienciaOrdenInspeccion
     *
     * @return integer
     */
    public function getReinspeccionProvenienciaOrdenInspeccion()
    {
        return $this->reinspeccionProvenienciaOrdenInspeccion;
    }

    /**
     * Set cerradaAutomaticamente
     *
     * @param boolean $cerradaAutomaticamente
     *
     * @return OrdenInspeccion
     */
    public function setCerradaAutomaticamente($cerradaAutomaticamente)
    {
        $this->cerradaAutomaticamente = $cerradaAutomaticamente;

        return $this;
    }

    /**
     * Get cerradaAutomaticamente
     *
     * @return boolean
     */
    public function getCerradaAutomaticamente()
    {
        return $this->cerradaAutomaticamente;
    }

    /**
     * Set fechaCerradaAutomaticamente
     *
     * @param \DateTime $fechaCerradaAutomaticamente
     *
     * @return OrdenInspeccion
     */
    public function setFechaCerradaAutomaticamente($fechaCerradaAutomaticamente)
    {
        $this->fechaCerradaAutomaticamente = $fechaCerradaAutomaticamente;

        return $this;
    }

    /**
     * Get fechaCerradaAutomaticamente
     *
     * @return \DateTime
     */
    public function getFechaCerradaAutomaticamente()
    {
        return $this->fechaCerradaAutomaticamente;
    }

    /**
     * Set motivoInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\MotivoInspeccion $motivoInspeccion
     *
     * @return OrdenInspeccion
     */
    public function setMotivoInspeccion(\Inspecciones\InspeccionesBundle\Entity\MotivoInspeccion $motivoInspeccion = null)
    {
        $this->motivoInspeccion = $motivoInspeccion;

        return $this;
    }

    /**
     * Get motivoInspeccion
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\MotivoInspeccion
     */
    public function getMotivoInspeccion()
    {
        return $this->motivoInspeccion;
    }

    /**
     * Set observacionesMotivoInspeccion
     *
     * @param string $observacionesMotivoInspeccion
     *
     * @return OrdenInspeccion
     */
    public function setObservacionesMotivoInspeccion($observacionesMotivoInspeccion)
    {
        $this->observacionesMotivoInspeccion = $observacionesMotivoInspeccion;

        return $this;
    }

    /**
     * Get observacionesMotivoInspeccion
     *
     * @return string
     */
    public function getObservacionesMotivoInspeccion()
    {
        return $this->observacionesMotivoInspeccion;
    }

    /**
     * Set fechaInspeccionCreado
     *
     * @param \DateTime $fechaInspeccionCreado
     *
     * @return OrdenInspeccion
     */
    public function setFechaInspeccionCreado($fechaInspeccionCreado)
    {
        $this->fechaInspeccionCreado = $fechaInspeccionCreado;

        return $this;
    }

    /**
     * Get fechaInspeccionCreado
     *
     * @return \DateTime
     */
    public function getFechaInspeccionCreado()
    {
        return $this->fechaInspeccionCreado;
    }

    /**
     * Set fechaInspeccionCompleta
     *
     * @param \DateTime $fechaInspeccionCompleta
     *
     * @return OrdenInspeccion
     */
    public function setFechaInspeccionCompleta($fechaInspeccionCompleta)
    {
        $this->fechaInspeccionCompleta = $fechaInspeccionCompleta;

        return $this;
    }

    /**
     * Get fechaInspeccionCompleta
     *
     * @return \DateTime
     */
    public function getFechaInspeccionCompleta()
    {
        return $this->fechaInspeccionCompleta;
    }

    /**
     * Set revisionTablet
     *
     * @param boolean $revisionTablet
     *
     * @return OrdenInspeccion
     */
    public function setRevisionTablet($revisionTablet)
    {
        $this->revisionTablet = $revisionTablet;

        return $this;
    }

    /**
     * Get revisionTablet
     *
     * @return boolean
     */
    public function getRevisionTablet()
    {
        return $this->revisionTablet;
    }

    /**
     * Set primerFechaProgramado
     *
     * @param \DateTime $primerFechaProgramado
     *
     * @return OrdenInspeccion
     */
    public function setPrimerFechaProgramado($primerFechaProgramado)
    {
        $this->primerFechaProgramado = $primerFechaProgramado;

        return $this;
    }

    /**
     * Get primerFechaProgramado
     *
     * @return \DateTime
     */
    public function getPrimerFechaProgramado()
    {
        return $this->primerFechaProgramado;
    }

    /**
     * Set ifGra
     *
     * @param integer $ifGra
     *
     * @return OrdenInspeccion
     */
    public function setIfGra($ifGra)
    {
        $this->ifGra = $ifGra;

        return $this;
    }

    /**
     * Get ifGra
     *
     * @return integer
     */
    public function getIfGra()
    {
        return $this->ifGra;
    }

    /**
     * Set cumplioIntimacion
     *
     * @param boolean $cumplioIntimacion
     *
     * @return OrdenInspeccion
     */
    public function setCumplioIntimacion($cumplioIntimacion)
    {
        $this->cumplioIntimacion = $cumplioIntimacion;

        return $this;
    }

    /**
     * Get cumplioIntimacion
     *
     * @return boolean
     */
    public function getCumplioIntimacion()
    {
        return $this->cumplioIntimacion;
    }

    /**
     * Set fechaInicioTablet
     *
     * @param \DateTime $fechaInicioTablet
     *
     * @return OrdenInspeccion
     */
    public function setFechaInicioTablet($fechaInicioTablet)
    {
        $this->fechaInicioTablet = $fechaInicioTablet;

        return $this;
    }

    /**
     * Get fechaInicioTablet
     *
     * @return \DateTime
     */
    public function getFechaInicioTablet()
    {
        return $this->fechaInicioTablet;
    }

    /**
     * Set fechaFinTablet
     *
     * @param \DateTime $fechaFinTablet
     *
     * @return OrdenInspeccion
     */
    public function setFechaFinTablet($fechaFinTablet)
    {
        $this->fechaFinTablet = $fechaFinTablet;

        return $this;
    }

    /**
     * Get fechaFinTablet
     *
     * @return \DateTime
     */
    public function getFechaFinTablet()
    {
        return $this->fechaFinTablet;
    }

    /**
     * Set vinculado
     *
     * @param boolean $vinculado
     *
     * @return OrdenInspeccion
     */
    public function setVinculado($vinculado)
    {
        $this->vinculado = $vinculado;

        return $this;
    }

    /**
     * Get vinculado
     *
     * @return boolean
     */
    public function getVinculado()
    {
        return $this->vinculado;
    }

    /**
     * Set inspeccionPorTablet
     *
     * @param boolean $inspeccionPorTablet
     *
     * @return OrdenInspeccion
     */
    public function setInspeccionPorTablet($inspeccionPorTablet)
    {
        $this->inspeccionPorTablet = $inspeccionPorTablet;

        return $this;
    }

    /**
     * Get inspeccionPorTablet
     *
     * @return boolean
     */
    public function getInspeccionPorTablet()
    {
        return $this->inspeccionPorTablet;
    }

    /**
     * Set fechaVinculado
     *
     * @param \DateTime $fechaVinculado
     *
     * @return OrdenInspeccion
     */
    public function setFechaVinculado($fechaVinculado)
    {
        $this->fechaVinculado = $fechaVinculado;

        return $this;
    }

    /**
     * Get fechaVinculado
     *
     * @return \DateTime
     */
    public function getFechaVinculado()
    {
        return $this->fechaVinculado;
    }

    /**
     * Set checklistBlanco
     *
     * @param boolean $checklistBlanco
     *
     * @return OrdenInspeccion
     */
    public function setChecklistBlanco($checklistBlanco)
    {
        $this->checklistBlanco = $checklistBlanco;

        return $this;
    }

    /**
     * Get checklistBlanco
     *
     * @return boolean
     */
    public function getChecklistBlanco()
    {
        return $this->checklistBlanco;
    }

    /**
     * Set cedulasVencidas
     *
     * @param boolean $cedulasVencidas
     *
     * @return OrdenInspeccion
     */
    public function setCedulasVencidas($cedulasVencidas)
    {
        $this->cedulasVencidas = $cedulasVencidas;

        return $this;
    }

    /**
     * Get cedulasVencidas
     *
     * @return boolean
     */
    public function getCedulasVencidas()
    {
        return $this->cedulasVencidas;
    }

    /**
     * Set clausurasVigentes
     *
     * @param boolean $clausurasVigentes
     *
     * @return OrdenInspeccion
     */
    public function setClausurasVigentes($clausurasVigentes)
    {
        $this->clausurasVigentes = $clausurasVigentes;

        return $this;
    }

    /**
     * Get clausurasVigentes
     *
     * @return boolean
     */
    public function getClausurasVigentes()
    {
        return $this->clausurasVigentes;
    }

    /**
     * Get getVistaSiguientePreguntaTablet
     *
     * @return \Doctrine\Common\Collections\Collection
     */    
    public function getVistaInspecciones()
    {
        $grupoInspecciones["id"]=$this->getId();
        $grupoInspecciones["checklist_blanco"]=$this->getChecklistBlanco();
        $grupoInspecciones["checklist"]=$this->getChecklist();
        if($grupoInspecciones["checklist_blanco"]){
            $grupoInspecciones["id_sap"]="";
        }else{
            $grupoInspecciones["id_sap"]=$this->getIdSap();
        }

        if($grupoInspecciones["checklist_blanco"]){
                $grupoInspecciones["numero_suaci"]=0;
        }else{
            if($this->getSuaci() !== null){
                $grupoInspecciones["numero_suaci"]=intval($this->getSuaci());
            }else{
                $grupoInspecciones["numero_suaci"]=0;
            }
        }        
        $grupoInspecciones["direcciones"]=$this->getDirecciones();
        //$grupoInspecciones["orden_inspeccion"]=$this->getInspecciones();
        $grupoInspecciones["area"]=$this->getArea()->getAreaVistaTablet();
        if($grupoInspecciones["checklist_blanco"]){
            $grupoInspecciones["circuito"]=array();
            $grupoInspecciones["circuito"]["id"]=0;
            $grupoInspecciones["circuito"]["circuito"]="";
        }else{
            $grupoInspecciones["circuito"]=$this->getCircuito();
        }
        if($grupoInspecciones["checklist_blanco"]){
            $grupoInspecciones["motivo_inspeccion"]=array();
            $grupoInspecciones["motivo_inspeccion"]["id"]=0;
            $grupoInspecciones["motivo_inspeccion"]["motivo"]="";
        }else{
            $grupoInspecciones["motivo_inspeccion"]=$this->getMotivoInspeccion();
        }              
        if($grupoInspecciones["checklist_blanco"]){
            $grupoInspecciones["observaciones_motivo_inspeccion"]="";
        }else{
            $grupoInspecciones["observaciones_motivo_inspeccion"]=$this->getVistaMotivoInspeccion();
        }   
        if($this->getObservaciones() !== null){
            $grupoInspecciones["aviso_programador"]=$this->getObservaciones();
        }else{
            $grupoInspecciones["aviso_programador"]="";
        }
        $grupoInspecciones["cordinada"]=0;

        $grupoInspecciones["anulada"]=$this->getAnulada();
        $grupoInspecciones["realizada"]=$this->getRealizada();
        $grupoInspecciones["autorizacion"]=$this->getAutorizacion();
        $grupoInspecciones["id_usuario_autorizador"]=$this->getIdUsuarioAutorizador();
        $grupoInspecciones["primer_fecha_programado"]=$this->getPrimerFechaProgramado();
        $grupoInspecciones["vinculado"]=$this->getVinculado();
        $grupoInspecciones["inspeccion_por_tablet"]=$this->getInspeccionPorTablet();

        if($this->getCedulasVencidas()){
            if($grupoInspecciones["checklist_blanco"]){
                $grupoInspecciones["cedulas_vencidas"]=false;
            }else{
                $grupoInspecciones["cedulas_vencidas"]=true;
            }           
            //$grupoInspecciones["cedulas_vencidas"]=$this->getCedulasVencidas();
        }else{
            if($grupoInspecciones["checklist_blanco"]){
                $grupoInspecciones["cedulas_vencidas"]=false;
            }else{
                $grupoInspecciones["cedulas_vencidas"]=true;
            }
        }

        if($this->getClausurasVigentes()){
            if($grupoInspecciones["checklist_blanco"]){
                $grupoInspecciones["clausuras_vigentes"]=false;
            }else{
                $grupoInspecciones["clausuras_vigentes"]=true;
            }

            //$grupoInspecciones["clausuras_vigentes"]=$this->getClausurasVigentes();
        }else{
            if($grupoInspecciones["checklist_blanco"]){
                $grupoInspecciones["clausuras_vigentes"]=false;
            }else{
                $grupoInspecciones["clausuras_vigentes"]=true;
            }
        }        
        $grupoInspecciones["Autorizador"]=array();
        $grupoInspecciones["Autorizador"]["Id"]=1;
        $grupoInspecciones["Autorizador"]["Titulo"]="Ing. Frank Hernandez";
        $grupoInspecciones["Autorizador"]["Cargo"]="Gerente Operativo de Fiscalización Ambiental";
        return $grupoInspecciones;
    }

    public function getVistaMotivoInspeccion(){
            $motivoClasificado = $this->getMotivoInspeccion()->getMotivo();
            $motivoTexto = 'MOTIVO INSPECCION: ';
            $motivoRevision = '';

            $motivoOrden = $this->getObservacionesMotivoInspeccion();

            if($motivoOrden){
                $motivoTexto .= $motivoOrden;
            }else{
                $motivoTexto = "Sin Motivo";
            }
            

            if($this->getRevisionTablet() == 1){
                $motivoRevision = "MOTIVO REVISION: ".$this->getRevisionObs()."\n\n--------------------------------------------------\n\n"; 
            }
            
            return $motivoRevision.$motivoClasificado.': '.$motivoTexto;  
    }
}
