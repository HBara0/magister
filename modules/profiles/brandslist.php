<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: brandslist.php
 * Created:        @zaher.reda    Mar 1, 2015 | 11:24:09 AM
 * Last Update:    @zaher.reda    Mar 1, 2015 | 11:24:09 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'name ASC';

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }

    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('brand', 'entity', 'country', 'endproduct'),
                    'overwriteField' => array('country' => '')
            ),
            'process' => array(
                    'filterKey' => 'ebpid',
                    'mainTable' => array(
                            'name' => 'entitiesbrandsproducts',
                            'filters' => array('ebid' => 'ebid'),
                    ),
                    'secTables' => array(
                            'entitiesbrands' => array(
                                    'filters' => array('brand' => array('name' => 'name')),
                                    'keyAttr' => 'ebid', 'joinKeyAttr' => 'ebid', 'joinWith' => 'entitiesbrandsproducts'
                            ),
                            'endproducttypes' => array(
                                    'filters' => array('endproduct' => array('name' => 'title')),
                                    'keyAttr' => 'eptid', 'joinKeyAttr' => 'eptid', 'joinWith' => 'entitiesbrandsproducts'
                            )
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filters[$filters_config['process']['filterKey']] = $filter_where_values;
        $filters_opts[$filters_config['process']['filterKey']] = 'IN';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    $brandproducts = EntBrandsProducts::get_data($filters, array('returnarray' => true, 'operators' => $filters_opts));
    if(is_array($brandproducts)) {
        foreach($brandproducts as $brandproduct) {
            $brand = $brandproduct->get_entitybrand();
            $entity = $brand->get_entity();

            $endproduct = $brandproduct->get_endproduct();
            if(!is_object($endproduct)) {
                $endproduct = new EndProducTypes();
            }

            $brands_list .= '<tr><td>'.$brand->name.'</td><td>'.$entity->parse_link().'</td><td>'.$entity->get_country()->name.'</td><td>'.$endproduct->get_displayname().'</td></tr>';
        }
    }
    eval("\$listpage = \"".$template->get('profiles_brandslist')."\";");
    output_page($listpage);
}
