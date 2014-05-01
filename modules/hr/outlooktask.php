<?php
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}


$outlook = new COM("Scripting.FileSystemObject");
print "Loaded OutLook, version {$outlook->Version}\n";
$myItem = $outlook->CreateItem(3);
$myItem->Subject = "add ocos task!";
$myItem->Body = "task body.....!";
$myItem->save();
?>