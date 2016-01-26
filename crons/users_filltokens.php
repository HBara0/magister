<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: users_filltokens.php
 * Created:        @hussein.barakat    05-Jan-2016 | 14:44:10
 * Last Update:    @hussein.barakat    05-Jan-2016 | 14:44:10
 */

require_once '../inc/init.php';

//if($_REQUEST['authkey'] == 'asfasdkjj!h4k23jh4k2_3h4k23jh') {
$allactiveusers = Users::get_data('gid !=7 AND (apiKey IS NULL OR apiKey = "") ', array('returnarray' => true, 'simple' => false));
if(is_array($allactiveusers)) {
    foreach($allactiveusers as $user) {
        $user->save_apikey();
    }
}
//}