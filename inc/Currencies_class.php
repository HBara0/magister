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

class Currencies extends AbstractClass {
    protected $base_currency = 'USD';
    protected $data = array();
    protected $error_handler = NULL;

    const TABLE_NAME = 'currencies';
    const PRIMARY_KEY = 'numCode';
    const DISPLAY_NAME = 'alphaCode';
    const SIMPLEQ_ATTRS = 'numCode, alphaCode, name';
    const CLASSNAME = __CLASS__;

    private $cachearr = '';

    public function __construct($base_currency) {
        $this->error_handler = new ErrorHandler(true);
        $this->base_currency = $base_currency;

        if(is_numeric($base_currency)) {
            $this->read($base_currency);
            $this->base_currency = $this->alphaCode;
        }
        elseif(is_string($base_currency)) {
            $this->read_byalphacode($base_currency);
        }

        $this->cache = new Cache();
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    private function read_byalphacode($id) {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'currencies WHERE alphaCode="'.$db->escape_string($id).'"'));
    }

    public function get() {
        return $this->data;
    }

    public function get_average_fxrate($currency, array $period = array(), array $options = array(), $base_currency = '') {
        global $db;
        if(empty($currency)) {
            /* $this->error_handler->record('missingparameter', 'Missing Parameter');
              $this->error_handler->get_errors_inline();
              exit */;
            return false;
        }
        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        if($currency == $base_currency) {
            return '1';
        }

        if($this->cache->iscached('fxrates', $currency.'-'.$period['from'].'-'.$period['from'].'-'.$period['year'].'-'.$period['month'].'-'.$base_currency)) {
            return $this->cache->data['fxrates'][$currency.'-'.$period['from'].'-'.$period['from'].'-'.$period['year'].'-'.$period['month'].'-'.$base_currency];
        }

        $query_where = $this->parse_period_assql($period);

        $query = $db->query("SELECT AVG(rate) AS rate
							FROM ".Tprefix."currencies_fxrates
							WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
							AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."'){$query_where}
							ORDER BY date DESC
							LIMIT 0, 1");
        $fx_rate = $db->fetch_assoc($query);
        if(!empty($fx_rate['rate'])) {
            if(isset($options['precision']) && !empty($options['precision'])) {
                $fx_rate['rate'] = round($fx_rate['rate'], $options['precision']);
            }

            $this->cache->data['fxrates'][$currency.'-'.$period['from'].'-'.$period['from'].'-'.$period['year'].'-'.$period['month'].'-'.$base_currency] = $fx_rate['rate'];
            $db->free_result($query);
            return $fx_rate['rate'];
        }
        else {
            if($base_currency != 'USD') {
                if($currency == 'USD') {
                    $usd_fx_rates[$currency] = $this->get_average_fxrate($base_currency, $period, $options, 'USD');
                    if(!empty($usd_fx_rates[$currency])) {
                        return 1 / $usd_fx_rates[$currency];
                    }
                }
                else {
                    if($currency != 'USD') {
                        $usd_fx_rates[$currency] = $this->get_average_fxrate($currency, $period, $options, 'USD');
                        $usd_fx_rates[$base_currency] = $this->get_average_fxrate($base_currency, $period, $options, 'USD');
                        if(!empty($usd_fx_rates[$base_currency])) {
                            return $usd_fx_rates[$currency] / $usd_fx_rates[$base_currency];
                        }
                    }
                }
            }
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
                    $fx_rate['rate'] = round($fx_rate['rate'], $options['precision']);
                }

                $fx_rates[$fx_rate[$options['distinct_by']]] = $fx_rate['rate'];
            }
            $db->free_result($query);
            return $fx_rates;
        }
        else {
            if($base_currency != 'USD') {
                $usd_fx_rates = $this->get_average_fxrates($currencies, $period, $options, 'USD');
                if(!empty($usd_fx_rates)) {
                    foreach($currencies as $currency) {
                        if($currency == $base_currency) {
                            if(!empty($usd_fx_rates[$currency])) {
                                $fx_rates['USD'] = 1 / $usd_fx_rates[$currency];
                            }
                        }
                        else {
                            if($currency != 'USD' && !empty($usd_fx_rates[$base_currency])) {
                                $fx_rates[$currency] = $usd_fx_rates[$currency] / $usd_fx_rates[$base_currency];
                            }
                        }
                    }
                    return $fx_rates;
                }
            }
            return 0;
        }
    }

    public function get_average_fxrates_transposed(array $currencies, array $period = array(), array $options = array(), $base_currency = '') {
        $rates = $this->get_average_fxrates($currencies, $period, $options, $base_currency);
        if($rates) {
            foreach($rates as $currency => $rate) {
                $new_rates[strval($rate)] = $currency;
                if($options['combine_values'] == true) {
                    $new_rates[strval($rate)] .= ' - '.$rate;
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
            $period['from'] = mktime(0, 0, 0, $i, 1, $year);  /* period from first day of the month of the report year */
            $period['to'] = mktime(23, 59, 59, $i, date('t', $period['from']), $year);

            if(isset($options['monthasname']) && $options['monthasname'] == true) {
                $rates[date('M', mktime(0, 0, 0, $i))] = $this->get_average_fxrate($currency, $period, $options, $base_currency);
            }
            else {
                $rates[$i] = $this->get_average_fxrate($currency, $period, $options, $base_currency);
            }
        }
        return $rates;
    }

    public function get_yearaverage_fxrate_yearbased($currency, $fromyear, $toyear, array $options = array(), $base_currency = '') {
        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        for($year = $fromyear; $year <= $toyear; $year++) {
            $period['from'] = mktime(0, 0, 0, 1, 1, $year);  /* period from first day of the month of the report year */
            $period['to'] = mktime(23, 59, 59, 12, date('t', $period['from']), $year);
            $rates[$year] = $this->get_average_fxrate($currency, $period, $options, $base_currency);
        }
        return $rates;
    }

    public function get_yearlast_fxrate($currency, $year, array $options = array(), $base_currency = '') {
        global $db;

        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        if($currency == $base_currency) {
            return 1;
        }

        if($this->cache->iscached('fxrates', $currency.'-'.$year.'-'.$base_currency)) {
            return $this->cache->data['fxrates'][$currency.'-'.$year.'-'.$base_currency];
        }

        $query_where = $this->parse_period_assql(array('from' => strtotime($year.'-1-1'), 'to' => strtotime($year.'-12-31')));

        return $this->cache->data['fxrates'][$currency.'-'.$year.'-'.$base_currency] = $db->fetch_field($db->query("SELECT rate
					FROM ".Tprefix."currencies_fxrates
					WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
					AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."')
					{$query_where}
					ORDER BY date DESC
					LIMIT 0, 1"), 'rate');
    }

    public function get_latest_fxrate($currency, $options = array(), $base_currency = '') {
        global $db;

        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        if($currency == $base_currency) {
            return 1;
        }

        if($this->cache->iscached('fxrates', $currency.'-latest-'.$base_currency)) {
            return $this->cache->data['fxrates'][$currency.'-latest-'.$base_currency];
        }

        if(isset($options['incDate']) && $options['incDate'] == 1) {
            $query_select = ', date';
            return $this->cache->data['fxrates'][$currency.'-latest-'.$base_currency] = $db->fetch_assoc($db->query("SELECT rate".$query_select."
				FROM ".Tprefix."currencies_fxrates
				WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
				AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."')
				ORDER BY date DESC
				LIMIT 0, 1"));
        }
        else {
            return $this->cache->data['fxrates'][$currency.'-latest-'.$base_currency] = $db->fetch_field($db->query("SELECT rate".$query_select."
				FROM ".Tprefix."currencies_fxrates
				WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
				AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."')
				ORDER BY date DESC
				LIMIT 0, 1"), 'rate');
        }
    }

    public function get_lastmonth_fxrate($currency, $period, array $options = array(), $base_currency = '') {
        global $db;

        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        if($currency == $base_currency) {
            return 1;
        }

        $period['month'] = 12;
        if($period['year'] == date('Y', TIME_NOW)) {
            $period['month'] = date('m', strtotime('last month'));
            if($period['month'] == 12) {
                $period['year'] -= 1;
            }
        }

        if($this->cache->iscached('fxrates', $currency.'-'.$period['year'].'-'.$period['month'].'-'.$base_currency)) {
            return $this->cache->data['fxrates'][$currency.'-'.$period['year'].'-'.$period['month'].'-'.$base_currency];
        }

        $query_where = $this->parse_period_assql(array('from' => strtotime($period['year'].'-'.$period['month'].'-1'), 'to' => strtotime($period['year'].'-'.$period['month'].'-1 +1month -1sec')));

        return $this->cache->data['fxrates'][$currency.'-'.$period['year'].'-'.$period['month'].'-'.$base_currency] = $db->fetch_field($db->query("SELECT rate
			FROM ".Tprefix."currencies_fxrates
			WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
			AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."')
			{$query_where}
			ORDER BY date DESC
			LIMIT 0, 1"), 'rate');
    }

    public function get_any_rate($currency, $period, array $options = array(), $base_currency = '') {
        global $db;

        if(empty($base_currency)) {
            $base_currency = $this->base_currency;
        }

        if($currency == $base_currency) {
            return 1;
        }

        return $db->fetch_field($db->query(
                                "SELECT abs(".$period['from']."-date) as datedelta,rate
			FROM ".Tprefix."currencies_fxrates
			WHERE baseCurrency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($base_currency)."')
			AND currency=(SELECT numCode FROM ".Tprefix."currencies WHERE alphaCode='".$db->escape_string($currency)."')
			ORDER BY datedelta ASC
			LIMIT 0, 1"), 'rate');
    }

    public function get_fxrate_bytype() {
        $args = func_get_args();
        if(!is_array($args)) {
            return false;
        }
        if(!isset($args[3])) {
            $args[3] = array();
        }
        switch($args[0]) {
            case 'mavg':
                return $this->get_average_fxrate($args[1], array('from' => strtotime($args[2]['year'].'-'.$args[2]['month'].'-1'), 'to' => strtotime($args[2]['year'].'-'.($args[2]['month'] + 1).'-1 +1month -1sec')), $args[3], $args[4]);
                break;
            case 'yavg':
                return $this->get_average_fxrate($args[1], array('from' => strtotime($args[2]['year'].'-1-1'), 'to' => strtotime($args[2]['year'].'-12-31')), $args[3], $args[4]);
                break;
            case 'ylast':
                return $this->get_yearlast_fxrate($args[1], $args[2]['year'], $args[3], $args[4]);
                break;
            case 'lastm':
                return $this->get_lastmonth_fxrate($args[1], $args[2], $args[3], $args[4]);
                break;
            case 'real':
            default:
                return $this->get_average_fxrate($args[1], $args[2], $args[3], $args[4]);
                break;
        }
        return false;
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
        else {
            $query_where = ' AND date >='.strtotime('today');
        }
        return $query_where;
    }

    public function set_fx_rates($source) {
        $this->load_fx_rates($source);
    }

    public function set_fx_rate($basecurrency, $currency, $rate, $date = TIME_NOW) {
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

    public function save_fx_rate_fromsource($source, $basecurrency, $currency, $rateproperty = 'rate') {
        if(is_empty($basecurrency, $currency, $source)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

//		$data = str_replace('rhs:', '"rhs":', $data);
//		$data = str_replace('lhs:', '"lhs":', $data);
//		$data = str_replace('error:', '"error":', $data);
//		$data = str_replace('icc:', '"icc":', $data);

        $data = json_decode($data, true);
        //$rate = explode(' ', $data['rhs']);
        if(is_array($data[$rateproperty])) {
            $data[$rateproperty] = current($data[$rateproperty]);
        }
        $this->set_fx_rate($basecurrency, $currency, $data[$rateproperty]);
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

            if(($i + 1) == $source_settings['depth_to_date']) {
                if(isset($rates_element[$source_settings['date_attr']])) {
                    $rates['date'] = date_create_from_format($source_settings['date_format'], $rates_element[$source_settings['date_attr']]);
                    $rates['date'] = date_timestamp_get($rates['date']);
                }
                else {
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
                    $convert_rates_to = 1 / floatval($rate[$source_settings['rate_attr']]);
                }
            }
            $currency_string = strval($rate[$source_settings['currency_attr']]);
            $rate_string = floatval($rate[$source_settings['rate_attr']]);
            $currencies_rates[$currency_string] = $rate_string;
            $cachearr['currencies'][] = '"'.$currency_string.'"';
        }

        $cachearr['currencies'][] = '"'.$source_settings['base_currency'].'"';
        $currencies = $this->get_currencies(array('numCode', 'alphaCode'), array('attribute' => 'alphaCode', 'values' => $cachearr['currencies']));

        foreach($currencies_rates as $currency_code => $currency_rate) {
            if($currency_code == $this->base_currency) {
                $currency_code = $source_settings['base_currency'];
                $currency_rate = $convert_rates_to;
            }

            if($convert_rates == true && $currency_code != $source_settings['base_currency']) {
                $currency_rate = $currency_rate * $convert_rates_to;
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

    public function validate_currency() {
        if(empty($this->data['alphaCode'])) {
            return null;
        }
        else {
            return $this;
        }
    }

    /**
     *
     * @param type $from timestamp
     * @param type $to  timestamp
     * @param type $fromcurrencies array (currency ids)
     * @param type $tocurrencies array(currency ids)
     * @return boolean or returns array of missing rates by date
     */
    public function get_missingfxrates($from, $to, $fromcurrencies, $tocurrencies) {
        global $db, $log;
        $cache = new Cache();
        if($from > $to) {
            return false;
        }
        $to_datetimeobject = new DateTime();
        $to_datetimeobject->setTimestamp($to);
        $from_datetimeobject = new DateTime();
        $from_datetimeobject->setTimestamp($from);
        if(is_array($fromcurrencies) && is_array($tocurrencies)) {
            if(is_object($to_datetimeobject) && is_object($from_datetimeobject)) {
                while($from_datetimeobject->format('Y-m-d') != $to_datetimeobject->format('Y-m-d')) {
                    $from_datetimeobject->add(new DateInterval('P1D'));
                    $insert_data['date'] = $from_datetimeobject->getTimestamp();
                    foreach($fromcurrencies as $fromcurrency) {
                        $beginOfDay = strtotime("midnight", $insert_data['date']);
                        $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;
                        $existingfxrate = CurrenciesFxRate::get_column('currency', 'currency IN ('.implode(',', $tocurrencies).') AND date BETWEEN '.$beginOfDay.' AND '.$endOfDay);
                        if(is_array($existingfxrate)) {
                            $exchcurrency = array_diff($existingfxrate, $tocurrencies);
                        }
                        else {
                            $exchcurrency = $tocurrencies;
                        }
                        $missingrates = self::get_historical_fxrates($from_datetimeobject, $exchcurrency, $fromcurrency);
                        if(is_array($missingrates)) {
                            $insert_data['baseCurrency'] = $fromcurrency;
                            if(is_array($missingrates['quotes'])) {
                                foreach($missingrates['quotes'] as $rawcurrency => $rate) {
                                    $actual_currency = substr($rawcurrency, 3);
                                    if(!empty($actual_currency)) {
                                        if(!$cache->iscached('currency', $actual_currency)) {
                                            $currency_obj = new Currencies($actual_currency);
                                            if(is_object($currency_obj) && !empty($currency_obj->numCode)) {
                                                $cache->add('currency', $currency_obj->numCode, $actual_currency);
                                            }
                                            else {
                                                continue;
                                            }
                                        }
                                        $insert_data['currency'] = $cache->get_cachedval('currency', $actual_currency);
                                        $insert_data['rate'] = $rate;
                                        if(CurrenciesFxRate::validate_requiredfields($inserdata)) {
                                            $query = $db->insert_query(CurrenciesFxRate::TABLE_NAME, $insert_data);
                                            if($query) {
                                                $log->record(CurrenciesFxRate::TABLE_NAME.'historicalapi', $db->last_id());
                                            }
                                            unset($insert_data['rate'], $insert_data['currency']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     *
     * @param type $date
     * @param type $tocurrencies
     * @param type $sourcecurrency
     * @return boolean
     */
    public function get_historical_fxrates($date, $tocurrencies, $sourcecurrency = '840') {
        if(is_array($tocurrencies)) {
            foreach($tocurrencies as $currency_id) {
                $currency_obj = new Currencies($currency_id);
                if(is_object($currency_obj) && !empty($currency_obj->numCode)) {
                    $findcurrencies_names[] = $currency_obj->alphaCode;
                }
            }
        }
        if(is_array($findcurrencies_names)) {
            if(is_object($date)) {
                $date = $date->format('Y-m-d');
            }
            elseif(is_numeric($date)) {
                $date = date('Y-m-d', $date);
            }
            $findcurrencies_names = implode(',', $findcurrencies_names);
            $sourcecurrency_obj = new Currencies($sourcecurrency);
            if(!is_object($sourcecurrency_obj)) {
                return false;
            }
            $sourcecurrency = $sourcecurrency_obj->alphaCode;
// Initialize CURL:
            $access_key = 'cc64c9d2fc1eb255343e8d271e59149f';
            $endpoint = 'historical';
            $findcurrencies_names = '&currencies ='.$findcurrencies_names;
            $cdf = 'http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&date='.$date.'&source='.$sourcecurrency.$findcurrencies_names;
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&date='.$date.'&source='.$sourcecurrency.$findcurrencies_names);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

// Decode JSON response:
            $results = json_decode($json, true);
            if(isset($results['success']) && $results['success'] == true) {
                return $results;
            }
        }

        return false;
    }

}
?>