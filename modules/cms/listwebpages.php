<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List Web Pages
 * $module: CMS
 * $id: listwebpages.php
 * Created By: 		@tony.assaad		Augusst 28, 2012 | 12:30 PM
 * Last Update: 	@zaher.reda			Augusst 28, 2012 | 03:45 PM
 */

/* if($core->usergroup['cms_canAddPage'] == 0) {
  error($lang->sectionnopermission);
  exit;
  } */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_url = sort_url();
    $allpages = new CmsPages();


    /* Perform inline filtering - START */

    $version_scale = range(1, 100, 0.1);
    array_unshift($version_scale, ''); /* Prepend empty elements to the beginning */

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('title', 'version', 'isPublished', 'lang', 'createdBy', 'date', 'hits'),
                    'overwriteField' => array('version' => parse_selectlist('filters[version]', 5, array_combine($version_scale, $version_scale), $core->input['filters']['version'], 1),
                            'isPublished' => parse_selectlist('filters[isPublished]', 2, array('' => '', '1' => $lang->published, '0' => $lang->notpublished), $core->input['filters']['isPublished']),
                            'lang' => parse_selectlist('filters[lang]', 2, array('' => '', 'en' => $lang->english, 'fr' => $lang->french), $core->input['filters']['lang'])
                    )
            ),
            'process' => array(
                    'filterKey' => 'cmspid',
                    'mainTable' => array(
                            'name' => 'cms_pages',
                            'filters' => array('title' => 'title', 'version' => 'version', 'isPublished' => 'isPublished', 'lang' => 'lang', 'hits' => 'hits', 'date' => array('operatorType' => 'date', 'name' => 'publishDate')),
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        if($filters_config['process']['filterKey'] == 'cmspid') {
            $filters_config['process']['filterKey'] = 'cp.cmspid';
        }
        $filter_where = 'WHERE '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));


    $pages_details = $allpages->get_multiplepages($filter_where);

    if(is_array($pages_details)) {
        foreach($pages_details as $pageid => $page) {
            $ispublished_icon = '';

            $rowclass = alt_row($rowclass);
            $page['dateCreated_output'] = date($core->settings['dateformat'], $page['dateCreated']);
            if($page['isPublished'] == 1) {
                $ispublished_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->published.'"/>';
            }
            else {
                $ispublished_icon = '<img src="./images/false.gif" border="0" />';
            }

            eval("\$cms_pages_list_rows .= \"".$template->get('cms_webpages_list_row')."\";");
        }

        /* Parse pagination - START */


        $multipages = new Multipages('cms_pages cp', $core->settings['itemsperlist'], $multipage_where);

        $cms_pages_list_rows .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
        /* Parse pagination - END */
    }
    else {
        $cms_pages_list_rows = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
    }
    eval("\$pageslist = \"".$template->get('cms_webpages_list')."\";");
    output_page($pageslist);
}
?>