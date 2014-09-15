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

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

}