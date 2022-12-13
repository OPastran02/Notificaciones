<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ambientes
 *
 * @ORM\Table(name="Vibraciones")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\VibracionesRepository")
 */

class Vibraciones
{
    /**
     * @var int
     *
     * @ORM\Column(name="idVibraciones", type="integer")
     * @ORM\Id
     */
    private $idVibraciones;

    /**
     * @var int
     *
     * @ORM\Column(name="Tipo", type="integer")
     */
    private $Tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="horario", type="integer")
     */
    private $horario;

    /**
     * @var float
     *
     * @ORM\Column(name="LMP_Eje_X", type="float")
     */
    private $LMP_Eje_X;

    /**
     * @var float
     *
     * @ORM\Column(name="LMP_Eje_Y", type="float")
     */
    private $LMP_Eje_Y;

    /**
     * @var float
     *
     * @ORM\Column(name="LMP_Eje_Z", type="float")
     */
    private $LMP_Eje_Z;

    /**
     * @var int
     *
     * @ORM\Column(name="Area", type="integer")
     */
    private $Area;


    /**
     * Get idVibraciones
     *
     * @return integer
     */
    public function getIdVibraciones()
    {
        return $this->idVibraciones;
    }

    /**
     * Get Tipo
     *
     * @return integer
     */
    public function getTipo()
    {
        return $this->Tipo;
    }

    /**
     * Get horario
     *
     * @return integer
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * Get LMP_Eje_X
     *
     * @return integer
     */
    public function getLMP_Eje_X()
    {
        return $this->LMP_Eje_X;
    }

    /**
     * Get LMP_Eje_Y
     *
     * @return integer
     */
    public function getLMP_Eje_Y()
    {
        return $this->LMP_Eje_Y;
    }

    /**
     * Get LMP_Eje_Z
     *
     * @return integer
     */
    public function getLMP_Eje_Z()
    {
        return $this->LMP_Eje_Z;
    }

    /**
     * Get Area
     *
     * @return integer
     */
    public function getArea()
    {
        return $this->Area;
    }



    /**
     * Set idVibraciones
     *
     * @param integer $idVibraciones
     *
     * @return Vibraciones
     */
    public function setIdVibraciones($idVibraciones)
    {
        $this->idVibraciones = $idVibraciones;

        return $this;
    }

    /**
     * Set Tipo
     *
     * @param integer $Tipo
     *
     * @return Vibraciones
     */
    public function setTipo($Tipo)
    {
        $this->Tipo = $Tipo;

        return $this;
    }

    /**
     * Set horario
     *
     * @param integer $horario
     *
     * @return Vibraciones
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;

        return $this;
    }

    /**
     * Set LMP_Eje_X
     *
     * @param integer $LMP_Eje_X
     *
     * @return Vibraciones
     */
    public function setLMP_Eje_X($LMP_Eje_X)
    {
        $this->LMP_Eje_X = $LMP_Eje_X;

        return $this;
    }

    /**
     * Set LMP_Eje_Y
     *
     * @param integer $LMP_Eje_Y
     *
     * @return Vibraciones
     */
    public function setLMP_Eje_Y($LMP_Eje_Y)
    {
        $this->LMP_Eje_Y = $LMP_Eje_Y;

        return $this;
    }

    /**
     * Set LMP_Eje_Z
     *
     * @param integer $LMP_Eje_Z
     *
     * @return Vibraciones
     */
    public function setLMP_Eje_Z($LMP_Eje_Z)
    {
        $this->LMP_Eje_Y = $LMP_Eje_Z;

        return $this;
    }

    /**
     * Set Area
     *
     * @param integer $Area
     *
     * @return Vibraciones
     */
    public function setArea($Area)
    {
        $this->Area = $Area;

        return $this;
    }
}
