<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotivosReInspeccion
 *
 * @ORM\Table(name="motivos_re_inspeccion")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\MotivosReInspeccionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MotivosReInspeccion
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
     * @ORM\Column(name="motivo", type="string", length=2500)
     */
    private $motivo;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenInspeccion")
     * @ORM\JoinColumn(name="orden_inspeccion_id", referencedColumnName="id")
     */
    protected $ordenInspeccion;

    /**
     * @var bool
     *
     * @ORM\Column(name="gofa", type="boolean")
     */
    private $gofa;

    /**
     * @var \DateTime
     *          
     * @ORM\Column(name="Fecha_Creado", type="datetime")
     */
    private $fechaCreado;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     * @ORM\JoinColumn(name="Id_Usuario_Creador", referencedColumnName="id")               
     */
    private $idUsuarioCreador;

    /**
     * @var bool
     *
     * @ORM\Column(name="desestimar_reinspeccion", type="boolean")
     */
    private $desestimarReinspeccion;

    /**
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->setIdUsuarioCreador();        
    }

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
     * Set motivo
     *
     * @param string $motivo
     *
     * @return MotivosReInspeccion
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set gofa
     *
     * @param boolean $gofa
     *
     * @return MotivosReInspeccion
     */
    public function setGofa($gofa)
    {
        $this->gofa = $gofa;

        return $this;
    }

    /**
     * Get gofa
     *
     * @return boolean
     */
    public function getGofa()
    {
        return $this->gofa;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return MotivosReInspeccion
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
     * Set desestimarReinspeccion
     *
     * @param boolean $desestimarReinspeccion
     *
     * @return MotivosReInspeccion
     */
    public function setDesestimarReinspeccion($desestimarReinspeccion)
    {
        $this->desestimarReinspeccion = $desestimarReinspeccion;

        return $this;
    }

    /**
     * Get desestimarReinspeccion
     *
     * @return boolean
     */
    public function getDesestimarReinspeccion()
    {
        return $this->desestimarReinspeccion;
    }

    /**
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return MotivosReInspeccion
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

    /**
     * Set idUsuarioCreador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $idUsuarioCreador
     *
     * @return MotivosReInspeccion
     */
    public function setIdUsuarioCreador(\Usuario\UsuarioBundle\Entity\Usuarios $idUsuarioCreador = null)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

        return $this;
    }

    /**
     * Get idUsuarioCreador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getIdUsuarioCreador()
    {
        return $this->idUsuarioCreador;
    }
}
