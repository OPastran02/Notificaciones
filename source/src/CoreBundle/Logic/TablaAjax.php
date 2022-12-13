<?php

namespace CoreBundle\Logic;

use Doctrine\ORM;
use CoreBundle\Logic\FormatoTabla;


Class TablaAjax{
	
    private $RequestDisplayStart;
	private $RequestDisplayLength;
	private $RequestSearch; 
	private $RequestOrderBy;
	private $RequestEcho; 
	private $RequestRouteName;
 	private $arrayRoles;

    private $arrayTablas;
    private $cantidadColumnas; 
    private $orderBy;
    private $where;
    private $SpConditions;

    private $FormatoTabla;
    private $baseDatos;
    private $nativeQuery;
    private $nativeCountQuery;
    private $nativeQueryParam;
    private $excelArray;
    private $firstTime;

    protected $em;



	public function __construct($request,$em,$yml,$arraybusqueda=null,$arrayRoles=null) {
        $this->arrayRoles=$arrayRoles;
		$this->RequestDisplayStart=intval($request->request->get('start'));
		$this->RequestDisplayLength=intval($request->request->get('length'));//esta boludez son los numeros de la tabla, 2 horas buscandola.
		$this->RequestSearch=$request->request->get('search');
		$this->RequestOrderBy=$request->request->get('order');		
		$this->RequestEcho=intval($request->request->get('sEcho'));
		$this->setExcelArray($arraybusqueda);		

		$this->arrayTablas = array();
		$this->nativeQueryParam= array();
		$this->setColumnsAndTypes($yml,$request);
		$this->em= $em;
	 	$this->RequestRouteName=$request->getBasePath();
	}

	public function setFistTime($firsttime){
		$this->firstTime=$firsttime;
	}

	public function setColumnsAndTypes($col,$request){

		$cantidadColumnas=0;
		$contador=0;
		$excelArray=$this->getExcelArray();

		foreach ($col as $key0 => $value0) {
			$straux=explode(" ", $key0);
			$bundlearray[$key0]=array();

			foreach ($value0 as $key1 => $value1) {
				if( !empty($value1[1]["mapeado"]) ){

				}else{	
					$cantidadColumnas+=1;
					$bundlearray[$key0][$contador]=array();
					$bundlearray[$key0][$contador]["alias"]=$straux[1];
					if (empty($value1[1]["alias"])){
						$bundlearray[$key0][$contador]["stringcolumna"]=$value1[1]["campo"];
					}else{
						$bundlearray[$key0][$contador]["stringcolumna"]=$value1[1]["alias"];
					}
					
					$bundlearray[$key0][$contador]["posicion"]=$key1-1;
					$bundlearray[$key0][$contador]["busqueda"]=array();

					//if( count($excelArray)>0 ){
					if( is_array($excelArray) ){
						if(isset($excelArray[$bundlearray[$key0][$contador]["posicion"]][0]) && $excelArray[$bundlearray[$key0][$contador]["posicion"]][0] != "" ){
							array_push($bundlearray[$key0][$contador]["busqueda"], utf8_encode($excelArray[$bundlearray[$key0][$contador]["posicion"]][0] ) );
						}
						if(isset($excelArray[$bundlearray[$key0][$contador]["posicion"]][1]) && $excelArray[$bundlearray[$key0][$contador]["posicion"]][1] !=""){
							array_push($bundlearray[$key0][$contador]["busqueda"], utf8_encode ($excelArray[$bundlearray[$key0][$contador]["posicion"]][1] ) );
						}
					}else{
						if($request->request->get('order_'.$bundlearray[$key0][$contador]["posicion"])!=""){
							array_push($bundlearray[$key0][$contador]["busqueda"], utf8_encode($request->request->get('order_'.$bundlearray[$key0][$contador]["posicion"] ) ) );
						}
						if($request->request->get('order_'.$bundlearray[$key0][$contador]["posicion"]."_2")!=""){
							array_push($bundlearray[$key0][$contador]["busqueda"], utf8_encode($request->request->get('order_'.$bundlearray[$key0][$contador]["posicion"]."_2" ) ) );
						}
					}
					foreach ($value1[1] as $key2 => $value2) {
						if ($key2!="campo" and $key2!="argumentos"){
						   $bundlearray[$key0][$contador][$key2]=$value2;	
						}elseif($key2=="argumentos"){
						   $bundlearray[$key0][$contador][$key2]=array();
						   $argaux=explode(",",$value2);
						   for($i=0;$i<=count($argaux)-1;$i++){	
						   	 array_push($bundlearray[$key0][$contador][$key2],$argaux[$i]);
						   }
						}
						if ($key2=="componente" and $value2=="Concat"){
							$bundlearray[$key0][$contador]["Concat"]=array();		
							$bundlearray[$key0][$contador]["Concat"][0]="CONCAT(";
							$bundlearray[$key0][$contador]["Concat"][1]=") AS ".$bundlearray[$key0][$contador]["stringcolumna"];
							$bundlearray[$key0][$contador]["Concat"][2]=array();

							$variables=explode("|", $value1[1]["concatenar"]);
							for ($i=0; $i <= count($variables)-1; $i++) { 
								array_push($bundlearray[$key0][$contador]["Concat"][2], $variables[$i]);
							}		
						}else if ($key2=="componente" and $value2=="Select"){
							if (isset($bundlearray[$key0][$contador]["separador"])){
								$separador=$bundlearray[$key0][$contador]["separador"];
							}else{
								$separador="||";
							}
							
							$bundlearray[$key0][$contador]["Concat"]=array();
							$bundlearray[$key0][$contador]["Concat"][0]="(SELECT GROUP_CONCAT(CONCAT(";
							$bundlearray[$key0][$contador]["Concat"][1]=",'".$separador."')) AS ".$bundlearray[$key0][$contador]["stringcolumna"];
							$bundlearray[$key0][$contador]["Concat"][2]=" FROM ";
							$bundlearray[$key0][$contador]["Concat"][3]=" JOIN ";
							$bundlearray[$key0][$contador]["Concat"][4]=array();

							$variables=explode("|", $value1[1]["concatenar"]);
							for ($i=0; $i <= count($variables)-1; $i++) { 
								array_push($bundlearray[$key0][$contador]["Concat"][4], $variables[$i]);
							}		
						}		
					}
					$contador+=1;
				}	
			}

		}

		unset($excelArray);
		
		$this->arrayTablas = $bundlearray;
		$this->cantidadColumnas= $cantidadColumnas;

		//print_r($bundlearray);
	}

	public function getArrayTablas(){
		return $this->arrayTablas;
	}

	public function getCantidadColumnas(){
		return $this->cantidadColumnas;
	}

	public function setSpecialConditions($str){
		if ($str!=""){
			$this->SpConditions=$str;
		}else{
			$this->SpConditions="1=1";
		}
	}

	public function getSpecialConditions(){
		return $this->SpConditions;
	}
	
	public function setsOrderBy(){

		if($this->firstTime <>1){
		  $bundleArray=$this->getArrayTablas();
		  		
		  $orderBy=$this->RequestOrderBy;
		
		  foreach ($bundleArray as $key => $value) {
		  	 foreach ($value as $key1 => $value1) {
		  	 //	print_r($value1["posicion"]);
		  	 	if ($value1["posicion"]==$orderBy[0]["column"]){
		  	 		if(isset($value1["componente"]) and $value1["componente"]=="Concat"){
						$ob="ORDER BY ".$value1["stringcolumna"]." ".$orderBy[0]["dir"];
					//	var_dump($ob);
		  	 		}else{
		  	 			if (!isset($value1["tieneAlias"])){
		  	 				$ob="ORDER BY ".$value1["alias"].".".$value1["stringcolumna"]." ".$orderBy[0]["dir"];	
		  	 			}else{
		  	 				$ob="ORDER BY ".$value1["stringcolumna"]." ".$orderBy[0]["dir"];	
		  	 			}
		  	 			
		  	 		}
		  	 	
		  	 	}
		  	 }	
		  }

		  unset($bundleArray);
		  unset($orderBy);

		  $this->orderBy=$ob;
		  //var_dump($ob);
		}
    }

    public function getOrderBy(){
          return $this->orderBy;
    }

	public function setWhere(){
		  $wh="";
		  $arrayTablas=$this->getArrayTablas();
		  	foreach ($arrayTablas as $key => $value) {
		  		foreach ($value as $key1 => $value0) {
		  			if(count($value0["busqueda"])>0){
		  					
		  					$value0["busqueda"] = str_replace("'", "''", $value0["busqueda"]);
		  					$wh .= "AND (";
		  					if(isset($value0["tieneAlias"]) and $value0["tieneAlias"]=="no"){
		  						if(isset($value0["nombreAlias"])){
									$wh .= $value0["nombreAlias"];
		  						}else{
		  							$wh .= $value0["stringcolumna"];	
		  						}		
		  					}else{
		  						$wh .= $value0["alias"].".".$value0["stringcolumna"];
		  					}       
			               
			                if ($value0["tipo"]=="String"){
			                
			                	$wh .= " LIKE '%".utf8_decode(str_replace('*','%',$value0["busqueda"][0]))."%' OR ";

			                }elseif ($value0["tipo"]=="Integer"){

			                	if(count($value0["busqueda"])>1){
			                	 	$wh .= " BETWEEN '".$value0["busqueda"][0]."' AND '".$value0["busqueda"][1]."' OR ";	
			                	}else{
			                		$wh .= " in ( ".$value0["busqueda"][0]." ) OR ";	
			                	}	
			               
			                }elseif($value0["tipo"]=="DateTime"){

			                	$value0["busqueda"][0]=str_replace("/", "-", $value0["busqueda"][0]);
			                	$date = new \DateTime($value0["busqueda"][0]);
			                	$busqueda1 = date_format($date, 'Y-m-d H:i:s');
			                
			                	if(count($value0["busqueda"])>1){
									$value0["busqueda"][1]=str_replace("/", "-", $value0["busqueda"][1]);
				                	$date = new \DateTime($value0["busqueda"][1]);
				                	$busqueda2 = date_format($date, 'Y-m-d H:i:s');

			                	 	$wh .= " BETWEEN '".$busqueda1."' AND '".$busqueda2."' OR ";	
			                	}else{
			                		$wh .= " = '".$busqueda1."' OR ";	
			                	}
			               
			                }elseif($value0["tipo"]=="Date"){
			            		$value0["busqueda"][0]=str_replace("/", "-", $value0["busqueda"][0]);
			                	$date = new \DateTime($value0["busqueda"][0]);
			                	$busqueda1 = date_format($date, 'Y-m-d');	

			            		if(count($value0["busqueda"])>1){
									$value0["busqueda"][1]=str_replace("/", "-", $value0["busqueda"][1]);
			            			$date = new \DateTime($value0["busqueda"][1]);
			                		$busqueda2 = date_format($date, 'Y-m-d');	

			                	 	$wh .= " BETWEEN '".$busqueda1."' AND '".$busqueda2."' OR ";	
			                	}else{
			                		$wh .= " = '".$busqueda1."' OR ";	
			                	}
			                
			                }
			                $wh = substr_replace( $wh, "", -3 );
			                $wh .= ')';
		  				}
		  			}
		  		}	
		  	  
		  $this->where=$wh;
		  unset($arrayTablas);
		  unset($wh);

		  //var_dump($wh);
	}

	public function getWhere(){
		return $this->where;
	}

	public function setiTotalRecords(){		
		if($this->nativeCountQuery){				

			$query=$this->nativeCountQuery." ".$this->getSpecialConditions()." ".$this->getWhere();			
			$stmt=$this->em->getConnection()->prepare($query);
			foreach ($this->nativeQueryParam as $param) {
				$stmt->bindValue($param[0],$param[1]);
			}
			
			$stmt->execute();
			$tabla=$stmt->fetchAll();			
			$this->iTotalRecords=$tabla[0]['count'];			
			$this->em->getConnection()->close();
			$this->em->clear();
			unset($stmt);
			unset($tabla);
			
		}
		else{


			if (!$this->nativeQuery){

				$arrayTablas=$this->getArrayTablas();
			
		 		$contador=0;

		 		$from = " FROM ";
		 		$campos="";
		 		$sSQL='SELECT ';
				foreach ($arrayTablas as $key => $value) {
				  	foreach ($value as $key1 => $value0) {
				  		//print_r($key);
				  		$alias=$value0["alias"];
				  		if($contador==0){
				  			if(isset($value0["componente"]) and $value0["componente"]=="Concat"){
								$campos.=$value0["Concat"][0];
								for ($i=0; $i <= count($value0["Concat"][2])-1 ; $i++) { 
									 $value0["Concat"][2][$i]=str_replace("\\", "'", $value0["Concat"][2][$i]); 
									if($i==0){							
										$campos.=$value0["Concat"][2][$i];		
									}elseif(($i%2)==0){
										$campos.=",".$value0["Concat"][2][$i];	
									}else{
										$campos.=",'".$value0["Concat"][2][$i]."'";
									}	
								}
								$campos.=$value0["Concat"][1];
							}elseif(isset($value0["componente"]) and $value0["componente"]=="Select"){
								$campos.=$value0["Concat"][0];
								for ($i=0; $i <= count($value0["Concat"][4])-1 ; $i++) { 
									 $value0["Concat"][4][$i]=str_replace("\\", "'", $value0["Concat"][4][$i]); 
									if($i==0){							
										$campos.=$value0["Concat"][4][$i];		
									}elseif(($i%2)==0){
										$campos.=",".$value0["Concat"][4][$i];	
									}	
								}	
							}else{
								$campos.=$value0["alias"].".".$value0["stringcolumna"];
				  			}
				  			
				  		}else{
				  			if(isset($value0["componente"]) and $value0["componente"]=="Concat"){
								$campos.=",".$value0["Concat"][0];
								for ($i=0; $i <= count($value0["Concat"][2])-1 ; $i++) {
								 $value0["Concat"][2][$i]=str_replace("\\", "'", $value0["Concat"][2][$i]);  
									if($i==0){							
										$campos.=$value0["Concat"][2][$i];		
									}elseif(($i%2)==0){
										$campos.=",".$value0["Concat"][2][$i];	
									}else{
										$campos.=",'".$value0["Concat"][2][$i]."'";
									}	
								}
								$campos.=$value0["Concat"][1];
							}elseif(isset($value0["componente"]) and $value0["componente"]=="Select"){
								 $value0["Concat"][4][$i]=str_replace("\\", "'", $value0["Concat"][4][$i]); 
								$campos.=",".$value0["Concat"][0];
								for ($i=0; $i <= count($value0["Concat"][4])-1 ; $i++) { 
									if($i==0){							
										$campos.=$value0["Concat"][4][$i];		
									}elseif(($i%2)==0){
										$campos.=",".$value0["Concat"][4][$i];	
									}		
								}
								$campos.=$value0["Concat"][1].$value0["Concat"][2].$value0["entidad"];
								if (isset($value0["joins"])){
									$campos.=$value0["Concat"][3].$value0["joins"];
								}
								$campos.=$value0["Concat"][1];
							}else{
								$campos.=",".$value0["alias"].".".$value0["stringcolumna"];
				  			}
				  		}
				  		$contador+=1;
					}										
					if($value === end($arrayTablas)){						
						
						$from .= $key;

					}elseif($value === reset($arrayTablas)){
						
						$postLeft = strpos($key,"left");
						if($postLeft){
							$from .= $key." LEFT JOIN ";
						}else{
							$from .= $key." JOIN ";	
						}
						
						
					}else{	
						$postLeft = strpos($key,"left");
						if($postLeft){
							$from .= $key." LEFT JOIN ";
						}else{
							$from .= $key." JOIN ";		
						}						
					}
				}


				$StringSql=$sSQL.$campos.$from." WHERE ".$this->getSpecialConditions()." ".$this->getWhere();
					//print_r($StringSql);
					//para ver query
				$query= $this->em->createQuery($StringSql)
								 ->getResult();
				$this->em->clear(); 
				$this->iTotalRecords=count($query);

				unset($arrayTablas);

			}else{
				$hayCount = false;
				$selectCount = strpos(strtolower($this->nativeQuery),"select");
				$fromCount = strpos(strtolower($this->nativeQuery),"from");
				$havingCount = strpos(strtolower($this->nativeQuery),"having");


				$queryCount = $this->nativeQuery;

				if($selectCount == 0 && $fromCount && !$havingCount){
					$queryCount = 'SELECT COUNT(*) as count FROM '.substr($queryCount, $fromCount+4);
					$hayCount = true;
				}		

				$query=$queryCount." ".$this->getSpecialConditions()." ".$this->getWhere();			
				$stmt=$this->em->getConnection()->prepare($query);
				foreach ($this->nativeQueryParam as $param) {
					$stmt->bindValue($param[0],$param[1]);
				}			
				
				if($hayCount){
					$stmt->execute();
					$tabla=$stmt->fetchAll();			
					$this->iTotalRecords=$tabla[0]['count'];					 
					$this->em->getConnection()->close();
					$this->em->clear();
					unset($stmt);
					unset($tabla);
				}else{
					$stmt->execute();
					$tabla=$stmt->fetchAll();
					$this->iTotalRecords=count($tabla);					 
					$this->em->getConnection()->close();
					$this->em->clear();
					unset($stmt);
					unset($tabla);
				}

				//var_dump($this->iTotalRecords);
				

			}	
		}

		
    }
   
	public function getiTotalRecords(){
		return $this->iTotalRecords;
    }

    public function getRequestRouteName(){
		return $this->RequestRouteName;
    }

	public function getiDisplayStart(){
		return $this->iDisplayStart;
	}

	public function getiDisplayLength(){
		return $this->iDisplayLength;
	}

	public function getrsearch(){
		return $this->rSearch;
	}

	public function setNativeQuery($nativeQuery){
		$this->nativeQuery=$nativeQuery;
	}

	public function getNativeQuery(){
		return $this->nativeQuery;
	}

	public function setNativeQueryParam($nativeQueryParam){
		$this->nativeQueryParam=$nativeQueryParam;
	}

	public function getNativeQueryParam(){
		return $this->nativeQueryParam;
	}

	public function getExcelArray(){
		return $this->excelArray;
	}

	public function setExcelArray($excelArray){
		$this->excelArray=$excelArray;
	}



    public function setArrayTablas(){
		
		if (!$this->nativeQuery){
			$arrayTablas=$this->getArrayTablas();
			//print_r($arrayTablas);
	 		$contador=0;

	 		$from = " FROM ";
	 		$campos="";
	 		$sSQL='SELECT ';
			foreach ($arrayTablas as $key => $value) {
			  	foreach ($value as $key1 => $value0) {
			  		//print_r($key);
			  		$alias=$value0["alias"];
			  		if($contador==0){
			  			if(isset($value0["componente"]) and $value0["componente"]=="Concat"){
							$campos.=$value0["Concat"][0];
							for ($i=0; $i <= count($value0["Concat"][2])-1 ; $i++) {
							   $value0["Concat"][2][$i]=str_replace("\\", "'", $value0["Concat"][2][$i]); 
								if($i==0){							
									$campos.=$value0["Concat"][2][$i];		
								}elseif(($i%2)==0){
									$campos.=",".$value0["Concat"][2][$i];	
								}else{
									$campos.=",'".$value0["Concat"][2][$i]."'";
								}	
							}
							$campos.=$value0["Concat"][1];
						}elseif(isset($value0["componente"]) and $value0["componente"]=="Select"){
							$campos.=$value0["Concat"][0];
							for ($i=0; $i <= count($value0["Concat"][4])-1 ; $i++) { 
								$value0["Concat"][4][$i]=str_replace("\\", "'", $value0["Concat"][4][$i]);
								if($i==0){							
									$campos.="'".$value0["Concat"][4][$i]."'";		
								}elseif(($i%2)==0){
									$campos.=",".$value0["Concat"][4][$i];	
								}else{
									$campos.=",'".$value0["Concat"][4][$i]."'";
								}		
							}	
						}else{
							$campos.=$value0["alias"].".".$value0["stringcolumna"];
			  			}
			  			
			  		}else{
			  			if(isset($value0["componente"]) and $value0["componente"]=="Concat"){
							$campos.=",".$value0["Concat"][0];
							for ($i=0; $i <= count($value0["Concat"][2])-1 ; $i++) { 
								$value0["Concat"][2][$i]=str_replace("\\", "'", $value0["Concat"][2][$i]);
								if($i==0){							
									$campos.=$value0["Concat"][2][$i];		
								}elseif(($i%2)==0){
									$campos.=",".$value0["Concat"][2][$i];	
								}else{
									$campos.=",'".$value0["Concat"][2][$i]."'";
								}	
							}
							$campos.=$value0["Concat"][1];
						}elseif(isset($value0["componente"]) and $value0["componente"]=="Select"){
							$campos.=",".$value0["Concat"][0];
							for ($i=0; $i <= count($value0["Concat"][4])-1 ; $i++) { 
								$value0["Concat"][4][$i]=str_replace("\\", "'", $value0["Concat"][4][$i]);
								if($i==0){							
									$campos.=$value0["Concat"][4][$i];		
								}elseif(($i%2)==0){
									$campos.=",".$value0["Concat"][4][$i];	
								}else{
									$campos.=",'".$value0["Concat"][4][$i]."'";
								}		
							}
							$campos.=$value0["Concat"][1].$value0["Concat"][2].$value0["entidad"];
							if (isset($value0["joins"])){
								$campos.=$value0["Concat"][3].$value0["joins"];
							}
							$campos.=$value0["Concat"][1];
						}else{
							$campos.=",".$value0["alias"].".".$value0["stringcolumna"];
			  			}
			  		}
			  		$contador+=1;
				}
				
				if($value === end($arrayTablas)){
					$from .= $key;
				}elseif($value === reset($arrayTablas)){
					$postLeft = strpos($key,"left");
						if($postLeft){
							$from .= $key." LEFT JOIN ";
						}else{
							$from .= $key." JOIN ";	
						}
				}else{
					$postLeft = strpos($key,"left");
						if($postLeft){
							$from .= $key." LEFT JOIN ";
						}else{
							$from .= $key." JOIN ";	
						}
				}
			}

			$StringSql=$sSQL.$campos.$from." WHERE ".$this->getSpecialConditions()." ".$this->getWhere()." ".$this->getOrderBy();
			if ($this->excelArray == null){
				$query= $this->em->createQuery($StringSql)
 							 ->setMaxResults($this->RequestDisplayLength)
							 ->setFirstResult($this->RequestDisplayStart)
							 ->getResult();
				$this->em->clear(); 
			}else{
				$query= $this->em->createQuery($StringSql)
							 ->getResult();
				$this->em->clear(); 
			}
			
			$this->baseDatos=$query;

			unset($arrayTablas);
		}else{
			$query=$this->nativeQuery." ".$this->getSpecialConditions()." ".$this->getWhere()." ".$this->getOrderBy();			
			if ($this->excelArray == null){
				$query.=" LIMIT ".$this->RequestDisplayLength." OFFSET ".$this->RequestDisplayStart;
			}	
			$stmt=$this->em->getConnection()->prepare($query);
			foreach ($this->nativeQueryParam as $param) {
				$stmt->bindValue($param[0],$param[1]);
			}	
			$stmt->execute();
	
			$this->baseDatos=$stmt->fetchAll();
			$this->em->clear(); 
			$this->em->getConnection()->close();

			unset($stmt);
		}	




    }

    public function getBaseDatos(){
    	return $this->baseDatos;
    }


    public function setrecords(){
		$FormatoTabla=new FormatoTabla($this->arrayRoles);
	    $arrayTabla=$this->getArrayTablas();
		$baseDatos=$this->getBaseDatos();
		$ruta=$this->getRequestRouteName();
		$records = array();
		$records["data"] = array(); 	
		$i=0;
		$j=0;
		$arrayTipos = array();
		$z = 0;

		//print_r($arrayTabla);
		//print_r($baseDatos);

		foreach ($arrayTabla as $key => $value) {			
			array_push($arrayTipos, $value);
		}	
		//print_r($arrayTipos);
		foreach ($baseDatos	 as $key => $value) {	
			$records["data"][$j] = array();
			for ($i=0; $i < count($arrayTipos) ; $i++) {				 					
				foreach ($arrayTipos[$i] as $key1 => $value1){					
					$valorbd = $value[$value1["stringcolumna"]];			
					array_push($records["data"][$j],$FormatoTabla->checkearFormato($value1['tipo'],$valorbd,$value1,$value,$ruta));
				}
			}
			$j++;
		}

		unset($FormatoTabla);
		unset($arrayTabla);
		unset($baseDatos);
		unset($arrayTipos);

		//print_r($records['data']);
		$records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    	//$records["customActionMessage"] = "Filtrado correctamente!"; // pass custom message(useful for getting status of group actions)
    	//$records["draw"] = $this->RequestEcho;
		$records["recordsTotal"] = $this->getiTotalRecords();
		$records["recordsFiltered"] = $this->getiTotalRecords();
/*
				$records["iTotalRecords"] = $this->getiTotalRecords();
		$records["iTotalDisplayRecords"] = 5;*/
    	//print_r($records);
    	return json_encode($records);
    }

    public function getrecords(){
    	return $this->$records;
    }

	  
	public function Initialize(){
		$value=array();
		$this->setsOrderBy();
		$this->setWhere();
		$this->setiTotalRecords();
		
    	$this->setArrayTablas();
		$value=$this->setrecords();
//print_r($this->baseDatos);
    	//exit();
    	$this->em->clear();


    	unset($RequestDisplayStart);
		unset($RequestDisplayLength);
		unset($RequestSearch) ;
		unset($RequestOrderBy);
		unset($RequestEcho);
		unset($RequestRouteName);
	 	unset($arrayRoles);

	    unset($arrayTablas);
	    unset($cantidadColumnas) ;
	    unset($orderBy);
	    unset($where);
	    unset($SpConditions);

	    unset($FormatoTabla);
	    unset($baseDatos);
	    unset($nativeQuery);
	    unset($nativeCountQuery);
	    unset($nativeQueryParam);
	    unset($excelArray);
	    unset($firstTime);


    	

		return $value;
	}

	public function getQueryTable(){
		$value=array();
		$this->setsOrderBy();
		$this->setWhere();
		$this->setiTotalRecords();
		
    	$this->setArrayTablas();
		return $this->baseDatos;
	}

	public function setQueryCount($query){
		$this->nativeCountQuery = $query;
	}

	public function getQueryCount(){
		return $this->nativeCountQuery;
	}


}





?>