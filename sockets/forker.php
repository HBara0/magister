<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: forker.php
 * Created:        @alain.paulikevitch    Nov 27, 2012 | 1:53:38 PM
 * Last Update:    @alain.paulikevitch    Nov 27, 2012 | 1:53:38 PM
 */
$cmd ="C:\Progra~1\apache\php-5.4\php.exe D:\Web\web\development\ocos\sockets\socketUDPWriter.php";
for($i=1;$i<20;$i++) {
	$cmd.=" | C:\Progra~1\apache\php-5.4\php.exe D:\Web\web\development\ocos\sockets\socketUDPWriter.php";
}
exec($cmd);
?>
