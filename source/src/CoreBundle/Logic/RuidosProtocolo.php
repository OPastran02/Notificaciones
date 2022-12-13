<?php

namespace CoreBundle\Logic;

use CoreBundle\Logic\Ruidos;

class RuidosProtocolo {
	// Resultado---
	//1 Supera
	//2 Enmascaramiento
	//3 No Supera
	//4 sin resultados
	// Metodo------
	// 0 sin metodo
	// 1 LM-LF < 3
	// 2 3 <= LM - LF < 10
	// 3 LM - LF >= 10

	public static function matriz($ruidos,$repoZonificacion,$repoAmbientes,$repoCorrrecion,$repoEquipos){
			$m = array(); //mediciones
			$puntitos = '................';
			$zonaRecidencial = false;
			$ruidos['resultadoI'] = 4;
			$ruidos['resultadoE'] = 4;
			$ruidos['p2ResultadoI'] = 4;
			$ruidos['p2ResultadoE'] = 4;
			$ruidos['p3ResultadoI'] = 4;
			$ruidos['p3ResultadoE'] = 4;
			$ruidos['medidorMarca'] = $puntitos;
			$ruidos['medidorModelo'] = $puntitos;
			$ruidos['medidorSerie'] = $puntitos;
			$ruidos['medidorClase'] = $puntitos;
			$ruidos['calibradorMarca'] = $puntitos;			
			$ruidos['calibradorSerie'] = $puntitos;
			$ruidos['calibradorClase'] = $puntitos;


			if( array_key_exists(365,$ruidos) ){
				$medidor = $repoEquipos->findOneById($ruidos[365]['id']);
				$ruidos['medidorMarca'] = $medidor->getMarca();
				$ruidos['medidorModelo'] = $medidor->getModelo();
				$ruidos['medidorSerie'] = $medidor->getSerie();
				$ruidos['medidorClase'] = $medidor->getClase();
			}
			if( array_key_exists(366,$ruidos) ){
				$calibrador = $repoEquipos->findOneById($ruidos[366]['id']);
				$ruidos['calibradorMarca'] = $calibrador->getMarca();
				$ruidos['calibradorSerie'] = $calibrador->getSerie();
				$ruidos['calibradorClase'] = $calibrador->getClase();
			}

			///-------------------------INTERIOR---------------------------------------
			//buscar LMPS interior
			$ruidos['interiorAsaTipo'] = $puntitos;
			if( array_key_exists(372,$ruidos) && array_key_exists(373,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[373]['id']);
				$ruidos['interiorAsaTipo'] = $zonificacion->getAsatipo();

				if( ($zonificacion->getId() > 1 &&  $zonificacion->getId() < 6) || $zonificacion->getId() == 21 ||  $zonificacion->getId() == 22 ){
					$zonaRecidencial = true;
				}

				switch ($ruidos[372]['respuesta']) {
					case 'DIURNO':
						$ruidos['LmpHI'] = $zonificacion->getDiurnoHabitable();
						$ruidos['LmpSI'] = $zonificacion->getDiurnoServicio();
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['LmpHI'] = $zonificacion->getNocturnoHabitable();
						$ruidos['LmpSI'] = $zonificacion->getNocturnoServicio();
					default:
						break;
				}
			}else{				
				$ruidos['LmpHI'] = $puntitos;
				$ruidos['LmpSI'] = $puntitos;
			}

			//MEDICION 1
			if( array_key_exists(375,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[375]['id']);
				$j = 1;				
				for ($i=376; $i < 382; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["IMedicion1"] = $resultados;
				$ruidos['resultadoI'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(385,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[385]['id']);
				$j = 1;				
				for ($i=386; $i < 392; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["IMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['resultadoI']){
					$ruidos['resultadoI'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(395,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[395]['id']);
				$j = 1;				
				for ($i=396; $i < 402; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["IMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['resultadoI']){
					$ruidos['resultadoI'] = $resultados['RESULTADO'];
				}
			}



			///-------------------------EXTERIOR---------------------------------------
			//buscar LMPS exterior
			$ruidos['exteriorAsaTipo']= $puntitos;
			if( array_key_exists(409,$ruidos) && array_key_exists(410,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[410]['id']);
				$ruidos['exteriorAsaTipo'] = $zonificacion->getAsaetipo();

				switch ($ruidos[409]['respuesta']) {
					case 'DIURNO':
						$ruidos['LmpE'] = $zonificacion->getDiurnoExterior();						
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['LmpE'] = $zonificacion->getNocturnoExterior();
					default:
						break;
				}
			}else{
				$ruidos['LmpE'] = $puntitos;
			}

			//MEDICION 1
			if( array_key_exists(412,$ruidos) ){
				$j = 1;
				for ($i=413; $i < 419; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				
				
				$ruidos["EMedicion1"] = $resultados;
				$ruidos['resultadoE'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(422,$ruidos) ){
				$j = 1;
				for ($i=423; $i < 429; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["EMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['resultadoE']){
					$ruidos['resultadoE'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(432,$ruidos) ){
				$j = 1;
				for ($i=433; $i < 439; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["EMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['resultadoE']){
					$ruidos['resultadoE'] = $resultados['RESULTADO'];
				}
			}


			///-------------------------AVISO ACUSTICO-------------------------------------

			$ruidos["AvisoAcusticoSupera"] = -1;

			if( array_key_exists(404,$ruidos) ){
				$ruidos["AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[404]['respuesta']);
			}

			if( array_key_exists(406,$ruidos) && $ruidos["AvisoAcusticoSupera"] != 1 ){
				$ruidos["AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[406]['respuesta']);
			}			



			//------------------------------------------------- PROTOCOLO 2 ------------------------------------

			
			///-------------------------INTERIOR---------------------------------------
			//buscar LMPS interior
			$ruidos['p2InteriorAsaTipo'] = $puntitos;
			if( array_key_exists(641,$ruidos) && array_key_exists(643,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[643]['id']);
				$ruidos['p2InteriorAsaTipo'] = $zonificacion->getAsatipo();

				if( ($zonificacion->getId() > 1 &&  $zonificacion->getId() < 6) || $zonificacion->getId() == 21 ||  $zonificacion->getId() == 22 ){
					$zonaRecidencial = true;
				}else{
					$zonaRecidencial = false;
				}

				switch ($ruidos[641]['respuesta']) {
					case 'DIURNO':
						$ruidos['p2LmpHI'] = $zonificacion->getDiurnoHabitable();
						$ruidos['p2LmpSI'] = $zonificacion->getDiurnoServicio();
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['p2LmpHI'] = $zonificacion->getNocturnoHabitable();
						$ruidos['p2LmpSI'] = $zonificacion->getNocturnoServicio();
					default:
						break;
				}
			}else{				
				$ruidos['p2LmpHI'] = $puntitos;
				$ruidos['p2LmpSI'] = $puntitos;
			}


			//MEDICION 1
			if( array_key_exists(445,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[445]['id']);
				$j = 1;				
				for ($i=446; $i < 452; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p2IMedicion1"] = $resultados;
				$ruidos['p2ResultadoI'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(455,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[455]['id']);
				$j = 1;				
				for ($i=456; $i < 462; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p2IMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p2ResultadoI']){
					$ruidos['p2ResultadoI'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(465,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[465]['id']);				
				$j = 1;				
				for ($i=466; $i < 472; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p2IMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p2ResultadoI']){
					$ruidos['p2ResultadoI'] = $resultados['RESULTADO'];
				}
			}

			///-------------------------EXTERIOR---------------------------------------
			//buscar LMPS exterior
			$ruidos['p2ExteriorAsaTipo']= $puntitos;
			if( array_key_exists(642,$ruidos) && array_key_exists(644,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[644]['id']);
				$ruidos['p2ExteriorAsaTipo'] = $zonificacion->getAsaetipo();

				switch ($ruidos[642]['respuesta']) {
					case 'DIURNO':
						$ruidos['p2LmpE'] = $zonificacion->getDiurnoExterior();						
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['p2LmpE'] = $zonificacion->getNocturnoExterior();
					default:
						break;
				}
			}else{
				$ruidos['p2LmpE'] = $puntitos;
			}

			//MEDICION 1
			if( array_key_exists(482,$ruidos) ){
				$j = 1;
				for ($i=483; $i < 489; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				
				
				$ruidos["p2EMedicion1"] = $resultados;
				$ruidos['p2ResultadoE'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(508,$ruidos) ){
				$j = 1;
				for ($i=509; $i < 515; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["p2EMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p2ResultadoE']){
					$ruidos['p2ResultadoE'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(556,$ruidos) ){
				$j = 1;
				for ($i=557; $i < 563; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["p2EMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p2ResultadoE']){
					$ruidos['p2ResultadoE'] = $resultados['RESULTADO'];
				}
			}

			///-------------------------AVISO ACUSTICO-------------------------------------

			$ruidos["p2AvisoAcusticoSupera"] = -1;

			if( array_key_exists(474,$ruidos) ){
				$ruidos["p2AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[474]['respuesta']);
			}

			if( array_key_exists(476,$ruidos) && $ruidos["p2AvisoAcusticoSupera"] != 1 ){
				$ruidos["p2AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[476]['respuesta']);
			}

				


			//------------------------------------------------- PROTOCOLO 3 ------------------------------------

			
			///-------------------------INTERIOR---------------------------------------
			//buscar LMPS interior
			$ruidos['p3InteriorAsaTipo'] = $puntitos;
			if( array_key_exists(645,$ruidos) && array_key_exists(647,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[647]['id']);
				$ruidos['p3InteriorAsaTipo'] = $zonificacion->getAsatipo();

				if( ($zonificacion->getId() > 1 &&  $zonificacion->getId() < 6) || $zonificacion->getId() == 21 ||  $zonificacion->getId() == 22 ){
					$zonaRecidencial = true;
				}else{
					$zonaRecidencial = false;
				}

				switch ($ruidos[645]['respuesta']) {
					case 'DIURNO':
						$ruidos['p3LmpHI'] = $zonificacion->getDiurnoHabitable();
						$ruidos['p3LmpSI'] = $zonificacion->getDiurnoServicio();
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['p3LmpHI'] = $zonificacion->getNocturnoHabitable();
						$ruidos['p3LmpSI'] = $zonificacion->getNocturnoServicio();
					default:
						break;
				}
			}else{				
				$ruidos['p3LmpHI'] = $puntitos;
				$ruidos['p3LmpSI'] = $puntitos;
			}


			//MEDICION 1
			if( array_key_exists(569,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[569]['id']);
				$j = 1;				
				for ($i=570; $i < 576; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p3IMedicion1"] = $resultados;
				$ruidos['p3ResultadoI'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(579,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[579]['id']);
				$j = 1;				
				for ($i=580; $i < 586; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p3IMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p3ResultadoI']){
					$ruidos['p3ResultadoI'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(589,$ruidos) ){
				$ambiente = $repoAmbientes->findOneById($ruidos[589]['id']);
				$j = 1;				
				for ($i=590; $i < 596; $i++) {
					if( array_key_exists($i,$ruidos) ){						
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{						
						$m[$j]=0;
					}					
					$j++;
				}
				if($ambiente->getTipo() == "S"){
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpSI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}else{
					$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpHI'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$zonaRecidencial);
				}

				$ruidos["p3IMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p3ResultadoI']){
					$ruidos['p3ResultadoI'] = $resultados['RESULTADO'];
				}
			}

			///-------------------------EXTERIOR---------------------------------------
			//buscar LMPS exterior
			$ruidos['p3ExteriorAsaTipo']= $puntitos;
			if( array_key_exists(646,$ruidos) && array_key_exists(648,$ruidos) ){
				$zonificacion = $repoZonificacion->findOneById($ruidos[648]['id']);
				$ruidos['p3ExteriorAsaTipo'] = $zonificacion->getAsaetipo();

				switch ($ruidos[646]['respuesta']) {
					case 'DIURNO':
						$ruidos['LmpE'] = $zonificacion->getDiurnoExterior();						
						break;
					case 'DOMINGO':
					case 'FERIADO':
					case 'NOCTURNO':
						$ruidos['p3LmpE'] = $zonificacion->getNocturnoExterior();
					default:
						break;
				}
			}else{
				$ruidos['p3LmpE'] = $puntitos;
			}

			//MEDICION 1
			if( array_key_exists(613,$ruidos) ){
				$j = 1;
				for ($i=614; $i < 620; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				
				
				$ruidos["p3EMedicion1"] = $resultados;
				$ruidos['p3ResultadoE'] = $resultados['RESULTADO'];
			}

			//MEDICION 2
			if( array_key_exists(623,$ruidos) ){
				$j = 1;
				for ($i=624; $i < 630; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["p3EMedicion2"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p3ResultadoE']){
					$ruidos['p3ResultadoE'] = $resultados['RESULTADO'];
				}
			}

			//MEDICION 3
			if( array_key_exists(633,$ruidos) ){
				$j = 1;
				for ($i=634; $i < 640; $i++) {
					if( array_key_exists($i,$ruidos) ){
						$m[$j] = $ruidos[$i]['respuesta'];
					}else{
						$m[$j]=0;
					}
					$j++;
				}
				$resultados=Ruidos::medir($repoCorrrecion,$ruidos['LmpE'],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6]);				

				$ruidos["p3EMedicion3"] = $resultados;
				if($resultados['RESULTADO'] < $ruidos['p3ResultadoE']){
					$ruidos['p3ResultadoE'] = $resultados['RESULTADO'];
				}
			}

			///-------------------------AVISO ACUSTICO-------------------------------------

			$ruidos["p3AvisoAcusticoSupera"] = -1;

			if( array_key_exists(598,$ruidos) ){
				$ruidos["p3AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[598]['respuesta']);
			}

			if( array_key_exists(600,$ruidos) && $ruidos["p3AvisoAcusticoSupera"] != 1 ){
				$ruidos["p3AvisoAcusticoSupera"] = Ruidos::avisoAcustico(70,$ruidos[600]['respuesta']);
			}

				

			for ($i=365; $i < 649 ; $i++) { 
				if(!array_key_exists($i,$ruidos)){
					$ruidos[$i]['respuesta']=$puntitos;
				}
			}

			
			return $ruidos;
			
	}

}


?>