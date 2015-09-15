<?php
/* -------Definiton-START-------- */

class Translations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tid';
    const TABLE_NAME = 'system_translations';
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
        if(!$this->validate_requiredfields($data)) {
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
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(!$this->validate_requiredfields($data)) {
            if(is_array($data)) {
                $update_array['tableName'] = $data['tableName'];
                $update_array['field'] = $data['field'];
                $update_array['language'] = $data['language'];
                $update_array['tableKey'] = $data['tableKey'];
                $update_array['text'] = $data['text'];
            }
            $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
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

    public function save(array $data = array()) {
        global $log;
        if(empty($data)) {
            $data = $this->data;
        }

        $log->record($this->data[static::PRIMARY_KEY], $data['inputChecksum']);

        if(isset($this->data[static::PRIMARY_KEY]) && !empty($this->data[static::PRIMARY_KEY])) {
            return $this->update($data);
        }
        if(empty($data[static::PRIMARY_KEY])) {
            unset($data[static::PRIMARY_KEY]);
        }

        if(isset($data['inputChecksum']) && !empty($data['inputChecksum'])) {
            $object = self::get_data(array('inputChecksum' => $data['inputChecksum']));
            if(is_object($object)) {
                return $object->update($data);
            }
        }
        $language = new SystemLanguages($data['language']);
        if(is_object($language) && !is_empty($language->htmllang)) {
            $data['language'] = $language->htmllang;
        }
        $unique_attrs = explode(',', static::UNIQUE_ATTRS);
        if(is_array($unique_attrs)) {
            foreach($unique_attrs as $attr) {
                $attr = trim($attr);
                if(empty($data[$attr])) {
                    $checks = null;
                    break;
                }
                $checks[$attr] = $data[$attr];
            }
        }
        if(is_array($checks)) {
            $object = self::get_data($checks);
            if(is_object($object)) {
                return $object->update($data);
            }

            if(is_array($object)) {
                foreach($object as $obj) {
                    $obj->delete();
                }
            }
        }
        return $this->create($data);
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('tableName', 'field', 'language', 'tableKey', 'text');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}