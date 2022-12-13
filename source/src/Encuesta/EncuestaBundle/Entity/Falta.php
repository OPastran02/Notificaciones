<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Falta
 *
 * @ORM\Table(name="encuesta_falta")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\FaltaRepository")
 */
class Falta
{

    /**      
     * @ORM\ManyToOne(targetEntity="RequisitosPreguntaGrupo", inversedBy="faltas")
     * @ORM\JoinColumn(name="grupo_id", referencedColumnName="id")
     */
    protected $requisito;

    /**     
     * @ORM\ManyToMany(targetEntity="Falta", mappedBy="faltaId2")
     */
    protected $faltaId1;

    /**     
     * @ORM\ManyToMany(targetEntity="Falta", inversedBy="faltaId1")
     * @ORM\JoinTable(name="encuesta_agrupacion_faltas",
     *      joinColumns={@ORM\JoinColumn(name="falta_id1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="falta_id2", referencedColumnName="id")}
     *      )
     */
    protected $faltaId2;

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
     * @ORM\Column(name="flata", type="string", length=255)
     */
    private $flata;

    /**
     * @var string
     *
     * @ORM\Column(name="texto_falta", type="string", length=255)
     */
    private $textoFalta;

    /**
     * @var bool
     *     
     * @ORM\Column(name="clausura", type="boolean")
     */
    private $clausura;


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
     * Set flata
     *
     * @param string $flata
     *
     * @return Falta
     */
    public function setFlata($flata)
    {
        $this->flata = $flata;

        return $this;
    }

    /**
     * Get flata
     *
     * @return string
     */
    public function getFlata()
    {
        return $this->flata;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->faltaId1 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->faltaId2 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set requisito
     *
     * @param \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito
     *
     * @return Falta
     */
    public function setRequisito(\Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo $requisito = null)
    {
        $this->requisito = $requisito;

        return $this;
    }

    /**
     * Get requisito
     *
     * @return \Encuesta\EncuestaBundle\Entity\RequisitosPreguntaGrupo
     */
    public function getRequisito()
    {
        return $this->requisito;
    }

    /**
     * Add faltaId1
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $faltaId1
     *
     * @return Falta
     */
    public function addFaltaId1(\Encuesta\EncuestaBundle\Entity\Falta $faltaId1)
    {
        $this->faltaId1[] = $faltaId1;

        return $this;
    }

    /**
     * Remove faltaId1
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $faltaId1
     */
    public function removeFaltaId1(\Encuesta\EncuestaBundle\Entity\Falta $faltaId1)
    {
        $this->faltaId1->removeElement($faltaId1);
    }

    /**
     * Get faltaId1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaltaId1()
    {
        return $this->faltaId1;
    }

    /**
     * Add faltaId2
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $faltaId2
     *
     * @return Falta
     */
    public function addFaltaId2(\Encuesta\EncuestaBundle\Entity\Falta $faltaId2)
    {
        $this->faltaId2[] = $faltaId2;

        return $this;
    }

    /**
     * Remove faltaId2
     *
     * @param \Encuesta\EncuestaBundle\Entity\Falta $faltaId2
     */
    public function removeFaltaId2(\Encuesta\EncuestaBundle\Entity\Falta $faltaId2)
    {
        $this->faltaId2->removeElement($faltaId2);
    }

    /**
     * Get faltaId2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaltaId2()
    {
        return $this->faltaId2;
    }

    /**
     * Set textoFalta
     *
     * @param string $textoFalta
     *
     * @return Falta
     */
    public function setTextoFalta($textoFalta)
    {
        $this->textoFalta = $textoFalta;

        return $this;
    }

    /**
     * Get textoFalta
     *
     * @return string
     */
    public function getTextoFalta()
    {
        return $this->textoFalta;
    }

    /**
     * Set clausura
     *
     * @param boolean $clausura
     *
     * @return Falta
     */
    public function setClausura($clausura)
    {
        $this->clausura = $clausura;

        return $this;
    }

    /**
     * Get clausura
     *
     * @return boolean
     */
    public function getClausura()
    {
        return $this->clausura;
    }

    /**
     * Get clausura
     *
     * @return boolean
     */
    public function getFaltaVistaTablet()
    {
        $falta["id"]=$this->getId();
        $falta["falta"]=$this->getFlata();
        $falta["texto_falta"]=$this->getTextoFalta();
        $falta["clausura"]=$this->getTextoFalta();

        return $falta;
    }
}
