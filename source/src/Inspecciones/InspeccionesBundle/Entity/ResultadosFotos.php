<?php

namespace Inspecciones\InspeccionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultadosFotos
 *
 * @ORM\Table(name="inspecciones_resultados_fotos")
 * @ORM\Entity(repositoryClass="Inspecciones\InspeccionesBundle\Repository\ResultadosFotosRepository")
 */
class ResultadosFotos
{
    /**
     * @ORM\ManyToOne(targetEntity="Resultados", inversedBy="fotos",cascade={"persist"})
     * @ORM\JoinColumn(name="resultado_id", referencedColumnName="id")
     */
    protected $resultados;

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
     * @ORM\Column(name="foto", type="text")
     */
    private $foto;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer")     
     */
    private $orden;


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
     * Set foto
     *
     * @param string $foto
     *
     * @return ResultadosFotos
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     *
     * @return ResultadosFotos
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set resultados
     *
     * @param \Inspecciones\InspeccionesBundle\Entity\Resultados $resultados
     *
     * @return ResultadosFotos
     */
    public function setResultados(\Inspecciones\InspeccionesBundle\Entity\Resultados $resultados = null)
    {
        $this->resultados = $resultados;

        return $this;
    }

    /**
     * Get resultados
     *
     * @return \Inspecciones\InspeccionesBundle\Entity\Resultados
     */
    public function getResultados()
    {
        return $this->resultados;
    }
}
