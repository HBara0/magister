<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EmailFormatting_class.php
 * Created:        @hussein.barakat    21-Jan-2016 | 16:03:19
 * Last Update:    @hussein.barakat    21-Jan-2016 | 16:03:19
 */

class EmailFormatting {
    private $message = array();
    private $type = '';

    public function __construct($messagedata = '', $type = '') {
        if(is_array($messagedata)) {
            $this->set_message($messagedata, $type);
        }
    }

    private function set_message($messagedata, $type) {
        global $template, $lang, $core;
        switch($type) {
            default :
                eval("\$outputmessage = \"".$template->get('emailfomatting_default')."\";");
                break;
        }
        $this->message = $outputmessage;
    }

    public function get_message() {
        return $this->message;
    }

}