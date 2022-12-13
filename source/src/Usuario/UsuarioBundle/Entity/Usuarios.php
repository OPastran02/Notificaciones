<?php

namespace Usuario\UsuarioBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="Usuario\UsuarioBundle\Repository\UsuariosRepository")
 * @UniqueEntity("usuario")
 * @ORM\HasLifecycleCallbacks()
 */ 
class Usuarios implements UserInterface
{
  /**
   * @var string
   *
   * @Assert\NotBlank()
   * @ORM\Column(name="access_token", type="text")
   */
  private $accessToken;

  /**
   * @var string
   *
   * @Assert\NotBlank()
   * @ORM\Column(name="access_token_apk", type="text")
   */
  private $access_token_apk;

    /**
     * @ORM\ManyToMany(targetEntity="TipoUsuario")        
     * @ORM\JoinTable(name="usuarios_tipousuarios",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tipo_usuario", referencedColumnName="id")}
     *      )
     */
    protected $tipoUsuario;

    /**
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumn(name="idArea", referencedColumnName="id")
     */
    protected $area;

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
     * @Assert\NotBlank()
     * @ORM\Column(name="usuario", type="string", length=15, unique=true)
     */
    private $usuario;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="nombre", type="string", length=25)
     */
    private $nombre;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="apellido", type="string", length=25)
     */
    private $apellido;


    /**
     * @var int
     *
     * @ORM\Column(name="sistemaNotificaciones", type="smallint")
     */
    private $sistemaNotificaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="idArea", type="smallint")
     */
    private $idArea;

    /**
     * @var int
     *
     * @ORM\Column(name="pedidos", type="smallint")
     */
    private $pedidos;

    /**
     * @var int
     *
     * @ORM\Column(name="establecimientos", type="smallint")
     */
    private $establecimientos;

    /**
     * @var int
     *
     * @ORM\Column(name="inbox", type="smallint")
     */
    private $inbox;

    /**
     * @var int
     *
     * @ORM\Column(name="antecedentes", type="smallint")
     */
    private $antecedentes;

    /**
     * @var int
     *
     * @ORM\Column(name="programacion", type="smallint")
     */
    private $programacion;

    /**
     * @var int
     *
     * @ORM\Column(name="documentacion", type="smallint")
     */
    private $documentacion;

    /**
     * @var int
     *
     * @ORM\Column(name="actasYFajas", type="smallint")
     */
    private $actasYFajas;

    /**
     * @var int
     *
     * @ORM\Column(name="cargaMasivaCedulas", type="smallint")
     */
    private $cargaMasivaCedulas;

    /**
     * @var int
     *
     * @ORM\Column(name="ipUsuario", type="string", length=17, nullable=true)
     */
    private $ipUsuario;

    /**
     * @var int
     *
     * @ORM\Column(name="nivelTablet", type="smallint")
     */
    private $nivelTablet;

    /**
     * @var int
     *
     * @ORM\Column(name="nivelPatrulla", type="smallint")
     */
    private $nivelPatrulla;

    /**
     * @var int
     *
     * @ORM\Column(name="rni", type="smallint")
     */
    private $rni;

    /**
     * @var bool
     *
     * @ORM\Column(name="habilitado", type="boolean")
     */
    private $habilitado;
    
    /**
     * @var DateTime
     *
     * @ORM\Column(name="ultimaConexion", type="date")
     */
    private $UltimaConexion;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="Fecha_Creado", type="date")
     */
    private $fechaCreado;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Creador", type="integer")
     */
    private $idUsuarioCreador;

    /**
     * @var \DateTime
     *     
     * @Assert\Date()
     * @ORM\Column(name="Fecha_Modificado", type="date")
     */
    private $fechaModificado;

    /**
     * @var int
     *     
     * @ORM\Column(name="Id_Usuario_Modificador", type="integer")
     */
    private $idUsuarioModificador;

    /**
     * @var int
     *     
     * @ORM\Column(name="laboratorio", type="integer")
     */
    private $laboratorio;


    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipoUsuario = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Usuarios
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuarios
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Usuarios
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Usuarios
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set sistemaNotificaciones
     *
     * @param integer $sistemaNotificaciones
     *
     * @return Usuarios
     */
    public function setSistemaNotificaciones($sistemaNotificaciones)
    {
        $this->sistemaNotificaciones = $sistemaNotificaciones;

        return $this;
    }

    /**
     * Get sistemaNotificaciones
     *
     * @return integer
     */
    public function getSistemaNotificaciones()
    {
        return $this->sistemaNotificaciones;
    }

    /**
     * Set idArea
     *
     * @param integer $idArea
     *
     * @return Usuarios
     */
    public function setIdArea($idArea)
    {
        $this->idArea = $idArea;

        return $this;
    }

    /**
     * Get idArea
     *
     * @return integer
     */
    public function getIdArea()
    {
        return $this->idArea;
    }

    /**
     * Set pedidos
     *
     * @param integer $pedidos
     *
     * @return Usuarios
     */
    public function setPedidos($pedidos)
    {
        $this->pedidos = $pedidos;

        return $this;
    }

    /**
     * Get pedidos
     *
     * @return integer
     */
    public function getPedidos()
    {
        return $this->pedidos;
    }

    /**
     * Set establecimientos
     *
     * @param integer $establecimientos
     *
     * @return Usuarios
     */
    public function setEstablecimientos($establecimientos)
    {
        $this->establecimientos = $establecimientos;

        return $this;
    }

    /**
     * Get establecimientos
     *
     * @return integer
     */
    public function getEstablecimientos()
    {
        return $this->establecimientos;
    }

    /**
     * Set inbox
     *
     * @param integer $inbox
     *
     * @return Usuarios
     */
    public function setInbox($inbox)
    {
        $this->inbox = $inbox;

        return $this;
    }

    /**
     * Get inbox
     *
     * @return integer
     */
    public function getInbox()
    {
        return $this->inbox;
    }

    /**
     * Set antecedentes
     *
     * @param integer $antecedentes
     *
     * @return Usuarios
     */
    public function setAntecedentes($antecedentes)
    {
        $this->antecedentes = $antecedentes;

        return $this;
    }

    /**
     * Get antecedentes
     *
     * @return integer
     */
    public function getAntecedentes()
    {
        return $this->antecedentes;
    }

    /**
     * Set programacion
     *
     * @param integer $programacion
     *
     * @return Usuarios
     */
    public function setProgramacion($programacion)
    {
        $this->programacion = $programacion;

        return $this;
    }

    /**
     * Get programacion
     *
     * @return integer
     */
    public function getProgramacion()
    {
        return $this->programacion;
    }

    /**
     * Set documentacion
     *
     * @param integer $documentacion
     *
     * @return Usuarios
     */
    public function setDocumentacion($documentacion)
    {
        $this->documentacion = $documentacion;

        return $this;
    }

    /**
     * Get documentacion
     *
     * @return integer
     */
    public function getDocumentacion()
    {
        return $this->documentacion;
    }

    /**
     * Set actasYFajas
     *
     * @param integer $actasYFajas
     *
     * @return Usuarios
     */
    public function setActasYFajas($actasYFajas)
    {
        $this->actasYFajas = $actasYFajas;

        return $this;
    }

    /**
     * Get actasYFajas
     *
     * @return integer
     */
    public function getActasYFajas()
    {
        return $this->actasYFajas;
    }

    /**
     * Set cargaMasivaCedulas
     *
     * @param integer $cargaMasivaCedulas
     *
     * @return Usuarios
     */
    public function setCargaMasivaCedulas($cargaMasivaCedulas)
    {
        $this->cargaMasivaCedulas = $cargaMasivaCedulas;

        return $this;
    }

    /**
     * Get cargaMasivaCedulas
     *
     * @return integer
     */
    public function getCargaMasivaCedulas()
    {
        return $this->cargaMasivaCedulas;
    }

    /**
     * Set ipUsuario
     *
     * @param string $ipUsuario
     *
     * @return Usuarios
     */
    public function setIpUsuario($ipUsuario)
    {
        $this->ipUsuario = $ipUsuario;

        return $this;
    }

    /**
     * Get ipUsuario
     *
     * @return string
     */
    public function getIpUsuario()
    {
        return $this->ipUsuario;
    }

    /**
     * Set nivelTablet
     *
     * @param integer $nivelTablet
     *
     * @return Usuarios
     */
    public function setNivelTablet($nivelTablet)
    {
        $this->nivelTablet = $nivelTablet;

        return $this;
    }

    /**
     * Get nivelTablet
     *
     * @return integer
     */
    public function getNivelTablet()
    {
        return $this->nivelTablet;
    }

    /**
     * Set nivelPatrulla
     *
     * @param integer $nivelPatrulla
     *
     * @return Usuarios
     */
    public function setNivelPatrulla($nivelPatrulla)
    {
        $this->nivelPatrulla = $nivelPatrulla;

        return $this;
    }

    /**
     * Get nivelPatrulla
     *
     * @return integer
     */
    public function getNivelPatrulla()
    {
        return $this->nivelPatrulla;
    }

    /**
     * Set rni
     *
     * @param integer $rni
     *
     * @return Usuarios
     */
    public function setRni($rni)
    {
        $this->rni = $rni;

        return $this;
    }

    /**
     * Get rni
     *
     * @return integer
     */
    public function getRni()
    {
        return $this->rni;
    }

    /**
     * Set habilitado
     *
     * @param boolean $habilitado
     *
     * @return Usuarios
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return boolean
     */
    public function getHabilitado()
    {
        return $this->habilitado;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return Usuarios
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

    /**
     * Set idUsuarioCreador
     *
     * @param integer $idUsuarioCreador
     *
     * @return Usuarios
     */
    public function setIdUsuarioCreador($idUsuarioCreador)
    {
        $this->idUsuarioCreador = $idUsuarioCreador;

        return $this;
    }

    /**
     * Get idUsuarioCreador
     *
     * @return integer
     */
    public function getIdUsuarioCreador()
    {
        return $this->idUsuarioCreador;
    }

    /**
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return Usuarios
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Get fechaModificado
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Set idUsuarioModificador
     *
     * @param integer $idUsuarioModificador
     *
     * @return Usuarios
     */
    public function setIdUsuarioModificador($idUsuarioModificador)
    {
        $this->idUsuarioModificador = $idUsuarioModificador;

        return $this;
    }

    /**
     * Get idUsuarioModificador
     *
     * @return integer
     */
    public function getIdUsuarioModificador()
    {
        return $this->idUsuarioModificador;
    }

    /**
     * Add tipoUsuario
     *
     * @param \Usuario\UsuarioBundle\Entity\TipoUsuario $tipoUsuario
     *
     * @return Usuarios
     */
    public function addTipoUsuario(\Usuario\UsuarioBundle\Entity\TipoUsuario $tipoUsuario)
    {
        $this->tipoUsuario[] = $tipoUsuario;

        return $this;
    }

    /**
     * Remove tipoUsuario
     *
     * @param \Usuario\UsuarioBundle\Entity\TipoUsuario $tipoUsuario
     */
    public function removeTipoUsuario(\Usuario\UsuarioBundle\Entity\TipoUsuario $tipoUsuario)
    {
        $this->tipoUsuario->removeElement($tipoUsuario);
    }

    /**
     * Get tipoUsuario
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTipoUsuario()
    {
        return $this->tipoUsuario;
    }

    /**
     * Set area
     *
     * @param \Usuario\UsuarioBundle\Entity\Area $area
     *
     * @return Usuarios
     */
    public function setArea(\Usuario\UsuarioBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Usuario\UsuarioBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    public function __toString()
    {
        return $this->getApellido().', '.$this->getNombre();
    }

    function getRoles(){
        $roles=array('IS_AUTHENTICATED_ANONYMOUSLY');

        if($this->getSistemaNotificaciones()==0){
          array_push($roles,'ROLE_NOTIFICACIONES_NULL');
        }else if ($this->getSistemaNotificaciones()==1){
          array_push($roles,'ROLE_NOTIFICACIONES_VIEW');
        }else if ($this->getSistemaNotificaciones()==2){
          array_push($roles,'ROLE_NOTIFICACIONES_VIEW');
          array_push($roles,'ROLE_NOTIFICACIONES_EDIT');  
        }else if ($this->getSistemaNotificaciones()==3){
          array_push($roles,'ROLE_NOTIFICACIONES_VIEW');
          array_push($roles,'ROLE_NOTIFICACIONES_EDIT');  
          array_push($roles,'ROLE_NOTIFICACIONES_ADMIN');  
        }

        if($this->getArea()->getId()==7){
          array_push($roles,'ROLE_REPORTEDIARIO_ADMIN');
        }

        if($this->getPedidos()==0){
          array_push($roles,'ROLE_PEDIDOS_NULL');
        }else if ($this->getPedidos()==1){
          array_push($roles,'ROLE_PEDIDOS_VIEW');
        }else if ($this->getPedidos()==2){ 
          array_push($roles,'ROLE_PEDIDOS_VIEW');
          array_push($roles,'ROLE_PEDIDOS_EDIT');  
        }else if ($this->getPedidos()==3){
          array_push($roles,'ROLE_PEDIDOS_VIEW');
          array_push($roles,'ROLE_PEDIDOS_EDIT');  
          array_push($roles,'ROLE_PEDIDOS_ADMIN');  
        }


        if($this->getEstablecimientos()==0){
          array_push($roles,'ROLE_ESTABLECIMIENTO_NULL');
        }else if ($this->getEstablecimientos()==1){
          array_push($roles,'ROLE_ESTABLECIMIENTO_VIEW');
        }else if ($this->getEstablecimientos()==2){
          array_push($roles,'ROLE_ESTABLECIMIENTO_VIEW');
          array_push($roles,'ROLE_ESTABLECIMIENTO_EDIT');  
        }else if ($this->getEstablecimientos()==3){
          array_push($roles,'ROLE_ESTABLECIMIENTO_VIEW');
          array_push($roles,'ROLE_ESTABLECIMIENTO_EDIT');  
          array_push($roles,'ROLE_ESTABLECIMIENTO_ADMIN');  
        }

        if($this->getInbox()==0){
          array_push($roles,'ROLE_INBOX_NULL');
        }else if ($this->getInbox()==1){
          array_push($roles,'ROLE_INBOX_VIEW');
        }else if ($this->getInbox()==2){
          array_push($roles,'ROLE_INBOX_VIEW');
          array_push($roles,'ROLE_INBOX_EDIT');  
        }else if ($this->getInbox()==3){
          array_push($roles,'ROLE_INBOX_VIEW');
          array_push($roles,'ROLE_INBOX_EDIT');  
          array_push($roles,'ROLE_INBOX_ADMIN');  
        }

        if($this->getAntecedentes()==0){
          array_push($roles,'ROLE_ANTECEDENTES_NULL');
        }else if ($this->getAntecedentes()==1){
          array_push($roles,'ROLE_ANTECEDENTES_VIEW');
        }else if ($this->getAntecedentes()==2){
          array_push($roles,'ROLE_ANTECEDENTES_VIEW');
          array_push($roles,'ROLE_ANTECEDENTES_EDIT');  
        }else if ($this->getAntecedentes()==3){
          array_push($roles,'ROLE_ANTECEDENTES_VIEW');
          array_push($roles,'ROLE_ANTECEDENTES_EDIT');  
          array_push($roles,'ROLE_ANTECEDENTES_ADMIN');  
        }

        if($this->getProgramacion()==0){
          array_push($roles,'ROLE_PROGRAMACION_NULL');
        }else if ($this->getProgramacion()==1){
          array_push($roles,'ROLE_PROGRAMACION_VIEW');
        }else if ($this->getProgramacion()==2){
          array_push($roles,'ROLE_PROGRAMACION_VIEW');
          array_push($roles,'ROLE_PROGRAMACION_EDIT');  
        }else if ($this->getProgramacion()==3){
          array_push($roles,'ROLE_PROGRAMACION_VIEW');
          array_push($roles,'ROLE_PROGRAMACION_EDIT');  
          array_push($roles,'ROLE_PROGRAMACION_ADMIN');  
        }

        if($this->getDocumentacion()==0){
          array_push($roles,'ROLE_DOCUMENTACION_NULL');
        }else if ($this->getDocumentacion()==1){
          array_push($roles,'ROLE_DOCUMENTACION_VIEW');
        }else if ($this->getDocumentacion()==2){
          array_push($roles,'ROLE_DOCUMENTACION_VIEW');
          array_push($roles,'ROLE_DOCUMENTACION_EDIT');  
        }else if ($this->getDocumentacion()==3){
          array_push($roles,'ROLE_DOCUMENTACION_VIEW');
          array_push($roles,'ROLE_DOCUMENTACION_EDIT');  
          array_push($roles,'ROLE_DOCUMENTACION_ADMIN');  
        }

        if($this->getActasYFajas()==0){
          array_push($roles,'ROLE_ACTASFAJAS_NULL');
        }else if ($this->getActasYFajas()==1){
          array_push($roles,'ROLE_ACTASFAJAS_VIEW');
        }else if ($this->getActasYFajas()==2){
          array_push($roles,'ROLE_ACTASFAJAS_VIEW');
          array_push($roles,'ROLE_ACTASFAJAS_EDIT');  
        }else if ($this->getActasYFajas()==3){
          array_push($roles,'ROLE_ACTASFAJAS_VIEW');
          array_push($roles,'ROLE_ACTASFAJAS_EDIT');  
          array_push($roles,'ROLE_ACTASFAJAS_ADMIN');  
        }            

        if($this->getCargaMasivaCedulas()==0){
          array_push($roles,'ROLE_CARGAMASIVA_NULL');
        }else if ($this->getCargaMasivaCedulas()==1){
          array_push($roles,'ROLE_CARGAMASIVA_VIEW');
        }else if ($this->getCargaMasivaCedulas()==2){
          array_push($roles,'ROLE_CARGAMASIVA_VIEW');
          array_push($roles,'ROLE_CARGAMASIVA_EDIT');  
        }else if ($this->getCargaMasivaCedulas()==3){
          array_push($roles,'ROLE_CARGAMASIVA_VIEW');
          array_push($roles,'ROLE_CARGAMASIVA_EDIT'); 
          array_push($roles,'ROLE_CARGAMASIVA_ADMIN');  
        }

        if($this->getLaboratorio()==0){
          array_push($roles,'ROLE_LABORATORIO_NULL');
        }else if ($this->getLaboratorio()==1){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
        }else if ($this->getLaboratorio()==2){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT');  
        }else if ($this->getLaboratorio()==3){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT'); 
          array_push($roles,'ROLE_LABORATORIO_INCHARGE');  
        }else if ($this->getLaboratorio()==4){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT');
          array_push($roles,'ROLE_LABORATORIO_SUPERVISOR');
        }else if ($this->getLaboratorio()==5){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT'); 
          array_push($roles,'ROLE_LABORATORIO_MANAGER');  
        }else if ($this->getLaboratorio()==6){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT'); 
          array_push($roles,'ROLE_LABORATORIO_DIRECTOR');  
        }else if ($this->getLaboratorio()==7){
          array_push($roles,'ROLE_LABORATORIO_VIEW');
          array_push($roles,'ROLE_LABORATORIO_EDIT'); 
          array_push($roles,'ROLE_LABORATORIO_INCHARGE');
          array_push($roles,'ROLE_LABORATORIO_SUPERVISOR');
          array_push($roles,'ROLE_LABORATORIO_MANAGER');
          array_push($roles,'ROLE_LABORATORIO_DIRECTOR');  
          array_push($roles,'ROLE_LABORATORIO_ADMIN');
        }  

        return $roles;

    }

    function getUsername(){
        return $this->getUsuario();
    }

    function eraseCredentials(){
    }

    function getSalt(){
        return null;
    }

    /**
     * Set ultimaConexion
     *
     * @param \DateTime $ultimaConexion
     *
     * @return Usuarios
     */
    public function setUltimaConexion($ultimaConexion)
    {
        $this->UltimaConexion = $ultimaConexion;

        return $this;
    }

    /**
     * Get ultimaConexion
     *
     * @return \DateTime
     */
    public function getUltimaConexion()
    {
        return $this->UltimaConexion;
    }

    /**
     * Set laboratorio
     *
     * @param integer $laboratorio
     *
     * @return Usuarios
     */
    public function setLaboratorio($laboratorio)
    {
        $this->laboratorio = $laboratorio;

        return $this;
    }

    /**
     * Get laboratorio
     *
     * @return integer
     */
    public function getLaboratorio()
    {
        return $this->laboratorio;
    }

    public function setAccessToken($token){
        $this->access_token=$token;
    }
    public function getAccessToken(){
        return $this->access_token;
    }

    public function setAccessToken_apk($token){
        $this->access_token_apk=$token;
    }
    public function getAccessToken_apk(){
        return $this->access_token_apk;
    }
}
