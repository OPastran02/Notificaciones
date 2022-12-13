<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Legislacion
 *
 * @ORM\Table(name="laboratorio_legislacion")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\LegislacionRepository")
 */
class Legislacion
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
     * @ORM\Column(name="legislacion", type="string", length=255, unique=true)
     */
    private $legislacion;

    /**     
     * @ORM\ManyToMany(targetEntity="Programa", mappedBy="legislaciones")
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
     * Set legislacion
     *
     * @param string $legislacion
     *
     * @return Legislacion
     */
    public function setLegislacion($legislacion)
    {
        $this->legislacion = $legislacion;

        return $this;
    }

    /**
     * Get legislacion
     *
     * @return string
     */
    public function getLegislacion()
    {
        return $this->legislacion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->programas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add programa
     *
     * @param \Laboratorio\PedidoBundle\Entity\Programa $programa
     *
     * @return Legislacion
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

    public function __toString()
    {
        return $this->legislacion;
    }
}
