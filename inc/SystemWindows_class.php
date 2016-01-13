<?php

class SystemWindows extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swid';
    const TABLE_NAME = 'system_windows';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name,type';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* Definition-Start */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'name' => $data['name'],
                'title' => $data['title'],
                'type' => $data['type'],
                'description' => $data['description'],
                'comment' => $data['comment'],
                'isActive' => $data['isActive'],
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
            $update_array['name'] = $data['name'];
            $update_array['title'] = $data['title'];
            $update_array['type'] = $data['type'];
            $update_array['description'] = $data['description'];
            $update_array['comment'] = $data['comment'];
            $update_array['isActive'] = $data['isActive'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

////////Definition End
    /* Getter functions-Start */}