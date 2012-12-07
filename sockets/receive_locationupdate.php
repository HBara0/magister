<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: receive_locationupdate.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 4:05:15 PM
 * Last Update:    @alain.paulikevitch    Nov 26, 2012 | 4:05:15 PM
 */

$currentscriptname = basename($_SERVER['SCRIPT_NAME']);
$currentscriptfolder = substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - strlen($currentscriptname));
require $currentscriptfolder.'../inc/init.php';

/**
  $socket = stream_socket_server("udp://0.0.0.0:8888", $errno, $errstr, STREAM_SERVER_BIND);
  if(!$socket) {
  die("$errstr ($errno)");
  }
  //$pkt = stream_socket_recvfrom($socket, 1500, 0, $peer);
  //echo ($pkt ? "true" : "false").PHP_EOL;
  $msg = '';
  $ln = 1024;
  $count = 1;
  do {
  $msg = fread($socket, $ln);

  $tmppar=explode('&',$msg);
  foreach($tmppar as $key=>$value) {
  $tmp=explode('=',$value);
  $params[$tmp[0]]=$tmp[1];
  }
  echo $count.": ".$msg.PHP_EOL;
  var_dump($params);
  echo PHP_EOL;
  $count++;
  }
  while(1 == 1);



  /**
  $config['ip'] = "0.0.0.0";
  $config['port'] = 8888;
  $config['max_clients'] = 20;
  $config['timeout'] = 600; //seconds
  $client = array();

  echo ' Server '.$config['ip'].' on port '.$config['port'].' started...'."\n";
  $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $config['timeout'], 'usec' => 0));
  socket_bind($socket, $config['ip'], $config['port']) or die('Could not bind to address');
  echo ' Server Initalized...'."\n";
  socket_listen($socket);
  echo ' Server is now listening...'."\n";
  socket_recv($socket, $buf, 2048);
  socket_close($socket);
  echo $buf;
  /* */
system("mode con:lines=25");
$timeout = 30;
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($sock, "0.0.0.0", 8888);
socket_set_block($sock);
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 10000, "usec" => 0));
$count = 1;
$target = $timeout + time();
$results = array();
//while(time() < $target) {
while(true) {

	if(($len = @socket_recvfrom($sock, $ret, 2048, 0, $cIP, $cPort)) != false) {
		$count++;
		////echo $count++.': '.$ret.' ['.$cIP.':'.$cPort.']'.PHP_EOL; //bin2hex($ret);
		//$results[substr($ret, 0, 6)][substr($ret, 7, strlen($ret) - 7)] = 1;
		//echo "\r";
		//echo "$count:$cIP:$cPort=> ".$ret.PHP_EOL;
		//echo print_r($results, true).PHP_EOL;
		//$target = $timeout+time();
	}
	$tmppar = explode('&', $ret);
	foreach($tmppar as $key => $value) {
		$tmp = explode('=', $value);
		$params[$tmp[0]] = $tmp[1];
	}

	var_dump($params);

	$asst = new Asset();
	$asst->record_location($params);


//	$lines = 25;
//	while($lines--)
//		echo "\n";
//	system("mode con:lines=25");
//	echo "[".($target - time())."] ".$count.PHP_EOL;
//	foreach($results as $key => $value) {
	// bin2hex()
	// ord()
	// hexdec()
	// dexhex()
//		echo count($value)." received from $key".PHP_EOL;
//	}
//	echo 'target= '.$target.' time='.time().PHP_EOL;
}

socket_set_nonblock($sock);
socket_close($sock);

?>
