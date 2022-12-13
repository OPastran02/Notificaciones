<?php

namespace CoreBundle\Logic;

class Ruidos {
	// Resultado---
	//1 Supera
	//2 Enmascaramiento
	//3 No Supera
	// Metodo------
	// 0 sin metodo
	// 1 LM-LF < 3
	// 2 3 <= LM - LF < 10
	// 3 LM - LF >= 10

	public static function medir($em,$maximo,$l1=0,$l2=0,$l3=0,$l4=0,$l5=0,$l6=0,$residencial = false){

			$lf=null;
			$lm=null;
			$variacion=null;
			$delta=null;
			$le=null;
			$resultado = array();


			if( $l1 != 0 && $l2 != 0 && $l3 != 0 ){
				$lm = ($l1+$l2+$l3) / 3;
                $lm=round($lm,1);
			}

			if( $l4 != 0 && $l5 != 0 && $l6 != 0 ){
				$lf = ($l4+$l5+$l6) /3;
				if($residencial && ($lf+7) < $maximo){
					$maximo = $lf+7;
				}
                $lf=round($lf,1);
			}

			if( $lf && $lm){
				$variacion = $lm - $lf;
				$delta = self::buscarDelta($variacion,$em);
				$le = round($lm,1) - $delta;

				if( $variacion < 3 ){
					$resultado['RESULTADO'] = 2;
					$resultado['METODO'] = 1;
				}

				if($variacion >= 3 && $variacion < 10){
					if($le > $maximo){
						$resultado['RESULTADO'] = 1;
						$resultado['METODO'] = 2;
					}else{
						$resultado['RESULTADO'] = 3;
						$resultado['METODO'] = 2;
					}
				}

				if( $variacion >= 10 ){
					$le = $lm;
					$resultado['RESULTADO'] = ($le > $maximo) ? 1 : 3;
					$resultado['METODO'] = 3;
				}

			}else{
				if( $lm ){
					$resultado['RESULTADO'] = ($lm > $maximo) ? 1 : 3;
					$resultado['METODO'] = 0;
				}else{
					$resultado['RESULTADO'] = ($lf > $maximo) ? 1 : 3;
					$resultado['METODO'] = 0;
				}
			}

			$resultado['L1'] = $l1;
			if($l1 == 0){
				$resultado['L1'] = '';
			}
			$resultado['L2'] = $l2;
			if($l2 == 0){
				$resultado['L2'] = '';
			}
			$resultado['L3'] = $l3;
			if($l3 == 0){
				$resultado['L3'] = '';
			}
			if($lm){
				$resultado['LM'] = round($lm,1);
			}else{
				$resultado['LM'] = "";
			}
			$resultado['L4'] = $l4;
			if($l4 == 0){
				$resultado['L4'] = '';
			}
			$resultado['L5'] = $l5;
			if($l5 == 0){
				$resultado['L5'] = '';
			}
			$resultado['L6'] = $l6;
			if($l6 == 0){
				$resultado['L6'] = '';
			}
			if($lf){
				$resultado['LF'] = round($lf,1);
			}else{
				$resultado['LF'] = '';
			}
			if($variacion){
				$resultado['VARIACION'] = round($variacion,1);
			}else{
				$resultado['VARIACION'] = '';
			}
			$resultado['DELTA'] = $delta;
			if($delta == 0){
				$resultado['DELTA'] = '';
			}
			if($le){
				$resultado['LE'] = round($le,1);
			}else{
				$resultado['LE'] = '';
			}

			return $resultado;
	}

	public static function avisoAcustico($maximo,$medicion){
		if($medicion > $maximo){
			return 1;
		}else{
		 	return 0;
		}
	}

	public static function buscarDelta($variacion,$em){
		$delta = $em->findOneByVariacion(round($variacion,1));
		if($delta){
			return $delta->getDelta();
		}else{
			return 0;
		}

	}


}
