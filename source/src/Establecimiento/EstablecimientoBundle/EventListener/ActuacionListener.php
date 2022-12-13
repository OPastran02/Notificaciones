<?php

namespace Establecimiento\EstablecimientoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events; 
use Symfony\Component\DependencyInjection\ContainerInterface;

use Establecimiento\EstablecimientoBundle\Entity\Actuacion;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

class ActuacionListener
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

        if ($entity instanceof Actuacion) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            $entity->setIdUsuarioCreador($usuario->getId()); 

            $eventManager = $entityManager->getEventManager();
            
            if($eventManager) {
                $eventManager->removeEventListener(
                    [Events::prePersist], 
                    $this
                );
                if(!is_null($entity->getEstablecimiento()->getId()) ){
                    $historial = new Historial();
                    $historial->setIdTabla($entity->getEstablecimiento()->getId());
                    $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity->getEstablecimiento()))->getName());
                    $historial->setCampo("Actuacion");
                    $historial->setValorAnterior('CODEDGCONTANUEVO01');
                    $historial->setValorNuevo($entity->__toString());
                    $historial->setUsuarioMotificador($usuario);
                    $historial->setFecha(new \DateTime("-3 hours"));
                    $entityManager->persist($historial);
                    $entityManager->flush($historial);
                }
                $eventManager->addEventListener(
                    [Events::prePersist], 
                    $this
                );
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();        
        
        if ($entity instanceof Actuacion) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            //$entity->setIdUsuarioCreador($usuario->getId());            

            $eventManager = $entityManager->getEventManager();
            
            if($eventManager) {
                $eventManager->removeEventListener(
                    [Events::preRemove], 
                    $this
                );
                
                $historial = new Historial();
                $historial->setIdTabla($entity->getEstablecimiento()->getId());
                $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity->getEstablecimiento()))->getName());
                $historial->setCampo("Actuacion");
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