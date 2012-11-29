<?php
$currentscriptname = basename($_SERVER['SCRIPT_NAME']);
$currentscriptfolder = substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-strlen($currentscriptname));
require $currentscriptfolder.'../inc/init.php';

$config['ip'] = "10.0.0.66";
$config['port'] = 8805;
$config['max_clients'] = 20;
$config['timeout'] = 600; //seconds
$client = array();

echo ' Server '.$config['ip'].' on port '.$config['port'].' started...'."\n";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $config['timeout'], 'usec' => 0));

socket_bind($socket, $config['ip'], $config['port']) or die('Could not bind to address');
echo ' Server Initalized...'."\n";
/* Start Listening for connections */
socket_listen($socket);
echo ' Server is now listening...'."\n";

while(true) {
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
			$input = socket_read($client[$i]['socket'], 1024);

			if($input == null) {
				socket_close($client[$i]['socket']);
				unset($client[$i]['socket']);
				continue;
			}

			$data = trim($input);
			if($data == 'exit') {
				socket_close($client[$i]['socket']);
				unset($client[$i]['socket']);
			}
			else {
				if($client[$i]['socket'] != null) {
					$data = $db->escape_string($core->sanitize_inputs($data, array('removetags' => true)));
					if(!empty($data)) {
						$lastentry = $db->fetch_assoc($db->query('SELECT isodid, timeLine FROM '.Tprefix.'itservices_onlinedevices WHERE deviceName="'.$data.'" ORDER BY timeLine DESC LIMIT 0, 1'));
						if(is_array($lastentry)) {
							if((time() - $lastentry['timeLine']) > (5*60)) {
								$db->insert_query('itservices_onlinedevices', array('deviceName' => $data, 'timeLine' => time()));
							}
							else
							{
								$db->update_query('itservices_onlinedevices', array('timeLine' => time()), 'isodid='.$lastentry['isodid']);
							}
						}
						else {
							$db->insert_query('itservices_onlinedevices', array('deviceName' => $data, 'timeLine' => time()));
						}
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
socket_close($socket);
?>
