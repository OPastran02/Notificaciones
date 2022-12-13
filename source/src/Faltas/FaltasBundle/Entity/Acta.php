<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Acta
 *
 * @ORM\Table(name="acta")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\ActaRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"serie", "numero"},
 *     errorPath="numero",
 *     message="El acta ya existe en el sistema."
 *)    
 */
class Acta
{

    /**          
     * @ORM\OneToOne(targetEntity="ActaUtilizada", mappedBy="acta",cascade={"persist"})     
     */
    protected $ActaUtilizada;    

    /**
     * @ORM\OneToMany(targetEntity="AsignacionActa", mappedBy="acta")
     * @ORM\OrderBy({"fecha" = "DESC"})
     */
    protected $AsignacionActa;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion", inversedBy="actas")
     * @ORM\JoinColumn(name="id_inspeccion", referencedColumnName="id")
     */
    protected $ordenInspeccion;



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
     * @Assert\Length(
     *      min = 1,
     *      max = 2,
     *      minMessage = "minimo 1 caracter",
     *      maxMessage = "maximo 2 caracteres"
     * )
     * @ORM\Column(name="Serie", type="string", length=2)
     */
    private $serie;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=9999999999)
     * @ORM\Column(name="Numero", type="integer")
     */
    private $numero;

    /** 
     * @Assert\NotBlank()  
     * @ORM\ManyToOne(targetEntity="EstadoActa")     
     */
    protected $estado;

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
     * Constructor
     */
    public function __construct()
    {
        $this->AsignacionActa = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set serie
     *
     * @param string $serie
     *
     * @return Acta
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Acta
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
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Acta
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
     * @return Acta
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
     * @return Acta
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
     * @return Acta
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
     * Set actaUtilizada
     *
     * @param \Faltas\FaltasBundle\Entity\ActaUtilizada $actaUtilizada
     *
     * @return Acta
     */
    public function setActaUtilizada(\Faltas\FaltasBundle\Entity\ActaUtilizada $actaUtilizada = null)
    {
        $this->ActaUtilizada = $actaUtilizada;

        return $this;
    }

    /**
     * Get actaUtilizada
     *
     * @return \Faltas\FaltasBundle\Entity\ActaUtilizada
     */
    public function getActaUtilizada()
    {
        return $this->ActaUtilizada;
    }

    /**
     * Add asignacionActum
     *
     * @param \Faltas\FaltasBundle\Entity\AsignacionActa $asignacionActum
     *
     * @return Acta
     */
    public function addAsignacionActum(\Faltas\FaltasBundle\Entity\AsignacionActa $asignacionActum)
    {
        $this->AsignacionActa[] = $asignacionActum;

        return $this;
    }

    /**
     * Remove asignacionActum
     *
     * @param \Faltas\FaltasBundle\Entity\AsignacionActa $asignacionActum
     */
    public function removeAsignacionActum(\Faltas\FaltasBundle\Entity\AsignacionActa $asignacionActum)
    {
        $this->AsignacionActa->removeElement($asignacionActum);
    }

    /**
     * Get asignacionActa
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAsignacionActa()
    {
        return $this->AsignacionActa;
    }

    /**
     * Set estado
     *
     * @param \Faltas\FaltasBundle\Entity\EstadoActa $estado
     *
     * @return Acta
     */
    public function setEstado(\Faltas\FaltasBundle\Entity\EstadoActa $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Faltas\FaltasBundle\Entity\EstadoActa
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return Acta
     */
    public function setOrdenInspeccion(\Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion = null)
    {
        $this->ordenInspeccion = $ordenInspeccion;

        return $this;
    }

    /**
     * Get ordenInspeccion
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion
     */
    public function getOrdenInspeccion()
    {
        return $this->ordenInspeccion;
    }

    public function __toString()
    {
        return $this->id;
    }
}
