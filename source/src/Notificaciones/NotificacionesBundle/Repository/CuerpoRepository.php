<?php

namespace Notificaciones\NotificacionesBundle\Repository;

/**
 * CuerpoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CuerpoRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllCuerpos()
    {
	    $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.nombre', 'ASC');
                 
        return $qb->getQuery()->getResult();
        //return $qb;
	}

	public function findAllCuerposForm()
    {
	    $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.nombre', 'ASC');
                 
        return $qb;
        //return $qb;
	}
}
