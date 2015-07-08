<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Segmentapplications.php
 * Created:        @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 */

/**
 * Description of Segmentapplications
 *
 * @author tony.assaad
 */
class SegmentApplications extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'psaid';
    const TABLE_NAME = 'segmentapplications';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'psaid, name, psid, title,sequence,description';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'name,psid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data = array()) {
        global $db, $core, $log;

        if(is_array($data)) {
            if(empty($data['title'])) {
                $this->errorcode = 1;
                return false;
            }

            if(value_exists(self::TABLE_NAME, 'title', $data['title'], 'psid='.$data['psid'])) {
                $this->errorcode = 2;
                return false;
            }

            if(empty($data['name']) && !isset($data['name'])) {
                $data['name'] = strtolower($data['title']);
                $data['name'] = preg_replace('/\s+/', '', $data['name']);
            }

            $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true, 'method' => 'striponly'));
            $segapplication_data = array(
                    'psid' => $data['psid'],
                    'title' => $data['title'],
                    'name' => $data['name'],
                    'sequence' => $data['sequence'],
                    'publishOnWebsite' => $data['publishOnWebsite'],
                    'description' => $core->sanitize_inputs($data['description'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true)),
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            $query = $db->insert_query(self::TABLE_NAME, $segapplication_data);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                if(!empty($data['segappfunctions']) && isset($data['segappfunctions'])) {
                    foreach($data['segappfunctions'] as $cfid) {
                        if(empty($cfid) || $cfid == 0) {
                            continue;
                        }
                        $segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $cfid, 'psaid' => $this->data[self::PRIMARY_KEY], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
                        if($segappfuncquery) {
                            $data['safid'] = $db->last_id();
                        }
                    }
                }

                $log->record('createsegappfunctions', $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
                return true;
            }
        }
    }

    public static function get_segmentsapplications_legacy() {
        global $db, $core;

        /* Need to put filter
         * Need to put filter
         * Need to put filter
         * Need to put filter
         */

        $sort_query = ' ORDER BY  title  ASC';
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

        $query = $db->query("SELECT psaid FROM ".Tprefix.self::TABLE_NAME."{$sort_query} LIMIT {$limit_start}, {$core->settings['itemsperlist']}");
        if($db->num_rows($query) > 0) {
            while($rowsegapp = $db->fetch_assoc($query)) {
                $segments_applications[$rowsegapp['psaid']] = new self($rowsegapp['psaid']);
            }
            return $segments_applications;
        }
        else {
            return false;
        }
    }

    public static function get_segmentsapplications($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segappfunctions() {
        global $db;
        $query = $db->query('SELECT cfid, safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid="'.intval($this->data['psaid']).'"');
        if($db->num_rows($query) > 0) {
            while($rowsegmentappfunc = $db->fetch_assoc($query)) {
                $segmentsappfunc[$rowsegmentappfunc['safid']] = new ChemicalFunctions($rowsegmentappfunc['cfid']);
            }
            return $segmentsappfunc;
        }
        else {
            return false;
        }
    }

    public function get_segappfunctionsobjs() {
        global $db;
        $query = $db->query('SELECT cfid, safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid="'.intval($this->data['psaid']).'"');
        if($db->num_rows($query) > 0) {
            while($rowsegmentappfunc = $db->fetch_assoc($query)) {
                $segmentsappfunc[$rowsegmentappfunc['safid']] = new SegApplicationFunctions($rowsegmentappfunc['safid']);
            }
            return $segmentsappfunc;
        }
        else {
            return false;
        }
    }

    public function get_endproduct() {
        global $db;

        $query = $db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE psaid="'.$this->data['psaid'].'"');
        if($db->num_rows($query) > 0) {
            while($endproduct = $db->fetch_assoc($query)) {
                $endproducts[$endproduct['eptid']] = new EndProducTypes($endproduct['eptid']);
            }
            return $endproducts;
        }
        else {
            return false;
        }
    }

    public function get_segment() {
        return new ProductsSegments($this->data['psid']);
    }

    public static function get_application_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

//    public function save(array $data = array()) {
//        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->data[self::PRIMARY_KEY])) {
//            //Update
//        }
//        else {
//            if(empty($data)) {
//                $data = $this->data;
//            }
//            $this->create($data);
//        }
//    }

    public function get_errorcode() {
        if(is_object($this)) {
            return $this->errorcode;
        }
        else {
            return $errorcode;
        }
    }

    protected function update(array $data) {
        global $db, $core;

        $valid_fields = array('title', 'psid', 'description', 'publishOnWebsite', 'sequence');
        foreach($valid_fields as $attr) {
            $valid_data[$attr] = $data[$attr];
        }

        $valid_data['description'] = $core->sanitize_inputs($valid_data['description'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        $valid_data['name'] = generate_alias($valid_data['title']);
        $valid_data['modifiedBy'] = $core->user['uid'];
        $valid_data['modifiedOn'] = TIME_NOW;
        $db->update_query(self::TABLE_NAME, $valid_data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/applicationprofile&amp;id='.$this->data[self::PRIMARY_KEY];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

}
?>
