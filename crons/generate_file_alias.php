<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generate_files_alias.php
 * Created:        @rasha.aboushakra    Mar 21, 2016 | 4:04:53 PM
 * Last Update:    @rasha.aboushakra    Mar 21, 2016 | 4:04:53 PM
 */

require '../inc/init.php';
global $db;

$files = Files::get_data('', array('retunarray' => true));
if(is_array($files)) {
    foreach($files as $file) {
        if(!empty($file->alias)) {
            continue;
        }
        $data['alias'] = generate_alias($file->title).'_'.$file->category.'_'.$file->ffid;
        $query = $db->update_query('files', $data, 'fid='.$file->fid);
    }
}