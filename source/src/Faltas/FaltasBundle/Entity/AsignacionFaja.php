<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * AsignacionFaja
 *
 * @ORM\Table(name="faja_asignacion")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\AsignacionFajaRepository") 
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"faja", "idUsuarioInspector","fechaAsignacion"},
 *     errorPath="idUsuarioInspector",
 *     message="La faja ya fue asignada a este inspector"
 *)
 */
class AsignacionFaja
{
    /**
     * @ORM\ManyToOne(targetEntity="Faja", inversedBy="asignaciones")
     * @ORM\JoinColumn(name="id_faja", referencedColumnName="id")
     */
    protected $faja;
    
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
     * @ORM\Column(name="id_faja", type="integer")
     */
    private $idFaja;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\ManytoOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    protected $idUsuarioInspector;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @Assert\NotBlank()
     * @ORM\Column(name="fecha_asignacion", type="date")
     */
    private $fechaAsignacion;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idFaja
     *
     * @param integer $idFaja
     *
     * @return AsignacionFaja
     */
    public function setIdFaja($idFaja)
    {
        $this->idFaja = $idFaja;

        return $this;
    }

    /**
     * Get idFaja
     *
     * @return integer
     */
    public function getIdFaja()
    {
        return $this->idFaja;
    }

    /**
     * Set fechaAsignacion
     *
     * @param \DateTime $fechaAsignacion
     *
     * @return AsignacionFaja
     */
    public function setFechaAsignacion($fechaAsignacion)
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    /**
     * Get fechaAsignacion
     *
     * @return \DateTime
     */
    public function getFechaAsignacion()
    {
        return $this->fechaAsignacion;
    }

    /**
     * Set faja
     *
     * @param \Faltas\FaltasBundle\Entity\Faja $faja
     *
     * @return AsignacionFaja
     */
    public function setFaja(\Faltas\FaltasBundle\Entity\Faja $faja)
    {
        $this->faja = $faja;

        return $this;
    }

    /**
     * Get faja
     *
     * @return \Faltas\FaltasBundle\Entity\Faja
     */
    public function getFaja()
    {
        return $this->faja;
    }

    /**
     * Set idUsuarioInspector
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $idUsuarioInspector
     *
     * @return AsignacionFaja
     */
    public function setIdUsuarioInspector(\Usuario\UsuarioBundle\Entity\Usuarios $idUsuarioInspector = null)
    {
        $this->idUsuarioInspector = $idUsuarioInspector;

        return $this;
    }

    /**
     * Get idUsuarioInspector
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getIdUsuarioInspector()
    {
        return $this->idUsuarioInspector;
    }

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


}
