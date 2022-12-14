<?php

namespace Notificaciones\NotificacionesBundle\Repository;

/**
 * NotificacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificacionRepository extends \Doctrine\ORM\EntityRepository
{
	public function notificacionesCercanas($lon, $lat,$cuadras){		
		$qb = $this->createQueryBuilder('n')
		->where('SQRT((n.lon - :lon)*(n.lon - :lon) + (n.lat - :lat)*(n.lat - :lat)) <= (0.00109513 * :cuadras) and ((n.fechaVueltaFirma is not null and n.notificador is null and n.estado <> 4) or n.estado = :estado)')
		->setParameter('lon', $lon)
		->setParameter('lat', $lat)
		->setParameter('estado', 2)
		->setParameter('cuadras', $cuadras)    	
    	;    	
             
    	return $qb->getQuery()->getResult();	
	}

	public function listNotificaciones(){
		$qb = $this->createQueryBuilder('n')
		->where('n.fechaVueltaFirma is not null and n.notificador is null')		
    	;
             
    	return $qb->getQuery()->getResult();	
	}

	public function listEstablecimientosConCedulaVencida($tipoCedula,$nocturno,$fecha,$idArea){
		if($fecha){
	        $fecha = " and (ultimaInspeccionEstablecimiento(e.id) between '1990-01-01' and  '".$fecha."' or  ultimaInspeccionEstablecimiento(e.id) is null)";
	    }else{
	        $fecha = '';
	    }
	    if($nocturno == 1){
	    	$nocturno = ' and n.nocturnidad = 1';
	    }else{
	    	$nocturno = ' and (n.nocturnidad = 0 or n.nocturnidad is null)';
	    }

	    if($idArea == 2){
	        $idArea = ' and d.comuna in (12,13,14,15)';
	    }elseif($idArea == 1){
	        $idArea = ' and d.comuna in (4,8)';
	    }elseif($idArea == 3){
	        $idArea = ' and d.comuna in (1,2,3,5,6)';
	    }elseif($idArea == 4){
	        $idArea = ' and d.comuna in (7,9,10,11)';
	    }else{
	        $idArea = '';
	    }


	    $query = 'SELECT DISTINCT e.id, d.lon, d.lat,c.tipo_id as idRubroPrincipal from notificacion as n
	    			INNER JOIN cedula as c on n.id = c.id
	    			INNER JOIN establecimiento as e on n.establecimiento_id = e.id
	                INNER JOIN direccion as d on e.id = d.Id_Establecimiento
	                where (SELECT count(*) from orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id
                      WHERE i.fecha_programado is not null and i.fecha_inspeccion is null and (o.eliminada  = 0 or o.eliminada is null) and (o.anulada = 0 or o.anulada is null)
                    and o.establecimiento_id=e.id AND i.fecha_programado > (DATE(NOW()) - INTERVAL 1 MONTH)              
              		) < 1 AND 
	                n.estado_id = 5 and c.tipo_id = '.$tipoCedula.$nocturno.$fecha.$idArea;


	    $stmt = $this->getEntityManager()->getConnection()->prepare($query);
	    $stmt->execute();
	    return $stmt->fetchAll();
	}

	public function ultimaCedulasPorEstablecimiento($idEstablecimiento){
		$qb = $this->createQueryBuilder('n')
		->join('n.cedula','c')
		->where('n.establecimiento = :establecimiento and c.numero is not null')
		->setParameter('establecimiento',$idEstablecimiento)
		->orderBy('n.fechaNotificacion','DESC');
		return $qb->getQuery()->getResult();
	}

	public function notificacionesxEstablecimiento($idEstablecimiento)
    {
        $qb = $this->createQueryBuilder('n')
        ->where('n.establecimiento = :idEstablecimiento')
        ->setParameter('idEstablecimiento', $idEstablecimiento)
        ;

        return  $qb->getQuery()->getResult();
    }

    public function notificacionxId($id)
    {
        $qb = $this->createQueryBuilder('n')        
        ->where('n.id = :idNot')
        ->setParameter('idNot', $id)
        ;

        return  $qb->getQuery()->getSingleResult();
    } 
}
