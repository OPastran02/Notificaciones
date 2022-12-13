<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cedula
 *
 * @ORM\Table(name="cedula")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\CedulaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cedula
{
    private $repository;
    private $contador;

    /**          
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Notificacion", inversedBy="cedula",cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=true, unique=true, onDelete="cascade")     
     */
    protected $notificacion;    

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer", nullable=true,unique=true)
     */
    private $numero;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="TipoCedula")
     */
    protected $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreDestinatario", type="string", length=150,nullable=true)
     */
    private $nombreDestinatario;

    /**
     * @var string
     *
     * @ORM\Column(name="actuacion", type="string", length=100,nullable=true)
     */
    private $actuacion;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @ORM\Column(name="activar_vencimiento", type="boolean")
     */
    private $vencer;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="fojas", type="integer")
     */
    private $fojas;

    /**
     * @var text
     *
     * @ORM\Column(name="cuerpo", type="text")
     */
    private $cuerpo;

    public function setContador($contador)
    {
        $this->contador = $contador;
    }

    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
    
    public function setMaxNumero()
    {   
        if(isset($this->contador)){
            $numero = $this->repository->maxCedula();    
            $this->setNumero($numero[0][1]+1+$this->contador);    
        } 
        
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Cedula
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
     * Set nombreDestinatario
     *
     * @param string $nombreDestinatario
     *
     * @return Cedula
     */
    public function setNombreDestinatario($nombreDestinatario)
    {
        $this->nombreDestinatario = $nombreDestinatario;

        return $this;
    }

    /**
     * Get nombreDestinatario
     *
     * @return string
     */
    public function getNombreDestinatario()
    {
        return $this->nombreDestinatario;
    }

    /**
     * Set actuacion
     *
     * @param string $actuacion
     *
     * @return Cedula
     */
    public function setActuacion($actuacion)
    {
        $this->actuacion = $actuacion;

        return $this;
    }

    /**
     * Get actuacion
     *
     * @return string
     */
    public function getActuacion()
    {
        return $this->actuacion;
    }

    /**
     * Set vencer
     *
     * @param boolean $vencer
     *
     * @return Cedula
     */
    public function setVencer($vencer)
    {
        $this->vencer = $vencer;

        return $this;
    }

    /**
     * Get vencer
     *
     * @return boolean
     */
    public function getVencer()
    {
        return $this->vencer;
    }

    /**
     * Set fojas
     *
     * @param integer $fojas
     *
     * @return Cedula
     */
    public function setFojas($fojas)
    {
        $this->fojas = $fojas;

        return $this;
    }

    /**
     * Get fojas
     *
     * @return integer
     */
    public function getFojas()
    {
        return $this->fojas;
    }

    /**
     * Set cuerpo
     *
     * @param string $cuerpo
     *
     * @return Cedula
     */
    public function setCuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    /**
     * Get cuerpo
     *
     * @return string
     */
    public function getCuerpo()
    {
        return $this->cuerpo;
    }

    /**
     * Set notificacion
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\Notificacion $notificacion
     *
     * @return Cedula
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
     * Set tipo
     *
     * @param \Notificaciones\NotificacionesBundle\Entity\TipoCedula $tipo
     *
     * @return Cedula
     */
    public function setTipo(\Notificaciones\NotificacionesBundle\Entity\TipoCedula $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Notificaciones\NotificacionesBundle\Entity\TipoCedula
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCedula()
    {
        return $this;
    }

    public function getNotificacionById()
    {
        $arrayNoti=array();
        foreach($this->notificacion as $value){
        }
    }

}
