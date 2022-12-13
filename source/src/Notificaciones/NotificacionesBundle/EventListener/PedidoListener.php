<?php

namespace Notificaciones\NotificacionesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ContainerInterface;


use Notificaciones\NotificacionesBundle\Entity\Pedido;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

class PedidoListener
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

        if ($entity instanceof Pedido) {
            $securityContext = $this->container->get('security.token_storage');         
            $usuario = $securityContext->getToken()->getUser();

            $entity->setUsuarioCreador($usuario->getId());
                        
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();        

        if ($entity instanceof Pedido) {
            $securityContext = $this->container->get('security.token_storage');
            $usuario = $securityContext->getToken()->getUser();            
            
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
                    $historial->setCampo("Pedido");
                    $historial->setValorAnterior('CODEDGCONTANUEVO01');
                    $historial->setValorNuevo($entity->__toString());
                    $historial->setUsuarioMotificador($usuario);
                    $historial->setFecha(new \DateTime("-3 hours"));
                    $entityManager->persist($historial);
                    $entityManager->flush($historial);
                }
                
            }          
            
        }
    }
}

?>