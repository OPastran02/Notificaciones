<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programa
 *
 * @ORM\Table(name="laboratorio_programa")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\ProgramaRepository")
 */
class Programa
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
     * @ORM\Column(name="Programa", type="string", length=255, unique=true)
     */
    private $programa;

    /**
     * @ORM\ManyToMany(targetEntity="Legislacion", inversedBy="programas")
     * @ORM\JoinTable(name="laboratorio_programa_legislacion")
     */
    protected $legislaciones;

    /**
     * @ORM\ManyToMany(targetEntity="Determinacion", inversedBy="programas")
     * @ORM\JoinTable(name="laboratorio_programa_determinacion")
     */
    protected $determinaciones;

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
     * Set programa
     *
     * @param string $programa
     *
     * @return Programa
     */
    public function setPrograma($programa)
    {
        $this->programa = $programa;

        return $this;
    }

    /**
     * Get programa
     *
     * @return string
     */
    public function getPrograma()
    {
        return $this->programa;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->legislaciones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add legislacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacione
     *
     * @return Programa
     */
    public function addLegislacione(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacione)
    {
        $this->legislaciones[] = $legislacione;

        return $this;
    }

    /**
     * Remove legislacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacione
     */
    public function removeLegislacione(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacione)
    {
        $this->legislaciones->removeElement($legislacione);
    }

    /**
     * Get legislaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLegislaciones()
    {
        return $this->legislaciones;
    }

    /**
     * Add determinacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\Determinacion $determinacione
     *
     * @return Programa
     */
    public function addDeterminacione(\Laboratorio\PedidoBundle\Entity\Determinacion $determinacione)
    {
        $this->determinaciones[] = $determinacione;

        return $this;
    }

    /**
     * Remove determinacione
     *
     * @param \Laboratorio\PedidoBundle\Entity\Determinacion $determinacione
     */
    public function removeDeterminacione(\Laboratorio\PedidoBundle\Entity\Determinacion $determinacione)
    {
        $this->determinaciones->removeElement($determinacione);
    }

    /**
     * Get determinaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeterminaciones()
    {
        return $this->determinaciones;
    }

    public function __toString()
    {
        return $this->programa;
    }

    public function getCodigo()
    {
        switch ($this->id)
        {
            case 1:
                return 'N'; //Natatorios

            case 2:
                return 'B'; //Agua de bebida

            case 3:
                return 'L'; //Lagos y Lagunas

            case 4:
                return 'RCH'; //Riachuelo

            case 5:
                return 'RP'; //Río de la Plata

            case 6:
                return 'A'; //Arroyos

            case 7:
                return 'AV'; //Aguas Varias

            case 8:
            case 21:
                return 'E'; //Efluentes (cualquiera de los dos)

            case 9:
                return 'MA'; //Monitoreo Atmosférico Manual

            case 10:
                return 'CAC'; //Calidad de Aire CUMAR - Subprograma I

            case 11:
                return 'FF'; //Contaminación atmosférica (fuentes fijas)

            case 12:
                return 'FM'; //Contaminación atmosférica (fuentes móviles)

            case 13:
                return 'Inexistente'; //Monitoreo Atmosférico Automático

            case 14:
                return 'SV'; //Calidad de Suelos Varios

            case 15:
                return 'SPP'; //Calidad de Suelos en Plazas y Parques

            case 16:
                return 'COM'; //Calidad de Compost

            case 17:
                return 'JA'; //Calidad de Juegos de Agua

            case 18:
                return 'HU'; //Calidad de Agua de Humedales

            case 19:
                return 'IL'; //Interlaboratorios

            case 20:
                return 'IAL'; //Intralaboratorios
            
            default:
                return 'Inexistente'; //Ningún caso de los anteriores
        }
    }
}
