<?php

namespace Establecimiento\EstablecimientoBundle\Repository;

/**
 * EstablecimientoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EstablecimientoRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllByIdRubroPrincipal($idRubroPrincipal,$fecha,$area)
    {
      if($fecha){
        $fecha = " and (ultimaInspeccionEstablecimiento(e.id) between '1990-01-01' and  '".$fecha."' or ultimaInspeccionEstablecimiento(e.id) is null)";
      }else{
        $fecha = '';
      }

      if($area == 'NORTE'){
        $area = ' and d.comuna in (12,13,14,15)';
      }elseif($area == 'SUR'){
        $area = ' and d.comuna in (4,8)';
      }elseif($area == 'ESTE'){
        $area = ' and d.comuna in (1,2,3,5,6)';
      }elseif($area == 'OESTE'){
        $area = ' and d.comuna in (7,9,10,11)';
      }else{
        $area = ' ';
      }

      $query = 'SELECT DISTINCT e.id, d.lon, d.lat,e.Id_Rubro_Principal as idRubroPrincipal  from establecimiento as e
              INNER JOIN direccion as d on e.id = d.Id_Establecimiento
              WHERE (SELECT count(*) from orden_inspeccion as o INNER JOIN inspeccion as i on o.id = i.orden_inspeccion_id
                      WHERE i.fecha_programado is not null and i.fecha_inspeccion is null and (o.eliminada  = 0 or o.eliminada is null) and (o.anulada = 0 or o.anulada is null)
                    and o.establecimiento_id=e.id AND i.fecha_programado > (DATE(NOW()) - INTERVAL 1 MONTH)              
              ) < 1 AND
              ( ultimaInspeccionEstablecimiento(e.id) < (DATE(NOW()) - INTERVAL 6 MONTH) OR
                (SELECT count(*) FROM acta_utilizada as u
                  INNER JOIN acta as a on u.id = a.id
                  INNER JOIN orden_inspeccion as o on o.id = a.id_inspeccion
                  WHERE u.fechaInspeccion between (DATE(NOW()) - INTERVAL 6 MONTH) and DATE(NOW()) and o.establecimiento_id = e.id) > 1 OR 
                (SELECT count(*) FROM faja as f
                  INNER JOIN orden_inspeccion as o on o.id = f.id_inspeccion
                  WHERE f.fecha_inspeccion between (DATE(NOW()) - INTERVAL 6 MONTH) and DATE(NOW()) and o.establecimiento_id = e.id ) > 1
                )
              AND e.Id_Rubro_Principal = '.$idRubroPrincipal.$fecha.$area;

      $stmt = $this->getEntityManager()->getConnection()->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll();
	
	}	

  public function listProgramacionVencida($idArea,$fecha){
     $date = new \DateTime();

     if($fecha){
        $fecha = " and (ultimaInspeccionEstablecimiento(e.id) between '1990-01-01' and  '".$fecha."' or ultimaInspeccionEstablecimiento(e.id) is null)";
      }else{
        $fecha = '';
      }

      if($idArea == 2){
        $idArea = 'r.proximaInspeccion between "1990-01-01" and  NOW() and d.comuna in (12,13,14,15)';
      }elseif($idArea == 1){
        $idArea = 'r.proximaInspeccion between "1990-01-01" and  NOW() and d.comuna in (4,8)';
      }elseif($idArea == 3){
        $idArea = 'r.proximaInspeccion between "1990-01-01" and  NOW() and d.comuna in (1,2,3,5,6)';
      }elseif($idArea == 4){
        $idArea = 'r.proximaInspeccion between "1990-01-01" and  NOW() and d.comuna in (7,9,10,11)';
      }else{
        $idArea = 'r.proximaInspeccion between "1990-01-01" and  NOW()';
      }

      $query = 'SELECT DISTINCT e.id, d.lon, d.lat from establecimiento as e
                INNER JOIN direccion as d on e.id = d.Id_Establecimiento
                INNER JOIN resultados_ultima_inspeccion as r on e.id = r.establecimiento_id
                where '.$idArea.$fecha;

      $stmt = $this->getEntityManager()->getConnection()->prepare($query);      
      $stmt->execute();
      return $stmt->fetchAll();
  }

  public function listReInspeccionarVinculada($idArea){
    $qb = $this->createQueryBuilder('e')
    ->select('DISTINCT i.id, d.lon, d.lat,i.idSap,i.checklist')
    ->join('e.direcciones','d')
    ->join('e.inspecciones','i')    
    ->where('i.reinspeccionar = 1 and i.area = :area')
    ->setParameter('area', $idArea);

    return $qb->getQuery()->getResult();  
  }

  public function listReProgramadaVinculada($idArea){
    $qb = $this->createQueryBuilder('e')
    ->select('DISTINCT o.id, d.lon, d.lat,o.idSap,o.checklist')
    ->join('e.direcciones','d')
    ->join('e.inspecciones','o')
    ->join('o.inspecciones','i')
    ->where('o.area = :area and (o.completa = 0 or o.completa is null) and (o.anulada = 0 or o.anulada is null) and 
        (o.eliminada = 0 or o.eliminada is null) and DATE_DIFF(CURRENT_DATE(),o.primerFechaProgramado) <= 30 and i.fechaInspeccion is null and DATE_DIFF(CURRENT_DATE(),i.fechaProgramado) >=7 ')
    ->setParameter('area', $idArea);    

    return $qb->getQuery()->getResult();  
  }

  public function buscarPorSMP($direccion){
        $establecimiento=$direccion->getEstablecimiento();
        

        if(!$establecimiento){
          $idEstablecimiento = 0;
        }else{
          $idEstablecimiento = $establecimiento->getId();
        }

        if($idEstablecimiento = ''){
          $idEstablecimiento = 0;
        }

        $qb = $this->createQueryBuilder('e')        
        ->join('e.direcciones','d')
        ->where('TRIM(d.sMP) = TRIM(:SMP) and e.id <> :establecimiento')
        ->setParameter('SMP',$direccion->getSMP())
        ->setParameter('establecimiento',$idEstablecimiento);

        return $qb->getQuery()->getResult();
    }

    public function unirEstablecimiento($id, $id2){
	    if(is_numeric($id) and is_numeric($id2)){
            $query = 'update actuacion set Id_Establecimiento = '.$id.' where Id_Establecimiento = '.$id2.';';
            $query .= 'update direccion set Id_Establecimiento = '.$id.' where Id_Establecimiento = '.$id2.';';
            $query .= 'delete from establecimientos_razonessociales where establecimiento_id = '.$id2.';';
            $query .= 'update establecimientos_razonessociales set establecimiento_id = '.$id.' where establecimiento_id = '.$id2.';';
            $query .= 'delete from establecimientos_rubros where establecimiento_id = '.$id2.';';
            $query .= 'update establecimientos_rubros set establecimiento_id = '.$id.' where establecimiento_id = '.$id2.';';
            $query .= 'update notificacion set establecimiento_id = '.$id.' where establecimiento_id = '.$id2.';';
            $query .= 'update orden_inspeccion set establecimiento_id = '.$id.' where establecimiento_id = '.$id2.';';
            $query .= 'delete from resultadosultimainspeccion_tipotratamiento where resultadosUltimaInspeccion_id = (select id from resultados_ultima_inspeccion where establecimiento_id = '.$id2.');';
            $query .= 'delete from resultadosultimainspeccion_tiporesiduos where resultadosUltimaInspeccion_id = (select id from resultados_ultima_inspeccion where establecimiento_id = '.$id2.');';
            $query .= 'delete from resultados_ultima_inspeccion where establecimiento_id = '.$id2.';';
            $query .= 'delete from establecimiento where id = '.$id2.';';

            $stmt = $this->getEntityManager()->getConnection()->prepare($query);
            $stmt->execute();
            return $stmt->errorCode();
        }else{
	        return 'cualquier cosa';
        }
	}

}
