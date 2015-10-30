<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Entbrandsproducts_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 */

/**
 * Description of Entbrandsproducts_class
 *
 * @author tony.assaad
 */
class EntBrandsProducts extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ebpid';
    const TABLE_NAME = 'entitiesbrandsproducts';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'ebpid, ebid, eptid, pcvid,description,classificationClass';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'ebid,eptid,pcvid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public static function get_producttypes_bybrand($id) {
        global $db;

        if(!empty($id)) {
            $query = $db->query('SELECT eptid FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.$db->escape_string($id).'"');
            while($endproduct = $db->fetch_assoc($query)) {
                $endproducts[$endproduct['eptid']] = new EndProducTypes($endproduct['eptid']);
            }
            return $endproducts;
        }
        return false;
    }

    public static function get_entitiesbrandsproducts_bybrand($id) {
        global $db;

        if(!empty($id)) {
            $query = $db->query('SELECT ebpid FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.$db->escape_string($id).'"');
            while($brandproduct = $db->fetch_assoc($query)) {
                $brandproducts[$brandproduct['ebpid']] = new Entbrandsproducts($brandproduct['ebpid']);
            }
            return $brandproducts;
        }
        return false;
    }

    public static function get_entbrandsproducts($filter_where = '') {
        global $db;

        /* Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         */

        $query = $db->query('SELECT * FROM '.Tprefix.'entitiesbrandsproducts '.$filter_where.'');
        while($rows = $db->fetch_assoc($query)) {
            $entbrandsproducts[$rows['ebpid']] = new Entbrandsproducts($rows['ebpid']);
        }
        return $entbrandsproducts;
    }

    public function get_entitybrand() {
        return new EntitiesBrands($this->data['ebid']);
    }

    public function get_endproduct() {
        if($this->data['eptid'] == 0) {
            return;
        }
        return new EndProducTypes($this->data['eptid']);
    }

    public function get_charactersticvalue() {
        return new ProductCharacteristicValues($this->pcvid);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'eptid' => $data['eptid'],
                'ebid' => $data['ebid'],
                'pcvid' => $data['pcvid'],
                'description' => $data['description'],
                'classificationClass' => $data['classificationClass'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $update_array['eptid'] = $data['eptid'];
            $update_array['ebid'] = $data['ebid'];
            $update_array['pcvid'] = $data['pcvid'];
            $update_array['description'] = $data['description'];
            $update_array['classificationClass'] = $data['classificationClass'];
            $update_array['modifiedBy'] = $core->user['uid'];
            $update_array['modifiedOn'] = TIME_NOW;
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        global $core;
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'><img title="Go To Brand End Product Profile" src="'.$core->settings['rootdir'].'/images/twoheadead_arrow.png"></a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/brandprofile&amp;ebpid='.$this->data[self::PRIMARY_KEY];
    }

    public function get_status() {
        global $core, $lang;
        $reviewed = $lang->notreviwed;
        if($this->isReviewed()) {
            $reviewedOn = date($core->settings['dateformat']." ".$core->settings['timeformat'], $this->reviewedOn);
            $reviewedBy_obj = new Users($this->reviewedBy);
            if(is_object($reviewedBy_obj)) {
                $reviewedBy = '<a href="'.$reviewedBy_obj->get_link().'" style="color:#91B64F;">'.$reviewedBy_obj->get_displayname().'</a>';
            }
            $reviewed = $lang->reviewedon." ".$reviewedOn." ".$lang->by." ".$reviewedBy;
        }
        return $reviewed;
    }

    public function isReviewed() {
        if($this->reviewedOn != 0) {
            return true;
        }
        else {
            return false;
        }
    }

}
?>
