<?php

namespace Encuesta\EncuestaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Respuestas
 *
 * @ORM\Table(name="encuesta_respuestas")
 * @ORM\Entity(repositoryClass="Encuesta\EncuestaBundle\Repository\RespuestasRepository")
 */
class Respuestas
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
     * @ORM\Column(name="Respuesta", type="string", length=255, unique=true)
     */
    private $respuesta;

    /**
     * @var int
     *
     * @ORM\Column(name="originalId", type="integer")     
     */
    private $originalId;


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
     * Set respuesta
     *
     * @param string $respuesta
     *
     * @return Respuestas
     */
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    /**
     * Get respuesta
     *
     * @return string
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    public function __toString()
    {
        return $this->respuesta;
    }
    

    /**
     * Set originalId
     *
     * @param integer $originalId
     *
     * @return Respuestas
     */
    public function setOriginalId($originalId)
    {
        $this->originalId = $originalId;

        return $this;
    }

    /**
     * Get originalId
     *
     * @return integer
     */
    public function getOriginalId()
    {
        return $this->originalId;
    }
}
