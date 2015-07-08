<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: website_exportcontent.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 5:09:29 PM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 5:09:29 PM
 */
require '../inc/init.php';
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=document_name.doc");


$segments = ProductsSegments::get_data();
//$segmentsout = '<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\"><body>';
foreach($segments as $segment) {
    if(empty($segment->description)) {
        $output = '<p>'.$segment->shortDescription.'</p>';
    }
    else {
        $output = '<p>'.$segment->description.'</p>';
    }
    /* parse application */
    $aplications_objs = $segment->get_applications(array('returnarray' => true, 'order' => 'sequence'));
    if(is_array($aplications_objs)) {
        foreach($aplications_objs as $item) {
            $application_output.='<h5>'.$item->get_displayname().'</h5>';
            $application_output.='<div>'.$item->description.'</div><hr>';
        }
    }
    eval("\$segmentsout .= \"".$template->get('website_segments')."\";");
    $application_output = '';
}
//$segmentsout = '</body><html>';
//header("Content-type: application/vnd.ms-word");
//header("Content-Disposition: attachment; filename=document_name.doc");
echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<body>";
echo $segmentsout;
echo "</body>";
echo "</html>";
