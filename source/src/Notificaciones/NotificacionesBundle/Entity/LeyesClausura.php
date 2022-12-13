<?php

namespace Notificaciones\NotificacionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * LeyesClausura
 *
 * @ORM\Table(name="leyes_clausura")
 * @ORM\Entity(repositoryClass="Notificaciones\NotificacionesBundle\Repository\LeyesClausuraRepository")
 * @UniqueEntity("ley")
 */
class LeyesClausura
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
     * @ORM\Column(name="Ley", type="string", length=100, unique=true)
     */
    private $ley;


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
     * Set ley
     *
     * @param string $ley
     *
     * @return LeyesClausura
     */
    public function setLey($ley)
    {
        $this->ley = $ley;

        return $this;
    }

    /**
     * Get ley
     *
     * @return string
     */
    public function getLey()
    {
        return $this->ley;
    }

    public function __toString(){
        return $this->getLey();   
    }
}

