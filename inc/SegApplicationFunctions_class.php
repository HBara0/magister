<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Segapplicationfunctions.php
 * Created:        @tony.assaad    Dec 3, 2013 | 4:57:25 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 4:57:25 PM
 */

/**
 * Description of Segapplicationfunctions
 *
 * @author tony.assaad
 */
class SegApplicationFunctions {
    private $segapplicationfunction = array();

    public function __construct($id, $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'safid, cfid, psaid';
        }
        $this->segapplicationfunction = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'segapplicationfunctions WHERE safid='.intval($id)));
    }

    public function get_function() {
        return new Chemicalfunctions($this->segapplicationfunction['cfid']);
    }

    public function get_application() {
        return new Segmentapplications($this->segapplicationfunction['psaid']);
    }

    public static function get_segmentsapplicationsfunctions(array $filters = array('filterwhere', 'hasitemperlist'), array $configs = array()) {
        global $db, $core;
        $sort_query = ' ORDER BY psaid ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = ' ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }
        if(!empty($filters['hasitemperlist']) && ($filters['hasitemperlist'] == 1) && isset($filters['hasitemperlist'])) {
            if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
                $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
            }
        }
        $limit_start = 0;
        if(isset($configs['limit'])) {
            $limit_start = intval($configs['limit']);
        }
        $limit_offset = $core->settings['itemsperlist'];
        if(isset($configs['offset'])) {
            $limit_offset = intval($configs['offset']);
        }

        if(!empty($filters['filterwhere']) && isset($filters['filterwhere'])) {
            $filter_where = ' WHERE '.$filters['filterwhere'];
        }

        $query = $db->query('SELECT safid FROM '.Tprefix.'segapplicationfunctions'.$filter_where.$sort_query.' LIMIT '.$limit_start.', '.$limit_offset);
        if($db->num_rows($query) > 0) {
            while($rowsegappfunc = $db->fetch_assoc($query)) {
                $segments_applicationsfunctions[$rowsegappfunc['safid']] = new self($rowsegappfunc['safid']);
            }
            return $segments_applicationsfunctions;
        }
        return false;
    }

    /* get the segment throught the Segmentapplications of this segapplicationfunction Obj */
    public function get_segment() {
        return $this->get_application()->get_segment();
    }

    public function get_createdby() {
        return new Users($this->segapplicationfunction['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->segapplicationfunction['modifiedBy']);
    }

    public function __get($attr) {
        if(isset($this->segapplicationfunction[$attr])) {
            return $this->segapplicationfunction[$attr];
        }
        return false;
    }

    public function get() {
        return $this->segapplicationfunction;
    }

}
?>
