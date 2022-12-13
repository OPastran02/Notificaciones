<?php

namespace Establecimiento\EstablecimientoBundle\Repository;

/**
 * RubroPrincipalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RubroPrincipalRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllRubroPrincipal()
    {
	    $qb = $this->createQueryBuilder('r');  
        $qb->orderBy('r.rubroPrincipal', 'ASC');        
        return $qb;
	}

	public function findAllRubroPrincipalArray()
    {
	    $qb = $this->createQueryBuilder('r')
	    ->select('r.id, r.rubroPrincipal');  
        $qb->orderBy('r.rubroPrincipal', 'ASC');        
        return $qb->getQuery()->getResult();
	}

	public function selectRubrosTabla()
    {
		$em=$this->getEntityManager();
		$dql="SELECT r.rubroPrincipal FROM EstablecimientoEstablecimientoBundle:RubroPrincipal r";

    	$consulta=$em->createQuery($dql);	

        return $consulta->getResult();

    }
}