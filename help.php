<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Main page
 * $id: index.php
 * Created: 	@zaher.reda		August 26, 2011 | 10:47 AM		
 * Last Update: @zaher.reda		August 26, 2011 | 10:47 AM	
 */
define('DIRECT_ACCESS', 1);

require './global.php';

if(!$core->input['action']) {
    if(isset($core->input['hdid'])) {
        $help_document = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."helpdocuments WHERE hdid=".$db->escape_string($core->input['hdid']).""));
        echo '<h3>'.$help_document['title'].'</h3>';
        echo '<p>'.$help_document['text'].'</p>';
    }
}
?>