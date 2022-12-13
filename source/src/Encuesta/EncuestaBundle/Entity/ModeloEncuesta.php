<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ModeloEncuesta
 *
 * @ORM\Table(name="encuesta_modelo_encuesta")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\ModeloEncuestaRepository")
 */
class ModeloEncuesta
{

    /**     
     * @ORM\ManyToMany(targetEntity="GrupoPreguntas")
     * @ORM\JoinTable(name="encuesta_modeloencuesta_grupopreguntas",
     *      joinColumns={@ORM\JoinColumn(name="modeloencuesta_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="grupopreguntas_id", referencedColumnName="id")}
     *      )
     */
    protected $grupoPreguntas;

    /**     
     * @ORM\ManyToMany(targetEntity="Pregunta")
     * @ORM\JoinTable(name="encuesta_modeloencuesta_pregunta_deshabilitadas",
     *      joinColumns={@ORM\JoinColumn(name="modeloencuesta_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="pregunta_id", referencedColumnName="id")}
     *      )
     */
    protected $preguntasDesHabilitadas;

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
     * @ORM\Column(name="nombreEncuesta", type="string", length=255, unique=true)
     */
    private $nombreEncuesta;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->grupoPreguntas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->preguntasDesHabilitadas = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set nombreEncuesta
     *
     * @param string $nombreEncuesta
     *
     * @return ModeloEncuesta
     */
    public function setNombreEncuesta($nombreEncuesta)
    {
        $this->nombreEncuesta = $nombreEncuesta;

        return $this;
    }

    /**
     * Get nombreEncuesta
     *
     * @return string
     */
    public function getNombreEncuesta()
    {
        return $this->nombreEncuesta;
    }

    /**
     * Add grupoPregunta
     *
     * @param \Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupoPregunta
     *
     * @return ModeloEncuesta
     */
    public function addGrupoPregunta(\Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupoPregunta)
    {
        $this->grupoPreguntas[] = $grupoPregunta;

        return $this;
    }

    public function setGrupoPregunta($gruposPregunta)
    {
        $this->grupoPreguntas=$gruposPregunta;
    }

    /**
     * Remove grupoPregunta
     *
     * @param \Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupoPregunta
     */
    public function removeGrupoPregunta(\Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupoPregunta)
    {
        $this->grupoPreguntas->removeElement($grupoPregunta);
    }

    /**
     * Get grupoPreguntas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupoPreguntas()
    {
        return $this->grupoPreguntas;
    }

    /**
     * Add preguntasDesHabilitada
     *
     * @param \Encuesta\EncuestaBundle\Entity\Pregunta $preguntasDesHabilitada
     *
     * @return ModeloEncuesta
     */
    public function addPreguntasDesHabilitada(\Encuesta\EncuestaBundle\Entity\Pregunta $preguntasDesHabilitada)
    {
        $this->preguntasDesHabilitadas[] = $preguntasDesHabilitada;

        return $this;
    }

    public function setPreguntasDesHabilitada($preguntasDeshabilitadas)
    {
        $this->preguntasDesHabilitadas=$preguntasDeshabilitadas;
    }

    /**
     * Remove preguntasDesHabilitada
     *
     * @param \Encuesta\EncuestaBundle\Entity\Pregunta $preguntasDesHabilitada
     */
    public function removePreguntasDesHabilitada(\Encuesta\EncuestaBundle\Entity\Pregunta $preguntasDesHabilitada)
    {
        $this->preguntasDesHabilitadas->removeElement($preguntasDesHabilitada);
    }

    /**
     * Get preguntasDesHabilitadas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreguntasDesHabilitadas()
    {
        return $this->preguntasDesHabilitadas;
    }
}
