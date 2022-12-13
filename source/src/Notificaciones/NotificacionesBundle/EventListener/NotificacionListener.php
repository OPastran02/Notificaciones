<?php

namespace Notificaciones\NotificacionesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;  

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Notificaciones\NotificacionesBundle\Entity\Notificacion;
use Establecimiento\EstablecimientoBundle\Entity\Historial;

class NotificacionListener
{
    protected $container;    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $uow = $entityManager->getUnitOfWork();

        if ($entity instanceof Notificacion) {
            $securityContext = $this->container->get('security.token_storage');         
            $usuario = $securityContext->getToken()->getUser();

            $entity->setUsuarioModificador($usuario->getId());

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

                $eventManager->addEventListener(
                    [Events::preUpdate], 
                    $this
                );
                
            }
                        
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Notificacion) {
            $securityContext = $this->container->get('security.token_storage');         
            $usuario = $securityContext->getToken()->getUser();

            $entity->setUsuarioModificador($usuario->getId());
                        
        }
    }
}

?>