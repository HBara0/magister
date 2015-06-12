<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketReportCompetition_class.php
 * Created:        @rasha.aboushakra    Apr 9, 2015 | 10:09:13 AM
 * Last Update:    @rasha.aboushakra    Apr 9, 2015 | 10:09:13 AM
 */

class MarketReportCompetition extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'mrcid';
    const TABLE_NAME = 'marketreport_competition';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mrid,sid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data = array()) {
        global $db;
        if(empty($data)) {
            $data = $this->data;
        }
        if(!isset($data['sid']) && !isset($data['coid'])) {
            return;
        }
        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        return $this;
    }

    public function update(array $data = array()) {
        global $db;
        if(empty($data)) {
            $data = $this->data;
        }
        if(!isset($data['sid']) && !isset($data['coid'])) {
            $mrcompetitionproducts = MarketReportCompetitionProducts::get_data(array('mrcid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
            if(is_array($mrcompetitionproducts)) {
                foreach($mrcompetitionproducts as $mrcompetitionproduct) {
                    $mrcompetitionproduct->delete();
                }
            }
            $this->delete();
            return;
        }
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}