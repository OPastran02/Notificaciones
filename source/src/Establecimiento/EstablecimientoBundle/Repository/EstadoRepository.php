<?php

namespace Establecimiento\EstablecimientoBundle\Repository;

/**
 * EstadoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EstadoRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllEstados()
    {
	    $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.estado', 'ASC');
        return $qb;
	}

	public function selectEstadosTabla()
    {
		$em=$this->getEntityManager();
		$dql="SELECT e.estado FROM EstablecimientoEstablecimientoBundle:Estado e";

    	$consulta=$em->createQuery($dql);	

        return $consulta->getResult();

    }
}
