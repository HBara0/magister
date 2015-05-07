<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Endproducttpes_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 */

/**
 * Description of Endproducttpes_class
 *
 * @author tony.assaad
 */
class EndProducTypes extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'eptid';
    const TABLE_NAME = 'endproducttypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'eptid, name, title, psaid, parent';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function update(array $data) {
        global $db, $core, $log;
        if(value_exists('endproducttypes', 'title', $data['title'], self::PRIMARY_KEY.'!='.intval($this->data[self::PRIMARY_KEY]))) {
            $this->errorcode = 2;
            return false;
        }

        $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
        if(empty($data['name'])) {
            $data['name'] = generate_alias($data['title']);
        }
        $endproducttypes_data = array(
                'name' => $data['name'],
                'title' => $data['title'],
                'psaid' => $data['segapplications'],
                'parent' => $data['parent'],
                'modifiedBy' => $core->user['uid'],
                'modifiedOn' => TIME_NOW
        );

        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function create(array $data) {
        global $db, $core, $log;
        if(empty($data['title'])) {
            $this->errorcode = 1;
            return false;
        }
        if(value_exists('endproducttypes', 'title', $data['title'])) {
            $this->errorcode = 2;
            return false;
        }

        $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
        if(empty($data['name'])) {
            $data['name'] = generate_alias($data['title']);
        }
        if(isset($data['parent']) && !empty($data['parent'])) {
            $endproducttype_parent = new EndProducTypes($data['parent']);
            if(is_object($endproducttype_parent)) {
                $data['segapplications'] = $endproducttype_parent->psaid;
            }
        }
        $endproducttypes_data = array(
                'name' => $data['name'],
                'title' => $data['title'],
                'psaid' => $data['segapplications'],
                'parent' => $data['parent'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW
        );
        $query = $db->insert_query('endproducttypes', $endproducttypes_data);
        return $this;
    }

    public function get_application() {
        return new SegmentApplications($this->data['psaid']);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

    public static function get_producttype_byname($name) {
        global $db;
        if(!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE name="'.$db->escape_string($name).'"'), 'eptid');
            if(!empty($id)) {
                return new EndProducTypes($id);
            }
        }
        return false;
    }

    public static function get_endproductypes() {
        global $db, $core;
        $sort_query = ' ORDER BY title ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $db->escape_string(' ORDER BY '.$core->input['sortby'].' '.$core->input['order']);
        }

//        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
//            $core->settings['itemsperlist'] = intval($core->input['perpage']);
//        }
//
//        $limit_start = 0;
//        if(isset($core->input['start'])) {
//            $limit_start = intval($core->input['start']);
//        }
        $query = $db->query("SELECT eptid FROM ".Tprefix."endproducttypes{$sort_query}"); // LIMIT {$limit_start}, {$core->settings['itemsperlist']}
        if($db->num_rows($query) > 0) {
            while($producttype = $db->fetch_assoc($query)) {
                $producttypes[$producttype['eptid']] = new EndProducTypes($producttype['eptid']);
            }
            return $producttypes;
        }
        return false;
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/endproducttypeprofile&amp;eptid='.$this->data[self::PRIMARY_KEY];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_primarykey() {
        return $this->data[self::PRIMARY_KEY];
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get_endproducttype_chain($value = '') {
        $parent = new EndProducTypes($this->data['parent']);
        $titlechain = $parent->title.$value;

        if($parent->parent != 0) {
            $titlechain = ' > '.$titlechain;
            $titlechain = $parent->get_endproducttype_chain($titlechain);
        }
        return $titlechain;
    }

    public function parse_endproducttype_list(array $endproducttypes = array(), $highlevel = true, $ref = '', $parsetype = 'list', $config = array()) {
        if(empty($endproducttypes)) {
            if(!isset($this->endproducttype)) {
                return false;
            }

            if($highlevel == true) {
                $endproducttypes = $this->endproducttype;
            }
            else {
                return false;
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $endproducttypes_list = '<ul>';
            }
            else {
                $endproducttypes_list = '<select name="'.$config['name'].'" id="'.$config['id'].'">';
            }
        }

        //$ref_param = $ref;
        // if(!isset($config['excludeapplication'])) {
        //  $config['excludeapplication'] = false;
        //  }
        foreach($endproducttypes as $id => $values) {
            if($parsetype == 'list') {
                //   if($exclude['application'] == false) {
                if($values['parent'] == 0) {
                    $endprod_obj = new EndProducTypes($values['eptid']);
                    $values['application'] = $endprod_obj->get_application()->get()['title'];
                    if(!empty($values['application'])) {
                        $values['application'] = ' - '.$values['application'];
                    }
                }
                //   }
                //<div style = "width:20%; display:inline-block; text-align: left;">'.$values['name'].'</div>'

                if($values['parent'] == 0) {
                    $endproducttypes_list.='<br/>';
                }
                $endproducttypes_list .= '<li><a href="#">'.$values['title'].$values['application'].' </a>';
                unset($values['application']);
                if(is_array($values['children']) && !empty($values['children'])) {
                    $endproducttypes_list .= '<a href="#endproducttype_'.$values['eptid'].'" id="showmore_endprofucttypechildren_'.$values['eptid'].'">&raquo;</a>';
                }


                $endproducttypes_list .= '</li>';
            }
            else {
                $endproducttypes_list .= '<option value="'.$values['eptid'].'">'.$ref.' '.$values['title'].'</option>';
            }

            if(is_array($values['children']) && !empty($values['children'])) {
                //    if(!empty($values['application'])) {
                //       $config['excludeapplication'] = true;
                //    }
                if($parsetype == 'list') {
                    $endproducttypes_list .= '<ul id="endprofucttypechildren_'.$values['eptid'].'" style="display:none;">';
                    $endproducttypes_list .= $this->parse_endproducttype_list($values['children'], false, $ref);
                    unset($values['children']['application']);
                    $endproducttypes_list .= '</ul>';
                }
                else {
                    $endproducttypes_list .= $this->parse_endproducttype_list($values['children'], false, $ref, 'select');
                }
            }

            if($highlevel == true) {
                $ref = '';
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $endproducttypes_list .= '</ul>';
            }
            else {
                $endproducttypes_list .= '</select>';
            }
        }


        return $endproducttypes_list;
    }

    public static function get_endproductypes_tree() {
        global $db, $core;
        $sort_query = ' ORDER BY title ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $db->escape_string(' ORDER BY '.$core->input['sortby'].' '.$core->input['order']);
        }


        $query = $db->query("SELECT eptid FROM ".Tprefix."endproducttypes WHERE parent=0 {$sort_query}");
        if($db->num_rows($query) > 0) {
            while($producttype = $db->fetch_assoc($query)) {

                $level = 'parent';
                if($menu['parent'] != 0) {
                    $level = 'children';
                }
                $endproductobj = new EndProducTypes($producttype['eptid']);
                $producttypes[$producttype['eptid']] = $endproductobj->get();
                //$producttypes[$producttype['eptid']] = $producttypes_obj[$producttype['eptid']]->get();
                $producttypes[$producttype['eptid']]['children'] = EndProducTypes::read_endproducttype_children($producttype['eptid'], $simple);
            }
            return $producttypes;
        }
        return false;
    }

    public function read_endproducttype_children($id, $simple = false) {
        global $db;

        $query_select = 'eptid';

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."endproducttypes WHERE parent=".$db->escape_string($id).' ORDER BY title ASC');
        if($db->num_rows($query) > 0) {
            while($producttype = $db->fetch_assoc($query)) {
                $producttype_obj = new EndProducTypes($producttype['eptid']);
                $producttypes[$producttype['eptid']] = $producttype_obj->get();
                $producttypes[$producttype['eptid']]['children'] = EndProducTypes::read_endproducttype_children($producttype['eptid'], $simple);
            }
            return $producttypes;
        }

        return false;
    }

}
?>
