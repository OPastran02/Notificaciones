<?php

namespace CoreBundle\Logic;

use Doctrine\ORM;
use CoreBundle\Logic\encriptador;



Class FormatoTabla{

    private $formato;
	private $valor;
	private $arrayestados; 
	private $arrayvalores;

	public function __construct($arrayRoles){
		$this->arrayRoles = $arrayRoles;	
	}

	public function formatoString($texto = '',$arrayestados){
		$style="";

		if(isset($arrayestados['attr'])){
			$attr=$arrayestados["attr"];
		}else{
			$attr="";
		}

		if(isset($arrayestados['color'])){
			$style.="color:".$arrayestados['color'].";b ";
		}

		if(isset($arrayestados['background-color'])){
			$style.="background-color:".$arrayestados['background-color']."; ";
		}

		if(isset($arrayestados['font-family'])){
			$style.="font-family:".$arrayestados['font-family']."; ";
		}

		if(isset($arrayestados['font-size'])){
			$style.="font-size:".$arrayestados['font-size']."; ";
		}

		if(isset($arrayestados['text-align'])){
			$style.="text-align:".$arrayestados['text-align']."; ";
		}

		if(isset($arrayestados['padding'])){
			$style.="padding:".$arrayestados['text-align']."px; ";
		}

		return '<h6 '.$attr.' style="'.$style.'">'.$texto.'</h6>';		
	}


	public function formatoFecha($fecha,$arrayestados,$arrayvalores){
		//print_r($arrayvalores);
		 $style="";

		if(isset($arrayestados['color'])){
			$style.="color:".$arrayestados['color']."; ";
		}

		if(isset($arrayestados['background-color'])){
			$style.="background-color:".$arrayestados['background-color']."; ";
		}

		if(isset($arrayestados['font-family'])){
			$style.="font-family:".$arrayestados['font-family']."; ";
		}

		if(isset($arrayestados['font-size'])){
			$style.="font-size:".$arrayestados['font-size']."; ";
		}

		if(isset($arrayestados['text-align'])){
			$style.="text-align:".$arrayestados['text-align']."; ";
		}

		if(isset($arrayestados['padding'])){
			$style.="padding:".$arrayestados['text-align']."px; ";
		}

		if(isset($arrayestados['date_format'])){
			$date_format=$arrayestados['date_format'];
		}else{
			$date_format="d-m-Y";
		}

		if(gettype($fecha) == 'string'){			
			$fecha = \DateTime::createFromFormat('Y-m-d', $fecha);
		}
		
		if($fecha != "") {
      		return '<h6 style="'.$style.'">'.date_format($fecha, $date_format).'</h6>'; 
      	}else{
      		return "";
     	}
	}

	public function formatoFechaHora($fecha,$arrayestados){
			 $style="";

		if(isset($arrayestados['color'])){
			$style.="color:".$arrayestados['color']."; ";
		}

		if(isset($arrayestados['background-color'])){
			$style.="background-color:".$arrayestados['background-color']."; ";
		}

		if(isset($arrayestados['font-family'])){
			$style.="font-family:".$arrayestados['font-family']."; ";
		}

		if(isset($arrayestados['font-size'])){
			$style.="font-size:".$arrayestados['font-size']."; ";
		}

		if(isset($arrayestados['text-align'])){
			$style.="text-align:".$arrayestados['text-align']."; ";
		}

		if(isset($arrayestados['padding'])){
			$style.="padding:".$arrayestados['text-align']."px; ";
		}

		if(isset($arrayestados['date_format'])){
			$date_format=$arrayestados['date_format'];
		}else{
			$date_format="d-m-Y H:i:s";
		}

		$date = new \DateTime($fecha);

		if($fecha != "") { 
      		return '<h6 style="'.$style.'">'.date_format($date, $date_format).'</h6>';
      	}else{
      		return "";
     	}
     	
     	return $fecha;
	}

	public function formatoBoolean($Boolean,$arrayestados){
		$style="";
		if(isset($arrayestados['color'])){
			$style.="color:".$arrayestados['color']."; ";
		}else{
			$style.="color:#000000;";
		}

		if(isset($arrayestados['background-color'])){
			$style.="background-color:".$arrayestados['background-color']."; ";
		}else{
			$style.="background-color:#FFFFFF;";
		}

		if(isset($arrayestados['font-family'])){
			$style.="font-family:".$arrayestados['font-family']."; ";
		}else{
			$style.="font-family:verdana;";
		}

		if(isset($arrayestados['font-size'])){
			$style.="font-size:".$arrayestados['font-size']."; ";
		}else{
			$style.="font-size:100%;";
		}

		if(isset($arrayestados['text-align'])){
			$style.="text-align:".$arrayestados['text-align']."; ";
		}else{
			$style.="text-align:left; ";
		}

		if(isset($arrayestados['padding'])){
			$style.="padding:".$arrayestados['text-align']."px; ";
		}else{
			$style.="padding:5px; ";
		}

		if($Boolean == "" || $Boolean == "0" ) { 
			$bool="NO";  		
      	}else{
      		$bool="SI";
     	}

     	return '<h6 style="'.$style.'">'.$bool.'</h6>';
	}



	public function setFormatoBoton($boton,$arrayestados,$arrayvalores){
		$strVariables="";


		if(isset($arrayestados['color'])){
			$color=$arrayestados['color'];
		}else{
			$color="black";
		}

		if(isset($arrayestados['tipoBoton'])){
			$type=$arrayestados['tipoBoton'];
		}else{
			$type="Submit";
		}

		if(isset($arrayestados['icono'])){
			$icono=$arrayestados["icono"];
		}else{
			$icono="fa fa-search";
		}

		if(isset($arrayestados['titulo'])){
			$titulo=$arrayestados["titulo"];
		}else{
			$titulo="Titulo Generico";
		}

		if(isset($arrayestados['attr'])){
			$attr=$arrayestados["attr"];
		}else{
			$attr="";
		}

		//ESTO ESTA PARA EL ORTO
		//print_r($arrayvalores);
 		if(isset($arrayestados['javascript'])){
			$aux11=explode("(", $arrayestados["javascript"]);
			$funcion=$aux11[0];
		}else{
			$funcion="";
		} 

		if(isset($arrayestados['argumentos'])){
			foreach ($arrayestados['argumentos'] as $key => $value) {
				$aux=explode(".", $value);
				$variable=$aux[1];
				if(isset($arrayestados['decodificar'])){
					//aca es donde se codifica todo SEPHYCODIFICA
					$strVariables.="".encriptador::mrcrypt_encrypt($arrayvalores[$aux[1]])."";
				}else{
					$strVariables.="".$arrayvalores[$aux[1]]."";
				}
				if($value !== end($arrayestados['argumentos'])){
					$strVariables.=",";
				}
				
			}
		}
		if ($funcion!=""){
			$javascript='href="javascript:'.$funcion.'(\''.$strVariables.'\');"';	
		}else{
			$javascript="";
		}
		
		if(isset($arrayestados['permisos'])){
			if(in_array($arrayestados['permisos'], $this->arrayRoles)){
				return '<div class="col-md-12">
							<a '.$javascript.' '.$attr.' class="btn btn-circle btn-outline sbold uppercase btn-block '.$color.'">
								<i class="'.$icono.'"></i>'.$titulo.'
							</a>
						</div>';
			}else{
				return '<div class="col-md-12">
							<a href="#" disabled class="btn btn-circle btn-outline btn-block sbold uppercase dark">
								<i class="'.$icono.'"></i> sin permisos
							</a>
						</div>';	

			}
		}else{
				return '<div class="col-md-12">
				<a '.$javascript.' '.$attr.' class="btn btn-circle btn-outline sbold uppercase btn-block '.$color.'">
					<i class="'.$icono.'"></i>'.$titulo.'
				</a>
			</div>';
		}

			
	}

    public function setFormatoAHref($valor,$arrayestados,$arrayvalores,$ruta){
		$cap="";
		$style="";
		if(isset($arrayestados['color'])){
			$color=$arrayestados['color'];
		}else{
			$color="black";
		}
       

		if(isset($arrayestados['icono'])){
			$icono=$arrayestados["icono"];
		}else{
			$icono="fa fa-search";
		}

		if(isset($arrayestados['titulo'])){
			$titulo=$arrayestados["titulo"];
		}else{
			$titulo="Titulo Generico";
		}

		if(isset($arrayestados['attr'])){
			$attr=$arrayestados["attr"];
		}else{
			$attr="";
		}

		$strVariables="";


        if(isset($arrayestados['argumentos'])){
			foreach ($arrayestados['argumentos'] as $key => $value) {
				$aux=explode(".", $value);
				$variable=$aux[1];
				
				if(isset($arrayestados['decodificar'])){
					//aca es donde se codifica todo SEPHYCODIFICA
					$strVariables.="".encriptador::mrcrypt_encrypt($arrayvalores[$aux[1]])."";
				}else{
					$strVariables.="".$arrayvalores[$aux[1]]."";
				}

				if($value !== end($arrayestados['argumentos'])){
					$strVariables.="/";
				}
				
			}
		}

		if(isset($arrayestados['ruta'])){
			$path=$ruta."/".$arrayestados['ruta']."/".$strVariables;		
		}else{
			$path="#";
		}

		if(isset($arrayestados['permisos'])){
			if(in_array($arrayestados['permisos'], $this->arrayRoles)){
				return '<div class="col-md-12">
							<a href="'.$path.'" '.$attr.' class="btn btn-circle btn-outline sbold uppercase btn-block '.$color.'">
								<i class="'.$icono.'"></i>'.$titulo.'
							</a>
						</div>';
			}else{
				return '<div class="col-md-12">
							<a href="#" disabled class="btn btn-circle btn-outline btn-block sbold uppercase dark">
								<i class="'.$icono.'"></i> sin permisos
							</a>
						</div>';	

			}
		}else{
				return '<div class="col-md-12">
				<a href="'.$path.'" '.$attr.' class="btn btn-circle btn-outline sbold uppercase btn-block '.$color.'">
					<i class="'.$icono.'"></i>'.$titulo.'
				</a>
			</div>';
		}
	}


	public function setExtra($arrayestados,$arrayvalores){
		//var_dump($arrayestados["extra"]);
		$valores=explode("|", $arrayestados["extraArg"]);
		if(isset($arrayestados["extraTipos"])){
				$tipos=explode("|", $arrayestados["extraTipos"]);	
		}
		for ($i=0; $i <=count($valores)-1 ; $i++) { 
			$valor=explode(".", $valores[$i]);
			if($i==0){
				if(isset($arrayestados['encriptArg'])){
					$texto=str_replace("--arg--", encriptador::mrcrypt_encrypt($arrayvalores[$valor[1]]),$arrayestados["extra"]);
				}else{
					$texto=str_replace("--arg--", $arrayvalores[$valor[1]],$arrayestados["extra"]);
				}				
			}else{
				if( gettype($arrayvalores[$valor[1]])!='object' )
					if(isset($arrayestados['encriptArg'])){
						$texto=str_replace("--arg".$i."--", encriptador::mrcrypt_encrypt($arrayvalores[$valor[1]]),$texto);
					}else{
						$texto=str_replace("--arg".$i."--", $arrayvalores[$valor[1]],$texto);
					}	
				else{
					if(isset($arrayestados['encriptArg'])){
						$texto=str_replace("--arg".$i."--", encriptador::mrcrypt_encrypt(date_format($arrayvalores[$valor[1]]),"d-m-Y"),$texto);
					}else{
						$texto=str_replace("--arg".$i."--", date_format($arrayvalores[$valor[1]],"d-m-Y"),$texto);
					}	
				}
			}
		}

		return $texto;

	}

	public function checkearFormato($formato,$valor,$arrayestados,$arrayvalores,$ruta){
		if (isset($arrayestados["componente"])){
			$componente=$arrayestados["componente"];
		}else{
			$componente="";
		}


		if($componente =="Button"){
			$value=$this->setFormatoBoton($valor,$arrayestados,$arrayvalores);
		}elseif ($componente =="aHref") {
			$value=$this->setFormatoAHref($valor,$arrayestados,$arrayvalores,$ruta);
		}elseif ($componente =="Select2") {
			$value=$this->setFormatoAHref($valor,$arrayestados,$arrayvalores);
		}elseif ($componente =="Empty") {
			$value="";
		}else{
			if($formato=="String"){
				$value=$this->formatoString($valor,$arrayestados);
			}else if($formato=="Integer"){
				$value=$this->formatoString($valor,$arrayestados);
			}else if($formato=="Date"){
				$value=$this->formatoFecha($valor,$arrayestados,$arrayvalores);
			}else if($formato=="DateTime"){
				$value=$this->formatoFechaHora($valor,$arrayestados);
			}else if($formato=="Boolean"){
				$value=$this->formatoBoolean($valor,$arrayestados);
			}else{
				$value="";
			}
		}	

		if(isset($arrayestados["extra"])){
			$value.=$this->setExtra($arrayestados,$arrayvalores);
		}

		return $value;
	}
}





?>