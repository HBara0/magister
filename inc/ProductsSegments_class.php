<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Products Segments Class
 * $id: ProductsSegments.php
 * Created:        @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 * Last Update:    @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 */

class ProductsSegments extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'psid';
    const TABLE_NAME = 'productsegments';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'psid,alias,title,titleAbbr';
    const UNIQUE_ATTRS = 'alias';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;

        $db->insert_query(self::TABLE_NAME, $data);
        return $this;
    }

    protected function update(array $data) {
        global $db;

        if(!isset($data['publishOnWebsite'])) {
            $data['publishOnWebsite'] = 0;
        }
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public static function get_segments($filters = null, $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function get_segment_byname($value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr('title', $value);
    }

    public static function get_segments_legacy($filters = '') {
        global $db;

        /* Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         */
        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT psid  FROM '.Tprefix.'productsegments'.$filters);
        if($db->num_rows($query) > 0) {
            while($rowsegment = $db->fetch_assoc($query)) {
                $segments[$rowsegment['psid']] = new self($rowsegment['psid']);
            }
            return $segments;
        }
        else {
            return false;
        }
    }

    public function get_applications($simple = true) {
        global $db;
        $query = $db->query('SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid="'.$this->data['psid'].'"');
        if($db->num_rows($query) > 0) {
            while($rowsegmentapp = $db->fetch_assoc($query)) {
                $segmentsapp[$rowsegmentapp['psaid']] = new SegmentApplications($rowsegmentapp['psaid'], $simple);
            }
            return $segmentsapp;
        }
        else {
            return false;
        }
    }

    public function get_coordinators() {
        global $db;
        $query = $db->query('SELECT pscid FROM '.Tprefix.'productsegmentcoordinators WHERE psid = "'.$this->data['psid'].'"');
        if($db->num_rows($query) > 0) {
            while($segmentcoordinator = $db->fetch_assoc($query)) {
                $segmentcoordinators[$segmentcoordinator['pscid']] = new ProdSegCoordinators($segmentcoordinator['pscid']);
            }
            return $segmentcoordinators;
        }
        else {
            return false;
        }
    }

    public function get_assignedemployees() {
        global $db;
        $query = $db->query('SELECT es.uid, es.emsid
        FROM '.Tprefix.'employeessegments es
        JOIN '.Tprefix.'users u ON (u.uid = es.uid)
        WHERE u.gid!=7 AND psid = '.intval($this->data['psid']).'
        ORDER BY displayName ASC');
        if($db->num_rows($query) > 0) {
            while($rowsegmentemployees = $db->fetch_assoc($query)) {
                $segmentsemployees[$rowsegmentemployees['emsid']] = new users($rowsegmentemployees['uid']);
            }
            return $segmentsemployees;
        }
        else {
            return false;
        }
    }

    public function get_suppliers() {
        return $this->get_entities('s');
    }

    public function get_entities($type = '') {
        global $db;

        if(!empty($type)) {
            $query_where = ' AND e.type = "'.$db->escape_string($type).'"';
        }
        $query = $db->query('SELECT e.eid
        FROM '.Tprefix.'entities e
        JOIN '.Tprefix.'entitiessegments es ON (es.eid = e.eid)
        JOIN '.Tprefix.'productsegments p ON (p.psid = es.psid)
        WHERE p.psid = '.intval($this->data['psid']).''.$query_where);
        if($db->num_rows($query) > 0) {
            while($entity = $db->fetch_assoc($query)) {
                $segmentsemployees[$entity['eid']] = new Entities($entity['eid']);
            }
            return $segmentsemployees;
        }
        else {
            return false;
        }
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/segmentprofile&amp;id='.$this->data[self::PRIMARY_KEY];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_customers($filterpermission = '') {
        global $db;
        $query = $db->query('SELECT e.eid FROM '.Tprefix.'entities e
        JOIN '.Tprefix.'entitiessegments es ON (es.eid = e.eid)
        JOIN '.Tprefix.'affiliatedentities a ON (e.eid = a.eid)
        JOIN '.Tprefix.'affiliatedemployees ae ON (a.affid = ae.affid)
        JOIN '.Tprefix.'assignedemployees ase ON (ase.uid = ae.uid)
        JOIN '.Tprefix.'productsegments p ON (p.psid = es.psid) WHERE e.type = "c" '.$filterpermission.' AND p.psid = '.intval($this->data['psid']).'');
        if($db->num_rows($query) > 0) {
            while($rowsegmentcustomers = $db->fetch_assoc($query)) {
                $segmentscustomers[$rowsegmentcustomers['eid']] = new Entities($rowsegmentcustomers['eid']);
            }
            return $segmentscustomers;
        }
        else {
            return false;
        }
    }

    public function get_segmentcategory() {
        return new SegmentCategories($this->data['category']);
    }

    public function get_segment_integrationOBId() {
        return $this->integrationOBId;
    }

}
?>
