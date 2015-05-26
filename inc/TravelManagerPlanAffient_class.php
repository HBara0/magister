<?php
/* -------Definiton-START-------- */

class TravelManagerPlanAffient extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmpsafid';
    const TABLE_NAME = 'travelmanager_plantrip_affient';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'tmpsid,type,primaryId';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'tmpsid' => $data['tmpsid'],
                'type' => $data['type'],
                'inputChecksum' => $data['inputChecksum'],
                'primaryId' => $data['primaryId'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['tmpsid'] = $data['tmpsid'];
            $update_array['type'] = $data['type'];
            $update_array['inputChecksum'] = $data['inputChecksum'];
            $update_array['primaryId'] = $data['primaryId'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_type() {
        return $this->data['type'];
    }

    public function get_entity() {
        if($this->get_type() == 'affiliate') {
            $result = new Affiliates($this->data['primaryId']);
        }
        elseif($this->get_type() == 'entity') {
            $result = new Entities($this->data['primaryId']);
        }
        else {
            return false;
        }
        return $result;
    }

}