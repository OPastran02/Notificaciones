<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GrupoPreguntas
 *
 * @ORM\Table(name="encuesta_grupo_preguntas")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\GrupoPreguntasRepository")
 */
class GrupoPreguntas
{
    /**     
     * @ORM\OneToMany(targetEntity="RequisitosPreguntaGrupo", mappedBy="grupo")
     */
    protected $requisitos;

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
     * @ORM\Column(name="nombreGrupo", type="string", length=255, unique=true)
     */
    private $nombreGrupo;    


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requisitos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombreGrupo
     *
     * @param string $nombreGrupo
     *
     * @return GrupoPreguntas
     */
    public function setNombreGrupo($nombreGrupo)
    {
        $this->nombreGrupo = $nombreGrupo;

        return $this;
    }

    /**
     * Get nombreGrupo
     *
     * @return string
     */
    public function getNombreGrupo()
    {
        return $this->nombreGrupo;
    }

    /**
     * Add requisito
     *
     * @param \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito
     *
     * @return GrupoPreguntas
     */
    public function addRequisito(\Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito)
    {
        $this->requisitos[] = $requisito;

        return $this;
    }

    /**
     * Remove requisito
     *
     * @param \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito
     */
    public function removeRequisito(\Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito)
    {
        $this->requisitos->removeElement($requisito);
    }

    /**
     * Get requisitos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRequisitos()
    {
        return $this->requisitos;
    }

    public function __toString()
    {
        return $this->nombreGrupo;
    }
}
