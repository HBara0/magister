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
	$read = array();
	$read[0] = $socket;

	for($i = 0; $i < $config['max_clients']; $i++) {
		if($client[$i]['socket'] != null) {
			$read[$i + 1] = $client[$i]['socket'];
			$data = 'start dataaa'; 
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

			//$data = 'ip:'.$ip.PHP_EOL.'recv:'.bin2hex(trim($input));

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

function getsome($howmany, $string = null) {
	static $data;
	if(!isset($data)) {
		if(isset($string)) {
			$data = $string;
		}
		else {
			return null;
		}
	}
	else {
		if(isset($string)) {
			$data = $string;
		}
	}
	$return = substr($data, 0, $howmany);
	$data = substr($data, $howmany, strlen($data) - $howmany);
	return $return;
}

function parse_one_location($line) {
	$label = '';
	$return = false;
	$hexdat = '';
	$value = '';
	for($i = 0; $i < strlen($line); $i++) {
		if($i == 0) {
			if(bin2hex($line[$i]) != '29')
				break;
			$hexdat.='<font color=green>';
		}
		if($i == 2)
			$hexdat.='<font color=red><b>';
		if($i == 3) {
			$value.=str_pad(hexdec(bin2hex($line[$i]).bin2hex($line[$i + 1])), 5, '0', STR_PAD_LEFT);
			$hexdat.='<font color=blue>';
		}
		else {
			$value.='   ';
		}
		if($i == 5) {
			$hexdat.='<font color=purple>';
		}

		$label.=str_pad($i, 2, '0', STR_PAD_LEFT).'<font color=gray style="font-weight:normal;">|</font>';
		$hexdat.=bin2hex($line[$i]).'<font color=black style="font-weight:normal;">|</font>';
		if($i == 1)
			$hexdat.='</font>';
		if($i == 2)
			$hexdat.='</b></font>';
		if($i == 4)
			$hexdat.='</font>';
		if($i == 8)
			$hexdat.='</font>';
	}
	logsomething('<pre><font color=gray><u>'.$label."</u></font>\n".$hexdat."\n".$value.'</pre>');

	//   Packet Structure
	//   Package Trailer (0x29 0x29)|Command Word (0x8E)|Package Length (0x00 0x1B)|Terminal ID|LOCATION DATA|Check Code|Package Trailer (0x0D)
	//   Location Data: yymmddhhmmss llll llll ssdd st fuel1 fuel2 fuel3 st1st2st3st4st5
	getsome(0, $line);
	if(bin2hex($start = getsome(2)) == '2929') {
		logsomething('start='.bin2hex($start).'<br>');
		$command = getsome(1);
		logsomething('command= '.bin2hex($command).'<br>');
		$length = hexdec(bin2hex(getsome(2)));
		logsomething('length= '.$length.'<br>');
		$termid = hexdec(bin2hex(getsome(4)));
		logsomething('terminal= '.$termid.'<br>');
		$checksumdata = substr($line, 3, 6);
		logsomething('checksumdata='.bin2hex($checksumdata).'<Br>');
		if(bin2hex($command) == "80") {
			$location['pin'] = $termid;
			$location['timeLine'] = date("d-m-Y H:i:s", strtotime('20'.bin2hex(getsome(1)).'/'.bin2hex(getsome(1)).'/'.bin2hex(getsome(1)).' '.bin2hex(getsome(1)).':'.bin2hex(getsome(1)).':'.bin2hex(getsome(1))));
			$lat = bin2hex(getsome(4));
			$location['lat'] = (float)(substr($lat, 0, 3).'.'.substr($lat, 3, strlen($lat) - 3));
			$long = bin2hex(getsome(4));
			$location['long'] = (float)(substr($long, 0, 3).'.'.substr($long, 3, strlen($long) - 3));
			$location['speed'] = bin2hex(getsome(2));
			$location['direction'] = bin2hex(getsome(2));
			$location['antenna'] = hexdec(bin2hex(getsome(1)));
			$location['fuel'] = hexdec(bin2hex(getsome(1)));
			getsome(2);
			$location['vehiclestate'] = hexdec(bin2hex(getsome(4)));
			$location['otherstate'] = hexdec(bin2hex(getsome(8)));
			logsomething('<pre>'.print_r($location, true).'</pre>');
			$return = true;
			//$asst = new Asset();
			//$asst->record_location($location);
		}
		$checksum = $checksumdata[0];
		for($i = 1; $i < strlen($checksumdata); $i++) {
			logsomething('chk: '.bin2hex($checksum).' ^ '.bin2hex($checksumdata[$i]).' = '.bin2hex($checksum ^ $checksumdata[$i]).'<Br>');
			$checksum^=$checksumdata[$i];
		}
		logsomething('calculated checksum= '.bin2hex($checksum).'<br>');
	}
	logsomething('<br>');

	return $return;
}

?>