<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationSalesOrder.php
 * Created:        @zaher.reda    Jul 6, 2015 | 9:56:31 PM
 * Last Update:    @zaher.reda    Jul 6, 2015 | 9:56:31 PM
 */

/**
 * Description of IntegrationMediationSalesOrder
 *
 * @author zaher.reda
 */
class IntegrationMediationSalesOrders extends AbstractClass {
    protected $status = 0;
    protected $data = array();

    const PRIMARY_KEY = 'imsoid';
    const TABLE_NAME = 'integration_mediation_salesorders';
    const DISPLAY_NAME = 'foreignId';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_localsalesrep() {
        return new Users($this->salesRepLocalId);
    }

    protected function update(array $data) {

    }

}