<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: contentcategorieslist.php
 * Created:        @hussein.barakat    Jun 30, 2015 | 10:00:25 AM
 * Last Update:    @hussein.barakat    Jun 30, 2015 | 10:00:25 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $filters_config = array(
            'parse' => array('filters' => array('name', 'title'),
            ),
            'process' => array(
                    'filterKey' => CmsContentCategories::PRIMARY_KEY,
                    'mainTable' => array(
                            'name' => CmsContentCategories::TABLE_NAME,
                            'filters' => array('name' => 'name', 'title' => 'title'),
                    ),
            )
    );
    eval("\$addcontentcat = \"".$template->get('popup_managecontentcategories')."\";");
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
    $contentcats = CmsContentCategories::get_data($filter_where, array('operators' => $filters_opts, 'returnarray' => true));
    if(is_array($contentcats)) {
        foreach($contentcats as $contentcat) {
            $content = $contentcat->get();
            $cmsccid = $content['cmsccid'];
            eval("\$cms_contentslist .= \"".$template->get('cms_contentcategories_rows')."\";");
        }
    }
    eval("\$contcatlist = \"".$template->get('cms_contentcategories_list')."\";");
    output_page($contcatlist);
}
else {
    if($core->input['action'] == 'get_updtcontent') {
        $contentcat_obj = new CmsContentCategories(intval($core->input['id']), false);
        if(is_object($contentcat_obj)) {
            $content = $contentcat_obj->get();
        }
        if($content['isEnabled'] == '1') {
            $checked = 'checked="checked"';
        }
        eval("\$addcontentcat = \"".$template->get('popup_managecontentcategories')."\";");
        echo($addcontentcat);
    }
    elseif($core->input['action'] == 'do_editcontentcat') {
        $content = $core->input['content'];
        $contentcat_obj = new CmsContentCategories();
        $contentcat_obj->set($content);
        $contentcat_obj = $contentcat_obj->save();
        switch($contentcat_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
            case 3:
                output_xml('<status>true</status><message>'.$lang->existingentryupdated.'</message>');
                break;
        }
    }
}