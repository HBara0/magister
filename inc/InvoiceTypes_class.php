<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: InvoiceTypes_class.php
 * Created:        @rasha.aboushakra    Sep 30, 2014 | 11:16:22 AM
 * Last Update:    @rasha.aboushakra    Sep 30, 2014 | 11:16:22 AM
 */

Class InvoiceTypes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stiid';
    const TABLE_NAME = 'saletypes_invoicing';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'stiid, affid, stid, invoicingEntity, isAffiliate, invoiceAffid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

    public function get_invoiceentity() {
        if($this->data['isAffiliate'] == 1) {
            $entity = Affiliates::get_affiliates(array('affid' => $this->data['invoiceAffid']));
            return $entity->get_displayname();
        }
        else {
            return $this->data['invoicingEntity'];
        }
    }

}
?>