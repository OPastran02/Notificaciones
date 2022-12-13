<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Direccion
 *
 * @ORM\Table(name="direccion")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\DireccionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Direccion
{   

    /**     
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Calles")
     * @ORM\JoinColumn(name="Id_Calle", referencedColumnName="id")
     */
    protected $calle;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="direcciones")
     * @ORM\JoinColumn(name="Id_Establecimiento", referencedColumnName="id")
     */
    protected $establecimiento;

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
     * @ORM\Column(name="Id_Establecimiento", type="integer")
     */
    private $idEstablecimiento;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="Id_Calle", type="integer")
     */
    private $idCalle;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=50000)
     * @ORM\Column(name="Altura", type="integer")
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="Piso", type="string", length=20, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="Dpto", type="string", length=20, nullable=true)
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="Local", type="string", length=20, nullable=true)
     */
    private $local;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="Comuna", type="smallint")
     */
    private $comuna;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @ORM\Column(name="Lon", type="float")
     */
    private $lon;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @ORM\Column(name="Lat", type="float")
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
     * @ORM\Column(name="PMatriz", type="integer", nullable=true)
     */
    private $pMatriz;

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
     * @var bool
     *
     * @ORM\Column(name="cmr", type="boolean", nullable=true)
     */
    private $cmr;

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
     * Set idCalle
     *
     * @param integer $idCalle
     *
     * @return Direccion
     */
    public function setIdCalle($idCalle)
    {
        $this->idCalle = $idCalle;

        return $this;
    }

    /**
     * Get idCalle
     *
     * @return int
     */
    public function getIdCalle()
    {
        return $this->idCalle;
    }

    /**
     * Set altura
     *
     * @param integer $altura
     *
     * @return Direccion
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;

        return $this;
    }

    /**
     * Get altura
     *
     * @return int
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
     * @return Direccion
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
     * @return Direccion
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
     * @return Direccion
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
     * @return Direccion
     */
    public function setComuna($comuna)
    {
        $this->comuna = $comuna;

        return $this;
    }

    /**
     * Get comuna
     *
     * @return int
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
     * @return Direccion
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
     * @return Direccion
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
     * @return Direccion
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
     * @return Direccion
     */
    public function setPMatriz($pMatriz)
    {
        $this->pMatriz = $pMatriz;

        return $this;
    }

    /**
     * Get pMatriz
     *
     * @return int
     */
    public function getPMatriz()
    {
        return $this->pMatriz;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Direccion
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
     * @return Direccion
     */
    public function setIdUsuarioCreador($idUsuarioCreador)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

        return $this;
    }

    /**
     * Get idUsuarioCreador
     *
     * @return int
     */
    public function getIdUsuarioCreador()
    {
        return $this->idUsuarioCreador;
    }
    

    /**
     * Set calle
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Calles $calle
     *
     * @return Direccion
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
     * Set idEstablecimiento
     *
     * @param integer $idEstablecimiento
     *
     * @return Direccion
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

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return Direccion
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
        $this->fechaCreado = new \DateTime();
        //$this->setIdUsuarioCreador('45');
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
