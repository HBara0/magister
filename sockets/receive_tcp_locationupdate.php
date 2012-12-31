<?php
/*
 * Copyright Â© 2012 Orkila International Offshore, All Rights Reserved
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
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sock, "10.0.0.66", 8888);
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 10000, "usec" => 0));
if(socket_listen($sock, 5) === false) {
	echo "socket_listen() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
}

do {
	if(($msgsock = socket_accept($sock)) === false) {
		echo "socket_accept() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
		break;
	}

	do {
		if(false === ($buf = socket_read($msgsock, 2048, PHP_BINARY_READ))) {
			echo "socket_read() failed: reason: ".socket_strerror(socket_last_error($msgsock))."\n";
			break 2;
		}
		var_dump($buf);
		if(!$buf = trim($buf)) {
			continue;
		}
		$tmppar = explode('&', $buf);
		foreach($tmppar as $key => $value) {
			$tmp = explode('=', $value);
			$params[$tmp[0]] = $tmp[1];
		}

		var_dump($params);

		$asst = new Asset();
		$asst->record_location($params);

		$talkback="ok";
		socket_write($msgsock, $talkback, strlen($talkback));
		echo "$buf\n";
	}
	while(true);
	socket_close($msgsock);
}
while(true);

socket_close($sock);
?>