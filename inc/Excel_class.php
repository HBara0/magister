<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Export Excel Class
 * $id: Excel_class.php
 * Created: 	@zaher.reda
 * Last Update: @zaher.reda 	August 4, 2009 | 05:14 PM
 */
 
class Excel {
	private $data = array();
	public function __construct($type, array $data) {
		if($type == 'query') {
			$this->query_data($data);
		}
		else
		{ 
			$this->data = $data;
		}
		$this->generate_file();
	}
	
	private function query_data($query_settings) {
		global $db;
		
		if(isset($query_settings['table'], $query_settings['select'])) {
			$query_settings['table'] = $db->escape_string($query_settings['table']);
			
			$query_settings['select'] = $db->escape_string(implode(",", $query_settings['select']));
			
			if(isset($query_settings['where'])) {
				$where = " WHERE ".$db->escape_string($query_settings['where']);
			}
			
			if(isset($query_settings['sort'])) {
				$order = " ORDER BY ".$db->escape_string($query_settings['sort']['order'])." ".$db->esape_string($query_settings['sort']['by']);
			}
			$query = $db->query("SELECT {$query_settings[select]} FROM ".Tprefix."{$query_settings[table]}{$where}{$order}");
			$i = 0;
			while($this->data[$i] = $db->fetch_arrary($query)) {
				$i++;
			}
		}
		else
		{
			return false;
		}
	}
	
	private function generate_file() {
		$num_rows = count($this->data); 
		$num_cols = count($this->data[0]);
		
		$header = "<Row ss:StyleID='s1'>";
		if(is_array($this->data[0])) {
			foreach($this->data[0] as $key => $val) {
				$header .= "<Cell><Data ss:Type='String'>{$val}</Data></Cell>\n";
			}
		}
		$header .= "</Row>\n";
		
		for($i=1;$i<$num_rows;$i++) {
			$row = "<Row>";
			if(is_array($this->data[$i])) {
				foreach($this->data[$i] as $key => $val) {
					if(empty($val)) {
						$val = "<Cell><Data ss:Type='String'>&nbsp;</Data></Cell>\n";
					}
					else
					{
						if(is_numeric($val)) {
							$data_type = "Number";
						}
						else
						{
							$data_type = "String";
						}
						$val = "<Cell><Data ss:Type='{$data_type}'>".utf8_encode(htmlspecialchars($val))."</Data></Cell>\n";
					}
					$row .= $val;
				}
				$content .= $row."</Row>\n"; 
			}
		}
		
		$filename = "OCOS";
		
		if(preg_match("/module=([A-Za-z\/]+)/i", $_SERVER['HTTP_REFERER'], $ref)) {
			$ref = explode("/", $ref[1]);
			$filename .= "-".ucfirst($ref[0]);
		}

		header("Content-type: application/vnd.ms-excel"); 
		header("Content-Disposition: attachment; filename={$filename}.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");

		print "<?xml version='1.0'?>\n";
  		print "<?mso-application progid='Excel.Sheet'?>\n";
		print "<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'
			  xmlns:o='urn:schemas-microsoft-com:office:office'
			  xmlns:x='urn:schemas-microsoft-com:office:excel'
			  xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'
			  xmlns:html='http://www.w3.org/TR/REC-html40'>";
		 print "<DocumentProperties 
				 xmlns='urn:schemas-microsoft-com:office:office'>
				  <Author>OCOS</Author>
				  <Created>".date("Y-m-d", time())."</Created>
				  <Company>ORKILA</Company>
			  </DocumentProperties>";
			  
		print "<ExcelWorkbook 
     			xmlns='urn:schemas-microsoft-com:office:excel'>
					<WindowHeight>8535</WindowHeight>
					<WindowWidth>12345</WindowWidth>
					<WindowTopX>480</WindowTopX>
					<WindowTopY>90</WindowTopY>
					<ProtectStructure>False</ProtectStructure>
  					<ProtectWindows>False</ProtectWindows>
				</ExcelWorkbook>";
  
  		print "<Styles>
					<Style ss:ID='s1'>
  						<Font ss:Bold='1'/>
						<Alignment ss:Horizontal='Center'/>
  					</Style>
				</Styles>";
				
		print "<Worksheet ss:Name='{$filename}'>
					<Table ss:ExpandedColumnCount='{$num_cols}' ss:ExpandedRowCount='{$num_rows}' x:FullColumns='1' x:FullRows='1'>
						<Column ss:Index='1' ss:AutoFitWidth='1'/>
						{$header}\n
						{$content}
					</Table>
					<WorksheetOptions xmlns='urn:schemas-microsoft-com:office:excel'></WorksheetOptions>
			</Worksheet>
		</Workbook>";
	}
}
?>