<?php 

$checklist = $_GET['checklist'];

$mi_pdf2 = '\\\\192.168.162.1/dgconta/01.Check List/CH'.$checklist.".pdf";
//$mi_pdf2 = 'http://192.168.162.8/notificaciones/web/uploads/Inspecciones/CH'.$checklist.'.pdf';


header('Content-type: application/pdf');
readfile($mi_pdf2) or die("File not found.");


?>