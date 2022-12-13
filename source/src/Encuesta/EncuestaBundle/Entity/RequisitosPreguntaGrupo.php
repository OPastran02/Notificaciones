<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RequisitosPreguntaGrupo
 *
 * @ORM\Table(name="encuesta_requisitos_pregunta_grupo", uniqueConstraints={@ORM\UniqueConstraint(name="grupo_pregunta_unique", columns={"grupo_id", "pregunta_id"})})
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\RequisitosPreguntaGrupoRepository")
 */
class RequisitosPreguntaGrupo
{    

    /**     
     * @ORM\ManyToMany(targetEntity="Respuestas")
     * @ORM\JoinTable(name="encuesta_requisitos_respuestas",
     *      joinColumns={@ORM\JoinColumn(name="requisito_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="respuesta_id", referencedColumnName="id")}
     *      )
     */
    protected $respuestasPreDefinidas;    

    /**      
     * @ORM\ManyToOne(targetEntity="GrupoPreguntas", inversedBy="requisitos")
     * @ORM\JoinColumn(name="grupo_id", referencedColumnName="id")
     */
    protected $grupo;

    /**          
     * @ORM\ManyToOne(targetEntity="Pregunta")
     * @ORM\JoinColumn(name="pregunta_id", referencedColumnName="id")
     */
    protected $pregunta;

    /**      
     * @ORM\OneToMany(targetEntity="SiguientePregunta", mappedBy="preguntaOrigen")     
     */
    protected $siguientePregunta;

