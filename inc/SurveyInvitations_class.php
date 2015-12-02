<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveyInvitations_class.php
 * Created:        @hussein.barakat    02-Dec-2015 | 15:20:22
 * Last Update:    @hussein.barakat    02-Dec-2015 | 15:20:22
 */

class SurveyInvitations extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'siid';
    const TABLE_NAME = 'surveys_invitations';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}