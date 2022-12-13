<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DireccionRS
 *
 * @ORM\Table(name="direccion_rs")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\DireccionRSRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DireccionRS
{   

    /**
     * @ORM\ManyToOne(targetEntity="Calles")
     * @ORM\JoinColumn(name="Id_Calle", referencedColumnName="id")
     */
    protected $calle;

    /**
     * @ORM\ManyToOne(targetEntity="RazonSocial", inversedBy="direcciones")
     * @ORM\JoinColumn(name="Id_RazonSocial", referencedColumnName="id")
     */
    protected $razonSocial;

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
     * @ORM\Column(name="Id_RazonSocial", type="integer")
     */
    private $idRazonSocial;

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
     * @ORM\Column(name="Piso", type="string", length=6, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="Dpto", type="string", length=6, nullable=true)
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="Local", type="string", length=6, nullable=true)
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
     * @ORM\Column(name="Lon", type="float")
     */
    private $lon;

    /**
     * @var float
     *
     * @Assert\NotBlank()
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
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        ///$this->setIdUsuarioCreador('45');
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
     * Set idRazonSocial
     *
     * @param integer $idRazonSocial
     *
     * @return DireccionRS
     */
    public function setIdRazonSocial($idRazonSocial)
    {
        $this->idRazonSocial = $idRazonSocial;

        return $this;
    }

    /**
     * Get idRazonSocial
     *
     * @return integer
     */
    public function getIdRazonSocial()
    {
        return $this->idRazonSocial;
    }

    /**
     * Set idCalle
     *
     * @param integer $idCalle
     *
     * @return DireccionRS
     */
    public function setIdCalle($idCalle)
    {
        $this->idCalle = $idCalle;

        return $this;
    }

    /**
     * Get idCalle
     *
     * @return integer
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * @return DireccionRS
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
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return DireccionRS
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
     * @return DireccionRS
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
     * Set calle
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Calles $calle
     *
     * @return DireccionRS
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
     * Set razonSocial
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\RazonSocial $razonSocial
     *
     * @return DireccionRS
     */
    public function setRazonSocial(\Establecimiento\EstablecimientoBundle\Entity\RazonSocial $razonSocial = null)
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

    public function __toString()
    {
        return $this->getCalle()->getCalle()." ".$this->getAltura()." ".$this->getPiso()." ".$this->getDpto()." ".$this->getLocal();
    }

    /**
     * Set cmr
     *
     * @param boolean $cmr
     *
     * @return DireccionRS
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
