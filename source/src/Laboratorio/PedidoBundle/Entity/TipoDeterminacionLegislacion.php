<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDeterminacionLegislacion
 *
 * @ORM\Table(name="laboratorio_tipo_determinacion_legislacion")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\TipoDeterminacionLegislacionRepository")
 */
class TipoDeterminacionLegislacion
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
     * @ORM\Column(name="tipo", type="string", length=255, unique=true)
     */
    private $tipo;


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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return TipoDeterminacionLegislacion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}

