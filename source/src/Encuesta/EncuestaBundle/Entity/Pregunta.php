<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pregunta
 *
 * @ORM\Table(name="encuesta_pregunta")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\PreguntaRepository")
 */
class Pregunta
{ 

    /**               
     * @ORM\ManyToOne(targetEntity="TipoPregunta")     
     */
    protected $tipo;

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
     * @ORM\Column(name="pregunta", type="string", length=255)
     */
    private $pregunta;

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
     * Set Id
     *
     * @param integer $id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set pregunta
     *
     * @param string $pregunta
     *
     * @return Pregunta
     */
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return string
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }

    /**
     * Set tipo
     *
     * @param \Encuesta\EncuestaBundle\Entity\TipoPregunta $tipo
     *
     * @return Pregunta
     */
    public function setTipo(\Encuesta\EncuestaBundle\Entity\TipoPregunta $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Encuesta\EncuestaBundle\Entity\TipoPregunta
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    public function __toString()
    {
        return $this->pregunta;
    }
}
