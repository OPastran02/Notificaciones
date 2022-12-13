<?php

namespace Faltas\FaltasBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoClausura
 *
 * @ORM\Table(name="faja_tipo_clausura")
 * @ORM\Entity(repositoryClass="Faltas\FaltasBundle\Repository\TipoClausuraRepository")
 */
class TipoClausura
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
     * @ORM\Column(name="tipo_clausura", type="string", length=20, unique=true)
     */
    private $tipoClausura;

    /**
     * @var int
     *
     * @ORM\Column(name="habilitado", type="smallint", nullable=true)
     */
    private $habilitado;


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
     * Set tipoClausura
     *
     * @param string $tipoClausura
     *
     * @return TipoClausura
     */
    public function setTipoClausura($tipoClausura)
    {
        $this->tipoClausura = $tipoClausura;

        return $this;
    }

    /**
     * Get tipoClausura
     *
     * @return string
     */
    public function getTipoClausura()
    {
        return $this->tipoClausura;
    }

    /**
     * Set habilitado
     *
     * @param integer $habilitado
     *
     * @return TipoClausura
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return int
     */
    public function getHabilitado()
    {
        return $this->habilitado;
    }


     public function __toString()
    {
        return $this->getTipoClausura();
    }
}

