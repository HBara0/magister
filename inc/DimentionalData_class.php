<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * Class to dimensionalize data
 * $id: DimentionalData_class.php
 * Created:        @zaher.reda    Apr 6, 2014 | 7:23:52 PM
 * Last Update:    @zaher.reda    Apr 6, 2014 | 7:23:52 PM
 */

class DimentionalData {
	private $data = null;
	private $raw_data = null;
	private $dimensions = null;
	private $requiredfields = null;
	private $totals = array();

	function __construct($data = null, $dimensions = null) {
		if(!is_null($data)) {
			$this->set_data($data);
		}

		if(!is_null($dimensions)) {
			$this->set_dimensions($dimensions);
		}
	}

	public function set_dimensions($dimensions) {
		$this->dimensions = $dimensions;
	}

	public function set_data($data, $requiredfields = '') {
		$this->raw_data = $data;
		$this->data = $this->dimensionalize($requiredfields);
	}

	public function set_requiredfields($fields) {
		$this->requiredfields = $fields;
	}

	private function dimensionalize($requiredfields = '', $raw_datas = '', $dimensions = '') {
		if(empty($raw_data) || !isset($raw_data)) {
			$raw_datas = $this->raw_data;
		}

		if(empty($dimensions) || !isset($dimensions)) {
			$dimensions = $this->dimensions;
		}

		if(empty($requiredfields) || !isset($requiredfields)) {
			$requiredfields = $this->requiredfields;
		}

		$temp_rawdata = array();
		$data = array();
		$data = $temp_rawdata;
		foreach($raw_datas as $key => $raw_data) {
			foreach($requiredfields as $field) {		
				$temp_data = $data;
				$aid = &$temp_rawdata[$field];
				foreach($dimensions as $dim) {
					$aid[$raw_data[$dim]] = array();
					$aid = &$aid[$raw_data[$dim]];
				}
				$aid[$key] = $raw_data[$field];
				$data = array_merge_recursive_replace($temp_data, $temp_rawdata);
			}
		}
		return $data;
	}

	private function sum_dimensions(&$totals, $data = '', $dimensions = '', $depth = 0, $previds = '') {
		if(empty($data) || !isset($data)) {
			$data = $this->data;
		}
		if(empty($dimensions) || !isset($dimensions)) {
			$dimensions = $this->dimensions;
		}

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
					$this->sum_dimensions($totals, $val, $dimensions, $depth, $previds);
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

	public function get_data() {
		$this->sum_dimensions($this->totals);

		return $this->totals;
	}

	public function get_output($options = '') {
		$this->sum_dimensions($this->totals);
		return $this->parse($options);
	}

	private function parse($options = array(), $data = null, $depth = 1, $previds = null, $total = null, $dimensions = null) {
		global $template;

		if(empty($data) || !isset($data)) {
			$data = $this->data;
		}

		if(empty($dimensions) || !isset($dimensions)) {
			$dimensions = $this->dimensions;
		}

		if(empty($total) || !isset($total)) {
			$total = $this->totals;
		}

		if(empty($options['requiredfields'])) {
			$options['requiredfields'] = $this->requiredfields;
		}
		
		$output = '';
		if(empty($rowcolor)) {
			$rowcolor = 'b1c984';
		}
		if(empty($fontsize)) {
			$fontsize = 18;
		}

		$rowcolor = $this->generate_hexcolor($rowcolor, $depth);
		$fontsize = $this->generate_fontsize($fontsize, $depth);
		if(is_array($data)) {
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
						$class = get_object_bytype($dimensions[$depth], $key);
						if($options['outputtype'] == 'div') {
							$columns = '<div style="display: inline-block; margin-left:'.(($depth - 1) * 20).'px;">'.$class->get()['name'].'</div>';
						}
						else {
							$columns = '<td style="margin-left:'.(($depth - 1) * 20).'px;">zzzz'.$class->get()['name'].'</td>';
						}
					}

					foreach($options['requiredfields'] as $field) {
						if($options['outputtype'] == 'div') {
							$columns .= '<div style="display: inline-block; font-size:'.$fontsize.'">'.$total[$dimensions[$depth]][$field.'-'.$previds].'</div>';
						}
						else {
							$columns .= '<td style="font-size:'.$fontsize.'">x'.$total[$dimensions[$depth]][$field.'-'.$previds].'</td>';
						}
					}

					if(isset($options['template']) && !empty($options['template'])) {
						eval("\$output .= \"".$template->get($options['template'])."\";");
					}
					else {
						if($options['outputtype'] == 'div') {
							$output .= '<div style="background-color:'.$rowcolor.'" id="dimension_'.$previds.'">'.$columns.'</div>';
						}
						else {
							$output .= '<tr style="background-color:'.$rowcolor.'" id="dimension_'.$previds.'">'.$columns.'</tr>';
						}
					}
					$columns = '';
					if(is_array($val)) {
						$depth = $depth + 1;
						if($depth == 0) {
							$key = '';
						}
						$output .= $this->parse($options, $val, $depth, $previds, $total, $dimensions);  /* include the function in the recurison */
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
		}
		if($options['noenclosingtags'] == false) {
			if($options['outputtype'] == 'table') {
				$output = '<table>'.$output.'</table>';
			}
		}
		return $output;
	}

	private function generate_hexcolor($hex = '', $depth, $threshold = 2) {
		$hex_array = str_split($hex, $threshold);
		$hex = '';
		foreach($hex_array as $h) {
			$dec = hexdec($h);
			$hex .= dechex($dec + ($depth * $threshold));
		}
		return $hex;
	}

	private function generate_fontsize($size = '', $depth, $min_size = 8) {
		$size = ($size - ($depth));
		if($size <= $min_size) {
			$size = $min_size;
		}
		return $size;
	}

}