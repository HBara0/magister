<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: representatives_class.php
 * Created:        @tony.assaad    Feb 10, 2014 | 10:49:58 AM
 * Last Update:    @tony.assaad    Feb 10, 2014 | 10:49:58 AM
 */

/**
 * Description of representatives_class
 *
 * @author tony.assaad
 */
class Representatives extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'rpid';
    const TABLE_NAME = 'representatives';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'rpid,name,email,phone,isSupportive';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    public function __construct($id = '', $simple = false) {
//        if(isset($id) && !empty($id)) {
//            $this->representative = $this->read($id, $simple);
//        }
//    }
//    protected function read($id, $simple) {
//        global $db;
//
//        $query_select = '*';
//        if($simple == true) {
//            $query_select = 'rpid, name, email';
//        }
//        if(!empty($id)) {
//            return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
//        }
//    }

    public function get() {
        return $this->data;
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_repposition() {
        $repposition = RepresentativePositions::get_data(array('rpid' => $this->data[self::PRIMARY_KEY]));
        if(is_object($repposition)) {
            return $repposition->get_position();
        }
    }

}
?>
