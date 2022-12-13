<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pedido
 *
 * @ORM\Table(name="pedido")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\PedidoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pedido
{
    /**
     * @ORM\OneToMany(targetEntity="Notificacion", mappedBy="pedidoNot", cascade={"persist"})
     */
    protected $notificaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="fecha_creado", type="date")
     */
    private $fechaCreado;

    /**
     * @var int
     *     
     * @ORM\Column(name="usuario_creador", type="integer")
     */
    private $usuarioCreador;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="fecha_autorizado", type="date", nullable=true)
     */
    private $fechaAutorizado;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioAutorizador;

    
    /**
    * @ORM\PrePersist    
    */

    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->setUsuarioCreador(1);        
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notificaciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Pedido
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
     * Set usuarioCreador
     *
     * @param integer $usuarioCreador
     *
     * @return Pedido
     */
    public function setUsuarioCreador($usuarioCreador)
    {
        $this->usuarioCreador = $usuarioCreador;

        return $this;
    }

    /**
     * Get usuarioCreador
     *
     * @return integer
     */
    public function getUsuarioCreador()
    {
        return $this->usuarioCreador;
    }

    /**
     * Set fechaAutorizado
     *
     * @param \DateTime $fechaAutorizado
     *
     * @return Pedido
     */
    public function setFechaAutorizado($fechaAutorizado)
    {
        $this->fechaAutorizado = $fechaAutorizado;

        return $this;
    }

    /**
     * Get fechaAutorizado
     *
     * @return \DateTime
     */
    public function getFechaAutorizado()
    {
        return $this->fechaAutorizado;
    }

    /**
     * Set usuarioAutorizador
     *
     * @param integer $usuarioAutorizador
     *
     * @return Pedido
     */
    public function setUsuarioAutorizador($usuarioAutorizador)
    {
        $this->usuarioAutorizador = $usuarioAutorizador;

        return $this;
    }

    /**
     * Get usuarioAutorizador
     *
     * @return integer
     */
    public function getUsuarioAutorizador()
    {
        return $this->usuarioAutorizador;
    }

    /**
     * Add notificaciones
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Notificacion $notificaciones
     *
     * @return Pedido
     */
    public function addNotificacione(\Notificaciones\NotificacionesBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones[] = $notificaciones;

        return $this;
    }

    /**
     * Remove notificaciones
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Notificacion $notificaciones
     */
    public function removeNotificacione(\Notificaciones\NotificacionesBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones->removeElement($notificaciones);
    }

    /**
     * Get notificaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }

    public function __toString()
    {
        return $this->id;
    }
}
