<?php

namespace Inspecciones\InspeccionesBundle\Repository;

/**
 * CircuitoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CircuitoRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllCircuitos()
    {
	    $qb = $this->createQueryBuilder('c');        
        $qb->orderBy('c.circuito', 'ASC');
        return $qb;
	}

	public function selectCircuitoTabla()
    {
		$em=$this->getEntityManager();
		$dql="SELECT c.circuito FROM InspeccionesInspeccionesBundle:Circuito c";
    	$consulta=$em->createQuery($dql);	

        return $consulta->getResult();
    }
}
