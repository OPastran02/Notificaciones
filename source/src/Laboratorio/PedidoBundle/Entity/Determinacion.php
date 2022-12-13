<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Determinacion
 *
 * @ORM\Table(name="laboratorio_determinacion")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\DeterminacionRepository")
 */
class Determinacion
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="unidadMedida", type="string", length=255)
     */
    private $unidadMedida;

    /**
     * @var string
     *
     * @ORM\Column(name="limiteCuantificable", type="string", length=255)
     */
    private $limiteCuantificable;

    /**
     * @var bool
     *
     * @ORM\Column(name="habilitado", type="boolean")
     */
    private $habilitado;

    /**
     * @var string
     *
     * @ORM\Column(name="metodologia", type="string", length=255)
     */
    private $metodologia;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_dato", type="string", length=255)
     */
    private $tipoDato;

    /**          
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Area")
     */
    protected $area;

    /**     
     * @ORM\OneToMany(targetEntity="DeterminacionLegislacion", mappedBy="determinacion")
     */
    protected $legislaciones;

    /**     
     * @ORM\ManyToMany(targetEntity="Programa", mappedBy="determinaciones")
     */
    private $programas;


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
     * @return Determinacion
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
     * Set unidadMedida
     *
     * @param string $unidadMedida
     *
     * @return Determinacion
     */
    public function setUnidadMedida($unidadMedida)
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return string
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }

    /**
     * Set limiteCuantificable
     *
     * @param string $limiteCuantificable
     *
     * @return Determinacion
     */
    public function setLimiteCuantificable($limiteCuantificable)
    {
        $this->limiteCuantificable = $limiteCuantificable;

        return $this;
    }

    /**
     * Get limiteCuantificable
     *
     * @return string
     */
    public function getLimiteCuantificable()
    {
        return $this->limiteCuantificable;
    }

    /**
     * Set habilitado
     *
     * @param boolean $habilitado
     *
     * @return Determinacion
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return bool
     */
    public function getHabilitado()
    {
        return $this->habilitado;
    }

    /**
     * Set metodologia
     *
     * @param string $metodologia
     *
     * @return Determinacion
     */
    public function setMetodologia($metodologia)
    {
        $this->metodologia = $metodologia;

        return $this;
    }

    /**
     * Get metodologia
     *
     * @return string
     */
    public function getMetodologia()
    {
        return $this->metodologia;
    }

    /**
     * Set tipoDato
     *
     * @param string $tipoDato
     *
     * @return Determinacion
     */
    public function setTipoDato($tipoDato)
    {
        $this->tipoDato = $tipoDato;

        return $this;
    }

    /**
     * Get tipoDato
     *
     * @return string
     */
    public function getTipoDato()
    {
        return $this->tipoDato;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->programas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return Determinacion
     */
    public function setArea(\Usuario\UsuarioBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Usuario\UsuarioBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Add programa
     *
     * @param \Laboratorio\PedidoBundle\Entity\Programa $programa
     *
     * @return Determinacion
     */
    public function addPrograma(\Laboratorio\PedidoBundle\Entity\Programa $programa)
    {
        $this->programas[] = $programa;

        return $this;
    }

    /**
     * Remove programa
     *
     * @param \Laboratorio\PedidoBundle\Entity\Programa $programa
     */
    public function removePrograma(\Laboratorio\PedidoBundle\Entity\Programa $programa)
    {
        $this->programas->removeElement($programa);
    }

    /**
     * Get programas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgramas()
    {
        return $this->programas;
    }

    /**
     * Add legislacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\DeterminacionLegislacion $legislacione
     *
     * @return Determinacion
     */
    public function addLegislacione(\Laboratorio\PedidoBundle\Entity\DeterminacionLegislacion $legislacione)
    {
        $this->legislaciones[] = $legislacione;

        return $this;
    }

    /**
     * Remove legislacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\DeterminacionLegislacion $legislacione
     */
    public function removeLegislacione(\Laboratorio\PedidoBundle\Entity\DeterminacionLegislacion $legislacione)
    {
        $this->legislaciones->removeElement($legislacione);
    }

    /**
     * Get legislaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLegislaciones()
    {
        return $this->legislaciones;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
