<?php

require_once 'global.php';

if ($core->input['type'] == 'quick') {
    $dofilter = false;
    if (isset($core->input['filter'])) {
        $dofilter = true;
        if (isset($core->input['rid']) && !empty($core->input['rid'])) {
            $report_data = $db->fetch_array($db->query('SELECT affid, spid FROM ' . Tprefix . 'reports WHERE rid=' . intval($core->input['rid'])));
        }
        else {
            if (isset($core->input['cid']) && !empty($core->input['cid'])) {
                $customer_filter = 'er.eid=' . intval($core->input['cid']);
            }

            if (isset($core->input['spid']) && !empty($core->input['spid'])) {
                if (is_array($core->input['spid'])) {
                    $core->input['spid'] = array_map(intval, $core->input['spid']);
                    if ($core->input['for'] == 'product') {
                        $supplier_filter = 'spid IN (' . implode(',', $core->input['spid']) . ')';
                    }
                    else {
                        $supplier_filter = 'er.eid IN (' . implode(',', $core->input['spid']) . ')';
                    }
                }
                else {
                    if ($core->input['for'] == 'product') {
                        $supplier_filter = 'spid=' . intval($core->input['spid']);
                    }
                    else {
                        $supplier_filter = 'er.eid=' . intval($core->input['spid']);
                    }
                }
            }
        }
    }

    if (isset($core->input['for'])) {

        if ($core->input['for'] == 'user') {
            $table = 'users';
            $attributes = array('firstName', 'lastName', 'displayName');
            $key_attribute = 'uid';
            $select_attributes = array('displayName');
            $order = array('by' => 'firstName', 'sort' => 'ASC');
            $extra_where = 'gid != 7';
        }
        elseif ($core->input['for'] == 'cities' || $core->input['for'] == 'sourcecity' || $core->input['for'] == 'destinationcity') {
            if (strlen($core->input['value']) < 2) {
                exit;
            }

            if (isset($core->input['coid']) && !empty($core->input['coid'])) {
                $restrictcountry_filter = "coid ='" . intval($core->input['coid']) . "'";
            }

            if (!empty($restrictcountry_filter)) {
                $extra_where = $restrictcountry_filter;
            }

            $table = 'cities';
            $attributes = array('name', 'unlocode');
            $key_attribute = 'ciid';
            $select_attributes = array('name', 'unlocode');
            $extra_info = array('table' => 'countries');
            $descinfo = 'citycountry';
            $order = array('by' => 'name', 'sort' => 'ASC');
            $disableSoundex = 1;
            $order = array('by' => 'defaultAirport DESC, name', 'sort' => 'ASC');
        }
        elseif ($core->input['for'] == 'countries') {
            $table = 'countries';
            $attributes = array('name');
            $key_attribute = 'coid';
            $select_attributes = array('name');
            $order = array('by' => 'name', 'sort' => 'ASC');
        }
        elseif ($core->input['for'] == 'airports') {
            if (strlen($core->input['value']) < 3) {
                exit;
            }
            $table = 'travelmanager_airports';
            $attributes = array('name');
            $key_attribute = 'apid';
            $select_attributes = array('name');
            $order = array('by' => 'name', 'sort' => 'ASC');
        }
        elseif ($core->input['for'] == 'hotels') {
            $extra_where = ' isApproved=0';
            if (isset($core->input['city']) && !empty($core->input['city'])) {
                $restrictdest_filter = "city='" . intval($core->input['city']) . "'";
            }
            if (isset($core->input['countryid']) && !empty($core->input['countryid'])) {
                $restrictdest_filter = "country='" . intval($core->input['countryid']) . "'";
            }
            if (!empty($restrictdest_filter)) {
                $extra_where .= ' AND ' . $restrictdest_filter;
            }
            $table = 'travelmanager_hotels';
            $attributes = array('name');
            $key_attribute = 'tmhid';
            $select_attributes = array('name');
            $descinfo = 'hotelcitycountry';
//$extra_info = array('table' => 'hotelcountries');
            $order = array('by' => 'name', 'sort' => 'ASC');
        }
        elseif ($core->input['for'] == 'basicfacilities') {
            $extra_where = ' isActive = 1';
            $table = 'facilitymgmt_facilities';
            $attributes = array('name');
            $key_attribute = 'fmfid';
            $select_attributes = array('name');
            $order = array('by' => 'name', 'sort' => 'ASC');
            $descinfo = 'basicfacilities';
            $extrainput = array('userlong' => $core->input['loacationLong'], 'userlat' => $core->input['loacationLat']);
        }
        elseif ($core->input['for'] == 'reservationfacilities') {
            $extra_where = ' isActive = 1 AND allowReservation = 1';
            $factypes = FacilityMgmtFactypes::get_column('fmftid', array('isActive' => 1, 'isMainLocation' => 0), array('returnarray' => true));
            if (is_array($factypes) && !empty($factypes)) {
                $extra_where.=' AND type IN (' . implode(',', $factypes) . ')';
            }
            $table = 'facilitymgmt_facilities';
            $attributes = array('name');
            $key_attribute = 'fmfid';
            $select_attributes = array('name');
            $order = array('by' => 'name', 'sort' => 'ASC');
            if (is_empty($core->input['timeFrom'], $core->input['dateFrom'], $core->input['timeTo'], $core->input['dateTo'])) {
                $results_list[0]['style'] = 'class="li-redbullet"';
                $results_list[0]['value'] = $lang->selectfulldate;
                $results_list[0]['desc'] = '';
                $results_list[0]['id'] = 0;
                $results_list = json_encode($results_list);
                output($results_list);
                exit;
            }
            $from = strtotime($core->input['dateFrom'] . ' ' . $core->input['timeFrom']);
            $to = strtotime($core->input['dateTo'] . ' ' . $core->input['timeTo']);
            $extrainput = array('mtid' => $core->input['mtid'], 'from' => $from, 'to' => $to, 'userlong' => $core->input['loacationLong'], 'userlat' => $core->input['loacationLat']);
            $descinfo = 'reservationfacilities';
        }
        elseif ($core->input['for'] == 'currencies') {
            $table = Currencies::TABLE_NAME;
            $attributes = array('alphaCode', 'name');
            $key_attribute = Currencies::PRIMARY_KEY;

            $select_attributes = array('alphaCode', 'name');
            $order = array('by' => 'name', 'sort' => 'ASC');
        }

        if ($core->input['returnType'] == 'jsontoken') {
            $core->input['returnType'] = 'json';
            $outputjsonformat = 'tokens';
        }
        $results_list = quick_search($table, $attributes, $core->input['value'], $select_attributes, $key_attribute, array('extrainput' => $extrainput, 'outputjsonformat' => $outputjsonformat, 'returnType' => $core->input['returnType'], 'order' => $order, 'extra_where' => $extra_where, 'descinfo' => $descinfo, 'disableSoundex' => $disableSoundex));
        $referrer = explode('&', $_SERVER['HTTP_REFERER']);
        $module = substr($referrer[0], strpos(strtolower($referrer[0]), 'module = ') + 7);
        output($results_list);
    }
}
?>