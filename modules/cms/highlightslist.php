<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: highlisghtslist.php
 * Created:        @hussein.barakat    Jun 30, 2015 | 2:05:42 PM
 * Last Update:    @hussein.barakat    Jun 30, 2015 | 2:05:42 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $filters_config = array(
            'parse' => array('filters' => array('name', 'type'),
            ),
            'process' => array(
                    'filterKey' => CmsHighlights::PRIMARY_KEY,
                    'mainTable' => array(
                            'name' => CmsHighlights::TABLE_NAME,
                            'filters' => array('name' => 'name', 'type' => 'type'),
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));

    $highlights = CmsHighlights::get_data($filter_where, array('operators' => $filters_opts, 'returnarray' => true));
    if(is_array($highlights)) {
        foreach($highlights as $highlight_obj) {
            $highlight = $highlight_obj->get();
            eval("\$highlights_rows .= \"".$template->get('cms_highlights_rows')."\";");
        }
    }
    eval("\$highlightslist = \"".$template->get('cms_highlights_list')."\";");
    output_page($highlightslist);
}
else {

}