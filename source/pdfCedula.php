<?php 

$cedula = $_GET['cedula'];

//if (file_exists('http://192.168.163.102:85/notificaciones/web/uploads/Inspecciones/CH'.$checklist.'.pdf')) {




$mi_pdf2 = 'http://'.$request->server->get('SERVER_ADDR').'/notificaciones/web/uploads/Notificaciones/CE'.$cedula.'.pdf';

if(file_exists('/opt/lampp/htdocs/Notificaciones/web/uploads/Notificaciones/CE'.$cedula.'.pdf')){
	header('Content-type: application/pdf');
	readfile($mi_pdf2) or die("File not found.");	
}else{
	echo "el archivo no existe";
}

?>