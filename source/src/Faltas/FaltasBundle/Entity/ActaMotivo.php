<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Motivo
 *
 * @ORM\Table(name="acta_motivo")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\ActaMotivoRepository")
  * @ORM\HasLifecycleCallbacks()
  * @UniqueEntity("motivoCompleto")
  * @UniqueEntity("motivo")
 */
class ActaMotivo
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
     * @Assert\NotBlank()
     * @ORM\Column(name="motivo", type="string", length=10,unique=true)
     */
    private $motivo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="motivoCompleto", type="string", length=150)
     */
    private $motivoCompleto;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="tipo", type="string", length=1)
     */
    private $tipo;


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
     * Set motivo
     *
     * @param string $motivo
     *
     * @return Motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set motivoCompleto
     *
     * @param string $motivoCompleto
     *
     * @return Motivo
     */
    public function setMotivoCompleto($motivoCompleto)
    {
        $this->motivoCompleto = $motivoCompleto;

        return $this;
    }

    /**
     * Get motivoCompleto
     *
     * @return string
     */
    public function getMotivoCompleto()
    {
        return $this->motivoCompleto;
    }


    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Motivo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    public function __toString()
    {
        return $this->getMotivo()." | ".$this->getMotivoCompleto()." | ".$this->getTipo();
    }
}
