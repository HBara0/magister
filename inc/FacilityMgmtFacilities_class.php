<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFacilities_class.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:53:03 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:53:03 AM
 */

/**
 * Description of FacilityMgmtFacilities_class
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFacilities extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmfid';
    const TABLE_NAME = 'facilitymgmt_facilities';
    const DISPLAY_NAME = 'name';
    const UNIQUE_ATTRS = 'affid,name,parent';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            if(is_array($data['dimensions'])) {
                $data['dimensions'] = implode("x", $data['dimensions']);
            }
            $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(is_array($data['dimensions'])) {
                $data['dimensions'] = implode("x", $data['dimensions']);
            }
            $data['modifiedOn'] = TIME_NOW;
            $data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
        return $this;
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public static function get_facilities_tree() {
        global $db, $core;
        $sort_query = ' ORDER BY name ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $db->escape_string(' ORDER BY '.$core->input['sortby'].' '.$core->input['order']);
        }


        $query = $db->query("SELECT fmfid FROM ".Tprefix."facilitymgmt_facilities WHERE parent=0 {$sort_query}");
        if($db->num_rows($query) > 0) {
            while($facility = $db->fetch_assoc($query)) {

                $level = 'parent';
                if($menu['parent'] != 0) {
                    $level = 'children';
                }
                $facility_obj = new FacilityMgmtFacilities($facility['fmfid']);
                $facilities[$facility['fmfid']] = $facility_obj->get();
                //$facilitys[$facility['fmfid']] = $facilitys_obj[$facility['fmfid']]->get();
                $facilities[$facility['fmfid']]['children'] = FacilityMgmtFacilities::read_facility_children($facility['fmfid'], $simple);
            }
            return $facilities;
        }
        return false;
    }

    public function parse_facility_list(array $facilities = array(), $highlevel = true, $ref = '', $parsetype = 'list', $config = array()) {
        global $core;
        if(empty($facilities)) {
            if(!isset($this->data)) {
                return false;
            }

            if($highlevel == true) {
                $facilities = $this->data;
            }
            else {
                return false;
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $facilities_list = '<ul>';
            }
            else {
                $facilities_list = '<select name="'.$config['name'].'" id="'.$config['id'].'">';
            }
        }


        foreach($facilities as $id => $values) {
            if($parsetype == 'list') {
                $facility = new FacilityMgmtFacilities($values['fmfid']);
                if($values['parent'] == 0) {
                    $facility_obj = new FacilityMgmtFacilities($values['fmfid']);
                    $values['affiliate'] = $facility_obj->get_affiliate()->get_displayname();
                    if(!empty($values['affiliate'])) {
                        $values['affiliate'] = ' - '.$values['affiliate'];
                    }
                }
                //   }
                //<div style = "width:20%; display:inline-block; text-align: left;">'.$values['name'].'</div>'

                if($values['parent'] == 0) {
                    $facilities_list.='<br/>';
                }
                $editlink = '<div style="float:right"><a target="_blank" href="'.$core->settings['rootdir'].'/index.php?module=facilitymgmt/managefacility&id='.$values['fmfid'].'"  title="Edit"><img src="'.$core->settings['rootdir'].'/images/edit.gif" border="0"/></a></div>';
                $delete_link = "<div style='float:right'><a href='#{$values['fmfid']}' id='deletefacility_{$values['fmfid']}_facilitymgmt/list_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletefacility}' /></a></div>";

                $facilities_list .= '<li>'.$values['name'].$values['affiliate'];
                unset($values['affiliate']);
                if(is_array($values['children']) && !empty($values['children'])) {
                    $facilities_list .= '<a href="#facility_'.$values['fmfid'].'" id="showmore_facilitychildren_'.$values['fmfid'].'">&raquo;</a>';
                }

                $facilities_list .=$delete_link.$editlink.' </li>';
            }
            else {
                $facilities_list .= '<option value="'.$values['fmfid'].'">'.$ref.' '.$values['name'].'</option>';
            }

            if(is_array($values['children']) && !empty($values['children'])) {
                //    if(!empty($values['affiliate'])) {
                //       $config['excludeaffiliate'] = true;
                //    }
                if($parsetype == 'list') {
                    $facilities_list .= '<ul id="facilitychildren_'.$values['fmfid'].'" style="display:none;">';
                    $facilities_list .= $this->parse_facility_list($values['children'], false, $ref);
                    unset($values['children']['affiliate']);
                    $facilities_list .= '</ul>';
                }
                else {
                    $facilities_list .= $this->parse_facility_list($values['children'], false, $ref, 'select');
                }
            }

            if($highlevel == true) {
                $ref = '';
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $facilities_list .= '</ul>';
            }
            else {
                $facilities_list .= '</select>';
            }
        }


        return $facilities_list;
    }

    public function read_facility_children($id, $simple = false) {
        global $db;

        $query_select = 'fmfid';

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."facilitymgmt_facilities WHERE parent=".$db->escape_string($id).' ORDER BY name ASC');
        if($db->num_rows($query) > 0) {
            while($facility = $db->fetch_assoc($query)) {
                $facility_obj = new FacilityMgmtFacilities($facility['fmfid']);
                $facilities[$facility['fmfid']] = $facility_obj->get();
                $facilities[$facility['fmfid']]['children'] = FacilityMgmtFacilities::read_facility_children($facility['fmfid'], $simple);
            }
            return $facilities;
        }

        return false;
    }

    public function delete_facility($todelete) {
        global $db;
        $attributes = static::PRIMARY_KEY;
        $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="'.static::TABLE_NAME.'"');
        if(is_array($tables)) {
            foreach($tables as $table) {
                $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$todelete." ");
                if($db->num_rows($query) > 0) {
                    $this->errorcode = 3;
                    return false;
                }
            }
        }
        $facilities = FacilityMgmtFacilities::get_data(array('parent' => $todelete), array('returnarray' => true));
        if(is_array($facilities)) {
            foreach($facilities as $facility) {
                $facilitytodelete = $facility->delete_facility($facility->fmfid);
                if(!$facilitytodelete) {
                    $this->errorcode = 3;
                    return false;
                }
            }
        }
        if($this->delete()) {
            $this->errorcode = 0;
            return true;
        }
    }

    public function delete() {
        global $db;
        if(empty($this->data[static::PRIMARY_KEY]) && empty($this->data['inputChecksum'])) {
            return false;
        }
        elseif(empty($this->data[static::PRIMARY_KEY]) && !empty($this->data['inputChecksum'])) {
            $query = $db->delete_query(static::TABLE_NAME, 'inputChecksum="'.$db->escape_string($this->data['inputChecksum']).'"');
        }
        else {
            $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        }
        if($query) {
            return true;
        }
        return false;
    }

    public function get_parent() {
        $parents_objs = FacilityMgmtFacilities::get_data(array(static::PRIMARY_KEY => $this->data['parent']));
        if(is_object($parents_objs)) {
            return $parents_objs;
        }
        return false;
    }

    public function get_mother() {
        $immediate_parent = $this->get_parent();
        if($immediate_parent == false) {
            return $this;
        }
        return $immediate_parent->get_mother();
    }

    public function is_reserved($from, $to) {
        if(is_empty($from, $to)) {
            return false;
        }
        $reservations = FacilityMgmtReservations::get_data(' fmfid = '.$this->data['fmfid'].' AND ((fromDate  BETWEEN '.$from.' AND '.$to.') OR (toDate BETWEEN '.$from.' AND '.$to.') OR (fromDate <= '.$from.' AND toDate >= '.$to.')) ');
        if(is_object($reservations) && !empty($reservations->fmrid)) {
            return $reservations;
        }
        return false;
    }

    public function get_type() {
        return new FacilityMgmtFactypes($this->data['type']);
    }

    public function getfulladdress() {
        $address = $this->get_displayname();
        $affiliate = new Affiliates($this->affid);

        if(!is_empty($this->parent)) {
            $motherfaciloty = $this->get_mother();
            if(is_object($motherfaciloty) && !is_empty($motherfaciloty->fmfid)) {
                $address.=' - '.$motherfaciloty->get_displayname();
            }
        }
        return $address;
    }

}