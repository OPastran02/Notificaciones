<?php

namespace Establecimiento\EstablecimientoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ResultadosUltimaInspeccion
 *
 * @ORM\Table(name="resultados_ultima_inspeccion")
 * @ORM\Entity(repositoryClass="Establecimiento\EstablecimientoBundle\Repository\ResultadosUltimaInspeccionRepository")
 */
class ResultadosUltimaInspeccion
{   
    /**     
     * @ORM\OneToOne(targetEntity="Establecimiento",inversedBy="resultadosUltimaInspeccion" )
     */
    protected $establecimiento;

    /**     
     * @ORM\ManyToMany(targetEntity="TipoResiduos")
     * @ORM\JoinTable(name="resultadosultimainspeccion_tiporesiduos",
     *      joinColumns={@ORM\JoinColumn(name="resultadosUltimaInspeccion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tipoResiduo_id", referencedColumnName="id")}
     *      )
     */
    protected $tipoResiduos;

    /**     
     * @ORM\ManyToMany(targetEntity="TipoTratamiento")
     * @ORM\JoinTable(name="resultadosultimainspeccion_tipotratamiento",
     *      joinColumns={@ORM\JoinColumn(name="resultadosUltimaInspeccion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tipoTratamiento_id", referencedColumnName="id")}
     *      )
     */
    protected $tipoTratamiento;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="proximaInspeccion", type="date", nullable=true)
     */
    private $proximaInspeccion;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1095)
     * @ORM\Column(name="inspeccionarCara", type="smallint", nullable=true)
     */
    private $inspeccionarCara;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=9999999)
     * @ORM\Column(name="superficieCubierta", type="float", nullable=true)
     */
    private $superficieCubierta;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=9999999)
     * @ORM\Column(name="superficieTotal", type="float", nullable=true)
     */
    private $superficieTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="inscripto326", type="string", length=2, nullable=true)
     */
    private $inscripto326;

    /**     
     *
     * @ORM\ManyToOne(targetEntity="EstadoRes326")
     */
    protected $estado326;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1000)
     * @ORM\Column(name="cantTanques", type="smallint", nullable=true)
     */
    private $cantTanques;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1000)
     * @ORM\Column(name="tanquesActivos", type="smallint", nullable=true)
     */
    private $tanquesActivos;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1000)
     * @ORM\Column(name="tanquesCegadosInertizados", type="smallint", nullable=true)
     */
    private $tanquesCegadosInertizados;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=1000)
     * @ORM\Column(name="tanquesErradicados", type="smallint", nullable=true)
     */
    private $tanquesErradicados;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=999999999)
     * @ORM\Column(name="curl", type="bigint", nullable=true, unique=true)
     */
    private $curl;

    /**
     * @var string
     *
     * @ORM\Column(name="generaEfluentesLiquidosInd", type="string", length=2, nullable=true)
     */
    private $generaEfluentesLiquidosInd;

    /**
     * @var int
     *
     * @Assert\Range(min=0,max=999999999)
     * @ORM\Column(name="caudalVuelcoMax", type="float", nullable=true)
     */
    private $caudalVuelcoMax;

    /**
     * @var \DateTime
     *
     * @Assert\Time()
     * @ORM\Column(name="horaVuelvoInicial", type="time", nullable=true)
     */
    private $horaVuelvoInicial;

    /**
     * @var \DateTime
     *
     * @Assert\Time()
     * @ORM\Column(name="horaVuelcoFinal", type="time", nullable=true)
     */
    private $horaVuelcoFinal;

    /**
     * @ORM\ManyToOne(targetEntity="TramiteEfluentes")
     */
    protected $tramiteEfluentes;

    /**
     * @var string
     *
     * @ORM\Column(name="tramiteEfluentesEstado", type="string", length=2, nullable=true)
     */
    private $tramiteEfluentesEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="reCirculacionAgua", type="string", length=2, nullable=true)
     */
    private $reCirculacionAgua;

    /**
     * @var string
     *
     * @ORM\Column(name="CTMMC", type="string", length=2, nullable=true)
     */
    private $cTMMC;

    /**
     * @ORM\ManyToOne(targetEntity="DestinoVuelco")
     */
    protected $destinoVuelcoEfluentes;

    /**
     * @var string
     *
     * @ORM\Column(name="realizaTratamientoEfluentesPrevioVuelco", type="string", length=2, nullable=true)
     */
    private $realizaTratamientoEfluentesPrevioVuelco;

    /**
     * @var string
     *
     * @ORM\Column(name="generaBarros", type="string", length=2, nullable=true)
     */
    private $generaBarros;

    /**
     * @var string
     *
     * @ORM\Column(name="protocoloVuelcaLimitesPermitidos", type="string", length=2, nullable=true)
     */
    private $protocoloVuelcaLimitesPermitidos;

    /**
     * @var string
     *
     * @ORM\Column(name="muestreoLaboratorio", type="string", length=2, nullable=true)
     */
    private $muestreoLaboratorio;

    /**
     * @var string
     *
     * @ORM\Column(name="resultadosLaboratorioLimitesPermisibles", type="string", length=2, nullable=true)
     */
    private $resultadosLaboratorioLimitesPermisibles;

    /**
     * @var string
     *
     * @ORM\Column(name="videoInspeccionoUIT", type="string", length=2, nullable=true)
     */
    private $videoInspeccionoUIT;

    public function __construct() {
        $this->tipoResiduos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoTratamiento = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

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
     * Set proximaInspeccion
     *
     * @param \DateTime $proximaInspeccion
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setProximaInspeccion($proximaInspeccion)
    {
        $this->proximaInspeccion = $proximaInspeccion;

        return $this;
    }

    /**
     * Get proximaInspeccion
     *
     * @return \DateTime
     */
    public function getProximaInspeccion()
    {
        return $this->proximaInspeccion;
    }

    /**
     * Set inspeccionarCara
     *
     * @param integer $inspeccionarCara
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setInspeccionarCara($inspeccionarCara)
    {
        $this->inspeccionarCara = $inspeccionarCara;

        return $this;
    }

    /**
     * Get inspeccionarCara
     *
     * @return integer
     */
    public function getInspeccionarCara()
    {
        return $this->inspeccionarCara;
    }

    /**
     * Set superficieCubierta
     *
     * @param integer $superficieCubierta
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setSuperficieCubierta($superficieCubierta)
    {
        $this->superficieCubierta = $superficieCubierta;

        return $this;
    }

    /**
     * Get superficieCubierta
     *
     * @return integer
     */
    public function getSuperficieCubierta()
    {
        return $this->superficieCubierta;
    }

    /**
     * Set superficieTotal
     *
     * @param integer $superficieTotal
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setSuperficieTotal($superficieTotal)
    {
        $this->superficieTotal = $superficieTotal;

        return $this;
    }

    /**
     * Get superficieTotal
     *
     * @return integer
     */
    public function getSuperficieTotal()
    {
        return $this->superficieTotal;
    }

    /**
     * Set inscripto326
     *
     * @param integer $inscripto326
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setInscripto326($inscripto326)
    {
        $this->inscripto326 = $inscripto326;

        return $this;
    }

    /**
     * Get inscripto326
     *
     * @return integer
     */
    public function getInscripto326()
    {
        return $this->inscripto326;
    }

    /**
     * Set cantTanques
     *
     * @param integer $cantTanques
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setCantTanques($cantTanques)
    {
        $this->cantTanques = $cantTanques;

        return $this;
    }

    /**
     * Get cantTanques
     *
     * @return integer
     */
    public function getCantTanques()
    {
        return $this->cantTanques;
    }

    /**
     * Set tanquesActivos
     *
     * @param integer $tanquesActivos
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setTanquesActivos($tanquesActivos)
    {
        $this->tanquesActivos = $tanquesActivos;

        return $this;
    }

    /**
     * Get tanquesActivos
     *
     * @return integer
     */
    public function getTanquesActivos()
    {
        return $this->tanquesActivos;
    }

    /**
     * Set tanquesCegadosInertizados
     *
     * @param integer $tanquesCegadosInertizados
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setTanquesCegadosInertizados($tanquesCegadosInertizados)
    {
        $this->tanquesCegadosInertizados = $tanquesCegadosInertizados;

        return $this;
    }

    /**
     * Get tanquesCegadosInertizados
     *
     * @return integer
     */
    public function getTanquesCegadosInertizados()
    {
        return $this->tanquesCegadosInertizados;
    }

    /**
     * Set tanquesErradicados
     *
     * @param integer $tanquesErradicados
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setTanquesErradicados($tanquesErradicados)
    {
        $this->tanquesErradicados = $tanquesErradicados;

        return $this;
    }

    /**
     * Get tanquesErradicados
     *
     * @return integer
     */
    public function getTanquesErradicados()
    {
        return $this->tanquesErradicados;
    }

    /**
     * Set curl
     *
     * @param integer $curl
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setCurl($curl)
    {
        $this->curl = $curl;

        return $this;
    }

    /**
     * Get curl
     *
     * @return integer
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * Set generaEfluentesLiquidosInd
     *
     * @param integer $generaEfluentesLiquidosInd
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setGeneraEfluentesLiquidosInd($generaEfluentesLiquidosInd)
    {
        $this->generaEfluentesLiquidosInd = $generaEfluentesLiquidosInd;

        return $this;
    }

    /**
     * Get generaEfluentesLiquidosInd
     *
     * @return integer
     */
    public function getGeneraEfluentesLiquidosInd()
    {
        return $this->generaEfluentesLiquidosInd;
    }

    /**
     * Set caudalVuelcoMax
     *
     * @param integer $caudalVuelcoMax
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setCaudalVuelcoMax($caudalVuelcoMax)
    {
        $this->caudalVuelcoMax = $caudalVuelcoMax;

        return $this;
    }

    /**
     * Get caudalVuelcoMax
     *
     * @return integer
     */
    public function getCaudalVuelcoMax()
    {
        return $this->caudalVuelcoMax;
    }

    /**
     * Set horaVuelvoInicial
     *
     * @param \DateTime $horaVuelvoInicial
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setHoraVuelvoInicial($horaVuelvoInicial)
    {
        $this->horaVuelvoInicial = $horaVuelvoInicial;

        return $this;
    }

    /**
     * Get horaVuelvoInicial
     *
     * @return \DateTime
     */
    public function getHoraVuelvoInicial()
    {
        return $this->horaVuelvoInicial;
    }

    /**
     * Set horaVuelcoFinal
     *
     * @param \DateTime $horaVuelcoFinal
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setHoraVuelcoFinal($horaVuelcoFinal)
    {
        $this->horaVuelcoFinal = $horaVuelcoFinal;

        return $this;
    }

    /**
     * Get horaVuelcoFinal
     *
     * @return \DateTime
     */
    public function getHoraVuelcoFinal()
    {
        return $this->horaVuelcoFinal;
    }

    /**
     * Set tramiteEfluentesEstado
     *
     * @param integer $tramiteEfluentesEstado
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setTramiteEfluentesEstado($tramiteEfluentesEstado)
    {
        $this->tramiteEfluentesEstado = $tramiteEfluentesEstado;

        return $this;
    }

    /**
     * Get tramiteEfluentesEstado
     *
     * @return integer
     */
    public function getTramiteEfluentesEstado()
    {
        return $this->tramiteEfluentesEstado;
    }

    /**
     * Set reCirculacionAgua
     *
     * @param integer $reCirculacionAgua
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setReCirculacionAgua($reCirculacionAgua)
    {
        $this->reCirculacionAgua = $reCirculacionAgua;

        return $this;
    }

    /**
     * Get reCirculacionAgua
     *
     * @return integer
     */
    public function getReCirculacionAgua()
    {
        return $this->reCirculacionAgua;
    }

    /**
     * Set cTMMC
     *
     * @param integer $cTMMC
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setCTMMC($cTMMC)
    {
        $this->cTMMC = $cTMMC;

        return $this;
    }

    /**
     * Get cTMMC
     *
     * @return integer
     */
    public function getCTMMC()
    {
        return $this->cTMMC;
    }

    /**
     * Set realizaTratamientoEfluentesPrevioVuelco
     *
     * @param integer $realizaTratamientoEfluentesPrevioVuelco
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setRealizaTratamientoEfluentesPrevioVuelco($realizaTratamientoEfluentesPrevioVuelco)
    {
        $this->realizaTratamientoEfluentesPrevioVuelco = $realizaTratamientoEfluentesPrevioVuelco;

        return $this;
    }

    /**
     * Get realizaTratamientoEfluentesPrevioVuelco
     *
     * @return integer
     */
    public function getRealizaTratamientoEfluentesPrevioVuelco()
    {
        return $this->realizaTratamientoEfluentesPrevioVuelco;
    }

    /**
     * Set generaBarros
     *
     * @param integer $generaBarros
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setGeneraBarros($generaBarros)
    {
        $this->generaBarros = $generaBarros;

        return $this;
    }

    /**
     * Get generaBarros
     *
     * @return integer
     */
    public function getGeneraBarros()
    {
        return $this->generaBarros;
    }

    /**
     * Set protocoloVuelcaLimitesPermitidos
     *
     * @param integer $protocoloVuelcaLimitesPermitidos
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setProtocoloVuelcaLimitesPermitidos($protocoloVuelcaLimitesPermitidos)
    {
        $this->protocoloVuelcaLimitesPermitidos = $protocoloVuelcaLimitesPermitidos;

        return $this;
    }

    /**
     * Get protocoloVuelcaLimitesPermitidos
     *
     * @return integer
     */
    public function getProtocoloVuelcaLimitesPermitidos()
    {
        return $this->protocoloVuelcaLimitesPermitidos;
    }

    /**
     * Set muestreoLaboratorio
     *
     * @param integer $muestreoLaboratorio
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setMuestreoLaboratorio($muestreoLaboratorio)
    {
        $this->muestreoLaboratorio = $muestreoLaboratorio;

        return $this;
    }

    /**
     * Get muestreoLaboratorio
     *
     * @return integer
     */
    public function getMuestreoLaboratorio()
    {
        return $this->muestreoLaboratorio;
    }

    /**
     * Set resultadosLaboratorioLimitesPermisibles
     *
     * @param integer $resultadosLaboratorioLimitesPermisibles
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setResultadosLaboratorioLimitesPermisibles($resultadosLaboratorioLimitesPermisibles)
    {
        $this->resultadosLaboratorioLimitesPermisibles = $resultadosLaboratorioLimitesPermisibles;

        return $this;
    }

    /**
     * Get resultadosLaboratorioLimitesPermisibles
     *
     * @return integer
     */
    public function getResultadosLaboratorioLimitesPermisibles()
    {
        return $this->resultadosLaboratorioLimitesPermisibles;
    }

    /**
     * Set videoInspeccionoUIT
     *
     * @param integer $videoInspeccionoUIT
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setVideoInspeccionoUIT($videoInspeccionoUIT)
    {
        $this->videoInspeccionoUIT = $videoInspeccionoUIT;

        return $this;
    }

    /**
     * Get videoInspeccionoUIT
     *
     * @return integer
     */
    public function getVideoInspeccionoUIT()
    {
        return $this->videoInspeccionoUIT;
    }

    /**
     * Add tipoResiduo
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoResiduos $tipoResiduo
     *
     * @return ResultadosUltimaInspeccion
     */
    public function addTipoResiduo(\Establecimiento\EstablecimientoBundle\Entity\TipoResiduos $tipoResiduo)
    {
        $this->tipoResiduos[] = $tipoResiduo;

        return $this;
    }

    /**
     * Remove tipoResiduo
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoResiduos $tipoResiduo
     */
    public function removeTipoResiduo(\Establecimiento\EstablecimientoBundle\Entity\TipoResiduos $tipoResiduo)
    {
        $this->tipoResiduos->removeElement($tipoResiduo);
    }

    /**
     * Get tipoResiduos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipoResiduos()
    {
        return $this->tipoResiduos;
    }

    /**
     * Add tipoTratamiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoTratamiento $tipoTratamiento
     *
     * @return ResultadosUltimaInspeccion
     */
    public function addTipoTratamiento(\Establecimiento\EstablecimientoBundle\Entity\TipoTratamiento $tipoTratamiento)
    {
        $this->tipoTratamiento[] = $tipoTratamiento;

        return $this;
    }

    /**
     * Remove tipoTratamiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TipoTratamiento $tipoTratamiento
     */
    public function removeTipoTratamiento(\Establecimiento\EstablecimientoBundle\Entity\TipoTratamiento $tipoTratamiento)
    {
        $this->tipoTratamiento->removeElement($tipoTratamiento);
    }

    /**
     * Get tipoTratamiento
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipoTratamiento()
    {
        return $this->tipoTratamiento;
    }

    /**
     * Set estado326
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\EstadoRes326 $estado326
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setEstado326(\Establecimiento\EstablecimientoBundle\Entity\EstadoRes326 $estado326 = null)
    {
        $this->estado326 = $estado326;

        return $this;
    }

    /**
     * Get estado326
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\EstadoRes326
     */
    public function getEstado326()
    {
        return $this->estado326;
    }

    /**
     * Set tramiteEfluentes
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\TramiteEfluentes $tramiteEfluentes
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setTramiteEfluentes(\Establecimiento\EstablecimientoBundle\Entity\TramiteEfluentes $tramiteEfluentes = null)
    {
        $this->tramiteEfluentes = $tramiteEfluentes;

        return $this;
    }

    /**
     * Get tramiteEfluentes
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\TramiteEfluentes
     */
    public function getTramiteEfluentes()
    {
        return $this->tramiteEfluentes;
    }

    /**
     * Set DestinoVuelcoEfluentes
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\DestinoVuelco $destinoVuelcoEfluentes
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setDestinoVuelcoEfluentes(\Establecimiento\EstablecimientoBundle\Entity\DestinoVuelco $destinoVuelcoEfluentes = null)
    {
        $this->destinoVuelcoEfluentes = $destinoVuelcoEfluentes;

        return $this;
    }

    /**
     * Get DestinoVuelcoEfluentes
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\DestinoVuelco
     */
    public function getDestinoVuelcoEfluentes()
    {
        return $this->destinoVuelcoEfluentes;
    }

    /**
     * Set establecimiento
     *
     * @param \Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento
     *
     * @return ResultadosUltimaInspeccion
     */
    public function setEstablecimiento(\Establecimiento\EstablecimientoBundle\Entity\Establecimiento $establecimiento)
    {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return \Establecimiento\EstablecimientoBundle\Entity\Establecimiento
     */
    public function getEstablecimiento()
    {
        return $this->establecimiento;
    }
}
