<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * DisposicionClausura
 *
 * @ORM\Table(name="disposicion_clausura")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\DisposicionClausuraRepository")
 */
class DisposicionClausura
{

    /**     
     * @ORM\Id     
     * @ORM\OneToOne(targetEntity="Disposicion", inversedBy="clausura",cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false, unique=true, onDelete="cascade")     
     */
    protected $disposicion; 

    /**     
     * @ORM\ManyToMany(targetEntity="LeyesClausura")
     * @ORM\JoinTable(name="disposicion_ley",
     *      joinColumns={@ORM\JoinColumn(name="disposicion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ley_id", referencedColumnName="id")}
     *      )
     */
    protected $leyes;
    

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @Assert\NotBlank()
     * @ORM\Column(name="fecha_clausura", type="date")
     */
    private $fechaClausura;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Faltas\FaltasBundle\Entity\TipoClausura")
     */
    protected $alcance;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="levantada", type="boolean", nullable=true)
     */
    private $levantada;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_levantamiento", type="date", nullable=true)
     */
    private $fechaLevantamiento;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="numero_nota_dgai", type="integer", nullable=true)
     */
    private $numeroNotaDgai;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=2020)
     * @ORM\Column(name="anio_nota_dgai", type="integer", nullable=true)
     */
    private $anioNotaDgai;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Controlador")
     */
    protected $controlador;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="numero_giro_documental", type="integer", nullable=true)
     */
    private $numeroGiroDocumental;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=2020)
     * @ORM\Column(name="anio_giro_documental", type="integer", nullable=true)
     */
    private $anioGiroDocumental;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\TipoActuacion")
     */
    protected $tipoActuacionRemicion;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="numero_actuacion_remicion", type="integer", nullable=true)
     */
    private $numeroActuacionRemicion;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=2020)
     * @ORM\Column(name="anio_actuacion_remicion", type="integer", nullable=true)
     */
    private $anioActuacionRemicion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->leyes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set fechaClausura
     *
     * @param \DateTime $fechaClausura
     *
     * @return DisposicionClausura
     */
    public function setFechaClausura($fechaClausura)
    {
        $this->fechaClausura = $fechaClausura;

        return $this;
    }

    /**
     * Get fechaClausura
     *
     * @return \DateTime
     */
    public function getFechaClausura()
    {
        return $this->fechaClausura;
    }

    /**
     * Set levantada
     *
     * @param boolean $levantada
     *
     * @return DisposicionClausura
     */
    public function setLevantada($levantada)
    {
        $this->levantada = $levantada;

        return $this;
    }

    /**
     * Get levantada
     *
     * @return boolean
     */
    public function getLevantada()
    {
        return $this->levantada;
    }

    /**
     * Set fechaLevantamiento
     *
     * @param \DateTime $fechaLevantamiento
     *
     * @return DisposicionClausura
     */
    public function setFechaLevantamiento($fechaLevantamiento)
    {
        $this->fechaLevantamiento = $fechaLevantamiento;

        return $this;
    }

    /**
     * Get fechaLevantamiento
     *
     * @return \DateTime
     */
    public function getFechaLevantamiento()
    {
        return $this->fechaLevantamiento;
    }

    /**
     * Set numeroNotaDgai
     *
     * @param integer $numeroNotaDgai
     *
     * @return DisposicionClausura
     */
    public function setNumeroNotaDgai($numeroNotaDgai)
    {
        $this->numeroNotaDgai = $numeroNotaDgai;

        return $this;
    }

    /**
     * Get numeroNotaDgai
     *
     * @return integer
     */
    public function getNumeroNotaDgai()
    {
        return $this->numeroNotaDgai;
    }

    /**
     * Set anioNotaDgai
     *
     * @param integer $anioNotaDgai
     *
     * @return DisposicionClausura
     */
    public function setAnioNotaDgai($anioNotaDgai)
    {
        $this->anioNotaDgai = $anioNotaDgai;

        return $this;
    }

    /**
     * Get anioNotaDgai
     *
     * @return integer
     */
    public function getAnioNotaDgai()
    {
        return $this->anioNotaDgai;
    }

    /**
     * Set numeroGiroDocumental
     *
     * @param integer $numeroGiroDocumental
     *
     * @return DisposicionClausura
     */
    public function setNumeroGiroDocumental($numeroGiroDocumental)
    {
        $this->numeroGiroDocumental = $numeroGiroDocumental;

        return $this;
    }

    /**
     * Get numeroGiroDocumental
     *
     * @return integer
     */
    public function getNumeroGiroDocumental()
    {
        return $this->numeroGiroDocumental;
    }

    /**
     * Set anioGiroDocumental
     *
     * @param integer $anioGiroDocumental
     *
     * @return DisposicionClausura
     */
    public function setAnioGiroDocumental($anioGiroDocumental)
    {
        $this->anioGiroDocumental = $anioGiroDocumental;

        return $this;
    }

    /**
     * Get anioGiroDocumental
     *
     * @return integer
     */
    public function getAnioGiroDocumental()
    {
        return $this->anioGiroDocumental;
    }

    /**
     * Set numeroActuacionRemicion
     *
     * @param integer $numeroActuacionRemicion
     *
     * @return DisposicionClausura
     */
    public function setNumeroActuacionRemicion($numeroActuacionRemicion)
    {
        $this->numeroActuacionRemicion = $numeroActuacionRemicion;

        return $this;
    }

    /**
     * Get numeroActuacionRemicion
     *
     * @return integer
     */
    public function getNumeroActuacionRemicion()
    {
        return $this->numeroActuacionRemicion;
    }

    /**
     * Set anioActuacionRemicion
     *
     * @param integer $anioActuacionRemicion
     *
     * @return DisposicionClausura
     */
    public function setAnioActuacionRemicion($anioActuacionRemicion)
    {
        $this->anioActuacionRemicion = $anioActuacionRemicion;

        return $this;
    }

    /**
     * Get anioActuacionRemicion
     *
     * @return integer
     */
    public function getAnioActuacionRemicion()
    {
        return $this->anioActuacionRemicion;
    }

    /**
     * Set disposicion
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Disposicion $disposicion
     *
     * @return DisposicionClausura
     */
    public function setDisposicion(\Notificaciones\NotificacionesBundle\Entity\Disposicion $disposicion)
    {
        $this->disposicion = $disposicion;

        return $this;
    }

    /**
     * Get disposicion
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Disposicion
     */
    public function getDisposicion()
    {
        return $this->disposicion;
    }

    /**
     * Add leye
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\LeyesClausura $leye
     *
     * @return DisposicionClausura
     */
    public function addLeye(\Notificaciones\NotificacionesBundle\Entity\LeyesClausura $leye)
    {
        $this->leyes[] = $leye;

        return $this;
    }

    /**
     * Remove leye
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\LeyesClausura $leye
     */
    public function removeLeye(\Notificaciones\NotificacionesBundle\Entity\LeyesClausura $leye)
    {
        $this->leyes->removeElement($leye);
    }

    /**
     * Get leyes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeyes()
    {
        return $this->leyes;
    }

    /**
     * Set alcance
     *
     * @param \Faltas\FaltasBundle\Entity\TipoClausura $alcance
     *
     * @return DisposicionClausura
     */
    public function setAlcance(\Faltas\FaltasBundle\Entity\TipoClausura $alcance = null)
    {
        $this->alcance = $alcance;

        return $this;
    }

    /**
     * Get alcance
     *
     * @return \Faltas\FaltasBundle\Entity\TipoClausura
     */
    public function getAlcance()
    {
        return $this->alcance;
    }

    /**
     * Set controlador
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Controlador $controlador
     *
     * @return DisposicionClausura
     */
    public function setControlador(\Notificaciones\NotificacionesBundle\Entity\Controlador $controlador = null)
    {
        $this->controlador = $controlador;

        return $this;
    }

    /**
     * Get controlador
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Controlador
     */
    public function getControlador()
    {
        return $this->controlador;
    }

    /**
     * Set tipoActuacionRemicion
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoActuacion $tipoActuacionRemicion
     *
     * @return DisposicionClausura
     */
    public function setTipoActuacionRemicion(\Establecimiento\EstablecimientoBundle\Entity\TipoActuacion $tipoActuacionRemicion = null)
    {
        $this->tipoActuacionRemicion = $tipoActuacionRemicion;

        return $this;
    }

    /**
     * Get tipoActuacionRemicion
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\TipoActuacion
     */
    public function getTipoActuacionRemicion()
    {
        return $this->tipoActuacionRemicion;
    }
}
