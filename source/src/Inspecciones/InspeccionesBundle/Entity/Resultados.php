<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resultados
 *
 * @ORM\Table(name="inspecciones_resultados")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\ResultadosRepository")
 */
class Resultados
{

    /**
     * @ORM\ManyToOne(targetEntity="OrdenInspeccion", inversedBy="resultados")     
     */
    protected $ordenInspeccion;

    /**     
     * @ORM\ManyToMany(targetEntity="\Encuesta\EncuestaBundle\Entity\Respuestas")
     * @ORM\JoinTable(name="inspecciones_respuestas",
     *      joinColumns={@ORM\JoinColumn(name="resultado_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="respuesta_id", referencedColumnName="id")}
     *      )
     */
    protected $respuestas;

    /**
     * @ORM\OneToMany(targetEntity="ResultadosFotos", mappedBy="resultados",cascade={"persist"})
     */
    protected $fotos;

    /**
     * @ORM\ManyToOne(targetEntity="\Encuesta\EncuestaBundle\Entity\Pregunta")
     */
    protected $pregunta;

    /**
     * @ORM\ManyToOne(targetEntity="\Encuesta\EncuestaBundle\Entity\GrupoPreguntas")
     */
    protected $grupo;

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
     * @ORM\Column(name="RespuestaLibre", type="string", length=1000, nullable=true)
     */
    private $respuestaLibre;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden;


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
     * Set respuestaLibre
     *
     * @param string $respuestaLibre
     *
     * @return Resultados
     */
    public function setRespuestaLibre($respuestaLibre)
    {
        $this->respuestaLibre = $respuestaLibre;

        return $this;
    }

    /**
     * Get respuestaLibre
     *
     * @return string
     */
    public function getRespuestaLibre()
    {
        return $this->respuestaLibre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->respuestas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set ordenInspeccion
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion
     *
     * @return Resultados
     */
    public function setOrdenInspeccion(\Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion $ordenInspeccion = null)
    {
        $this->ordenInspeccion = $ordenInspeccion;

        return $this;
    }

    /**
     * Get ordenInspeccion
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion
     */
    public function getOrdenInspeccion()
    {
        return $this->ordenInspeccion;
    }

    public function setRespuesta($respuesta)
    {
        $this->respuestas = $respuesta;

        return $this;
    }

    /**
     * Add respuesta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Respuestas $respuesta
     *
     * @return Resultados
     */
    public function addRespuesta(\Encuesta\EncuestaBundle\Entity\Respuestas $respuesta)
    {
        $this->respuestas[] = $respuesta;

        return $this;
    }

    /**
     * Remove respuesta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Respuestas $respuesta
     */
    public function removeRespuesta(\Encuesta\EncuestaBundle\Entity\Respuestas $respuesta)
    {
        $this->respuestas->removeElement($respuesta);
    }

    /**
     * Get respuestas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRespuestas()
    {
        return $this->respuestas;
    }

    public function setRespuestas($respuestas)
    {
        $this->respuestas = $respuestas;
    }

    /**
     * Set pregunta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Pregunta $pregunta
     *
     * @return Resultados
     */
    public function setPregunta(\Encuesta\EncuestaBundle\Entity\Pregunta $pregunta = null)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return \Encuesta\EncuestaBundle\Entity\Pregunta
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }

    /**
     * Set grupo
     *
     * @param \Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupo
     *
     * @return Resultados
     */
    public function setGrupo(\Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return \Encuesta\EncuestaBundle\Entity\GrupoPreguntas
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     *
     * @return Resultados
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Add foto
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\ResultadosFotos $foto
     *
     * @return Resultados
     */
    public function addFoto(\Inspecciones\InspeccionesBundle\Entity\ResultadosFotos $foto)
    {
        $this->fotos[] = $foto;

        return $this;
    }

    /**
     * Remove foto
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\ResultadosFotos $foto
     */
    public function removeFoto(\Inspecciones\InspeccionesBundle\Entity\ResultadosFotos $foto)
    {
        $this->fotos->removeElement($foto);
    }

    /**
     * Get fotos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFotos()
    {
        return $this->fotos;
    }
}
