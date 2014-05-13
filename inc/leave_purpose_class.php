<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: leave_purpose.php
 * Created:        @tony.assaad    May 13, 2014 | 1:19:31 PM
 * Last Update:    @tony.assaad    May 13, 2014 | 1:19:31 PM
 */

/**
 * Description of leave_purpose
 *
 * @author tony.assaad
 */
class leave_purpose {
    private $purpose = array();

    const primarykey = 'ltpid';
    const table = 'leavetypes_purposes';

    public function __construct($id = 0, $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->purpose = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;
        if(empty($id)) {
            return false;
        }
        $query_select = '*';
        if($simple == true) {
            $query_select = self::primarykey.', name, purpose';
        }
        return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::table.' WHERE '.self::primarykey.'='.$db->escape_string($id)));
    }

    public function get() {
        return $this->purpose;
    }

    public function get_createdby() {
        return new Users($this->purpose['createdBy']);
    }

}