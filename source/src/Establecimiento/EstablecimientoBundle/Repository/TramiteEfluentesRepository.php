<?php

namespace Establecimiento\EstablecimientoBundle\Repository;

/**
 * tramiteEfluentesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TramiteEfluentesRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllTramiteEfluentes()
    {
	    $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.tramiteEfluentes', 'ASC');        
        return $qb;
	}
}
