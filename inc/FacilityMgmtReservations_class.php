<?php
/* -------Definiton-START-------- */

class FacilityMgmtReservations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'fmrid';
    const TABLE_NAME = 'facilitymgmt_reservations';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'mtid';
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
                'fmfid' => $data['fmfid'],
                'fromDate' => $data['fromDate'],
                'toDate' => $data['toDate'],
                'reservedBy' => $data['reservedBy'],
                'purpose' => $data['purpose'],
                'mtid' => $data['mtid'],
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
            $update_array['fmfid'] = $data['fmfid'];
            $update_array['fromDate'] = $data['fromDate'];
            $update_array['toDate'] = $data['toDate'];
            $update_array['reservedBy'] = $data['reservedBy'];
            $update_array['purpose'] = $data['purpose'];
            $update_array['mtid'] = $data['mtid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public function get_reservedBy() {
        return new Users($this->data['reservedBy']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
    public function get_meeting() {
        return new Users($this->data['mtid']);
    }

}