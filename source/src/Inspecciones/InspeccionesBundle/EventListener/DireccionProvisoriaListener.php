<?php

namespace Inspecciones\InspeccionesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;  

use Symfony\Component\DependencyInjection\ContainerInterface;

use Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

class DireccionProvisoriaListener
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

        if ($entity instanceof DireccionProvisoria) {
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

        if ($entity instanceof DireccionProvisoria) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            $eventManager = $entityManager->getEventManager();
            
            if($eventManager) {
                $eventManager->removeEventListener(
                    [Events::postPersist], 
                    $this
                );
                if(!is_null($entity->getOrdenInspeccion()->getId()) ){
                    $historial = new Historial();
                    $historial->setIdTabla($entity->getOrdenInspeccion()->getId());
                    $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity->getOrdenInspeccion()))->getName());
                    $historial->setCampo("DireccionProvisoria");
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

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DireccionProvisoria) {
        	$securityContext = $this->container->get('security.token_storage');        	
        	$usuario = $securityContext->getToken()->getUser();

            $entity->setIdUsuarioModificador($usuario->getId());
                        
        }
    } 

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();        

        if ($entity instanceof DireccionProvisoria) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            

            $eventManager = $entityManager->getEventManager();
            
            if($eventManager) {
                $eventManager->removeEventListener(
                    [Events::preRemove], 
                    $this
                );
                
                $historial = new Historial();
                $historial->setIdTabla($entity->getOrdenInspeccion()->getId());
                $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity->getOrdenInspeccion()))->getName());
                $historial->setCampo("DireccionProvisoria");
                $historial->setValorAnterior($entity->__toString());
                $historial->setValorNuevo('Eliminado');
                $historial->setUsuarioMotificador($usuario);
                $historial->setFecha(new \DateTime("-3 hours"));
                $entityManager->persist($historial);
                $entityManager->flush($historial);               

                $eventManager->addEventListener(
                    [Events::preRemove], 
                    $this
                );
                
            }
        }
    }
}

?>