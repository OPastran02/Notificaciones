<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * RazonSocial
 *
 * @ORM\Table(name="razon_social")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\RazonSocialRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"cuit"}
 *)
 */
class RazonSocial
{   

    /**     
     * @ORM\OneToMany(targetEntity="EstablecimientoRazonSocial", mappedBy="razonSocial")
     */
    protected $establecimientos;    

    /**
     * @ORM\OneToMany(targetEntity="DireccionRS", mappedBy="razonSocial")
     */
    protected $direcciones;

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
     * @ORM\Column(name="cuit", type="bigint", unique=true)
     */
    private $cuit;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="tipo", type="string", length=1)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @Assert\Email()
     * @ORM\Column(name="mail", type="string", length=100, nullable=true)
     */
    private $mail;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="nombre1", type="string", length=100)
     */
    private $nombre1;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre2", type="string", length=100, nullable=true)
     */
    private $nombre2;

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
     * Constructor
     */
    public function __construct()
    {
        $this->direcciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->establecimientos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->setFechaCreado(new \DateTime());
        //$this->setIdUsuarioCreador('45');
    }     

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setFechaModificadoValue()
    {
        $this->fechaModificado = new \DateTime();        
        //$this->idUsuarioModificador = 1;
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
     * Set cuit
     *
     * @param integer $cuit
     *
     * @return RazonSocial
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return integer
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return RazonSocial
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return RazonSocial
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return RazonSocial
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set nombre1
     *
     * @param string $nombre1
     *
     * @return RazonSocial
     */
    public function setNombre1($nombre1)
    {
        $this->nombre1 = $nombre1;

        return $this;
    }

    /**
     * Get nombre1
     *
     * @return string
     */
    public function getNombre1()
    {
        return $this->nombre1;
    }

    /**
     * Set nombre2
     *
     * @param string $nombre2
     *
     * @return RazonSocial
     */
    public function setNombre2($nombre2)
    {
        $this->nombre2 = $nombre2;

        return $this;
    }

    /**
     * Get nombre2
     *
     * @return string
     */
    public function getNombre2()
    {
        return $this->nombre2;
    }    

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return RazonSocial
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }
    

    /**
     * Set idUsuarioModificador
     *
     * @param integer $idUsuarioCreador
     *
     * @return RazonSocial
     */
    public function setIdUsuarioCreador($idUsuarioCreador)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

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
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return RazonSocial
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;                
    }

    /**
     * Set idUsuarioModificador
     *
     * @param integer $idUsuarioModificador
     *
     * @return RazonSocial
     */
    public function setIdUsuarioModificador($idUsuarioModificador)
    {
        $this->idUsuarioModificador = $idUsuarioModificador;

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
     * Add establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return RazonSocial
     */
    public function addEstablecimiento(\Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento)
    {
        $this->establecimientos[] = $establecimiento;

        return $this;
    }

    public function setEstablecimiento($establecimiento)
    {
        $this->establecimientos = $establecimiento;        
    }

    /**
     * Remove establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     */
    public function removeEstablecimiento(\Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento)
    {
        $this->establecimientos->removeElement($establecimiento);
    }

    /**
     * Get establecimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstablecimientos()
    {
        return $this->establecimientos;
    }

    /**
     * Add direccione
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\DireccionRS $direccione
     *
     * @return RazonSocial
     */
    public function addDireccione(\Establecimiento\EstablecimientoBundle\Entity\DireccionRS $direccione)
    {
        $this->direcciones[] = $direccione;

        return $this;
    }

    /**
     * Remove direccione
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\DireccionRS $direccione
     */
    public function removeDireccione(\Establecimiento\EstablecimientoBundle\Entity\DireccionRS $direccione)
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
    
    public function __toString()
    {
        return $this->nombre1.' '.$this->nombre2.' CUIT: '.$this->cuit;
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
     * Get idUsuarioModificador
     *
     * @return integer
     */
    public function getIdUsuarioModificador()
    {
        return $this->idUsuarioModificador;
    }
}
