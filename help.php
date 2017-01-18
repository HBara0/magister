<?php

define('DIRECT_ACCESS', 1);

require './global.php';

if (!$core->input['action']) {
    if (isset($core->input['hdid'])) {
        $help_document = $db->fetch_assoc($db->query("SELECT * FROM " . Tprefix . "helpdocuments WHERE hdid=" . $db->escape_string($core->input['hdid']) . ""));
        echo '<h1>' . $help_document['title'] . '</h1>';
        echo '<p>' . $help_document['text'] . '</p>';
    }
}
?>