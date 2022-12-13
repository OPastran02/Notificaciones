<?php

namespace Laboratorio\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Muestra
 *
 * @ORM\Table(name="laboratorio_muestra")
 * @ORM\Entity(repositoryClass="Laboratorio\PedidoBundle\Repository\MuestraRepository")
 */
class Muestra
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMuestra;

    /**
     * @ORM\ManyToOne(targetEntity="Pedido", inversedBy="muestras")
     */
    protected $pedido;

    /**
     * @var bool
     *
     * @ORM\Column(name="supervisado", type="boolean")
     */
    private $supervisado;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioSupervisador;

    /**
     * @var bool
     *
     * @ORM\Column(name="autorizado", type="boolean")
     */
    private $autorizado;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     */
    private $usuarioAutorizador;

    /**
     * @var bool
     *
     * @ORM\Column(name="anulada", type="boolean")
     */
    private $anulada;

    /**
     * @ORM\OneToMany(targetEntity="MuestraEstados", mappedBy="muestra",cascade={"persist"})
     */
    protected $estados;

    /**
     * @ORM\ManyToMany(targetEntity="\Usuario\UsuarioBundle\Entity\Usuarios")
     * @ORM\JoinTable(name="laboratorio_muestra_auxiliarescampo",
     *      joinColumns={@ORM\JoinColumn(name="muestra_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     *      )
     */
    protected $auxiliares;

    /**
     * @ORM\OneToMany(targetEntity="CargaResultados", mappedBy="muestra",cascade={"persist"})
     */
    protected $resultados;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_toma_muestra", type="date",nullable=true)
     */
    private $fechaTomaMuestra;

    /**
     * @var string
     *
     * @ORM\Column(name="lugarExtraccion", type="string", length=70, nullable=true)
     */
    private $lugarExtraccion;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroMuestra", type="integer")
     */
    private $numeroMuestra;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->idMuestra;
    }

    /**
     * Set supervisado
     *
     * @param boolean $supervisado
     *
     * @return Muestra
     */
    public function setSupervisado($supervisado)
    {
        $this->supervisado = $supervisado;

        return $this;
    }

    /**
     * Get supervisado
     *
     * @return bool
     */
    public function getSupervisado()
    {
        return $this->supervisado;
    }

    /**
     * Set anulada
     *
     * @param boolean $anulada
     *
     * @return Muestra
     */
    public function setAnulada($anulada)
    {
        $this->anulada = $anulada;

        return $this;
    }

    /**
     * Get anulada
     *
     * @return bool
     */
    public function getAnulada()
    {
        return $this->anulada;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->estados = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set autorizado
     *
     * @param boolean $autorizado
     *
     * @return Muestra
     */
    public function setAutorizado($autorizado)
    {
        $this->autorizado = $autorizado;

        return $this;
    }

    /**
     * Get autorizado
     *
     * @return boolean
     */
    public function getAutorizado()
    {
        return $this->autorizado;
    }

    /**
     * Set pedido
     *
     * @param \Laboratorio\PedidoBundle\Entity\Pedido $pedido
     *
     * @return Muestra
     */
    public function setPedido(\Laboratorio\PedidoBundle\Entity\Pedido $pedido = null)
    {
        $this->pedido = $pedido;

        return $this;
    }

    /**
     * Get pedido
     *
     * @return \Laboratorio\PedidoBundle\Entity\Pedido
     */
    public function getPedido()
    {
        return $this->pedido;
    }

    /**
     * Set usuarioSupervisador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioSupervisador
     *
     * @return Muestra
     */
    public function setUsuarioSupervisador(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioSupervisador = null)
    {
        $this->usuarioSupervisador = $usuarioSupervisador;

        return $this;
    }

    /**
     * Get usuarioSupervisador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioSupervisador()
    {
        return $this->usuarioSupervisador;
    }

    /**
     * Set usuarioAutorizador
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $usuarioAutorizador
     *
     * @return Muestra
     */
    public function setUsuarioAutorizador(\Usuario\UsuarioBundle\Entity\Usuarios $usuarioAutorizador = null)
    {
        $this->usuarioAutorizador = $usuarioAutorizador;

        return $this;
    }

    /**
     * Get usuarioAutorizador
     *
     * @return \Usuario\UsuarioBundle\Entity\Usuarios
     */
    public function getUsuarioAutorizador()
    {
        return $this->usuarioAutorizador;
    }

    /**
     * Add estado
     *
     * @param \Laboratorio\PedidoBundle\Entity\MuestraEstados $estado
     *
     * @return Muestra
     */
    public function addEstado(\Laboratorio\PedidoBundle\Entity\MuestraEstados $estado)
    {
        $this->estados[] = $estado;

        return $this;
    }

    /**
     * Remove estado
     *
     * @param \Laboratorio\PedidoBundle\Entity\MuestraEstados $estado
     */
    public function removeEstado(\Laboratorio\PedidoBundle\Entity\MuestraEstados $estado)
    {
        $this->estados->removeElement($estado);
    }

    /**
     * Get estados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstados()
    {
        return $this->estados;
    }

    /**
     * Add auxiliare
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $auxiliare
     *
     * @return Muestra
     */
    public function addAuxiliare(\Usuario\UsuarioBundle\Entity\Usuarios $auxiliare)
    {
        $this->auxiliares[] = $auxiliare;

        return $this;
    }

    /**
     * Remove auxiliare
     *
     * @param \Usuario\UsuarioBundle\Entity\Usuarios $auxiliare
     */
    public function removeAuxiliare(\Usuario\UsuarioBundle\Entity\Usuarios $auxiliare)
    {
        $this->auxiliares->removeElement($auxiliare);
    }

    /**
     * Get auxiliares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuxiliares()
    {
        return $this->auxiliares;
    }

    public function setAuxiliares($auxiliares)
    {
        $this->auxiliares = $auxiliares;
    }

    /**
     * Add resultado
     *
     * @param \Laboratorio\PedidoBundle\Entity\CargaResultados $resultado
     *
     * @return Muestra
     */
    public function addResultado(\Laboratorio\PedidoBundle\Entity\CargaResultados $resultado)
    {
        $this->resultados[] = $resultado;

        return $this;
    }

    /**
     * Remove resultado
     *
     * @param \Laboratorio\PedidoBundle\Entity\CargaResultados $resultado
     */
    public function removeResultado(\Laboratorio\PedidoBundle\Entity\CargaResultados $resultado)
    {
        $this->resultados->removeElement($resultado);
    }

    /**
     * Get resultados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultados()
    {
        return $this->resultados;
    }

    /**
     * Set fechaTomaMuestra
     *
     * @param \DateTime $fechaTomaMuestra
     *
     * @return Muestra
     */
    public function setFechaTomaMuestra($fechaTomaMuestra)
    {
        $this->fechaTomaMuestra = $fechaTomaMuestra;

        return $this;
    }

    /**
     * Get fechaTomaMuestra
     *
     * @return \DateTime
     */
    public function getFechaTomaMuestra()
    {
        return $this->fechaTomaMuestra;
    }

    /**
     * Set lugarExtraccion
     *
     * @param string $lugarExtraccion
     *
     * @return Calles
     */
    public function setLugarExtraccion($lugarExtraccion)
    {
        $this->lugarExtraccion = $lugarExtraccion;

        return $this;
    }

    /**
     * Get lugarExtraccion
     *
     * @return string
     */
    public function getLugarExtraccion()
    {
        return $this->lugarExtraccion;
    }

    /**
     * Get idMuestra
     *
     * @return integer
     */
    public function getIdMuestra()
    {
        return $this->idMuestra;
    }

    public function __toString()
    {
        return ''.$this->numeroMuestra;
    }


    /**
     * Set numeroMuestra
     *
     * @param integer $numeroMuestra
     *
     * @return Muestra
     */
    public function setNumeroMuestra($numeroMuestra)
    {
        $this->numeroMuestra = $numeroMuestra;

        return $this;
    }

    /**
     * Get numeroMuestra
     *
     * @return integer
     */
    public function getNumeroMuestra()
    {
        return $this->numeroMuestra;
    }

    private function getAreasIntervinientes()
    {
        $areas = array();
        foreach ($this->resultados as $resultado)
            if ($resultado->getResultado() != '')
                $areas[$resultado->getDeterminacion()->getArea()->getId()] = $resultado->getDeterminacion()->getArea();

        return empty($areas) ? null : $areas;
    }

    public function getNumeroMuestraCompleto()
    {
        $codigo = $this->pedido->getPrograma()->getCodigo();

        if ($this->fechaTomaMuestra)
        {
            $areasArray = array();
            $areas = '';

            if ($this->getAreasIntervinientes())
            {
                foreach ($this->getAreasIntervinientes() as $area)
                {
                    if ($area->getId() == 22)
                        $areasArray[0] = 'Q';
                    elseif ($area->getId() == 20)
                        $areasArray[1] = 'M';
                    elseif ($area->getId() == 21)
                        $areasArray[2] = 'I';
                }

                ksort($areasArray);
                $areas = implode('', $areasArray);

                if ($codigo != 'Inexistente')
                {
                    if ($areas != '')
                        return $this->numeroMuestra.'/'.date_format($this->fechaTomaMuestra, 'y').'-'.$codigo.'-'.$areas;
                    else
                        return $this->numeroMuestra.'/'.date_format($this->fechaTomaMuestra, 'y').'-'.$codigo;
                }
                else
                    return $this->numeroMuestra;
            }
            else
                return $this->numeroMuestra;
        }
        else
            return $this->numeroMuestra;
    }

    public function getIncertidumbre()
    {
        $incertidumbre = $incertidumbrePreliminar = array();
        foreach ($this->estados as $estado)
            array_push($incertidumbre, $estado->observacionesToArray());

        foreach ($incertidumbre as $inc)
        {
            if ($inc)
            {
                switch (key($inc))
                {
                    case 'CAMPO':
                        $incertidumbrePreliminar[0] = $inc;
                        break;

                    case 'FISICO-QUIMICA':
                        $incertidumbrePreliminar[1] = $inc;
                        break;

                    case 'INSTRUMENTAL':
                        $incertidumbrePreliminar[2] = $inc;
                        break;

                    case 'BIOLOGICO':
                        $incertidumbrePreliminar[3] = $inc;
                        break;
                }
            }
        }

        unset($incertidumbre);
        $incertidumbre = array();

        if ($incertidumbrePreliminar)
            foreach ($incertidumbrePreliminar as $incPre)
                $incertidumbre[key($incPre)] = $incPre[key($incPre)];

        return empty($incertidumbre) ? null : $incertidumbre;
    }

    private function getEstadoDT ()
    {
        return $this->supervisado ? 'Aprobada' : 'Pendiente';
    }

    private function getEstadoCampo ()
    {
        $estadoCampo = '';

        foreach ($this->estados as $estado)
        {
            if ($estado->getArea()->getArea() == 'CAMPO')
            {
                if ($estado->getEstado()->getEstado() == 'Aprobada')
                    $estadoCampo = $estado->getEstado()->getEstado();
                else
                    $estadoCampo = 'Pendiente';
            }
        }

        return $estadoCampo ? $estadoCampo : null;
    }

    private function getEstadoQMI ()
    {
        $estadoQMI = '';

        $b = '';
        $i = '';
        $fq = '';

        foreach ($this->estados as $estado)
        {
            if ($estado->getArea()->getArea() != 'CAMPO')
            {
                if ($estado->getArea()->getId() == 20)
                    $b = $estado->getEstado()->getEstado();
                elseif ($estado->getArea()->getId() == 21)
                    $i = $estado->getEstado()->getEstado();
                elseif ($estado->getArea()->getId() == 22)
                    $fq = $estado->getEstado()->getEstado();
            }
        }

        // Descartar las áreas 'null'
        $arrayEstados = array();
        if ($b)
            array_push($arrayEstados, $b);
        if ($i)
            array_push($arrayEstados, $i);
        if ($fq)
            array_push($arrayEstados, $fq);

        // Ver si todas las áreas están aprobadas o no
        $z = 0;
        while (isset($arrayEstados[$z]) && $arrayEstados[$z] == 'Aprobada' && $z < count($arrayEstados))
            $z++;

        if ($z == count($arrayEstados))
            $estadoQMI = 'Aprobada';
        else
            $estadoQMI = 'Pendiente';

        return $estadoQMI ? $estadoQMI : null;
    }

    private function getEstadoAprobacion ()
    {
        if ($this->getEstadoDT() == 'Aprobada' && $this->getEstadoCampo() == 'Aprobada' && $this->getEstadoQMI() == 'Aprobada')
            return 2;
        elseif ($this->getEstadoDT() == 'Aprobada' || $this->getEstadoCampo() == 'Aprobada' || $this->getEstadoQMI() == 'Aprobada')
            return 1;
        else
            return 0;
    }

    public function crearMuestraPendiente ()
    {
        return array(
            'idMuestra' => $this->idMuestra,
            'numeroMuestra' => $this->getNumeroMuestraCompleto(),
            'estadoAprobacion' => $this->getEstadoAprobacion(),
            'fechaTomaMuestra' => $this->fechaTomaMuestra,
            'programa' => $this->pedido->getPrograma()->getPrograma(),
            'tipo' => $this->pedido->getTipoPedido()->getTipo(),
            'DT' => $this->getEstadoDT(),
            'CAMPO' => $this->getEstadoCampo(),
            'QMI' => $this->getEstadoQMI()
        );
    }
}
