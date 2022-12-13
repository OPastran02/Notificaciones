<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacion")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\NotificacionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notificacion
{   

    /**     
     * @ORM\OneToOne(targetEntity="Cedula", mappedBy="notificacion",cascade={"persist"})
     */
    protected $cedula;

    /**     
     * @ORM\OneToOne(targetEntity="Disposicion", mappedBy="notificacion",cascade={"persist"})
     */
    protected $disposicion;

    /**
     * @var int
     *     
     * @ORM\ManyToOne(targetEntity="Pedido", inversedBy="notificaciones")     
     * @ORM\JoinColumn(name="id_pedido", referencedColumnName="id")
     */
    protected $pedidoNot;   

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
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Establecimiento")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id")     
     */
    protected $establecimiento;

    /**
     * @var int
     *     
     * @ORM\Column(name="establecimiento_id", type="integer")
     */
    private $idEstablecimiento;

    /**
     * @var int
     *     
     * @ORM\Column(name="plazo1", type="integer")
     */
    private $plazo1;

    /**
     * @var int
     *     
     * @ORM\Column(name="plazo2", type="integer")
     */
    private $plazo2;

    /**
     * @var int
     *     
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")     
     */
    protected $notificador;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_entrega", type="date", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_devolucion", type="date", nullable=true)
     */
    private $fechaDevolucion;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_notificacion", type="date", nullable=true)
     */
    private $fechaNotificacion;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_envio_firma", type="date", nullable=true)
     */
    private $fechaEnvioFirma;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_vuelta_firma", type="date", nullable=true)
     */
    private $fechaVueltaFirma;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     */
    protected $estado;

    /**
     * @var int
     *     
     * @ORM\Column(name="usuario_modificador", type="integer")
     */
    private $usuarioModificador;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_modificado", type="date")
     */
    private $fechaModificado;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="prorroga", type="boolean", nullable=true)
     */
    private $prorroga;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="presentacion_agregar", type="boolean", nullable=true)
     */
    private $presentacionAgregar;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="art61", type="boolean", nullable=true)
     */
    private $art61;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="citacion", type="boolean", nullable=true)
     */
    private $citacion;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="nocturnidad", type="boolean", nullable=true)
     */
    private $nocturnidad;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_notificada", type="string", length=100)
     */
    private $direccionNotificada;

    /**
     * @var smallint
     *
     * @ORM\Column(name="comuna_notificada", type="smallint")
     */
    private $comunaNotificada;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_domicilio_notificado", type="string", length=1)
     */
    private $TipoDomicilioNotificada;


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
     * @var int
     *
     * @ORM\Column(name="id_pedido", type="integer")     
     */
    private $idPedido;

    /**
     * @var int
     *
     * @ORM\Column(name="usuario_eliminador", type="integer", nullable=true)
     */
    private $usuarioEliminador;    

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setFechaModificadoValue()
    {
        $this->fechaModificado = new \DateTime();        
        //$this->setUsuarioModificador(1);
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
     * Set plazo1
     *
     * @param integer $plazo1
     *
     * @return Notificacion
     */
    public function setPlazo1($plazo1)
    {
        $this->plazo1 = $plazo1;

        return $this;
    }

    /**
     * Get plazo1
     *
     * @return integer
     */
    public function getPlazo1()
    {
        return $this->plazo1;
    }

    /**
     * Set plazo2
     *
     * @param integer $plazo2
     *
     * @return Notificacion
     */
    public function setPlazo2($plazo2)
    {
        $this->plazo2 = $plazo2;

        return $this;
    }

    /**
     * Get plazo2
     *
     * @return integer
     */
    public function getPlazo2()
    {
        return $this->plazo2;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     *
     * @return Notificacion
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set fechaDevolucion
     *
     * @param \DateTime $fechaDevolucion
     *
     * @return Notificacion
     */
    public function setFechaDevolucion($fechaDevolucion)
    {
        $this->fechaDevolucion = $fechaDevolucion;

        return $this;
    }

    /**
     * Get fechaDevolucion
     *
     * @return \DateTime
     */
    public function getFechaDevolucion()
    {
        return $this->fechaDevolucion;
    }

    /**
     * Set fechaNotificacion
     *
     * @param \DateTime $fechaNotificacion
     *
     * @return Notificacion
     */
    public function setFechaNotificacion($fechaNotificacion)
    {
        $this->fechaNotificacion = $fechaNotificacion;

        return $this;
    }

    /**
     * Get fechaNotificacion
     *
     * @return \DateTime
     */
    public function getFechaNotificacion()
    {
        return $this->fechaNotificacion;
    }

    /**
     * Set fechaEnvioFirma
     *
     * @param \DateTime $fechaEnvioFirma
     *
     * @return Notificacion
     */
    public function setFechaEnvioFirma($fechaEnvioFirma)
    {
        $this->fechaEnvioFirma = $fechaEnvioFirma;

        return $this;
    }

    /**
     * Get fechaEnvioFirma
     *
     * @return \DateTime
     */
    public function getFechaEnvioFirma()
    {
        return $this->fechaEnvioFirma;
    }

    /**
     * Set fechaVueltaFirma
     *
     * @param \DateTime $fechaVueltaFirma
     *
     * @return Notificacion
     */
    public function setFechaVueltaFirma($fechaVueltaFirma)
    {
        $this->fechaVueltaFirma = $fechaVueltaFirma;

        return $this;
    }

    /**
     * Get fechaVueltaFirma
     *
     * @return \DateTime
     */
    public function getFechaVueltaFirma()
    {
        return $this->fechaVueltaFirma;
    }

    /**
     * Set usuarioModificador
     *
     * @param integer $usuarioModificador
     *
     * @return Notificacion
     */
    public function setUsuarioModificador($usuarioModificador)
    {
        $this->usuarioModificador = $usuarioModificador;

        return $this;
    }

    /**
     * Get usuarioModificador
     *
     * @return integer
     */
    public function getUsuarioModificador()
    {
        return $this->usuarioModificador;
    }

    /**
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return Notificacion
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
     * Set prorroga
     *
     * @param boolean $prorroga
     *
     * @return Notificacion
     */
    public function setProrroga($prorroga)
    {
        $this->prorroga = $prorroga;

        return $this;
    }

    /**
     * Get prorroga
     *
     * @return boolean
     */
    public function getProrroga()
    {
        return $this->prorroga;
    }

    /**
     * Set presentacionAgregar
     *
     * @param boolean $presentacionAgregar
     *
     * @return Notificacion
     */
    public function setPresentacionAgregar($presentacionAgregar)
    {
        $this->presentacionAgregar = $presentacionAgregar;

        return $this;
    }

    /**
     * Get presentacionAgregar
     *
     * @return boolean
     */
    public function getPresentacionAgregar()
    {
        return $this->presentacionAgregar;
    }

    /**
     * Set art61
     *
     * @param boolean $art61
     *
     * @return Notificacion
     */
    public function setArt61($art61)
    {
        $this->art61 = $art61;

        return $this;
    }

    /**
     * Get art61
     *
     * @return boolean
     */
    public function getArt61()
    {
        return $this->art61;
    }

    /**
     * Set citacion
     *
     * @param boolean $citacion
     *
     * @return Notificacion
     */
    public function setCitacion($citacion)
    {
        $this->citacion = $citacion;

        return $this;
    }

    /**
     * Get citacion
     *
     * @return boolean
     */
    public function getCitacion()
    {
        return $this->citacion;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Notificacion
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
     * Set direccionNotificada
     *
     * @param string $direccionNotificada
     *
     * @return Notificacion
     */
    public function setDireccionNotificada($direccionNotificada)
    {
        $this->direccionNotificada = $direccionNotificada;

        return $this;
    }

    /**
     * Get direccionNotificada
     *
     * @return string
     */
    public function getDireccionNotificada()
    {
        return $this->direccionNotificada;
    }

    /**
     * Set idPedido
     *
     * @param integer $idPedido
     *
     * @return Notificacion
     */
    public function setIdPedido($idPedido)
    {
        $this->idPedido = $idPedido;

        return $this;
    }

    /**
     * Get idPedido
     *
     * @return integer
     */
    public function getIdPedido()
    {
        return $this->idPedido;
    }

    /**
     * Set cedula
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Cedula $cedula
     *
     * @return Notificacion
     */
    public function setCedula(\Notificaciones\NotificacionesBundle\Entity\Cedula $cedula = null)
    {
        $this->cedula = $cedula;

        return $this;
    }

    /**
     * Get cedula
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Cedula
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * Set disposicion
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Cedula $cedula
     *
     * @return Notificacion
     */
    public function setDisposicion(\Notificaciones\NotificacionesBundle\Entity\Disposicion $disposicion = null)
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
     * Set pedidoNot
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Pedido $pedidoNot
     *
     * @return Notificacion
     */
    public function setPedidoNot(\Notificaciones\NotificacionesBundle\Entity\Pedido $pedidoNot = null)
    {
        $this->pedidoNot = $pedidoNot;

        return $this;
    }

    /**
     * Get pedidoNot
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Pedido
     */
    public function getPedidoNot()
    {
        return $this->pedidoNot;
    }

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return Notificacion
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
     * Set notificador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $notificador
     *
     * @return Notificacion
     */
    public function setNotificador(\Usuario\UsuarioBundle\Entity\Usuarios $notificador = null)
    {
        $this->notificador = $notificador;

        return $this;
    }

    /**
     * Get notificador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getNotificador()
    {
        return $this->notificador;
    }

    /**
     * Set estado
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Estado $estado
     *
     * @return Notificacion
     */
    public function setEstado(\Notificaciones\NotificacionesBundle\Entity\Estado $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set lon
     *
     * @param float $lon
     *
     * @return Notificacion
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
     * @return Notificacion
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
     * Set nocturnidad
     *
     * @param boolean $nocturnidad
     *
     * @return Notificacion
     */
    public function setNocturnidad($nocturnidad)
    {
        $this->nocturnidad = $nocturnidad;

        return $this;
    }

    /**
     * Get nocturnidad
     *
     * @return boolean
     */
    public function getNocturnidad()
    {
        return $this->nocturnidad;
    }

    /**
     * Set usuarioEliminador
     *
     * @param integer $usuarioEliminador
     *
     * @return Notificacion
     */
    public function setUsuarioEliminador($usuarioEliminador)
    {
        $this->usuarioEliminador = $usuarioEliminador;

        return $this;
    }

    /**
     * Get usuarioEliminador
     *
     * @return integer
     */
    public function getUsuarioEliminador()
    {
        return $this->usuarioEliminador;
    }

    /**
     * Set comunaNotificada
     *
     * @param integer $comunaNotificada
     *
     * @return Notificacion
     */
    public function setComunaNotificada($comunaNotificada)
    {
        $this->comunaNotificada = $comunaNotificada;

        return $this;
    }

    /**
     * Get comunaNotificada
     *
     * @return integer
     */
    public function getComunaNotificada()
    {
        return $this->comunaNotificada;
    }

    /**
     * Set tipoDomicilioNotificada
     *
     * @param string $tipoDomicilioNotificada
     *
     * @return Notificacion
     */
    public function setTipoDomicilioNotificada($tipoDomicilioNotificada)
    {
        $this->TipoDomicilioNotificada = $tipoDomicilioNotificada;

        return $this;
    }

    /**
     * Get tipoDomicilioNotificada
     *
     * @return string
     */
    public function getTipoDomicilioNotificada()
    {
        return $this->TipoDomicilioNotificada;
    }

    /**
     * Set idEstablecimiento
     *
     * @param integer $idEstablecimiento
     *
     * @return Notificacion
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
     * Get idEstablecimiento
     *
     * @return integer
     */
    public function getIdVistaNotificacion()
    {
        return $this->idEstablecimiento;
    }

    public function __toString()
    {
        return ''.$this->id;
    }
}
