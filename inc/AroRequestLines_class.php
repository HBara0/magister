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
    protected $errorcode = 0;

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
        global $db, $core;

        $data = $this->calculate_values();
        //  if(!$this->validate_requiredfields($data)) {
        $query = $db->insert_query(self::TABLE_NAME, $data);
        // }
    }

    protected function update(array $data) {

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
            $purchasetype = new PurchaseTypes(array('ptid' => $data['ptid']));
            unset($data['ptid']);
        }
        /* Get Aro request order type - End */

        if(isset($data['quantity']) && !empty($data['quantity'])) {
            if(!isset($data['qtyPotentiallySoldPerc']) && isset($data['qtyPotentiallySold'])) {
                $data['qtyPotentiallySoldPerc'] = round(($data['qtyPotentiallySold'] / $data['quantity']) * 100, 2);
            }
        }
        if(isset($data['qtyPotentiallySoldPerc']) && !empty($data['qtyPotentiallySoldPerc'])) {
            $data['qtyPotentiallySold'] = (($data['qtyPotentiallySoldPerc'] * $data['quantity']) / 100);
        }

        if(is_object($purchasetype)) {
            $data['affBuyingPrice'] = round((($data['intialPrice'] + $fees) + ($data['intialPrice'] * $inter_com)), 2);
            $data['totalBuyingValue'] = round($data['quantity'] * $data['affBuyingPrice'], 2);
            if($purchasetype->isPurchasedByEndUser == 1) {
                $data['affBuyingPrice'] = '-';
                $data['totalBuyingValue'] = round($data['intialPrice'] * $data['quantity'], 2);
            }
        }
        if(isset($data['quantity']) && !empty($data['quantity'])) {
            $data['costPriceAtRiskRatio'] = round(($data['costPrice'] + (($data['totalBuyingValue'] * $riskratio) / $data['quantity'])), 2);
        }
        $data['grossMarginAtRiskRatio'] = round((($data['sellingPrice'] - $data['costPriceAtRiskRatio']) * $data['quantity']), 2);

        return $data;
    }

}