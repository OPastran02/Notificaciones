<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ruidos
 *
 * @ORM\Table(name="Ruidos")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\RuidosRepository")
 */
class Ruidos
{
    /**
     * @var int
     *
     * @ORM\Column(name="idRuidos", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idRuidos;

    /**
     * @var int
     *
     * @ORM\Column(name="ASAE", type="integer", nullable=true)
     */
    private $ASAE;

    /**
     * @var int
     *
     * @ORM\Column(name="Ambiente", type="integer", nullable=true)
     */
    private $Ambiente;

    /**
     * @var int
     *
     * @ORM\Column(name="Periodo", type="integer", nullable=true)
     */
    private $Periodo;

    /**
     * @var int
     *
     * @ORM\Column(name="ASA", type="integer",nullable=true)
     */
    private $ASA;

    /**
     * @var int
     *
     * @ORM\Column(name="Recinto", type="integer",nullable=true)
     */
    private $Recinto;

    /**
     * @var int
     *
     * @ORM\Column(name="Uso_predominante", type="integer",nullable=true)
     */
    private $Uso_predominante;

    /**
     * @var int
     *
     * @ORM\Column(name="LMP", type="integer",nullable=true)
     */
    private $LMP;
    
    /**
     * @var int
     *
     * @ORM\Column(name="Correccion", type="integer",nullable=true)
     */
    private $Correccion;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->idRuidos;
    }

    /**
     * Get ASAE
     *
     * @return integer
     */
    public function getASAE()
    {
        return $this->ASAE;
    }

    /**
     * Get Ambiente
     *
     * @return integer
     */
    public function getAmbiente()
    {
        return $this->Ambiente;
    }

    /**
     * Get Periodo
     *
     * @return integer
     */
    public function getPeriodo()
    {
        return $this->Periodo;
    }

    /**
     * Get ASA
     *
     * @return integer
     */
    public function getASA()
    {
        return $this->ASA;
    }

    /**
     * Get Recinto
     *
     * @return integer
     */
    public function getRecinto()
    {
        return $this->Recinto;
    }

    /**
     * Get Uso_predominante
     *
     * @return integer
     */
    public function getUso_predominante()
    {
        return $this->Uso_predominante;
    }

    /**
     * Get LMP
     *
     * @return integer
     */
    public function getLMP()
    {
        return $this->LMP;
    }

    /**
     * Get Correccion
     *
     * @return integer
     */
    public function getCorreccion()
    {
        return $this->Correccion;
    }
}
