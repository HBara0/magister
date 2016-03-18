<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Files_class.php
 * Created:        @rasha.aboushakra    Mar 17, 2016 | 3:58:15 PM
 * Last Update:    @rasha.aboushakra    Mar 17, 2016 | 3:58:15 PM
 */

/**
 * Description of Files_class
 *
 * @author rasha.aboushakra
 */
class Files extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fid';
    const TABLE_NAME = 'files';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}