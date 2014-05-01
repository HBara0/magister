<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: RequirementsChanges_class.php
 * Created:        @zaher.reda    Feb 25, 2014 | 1:02:03 PM
 * Last Update:    @zaher.reda    Feb 25, 2014 | 1:02:03 PM
 */

class RequirementsChanges {
    private $reqchange = array();
    private $errorcode = 0;

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->reqchange = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = false) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'drid, title';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."development_requirements_changes WHERE drcid=".$db->escape_string($id)));
    }

    public function create($reqchange) {
        global $core, $db;
        /* To expand checks */
        $required_data = array('drid', 'title', 'description');
        foreach($required_data as $attr) {
            if(empty($reqchange[$attr])) {
                $this->errorcode = 2;
                return false;
            }
        }

        if(value_exists('development_requirements_changes', 'title', $reqchange['title'], 'drid='.intval($reqchange['drid']))) {
            $this->errorcode = 602;
            return false;
        }

        unset($reqchange['action'], $reqchange['module']);
        $requirement_obj = new Requirements($reqchange['drid']);
        $reqchange['refKey'] = $requirement_obj->get_lastchangekey() + 1;

        $reqchange['dateRequested'] = strtotime($reqchange['dateRequested']);
        $reqchange['approvedBy'] = $reqchange['createdBy'] = $core->user['uid'];
        $reqchange['dateCreated'] = TIME_NOW;
        $query = $db->insert_query('development_requirements_changes', $reqchange);
        unset($requirement_obj, $reqchange);
        if($query) {
            $this->errorcode = 0;
            return true;
        }
        $this->errorcode = 601;
        return false;
    }

    public function get_creator() {
        if(empty($this->reqchange['createdBy'])) {
            return false;
        }
        return new Users($this->reqchange['createdBy']);
    }

    public function get_requester() {
        if(empty($this->reqchange['requestedBy'])) {
            return false;
        }
        return new Users($this->reqchange['requestedBy']);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get() {
        return $this->reqchange;
    }

}
?>
