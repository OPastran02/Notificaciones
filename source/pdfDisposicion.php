<?php 

$dispo = $_GET['dispo'];

//if (file_exists('http://192.168.163.102:85/notificaciones/web/uploads/Inspecciones/CH'.$checklist.'.pdf')) {


$mi_pdf2 = 'http://192.168.162.8/notificaciones/web/uploads/Notificaciones/'.$dispo.'.pdf';


if(file_exists('c:\\wamp64/www/notificaciones/web/uploads/Notificaciones/'.$dispo.'.pdf')){
	header('Content-type: application/pdf');
	readfile($mi_pdf2) or die("File not found.");	
}else{
	echo "el archivo no existe";
}




?>