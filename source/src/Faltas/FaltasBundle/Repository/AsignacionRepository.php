<?php

namespace Faltas\FaltasBundle\Repository;

/**
 * AsignacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AsignacionRepository extends \Doctrine\ORM\EntityRepository
{
	public function findBySerieAndNumero($Serie,$Numero)
    {

    	$em=$this->getEntityManager();

    	$dql="SELECT a.serie, a.numero
    		FROM FaltasFaltasBundle:Acta a
    		WHERE a.serie=:Serie and a.numero=:Numero";

    	$consulta=$em->createQuery($dql);
    	$consulta->setParameter('Serie',$Serie);
    	$consulta->setParameter('Numero',$Numero);		
	    
	    $count=$consulta->getResult();

	    if(!empty($count)){
	    	return 1;
	    }else{
	    	return 0;
	    }
	}

	public function findNombreApellidobyIdActa($acta)
    {
    	$em=$this->getEntityManager();

    	$dql="SELECT s.id,s.fecha,CONCAT(u.apellido,', ',u.nombre) AS Nombre
    		FROM FaltasFaltasBundle:AsignacionActa s
    		JOIN s.inspector u
    		JOIN s.acta a
    		WHERE a.id=:acta 
    		ORDER BY s.fecha DESC";

    	$consulta=$em->createQuery($dql);
    	$consulta->setParameter('acta',$acta);		
	    
        return $consulta->getResult();
	}
}
