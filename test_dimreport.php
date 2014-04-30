<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: test_dimreport.php
 * Created:        @zaher.reda    Mar 17, 2014 | 1:01:47 PM
 * Last Update:    @zaher.reda    Mar 17, 2014 | 1:01:47 PM
 */

include './inc/init.php';


$dimensions = array(1 => 'affid', 2 => 'cid', 3 => 'pid');
$required_fileds = array('qty', 'perc');
$rows = array(70 => array('affid' => 1, 'cid' => 15, 'pid' => 500, 'bdid' => 70, 'qty' => 1000, 'perc' => 0.2),
        71 => array('affid' => 1, 'cid' => 15, 'pid' => 500, 'bdid' => 71, 'qty' => 2000, 'perc' => 0.5),
        72 => array('affid' => 2, 'cid' => 16, 'pid' => 500, 'bdid' => 72, 'qty' => 3000, 'perc' => 0.5)
);

$data = dimentionalize_data($rows, $dimensions, $required_fileds);
//print_r($data);

end($dimensions);
$last_dim = key($dimensions);
reset($dimensions);

$total = array();
dosum($data, $total);
print_r($total);
echo '<br />';
parse($data['qty']);
function parse($data, $depth = 1, $previds = '') {
    global $total, $dimensions, $required_fileds;
    foreach($data as $key => $d) {
        if(!empty($previds)) {
            $previds .= '-'.$key;
        }
        else {
            $previds = $key;
        }

        if($depth <= count($dimensions)) {
            // if($depth != 0) {
            echo $key;
            $class = get_object($dimensions[$depth], $key);
            echo str_repeat('__', $depth).$class->get()['name'];
            foreach($required_fileds as $field) {
                // echo $dimensions[$depth].'-'.$field.'-'.$previds.'<br />';
                echo '-'.$total[$dimensions[$depth]][$field.'-'.$previds].'-';
            }
            echo '<br />';
            //}
            if(is_array($d)) {
                $depth = $depth + 1;
                if($depth == 0) {
                    $key = '';
                }
                parse($d, $depth, $previds);
            }
        }
        $depth -= 1;
        if($depth == 1) {
            $previds = '';
        }
        else {
            $previds = preg_replace('/-([0-9]+)$/', '', $previds); //Remove last portion of previd
        }
    }
}

function get_object($dim, $id) {
    switch($dim) {
        case 'affid':
            return new Affiliates($id);
            break;
        case 'cid':
        case 'spid':
            return new Entities($id);
            break;
        case 'pid':
            return new Products($id);
            break;
    }
}

function dosum($data, &$totals, $depth = 0, $previds = '') {
    global $dimensions;

    foreach($data as $key => $d) {
        if(!empty($previds)) {
            $previds .= '-'.$key;
        }
        else {
            $previds = $key;
        }

        if($depth < count($dimensions)) {
            $dim_value = $dimensions[$depth];
            if($depth === 0) {
                $dim_value = 'gtotal';
            }

            $totals[$dim_value][$previds] = array_sum_recursive($d);

            if(is_array($d)) {
                $depth = $depth + 1;
                dosum($d, $totals, $depth, $previds);
            }
        }
        else {
            if(is_array($d)) {
                $totals[$dimensions[$depth]][$previds] = array_sum($d);
            }
            else {
                $totals[$dimensions[$depth]][$previds] = $d;
            }
        }
        $depth -= 1;
        if($depth == 0) {
            $previds = '';
        }
        else {
            $previds = preg_replace('/-([0-9]+)$/', '', $previds); //Remove last portion of previd
        }
    }
}

function array_merge_recursive_replace() {
    $arrays = func_get_args();
    $base = array_shift($arrays);
    foreach($arrays as $array) {
        reset($base);
        while(list($key, $value) = @each($array)) {
            if(is_array($value) && @is_array($base[$key])) {
                $base[$key] = array_merge_recursive_replace($base[$key], $value);
            }
            else {
                $base[$key] = $value;
            }
        }
    }
    return $base;
}

function dimentionalize_data($raw_datas, $dimensions, $required_fileds) {
    $temp_rawdata = array();
    $data = array();
    $data = $temp_rawdata;
    foreach($raw_datas as $raw_data) {
        foreach($required_fileds as $field) {
            $temp_data = $data;
            $aid = &$temp_rawdata[$field];
            foreach($dimensions as $dim) {
                $aid[$raw_data[$dim]] = array();
                $aid = &$aid[$raw_data[$dim]];
            }
            $aid[$raw_data['bdid']] = $raw_data[$field];
            $data = array_merge_recursive_replace($temp_data, $temp_rawdata);
        }
    }

    return $data;
}

?>