<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketReportCompetitionProducts_class.php
 * Created:        @rasha.aboushakra    Apr 10, 2015 | 11:04:03 AM
 * Last Update:    @rasha.aboushakra    Apr 10, 2015 | 11:04:03 AM
 */

class MarketReportCompetitionProducts extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'mrcpid';
    const TABLE_NAME = 'marketreport_competition_products';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mrcid,pid,csid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data = array()) {
        global $db, $errorhandler;
        if(empty($data)) {
            $data = $this->data;
        }
        $marketreport_competition = MarketReportCompetition::get_data(array('mrcid' => $data['mrcid']));
        if(is_object($marketreport_competition)) {
            $supplier = Entities::get_data(array('eid' => $marketreport_competition->sid, 'type' => 'cs'));
        }
        if((empty($data['pid']) && empty($data['csid'])) || empty($data['mrcid'])) {
            if(is_object($supplier)) {
                $errorhandler->record('requiredfields: ', 'chemcial Substance for supplier '.$supplier->get_displayname());
            }
            return;
        }
        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        return $this;
    }

    public function update(array $data = array()) {
        global $db, $errorhandler;
        if(empty($data)) {
            $data = $this->data;
        }
        $marketreport_competition = MarketReportCompetition::get_data(array('mrcid' => $data['mrcid']));
        if(is_object($marketreport_competition)) {
            $supplier = Entities::get_data(array('eid' => $marketreport_competition->sid, 'type' => 'cs'));
        }
        if((empty($data['pid']) && empty($data['csid'])) || empty($data['mrcid'])) {
            if(is_object($supplier)) {
                $errorhandler->record('requiredfields: ', 'chemcial Substance for supplier '.$supplier->get_displayname());
            }
            return;
        }
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}