<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: migrate_usergroups.php
 * Created:        @zaher.reda    Sep 2, 2013 | 9:15:16 AM
 * Last Update:    @zaher.reda    Sep 2, 2013 | 9:15:16 AM
 */
exit;
require '../inc/init.php';
define('AUTHCODE', 'ZdiILL7pG0GR4p6oi3fhHEc');

if($core->input['authCode'] == AUTHCODE) {
    $query = $db->query('SELECT uid, gid FROM '.Tprefix.'users');
    while($user = $db->fetch_assoc($query)) {
        $insert_query = $db->insert_query('users_usergroups', array('gid' => $user['gid'], 'uid' => $user['uid'], 'isMain' => 1));
    }
}
?>
