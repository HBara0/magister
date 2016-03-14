<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsFundsEngaged_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:38:08 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:38:08 PM
 */

class AroRequestsFundsEngaged extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arfeid';
    const TABLE_NAME = 'aro_requests_fundsengaged';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'aorid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            return $this;
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            return $this;
        }
    }

    /**
     *
     * @global type $db
     * @param type $upto start date of the required period
     * @param type $affid
     * @param type $options array defining extra join tables whether aro customers tables or partes info table 4
     *                      + extra filters for customer or vendor ids
     * @return type  funds array at that specified period
     */
    public function get_fundsamount($upto, $affid, $options = array()) {
        global $db;

        $query = $db->query('SELECT * FROM aro_requests_fundsengaged f JOIN aro_requests r ON (f.aorid=r.aorid) '.$options['join'].' WHERE affid='.$affid.' AND r.createdOn < '.$upto.' '.$options['filter'].' order by r.createdOn desc limit 0,1');
        if($db->num_rows($query) > 0) {
            while($funds = $db->fetch_assoc($query)) {
                return $funds;
            }
        }
    }

}