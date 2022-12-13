<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDispo
 *
 * @ORM\Table(name="tipo_dispo")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\TipoDispoRepository")
 * @UniqueEntity("tipoDispo")
 */
class TipoDispo
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
     * @ORM\Column(name="tipo_dispo", type="string", length=50, unique=true)
     */
    private $tipoDispo;


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
     * Set tipoDispo
     *
     * @param string $tipoDispo
     *
     * @return TipoDispo
     */
    public function setTipoDispo($tipoDispo)
    {
        $this->tipoDispo = $tipoDispo;

        return $this;
    }

    /**
     * Get tipoDispo
     *
     * @return string
     */
    public function getTipoDispo()
    {
        return $this->tipoDispo;
    }

    public function __toString()
    {
        return $this->getTipoDispo();
    }
}
