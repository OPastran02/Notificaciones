<?php

namespace Faltas\FaltasBundle\Repository;

/**
 * MotivoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActaMotivoRepository extends \Doctrine\ORM\EntityRepository
{
	public function selectMotivosTabla()
    {
		$em=$this->getEntityManager();
		$dql="SELECT e.motivo FROM FaltasFaltasBundle:ActaMotivo e";

    	$consulta=$em->createQuery($dql);	

        return $consulta->getResult();

    }

    public function findAllMotivos()
    {
	    $qb = $this->createQueryBuilder('t');        
        $qb->orderBy('t.motivo', 'ASC');
                 
        //return $qb->getQuery()->getResult();
        return $qb;
	}
}
