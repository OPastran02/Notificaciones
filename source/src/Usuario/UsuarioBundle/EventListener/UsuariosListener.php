<?php

namespace Usuario\UsuarioBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Usuario\UsuarioBundle\Entity\Usuarios;

class UsuariosListener
{
    protected $container;    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
       /*$entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        

        if ($entity instanceof Usuarios) {
            $entity->setUltimaConexion(new \DateTime());

            $securityContext = $this->container->get('security.token_storage');         
            $usuario = $securityContext->getToken()->getUser();
            
            $entity->setIdUsuarioModificador($usuario->getId());
            $entity->setFechaModificado(new \DateTime());

                        
        }*/
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /*$entity = $args->getEntity();
        

        if ($entity instanceof Usuarios) {
            $securityContext = $this->container->get('security.token_storage');         
            $usuario = $securityContext->getToken()->getUser();
            

            $entity->setUltimaConexion(new \DateTime());
            $entity->setFechaCreado(new \DateTime());
            $entity->setIdUsuarioCreador($usuario->getId());
            $entity->setIdUsuarioModificador($usuario->getId());
            $entity->setFechaModificado(new \DateTime());
                        
        }*/
    }
}

?>