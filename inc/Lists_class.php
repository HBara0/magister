<?php
class Lists {
	protected $displayinfo = array();
	protected $data = array(), $multipage_data = array();
	
	public function __construct($data, array $fields, array $displayinfo, $is_query=true, $orderby='', $limit='') {
		if($is_query == 1) {
			$this->data = $this->get_data_from_query($data, $fields, $orderby, $limit);
		}
		else
		{
			$this->$data = $this->get_data_from_array($data, $fields);
		}
		$this->set_displayinfo($displayinfo);
	}
	
	protected function get_data_from_query($table, $fields, $orderby='', $limit='') {
		global $db;
		
		foreach($fields as $key => $val) {
			$fields_query .= "{$comma}".$db->escape_string($val)."";
			$comma = ", ";			
		}
		
		if(empty($orderby)) {
			$orderby_query = "ORDER BY $fields[0] ASC";
		}
		else
		{
			$orderby_query = "ORDER BY ".$db->escape_string($orderby);
		}
		
		if(empty($limit)) {
			$limit_query = "LIMIT 0, 10";
			$this->multipage_data['limit'] = 10;
		}
		else
		{
			$limit_query = "LIMIT ".$db->escape_string($limit);
			$limits = explode(", ", $limit);
			$this->multipage_data['limit'] = $limits[1];
		}
		echo $limit_query."<br />";
		$i = 0;
		$query = $db->query("SELECT {$fields_query} FROM {$table} {$orderby_query} {$limit_query}");
		while($fetch = $db->fetch_array($query)) {
			foreach($fetch as $key => $val) {
				if(!is_numeric($key)) {	
					$current_key = array_keys($fields, $key);				
					if(is_numeric($current_key[0])) {					
						$data[$i][$key] = $val; 
					}
					else
					{
						$data[$i][$current_key[0]] = $val;
					}
				}
			}
			$i++;
		}
		
		$this->multipage_data['total'] = $db->fetch_field($db->query("SELECT COUNT(*) as countrows FROM {$table}{$where}"), "countrows");
		
		return $data;
	}
	
	protected function get_data_from_array(array $data, $fields) {
		if(!$this->is_associative($data)) {
			foreach($data as $key) {
				$data[$i][array_keys($fields, $key)] = $data[$key];
			}
		}
		return $data;
	}
	
	protected function is_associative($array) {
		foreach($array as $key) {
			if(is_numeric($key)) {
				return false;
			}
		}
	}
	
	protected function set_displayinfo(array $info) {
		$this->displayinfo = $info;
	} 
	
	public function parse_list() {
		if($this->displayinfo['type'] == 'list')
		{
			if(count($this->data[0]) == 1) {
				if($this->displayinfo['listtype']) {
					$listtype = " type='".$this->displayinfo['listtype']."'";
				}
				
				if($this->displayinfo['mainclass']) {
					$listclass = " class='".$this->displayinfo['mainclass']."'";
				}
				
				if($this->displayinfo['id']) {
					$listid = " id='".$this->displayinfo['id']."'";
				}
				
				$list = "<ul{$listtype}{$listclass}{$listid}>\n";
				foreach($this->data as $key => $val) {				
					foreach($val as $k => $v) {
						$list .= "<li>{$v}</li>\n";
					}
				}
				$list .= "</ul>\n";
			}
			else
			{
				echo "Array must be single column";
				exit;
			}
		}
		else
		{
			if($this->displayinfo['mainclass']) {
				$table_class = " class='".$this->displayinfo['mainclass']."'";
			}
			
			if($this->displayinfo['id']) {
					$tableid = " id='".$this->displayinfo['id']."'";
			}
				
			$list = "<table{$table_class}{$tableid} border='1'>\n";
			
			$list .= "<tr>";
			foreach($this->data[0] as $k => $v) 
			{
				$list .= "<td><strong>{$k}</strong></td>";
			}	
			$list .= "</tr>\n";
			
			foreach($this->data as $key => $val) {
				$list .= "<tr>";
				foreach($val as $k => $v) {
					$list .= "<td>".$v."</td>";
				}
				$list .= "</tr>\n";				
			}
			$list .= "</table>";
		}
		return $list;
	}
	
	public function multipages($link) {
		$numberpages = round($this->multipage_data['total']/$this->multipage_data['limit']);

		for($i=1;$i<=$numberpages;$i++) {			
			if(!$prev) {
				$prev = 0;
			}

			$ref = str_replace("(1)", $prev, $link);
			$ref = str_replace("(2)", $this->multipage_data['limit'], $ref);
			$prev = ($prev + ($this->multipage_data['limit']));

			echo "<a href='{$ref}'>".$i."</a> ";
		}
	}
	
	public function sortdata($link, array $fields, array $methods) {
		foreach($fields as $key => $val) {
		
		}
		
		$this->parse_list();
	}
}
?>