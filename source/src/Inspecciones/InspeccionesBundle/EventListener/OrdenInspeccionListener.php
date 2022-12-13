<?php

namespace Inspecciones\InspeccionesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Establecimiento\EstablecimientoBundle\Entity\Historial;
use Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion;

class OrdenInspeccionListener
{
    protected $container;    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof OrdenInspeccion) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            $entity->setIdUsuarioCreador($usuario->getId());
            $entity->setIdUsuarioModificador($usuario->getId());            
            
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();        

        if ($entity instanceof OrdenInspeccion) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            if($usuario){

                $eventManager = $entityManager->getEventManager();
                
                if($eventManager) {
                    $eventManager->removeEventListener(
                        [Events::postPersist], 
                        $this
                    );
                    if(!is_null($entity) ){
                        $historial = new Historial();
                        $historial->setIdTabla($entity->getId());
                        $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity))->getName());
                        $historial->setCampo("OrdenInspeccion");
                        $historial->setValorAnterior('CODEDGCONTANUEVO01');
                        $historial->setValorNuevo($entity->__toString());                    
                        $historial->setUsuarioMotificador($usuario);                                            
                        $historial->setFecha(new \DateTime("-3 hours"));
                        $entityManager->persist($historial);
                        $entityManager->flush($historial);
                    }

                    $eventManager->addEventListener(
                        [Events::postPersist], 
                        $this
                    );
                    
                }
            }
            
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
       /* $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $uow = $entityManager->getUnitOfWork();

        if ($entity instanceof OrdenInspeccion) {
        	$securityContext = $this->container->get('security.token_storage');        	
        	$usuario = $securityContext->getToken()->getUser();

            if($usuario){

                if(gettype($usuario) != 'string'){
                    $entity->setIdUsuarioModificador($usuario->getId());    
                
                

                    $cambios = $uow->getEntityChangeSet($entity);

                    $eventManager = $entityManager->getEventManager();

                    if($eventManager) {
                        $eventManager->removeEventListener(
                            [Events::preUpdate], 
                            $this
                        );
                        foreach ($cambios as $key => $cambio) {                    
                            if(gettype($cambio) == 'array'){
                                if($cambio[0] instanceof \DateTime){
                                    $cambio[0] = $cambio[0]->format('Y-m-d H:i:s');
                                }
                                if($cambio[1] instanceof \DateTime){
                                    $cambio[1] = $cambio[1]->format('Y-m-d H:i:s');
                                }
                                if($cambio[0] != $cambio[1]){
                                    $historial = new Historial();
                                    $historial->setIdTabla($entity->getId());
                                    $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity))->getName());
                                    $historial->setCampo($key);
                                    if(is_null($cambio[0]) ){
                                        $historial->setValorAnterior("vacío");
                                    }else{
                                        $historial->setValorAnterior($cambio[0]);
                                    }
                                    if(is_null($cambio[1]) ){
                                        $historial->setValorNuevo("vacío");
                                    }else{
                                        $historial->setValorNuevo($cambio[1]);
                                    }
                                    $historial->setUsuarioMotificador($usuario);
                                    //$historial->setFecha(new \DateTime("-3 hours"));
                                    $entityManager->persist($historial);
                                    $entityManager->flush($historial);                            
                                }
                                
                            }                    
                        }

                        /*$eventManager->addEventListener(
                            [Events::preUpdate], 
                            $this
                        );*/
                   /* }

                }
            }
                        
        }*/
    }  
}

?>