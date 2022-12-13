<?php
namespace CoreBundle\Logic;

class Excel
{
    public static function excelToArray($nombreArchivo,$readDataOnly = true)
    {
        $XLFileType = \PHPExcel_IOFactory::identify($nombreArchivo); 
        $objReader = \PHPExcel_IOFactory::createReader($XLFileType); 

        $objPHPExcel = $objReader->load($nombreArchivo);  
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $array=array();
        $contador=0;
        $lastColumn = $objWorksheet->getHighestColumn();
        foreach($objWorksheet->getRowIterator() as $rowIndex => $row) {
            array_push($array, $objWorksheet->rangeToArray('A'.$rowIndex.':'.$lastColumn.$rowIndex));
            $contador++;
        }
        return $array;      

    }

}
?>