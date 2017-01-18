<?php

/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: general_resizefileimages.php
 * Created:        @hussein.barakat    30-Mar-2016 | 09:48:15
 * Last Update:    @hussein.barakat    30-Mar-2016 | 09:48:15
 */

require '../inc/init.php';
if ($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
    if (!empty($core->input['file'])) {
        iteratethrough_file(path);
    }
}

/**
 *
 * @param type $file
 */
function iteratethrough_file($file, $outfile = '') {
    $dir = new DirectoryIterator($file);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            if ($fileinfo->isDir()) {
                iteratethrough_file($fileinfo->getRealPath());
            }
            else {
                if ($fileinfo->getExtension() == 'png') {
                    if ($outfile) {
                        $outfile_path = $outfile;
                    }
                    else {
                        $outfile_path = dirname($file);
                    }
                    resize_image_png($fileinfo->getRealPath(), 200, 200, $outfile_path . '\\newimgs\\' . $fileinfo->getBasename());
                    echo($fileinfo->getFilename() . '<br>');
                }
            }
        }
    }
    return;
}
