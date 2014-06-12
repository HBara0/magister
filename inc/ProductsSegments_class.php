<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Products Segments Class
 * $id: ProductsSegments.php
 * Created:        @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 * Last Update:    @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 */

class ProductsSegments {
    private $segment = array();

    const PRIMARY_KEY = 'psid';
    const TABLE_NAME = 'productsegments';
    const DISPLAY_NAME = 'title';

    public function __construct($id = '', $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'psid, title';
        }

        $this->segment = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'productsegments WHERE psid='.intval($id)));
    }

    public static function get_segment_byname($value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr('title', $value);
    }

    public static function get_segments($filters = '') {
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

    public function get_applications() {
        global $db;
        $query = $db->query('SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid="'.$this->segment['psid'].'"');
        if($db->num_rows($query) > 0) {
            while($rowsegmentapp = $db->fetch_assoc($query)) {
                $segmentsapp[$rowsegmentapp['psaid']] = new SegmentApplications($rowsegmentapp['psaid']);
            }
            return $segmentsapp;
        }
        else {
            return false;
        }
    }

    public function get_coordinators() {
        global $db;
        $query = $db->query('SELECT pscid FROM '.Tprefix.'productsegmentcoordinators WHERE psid = "'.$this->segment['psid'].'"');
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
        WHERE u.gid!=7 AND psid = '.intval($this->segment['psid']).'
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
        WHERE p.psid = '.intval($this->segment['psid']).''.$query_where);
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

    public function get_customers($filterpermission = '') {
        global $db;
        $query = $db->query('SELECT e.eid FROM '.Tprefix.'entities e
        JOIN '.Tprefix.'entitiessegments es ON (es.eid = e.eid)
        JOIN '.Tprefix.'affiliatedentities a ON (e.eid = a.eid)
        JOIN '.Tprefix.'affiliatedemployees ae ON (a.affid = ae.affid)
        JOIN '.Tprefix.'assignedemployees ase ON (ase.uid = ae.uid)
        JOIN '.Tprefix.'productsegments p ON (p.psid = es.psid) WHERE e.type = "c" '.$filterpermission.' AND p.psid = '.intval($this->segment['psid']).'');
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

    public function __get($attr) {
        if(isset($this->segment[$attr])) {
            return $this->segment[$attr];
        }
        return false;
    }

    public function get_displayname() {
        return $this->segment[self::DISPLAY_NAME];
    }

    public function get() {
        return $this->segment;
    }

}
?>
