<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: bugslist.php
 * Created:        @zaher.reda    Jun 25, 2014 | 4:34:43 PM
 * Last Update:    @zaher.reda    Jun 25, 2014 | 4:34:43 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $bugs = DevelopmentBugs::get_bugs(null, array('order' => 'isFixed'));

    if(is_array($bugs)) {
        foreach($bugs as $bug) {
            $rowclass = 'danger';
            if($bug->isFixed) {
                $rowclass = 'success';
            }

            $bug->link = $bug->parse_link();
            eval("\$bugs_list .= \"".$template->get('development_bugslist_row')."\";");
        }
    }
    else {
        $bugs_list = '<tr><td colspan="4">'.$lang->na.'</td></tr>';
    }

    eval("\$bugslist_page = \"".$template->get('development_bugslist')."\";");
    output_page($bugslist_page);
}
?>