<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: gadgets_test.php
 * Created:        @hussein.barakat    04-Mar-2016 | 15:14:05
 * Last Update:    @hussein.barakat    04-Mar-2016 | 15:14:05
 */

class gadgets_test extends SystemGadget {
    protected $data = array();

    const WIDGET_ID = '1';

    public function __construct() {

    }

    public function create(array $data) {

    }

    public function update(array $data) {

    }

    public function feedobject() {

    }

    public function parse() {
        return '<div style="width:25%;height:30%;border: #008200 solid thick"><h1>Hola muchachos</h1><hr><div style="background-color:grey">Gadget Uno</div><br><div>some data</div></div>';
    }

}