<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: socketUDPWriter.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 5:54:49 PM
 * Last Update:    @alain.paulikevitch    Nov 26, 2012 | 5:54:49 PM
 */

function getany($array) {
	return $array[rand(0, count($array) - 1)];
}


$currentscriptname = basename($_SERVER['SCRIPT_NAME']);
$currentscriptfolder = substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-strlen($currentscriptname));
require $currentscriptfolder.'../inc/init.php';

$lc=array("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","z");
$lv=array("a","e","i","o","u","u","y");

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_connect($sock,"10.0.0.66", 8888);
socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
$id=getany($lc).getany($lv).getany($lc).getany($lv).getany($lc).getany($lv);
$buf = array("Hello World!","Bye Bye World","Another Message","And a fourth message");

for($i=0;$i<100;$i++) {
	$msg=$id.'-'.$i; //.': '.$buf[rand(0,count($buf)-1)];
	echo $msg.PHP_EOL;
	socket_write($sock,$msg,strlen($msg));
	//time_nanosleep(0, rand(0,10000000));
	time_nanosleep(0, rand(0,10));
}

socket_close($sock);


/*
$fp = stream_socket_client("udp://10.0.0.66:8888", $errno, $errstr);
if (!$fp) {
    echo "ERROR: $errno - $errstr<br />\n";
} else {
    fwrite($fp, "hallasodfasd\n");
    echo fread($fp, 26);
    fclose($fp);
}
*/
?>
