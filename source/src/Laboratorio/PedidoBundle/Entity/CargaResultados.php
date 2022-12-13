<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CargaResultados
 *
 * @ORM\Table(name="laboratorio_carga_resultados")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\CargaResultadosRepository")
 */
class CargaResultados
{
    /**
     * @var bool
     *
     * @ORM\Column(name="bloqueado", type="boolean")
     */
    private $bloqueado;

    /**
     * @ORM\ManyToOne(targetEntity="Determinacion")
     */
    protected $determinacion;

    /**
     * @ORM\ManyToOne(targetEntity="Muestra", inversedBy="resultados")
     */
    protected $muestra;

    /**
     * @ORM\ManyToOne(targetEntity="Legislacion")
     */
    protected $legislacion;

    /**
     * @ORM\ManyToOne(targetEntity="Legislacion")
     */
    protected $legislacionSinContacto;

    /**
     * @ORM\ManyToOne(targetEntity="Legislacion")
     */
    protected $legislacionPasivo;

    /**
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    protected $usuario;

    /**
     * @ORM\OneToMany(targetEntity="HistorialCargaResultados", mappedBy="idResultado",cascade={"persist"})
     */
    protected $historial;

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
     * @ORM\Column(name="resultado", type="string")
     */
    private $resultado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fecha_Inicio_Analisis", type="date",nullable=true)
     */
    private $fechaInicioAnalisis;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fecha_Fin_Analisis", type="date",nullable=true)
     */
    private $fechaFinAnalisis;


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
     * Set resultado
     *
     * @param string $resultado
     *
     * @return CargaResultados
     */
    public function setResultado($resultado)
    {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * Get resultado
     *
     * @return string
     */
    public function getResultado()
    {
        return $this->resultado;
    }

    /**
     * Set determinacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Determinacion $determinacion
     *
     * @return CargaResultados
     */
    public function setDeterminacion(\Laboratorio\PedidoBundle\Entity\Determinacion $determinacion = null)
    {
        $this->determinacion = $determinacion;

        return $this;
    }

    /**
     * Get determinacion
     *
     * @return \Laboratorio\PedidoBundle\Entity\Determinacion
     */
    public function getDeterminacion()
    {
        return $this->determinacion;
    }

    /**
     * Set muestra
     *
     * @param \Laboratorio\PedidoBundle\Entity\Muestra $muestra
     *
     * @return CargaResultados
     */
    public function setMuestra(\Laboratorio\PedidoBundle\Entity\Muestra $muestra = null)
    {
        $this->muestra = $muestra;

        return $this;
    }

    /**
     * Get muestra
     *
     * @return \Laboratorio\PedidoBundle\Entity\Muestra
     */
    public function getMuestra()
    {
        return $this->muestra;
    }

    /**
     * Set legislacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacion
     *
     * @return CargaResultados
     */
    public function setLegislacion(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacion = null)
    {
        $this->legislacion = $legislacion;

        return $this;
    }

    /**
     * Get legislacion
     *
     * @return \Laboratorio\PedidoBundle\Entity\Legislacion
     */
    public function getLegislacion()
    {
        return $this->legislacion;
    }

    /**
     * Set usuario
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuario
     *
     * @return CargaResultados
     */
    public function setUsuario(\Usuario\UsuarioBundle\Entity\Usuarios $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set fechaInicioAnalisis
     *
     * @param \DateTime $fechaInicioAnalisis
     *
     * @return CargaResultados
     */
    public function setFechaInicioAnalisis($fechaInicioAnalisis)
    {
        $this->fechaInicioAnalisis = $fechaInicioAnalisis;

        return $this;
    }

    /**
     * Get fechaInicioAnalisis
     *
     * @return \DateTime
     */
    public function getFechaInicioAnalisis()
    {
        return $this->fechaInicioAnalisis;
    }

    /**
     * Set fechaFinAnalisis
     *
     * @param \DateTime $fechaFinAnalisis
     *
     * @return CargaResultados
     */
    public function setFechaFinAnalisis($fechaFinAnalisis)
    {
        if ($fechaFinAnalisis)
            $f = date_create(date_format($fechaFinAnalisis, 'Y-m-d'));
        else
            $f = date_create('today');

        $this->fechaFinAnalisis = date_create_from_format('j-m-Y', date_format($f, 'd').'-'.date_format($f, 'm').'-2020');

        return $this;
    }

    /**
     * Get fechaFinAnalisis
     *
     * @return \DateTime
     */
    public function getFechaFinAnalisis()
    {
        return $this->fechaFinAnalisis;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->historial = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add historial
     *
     * @param \Laboratorio\PedidoBundle\Entity\HistorialCargaResultados $historial
     *
     * @return CargaResultados
     */
    public function addHistorial(\Laboratorio\PedidoBundle\Entity\HistorialCargaResultados $historial)
    {
        $this->historial[] = $historial;

        return $this;
    }

    /**
     * Remove historial
     *
     * @param \Laboratorio\PedidoBundle\Entity\HistorialCargaResultados $historial
     */
    public function removeHistorial(\Laboratorio\PedidoBundle\Entity\HistorialCargaResultados $historial)
    {
        $this->historial->removeElement($historial);
    }

    /**
     * Get historial
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistorial()
    {
        return $this->historial;
    }

    /**
     * Set bloqueado
     *
     * @param boolean $bloqueado
     *
     * @return CargaResultados
     */
    public function setBloqueado($bloqueado)
    {
        $this->bloqueado = $bloqueado;

        return $this;
    }

    /**
     * Get bloqueado
     *
     * @return boolean
     */
    public function getBloqueado()
    {
        return $this->bloqueado;
    }

    public function __toString()
    {
        $fechaInicio= '';
        $fechaFin = '';
        if($this->fechaInicioAnalisis)
        {
            $fechaInicio= $this->fechaInicioAnalisis->format('Y-m-d');
        }
        if($this->fechaFinAnalisis)
        {
            $fechaFin = $this->fechaFinAnalisis->format('Y-m-d');
        }

        //return $this->legislacion." ".$this->usuario." ".$this->id." ".$this->resultado." ".$fechaInicio." ".$fechaFin;
        return $this->resultado." ".$this->determinacion." ".$this->legislacion." ".$this->legislacionSinContacto." ".$this->legislacionPasivo." ".$this->usuario." ".$fechaInicio." ".$fechaFin;
    }

    /**
     * Set legislacionSinContacto
     *
     * @param string $legislacionSinContacto
     *
     * @return CargaResultados
     */
    public function setLegislacionSinContacto($legislacionSinContacto)
    {
        $this->legislacionSinContacto = $legislacionSinContacto;

        return $this;
    }

    /**
     * Get legislacionSinContacto
     *
     * @return string
     */
    public function getLegislacionSinContacto()
    {
        return $this->legislacionSinContacto;
    }

    /**
     * Set legislacionPasivo
     *
     * @param string $legislacionPasivo
     *
     * @return CargaResultados
     */
    public function setLegislacionPasivo($legislacionPasivo)
    {
        $this->legislacionPasivo = $legislacionPasivo;

        return $this;
    }

    /**
     * Get legislacionPasivo
     *
     * @return string
     */
    public function getLegislacionPasivo()
    {
        return $this->legislacionPasivo;
    }
}
