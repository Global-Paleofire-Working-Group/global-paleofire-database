<?php


/* 
 * fichier \Models\PaleofireCSVWriter.php
 * 
 */


/**
 * Class PaleofireCSVWriter
 *
 */
class PaleofireCSVWriter 
{
	
	private $_lines;
	private $_columns;
	private $_output;
	
	/**
     * Constucteur de la classe
     **/
    public function __construct()
    {
		$this->_lines = array();
		$this->_columns = array();
		$this->_output="";
	}
	
	public function prepare($array_values)
	{
		$this->_columns = end($array_values);
		foreach ($array_values as $value) {
			if($value!=$this->_columns){
				$this->_lines[]=$value;
			}
		}
	}

	public function write($file_name=NULL){
		$name = "export_gpwg_database_".date("Ymd").".csv";
		if($file_name!=NULL){
			$name = $file_name.".csv";
		}
    
		ob_clean();
		header("Content-type: text/csv");  
		header("Cache-Control: no-store, no-cache");  
		header('Content-Disposition: attachment; filename="'.$name.'"');  
		$outstream = fopen("php://output",'w');  
		  
		fputcsv($outstream, $this->_columns, ',', '"');  
		foreach( $this->_lines as $row )  
		{  
			fputcsv($outstream, $row, ',', '"');  
		}  
		$this->_output = ob_get_contents();
		ob_clean();
		fclose($outstream); 
		ob_clean();
		echo $this->_output;
		exit;
	}
}