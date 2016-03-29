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
    $filters_config = array(
            'parse' => array('filters' => array('name', 'city', 'country', 'phone', 'address', 'avgPrice', 'isApproved'),
                    'overwriteField' => array('avgPrice' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->avgprice.'"/>',
                            'isApproved' => parse_selectlist('filters[isApproved]', 2, array('' => '', '1' => $lang->yes, '0' => $lang->no), $core->input['filters']['isApproved']),
                            'phone' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->phone.'"/>',
                            'address' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->address.'"/>'
                    )
            ),
            'process' => array(
                    'filterKey' => 'tmhid',
                    'mainTable' => array(
                            'name' => 'travelmanager_hotels',
                            'filters' => array('name' => 'name', 'isApproved' => 'isApproved'),
                    ),
                    'secTables' => array(
                            'countries' => array(
                                    'filters' => array('country' => array('name' => 'countries.name')),
                                    'keyAttr' => 'coid', 'joinKeyAttr' => 'country', 'joinWith' => 'travelmanager_hotels'
                            ),
                            'cities' => array(
                                    'filters' => array('city' => array('name' => 'cities.name')),
                                    'keyAttr' => 'ciid', 'joinKeyAttr' => 'city', 'joinWith' => 'travelmanager_hotels'
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

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
//end parsing filters
    if($core->usergroup['travelmanager_canApproveHotels'] == 0) {
   
        $hidecreate = 'display:none;';
    }
 $hotels = TravelManagerHotels::get_data($filter_where, array('operators' => $filters_opts, 'returnarray' => true, 'simple' => false, 'order' => array('sort' => array('isApproved' => 'DESC'), 'by' => array('name', 'avgPrice', 'isApproved'))));
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
            $hotel_address = '-';
            if(!empty($hotel->addressLine1) || !empty($hotel->addressLine2)) {
                $hotel_address = $hotel->addressLine1.'<br/>'.$hotel->addressLine2;
            }
            $hotel_phone = '-';
            if(!empty($hotel->phone)) {
                $hotel_phone = $hotel->phone;
            }
            //add tools icons depending on user permissions
            $tool_items = ' <li><a target="_blank" href="'.$core->settings['rootdir'].'/index.php?module=travelmanager/viewhotel&id='.$hotel->tmhid.'"><span class="glyphicon glyphicon-eye-open"></span>&nbsp'.$lang->viewhotel.'</a></li>';
            if($core->usergroup['travelmanager_canApproveHotels'] == 1) {
                $tool_items .= '<li><a target="_blank" href="'.$core->settings['rootdir'].'/index.php?module=travelmanager/edithotel&id='.$hotel->tmhid.'"><span class="glyphicon glyphicon-pencil"></span>&nbsp'.$lang->edithotel.'</a></li>';
//                if($hotel->isApproved == 0) {
//                    $tool_items .= '<li role="separator" class="divider"></li><li class="greenbackground"><a><span class="glyphicon glyphicon-ok"></span>&nbsp'.$lang->approve.'</a></li>';
//                }
            }
            eval("\$tools = \"".$template->get('tools_buttonselectlist')."\";");

            eval("\$hotel_rows .= \"".$template->get('travelmanager_hotelslist_rows')."\";");
            unset($tool_items);
        }
    }
    eval("\$travelmanager_hotelslist .= \"".$template->get('travelmanager_hotelslist')."\";");
    output_page($travelmanager_hotelslist);
}