<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pedido
 *
 * @ORM\Table(name="laboratorio_pedido")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\PedidoRepository")
 */
class Pedido
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
     * @ORM\ManyToOne(targetEntity="Programa")
     */
    protected $programa;

    /**          
     * @ORM\ManyToOne(targetEntity="TipoPedido")
     */
    protected $tipoPedido;

    /**          
     * @ORM\ManyToOne(targetEntity="Prioridad")
     */
    protected $prioridad;

    /**          
     * @ORM\ManyToOne(targetEntity="EstadoPedido")
     */
    protected $estadoPedido;

    /**          
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Establecimiento")
     */
    protected $establecimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fecha_Programacion", type="date")
     */
    private $fechaProgramacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="anulado", type="boolean")
     */
    private $anulado;

    /**
     * @var int
     *     
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioAnulador;

    /**
     * @var bool
     *
     * @ORM\Column(name="eliminado", type="boolean")
     */
    private $eliminado;

    /**
     * @var int
     *     
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioEliminador;

    /**
     * @var bool
     *
     * @ORM\Column(name="autorizado", type="boolean")
     */
    private $autorizado;

    /**
     * @var int
     *     
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioAutorizado;

    /**     
     * @ORM\ManyToMany(targetEntity="\Usuario\UsuarioBundle\Entity\Area")
     * @ORM\JoinTable(name="laboratorio_pedido_area",
     *      joinColumns={@ORM\JoinColumn(name="pedido_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="area_id", referencedColumnName="id")}
     *      )
     */
    protected $areas;

    /**     
     * @ORM\OneToMany(targetEntity="Muestra", mappedBy="pedido",cascade={"persist"})
     */
    protected $muestras;

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
     * Set fechaProgramacion
     *
     * @param \DateTime $fechaProgramacion
     *
     * @return pedido
     */
    public function setFechaProgramacion($fechaProgramacion)
    {
        $this->fechaProgramacion = $fechaProgramacion;

        return $this;
    }

    /**
     * Get fechaProgramacion
     *
     * @return \DateTime
     */
    public function getFechaProgramacion()
    {
        return $this->fechaProgramacion;
    }

    /**
     * Set anulado
     *
     * @param boolean $anulado
     *
     * @return pedido
     */
    public function setAnulado($anulado)
    {
        $this->anulado = $anulado;

        return $this;
    }

    /**
     * Get anulado
     *
     * @return bool
     */
    public function getAnulado()
    {
        return $this->anulado;
    }

    /**
     * Set eliminado
     *
     * @param boolean $eliminado
     *
     * @return Pedido
     */
    public function setEliminado($eliminado)
    {
        $this->eliminado = $eliminado;

        return $this;
    }

    /**
     * Get eliminado
     *
     * @return boolean
     */
    public function getEliminado()
    {
        return $this->eliminado;
    }

    /**
     * Set tipoPedido
     *
     * @param \Laboratorio\PedidoBundle\Entity\TipoPedido $tipoPedido
     *
     * @return Pedido
     */
    public function setTipoPedido(\Laboratorio\PedidoBundle\Entity\TipoPedido $tipoPedido = null)
    {
        $this->tipoPedido = $tipoPedido;

        return $this;
    }

    /**
     * Get tipoPedido
     *
     * @return \Laboratorio\PedidoBundle\Entity\TipoPedido
     */
    public function getTipoPedido()
    {
        return $this->tipoPedido;
    }

    /**
     * Set prioridad
     *
     * @param \Laboratorio\PedidoBundle\Entity\Prioridad $prioridad
     *
     * @return Pedido
     */
    public function setPrioridad(\Laboratorio\PedidoBundle\Entity\Prioridad $prioridad = null)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return \Laboratorio\PedidoBundle\Entity\Prioridad
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set estadoPedido
     *
     * @param \Laboratorio\PedidoBundle\Entity\EstadoPedido $estadoPedido
     *
     * @return Pedido
     */
    public function setEstadoPedido(\Laboratorio\PedidoBundle\Entity\EstadoPedido $estadoPedido = null)
    {
        $this->estadoPedido = $estadoPedido;

        return $this;
    }

    /**
     * Get estadoPedido
     *
     * @return \Laboratorio\PedidoBundle\Entity\EstadoPedido
     */
    public function getEstadoPedido()
    {
        return $this->estadoPedido;
    }

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return Pedido
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
     * Set usuarioAnulador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioAnulador
     *
     * @return Pedido
     */
    public function setUsuarioAnulador(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioAnulador = null)
    {
        $this->usuarioAnulador = $usuarioAnulador;

        return $this;
    }

    /**
     * Get usuarioAnulador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioAnulador()
    {
        return $this->usuarioAnulador;
    }

    /**
     * Set usuarioEliminador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioEliminador
     *
     * @return Pedido
     */
    public function setUsuarioEliminador(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioEliminador = null)
    {
        $this->usuarioEliminador = $usuarioEliminador;

        return $this;
    }

    /**
     * Get usuarioEliminador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioEliminador()
    {
        return $this->usuarioEliminador;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return Pedido
     */
    public function addArea(\Usuario\UsuarioBundle\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     */
    public function removeArea(\Usuario\UsuarioBundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Add muestra
     *
     * @param \Laboratorio\PedidoBundle\Entity\Muestra $muestra
     *
     * @return Pedido
     */
    public function addMuestra(\Laboratorio\PedidoBundle\Entity\Muestra $muestra)
    {
        $this->muestras[] = $muestra;

        return $this;
    }

    /**
     * Remove muestra
     *
     * @param \Laboratorio\PedidoBundle\Entity\Muestra $muestra
     */
    public function removeMuestra(\Laboratorio\PedidoBundle\Entity\Muestra $muestra)
    {
        $this->muestras->removeElement($muestra);
    }

    /**
     * Get muestras
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMuestras()
    {
        return $this->muestras;
    }

    /**
     * Set programa
     *
     * @param \Laboratorio\PedidoBundle\Entity\Programa $programa
     *
     * @return Pedido
     */
    public function setPrograma(\Laboratorio\PedidoBundle\Entity\Programa $programa = null)
    {
        $this->programa = $programa;

        return $this;
    }

    /**
     * Get programa
     *
     * @return \Laboratorio\PedidoBundle\Entity\Programa
     */
    public function getPrograma()
    {
        return $this->programa;
    }

    /**
     * Set autorizado
     *
     * @param boolean $autorizado
     *
     * @return Pedido
     */
    public function setAutorizado($autorizado)
    {
        $this->autorizado = $autorizado;

        return $this;
    }

    /**
     * Get autorizado
     *
     * @return boolean
     */
    public function getAutorizado()
    {
        return $this->autorizado;
    }

    /**
     * Set usuarioAutorizado
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioAutorizado
     *
     * @return Pedido
     */
    public function setUsuarioAutorizado(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioAutorizado = null)
    {
        $this->usuarioAutorizado = $usuarioAutorizado;

        return $this;
    }

    /**
     * Get usuarioAutorizado
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioAutorizado()
    {
        return $this->usuarioAutorizado;
    }
}
