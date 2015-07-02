<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: CmsContentCategories_class.php
 * Created:        @zaher.reda    Jul 31, 2014 | 11:33:53 PM
 * Last Update:    @zaher.reda    Jul 31, 2014 | 11:33:53 PM
 */

/**
 * Description of CmsContentCategories_class
 *
 * @author zaher.reda
 */
class CmsContentCategories extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'cmsccid';
    const TABLE_NAME = 'cms_contentcategories';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'cmsccid, name, title';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'name,title';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;
        if(empty($data['name']) || !isset($data['name']) || empty($data['title']) || !isset($data['title'])) {
            $this->errorcode = 1;
            return;
        }
        $table_array = array(
                'name' => $data['tableName'],
                'title' => $data['className'],
        );
        $table_array['isEnabled'] = 0;
        if($data['isEnabled'] == 1) {
            $table_array['isEnabled'] = 1;
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            if(empty($data['name']) || !isset($data['name']) || empty($data['title']) || !isset($data['title'])) {
                $this->errorcode = 1;
                return $this;
            }
            $table_array['name'] = $data['name'];
            $table_array['title'] = $data['title'];
            $table_array['isEnabled'] = 0;
            if($data['isEnabled'] == 1) {
                $table_array['isEnabled'] = 1;
            }
        }

        $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        $this->errorcode = 3;
        return $this;
    }

}