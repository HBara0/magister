<?php
/* -------Definiton-START-------- */

class CmsNewsHighlights extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'cmsnhid';
    const TABLE_NAME = 'cms_newshighlights';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'cmsnid,cmshid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'cmsnhid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'cmsnid' => $data['cmsnid'],
                'cmshid' => $data['cmshid'],
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
            $update_array['cmsnid'] = $data['cmsnid'];
            $update_array['cmshid'] = $data['cmshid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public function get_CmsHighlights() {
        return new CmsHighlights($this->data['cmshid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
}