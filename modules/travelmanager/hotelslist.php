<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: hoteslist.php
 * Created:        @hussein.barakat    Jun 16, 2015 | 5:14:28 PM
 * Last Update:    @hussein.barakat    Jun 16, 2015 | 5:14:28 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();

    $filters_config = array(
            'parse' => array('filters' => array('name', 'city', 'country', 'isApproved', 'avgprice'),
                    'overwriteField' => array('avgprice' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->avgprice.'"/>',
                            'isApproved' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'tmhid',
                    'mainTable' => array(
                            'name' => 'travelmanager_hotels',
                            'filters' => array('name' => 'name'),
                    ),
                    'secTables' => array(
                            'countries' => array(
                                    'filters' => array('country' => array('name' => 'name')),
                                    'keyAttr' => 'coid', 'joinKeyAttr' => 'coid', 'joinWith' => 'travelmanager_hotels'
                            ),
                            'cities' => array(
                                    'filters' => array('cities' => array('name' => 'name')),
                                    'keyAttr' => 'ciid', 'joinKeyAttr' => 'ciid', 'joinWith' => 'travelmanager_hotels'
                            ),
                    )
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

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
//end parsing filters

    $hotels = TravelManagerHotels::get_data($filters, array('operators' => $filters_opts, 'returnarray' => true, 'order' => array('sort' => array('isApproved' => 'DESC'), 'by' => array('name', 'avgPrice', 'isApproved'))));
    if(is_array($hotels)) {
        foreach($hotels as $hotel) {
            $hotel_link = $hotel->get_displayname();
            $hotel_city = $hotel->get_city()->get_displayname();
            $country = $hotel->get_country();
            $hotel_country = $hotel->get_country()->name;
            if($hotel->isApproved == '1') {
                $hotel_approved = '<img alt="Approved" src="'.$core->settings['rootdir'].'/images/true.gif">';
            }
            else {
                $hotel_approved = '<img alt="Not Approved" src="'.$core->settings['rootdir'].'/images/false.gif">';
            }
            $hotel_avgprice = '-';
            if(isset($hotel->avgPrice) && isset($hotel->currency) && !is_empty($hotel->avgPrice, $hotel->currency)) {
                $currency = new Currencies($hotel->currency);
                if(is_object($currency)) {
                    $hotel_avgprice = $hotel->avgPrice.' - '.$currency->get_displayname();
                }
            }
            eval("\$hotel_rows .= \"".$template->get('travelmanager_hotelslist_rows')."\";");
        }
    }
    eval("\$travelmanager_hotelslist .= \"".$template->get('travelmanager_hotelslist')."\";");
    output_page($travelmanager_hotelslist);
}