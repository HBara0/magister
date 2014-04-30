<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: marketintelligence_report_preview.php
 * Created:        @tony.assaad    Mar 11, 2014 | 11:07:54 AM
 * Last Update:    @tony.assaad    Mar 11, 2014 | 11:07:54 AM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['crm_canGenerateMIRep'] == 0) {
	error($lang->sectionpermision);
}
if(!($core->input['action'])) {
	if($core->input['referrer'] == 'generate') {
		$mireportdata = ($core->input['mireport']);

		/* split the dimension and explode them into chuck of array */
		$mireportdata['dimension'] = explode(',', $mireportdata['dimension'][0]);
		$mireportdata['dimension'] = array_filter($mireportdata['dimension']);

		/* to create array using existing values (using array_values()) and range() to create a new range from 1 to the size of the  dimension array */
		$mireportdata['dimension'] = array_combine(range(1, count($mireportdata['dimension'])), array_values($mireportdata['dimension']));


		$marketdata_indexes = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
		/* get Market intellgence baisc Data  --START */

		/* Get cfpid of segment ----START */
		foreach($mireportdata['filter']['spid'] as $eid) {
			$entiy = new Entities($eid);
			if(is_array($entiy->get_segments())) {
				$entity_segmentobjs = $entiy->get_segments();
			}
			//$cfpid = $entiy->get_segments()->get_applications()->get_segmentsapplications()->get();
		}
		/* Get cfpid of segment  ----END */

		$marketin_objs = Marketintelligence::get_marketdata($mireportdata['filter']);
		if(is_array($marketin_objs)) {
			foreach($marketin_objs as $marketin_obj) {
				$market_data[$marketin_obj->get()['mibdid']] = $marketin_obj->get();
			}
 print_r($market_data);
			$dimensionalize_ob = new DimentionalData();
			$mireportdata['dimension'] = $dimensionalize_ob->set_dimensions($mireportdata['dimension']);

			$dimensionalize_ob->set_requiredfields($marketdata_indexes);
			$dimensionalize_ob->set_data($market_data);
			$parsed_dimension = $dimensionalize_ob->get_output(array('outputtype' => 'table', 'noenclosingtags' => true));
			 
			//foreach($totals['gtotal'] as $report_header => $header_data) {
			$dimension_head.= '<th>'.$report_header.'</th>';
			//}
		}
		/* get Market intellgence baisc Data  --END */
	}

	eval("\$mireport_output = \"".$template->get('crm_marketintelligence_report_output')."\";");
	output($mireport_output);
}
function parse_dimensionaldata($data, $depth = 1, $previds = '', $total, $dimensions, $required_fileds) {
	global $template;
	$mireport_output_firstdimension = '';
	if(empty($marketreport_dimensioncolor)) {
		$marketreport_dimensioncolor = 'b1c984';
	}
	if(empty($marketreport_fontsize)) {
		$marketreport_fontsize = 18;
	}

	$marketreport_dimensioncolor = generate_hexcolor($marketreport_dimensioncolor, $depth);
	$marketreport_fontsize = generate_fontsize($marketreport_fontsize, $depth);
	foreach($data as $key => $val) {
		$altrow = alt_row('trow');
		if(!empty($previds)) {
			$previds .= '-'.$key;
		}
		else {
			$previds = $key;
		}

		if($depth <= count($dimensions)) {
			if(isset($dimensions[$depth]) && !empty($dimensions[$depth]) && (isset($key) && !empty($key))) {
				$class = get_object_type($dimensions[$depth], $key);

				$parsed_dimensionname = '<span style="margin-left:'.(($depth - 1) * 20).'px;">'.$class->get()['name'].'</span>';
			}

			foreach($required_fileds as $field) {

				$parsed_dimensionname .= '<td style="font-size:'.$marketreport_fontsize.'">'.$total[$dimensions[$depth]][$field.'-'.$previds].'</td>';
			}
			eval("\$mireport_output_firstdimension .= \"".$template->get('crm_marketintelligence_report_outputfirstdimension')."\";");

			if(is_array($val)) {
				$depth = $depth + 1;
				if($depth == 0) {
					$key = '';
				}
				$mireport_output_firstdimension .= parse_dimensionaldata($val, $depth, $previds, $total, $dimensions, $required_fileds);  /* include the function in the recurison */
			}
		}
		$depth -= 1;
		if($depth == 1) {
			$previds = '';
		}
		else {
			$previds = preg_replace('/-([0-9]+)$/', '', $previds); // $ Remove last portion of previd
		}
	}
	return $mireport_output_firstdimension;
}

function dosumimensionaldata($data, $dimensions, &$totals, $depth = 0, $previds = '') {
	foreach($data as $key => $val) {
		if(!empty($previds)) {
			$previds .= '-'.$key;
		}
		else {
			$previds = $key;
		}

		if($depth <= count($dimensions)) {

			$dim_value = $dimensions[$depth];
			if($depth === 0) {
				$dim_value = 'gtotal';
			}

			$totals[$dim_value][$previds] = array_sum_recursive($val);

			if(is_array($val)) {
				$depth = $depth + 1;
				dosumimensionaldata($val, $dimensions, $totals, $depth, $previds);
			}
		}
		else {
			if(empty($dimensions[$depth])) {
				continue;
			}
			if(is_array($val)) {
				$totals[$dimensions[$depth]][$previds] = array_sum($val);
			}
			else {
				$totals[$dimensions[$depth]][$previds] = $val;
			}
		}
		$depth -= 1;
		if($depth == 0) {
			$previds = '';
		}
		else {
			$previds = preg_replace('/-([0-9]+)$/', '', $previds); //Remove last portion of
		}
	}
}

function generate_hexcolor($hex = '', $depth) {
	$hex_array = str_split($hex, 2);
	$hex = '';
	foreach($hex_array as $h) {
		$dec = hexdec($h);
		$hex .= dechex($dec + ($depth * 2));
	}
	return $hex;
}

function generate_fontsize($size = '', $depth) {
	$size = ($size - ($depth));
	if($size <= 8) {
		$size = 8;
	}
	return $size;
}

?>
