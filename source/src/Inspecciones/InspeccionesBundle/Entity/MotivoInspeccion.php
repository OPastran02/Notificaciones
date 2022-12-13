<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotivoInspeccion
 *
 * @ORM\Table(name="motivo_inspeccion")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\MotivoInspeccionRepository")
 */
class MotivoInspeccion
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
     * @ORM\Column(name="motivo", type="string", length=60, unique=true)
     */
    private $motivo;


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
     * @return MotivoInspeccion
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

    public function __toString()
    {
        return $this->motivo;
    }
}

