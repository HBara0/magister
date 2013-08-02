<?php
ini_set('max_execution_time', "-1");
//echo "Starting...".PHP_EOL.'<Br>';
$currentscriptname = basename($_SERVER['SCRIPT_NAME']);
$currentscriptfolder = substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - strlen($currentscriptname));
require '../inc/init.php';



$config['ip'] = "70.38.119.243";
$config['port'] = 8804;
$config['max_clients'] = 20;
$config['timeout'] = 600; //seconds
$client = array();
clearlog();
echo ' Server '.$config['ip'].' on port '.$config['port'].' started...'."\n";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $config['timeout'], 'usec' => 0));
socket_bind($socket, $config['ip'], $config['port']) or die('Could not bind to address');
echo ' Server Initialized...'."\n";
/* Start Listening for connections */
socket_listen($socket);
echo ' Server is now listening...'."\n";
$fh = fopen('socketdata.txt', 'w');

$gotsomething = false;
while(true && !$gotsomething) {
	//$data = ' connection interrupted';
	$read = array();
	$read[0] = $socket;

	for($i = 0; $i < $config['max_clients']; $i++) {
		if($client[$i]['socket'] != null) {
			$read[$i + 1] = $client[$i]['socket'];
		}
	}

	$ready = socket_select($read, $write, $except, null);

	if(in_array($socket, $read)) {
		for($i = 0; $i < $config['max_clients']; $i++) {
			if($client[$i]['socket'] == null) {
				$client[$i]['socket'] = socket_accept($socket);
				break;
			}
			elseif($i == $config['max_clients'] - 1) {
				echo 'Error: too many clients';
			}
		}

		if(--$ready <= 0) {
			continue;
		}
	}

	for($i = 0; $i < $config['max_clients']; $i++) {
		if(in_array($client[$i]['socket'], $read)) {
			socket_getpeername($client[$i]['socket'], $ip);
			$input = socket_read($client[$i]['socket'], 1024);

			if($input == null) {
				//socket_close($client[$i]['socket']);
				unset($client[$i]['socket']);
				continue;
			}

			$data = 'ip:'.$ip.PHP_EOL.'recv:'.bin2hex(trim($input));

			if($data == 'exit') {
				socket_close($client[$i]['socket']);
				unset($client[$i]['socket']);
			}
			else {
				if($client[$i]['socket'] != null) {
					if(!empty($data)) {
						fwrite($fh, $data."\n");
						$gotsomething = parse_one_location($input);
						$hex_input = str_split(bin2hex($data), 2);
						socket_write($client[$i]['socket'], hex2bin('2929210005'.$hex_input[10].$hex_input[2].'000D'));
						//fwrite($fh,'sent:'.'2929210005'.$hex_input[10].$hex_input[2].'000D'."\n");
					}
				}
			}
		}
		else {
			if($client[$i]['socket'] != null) {
				socket_close($client[$i]['socket']);
				unset($client[$i]['socket']);
			}
		}
	}
}
fclose($fh);




/* parse function --START */
function parse_one_location($line) {
	$label = '';
	$return = false;
	$hexdat = '';
	$value = '';
	$split_length = 2;

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
	$delimiter = array('timeline' => array(9 => '/', 10 => '/', 11 => ' ', 12 => ':', 13 => ':'),
			'lattitude' => array(15 => '.'),
			'longitutde' => array(19 => '.')
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
			$location['timeLine'] = date("d-m-Y H:i:s", strtotime($timeline.'/'.$timeline.'/'.$timeline.' '.$timeline.':'.$timeline.':'.$timeline));
			$location['lat'] = $lat = get_patterndata($packet_result, $packet_pattern['lat'], $delimiter['lattitude']);
			$location['long'] = $lat = get_patterndata($packet_result, $packet_pattern['long'], $delimiter['longitutde']);
			$location['speed'] = get_patterndata($packet_result, $packet_pattern['speed'], $delimiter['speed']);
			$location['speed'] = get_patterndata($packet_result, $packet_pattern['speed'], $delimiter['speed']);
			$location['direction'] = get_patterndata($packet_result, $packet_pattern['direction'], $delimiter['direction']);
			$location['antenna'] = get_patterndata($packet_result, $packet_pattern['antenna'], $delimiter['antenna']);
			$location['fuel'] = get_patterndata($packet_result, $packet_pattern['fuel'], $delimiter['fuel']);
			$location['vehiclestate'] = get_patterndata($packet_result, $packet_pattern['vehiclestate'], $delimiter['vehiclestate']);
			$location['otherstate'] = get_patterndata($packet_result, $packet_pattern['otherstate'], $delimiter['otherstate']);
			$asst = new Assets();
			$asst->record_location($location);

			/* Record Location ---END */
		}
	}
	return true;
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

function clearlog() {
	$fh2 = fopen('logfile.html', 'w');
	fwrite($fh2, date('Y-m-d H:i:s', TIME_NOW)."\n");
	fclose($fh2);
}

function logsomething($msg) {
	$fh2 = fopen('logfile.html', 'a');
	fwrite($fh2, $msg."\n");
	fclose($fh2);
}

?>