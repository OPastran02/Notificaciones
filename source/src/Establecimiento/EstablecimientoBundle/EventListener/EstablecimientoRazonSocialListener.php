<?php

namespace Establecimiento\EstablecimientoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Establecimiento\EstablecimientoBundle\Entity\EstablecimientoRazonSocial;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

class EstablecimientoRazonSocialListener
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

        if ($entity instanceof EstablecimientoRazonSocial) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
            $entity->setIdUsuariocreador($usuario->getId());            

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
                    $historial->setCampo("RazonSocial");
                    $historial->setValorAnterior('CODEDGCONTANUEVO01');
                    $historial->setValorNuevo($entity->getRazonSocial()->__toString());
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

        if ($entity instanceof EstablecimientoRazonSocial) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();                        
            

            $eventManager = $entityManager->getEventManager();
            
            if($eventManager) {
                $eventManager->removeEventListener(
                    [Events::preRemove], 
                    $this
                );
                
                $historial = new Historial();
                $historial->setIdTabla($entity->getEstablecimiento()->getId());
                $historial->setTabla($className = $entityManager->getClassMetadata(get_class($entity->getEstablecimiento()))->getName());
                $historial->setCampo("RazonSocial");
                $historial->setValorAnterior($entity->getRazonSocial()->__toString());
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