<?php
/* -------Definiton-START-------- */

class BasicIngredients extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'biid';
    const TABLE_NAME = 'basic_ingredients';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'biid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $table_array = array(
                    'title' => $data['title'],
                    'description' => $data['description'],
            );
            $table_array['name'] = generate_alias($data['title']);
            $query = $db->insert_query(self::TABLE_NAME, $table_array);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
            }
            return $this;
        }
    }

    protected function update(array $data) {
        global $db;
        if(!$this->validate_requiredfields($data)) {

            if(is_array($data)) {
                $update_array['title'] = $data['title'];
                $update_array['description'] = $data['description'];
            }
            $update_array['name'] = generate_alias($data['title']);

            $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            return $this;
        }
    }

    /* -------FUNCTIONS-END-------- */
    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('title');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}