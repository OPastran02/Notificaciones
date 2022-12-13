<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ambientes
 *
 * @ORM\Table(name="equipoRuidos")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\EquipoRuidosRepository")
 */
class EquipoRuidos
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
     * @ORM\Column(name="Tipo", type="string", length=1)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="Marca", type="string", length=45)
     */
    private $marca;

    /**
     * @var string
     *
     * @ORM\Column(name="Modelo", type="string", length=45)
     */
    private $modelo;

    /**
     * @var string
     *
     * @ORM\Column(name="Serie", type="string", length=45)
     */
    private $serie;

    /**
     * @var string
     *
     * @ORM\Column(name="Clase", type="string", length=45)
     */
    private $clase;

    /**
     * @var string
     *
     * @ORM\Column(name="Denominacion", type="string", length=45)
     */
    private $denominacion;

    


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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return EquipoRuidos
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

    /**
     * Set marca
     *
     * @param string $marca
     *
     * @return EquipoRuidos
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;
    
        return $this;
    }

    /**
     * Get marca
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     *
     * @return EquipoRuidos
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    
        return $this;
    }

    /**
     * Get modelo
     *
     * @return string
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set serie
     *
     * @param string $serie
     *
     * @return EquipoRuidos
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    
        return $this;
    }

    /**
     * Get serie
     *
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set clase
     *
     * @param string $clase
     *
     * @return EquipoRuidos
     */
    public function setClase($clase)
    {
        $this->clase = $clase;
    
        return $this;
    }

    /**
     * Get clase
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     *
     * @return EquipoRuidos
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    
        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }
}
