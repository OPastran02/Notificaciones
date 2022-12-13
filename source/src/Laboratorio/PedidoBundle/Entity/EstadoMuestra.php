<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoMuestra
 *
 * @ORM\Table(name="laboratoio_estado_muestra")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\EstadoMuestraRepository")
 */
class EstadoMuestra
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
     * @ORM\Column(name="estado", type="string", length=255, unique=true)
     */
    private $estado;


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
     * Set estado
     *
     * @param string $estado
     *
     * @return EstadoMuestra
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function __toString()
    {
        return $this->estado;
    }
}

