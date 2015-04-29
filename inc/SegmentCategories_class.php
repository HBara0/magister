<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SegmentCategories_class.php
 * Created:        @hussein.barakat    Apr 9, 2015 | 11:42:45 AM
 * Last Update:    @hussein.barakat    Apr 9, 2015 | 11:42:45 AM
 */

class SegmentCategories extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'scid';
    const TABLE_NAME = 'segmentscategories';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'scid,title,name';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function save(array $data = array()) {
        parent::save($data);
    }

    public function create(array $data) {
        ;
    }

    public function update(array $data) {

    }

}