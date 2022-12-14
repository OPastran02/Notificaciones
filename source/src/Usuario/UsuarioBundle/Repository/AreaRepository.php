<?php

namespace Usuario\UsuarioBundle\Repository;

/**
 * AreaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AreaRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllAreas($id = null)
    {
	    $qb = $this->createQueryBuilder('c');        
        
        if($id != null){            
            $qb->where('c.id = :id ')
            ->setParameter('id',$id);
        }

        $qb->orderBy('c.area', 'ASC');

        return $qb;
	}

	public function selectAreaTabla()
    {
		$em=$this->getEntityManager();
		$dql="SELECT a.area FROM UsuarioUsuarioBundle:Area a";
    	$consulta=$em->createQuery($dql);	

        return $consulta->getResult();
    }

    public function findAllAreasLaboratorio()
    {
        $qb = $this->createQueryBuilder('c')
        ->where('c.id in (19, 20, 21, 22, 23)');
        $qb->orderBy('c.area', 'ASC');
        return $qb;
    }
}
