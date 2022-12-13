<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoRes326
 *
 * @ORM\Table(name="estado_res326")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\EstadoRes326Repository")
 * @UniqueEntity(
 *     fields={"estadoRes326"}
 *)
 */
class EstadoRes326
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
     * @Assert\NotBlank()
     * @ORM\Column(name="estadoRes326", type="string", length=50, unique=true)
     */
    private $estadoRes326;


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
     * Set estadoRes326
     *
     * @param string $estadoRes326
     *
     * @return EstadoRes326
     */
    public function setEstadoRes326($estadoRes326)
    {
        $this->estadoRes326 = $estadoRes326;

        return $this;
    }

    /**
     * Get estadoRes326
     *
     * @return string
     */
    public function getEstadoRes326()
    {
        return $this->estadoRes326;
    }

    public function __toString()
    {
        return $this->getEstadoRes326();
    }
}
