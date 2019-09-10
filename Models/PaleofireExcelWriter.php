<?php

/* 
 * fichier \Models\PaleofireExcelWriter.php
 * 
 */

/** Include path **/
ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
include 'PHPExcel.php';


/**
 * Class PaleofireExcelWriter
 *
 */
class PaleofireExcelWriter 
{
    private $_objPHPExcel;
	
	
	/**
     * Constucteur de la classe
     **/
    public function __construct()
    {
		$this->_objPHPExcel = new PHPExcel();
	}
	
	public function prepare($array_values)
	{
		
		$this->_objPHPExcel->getProperties()->setCreator("Global Paleofire Work Group");
		$this->_objPHPExcel->getProperties()->setLastModifiedBy("Global Paleofire Work Group");
		$this->_objPHPExcel->getProperties()->setTitle("Office 2007 XLSX GPWG Paleofire Database");
		$this->_objPHPExcel->getProperties()->setSubject("GPWG Paleofire Database");
		$this->_objPHPExcel->getProperties()->setDescription("Paleodire Database");


		// Add some data
		$this->_objPHPExcel->setActiveSheetIndex(0);
		$this->_objPHPExcel->getActiveSheet()->SetCellValue('A1', $array_values[1]);
		/*$this->_objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
		$this->_objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
		$this->_objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');*/

		// Rename sheet
		$this->_objPHPExcel->getActiveSheet()->setTitle('GPWG Export');

		

	}
	
	public function write(){
		// Save Excel 2007 file
		// We'll be outputting an excel file
		$name = "export_gpwg_database_".date("Ymd").".xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$name.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, 'Excel2007');
		//$objWriter = new PHPExcel_Writer_Excel2007($this->_objPHPExcel);

		$objWriter->save($name);	
		//readfile($name);
		unlink($name);
	}
}