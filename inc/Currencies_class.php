<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Currencies Class
 * $id: Currencies_class.php
 * Created: 	@zaher.reda 		November 14, 2011 | 10:04 AM
 * Last Update: @zaher.reda 		May 17, 2012 | 04:07 PM
 */
 
class Currencies {
	private $base_currency = 'USD';
	private $error_handler = NULL;
	
	public function __constructor($base_currency) {
		$this->error_handler = new ErrorHandler(true);
		$this->base_currency = $base_currency;
 	}
	
	public function get_average_fxrate($currency, array $period = array(), array $options = array(), $base_currency = '') {
		global $db;
		if(empty($currency)) {
			/*$this->error_handler->record('missingparameter', 'Missing Parameter');
			$this->error_handler->get_errors_inline();
			exit*/;
			return false;
		}
		
		if(empty($base_currency)) {
			$base_currency = $this->base_currency;	
		}
		
		if($currency == $base_currency) {
			return '1';
		}	
		
		$query_where = $this->parse_period_assql($period);		

		$query = $db->query("SELECT AVG(rate) AS rate
							FROM ".Tprefix."currencies_fxrates 
							WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."') 
							AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."'){$query_where}
							ORDER BY date DESC
							LIMIT 0, 1");

		if($db->num_rows($query) > 0) {
			$fx_rate = $db->fetch_assoc($query);
			if(isset($options['precision']) && !empty($options['precision'])) {
				$fx_rate['rate'] = number_format($fx_rate['rate'], $options['precision']);
			}
				
			return $fx_rate['rate'];
		}
		else
		{
			return 0;
		}
	}
	
	public function get_average_fxrates(array $currencies, array $period = array(), array $options = array(), $base_currency = '') {
		global $db;
		
		if(empty($currencies)) {
			$this->error_handler->record('missingparameter', 'Missing Parameter');
			$this->error_handler->get_errors_inline();
			exit;
		}

		if(empty($base_currency)) {
			$base_currency = $this->base_currency;	
		}
		
		if(!isset($options['distinct_by']) || empty($options['distinct_by'])) {
			$options['distinct_by'] = 'currency';
		}
		
		$query_where = $this->parse_period_assql($period);
		
		$query = $db->query("SELECT DISTINCT({$options[distinct_by]}), AVG(rate) AS rate 
							FROM ".Tprefix."currencies_fxrates cfx JOIN ".Tprefix."currencies c ON (cfx.currency=c.numCode)
							WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."') 
							AND currency IN (SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode IN ('".implode('\', \'', $currencies)."')){$query_where}
							GROUP BY currency
							ORDER BY c.alphaCode ASC");

		if($db->num_rows($query) > 0) {
			while($fx_rate = $db->fetch_assoc($query)) {
				if(isset($options['precision']) && !empty($options['precision'])) {
					$fx_rate['rate'] = number_format($fx_rate['rate'], $options['precision']);
				}
				
				$fx_rates[$fx_rate[$options['distinct_by']]] = $fx_rate['rate'];	
			}
			return $fx_rates;
		}
		else
		{
			return 0;	
		}
	}
	
	public function get_average_fxrates_transposed(array $currencies, array $period = array(), array $options = array(), $base_currency = '') {
		$rates = $this->get_average_fxrates($currencies, $period, $options, $base_currency);
		if($rates) {
			foreach($rates as $currency => $rate) {
				$new_rates[$rate] = $currency;
				if($options['combine_values'] == true) {
					$new_rates[$rate] .= ' - '.$rate;
				}
			}
			return $new_rates;
		}
		return false;
	}
	
	public function get_yearaverage_fxrate_monthbased($currency, $year, array $options = array(), $base_currency = '') {
		if(empty($base_currency)) {
			$base_currency = $this->base_currency;	
		}
		
		for($i = 1; $i <= 12; $i++) {
			$period['from'] = mktime(0, 0, 0, $i, 1, $year);
			$period['to'] = mktime(23, 59, 59, $i, date('t', $month_start), $year);
			
			$rates[$i] = $this->get_average_fxrate($currency, $period, $options, $base_currency);
		}
		return $rates;
	}
	
	private function parse_period_assql(array $period = array()) {
		global $db;
		$query_where = '';
		
		if(is_array($period) && !empty($period)) {
			if(isset($period['from']) && isset($period['to'])) {
				if(!empty($period['from']) && !empty($period['to'])) {
					$query_where = ' AND (date BETWEEN '.$db->escape_string($period['from']).' AND '.$db->escape_string($period['to']).')';
				}
				elseif(!empty($period['from']) && empty($period['to'])) {
					$query_where = ' AND date >='.$period['from'];
				}
				elseif(empty($period['from']) && !empty($period['to'])) {
					$query_where = ' AND date >='.$period['to'];
				}
			}
		}
		else
		{
			$query_where = ' AND date >='.strtotime('today');	
		}
		return $query_where;
	}
	
	public function set_fx_rates($source) {
		$this->load_fx_rates($source);
	}
	
	public function set_fx_rate($basecurrency, $currency, $rate, $date=TIME_NOW) {
		global $db;
		
		if(is_empty($basecurrency, $currency, $rate)) {
			return false;	
		}
		$new_fx_rate = array(
			'rate' => $rate,
			'baseCurrency' => $basecurrency,
			'currency' => $currency,
			'date' => $date
		);
		
		$newfx_query = $db->insert_query('currencies_fxrates', $new_fx_rate);
	}
	
	public function save_fx_rate_fromsource($source, $basecurrency, $currency) {
		if(is_empty($basecurrency, $currency, $source)) {
			return false;	
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $source);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$data = curl_exec($ch);
		curl_close($ch);		
		
		$data = str_replace('rhs:', '"rhs":', $data);
		$data = str_replace('lhs:', '"lhs":', $data);
		$data = str_replace('error:', '"error":', $data);
		$data = str_replace('icc:', '"icc":', $data);

		$data = json_decode($data, true);
		$rate = explode(' ', $data['rhs']);
		
		$this->set_fx_rate($basecurrency, $currency, $rate[0]);
	}
	
	private function load_fx_rates($source) {
		global $db;
		
		$dom = new DOMDocument();
		$dom->load($source);
		$xml_object = new SimpleXMLElement($dom->saveXML());
		
		if(!$dom) {
			return false;	
		}
				
		$currencies_rates = array();
		// Later a DB table value
		$source_settings = array(
		'rate_attr' => 'rate',
		'currency_attr' => 'currency',
		'date_attr' => 'time',
		'date_format' => 'Y-m-j',
		'parent_element' => 'Cube',
		'depth_to_rates' => 3,
		'depth_to_date' => 2,
		'base_currency' => 'EUR'
		);
		
		$rates_element = $xml_object;
		//foreach($xml_object->children() as $rates_element) {
			for($i = 0; $i < $source_settings['depth_to_rates']; $i++) {
				$rates_element = $rates_element->{$source_settings['parent_element']};
				
				if(($i+1) == $source_settings['depth_to_date']) {
					if(isset($rates_element[$source_settings['date_attr']])) {
						$rates['date'] = date_create_from_format($source_settings['date_format'], $rates_element[$source_settings['date_attr']]);
						$rates['date'] = date_timestamp_get($rates['date']);
					}
					else
					{
						$rates['date'] = TIME_NOW;	
					}
				}
			}
		
			$convert_rates = false;
			if($source_settings['base_currency'] != $this->base_currency) {				
				$convert_rates = true;
			}
		
			$convert_rates_to = 0;
			foreach($rates_element as $rate) {
				if($convert_rates == true) {
					if($rate[$source_settings['currency_attr']] == $this->base_currency) {
						$convert_rates_to = 1/floatval($rate[$source_settings['rate_attr']]);
					}
				}
				$currency_string = strval($rate[$source_settings['currency_attr']]);
				$rate_string =  floatval($rate[$source_settings['rate_attr']]);
				$currencies_rates[$currency_string] = $rate_string;	
				$cache['currencies'][] = '"'.$currency_string.'"';
			}
			
			$cache['currencies'][] = '"'.$source_settings['base_currency'].'"';
			$currencies = $this->get_currencies(array('numCode', 'alphaCode'), array('attribute' => 'alphaCode', 'values' => $cache['currencies']));
	
			foreach($currencies_rates as $currency_code => $currency_rate) {
				if($currency_code == $this->base_currency) {
					$currency_code = $source_settings['base_currency'];
					$currency_rate = $convert_rates_to;
				}
	
				if($convert_rates == true && $currency_code != $source_settings['base_currency']) {
					$currency_rate = $currency_rate*$convert_rates_to;
				}
				
				$new_fx_rate = array(
					'rate' => $currency_rate,
					'baseCurrency' => $currencies[$this->base_currency]['numCode'],
					'currency' => $currencies[$currency_code]['numCode'],
					'date' => $rates['date']
				);
	
				if($new_fx_rate['currency'] == 0) {
					continue;
				}
				
				$newfx_query = $db->insert_query('currencies_fxrates', $new_fx_rate);
				if(!$newfx_query) {
					//$this->error_handler->record('queryerror', $new_fx_rate);
				}
			}
		//}
		
		//$this->error_handler->get_errors_inline();
	}

	public function get_currencies(array $attributes = array(), array $limit_to = array()) {
		global $db;
		
		if(empty($attributes)) {
			$attributes = array('numCode', 'alphaCode');	
		}
		
		if(!in_array('numCode', $attributes)) {
			$attributes[] = 'numCode';	
		}
		
		if(!empty($limit_to)) {
			$query_where = ' WHERE '.$limit_to['attribute'].' IN ('.implode(', ', $limit_to['values']).')';
		}
		
		$query = $db->query("SELECT ".implode(', ', $attributes)." FROM ".Tprefix."currencies{$query_where}");
		while($currency = $db->fetch_assoc($query)) {
			$currencies[$currency['alphaCode']] = $currency;
		}
		
		return $currencies; 
	}
	
	public function get_currency_by_alphacode($alphacode) {
		global $db;
		
		if(empty($alphacode)) {
			return false;	
		}
		
		//if($this->cache['currencies'][
		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($alphacode)."'"));
	}
}
?>