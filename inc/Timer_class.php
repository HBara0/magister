<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Export Timer Class
 * $id: Timer_class.php
 * Created: 	@zaher.reda		September 9, 2009 | 03:30 PM
 * Last Update: @zaher.reda 	September 9, 2009 | 03:30 PM
 */

class Timer {
	private $start, $end;
	private $totaltime;
	
	public function __construct() {
		$this->add();
	}
	
	public function add() {
		if(!$this->start) {
			$microtime = explode(' ', microtime());
			$this->start = $microtime[1] + $microtime[0];
		}
	}
	
	public function stop() {
		if($this->start) {
			$microtime2 = explode(' ', microtime());
			$this->end = $microtime2[1] + $microtime2[0];
			$this->totaltime = $this->end - $this->start;
		}
	}
	
	public function get() {
		return number_format($this->totaltime, 7);
	}
	
	public function reset_timer() {
		$start = $end = $totaltime = '';
	}
}
?>