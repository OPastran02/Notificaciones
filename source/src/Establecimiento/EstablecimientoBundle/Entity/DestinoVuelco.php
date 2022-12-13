<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * destinoVuelco
 *
 * @ORM\Table(name="destino_vuelco")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\DestinoVuelcoRepository")
 * @UniqueEntity("destinoVuelco")
 */
class DestinoVuelco
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
     * @ORM\Column(name="destinoVuelco", type="string", length=50, unique=true)
     */
    private $destinoVuelco;


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
     * Set destinoVuelco
     *
     * @param string $destinoVuelco
     *
     * @return destinoVuelco
     */
    public function setDestinoVuelco($destinoVuelco)
    {
        $this->destinoVuelco = $destinoVuelco;

        return $this;
    }

    /**
     * Get destinoVuelco
     *
     * @return string
     */
    public function getDestinoVuelco()
    {
        return $this->destinoVuelco;
    }
    
    public function __toString()
    {
        return $this->getDestinoVuelco();
    }
}
