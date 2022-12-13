<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CorrecionRuidos
 *
 * @ORM\Table(name="correcionRuidos")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\CorrecionRuidosRepository") 
 */
class CorrecionRuidos
{
    /**
     * @var decimal
     *
     * @ORM\Column(name="variacion", type="decimal")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $variacion;

    /**
     * @var decimal
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="delta", type="decimal")
     */
    private $delta;


    /**
     * Get id
     *
     * @return decimal
     */
    public function getVariacion()
    {
        return $this->variacion;
    }

    /**
     * Set delta
     *
     * @param decimal $delta
     *
     * @return Circuito
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta
     *
     * @return decimal
     */
    public function getDelta()
    {
        return $this->delta;
    }

    public function __toString()
    {
        return $this->delta;
    }

    /**
     * Set variacion
     *
     * @param string $variacion
     *
     * @return CorrecionRuidos
     */
    public function setVariacion($variacion)
    {
        $this->variacion = $variacion;
    
        return $this;
    }
}
