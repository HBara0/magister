<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveyAssociations_class.php
 * Created:        @rasha.aboushakra    Oct 14, 2015 | 12:12:54 PM
 * Last Update:    @rasha.aboushakra    Oct 14, 2015 | 12:12:54 PM
 */

/**
 * Description of SurveyAssociations_class
 *
 * @author rasha.aboushakra
 */
class SurveyAssociations extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'aaid';
    const TABLE_NAME = 'surveys_associations';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const REQUIRED_ATTRS = 'sid,attr,id';
    const UNIQUE_ATTRS = 'sid,attr';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $this->errorcode = 1;
                return $this;
            }
            //  $data['createdOn'] = TIME_NOW;
            // $data['createdBy'] = $core->user['uid'];
            $db->insert_query(self::TABLE_NAME, $data);
            return $this;
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $this->errorcode = 1;
                return $this;
            }
            //  $data['modifiedOn'] = TIME_NOW;
            // $data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            return $this;
        }
    }

}