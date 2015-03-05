<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestLines_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:29:55 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:29:55 PM
 */

class AroRequestLines extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'arlid';
    const TABLE_NAME = 'aro_requests_lines';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'pid,packing,daysInStock,intialPrice';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $data = $this->calculate_values();
            if(empty($data['psid'])) {
                $product = new Products($data['pid']);
                $data['psid'] = $product->get_segment()['psid'];
            }
            if($data['costPrice'] < $data['affBuyingPrice']) {
                $this->errorcode = 3;
                return;
            }
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $data = $this->calculate_values();
            if(empty($data['psid'])) {
                $product = new Products($data['pid']);
                $data['psid'] = $product->get_segment()['psid'];
            }
            if($data['costPrice'] < $data['affBuyingPrice']) {
                $this->errorcode = 3;
                return;
            }
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    public function calculate_values(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        $parmsfornetmargin = $data['parmsfornetmargin'];
        $parmsfornetmargin['localBankInterestRate'] = $parmsfornetmargin['localBankInterestRate'] / 100;
        $parmsfornetmargin['localBankInterestRate'] = $parmsfornetmargin['localBankInterestRate'] / 100;
        $parmsfornetmargin['intermedBankInterestRate'] = $parmsfornetmargin['intermedBankInterestRate'] / 100;
        //   $parmsfornetmargin['intermedPeriodOfInterest'] = $parmsfornetmargin['intermedPeriodOfInterest'] / 100;
        // $parmsfornetmargin['riskRatio'] = $parmsfornetmargin['riskRatio'] / 100;
        $parmsfornetmargin['fees'] = 10;
        $parmsfornetmargin['inter_com'] = 8 / 100;
        $parmsfornetmargin['riskRatio'] = 3 / 100;
        $parmsfornetmargin['totalQty'] = 37;
        //$parmsfornetmargin=array('fees' => 10,
        ////       'inter_com' => 10 / 100,
        //      'riskRatio' => 3 / 100,
        //  'localBankInterestRate' => 8 / 100,
        // 'localPeriodOfInterest' => 0,
        // 'warehousingTotalLoad' => 10,
        // 'warehousingPeriod' => 1,
        // 'warehousingRate' => 0.25,
        //    'totalQty' => 37.500
        //   );
        $parmsfornetmargin['YearDays'] = 365;
        unset($data['parmsfornetmargin']);

        /* Get Aro request order type - Start */
        if(empty($data['ptid']) && !empty($data['aorid'])) {
            $aroorderrequest = AroRequests::get_data(array('aorid' => $data['aorid']));
            $data['ptid'] = $aroorderrequest->orderType;
        }
        if(isset($data['ptid']) && !empty($data['ptid'])) {
            $purchasetype = new PurchaseTypes($data['ptid']);
            unset($data['ptid']);
        }
        /* Get Aro request order type - End */

        if(isset($data['quantity']) && !empty($data['quantity'])) {
            if(!isset($data['qtyPotentiallySoldPerc']) && isset($data['qtyPotentiallySold'])) {
                $data['qtyPotentiallySoldPerc'] = round(($data['qtyPotentiallySold'] / $data['quantity']) * 100, 2);
            }
            else {
                $data['qtyPotentiallySold'] = (($data['qtyPotentiallySoldPerc'] * $data['quantity']) / 100);
            }
        }
        if(empty($data['qtyPotentiallySold'])) {
            $data['qtyPotentiallySoldPerc'] = 0;
        }

        if(is_object($purchasetype)) {
            $data['affBuyingPrice'] = round((($data['intialPrice'] + $parmsfornetmargin['fees']) + ($data['intialPrice'] * $parmsfornetmargin['inter_com'])), 2);
            $data['totalBuyingValue'] = round($data['quantity'] * $data['affBuyingPrice'], 2);
            if($purchasetype->isPurchasedByEndUser == 1) {
                $data['affBuyingPrice'] = '-';
                $data['totalBuyingValue'] = round($data['intialPrice'] * $data['quantity'], 2);
            }
        }
        if(isset($data['quantity']) && !empty($data['quantity'])) {
            $data['costPriceAtRiskRatio'] = ceil(($data['costPrice'] + (($data['totalBuyingValue'] * $parmsfornetmargin['riskRatio']) / $data['quantity'])));
        }
        $data['grossMarginAtRiskRatio'] = floor((($data['sellingPrice'] - $data['costPriceAtRiskRatio']) * $data['quantity']));

        if($purchasetype->isPurchasedByEndUser == 1) {
            $data['daysInStock'] = 0;
            $data['qtyPotentiallySold'] = 0;
            $data['qtyPotentiallySoldPerc'] = 0;
        }
        $data['netMargin'] = round($this->calculate_netmargin($purchasetype, $data, $parmsfornetmargin), 2);

        if((($data['sellingPrice'] * $data['quantity']) * $data['exchangeRateToUSD']) != 0) {
            $data['netMarginPerc'] = round($data['netMargin'] / (( $data['sellingPrice'] * $data['quantity']) * $data['exchangeRateToUSD']), 2);
        }
        unset($data['exchangeRateToUSD']);
        return $data;
    }

    private function calculate_netmargin($purchasetype, $data = array(), $parms = array()) {
        $parmsfornetmargin['YearDays'] = 365;

        if($parms['localPeriodOfInterest'] != 0 && $parms['warehousingPeriod'] != 0) {
            $netmargin = (($data['grossMarginAtRiskRatio'] - (($data['quantity'] * $data['affBuyingPrice'] * $parms['localBankInterestRate']) / ( $parmsfornetmargin['YearDays'] * $parms['localPeriodOfInterest']))) * $data['exchangeRateToUSD']);
            $netmargin -= ((($parms['warehousingTotalLoad'] * $data['quantity']) / $parms['totalQty']) * ($data['daysInStock'] / $parms['warehousingPeriod']) * $parms['warehousingRate']);
        }
        if($purchasetype->isPurchasedByEndUser == 1) {
            if($parms['localPeriodOfInterest'] != 0) {
                $netmargin = ($data['grossMarginAtRiskRatio'] - (($data['quantity'] * $data['costPriceAtRiskRatio']) * $parms['intermedBankInterestRate'] / $parmsfornetmargin['YearDays'] * $parms['intermedPeriodOfInterest']) * $data['exchangeRateToUSD']);
            }
        }
        return $netmargin;
    }

    private function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}