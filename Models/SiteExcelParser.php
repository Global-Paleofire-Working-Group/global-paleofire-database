<?php

/* 
 * fichier \Models\SiteExcelParser.php
 * 
 */

include_once('Site.php');

/**
 * Class SiteExcelParser
 *
 */
class SiteExcelParser 
{
    private $_column_names;
	
	private $_rows;
	
	private $_nb_rows;
	
	/**
     * Constucteur de la classe
     **/
    public function __construct($sheet)
    {
		$this->_column_names = array();
		$this->_rows = array();
		$x = 1;
		while($x <= $sheet['numRows']) {
			if($x == 1 ){
				$y = 1;
				while($y <= $sheet['numCols']) {
					$cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
					$this->_column_names[$y] = $cell;
					$y++;
				}
							
			}
			else
			{
				$y = 1;
				while($y <= $sheet['numCols']) {
					
					$cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
					$this->_rows[$x][$y] = $cell;
					$y++;
				} 
					
			}
			$x++;	
		}
		$this->_nb_rows = $x-1;
	}
	
	public function prepare()
	{
		$sites = array();
		$nb_row = 1;
		while ($nb_row <= $this->_nb_rows){
			$col=1;
			if($nb_row!=2){
				$new_site = new Site();
				while ($col <= count($this->_column_names)){
					$column_name = $this->_column_names[$col];
					if(isset($this->_rows[$nb_row][$col])){
						$value =  $this->_rows[$nb_row][$col];
						$new_site->prepareAndSetFieldValue($column_name, $value);
					}
					//else on n'a pas d'enregistrement dans le fichier excel
					
					$col++;
				}
				if($new_site->isDefined()){
					$sites[] = $new_site;
				}
			}
			$nb_row++;
		}
		return $sites;
	}
}