<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SiguientePregunta
 *
 * @ORM\Table(name="encuesta_siguiente_pregunta")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\SiguientePreguntaRepository")
 */
class SiguientePregunta
{   
    /**     
     * @ORM\ManyToOne(targetEntity="RequisitosPreguntaGrupo", inversedBy="siguientePregunta")
     * @ORM\JoinColumn(name="requisitoorigen_id", referencedColumnName="id")
     */
    protected $preguntaOrigen;

    /**     
     * @ORM\ManyToOne(targetEntity="RequisitosPreguntaGrupo")
     * @ORM\JoinColumn(name="requisitosiguiente_id", referencedColumnName="id")
     */
    protected $preguntaSiguiente;

    /**     
     * @ORM\ManyToMany(targetEntity="SiguientePregunta", mappedBy="siguientePreguntaId2")
     */
    protected $siguientePreguntaId1;

    /**     
     * @ORM\ManyToMany(targetEntity="SiguientePregunta", inversedBy="siguientePreguntaId1")
     * @ORM\JoinTable(name="encuesta_agrupacion_siguiente_pregunta",
     *      joinColumns={@ORM\JoinColumn(name="siguientpregunta_id1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="siguientepregunta_id2", referencedColumnName="id")}
     *      )
     */
    protected $siguientePreguntaId2;

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
     * @ORM\Column(name="validacion", type="string", length=255, nullable=true)
     */
    private $validacion;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->siguientePreguntaId1 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->siguientePreguntaId2 = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set validacion
     *
     * @param string $validacion
     *
     * @return SiguientePregunta
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
     * Set preguntaOrigen
     *
     * @param \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $preguntaOrigen
     *
     * @return SiguientePregunta
     */
    public function setPreguntaOrigen(\Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $preguntaOrigen = null)
    {
        $this->preguntaOrigen = $preguntaOrigen;

        return $this;
    }

    /**
     * Get preguntaOrigen
     *
     * @return \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo
     */
    public function getPreguntaOrigen()
    {
        return $this->preguntaOrigen;
    }

    /**
     * Set preguntaSiguiente
     *
     * @param \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $preguntaSiguiente
     *
     * @return SiguientePregunta
     */
    public function setPreguntaSiguiente(\Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $preguntaSiguiente = null)
    {
        $this->preguntaSiguiente = $preguntaSiguiente;

        return $this;
    }

    /**
     * Get preguntaSiguiente
     *
     * @return \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo
     */
    public function getPreguntaSiguiente()
    {
        return $this->preguntaSiguiente;
    }

    /**
     * Add siguientePreguntaId1
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId1
     *
     * @return SiguientePregunta
     */
    public function addSiguientePreguntaId1(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId1)
    {
        $this->siguientePreguntaId1[] = $siguientePreguntaId1;

        return $this;
    }

    /**
     * Remove siguientePreguntaId1
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId1
     */
    public function removeSiguientePreguntaId1(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId1)
    {
        $this->siguientePreguntaId1->removeElement($siguientePreguntaId1);
    }

    /**
     * Get siguientePreguntaId1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiguientePreguntaId1()
    {
        return $this->siguientePreguntaId1;
    }

    /**
     * Add siguientePreguntaId2
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId2
     *
     * @return SiguientePregunta
     */
    public function addSiguientePreguntaId2(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId2)
    {
        $this->siguientePreguntaId2[] = $siguientePreguntaId2;

        return $this;
    }

    /**
     * Remove siguientePreguntaId2
     *
     * @param \Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId2
     */
    public function removeSiguientePreguntaId2(\Encuesta\EncuestaBundle\Entity\SiguientePregunta $siguientePreguntaId2)
    {
        $this->siguientePreguntaId2->removeElement($siguientePreguntaId2);
    }

    /**
     * Get siguientePreguntaId2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiguientePreguntaId2()
    {
        return $this->siguientePreguntaId2;
    }

    /**
     * Get getVistaSiguientePreguntaTablet
     *
     * @return \Doctrine\Common\Collections\Collection
     */    
    public function getVistaSiguientePreguntaTablet()
    {
        $i=0;
        $iteracionSiguientePregunta=0;
        $grupoPreguntas["id_pregunta"]=$this->getPreguntaSiguiente()->getPregunta()->getId();
        $grupoPreguntas["Pregunta"]=$this->getPreguntaSiguiente()->getPregunta()->getPregunta();
        $grupoPreguntas["respuestas_posibles"]=$this->getPreguntaSiguiente()->getRespuestasPreDefinidas();
        foreach ($this->getPreguntaSiguiente()->getFaltas() as $falta){
            $grupoPreguntas["faltas"][$i]=$falta->getFaltaVistaTablet();
            unset($grupoPreguntas["faltas"][$i]["falta_id1"]);
            unset($grupoPreguntas["faltas"][$i]["falta_id2"]);
            unset($grupoPreguntas["faltas"][$i]["requisito"]);
            $i++;
        }
        foreach($this->getPreguntaSiguiente()->getSiguientePregunta() as $siguientePregunta){
            $grupoPreguntas["siguiente_pregunta"][$iteracionSiguientePregunta]=$siguientePregunta->getVistaSiguientePreguntaTablet();
            $iteracionSiguientePregunta++; 
        }
        //$grupoPreguntas["siguiente_pregunta"]=$this->getPreguntaSiguiente()->getSiguientePregunta();
        $grupoPreguntas["id_tipo_pregunta"]=$this->getPreguntaSiguiente()->getPregunta()->getTipo()->getId();
        $grupoPreguntas["tipo_pregunta"]=$this->getPreguntaSiguiente()->getPregunta()->getTipo()->getTipoPregunta();
        $grupoPreguntas["mostrar_inicio"]=$this->getPreguntaSiguiente()->getMostrarInicio();
        $grupoPreguntas["obligatorio"]=$this->getPreguntaSiguiente()->getObligatorio();
        $grupoPreguntas["requiere_foto"]=$this->getPreguntaSiguiente()->getRequiereFoto();
        $grupoPreguntas["validacion"]=$this->getValidacion();
        $grupoPreguntas["orden"]=$this->getPreguntaSiguiente()->getOrden();
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
