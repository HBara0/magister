<?php
/* -------Definiton-START-------- */

class SystemLanguages extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'slid';
    const TABLE_NAME = 'system_languages';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'fileName' => $data['fileName'],
                'name' => $data['name'],
                'version' => $data['version'],
                'rtl' => $data['rtl'],
                'htmllang' => $data['htmllang'],
                'charset' => $data['charset'],
                'author' => $data['author'],
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
            $update_array['fileName'] = $data['fileName'];
            $update_array['name'] = $data['name'];
            $update_array['version'] = $data['version'];
            $update_array['rtl'] = $data['rtl'];
            $update_array['htmllang'] = $data['htmllang'];
            $update_array['charset'] = $data['charset'];
            $update_array['author'] = $data['author'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}