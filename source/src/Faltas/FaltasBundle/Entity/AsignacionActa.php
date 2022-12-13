<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Asignacion
 *
 * @ORM\Table(name="acta_asignacion")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\AsignacionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"acta", "inspector","fecha"},
 *     errorPath="inspector",
 *     message="El acta ya fue asignada a este inspector"
 *)
 */
class AsignacionActa
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
     * @ORM\ManyToOne(targetEntity="Acta", inversedBy="AsignacionActa")     
     * @ORM\JoinColumn(name="acta_id", referencedColumnName="id")
     */
    protected $acta;

    /** 
     * @Assert\NotBlank()  
     * @ORM\ManyToOne(targetEntity="Usuario\UsuarioBundle\Entity\Usuarios")
     */
    protected $inspector;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @Assert\NotBlank()
     * @ORM\Column(name="fecha", type="date")
     *
     */
    private $fecha;

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
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return AsignacionActa
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
     * @return AsignacionActa
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
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return AsignacionActa
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
     * Set idUsuarioModificador
     *
     * @param integer $idUsuarioModificador
     *
     * @return AsignacionActa
     */
    public function setIdUsuarioModificador($idUsuarioModificador)
    {
        $this->idUsuarioModificador = $idUsuarioModificador;

        return $this;
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

    
    /**
    * @ORM\PrePersist    
    */

    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->setIdUsuarioCreador(1);        
    }

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setFechaModificadoValue()
    {
        $this->fechaModificado = new \DateTime();        
        //$this->setIdUsuarioModificador(1);
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return AsignacionActa
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
     * Set acta
     *
     * @param \Faltas\FaltasBundle\Entity\Acta $acta
     *
     * @return AsignacionActa
     */
    public function setActa(\Faltas\FaltasBundle\Entity\Acta $acta = null)
    {
        $this->acta = $acta;

        return $this;
    }

    /**
     * Get acta
     *
     * @return \Faltas\FaltasBundle\Entity\Acta
     */
    public function getActa()
    {
        return $this->acta;
    }

    /**
     * Set inspector
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $inspector
     *
     * @return AsignacionActa
     */
    public function setInspector(\Usuario\UsuarioBundle\Entity\Usuarios $inspector = null)
    {
        $this->inspector = $inspector;

        return $this;
    }

    /**
     * Get inspector
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getInspector()
    {
        return $this->inspector;
    }

    public function __toString()
    {
        return $this->id;
    }
}
