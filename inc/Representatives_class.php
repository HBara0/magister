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
    const UNIQUE_ATTRS = null;
    const REQUIRED_ATTRS = 'name';

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
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 3;
            return false;
        }
        $fields = array('name', 'email', 'phone', 'isSupportive', 'isActive');
        foreach($fields as $field) {
            $table_array[$field] = $data[$field];
        }

        if(empty($table_array['createdBy'])) {
            $table_array['createdBy'] = $core->user['uid'];
        }
        if(empty($table_array['createdOn'])) {
            $table_array['createdOn'] = TIME_NOW;
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data = $table_array;
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 3;
            return false;
        }
        $fields = array('rpid', 'name', 'email', 'phone', 'isSupportive', 'isActive');
        foreach($fields as $field) {
            $update_array[$field] = $data[$field];
        }

        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
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

    public function get_entities() {

        $entids = EntitiesRepresentatives::get_column('eid', array('rpid' => $this->data['rpid']), array('returnarray' => true));
        if(is_array($entids)) {
            $entities = Entities::get_data(array('eid' => $entids), array('returnarray' => true));
            return $entities;
        }
        return false;
    }

    public function get_entities_names() {

        $entids = EntitiesRepresentatives::get_column('eid', array('rpid' => $this->data['rpid']), array('returnarray' => true));
        if(is_array($entids)) {
            $entities = Entities::get_column('companyName', array('eid' => $entids), array('returnarray' => true));
            return $entities;
        }
        return false;
    }

    public function delete_representative($todelete = '') {
        global $db;
        if(empty($todelete)) {
            $todelete = $this->data['rpid'];
        }
        $attributes = array(static::PRIMARY_KEY);
        foreach($attributes as $attribute) {
            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="'.static::TABLE_NAME.'"');
            if(is_array($tables)) {
                foreach($tables as $table) {
                    switch($table) {
                        case EntitiesRepresentatives::TABLE_NAME:
                        case RepresentativePositions::TABLE_NAME:
                        case RepresentativesSegments::TABLE_NAME:
                            conitnue;
                        default:
                            $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$todelete." ");
                            if($db->num_rows($query) > 0) {
                                $this->errorcode = 3;
                                return false;
                            }
                            break;
                    }
                }
            }
        }
        $entreps = EntitiesRepresentatives::get_data(array('rpid' => $todelete), array('returnarray' => true));
        if(is_array($entreps)) {
            foreach($entreps as $rep) {
                $rep = $rep->delete();
                if(!$rep) {
                    return $code;
                }
            }
        }
        $segreps = RepresentativesSegments::get_data(array('rpid' => $todelete), array('returnarray' => true));
        if(is_array($segreps)) {
            foreach($segreps as $rep) {
                $rep = $rep->delete();
                if(!$rep) {
                    return $code;
                }
            }
        }
        $posreps = RepresentativePositions::get_data(array('rpid' => $todelete), array('returnarray' => true));
        if(is_array($posreps)) {
            foreach($posreps as $rep) {
                $rep = $rep->delete();
                if(!$rep) {
                    return $code;
                }
            }
        }
        $delete = $this->delete();
        if($delete) {
            $this->errorcode = 0;
            return true;
        }
    }

    public function get_contactinfo() {
        if(!empty($this->data['email'])) {
            return $this->data['email'];
        }
        else if(!empty($this->data['phone'])) {
            return $this->data['phone'];
        }
        else {
            return false;
        }
    }

}
?>
