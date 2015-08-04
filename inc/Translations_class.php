<?php
/* -------Definiton-START-------- */

class Translations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tid';
    const TABLE_NAME = 'translations';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'tableName,field,language,tableKey';
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
                'tableName' => $data['tableName'],
                'field' => $data['field'],
                'language' => $data['language'],
                'tableKey' => $data['tableKey'],
                'text' => $data['text'],
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
            $update_array['tableName'] = $data['tableName'];
            $update_array['field'] = $data['field'];
            $update_array['language'] = $data['language'];
            $update_array['tableKey'] = $data['tableKey'];
            $update_array['text'] = $data['text'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_language() {
        return new SystemLanguages($this->language);
    }

    public function get_translation($field, $table, $language, $key) {
        $translations = Translations::get_data(array('language' => $language, 'field' => $field, 'tableKey' => $key, 'tableName' => $table), array('returnarray' => false));
        if(is_object($translations)) {
            return $translations;
        }
        return false;
    }

}