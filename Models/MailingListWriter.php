<?php


/* 
 * fichier \Models\MailingListWriter.php
 * 
 */



/**
 * Class MailingListWriter
 *
 */
class MailingListWriter 
{
	
	private $_emails;
	private $_output;
	
	/**
     * Constucteur de la classe
     **/
    public function __construct()
    {
		$this->_emails = array();
		$this->_output="";
	}
	
	public function prepare($list_emails)
	{
		foreach ($list_emails as $value) {
			if(!empty($value) && $value!=""){
				$this->_emails[]=$value;
			}
		}
	}

	public function write($file_name=NULL){
		$name = "GCD_mailingList".date("Ymd").".csv";
		if($file_name!=NULL){
			$name = $file_name.".csv";
		}
    
		ob_clean();
		header("Content-type: text/csv");  
		header("Cache-Control: no-store, no-cache");  
		header('Content-Disposition: attachment; filename="'.$name.'"');  
		$outstream = fopen("php://output",'w');  
		  
		fputcsv($outstream, $this->_emails, ',', '"');    
		$this->_output = ob_get_contents();
		ob_clean();
		fclose($outstream); 
		ob_clean();
		echo $this->_output;
		exit;
	}
}