<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Meeting_association.php
 * Created:        @tony.assaad    Nov 19, 2013 | 12:03:27 PM
 * Last Update:    @tony.assaad    Nov 19, 2013 | 12:03:27 PM
 */

/**
 * Description of Meeting_association
 *
 * @author tony.assaad
 */
class MeetingsAssociations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'mtaid';
    const TABLE_NAME = 'meetings_associations';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mtid,id,idAttr';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    private function read($id, $simple = false) {
//        global $db;
//        return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meetings_associations WHERE mtaid=".$db->escape_string($id)));
//    }

    public static function set_association($association = array()) {
        global $db;
        if(is_array($association)) {
            $db->insert_query('meetings_associations', $association);
        }
    }

    public function get_meeting() {
        return new Meetings($this->association['mtid']);
    }

    public function get() {
        return $this->data;
    }

    protected function update(array $data) {

    }

}
?>
