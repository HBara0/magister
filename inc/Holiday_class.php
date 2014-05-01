<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Holiday_class.php
 * Created:        @tony.assaad    Apr 2, 2013 | 10:33:53 AM
 * Last Update:    @tony.assaad    Apr 2, 2013 | 10:33:53 AM
 */

/**
 * Description of Holiday_class
 *
 * @author tony.assaad
 */
class Holiday {
    private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
    private $holidays = array();

    public function __construct($id = '') {
        if(isset($id) && !empty($id)) {
            $this->holiday = $this->read($id);
        }
    }

    public function manage($data = array(), $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;
        if(is_array($data)) {
            print_r($data);

            if($options['operationtype'] == 'editholiday') {
                
            }
        }
    }

    public function add_holidaysexceptions($id) {
        global $db, $log, $core;
    }

    public function get() {
        return $this->holiday;
    }

    public function get_status() {
        return $this->status;
    }

    private function read() {
        global $db, $core;
    }

}
?>
