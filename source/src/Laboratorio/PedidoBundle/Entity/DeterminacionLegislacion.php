<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeterminacionLegislacion
 *
 * @ORM\Table(name="laboratorio_determinacion_legislacion")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\DeterminacionLegislacionRepository")
 */
class DeterminacionLegislacion
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
     * @ORM\ManyToOne(targetEntity="Determinacion", inversedBy="legislaciones")     
     */
    protected $determinacion;

    /**    
     * @ORM\ManyToOne(targetEntity="Legislacion")
     */
    protected $legislacion;

    /**
     * @ORM\ManyToOne(targetEntity="TipoDeterminacionLegislacion")
     */
    protected $tipo;

    /**
     * @var float
     *
     * @ORM\Column(name="min", type="float", nullable=true)
     */
    private $min;

    /**
     * @var bool
     *
     * @ORM\Column(name="minIgual", type="boolean", nullable=true)
     */
    private $minIgual;

    /**
     * @var float
     *
     * @ORM\Column(name="max", type="float", nullable=true)
     */
    private $max;

    /**
     * @var bool
     *
     * @ORM\Column(name="maxIgual", type="boolean", nullable=true)
     */
    private $maxIgual;

    /**
     * @var string
     *
     * @ORM\Column(name="MostrarComo", type="string", length=255, nullable=true)
     */
    private $mostrarComo;    

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
     * Set min
     *
     * @param float $min
     *
     * @return DeterminacionLegislacion
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set minIgual
     *
     * @param boolean $minIgual
     *
     * @return DeterminacionLegislacion
     */
    public function setMinIgual($minIgual)
    {
        $this->minIgual = $minIgual;

        return $this;
    }

    /**
     * Get minIgual
     *
     * @return bool
     */
    public function getMinIgual()
    {
        return $this->minIgual;
    }

    /**
     * Set max
     *
     * @param float $max
     *
     * @return DeterminacionLegislacion
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set maxIgual
     *
     * @param boolean $maxIgual
     *
     * @return DeterminacionLegislacion
     */
    public function setMaxIgual($maxIgual)
    {
        $this->maxIgual = $maxIgual;

        return $this;
    }

    /**
     * Get maxIgual
     *
     * @return bool
     */
    public function getMaxIgual()
    {
        return $this->maxIgual;
    }

    /**
     * Set mostrarComo
     *
     * @param string $mostrarComo
     *
     * @return DeterminacionLegislacion
     */
    public function setMostrarComo($mostrarComo)
    {
        $this->mostrarComo = $mostrarComo;

        return $this;
    }

    /**
     * Get mostrarComo
     *
     * @return string
     */
    public function getMostrarComo()
    {
        return $this->mostrarComo;
    }

    /**
     * Set determinacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Determinacion $determinacion
     *
     * @return DeterminacionLegislacion
     */
    public function setDeterminacion(\Laboratorio\PedidoBundle\Entity\Determinacion $determinacion)
    {
        $this->determinacion = $determinacion;

        return $this;
    }

    /**
     * Get determinacion
     *
     * @return \Laboratorio\PedidoBundle\Entity\Determinacion
     */
    public function getDeterminacion()
    {
        return $this->determinacion;
    }

    /**
     * Set legislacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacion
     *
     * @return DeterminacionLegislacion
     */
    public function setLegislacion(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacion)
    {
        $this->legislacion = $legislacion;

        return $this;
    }

    /**
     * Get legislacion
     *
     * @return \Laboratorio\PedidoBundle\Entity\Legislacion
     */
    public function getLegislacion()
    {
        return $this->legislacion;
    }

    /**
     * Set tipo
     *
     * @param \Laboratorio\PedidoBundle\Entity\TipoDeterminacionLegislacion $tipo
     *
     * @return DeterminacionLegislacion
     */
    public function setTipo(\Laboratorio\PedidoBundle\Entity\TipoDeterminacionLegislacion $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Laboratorio\PedidoBundle\Entity\TipoDeterminacionLegislacion
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
