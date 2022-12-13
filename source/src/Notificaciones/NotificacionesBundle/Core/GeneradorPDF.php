
<?php

namespace Notificaciones\NotificacionesBundle\Core;


use CoreBundle\Logic\Pdf\fpdi;

include ("../Pdf/fpdi.php");

include ("../conexion.php");

$cuerpo=$_GET['cuerpo'];

$sSQL= sqlsrv_query($conn,"SELECT Cuerpo FROM Pedido.Cuerpos WHERE Id_Cuerpo =".$cuerpo);
$data = sqlsrv_fetch_array($sSQL);	

$cuerpo1=$data[0];
$RazonSocial=$_GET['RazonSocial'];
$comuna=$_GET['comuna'];
$cuerpo=$_GET['cuerpo'];
$plazo2=$_GET['plazo2'];
$plazo1=$_GET['plazo1'];
$Direccion=$_GET['direccion'];
$Actuacion=$_GET['actuacion'];
$Numero=$_GET['Numero'];
$Fojas=$_GET['Fojas'];
$Legal=$_GET['legal'];

if($Legal == 1){
	$TipoLegal = 'CONSTITUIDO';
}else{
	$TipoLegal = 'DENUNCIADO';
}


$template_pdf = "ModeloCedula.pdf";
$pdf = new FPDI();
 
// importamos el documento
$pdf->setSourceFile($template_pdf);
 
 // seteamos la fuente, el estilo y el tamano 
$pdf->SetFont('Times','B',10);
 
// seteamos la posicion inicial
$pdf->SetXY(25, 80);
/*
select * from Documento.fnCedulas(37,1)
select * from Establecimiento.fnEstablecimientoActuacion(37)
SELECT * FROM Establecimiento.fnEstablecimientoRazonSocial(37) WHERE FechaFin = '31/12/9999'
*/


$pdf->AddPage();
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx);

$pdf->SetFont('Times','B',14);
$pdf->SetXY(145, 5);
$pdf->Write(4, utf8_decode('Cedula N°: ').$Numero);

$pdf->SetFont('Times','B',10);
$pdf->SetXY(58, 38);
$pdf->Write(4, utf8_decode($RazonSocial));

$pdf->SetFont('Times','B',12);
$pdf->SetXY(30, 47);
$pdf->Write(4, utf8_decode($Direccion));

$pdf->SetFont('Times','B',12);
$pdf->SetXY(36, 51);
$pdf->Write(4, $comuna);

$pdf->SetFont('Times','B',12);
$pdf->SetXY(123, 60);
$pdf->Write(4, $TipoLegal);

$pdf->SetFont('Times','B',8);
$pdf->SetXY(14, 70);
$porcion = explode("-",$Actuacion);
//$pdf->MultiCell(30, 4, $Actuacion, 0,'J',0);
$pdf->Write(4, $porcion[0]);
$pdf->Write(4, '-');
$pdf->Write(4, $porcion[1]);
$pdf->Write(4, '-');
$pdf->Write(4, $porcion[2]);
$pdf->Write(4, '-');
$pdf->SetXY(14, 74);
$pdf->Write(4, $porcion[3]);
$pdf->Write(4, '-');
$pdf->Write(4, $porcion[4]);

$pdf->SetFont('Times','B',10);

if($plazo1 < 99999){
$pdf->SetXY(64, 70);
$pdf->Write(4, $plazo1);
}

if ($plazo2 < 99999){
$pdf->SetXY(102, 70);
$pdf->Write(4, $plazo2);
}
$pdf->SetXY(144, 70);
$pdf->Write(4, $Fojas);


$pdf->SetXY(10, 80);
$pdf->SetFont('Arial','',10);
$pdf->SetFont('');

$pdf->WriteHTML($cuerpo1,true);
$pdf->MultiCell(189, 4, '', 0,'J',0);

$cuerpo2 = utf8_decode('

Nota: toda documentación  a ingresar ante la Mesa de Entradas de esta Dirección General, deberá ser presentada en formato digital en PDF (máximo 10mb o 100 kb); junto a una nota de presentación firmada por el titular/representante legal conforme artículo 36 de la Ley de Procedimientos Administrativos Decreto 1510/97, en la cual se detallen los archivos adjuntos.

Se hace saber que en el link:
	http://www.buenosaires.gob.ar/agenciaambiental/listado-de-inspectores-de-la-direccion-general-de-control-ambiental podrá consultar el listado público de personal habilitado para realizar inspecciones en nombre de la Dirección General de Control Ambiental.  Asimismo en caso de consultas y/o quejas respecto del accionar inspectivo, podrá dirigirse al Centro de Información y Formación Ambiental sito en Av. Escalada y Castañares, los días Lunes a Viernes en el horario de 9 a 14 hs.-

Se hace saber que el Gobierno de la Ciudad de Buenos cuenta con el Programa Buenos Aires Produce más Limpio. Dicho programa es una herramienta que se ofrece a las PyMES de la Ciudad de Buenos Aires con el fin de asesorar gratuitamente sobre aspectos técnicos y normativos ambientales. Es de adhesión voluntaria y busca acompañar la mejora de la gestión ambiental de los establecimientos industriales y de servicios. Para informarse acerca del Programa podrá escribir a produccionlimpia@buenosaires.gob.ar o llamar al 4601-2708/2823.');	

$pdf->MultiCell(189, 4, $cuerpo2, 0,'J',0);


$pdf->Write(4,utf8_decode('
	                                                     


	                                                      QUEDA USTED DEBIDAMENTE NOTIFICADO

Buenos Aires,                         de 2017                                                  
                                                                                                                                                                                                                                                        
    
                                                                                                                                                           Firma y Sello                                                                                                                




En...................................................a los...............días del mes de.................de..............., siendo las.......................hs. me constituí en el domicilio precedentemente indicado, requiriendo la presencia del
Sr./Sra...................................................................................respondiendo a mis llamados una persona que dijo
ser..........................................................................................., D.N.I. Nº ..................................
manifestó que el requerido    SI     NO   vive allí, por ello: 

a)procedí a notificarle haciéndole entrega del duplicado de la presente de igual tenor, a quien previa lectura la recibió, firmando al pie como consta de ello.  

b)la cédula no pudo ser entregada por..................................................................................................................
....................................................................................................................................................................................
Por ello, conforme al Art. Nº 61 del Decreto 1510/97 procedí a fijar en la puerta de acceso duplicado de la presente.
'));
header("Content-Transfer-Encoding", "binary");
header('Cache-Control: maxage=3600'); 
header('Pragma: public');
 
//enviamos el documento creado con un nombre nuevo y forzamos su descarga.
//$pdf->Output('recibos.pdf', 'D');
$pdf->Output('recibos.pdf', 'I');


?>