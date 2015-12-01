<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Requirements Class
 * $id: Requirements_class.php
 * Created By: 		@zaher.reda			May 21, 2012 | 09:38 PM
 * Last Update: 	@zaher.reda			May 21, 2012 | 09:38 PM
 */

class Requirements extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'drid';
    const TABLE_NAME = 'development_requirements';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'drid, refWord, refKey, title, parent, isCompleted, module';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

//    public function __construct($id = '', $simple = false) {
//        if(isset($id) && !empty($id)) {
//            $this->data = $this->read_requirement($id, $simple);
//        }
//    }

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function read_requirement_full($id = null, $simple = false) {
        global $db, $core;

        if(empty($id)) {
            $id = $this->{self::PRIMARY_KEY};
        }
        $text_fields = array('performance', 'userInterface', 'security', 'description');

        $query_select = 'dr1.*, dr2.title AS parentTitle';
        if($simple == true) {
            $query_select = 'dr1.drid, refWord, dr1.title';
        }

        $query = $db->query("SELECT {$query_select}
		FROM ".Tprefix."development_requirements  dr1
		LEFT JOIN ".Tprefix."development_requirements  dr2 ON (dr1.parent=dr2.drid)
		WHERE dr1.drid=".$db->escape_string($id));

        if($db->num_rows($query) > 0) {
            while($requirement = $db->fetch_assoc($query)) {
                $level = 'parent';
                if($requirement['parent'] == $id) {
                    $level = 'children';
                }
                if($simple == false) {
                    $requirement['dateCreated_output'] = date($core->settings['dateformat'], $requirement['dateCreated']);

                    foreach($text_fields as $field) {
                        //$requirement[$field] = htmlentities($requirement[$field]);
                        fix_newline($requirement[$field]);
                        //parse_ocode($requirement[$field]);
                    }
                }
                $requirements = $requirement;
                $requirements['children'] = $this->read_requirement_children($requirement['drid'], $simple);
            }
        }

        $this->data = $requirements;
    }

    public function read_requirement_children($id, $simple = false) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'drid, refWord, refKey, title';
        }

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."development_requirements WHERE parent=".$db->escape_string($id).' ORDER BY refWord ASC, refKey ASC');
        if($db->num_rows($query) > 0) {
            while($requirement = $db->fetch_assoc($query)) {
                $requirements[$requirement['drid']] = $requirement;
                $requirements[$requirement['drid']]['children'] = $this->read_requirement_children($requirement['drid'], $simple);
            }
            return $requirements;
        }

        return false;
    }

    public function get_changes($id = '') {
        global $db, $core;

        if(empty($id)) {
            $id = $this->data['drid'];
        }

        $query = $db->query("SELECT drc.*, u.displayName AS createdByName, dr.title AS outcomeReqTitle, dr.refKey AS drRefKey, dr.refWord AS drRefWord
							FROM ".Tprefix."development_requirements_changes  drc
							LEFT JOIN ".Tprefix."development_requirements dr ON (drc.outcomeReq=dr.drid)
							JOIN ".Tprefix."users u ON (drc.createdBy=u.uid)
							WHERE drc.drid=".$db->escape_string($id)."
							ORDER BY dateCreated ASC");
        if($db->num_rows($query) > 0) {
            while($change = $db->fetch_assoc($query)) {
                $change['dateCreated_output'] = date($core->settings['dateformat'], $change['dateCreated']);
                $changes[$change['drcid']] = $change;
            }
        }

        return $changes;
    }

    public function read_user_requirements($simple = false) {
        global $db, $core;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'drid, refWord, refKey, title';
        }

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."development_requirements WHERE (assignedTo=0 OR assignedTo=".$core->user['uid'].' OR createdBy='.$core->user['uid'].') AND parent=0 ORDER BY refWord ASC, refKey ASC');
        if($db->num_rows($query)) {
            while($requirement = $db->fetch_assoc($query)) {
                $level = 'parent';
                if($requirement['parent'] != 0) {
                    $level = 'children';
                }

                $requirements[$requirement['drid']] = $requirement;
                $requirements[$requirement['drid']]['children'] = $this->read_requirement_children($requirement['drid'], $simple);
            }

            return $requirements;
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

    public function get_parent() {
        if(!isset($this->data['parent'])) {
            return false;
        }
        return new Requirements($this->data['parent']);
    }

    public function get_lastchangekey() {
        global $db;

        return $db->fetch_field($db->query('SELECT refKey FROM '.Tprefix.'development_requirements_changes WHERE drid='.intval($this->data['drid']).' ORDER BY refKey DESC LIMIT 0, 1'), 'refKey');
    }

    public function update(array $data = array()) {
        global $core, $db;

        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];

        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            return $this;
        }
        $this->errorcode = 601;
        return false;
    }

