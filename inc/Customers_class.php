<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Customers_class.php
 * Created:        @zaher.reda    Jun 11, 2014 | 11:21:38 AM
 * Last Update:    @zaher.reda    Jun 11, 2014 | 11:21:38 AM
 */

/**
 * Description of Customers_class
 *
 * @author zaher.reda
 */
class Customers extends Entities {
    //put your code here

    const PRIMARY_KEY = 'cid';
    const TABLE_NAME = 'entities';
    const DISPLAY_NAME = 'companyName';

    public function __construct($id, $action = '', $simple = true) {
        $this->data = $this->read($id, $simple);
        $this->data[self::PRIMARY_KEY] = $this->data[parent::PRIMARY_KEY];
    }

}