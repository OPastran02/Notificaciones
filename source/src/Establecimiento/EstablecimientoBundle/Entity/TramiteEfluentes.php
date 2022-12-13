<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * TramiteEfluentes
 *
 * @ORM\Table(name="tramite_efluentes")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\TramiteEfluentesRepository")
 * @UniqueEntity("tramiteEfluentes")
 */
class TramiteEfluentes
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
     * @ORM\Column(name="tramiteEfluentes", type="string", length=50, unique=true)
     */
    private $tramiteEfluentes;


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
     * Set tramiteEfluentes
     *
     * @param string $tramiteEfluentes
     *
     * @return tramiteEfluentes
     */
    public function setTramiteEfluentes($tramiteEfluentes)
    {
        $this->tramiteEfluentes = $tramiteEfluentes;

        return $this;
    }

    /**
     * Get tramiteEfluentes
     *
     * @return string
     */
    public function getTramiteEfluentes()
    {
        return $this->tramiteEfluentes;
    }

    public function __toString()
    {
        return $this->getTramiteEfluentes();
    }
}
