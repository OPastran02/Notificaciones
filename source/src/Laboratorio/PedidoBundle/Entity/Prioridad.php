<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prioridad
 *
 * @ORM\Table(name="laboratorio_prioridad")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\PrioridadRepository")
 */
class Prioridad
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
     * @ORM\Column(name="prioridad", type="string", length=255, unique=true)
     */
    private $prioridad;


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
     * Set prioridad
     *
     * @param string $prioridad
     *
     * @return prioridad
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return string
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    public function __toString()
    {
        return $this->prioridad;
    }
}

