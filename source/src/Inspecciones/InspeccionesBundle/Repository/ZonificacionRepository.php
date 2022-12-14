<?php

namespace Inspecciones\InspeccionesBundle\Repository;

/**
 * ZonificacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ZonificacionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllZonificacion()
    {
        $qb = $this->createQueryBuilder('z')
            ->where('z.id > 19');
        $qb->orderBy('z.id', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