//    public function save(array $data = array()) {
//        if(empty($data)) {
//            $data = $this->requirement;
//        }
//
//        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->requirement[self::PRIMARY_KEY])) {
//            return $this->update($data);
//        }
//        else {
//            return $this->create($data);
//        }
//    }

    public function parse_requirements_list(array $requirements = array(), $highlevel = true, $ref = '', $parsetype = 'list', $config = array()) {
        if(empty($requirements)) {
            if(!isset($this->data)) {
                return false;
            }

            if($highlevel == true) {
                $requirements = $this->data;
            }
            else {
                return false;
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $requirements_list = '<ul>';
            }
            else {
                $requirements_list = '<select name="'.$config['name'].'" id="'.$config['id'].'">';
            }
        }

        $ref_param = $ref;

        foreach($requirements as $id => $values) {
            if(empty($ref)) {
                $ref = $values['refWord'].' '.$values['refKey'];
            }
            else {
                $ref = $ref_param.'.'.$values['refKey'];
            }
            if($parsetype == 'list') {
                $requirements_list .= '<li><a href="index.php?module=development/viewrequirement&id='.$values['drid'].'" target="_blank">'.$ref.' '.$values['title'].'</a>';

                if(!empty($values['isCompleted']) && !is_array($values['children'])) {
                    $requirements_list .= ' &#10004;';
                }
                elseif(!empty($values['isCompleted']) && is_array($values['children'])) {
                    $requirements_list .= ' &#10003;';
                }

                if(is_array($values['children']) && !empty($values['children'])) {
                    $requirements_list .= ' <a href="#requirement_'.$values['drid'].'" id="showmore_requirementchildren_'.$values['drid'].'">&raquo;</a>';
                }

                $requirements_list .= '</li>';
            }
            else {
                $requirements_list .= '<option value="'.$values['drid'].'">'.$ref.' '.$values['title'].'</option>';
            }

            if(is_array($values['children']) && !empty($values['children'])) {
                if($parsetype == 'list') {
                    $requirements_list .= '<ul id="requirementchildren_'.$values['drid'].'" style="display:none;">';
                    $requirements_list .= $this->parse_requirements_list($values['children'], false, $ref);
                    $requirements_list .= '</ul>';
                }
                else {
                    $requirements_list .= $this->parse_requirements_list($values['children'], false, $ref, 'select');
                }
            }

            if($highlevel == true) {
                $ref = '';
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $requirements_list .= '</ul>';
            }
            else {
                $requirements_list .= '</select>';
            }
        }


        return $requirements_list;
    }

    public function parse_fullreferencekey() {
        $reference = $this->refKey;

        if(!empty($this->parent)) {
            $reference = $this->get_parent()->parse_fullreferencekey().'.'.$reference;
        }
        return $reference;
    }

    public function hasChildren() {
        $requirements = Requirements::get_data(array('parent' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($requirements)) {
            return true;
        }
        return false;
    }

    public function get_topoftree() {
        if(!empty($this->parent)) {
            $parent = $this->get_parent()->get_topoftree();
            return $parent;
        }
        return $this;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get_link() {
        return 'index.php?module=development/viewrequirement&id='.$this->data[self::PRIMARY_KEY];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->data[self::DISPLAY_NAME].'</a>';
    }

}
?>