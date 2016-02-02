<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EntitiesSegments_class.php
 * Created:        @zaher.reda    Aug 21, 2014 | 12:17:34 PM
 * Last Update:    @zaher.reda    Aug 21, 2014 | 12:17:34 PM
 */

/**
 * Description of EntitiesSegments_class
 *
 * @author zaher.reda
 */
class EntitiesSegments extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'esid';
    const TABLE_NAME = 'entitiessegments';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;

        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            return true;
        }
        return false;
    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {
        if(isset($this->data[self::PRIMARY_KEY]) && !empty($this->data[self::PRIMARY_KEY])) {
            $this->update($data);
        }
        else {
            $this->create($data);
        }
    }

    public function get_entity() {
        return new Entities($this->data[Entities::PRIMARY_KEY]);
    }

    public function get_segment() {
        return new ProductsSegments($this->data[Entities::PRIMARY_KEY]);
    }

}