<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MuestraEstados
 *
 * @ORM\Table(name="laboratorio_muestra_estados")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\MuestraEstadosRepository")
 */
class MuestraEstados
{
    /**
     * @ORM\Id     
     * @ORM\ManyToOne(targetEntity="Muestra", inversedBy="estados")     
     */
    protected $muestra;

    /**
     * @ORM\Id          
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Area")
     */
    protected $area;

    /**          
     * @ORM\ManyToOne(targetEntity="EstadoMuestra")
     */
    protected $estado;

    /**
     * @var text
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observacion;

    /**
     * @var text
     *
     * @ORM\Column(name="conclusion", type="text", nullable=true)
     */
    private $conclusion;

    /**
     * Set muestra
     *
     * @param \Laboratorio\PedidoBundle\Entity\Muestra $muestra
     *
     * @return MuestraEstados
     */
    public function setMuestra(\Laboratorio\PedidoBundle\Entity\Muestra $muestra)
    {
        $this->muestra = $muestra;

        return $this;
    }

    /**
     * Get muestra
     *
     * @return \Laboratorio\PedidoBundle\Entity\Muestra
     */
    public function getMuestra()
    {
        return $this->muestra;
    }

    /**
     * Set area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return MuestraEstados
     */
    public function setArea(\Usuario\UsuarioBundle\Entity\Area $area)
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
     * Set estado
     *
     * @param \Laboratorio\PedidoBundle\Entity\EstadoMuestra $estado
     *
     * @return MuestraEstados
     */
    public function setEstado(\Laboratorio\PedidoBundle\Entity\EstadoMuestra $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Laboratorio\PedidoBundle\Entity\EstadoMuestra
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function __toString()
    {
        return $this->estado->getEstado();
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return MuestraEstados
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set conclusion
     *
     * @param string $conclusion
     *
     * @return MuestraEstados
     */
    public function setConclusion($conclusion)
    {
        $this->conclusion = $conclusion;

        return $this;
    }

    /**
     * Get conclusion
     *
     * @return string
     */
    public function getConclusion()
    {
        return $this->conclusion;
    }

    public function observacionesToArray()
    {
        return empty($this->observacion) ? null : [$this->area->getArea() => explode('.', $this->observacion)];
    }
}
