<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: website_exportcontent.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 5:09:29 PM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 5:09:29 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->input['extract'] == 'segments') {
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
        $application_output = $output = '';
    }
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=cms_segments.doc");
    echo "<html>";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
    echo "<body>";
    echo $segmentsout;
    echo "</body>";
    echo "</html>";
}
elseif($core->input['extract'] == 'pages') {
    $cms_pages = CmsPages::get_latest_pages();
    if(is_array($cms_pages)) {
        foreach($cms_pages as $cms_page) {
            eval("\$pagesout .= \"".$template->get('website_cmspages')."\";");
        }
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=cms_pages.doc");
        echo "<html>";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
        echo "<body>";
        echo $pagesout;
        echo "</body>";
        echo "</html>";
    }
}
if(!$core->input['action']) {

    eval("\$extract = \"".$template->get('cms_extract')."\";");
    output_page($extract);
}
else {

    if($core->input['action'] == 'show_segments') {
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
            $application_output = $output = '';
        }
//$segmentsout = '</body><html>';
//header("Content-type: application/vnd.ms-word");
//header("Content-Disposition: attachment; filename=document_name.doc");
        output($segmentsout);
        exit;
    }
    else if($core->input['action'] == 'show_cmspages') {
        $cms_pages = CmsPages::get_latest_pages();
        if(is_array($cms_pages)) {
            foreach($cms_pages as $cms_page) {
                eval("\$pagesout .= \"".$template->get('website_cmspages')."\";");
            }
        }
        output($pagesout);
        exit;
    }
}