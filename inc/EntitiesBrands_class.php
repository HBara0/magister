<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Entitiesbrands_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 */

/**
 * Description of Entitiesbrands_class
 *
 * @author tony.assaad
 */
class EntitiesBrands extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ebid';
    const TABLE_NAME = 'entitiesbrands';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = '*';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $this->data = $data;
            if(empty($this->data['name']) || empty($this->data['eid'])) {
                $this->errorcode = 1;
                return false;
            }
            if(value_exists('entitiesbrands', 'name', $this->data['name'], 'eid='.$core->input['entitybrand']['eid'])) {
                $this->errorcode = 2;
                return false;
            }

            $brand = EntitiesBrands::get_data(array('name' => $this->data['name'], 'eid' => $this->data['eid']));

            if(!is_object($brand)) {
                $enttitbrand_data = array(
                        'name' => $this->data['name'],
                        'eid' => $this->data['eid'],
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW
                );
                if($this->data['isGeneral'] == 1) {
                    $enttitbrand_data['isGeneral'] = 1;
                }
                $query = $db->insert_query(self::TABLE_NAME, $enttitbrand_data);
            }
            else {
                $query = true;
            }

            if($this->data['isGeneral'] == 1) {
                unset($this->data['endproducttypes']);
                $this->data['endproducttypes'][] = 0;
            }
            if($query) {
                if(is_object($brand)) {
                    $this->ebid = $brand->ebid;
                }
                else {
                    $this->ebid = $db->last_id();
                }
                if(is_array($this->data['endproducttypes'])) {
                    foreach($this->data['endproducttypes'] as $key => $endproduct) {
                        if(!empty($endproduct['eptid'])) {
                            $eptid = $endproduct['eptid'];
                            if(value_exists('entitiesbrandsproducts', 'eptid', $eptid, 'pcvid='.intval($data['pcvid']).' AND ebid='.$this->ebid)) {
                                continue;
                            }
                            $entitiesbrandsproducts_data = array(
                                    'ebid' => $this->ebid,
                                    'eptid' => $eptid,
                                    'pcvid' => $this->data['pcvid'],
                                    'description' => $endproduct['description'],
                                    'classificationClass' => $endproduct['classificationClass'],
                                    'createdBy' => $core->user['uid'],
                                    'createdOn' => TIME_NOW
                            );
                            $entitybrand_obj = new EntBrandsProducts();
                            $entitybrand_obj->set($entitiesbrandsproducts_data);
                            $entitybrand_obj->save();
                        }
                    }
                }
                $this->errorcode = 0;
                return true;
            }
        }
    }

    public function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $update_array['name'] = $data['name'];
            $update_array['eid'] = $data['eid'];
            $update_array['modifiedBy'] = $core->user['uid'];
            $update_array['modifiedOn'] = TIME_NOW;
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));

        if(is_array($data['endproducttypes'])) {
            foreach($data['endproducttypes'] as $eptid) {
                if(value_exists('entitiesbrandsproducts', 'eptid', $eptid, 'pcvid='.intval($data['pcvid']).' AND ebid='.intval($this->data[self::PRIMARY_KEY]))) {
                    continue;
                }
                $entitiesbrandsproducts_data = array(
                        'ebid' => $this->data[self::PRIMARY_KEY],
                        'eptid' => $eptid,
                        'pcvid' => $data['pcvid'],
                        'description' => $this->data['description'],
                        'classificationClass' => $this->data['classificationClass'],
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW
                );
                $entitybrand_obj = new EntBrandsProducts();
                $entitybrand_obj->set($entitiesbrandsproducts_data);
                $entitybrand_obj->save();
            }
        }
        return $this;
    }

    public function get_entity($simple = true) {
        return new Entities($this->data['eid'], null, $simple);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

    public static function get_data($filters = '', $configs = array()) {
        $data = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_entitybrands_byattr($attr, $value) {
        $data = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_entitybrands_byeid($id) {
        global $db;

        if(!empty($id)) {
            $query = $db->query('SELECT ebid FROM '.Tprefix.'entitiesbrands WHERE eid="'.$db->escape_string($id).'"');
            while($brand = $db->fetch_assoc($query)) {
                $brands[$brand['ebid']] = new EntitiesBrands($brand['ebid']);
            }
            return $brands;
        }
        return false;
    }

    public static function get_entitybrands() {
        global $db, $core;

        $sort_query = ' ORDER BY name ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = ' ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }
        $query = $db->query('SELECT ebid FROM '.Tprefix.'entitiesbrands'.$sort_query.' LIMIT '.$limit_start.', '.$core->settings['itemsperlist']);
        if($db->num_rows($query) > 0) {
            while($brand = $db->fetch_assoc($query)) {
                $brands[$brand['ebid']] = new EntitiesBrands($brand['ebid']);
            }
            return $brands;
        }
        else {
            return false;
        }
    }

    public function get_entbrandproducts() {
        global $db;

        $query = $db->query('SELECT ebpid  FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.intval($this->data['ebid']).'"');
        while($entbrandproduct = $db->fetch_assoc($query)) {
            $entbrandproducts[$entbrandproduct['ebpid']] = new EntBrandsProducts($entbrandproduct['ebpid']);
        }
        return $entbrandproducts;
    }

    public function get_producttypes() {
        global $db;

        $query = $db->query('SELECT eptid FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.intval($this->data['ebid']).'"');
        while($endproduct = $db->fetch_assoc($query)) {
            $endproducts[$endproduct['eptid']] = new EndProducTypes($endproduct['eptid']);
        }
        return $endproducts;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/brandprofile&amp;ebid='.$this->data[self::PRIMARY_KEY];
    }

}
?>
