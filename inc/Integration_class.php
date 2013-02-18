<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Integration class to manage integration with 3rd party applications
 * $id: integration.php
 * Created:        @zaher.reda    Feb 18, 2013 | 12:03:10 PM
 * Last Update:    @zaher.reda    Feb 18, 2013 | 12:03:10 PM
 */

class Integration {
	protected $period = array('from' => '30 minutes ago', 'to' => 'tomorrow');
	protected $affiliates_index = array();
	protected $f_db;
	protected $foreign_system;
	
	private $status = 0;
	
	public function __construct($foreign_system, array $database_info) {
		if(!isset($database_info['engine'])) {
			$this->status = 702;
			return false;
		}
		
		if(!empty($foreign_system)) {
			$this->set_foreign_system($foreign_system);
		}
		else
		{
			$this->status = 100601;
			return false;
		}	

		return $this->connect($database_info);
	}
	
	private function connect(array $database_info) {
		switch($database_info['engine']) {
			case 'pg':
			case 'pgsql':
			case 'postgre':
			case 'postgreSQL':
				$this->f_db = new PostgreSQLConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
				break;
			case 'mySQL':
				$this->f_db = new MySQLConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
				break;
			case 'mySQLi':
				$this->f_db = new MySQLiConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
				break;
			default: $this->status = 702; return false;
		}
					
		return true;
	}
	
	public function set_sync_interval(array $sync_perid) {
		if(!empty($sync_perid['from'])) {
			$this->period['from'] = $sync_perid['from'];
		}
		
		if(!empty($sync_perid['to'])) {
			$this->period['to'] = $sync_perid['to'];
		}
		return true;
	}

	private function set_foreign_system($foreign_system) {
		$this->foreign_system = intval($foreign_system);
	}
	
	public function match_affiliates_ids(array $ids) {
		foreach($ids as $fkey => $key) {
			if(empty($key)) {
				continue;
			}
			$this->affiliates_index[$fkey] = $key;
		}
		return true;
	}
}
?>
