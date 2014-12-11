<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: RepresentativePositions_class.php
 * Created:        @rasha.aboushakra    Dec 10, 2014 | 9:51:42 AM
 * Last Update:    @rasha.aboushakra    Dec 10, 2014 | 9:51:42 AM
 */

class RepresentativePositions extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'rppid';
    const TABLE_NAME = 'representativespositions';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'rppid,rpid,posid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_representative() {
        return new Representatives($this->data['rpid']);
    }

    public function get_position() {
        return Positions::get_data(array('posid' => $this->data['posid']));
    }

}