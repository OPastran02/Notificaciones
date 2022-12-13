<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Historial
 *
 * @ORM\Table(name="historial")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\HistorialRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Historial
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
     * @ORM\Column(name="idTabla", type="integer")
     */
    private $idTabla;

    /**
     * @var string
     *     
     * @ORM\Column(name="tabla", type="string", length=150)
     */
    private $tabla;

    /**
     * @var string
     *     
     * @ORM\Column(name="campo", type="string", length=150)
     */
    private $campo;

    /**     
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios" ,cascade={"persist"})     
     */
    protected $usuarioMotificador;   

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *     
     * @ORM\Column(name="valorAnterior", type="text")
     */
    private $valorAnterior;

    /**
     * @var string
     *     
     * @ORM\Column(name="valorNuevo", type="text")
     */
    private $valorNuevo; 


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
     * Set idTabla
     *
     * @param integer $idTabla
     *
     * @return Historial
     */
    public function setIdTabla($idTabla)
    {
        $this->idTabla = $idTabla;

        return $this;
    }

    /**
     * Get idTabla
     *
     * @return integer
     */
    public function getIdTabla()
    {
        return $this->idTabla;
    }

    /**
     * Set tabla
     *
     * @param string $tabla
     *
     * @return Historial
     */
    public function setTabla($tabla)
    {
        $this->tabla = $tabla;

        return $this;
    }

    /**
     * Get tabla
     *
     * @return string
     */
    public function getTabla()
    {
        return $this->tabla;
    }

    /**
     * Set campo
     *
     * @param string $campo
     *
     * @return Historial
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;

        return $this;
    }

    /**
     * Get campo
     *
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Historial
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set valorAnterior
     *
     * @param string $valorAnterior
     *
     * @return Historial
     */
    public function setValorAnterior($valorAnterior)
    {
        $this->valorAnterior = $valorAnterior;

        return $this;
    }

    /**
     * Get valorAnterior
     *
     * @return string
     */
    public function getValorAnterior()
    {
        return $this->valorAnterior;
    }

    /**
     * Set valorNuevo
     *
     * @param string $valorNuevo
     *
     * @return Historial
     */
    public function setValorNuevo($valorNuevo)
    {
        $this->valorNuevo = $valorNuevo;

        return $this;
    }

    /**
     * Get valorNuevo
     *
     * @return string
     */
    public function getValorNuevo()
    {
        return $this->valorNuevo;
    }

    /**
     * Set usuarioMotificador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioMotificador
     *
     * @return Historial
     */
    public function setUsuarioMotificador(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioMotificador = null)
    {
        $this->usuarioMotificador = $usuarioMotificador;

        return $this;
    }

    /**
     * Get usuarioMotificador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioMotificador()
    {
        return $this->usuarioMotificador;
    }    

    public function __toString()
    {
        return $this->id;
    }

    /**
    * @ORM\PrePersist    
    */
    public function setFechaValue()
    {
        $this->fecha = new \DateTime("-3 hours");        
    }
}
