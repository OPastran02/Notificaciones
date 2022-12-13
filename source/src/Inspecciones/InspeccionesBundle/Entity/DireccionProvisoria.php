<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DireccionProvisoria
 *
 * @ORM\Table(name="direccion_provisoria")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\DireccionProvisoriaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DireccionProvisoria
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
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Calles")
     */
    protected $calle;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=50000)
     * @ORM\Column(name="altura", type="integer")
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=6, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=6, nullable=true)
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="local", type="string", length=6, nullable=true)
     */
    private $local;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="comuna", type="smallint")
     */
    private $comuna;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @ORM\Column(name="lon", type="float")
     */
    private $lon;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="SMP", type="string", length=20)
     */
    private $sMP;

    /**
     * @var int
     *
     * @ORM\Column(name="pMatriz", type="integer", nullable=true)
     */
    private $pMatriz;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenInspeccion", inversedBy="direcciones")
     * @ORM\JoinColumn(name="orden_inspeccion_id", referencedColumnName="id")
     */
    protected $ordenInspeccion;

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
     * @var bool
     *
     * @ORM\Column(name="cmr", type="boolean", nullable=true)
     */
    private $cmr;


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
     * Set altura
     *
     * @param integer $altura
     *
     * @return DireccionProvisoria
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;

        return $this;
    }

    /**
     * Get altura
     *
     * @return integer
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * Set piso
     *
     * @param string $piso
     *
     * @return DireccionProvisoria
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set dpto
     *
     * @param string $dpto
     *
     * @return DireccionProvisoria
     */
    public function setDpto($dpto)
    {
        $this->dpto = $dpto;

        return $this;
    }

    /**
     * Get dpto
     *
     * @return string
     */
    public function getDpto()
    {
        return $this->dpto;
    }

    /**
     * Set local
     *
     * @param string $local
     *
     * @return DireccionProvisoria
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set comuna
     *
     * @param integer $comuna
     *
     * @return DireccionProvisoria
     */
    public function setComuna($comuna)
    {
        $this->comuna = $comuna;

        return $this;
    }

    /**
     * Get comuna
     *
     * @return integer
     */
    public function getComuna()
    {
        return $this->comuna;
    }

    /**
     * Set lon
     *
     * @param float $lon
     *
     * @return DireccionProvisoria
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return DireccionProvisoria
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set sMP
     *
     * @param string $sMP
     *
     * @return DireccionProvisoria
     */
    public function setSMP($sMP)
    {
        $this->sMP = $sMP;

        return $this;
    }

    /**
     * Get sMP
     *
     * @return string
     */
    public function getSMP()
    {
        return $this->sMP;
    }

    /**
     * Set pMatriz
     *
     * @param integer $pMatriz
     *
     * @return DireccionProvisoria
     */
    public function setPMatriz($pMatriz)
    {
        $this->pMatriz = $pMatriz;

        return $this;
    }

    /**
     * Get pMatriz
     *
     * @return integer
     */
    public function getPMatriz()
    {
        return $this->pMatriz;
    }

    /**
     * Set calle
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Calles $calle
     *
     * @return DireccionProvisoria
     */
    public function setCalle(\Establecimiento\EstablecimientoBundle\Entity\Calles $calle = null)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\Calles
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return DireccionProvisoria
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
     * @return DireccionProvisoria
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
     * @return DireccionProvisoria
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
     * @return DireccionProvisoria
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
     * @return DireccionProvisoria
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
        return $this->getCalle()->getCalle()." ".$this->getAltura()." ".$this->getPiso()." ".$this->getDpto()." ".$this->getLocal();
    }

    /**
     * Set cmr
     *
     * @param boolean $cmr
     *
     * @return Direccion
     */
    public function setCmr($cmr)
    {
        $this->cmr = $cmr;

        return $this;
    }

    /**
     * Get cmr
     *
     * @return boolean
     */
    public function getCmr()
    {
        return $this->cmr;
    }
}
