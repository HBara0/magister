<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsCurStkSupervision.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 */

class AroRequestsCurStkSupervision extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arcssid';
    const TABLE_NAME = 'aro_requests_curstksupervision';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    // const UNIQUE_ATTRS = 'aorid,pid,packing';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            //$currentstock['aorid'] = $data['aorid'];
            unset($data['packingTitle']);
            $dates = array('dateOfStockEntry', 'expiryDate', 'estDateOfSale');
            foreach($dates as $date) {
                if(isset($data[$date]) && !empty($data[$date])) {
                    $data[$date] = strtotime($data[$date]);
                }
            }
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                return $this;
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            unset($data['packingTitle']);
            $dates = array('dateOfStockEntry', 'expiryDate', 'estDateOfSale');
            foreach($dates as $date) {
                if(isset($data[$date]) && !empty($data[$date])) {
                    $data[$date] = strtotime($data[$date]);
                }
            }
            $query = $db->update_query(self::TABLE_NAME, $data, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                return $this;
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        global $errorhandler;
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity', 'packing');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $errorhandler->record('Required fields', $field);
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    /**
     * ARO Lead time analysis
     * -add a line at the end of the stock list:
      Monthly average sales of each product. (last 12 months, last 3 months, next 3 months of previous year)
      Then divide the remaining stock by those and give 3 average remaining days of stock.
      In red if the product will arrive in the warehouse after those 3 numbers, in green if the remaining days of stock when the product will enter the warehouse will be below 90 days, and in red also if above 90 days.
     * @global type $db
     * @param string $filters
     * @return string
     */
    public function get_monthlyaveragesales($filters, $stockentrydate) {
        global $db, $lang;

        require_once ROOT.INC_ROOT.'integration_config.php';
        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);

        $formatter = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);


        $foreignId = $db->fetch_field($db->query('SELECT foreignId FROM integration_mediation_products WHERE foreignSystem=3 AND localId="'.$this->pid.'"'), 'foreignId');
        if(!empty($foreignId)) {
            if(!empty($filters)) {
                $intgdb = $integration->get_dbconn();
                $invoicelines_obj = new IntegrationOBInvoiceLine(null, $intgdb);
                $periods = array('12' => '-12 months', '3' => '-3 months', '+3' => '+3 months');
                foreach($periods as $numberofmonths => $period) {
                    $periodrange['basedate'] = TIME_NOW;
                    $periodrange['to'] = $periodrange['basedate'];
                    switch($period) {
                        case '+3 months':
                            $key = 3;
                            $periodrange['from'] = strtotime('-1 years', $periodrange['basedate']);
                            $periodrange['to'] = strtotime($period, $periodrange['from']);
                            break;
                        default:
                            $periodrange['from'] = strtotime($period, $periodrange['basedate']);
                            break;
                    }
                    $filters = " (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', $periodrange['fromdate'])."' AND '".date('Y-m-d 00:00:00', $periodrange['to'])."')";
                    $invoicelines = $invoicelines_obj->get_salesinvoicesummary($foreignId, $filters);
                    if(is_array($invoicelines)) {
                        foreach($invoicelines as $invoiceline) {
                            $data['salesqty'][$period] += $invoiceline['qtyinvoiced'];
                            // $data['salesamt'][$period] += $invoiceline['linenetamt'];
                        }
                        $data['avgsalesqty'][$period] +=$data['salesqty'][$period] / $numberofmonths;
                        // $data['avgsalesamt'][$period] +=$data['salesamt'][$period] / $numberofmonths;
                        if($data['avgsalesqty'][$period] != 0) {
                            $data['daysofstock'][$period] = ($this->quantity / $data['avgsalesqty'][$period]) * 30;
                        }
                        $output .='<td class="border_right">'.$formatter->format($data['avgsalesqty'][$period]).'</td>';
                    }
                }
            }
            if(is_array($data['daysofstock'])) {
                if(!empty($stockentrydate)) {
                    $datediff = $periodrange['basedate'] - $stockentrydate;
                    $diff = abs(floor($datediff / (60 * 60 * 24)));
                }
                foreach($data['daysofstock'] as $daysofstock) {
                    if($diff > $daysofstock) {
                        $style['color'] = 'color:red';
                    }
                    else if($diff > 90) {
                        $style['color'] = 'color:red';
                    }
                    else {
                        $style['color'] = 'color:green';
                    }
                    $output .='<td class="border_right" style='.$style['color'].'>'.$formatter->format($daysofstock).'</td>';
                }
            }
            return $output;
        }
    }

}