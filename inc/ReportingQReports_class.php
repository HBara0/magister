<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ReportingQReports_class.php
 * Created:        @zaher.reda    May 27, 2015 | 6:03:37 PM
 * Last Update:    @zaher.reda    May 27, 2015 | 6:03:37 PM
 */

/**
 * Description of ReportingQReports_class
 *
 * @author zaher.reda
 */
class ReportingQReports extends AbstractClass {
    const PRIMARY_KEY = 'rid';
    const TABLE_NAME = 'reports';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    protected function update(array $data) {

    }

    /**
     *
     * @return \Affiliates
     */
    public function get_affiliate() {
        return new Affiliates($this->affid);
    }

    /**
     *
     * @global type $db
     * @param type $todelete
     * @return boolean true if success, false otherwise
     */
    public function delete_qr($data) {
        global $db, $log;
        if($this->isSent == 1) {
            return false;
        }
        //array of tables that we should stop deleting process if records exist
        $criticaltables = array(ReportingQrRecipientViews::TABLE_NAME, ReportingQrRecipient::TABLE_NAME);

        $attributes = array(self::PRIMARY_KEY);
        foreach($attributes as $attribute) {
            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME IN ("'.implode('" , "', $criticaltables).'")');
            if(is_array($tables)) {
                foreach($tables as $table) {
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$todelete." ");
                    if($db->num_rows($query) > 0) {
                        $this->errorcode = 3;
                        return false;
                    }
                }
            }
        }
        //classes in which we should look for the report id and deleted resulting objects
        $class_names = array(KeyCustomers::CLASSNAME, ProductsActivity::CLASSNAME, MarketReport::CLASSNAME, ReportContributors::CLASSNAME);

        //go through each class and delete records having this rid
        foreach($class_names as $class) {
            $existingrecords_objs = $class::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
            if(is_array($existingrecords_objs)) {
                foreach($existingrecords_objs as $existingrecords_obj) {
                    $delete_res = $existingrecords_obj->delete();
                    if(!$delete_res) {
                        return false;
                    }
                }
            }
        }

        $delete = $this->delete();
        if(!$delete) {
            return false;
        }
        //report identifier in this case will have : year,quarter, affid, spid, requester and reference
        $log->record('deleteQR', array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY], 'affid' => $this->data['affid'], 'spid' => $this->data['spid'], 'year' => $this->data['year'], 'q' => $this->data['q'], 'reference' => $data['reference'], 'requester' => $data['uid']));
        return true;
    }

}