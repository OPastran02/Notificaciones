<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistorialCargaResultados
 *
 * @ORM\Table(name="laboratorio_historial_carga_resultados")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\HistorialCargaResultadosRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class HistorialCargaResultados
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
     * @ORM\ManyToOne(targetEntity="CargaResultados", inversedBy="historial")     
     */
    protected $idResultado;

    /**
     * @ORM\ManyToOne(targetEntity="Determinacion")
     */
    protected $determinacion;

    /**
     * @var string
     *
     * @ORM\Column(name="resultado", type="string")
     */
    private $resultado;

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
     * @var \DateTime
     *     
     * @ORM\Column(name="Fecha_Creado", type="datetime")
     */
    private $fechaCreado;


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
     * @return HistorialCargaResultados
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
     * Set fechaInicioAnalisis
     *
     * @param \DateTime $fechaInicioAnalisis
     *
     * @return HistorialCargaResultados
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
     * @return HistorialCargaResultados
     */
    public function setFechaFinAnalisis($fechaFinAnalisis)
    {
        $this->fechaFinAnalisis = $fechaFinAnalisis;

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
     * Set idResultado
     *
     * @param \Laboratorio\PedidoBundle\Entity\CargaResultados $idResultado
     *
     * @return HistorialCargaResultados
     */
    public function setIdResultado(\Laboratorio\PedidoBundle\Entity\CargaResultados $idResultado = null)
    {
        $this->idResultado = $idResultado;

        return $this;
    }

    /**
     * Get idResultado
     *
     * @return \Laboratorio\PedidoBundle\Entity\CargaResultados
     */
    public function getIdResultado()
    {
        return $this->idResultado;
    }

    /**
     * Set determinacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Determinacion $determinacion
     *
     * @return HistorialCargaResultados
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
     * Set legislacion
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacion
     *
     * @return HistorialCargaResultados
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
     * Set legislacionSinContacto
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacionSinContacto
     *
     * @return HistorialCargaResultados
     */
    public function setLegislacionSinContacto(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacionSinContacto = null)
    {
        $this->legislacionSinContacto = $legislacionSinContacto;

        return $this;
    }

    /**
     * Get legislacionSinContacto
     *
     * @return \Laboratorio\PedidoBundle\Entity\Legislacion
     */
    public function getLegislacionSinContacto()
    {
        return $this->legislacionSinContacto;
    }

    /**
     * Set legislacionPasivo
     *
     * @param \Laboratorio\PedidoBundle\Entity\Legislacion $legislacionPasivo
     *
     * @return HistorialCargaResultados
     */
    public function setLegislacionPasivo(\Laboratorio\PedidoBundle\Entity\Legislacion $legislacionPasivo = null)
    {
        $this->legislacionPasivo = $legislacionPasivo;

        return $this;
    }

    /**
     * Get legislacionPasivo
     *
     * @return \Laboratorio\PedidoBundle\Entity\Legislacion
     */
    public function getLegislacionPasivo()
    {
        return $this->legislacionPasivo;
    }

    /**
     * Set usuario
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuario
     *
     * @return HistorialCargaResultados
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
    * @ORM\PrePersist    
    */
    public function setFechaCreadoValue()
    {
        $this->fechaCreado = new \DateTime();
        //$this->setIdUsuarioCreador();        
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

        return $this->resultado." ".$this->determinacion." ".$this->legislacion." ".$this->legislacionSinContacto." ".$this->legislacionPasivo." ".$this->usuario." ".$fechaInicio." ".$fechaFin;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return HistorialCargaResultados
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Get fechaCreado
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }
}
