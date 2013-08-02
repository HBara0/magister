<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: sockettest2.php
 * Created:        @tony.assaad    Jul 18, 2013 | 10:10:52 AM
 * Last Update:    @tony.assaad    Jul 18, 2013 | 10:10:52 AM
 */
ini_set('max_execution_time', 6000);
echo "Starting...".PHP_EOL.'<Br>';
$currentscriptname = basename($_SERVER['SCRIPT_NAME']);
$currentscriptfolder = substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - strlen($currentscriptname));
require '../inc/init.php';


$config['ip'] = "70.38.119.243";
$config['port'] = 8804;
//$config['port'] = ceil(substr(microtime(),2,4));   /*auto generated port fot testing*/
$config['max_clients'] = 20;
$config['timeout'] = 600; //seconds


$file = fopen('socketdata.txt', 'w');
$read = array();
$read[0] = $socket;
//$ready = socket_select($read, $write, $except, null);

for($i = 0; $i < $config['max_clients']; $i++) {
	//socket_getpeername($client[$i]['socket'], $ip);
	//$input = socket_read($client[$i]['socket'], 1024);
	$data = 'ip:'.$ip.PHP_EOL.'recv:'.bin2hex(trim($input));
	parse_one_location($input);
	echo'<br>';
}


/* parse function --START */
function parse_one_location($line) {
	$label = '';
	$return = false;
	$hexdat = '';
	$value = '';

	$split_length = 2;
	$line = '292980002839316410130718075631033537210352946500090212ffff000002fc00000005640812000034900d';
	$packet_result = str_split($line, $split_length);

	$packet_pattern = array('trailerstart' => array(0, 1),
			'command' => 2,
			'length' => array(3, 4),
			'deviceId' => array(5, 6, 7, 8),
			'timeline' => array(9, 10, 11, 12, 13, 14),
			'lat' => array(15, 16, 17, 18),
			'long' => array(19, 20, 21, 22),
			'speed' => array(23, 24),
			'direction' => array(25, 26),
			'antenna' => 27,
			'fuel' => array(28, 29, 30),
			'vehiclestate' => array(31, 32, 33, 34),
			'otherstate' => array(35, 36, 37, 38, 39, 40, 41, 42),
			'checkcode' => 43,
			'trailerend' => 44
	);
	$delimiter = array(
			'timeline' => array(9 => '-', 10 => '-', 11 => ' ', 12 => ':', 13 => ':'),
			'lattitude' =>  array(2 => '.'),
			'longitutde' => array(2 => '.')
	);

	if(get_patterndata($packet_result, $packet_pattern['trailerstart']) == '2929') {
		$command = get_patterndata($packet_result, $packet_pattern['command']);
		$length = get_patterndata($packet_result, $packet_pattern['length']);
		$termid = get_patterndata($packet_result, $packet_pattern['deviceId']);
		$timeline = get_patterndata($packet_result, $packet_pattern['timeline'], $delimiter['timeline']);

		if($command == 80) {
			/* Recoed Location ---START */
			$location['deviceId'] = $termid;
			$checksumdata = get_patterndata($packet_result, $packet_pattern['checkcode'], $delimiter['checkcode']);
			$location['timeLine'] = get_patterndata($packet_result, $packet_pattern['timeline'], $delimiter['timeline']);
			$location['lat'] = parse_data_bystrpos($packet_result, $packet_pattern['lat'], $delimiter['lattitude'], true);
			$location['long'] = parse_data_bystrpos($packet_result, $packet_pattern['long'], $delimiter['longitutde'], true);
			$location['speed'] = get_patterndata($packet_result, $packet_pattern['speed'], $delimiter['speed']);
			$location['speed'] = get_patterndata($packet_result, $packet_pattern['speed'], $delimiter['speed']);
			$location['direction'] = get_patterndata($packet_result, $packet_pattern['direction'], $delimiter['direction']);
			$location['antenna'] = get_patterndata($packet_result, $packet_pattern['antenna'], $delimiter['antenna']);
			$location['fuel'] = get_patterndata($packet_result, $packet_pattern['fuel'], $delimiter['fuel']);
			$location['vehiclestate'] = get_patterndata($packet_result, $packet_pattern['vehiclestate'], $delimiter['vehiclestate']);
			$location['otherstate'] = get_patterndata($packet_result, $packet_pattern['otherstate'], $delimiter['otherstate']);
			print_r($location);
			//$asst = new Assets();
			//$asst->record_location($location);

			/* Record Location ---END */
		}
	}
	return true;
}

function parse_data_bystrpos($dataresult = array(), $pattern = array(), $delimiters = array(), $islocation=false) {
	foreach($pattern as $val) {
		$pattern_val .= $dataresult[$val];
	}
	
	if($islocation == true) {
		$sign = substr($pattern_val, 0, 1);
		$pattern_val = substr($pattern_val, 1, strlen($pattern_val));
	}
	
	foreach($delimiters as $pos => $delim) {
		$pattern_val = substr($pattern_val, 0, $pos).$delim.substr($pattern_val, $pos, strlen($pattern_val));
	}
	
	if($islocation == true) { 
		//$pattern_val = $sign.$pattern_val;
	}
	return $pattern_val;
}

/* parse function --END */
function get_patterndata($dataresult = array(), $pattern = array(), $delimiters = array()) {
	if(is_array($pattern)) {
		foreach($pattern as $val) {
			if(is_array($delimiters)) {
				$pattern_val .=$dataresult[$val].$delimiters[$val];
			}
			else {
				$pattern_val .=$dataresult[$val].$delimiters;
			}
		}
	}
	else {
		$pattern_val = $dataresult[$pattern];
	}

	return $pattern_val;
}
?>


