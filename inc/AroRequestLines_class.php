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
        global $db, $core, $log;
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
            unset($data['qtyPotentiallySold_disabled'], $data['daysInStock_disabled']);
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
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
            unset($data['qtyPotentiallySold_disabled'], $data['daysInStock_disabled']);
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
        $fees = $inter_com = $riskratio = 10;

        /* Get Aro request order type - Start */
        if(empty($data['ptid']) && !empty($data['aorid'])) {
            $aroorderrequest = AroOrderRequest::get_data(array('aorid' => $data['aorid']));
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

        if(is_object($purchasetype)) {
            $data['affBuyingPrice'] = round((($data['intialPrice'] + $fees) + ($data['intialPrice'] * $inter_com)), 2);
            $data['totalBuyingValue'] = round($data['quantity'] * $data['affBuyingPrice'], 2);
            if($purchasetype->isPurchasedByEndUser == 1) {
                $data['affBuyingPrice'] = '-';
                $data['totalBuyingValue'] = round($data['intialPrice'] * $data['quantity'], 2);
            }
            $data['qtyPotentiallySold_disabled'] = $data['daysInStock_disabled'] = 1;
            if($purchasetype->qtyIsNotStored == 1) {
                $data['qtyPotentiallySold_disabled'] = 0;
                $data['daysInStock_disabled'] = 0;
            }
        }
        if(isset($data['quantity']) && !empty($data['quantity'])) {
            $data['costPriceAtRiskRatio'] = round(($data['costPrice'] + (($data['totalBuyingValue'] * $riskratio) / $data['quantity'])), 2);
        }
        $data['grossMarginAtRiskRatio'] = round((($data['sellingPrice'] - $data['costPriceAtRiskRatio']) * $data['quantity']), 2);

        $data['netMargin'] = $this->calculate_netmargin($data, $purchasetype);
        unset($data['exchangeRateToUSD']);


        return $data;
    }

    private function calculate_netmargin($data, $purchasetype) {
        $exchangeRateToUSD = $data['exchangeRateToUSD'];

        if($purchasetype->title == 'RIC') {
            //   return ($data['grossMarginAtRiskRatio'] - (($data['quantity'] * $data['costPriceAtRiskRatio']) * BR / years * pol) * $exchangeRateToUSD);
        }
        else {
            //   return (($data['grossMarginAtRiskRatio'] - (($data['quantity'] * $data['affBuyingPrice'] * BR) / (years * pol))) * $exchangeRateToUSD);
            //    ((N27-((D27*I27*LocalBR)/YearDays*POI_L))*X_USD-(((WH_TL*D27)/TotalQty)*(E27/WH_PR)*WH_R))
        }
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