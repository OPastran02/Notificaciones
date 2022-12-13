<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Controladores
 *
 * @ORM\Table(name="controladores")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\ControladorRepository")
 * @UniqueEntity(
 *     fields={"nombre", "apellido","numero","reparticion"},
 *     errorPath="nombre",
 *     message="El controlador ya existe"
 *)
 */
class Controlador
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
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255)
     */
    private $apellido;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=99999999)
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**     
     * @ORM\ManyToOne(targetEntity="\Establecimiento\EstablecimientoBundle\Entity\Reparticion")     
     */
    protected $reparticion;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Controladores
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Controladores
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Controladores
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }    

    public function __toString(){
        return $this->getNombre()." ".$this->getApellido();   
    }



    /**
     * Set reparticion
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Reparticion $reparticion
     *
     * @return Controlador
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
}