    /**      
     * @ORM\OneToMany(targetEntity="Falta", mappedBy="requisito", cascade={"persist"})     
     */
    protected $faltas;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="MostrarInicio", type="boolean")
     */
    private $mostrarInicio;

    /**
     * @var bool
     *
     * @ORM\Column(name="Obligatorio", type="boolean")
     */
    private $obligatorio;

    /**
     * @var bool
     *
     * @ORM\Column(name="RequiereFoto", type="boolean")
     */
    private $requiereFoto;

    /**
     * @var string
     *
     * @ORM\Column(name="validacion", type="string", length=255)
     */
    private $validacion;

    /**
     * @var int
     *          
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->respuestasPreDefinidas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->siguientePregunta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->faltas = new \Doctrine\Common\Collections\ArrayCollection();
    }

   /**
     * Get Orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
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
     * Get id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set mostrarInicio
     *
     * @param boolean $mostrarInicio
     *
     * @return RequisitosPreguntaGrupo
     */
    public function setMostrarInicio($mostrarInicio)
    {
        $this->mostrarInicio = $mostrarInicio;

        return $this;
    }

    /**
     * Get mostrarInicio
     *
     * @return boolean
     */
    public function getMostrarInicio()
    {
        return $this->mostrarInicio;
    }

    /**
     * Set obligatorio
     *
     * @param boolean $obligatorio
     *
     * @return RequisitosPreguntaGrupo
     */
    public function setObligatorio($obligatorio)
    {
        $this->obligatorio = $obligatorio;

        return $this;
    }

    /**
     * Get obligatorio
     *
     * @return boolean
     */
    public function getObligatorio()
    {
        return $this->obligatorio;
    }

    /**
     * Set requiereFoto
     *
     * @param boolean $requiereFoto
     *
     * @return RequisitosPreguntaGrupo
     */
    public function setRequiereFoto($requiereFoto)
    {
        $this->requiereFoto = $requiereFoto;

        return $this;
    }

    /**
     * Get requiereFoto
     *
     * @return boolean
     */
    public function getRequiereFoto()
    {
        return $this->requiereFoto;
    }

    /**
     * Set validacion
     *
     * @param string $validacion
     *
     * @return RequisitosPreguntaGrupo
     */
    public function setValidacion($validacion)
    {
        $this->validacion = $validacion;

        return $this;
    }

    /**
     * Get validacion
     *
     * @return string
     */
    public function getValidacion()
    {
        return $this->validacion;
    }

    /**
     * Add respuestasPreDefinida
     *
     * @param \Encuesta\EncuestaBundle\Entity\Respuestas $respuestasPreDefinida
     *
     * @return RequisitosPreguntaGrupo
     */
    public function addRespuestasPreDefinida(\Encuesta\EncuestaBundle\Entity\Respuestas $respuestasPreDefinida)
    {
        $this->respuestasPreDefinidas[] = $respuestasPreDefinida;

        return $this;
    }

    /**
     * Remove respuestasPreDefinida
     *
     * @param \Encuesta\EncuestaBundle\Entity\Respuestas $respuestasPreDefinida
     */
    public function removeRespuestasPreDefinida(\Encuesta\EncuestaBundle\Entity\Respuestas $respuestasPreDefinida)
    {
        $this->respuestasPreDefinidas->removeElement($respuestasPreDefinida);
    }

    /**
     * Get respuestasPreDefinidas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRespuestasPreDefinidas()
    {
        return $this->respuestasPreDefinidas;
    }

    /**
     * Set grupo
     *
     * @param \Encuesta\EncuestaBundle\Entity\GrupoPreguntas $grupo
     *
     * @return RequisitosPreguntaGrupo
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
     * Set pregunta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Pregunta $pregunta
     *
     * @return RequisitosPreguntaGrupo
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
     * Add siguientePreguntum
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntum
     *
     * @return RequisitosPreguntaGrupo
     */
    public function addSiguientePreguntum(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntum)
    {
        $this->siguientePregunta[] = $siguientePreguntum;

        return $this;
    }

    /**
     * Remove siguientePreguntum
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntum
     */
    public function removeSiguientePreguntum(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntum)
    {
        $this->siguientePregunta->removeElement($siguientePreguntum);
    }

    /**
     * Get siguientePregunta
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiguientePregunta()
    {
        return $this->siguientePregunta;
    }

    /**
     * Add falta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $falta
     *
     * @return RequisitosPreguntaGrupo
     */
    public function addFalta(\Encuesta\EncuestaBundle\Entity\Falta $falta)
    {
        $this->faltas[] = $falta;

        return $this;
    }

    /**
     * Remove falta
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $falta
     */
    public function removeFalta(\Encuesta\EncuestaBundle\Entity\Falta $falta)
    {
        $this->faltas->removeElement($falta);
    }

    /**
     * Get faltas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaltas()
    {
        return $this->faltas;
    }


    /**
     * Get Vistas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVistaPreguntaTablet()
    {
        $iteracionFalta=0;
        $iteracionSiguientePregunta=0;
        $contadorSiguientePregunta=0;
        $grupoPreguntas["id_pregunta"]=$this->getPregunta()->getId();
        $grupoPreguntas["Pregunta"]=$this->getPregunta()->getPregunta();
        $grupoPreguntas["respuestas_posibles"]=$this->getRespuestasPreDefinidas();
        foreach ($this->getFaltas() as $falta){
            $grupoPreguntas["faltas"][$iteracionFalta]=$falta->getFaltaVistaTablet();
            unset($grupoPreguntas["faltas"][$iteracionFalta]["falta_id1"]);
            unset($grupoPreguntas["faltas"][$iteracionFalta]["falta_id2"]);
            unset($grupoPreguntas["faltas"][$iteracionFalta]["requisito"]);
            $iteracionFalta++;
        }
        foreach($this->getSiguientePregunta() as $siguientePregunta){
            $grupoPreguntas["siguiente_pregunta"][$iteracionSiguientePregunta]=$siguientePregunta->getVistaSiguientePreguntaTablet();
            $iteracionSiguientePregunta++; 
        }
        $grupoPreguntas["id_tipo_pregunta"]=$this->getPregunta()->getTipo()->getId();
        $grupoPreguntas["tipo_pregunta"]=$this->getPregunta()->getTipo()->getTipoPregunta();
        $grupoPreguntas["mostrar_inicio"]=$this->getMostrarInicio();
        $grupoPreguntas["obligatorio"]=$this->getObligatorio();
        $grupoPreguntas["requiere_foto"]=$this->getRequiereFoto();
        $grupoPreguntas["validacion"]=$this->getValidacion();
        $grupoPreguntas["orden"]=$this->getOrden(); 
         //DUMMY RESPUESTAS
        $grupoPreguntas["respuesta_predefinida"]=array();

        if($grupoPreguntas["id_tipo_pregunta"]== 1 && $grupoPreguntas["id_pregunta"]== 6){
            $grupoPreguntas["respuesta_predefinida"][0]=1234; 
        }else if($grupoPreguntas["id_tipo_pregunta"]== 2 && $grupoPreguntas["id_pregunta"]== 5){
           $grupoPreguntas["respuesta_predefinida"][0]="Texto de prueba";
        }else if($grupoPreguntas["id_tipo_pregunta"]== 3 && $grupoPreguntas["id_pregunta"]== 1){
           $grupoPreguntas["respuesta_predefinida"][0]=1;
        }else if($grupoPreguntas["id_tipo_pregunta"]== 6 && $grupoPreguntas["id_pregunta"]== 8){
           $grupoPreguntas["respuesta_predefinida"][0]=50;
        }else if($grupoPreguntas["id_tipo_pregunta"]== 8 && $grupoPreguntas["id_pregunta"]== 17){
           $grupoPreguntas["respuesta_predefinida"][0]=3;
        }else if($grupoPreguntas["id_tipo_pregunta"]== 10){
           $grupoPreguntas["respuesta_predefinida"]=array();
        }else if($grupoPreguntas["id_tipo_pregunta"]== 11){
           $grupoPreguntas["respuesta_predefinida"]=array();
        }else if($grupoPreguntas["id_tipo_pregunta"]== 12){
           $grupoPreguntas["respuesta_predefinida"]=array();
        }else{
            $grupoPreguntas["respuesta_predefinida"]=array();
        }
        //fom
        return $grupoPreguntas;
    }
}
