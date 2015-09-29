<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: reserve.php
 * Created:        @hussein.barakat    Sep 28, 2015 | 4:31:10 PM
 * Last Update:    @hussein.barakat    Sep 28, 2015 | 4:31:10 PM
 */



if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    eval("\$reservefacility = \"".$template->get('facilitymgmt_reservefacility')."\";");
    output_page($reservefacility);
}