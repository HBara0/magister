<?php
/* -------Definiton-START-------- */

class TravelManagerPlanSegmentEntitySegments extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmpsesid';
    const TABLE_NAME = 'travelmanager_plantrip_entitiysegment';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'psid,tmpsafid';
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
                'psid' => intval($data['psid']),
                'tmpsafid' => intval($data['tmpsafid']),
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
            $update_array['psid'] = intval($data['psid']);
            $update_array['tmpsafid'] = intval($data['tmpsafid']);
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_segment() {
        return new ProductsSegments(intval($this->data['psid']));
    }

}