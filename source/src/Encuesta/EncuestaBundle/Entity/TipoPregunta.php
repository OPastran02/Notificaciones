<?php

namespace Encuesta\EncuestaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPregunta
 *
 * @ORM\Table(name="encuesta_tipo_pregunta")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\TipoPreguntaRepository")
 */
class TipoPregunta
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
     * @ORM\Column(name="tipoPregunta", type="string", length=255, unique=true)
     */
    private $tipoPregunta;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipoPregunta
     *
     * @param string $tipoPregunta
     *
     * @return TipoPregunta
     */
    public function setTipoPregunta($tipoPregunta)
    {
        $this->tipoPregunta = $tipoPregunta;

        return $this;
    }

    /**
     * Get tipoPregunta
     *
     * @return string
     */
    public function getTipoPregunta()
    {
        return $this->tipoPregunta;
    }
}
