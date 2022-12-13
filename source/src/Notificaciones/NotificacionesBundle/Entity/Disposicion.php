<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Disposicion
 *
 * @ORM\Table(name="disposicion")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\DisposicionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"numero", "reparticion","anio"},
 *     errorPath="numero",
 *     message="La disposición ya existe"
 *)
 */
class Disposicion
{

    /**               
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Notificacion", inversedBy="disposicion",cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=true, unique=true, onDelete="cascade")     
     */
    protected $notificacion;   

    /**     
     * @ORM\OneToOne(targetEntity="DisposicionClausura", mappedBy="disposicion",cascade={"persist"})
     */
    protected $clausura;    

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**     
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Reparticion")
     */
    protected $reparticion;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=2050)
     * @ORM\Column(name="anio", type="integer")
     */
    private $anio;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TipoDispo")
     */
    protected $tipo;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="requiereInspector", type="boolean", nullable=true)
     */
    private $requiereInspector;


    public function __toString()
    {
        return 'DI-'.$this->getNumero().'-'.$this->getReparticion()->__toString().'-'.$this->getAnio();
    }   

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Disposicion
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set anio
     *
     * @param integer $anio
     *
     * @return Disposicion
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return integer
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set notificacion
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Notificacion $notificacion
     *
     * @return Disposicion
     */
    public function setNotificacion(\Notificaciones\NotificacionesBundle\Entity\Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;

        return $this;
    }

    /**
     * Get notificacion
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\Notificacion
     */
    public function getNotificacion()
    {
        return $this->notificacion;
    }

    /**
     * Set clausura
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\DisposicionClausura $clausura
     *
     * @return Disposicion
     */
    public function setClausura(\Notificaciones\NotificacionesBundle\Entity\DisposicionClausura $clausura = null)
    {
        $this->clausura = $clausura;

        return $this;
    }

    /**
     * Get clausura
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\DisposicionClausura
     */
    public function getClausura()
    {
        return $this->clausura;
    }

    /**
     * Set reparticion
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Reparticion $reparticion
     *
     * @return Disposicion
     */
    public function setReparticion(\Establecimiento\EstablecimientoBundle\Entity\Reparticion $reparticion = null)
    {
        $this->reparticion = $reparticion;

        return $this;
    }

    /**
     * Get reparticion
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\Reparticion
     */
    public function getReparticion()
    {
        return $this->reparticion;
    }

    /**
     * Set tipo
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\TipoDispo $tipo
     *
     * @return Disposicion
     */
    public function setTipo(\Notificaciones\NotificacionesBundle\Entity\TipoDispo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\TipoDispo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set requiereInspector
     *
     * @param boolean $requiereInspector
     *
     * @return DisposicionClausura
     */
    public function setRequiereInspector($requiereInspector)
    {
        $this->requiereInspector = $requiereInspector;

        return $this;
    }

    /**
     * Get requiereInspector
     *
     * @return boolean
     */
    public function getRequiereInspector()
    {
        return $this->requiereInspector;
    }

    public function getDisposicion()
    {
        return $this;
    }
}
