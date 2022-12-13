<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Circuito
 *
 * @ORM\Table(name="circuito")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\CircuitoRepository")
 * @UniqueEntity("circuito")
 */
class Circuito
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
     * @ORM\Column(name="circuito", type="string", length=50, unique=true)
     */
    private $circuito;


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
     * Set circuito
     *
     * @param string $circuito
     *
     * @return Circuito
     */
    public function setCircuito($circuito)
    {
        $this->circuito = $circuito;

        return $this;
    }

    /**
     * Get circuito
     *
     * @return string
     */
    public function getCircuito()
    {
        return $this->circuito;
    }

    public function __toString()
    {
        return $this->circuito;
    }
}
