<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: home.php
 * Created:        @zaher.reda    Jul 11, 2014 | 12:20:35 PM
 * Last Update:    @zaher.reda    Jul 11, 2014 | 12:20:35 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    eval("\$home = \"".$template->get('crm_home')."\";");
    output_page($home);
}
?>