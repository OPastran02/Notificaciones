<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ambientes
 *
 * @ORM\Table(name="ambiente")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\AmbientesRepository")
 */
class Ambientes
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
     * @ORM\Column(name="tipo", type="string", length=1)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="ambiente", type="string", length=45)
     */
    private $ambiente;

    

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
     * @return Ambientes
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
     * Set ambiente
     *
     * @param string $ambiente
     *
     * @return Ambientes
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
    
        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }
}
