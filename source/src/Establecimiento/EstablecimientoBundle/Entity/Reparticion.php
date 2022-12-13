<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reparticion
 *
 * @ORM\Table(name="reparticion")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\ReparticionRepository")
 * @UniqueEntity(
 *     fields={"reparticion"}
 *)
 */
class Reparticion
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
     * @ORM\Column(name="reparticion", type="string", length=50, unique=true)
     */
    private $reparticion;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="prioridad", type="integer")
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
     * Set reparticion
     *
     * @param string $reparticion
     *
     * @return Reparticion
     */
    public function setReparticion($reparticion)
    {
        $this->reparticion = $reparticion;

        return $this;
    }

    /**
     * Get reparticion
     *
     * @return string
     */
    public function getReparticion()
    {
        return $this->reparticion;
    }

    /**
     * Set prioridad
     *
     * @param integer $prioridad
     *
     * @return Reparticion
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return integer
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    public function __toString()
    {
        return $this->getReparticion();
    }
}
