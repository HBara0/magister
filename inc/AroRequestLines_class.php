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
        $data = $this->calculate_values($data, true);
//        if(empty($data['psid'])) {
//            $product = new Products($data['pid']);
//            $data['psid'] = $product->get_segment()['psid'];
//        }
        unset($data['ptid'], $data['commission']);
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
    }

    protected function update(array $data) {
        global $db, $log;
        $data = $this->calculate_values($data, true);
//        if(empty($data['psid'])) {
//            $product = new Products($data['pid']);
//            $data['psid'] = $product->get_segment()['psid'];
//        }
        unset($data['ptid'], $data['commission']);
        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
    }

    public function calculate_values(array $data = array(), $returnall = false) {
        if(empty($data)) {
            $data = $this->data;
        }
        if(isset($parmsfornetmargin['totalDiscount']) && !empty($parmsfornetmargin['totalDiscount'])) {
            $parmsfornetmargin['commission'] = $parmsfornetmargin['commission'] - $parmsfornetmargin['totalDiscount'];
        }
        if(isset($data['totalDiscount']) && !empty($data['totalDiscount'])) {
            $data['commission'] = $data['commission'] - $data['totalDiscount'];
        }
        $parmsfornetmargin = $data['parmsfornetmargin'];
        $parmsfornetmargin['localBankInterestRate'] = $parmsfornetmargin['localBankInterestRate'] / 100;
        $parmsfornetmargin['intermedBankInterestRate'] = $parmsfornetmargin['intermedBankInterestRate'] / 100;
        $parmsfornetmargin['commission'] = $parmsfornetmargin['commission'] / 100;
        $parmsfornetmargin['riskRatio'] = $parmsfornetmargin['localRiskRatio'] / 100;
        $parmsfornetmargin['YearDays'] = 365;
        unset($data['parmsfornetmargin']);

        $data['commission'] = $data['commission'] / 100;
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
            if($data['quantity'] != 0 && ((isset($data['qtyPotentiallySold']) && !empty($data['qtyPotentiallySold'])) || ($data['qtyPotentiallySold'] == 0))) {
                $new_data['qtyPotentiallySoldPerc'] = round(($data['qtyPotentiallySold'] / $data['quantity']) * 100, 3);
            }
        }

        if(is_object($purchasetype)) {
            $affbuyingprice_data = array('intialPrice' => $data['intialPrice'], 'commission' => $data['commission'], 'unitfees' => $parmsfornetmargin['unitfees'], 'commission' => $parmsfornetmargin['commission'], 'isPurchasedByEndUser' => $purchasetype->isPurchasedByEndUser);
            $new_data['affBuyingPrice'] = $this->calculate_affbuyingprice($affbuyingprice_data);
            //  $data['affBuyingPrice'] = round((($data['intialPrice'] + $parmsfornetmargin['unitfees']) + ($data['intialPrice'] * $parmsfornetmargin['commission'])), 2);
            $new_data['totalBuyingValue'] = $this->calculate_totalbuyingvalue(array('affBuyingPrice' => $new_data['affBuyingPrice'], 'quantity' => $data['quantity'], 'intialPrice' => $data['intialPrice'], 'isPurchasedByEndUser' => $purchasetype->isPurchasedByEndUser));
            // $data['totalBuyingValue'] = round($data['quantity'] * $data['affBuyingPrice'], 2);
//            if($purchasetype->isPurchasedByEndUser == 1) {
//                $data['affBuyingPrice'] = '-';
//                $data['totalBuyingValue'] = round($data['intialPrice'] * $data['quantity'], 2);
//            }
        }
        if(isset($data['quantity']) && !empty($data['quantity'])) {
            $new_data['costPriceAtRiskRatio'] = round(($data['costPrice'] + (($new_data['totalBuyingValue'] * $parmsfornetmargin['riskRatio']) / $data['quantity'])), 2);
        }
        $new_data['grossMarginAtRiskRatio'] = 0;
        if(!empty($data['sellingPrice'])) {
            $new_data['grossMarginAtRiskRatio'] = round((($data['sellingPrice'] - $new_data['costPriceAtRiskRatio']) * $data['quantity']), 2);
        }
        else {
            if(empty($data['sellingPrice']) && !empty($new_data['costPriceAtRiskRatio'])) {
                $new_data['grossMarginAtRiskRatio'] = 0;
            }
        }
        $new_data['riskRatioAmount'] = round(($new_data['costPriceAtRiskRatio'] * $data['quantity']), 2);
        if($purchasetype->isPurchasedByEndUser == 1) {
            $new_data['daysInStock'] = 0;
            $new_data['qtyPotentiallySold'] = 0;
            $new_data['qtyPotentiallySoldPerc'] = 100;
        }
        else {
            if(empty($data['qtyPotentiallySold'])) {
                $new_data['qtyPotentiallySoldPerc'] = 0;
            }
        }
        $new_data['netMargin'] = round($this->calculate_netmargin($purchasetype, $data, $new_data, $parmsfornetmargin), 2);

        if((($data['sellingPrice'] * $data['quantity']) * $data['exchangeRateToUSD']) != 0) {
            $new_data['netMarginPerc'] = round(($new_data['netMargin'] / (( $data['sellingPrice'] * $data['quantity']) * $data['exchangeRateToUSD'])) * 100, 2);
        }
        unset($data['exchangeRateToUSD']);

        if($returnall === true) {
            $new_data = array_merge($data, $new_data);
        }
        return $new_data;
    }

    private function calculate_netmargin($purchasetype, $data = array(), $newdata = array(), $parms = array()) {
        $parmsfornetmargin['YearDays'] = 365;
        $netmargin = (($newdata['grossMarginAtRiskRatio'] - ((($data['quantity'] * $newdata['affBuyingPrice'] * $parms['localBankInterestRate']) / $parmsfornetmargin['YearDays']) * $parms['localPeriodOfInterest'])) * $data['exchangeRateToUSD']);
        if($parms['warehousingPeriod'] != 0 && $parms['warehousingRate'] != 0 && $parms['totalQty'] != 0) {
            $netmargin -= ((($parms['warehousingTotalLoad'] * $data['quantity']) / $parms['totalQty']) * ($data['daysInStock'] / $parms['warehousingPeriod']) * $parms['warehousingRate']);
        }
        if($purchasetype->isPurchasedByEndUser == 1) {
            $netmargin = ($newdata['grossMarginAtRiskRatio'] - (($data['quantity'] * $newdata['costPriceAtRiskRatio']) * ($parms['intermedBankInterestRate'] / $parmsfornetmargin['YearDays']) * $parms['intermedPeriodOfInterest'])) * $data['exchangeRateToUSD'];
        }
        return $netmargin;
    }

    public function calculate_affbuyingprice($data = array()) {
        if($data['isPurchasedByEndUser'] == 1) {
            return '-';
        }
        return round((($data['intialPrice'] + $data['unitfees']) + ($data['intialPrice'] * $data['commission'])), 2);
    }

    public function calculate_totalbuyingvalue($data = array()) {
        if($data['isPurchasedByEndUser'] == 1) {
            return round($data['intialPrice'] * $data['quantity'], 2);
        }
        else {
            return round($data['quantity'] * $data['affBuyingPrice'], 2);
        }
    }

    public function validate_requiredfields(array $data = array()) {
        global $errorhandler, $lang;
        if(empty($data)) {
            $data = $this->data;
        }
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $errorhandler->record('Required fields', $lang->$field);
                    $this->errorcode = 2;
                    return;
                }
            }
            if($data['costPrice'] < $data['affBuyingPrice']) {
                $this->errorcode = 3;
                return;
            }
        }
    }

}