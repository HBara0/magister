<?php
/* -------Definiton-START-------- */

class ProductCharacteristics extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'pcid';
    const TABLE_NAME = 'productcharacteristics';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name,title';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'title' => $data['title'],
        );
        $table_array['name'] = generate_alias($data['title']);
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['title'] = $data['title'];
        }
        $update_array['name'] = generate_alias($update_array['title']);
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}