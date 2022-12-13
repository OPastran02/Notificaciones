<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActaUtilizada
 *
 * @ORM\Table(name="acta_utilizada")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\ActaUtilizadaRepository")
  * @ORM\HasLifecycleCallbacks()
 */
class ActaUtilizada
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Acta", inversedBy="ActaUtilizada",cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=true, unique=true, onDelete="cascade")     
     */
    protected $acta; 

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="ActaMotivo")        
     * @ORM\JoinTable(name="acta_utilizada_motivo",
     *      joinColumns={@ORM\JoinColumn(name="id_acta_utilizada", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_acta_motivo", referencedColumnName="id")}
     *      )
     */
    protected $actaMotivo;  

    /** 
     * @Assert\NotBlank()  
     * @ORM\ManyToOne(targetEntity="Usuario\UsuarioBundle\Entity\Area")     
     */
    protected $areas;   

   

    /**
     * @var int
     *
     * @ORM\Column(name="Sap", type="bigint", nullable=true)
     */
    private $sap;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=9999999999)
     * @ORM\Column(name="checklist", type="integer", nullable=true)
     */
    private $checklist;


    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="comprobado", type="boolean")
     */
    private $comprobado;

    /**
     * @var string
     *
     * @ORM\Column(name="puntoencuentro", type="string", length=150, nullable=true)
     */
    private $puntoencuentro;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fechaInspeccion", type="date")
     */
    private $fechaInspeccion;

    /**
     * @var string
     *
     * @ORM\Column(name="dominioL", type="string", length=4, nullable=true)
     */
    private $dominioL;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=999)
     * @ORM\Column(name="dominioR", type="integer", nullable=true)
     */
    private $dominioR;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=999999)
     * @ORM\Column(name="interno", type="integer", nullable=true)
     */
    private $interno;

    /**
     * @var string
     *
     * @ORM\Column(name="marca", type="string", length=50, nullable=true)
     */
    private $marca;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=50, nullable=true)
     */
    private $modelo;

    /**
     * @var float
     *
     * @Assert\Type("float")
     * @ORM\Column(name="ruido", type="float", nullable=true)
     */
    private $ruido;

    /**
     * @var float
     *
     * @Assert\Type("float")
     * @ORM\Column(name="humo", type="float", nullable=true)
     */
    private $humo;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fechaRecepcion", type="date")
     */
    private $fechaRecepcion;


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
     * Constructor
     */
    public function __construct()
    {
        $this->actaMotivo = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set sap
     *
     * @param integer $sap
     *
     * @return ActaUtilizada
     */
    public function setSap($sap)
    {
        $this->sap = $sap;

        return $this;
    }

    /**
     * Get sap
     *
     * @return integer
     */
    public function getSap()
    {
        return $this->sap;
    }

    /**
     * Set checklist
     *
     * @param integer $checklist
     *
     * @return ActaUtilizada
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
     * Set comprobado
     *
     * @param boolean $comprobado
     *
     * @return ActaUtilizada
     */
    public function setComprobado($comprobado)
    {
        $this->comprobado = $comprobado;

        return $this;
    }

    /**
     * Get comprobado
     *
     * @return boolean
     */
    public function getComprobado()
    {
        return $this->comprobado;
    }

    /**
     * Set puntoencuentro
     *
     * @param string $puntoencuentro
     *
     * @return ActaUtilizada
     */
    public function setPuntoencuentro($puntoencuentro)
    {
        $this->puntoencuentro = $puntoencuentro;

        return $this;
    }

    /**
     * Get puntoencuentro
     *
     * @return string
     */
    public function getPuntoencuentro()
    {
        return $this->puntoencuentro;
    }

    /**
     * Set fechaInspeccion
     *
     * @param \DateTime $fechaInspeccion
     *
     * @return ActaUtilizada
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
     * Set dominioL
     *
     * @param string $dominioL
     *
     * @return ActaUtilizada
     */
    public function setDominioL($dominioL)
    {
        $this->dominioL = $dominioL;

        return $this;
    }

    /**
     * Get dominioL
     *
     * @return string
     */
    public function getDominioL()
    {
        return $this->dominioL;
    }

    /**
     * Set dominioR
     *
     * @param integer $dominioR
     *
     * @return ActaUtilizada
     */
    public function setDominioR($dominioR)
    {
        $this->dominioR = $dominioR;

        return $this;
    }

    /**
     * Get dominioR
     *
     * @return integer
     */
    public function getDominioR()
    {
        return $this->dominioR;
    }

    /**
     * Set interno
     *
     * @param integer $interno
     *
     * @return ActaUtilizada
     */
    public function setInterno($interno)
    {
        $this->interno = $interno;

        return $this;
    }

    /**
     * Get interno
     *
     * @return integer
     */
    public function getInterno()
    {
        return $this->interno;
    }

    /**
     * Set marca
     *
     * @param string $marca
     *
     * @return ActaUtilizada
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     *
     * @return ActaUtilizada
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return string
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set ruido
     *
     * @param float $ruido
     *
     * @return ActaUtilizada
     */
    public function setRuido($ruido)
    {
        $this->ruido = $ruido;

        return $this;
    }

    /**
     * Get ruido
     *
     * @return float
     */
    public function getRuido()
    {
        return $this->ruido;
    }

    /**
     * Set humo
     *
     * @param float $humo
     *
     * @return ActaUtilizada
     */
    public function setHumo($humo)
    {
        $this->humo = $humo;

        return $this;
    }

    /**
     * Get humo
     *
     * @return float
     */
    public function getHumo()
    {
        return $this->humo;
    }

    /**
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return ActaUtilizada
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
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return ActaUtilizada
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
     * @return ActaUtilizada
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
     * @return ActaUtilizada
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
     * @return ActaUtilizada
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
     * Set acta
     *
     * @param \Faltas\FaltasBundle\Entity\Acta $acta
     *
     * @return ActaUtilizada
     */
    public function setActa(\Faltas\FaltasBundle\Entity\Acta $acta)
    {
        $this->acta = $acta;

        return $this;
    }

    /**
     * Get acta
     *
     * @return \Faltas\FaltasBundle\Entity\Acta
     */
    public function getActa()
    {
        return $this->acta;
    }

    /**
     * Add actaMotivo
     *
     * @param \Faltas\FaltasBundle\Entity\ActaMotivo $actaMotivo
     *
     * @return ActaUtilizada
     */
    public function addActaMotivo(\Faltas\FaltasBundle\Entity\ActaMotivo $actaMotivo)
    {
        $this->actaMotivo[] = $actaMotivo;

        return $this;
    }

    /**
     * Remove actaMotivo
     *
     * @param \Faltas\FaltasBundle\Entity\ActaMotivo $actaMotivo
     */
    public function removeActaMotivo(\Faltas\FaltasBundle\Entity\ActaMotivo $actaMotivo)
    {
        $this->actaMotivo->removeElement($actaMotivo);
    }

    /**
     * Get actaMotivo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActaMotivo()
    {
        return $this->actaMotivo;
    }

    /**
     * Set areas
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $areas
     *
     * @return ActaUtilizada
     */
    public function setAreas(\Usuario\UsuarioBundle\Entity\Area $areas = null)
    {
        $this->areas = $areas;

        return $this;
    }

    /**
     * Get areas
     *
     * @return \Usuario\UsuarioBundle\Entity\Area
     */
    public function getAreas()
    {
        return $this->areas;
    }
}
