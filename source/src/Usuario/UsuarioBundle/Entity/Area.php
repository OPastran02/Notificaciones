<?php

namespace Usuario\UsuarioBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Usuario\UsuarioBundle\Repository\AreaRepository")
 * @UniqueEntity("area")
 */
class Area
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="area", type="string", length=50, unique=true)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=100)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="cargo", type="string", length=100)
     */
    private $cargo;


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
     * Set idArea
     *
     * @param integer $idArea
     *
     * @return Area
     */
    public function setIdArea($idArea)
    {
        $this->idArea = $idArea;

        return $this;
    }

    /**
     * Get idArea
     *
     * @return int
     */
    public function getIdArea()
    {
        return $this->idArea;
    }

    /**
     * Set area
     *
     * @param string $area
     *
     * @return Area
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Area
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     *
     * @return Area
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    public function __toString()
    {
        return $this->getArea();
    }

    /**
     * Get cargo
     *
     * @return string
     */
    public function getAreaVistaTablet()
    {
        $grupoInspecciones["id"]=$this->getId();
        $grupoInspecciones["area"]=$this->getArea();
        $grupoInspecciones["titulo"]=$this->getTitulo();
        $grupoInspecciones["cargo"]=$this->getCargo();

        return $grupoInspecciones;
    }
}
