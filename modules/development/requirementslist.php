<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Requirements List
 * $module: development
 * $id: requirementslist.php	
 * Created By: 		@zaher.reda			May 21, 2012 | 09:38 PM
 * Last Update: 	@zaher.reda			May 21, 2012 | 09:38 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $requirements = new Requirements();
    $requirements_list = $requirements->read_user_requirements();
    if(is_array($requirements_list)) {
        $requirements_list = $requirements->parse_requirements_list($requirements_list);
    }

    eval("\$list = \"".$template->get('development_requirementslist')."\";");
    output_page($list);
}
?>