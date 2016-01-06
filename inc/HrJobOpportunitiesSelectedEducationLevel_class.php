<?php
/* -------Definiton-START-------- */

class HrJobOpportunitiesSelectedEducationLevel extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'hrjose';
    const TABLE_NAME = 'hr_jobopportunities_selected_educationlevel';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'joelid,joid,type';
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
                'joid' => $data['joid'],
                'joelid' => $data['joelid'],
                'type' => $data['type'],
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
            $update_array['joid'] = $data['joid'];
            $update_array['joelid'] = $data['joelid'];
            $update_array['type'] = $data['type'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}