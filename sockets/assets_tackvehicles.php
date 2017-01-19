<?php

ini_set('max_execution_time', 1000);
require '../inc/init.php';

/* Server Configurations - START */
$config['ip'] = '70.38.119.243';
$config['port'] = 8804;
$config['max_clients'] = 20;
$config['timeout'] = 600; //seconds
$client = array();
/* Server Configurations - End */

/* Start Server */
echo ' Server ' . $config['ip'] . ' on port ' . $config['port'] . ' started...' . "\n";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $config['timeout'], 'usec' => 0));
socket_bind($socket, $config['ip'], $config['port']) or die('Could not bind to address');
echo ' Server Initalized...' . "\n";

/* Start Listening for connections */
socket_listen($socket);
echo ' Server is now listening...' . "\n";
//$fh = fopen('socketdata.txt', 'w');
$fh = fopen('socketdata-2.txt', 'w');
while (true) {
    $read = array();
    $read[0] = $socket;
    for ($i = 0; $i < $config['max_clients']; $i++) {
        if ($client[$i]['socket'] != null) {
            $read[$i + 1] = $client[$i]['socket'];
        }
    }

    $ready = socket_select($read, $write, $except, null);

    if (in_array($socket, $read)) {
        for ($i = 0; $i < $config['max_clients']; $i++) {
            if ($client[$i]['socket'] == null) {
                $client[$i]['socket'] = socket_accept($socket);
                break;
            }
            elseif ($i == $config['max_clients'] - 1) {
                echo 'Error: too many clients';
            }
        }
        if (--$ready <= 0) {
            continue;
        }
    }

    for ($i = 0; $i < $config['max_clients']; $i++) {
        if (in_array($client[$i]['socket'], $read)) {
            $input = socket_read($client[$i]['socket'], 1024);

            if ($input == null) {
                //socket_close($client[$i]['socket']);
                unset($client[$i]['socket']);
                continue;
            }

            $data = trim($input);
            if ($data == 'exit') {
                /* If device sends 'exit' command, close connection */
                socket_close($client[$i]['socket']);
                unset($client[$i]['socket']);
            }
            else {
                if ($client[$i]['socket'] != null) {
                    if (!empty($data)) {
                        fwrite($fh, $data . "\n");
                        /* Convert incoming binary into Hex */
                        fwrite($fh, print_r(unpack("cchars/nint", $data), true) . "\n");
                        fwrite($fh, bin2hex($data) . "\n");
                        fwrite($fh, base_convert(bin2hex($data), 16, 2) . "\n");
                        fwrite($fh, bindec($data) . "\n");
                        fwrite($fh, "\n --------- \n");
                        $hex_input = str_split(bin2hex($data), 2);
                        /* Respond to the request with handshake reply */
                        socket_write($client[$i]['socket'], hex2bin('2929210005' . $hex_input[10] . $hex_input[2] . '000D'));
                    }
                }
            }
        }
        else {
            if ($client[$i]['socket'] != null) {
                socket_close($client[$i]['socket']);
                unset($client[$i]['socket']);
            }
        }
    }
}
fclose($fh);
socket_close($socket);
?>
