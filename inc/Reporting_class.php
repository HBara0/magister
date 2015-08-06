<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Reporting Class
 * $id: reporting_class.php
 * Created:			@tony.assaad    March 06, 2013 | 11:44:29 AM
 * Last Update:     @tony.assaad    March 06, 2013 | 11:44:29 AM
 */

class Reporting {
    protected $report;

    public function __construct($reportdata = array()) {
        if(isset($reportdata['rid']) && !empty($reportdata['rid'])) {
            $this->get_report_byid($reportdata['rid']);
        }
        else {
            if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !is_empty($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter'])) {
                $this->get_report_byinfo($reportdata);
            }
            else {
                return false;
            }
        }
    }

    public function get_report_supplier_audits() {
        global $db;
        $suppaudits = SupplierAudits::get_data(array('eid' => $this->report['spid']), array('returnarray' => true));
        if(is_array($suppaudits)) {
            foreach($suppaudits as $suppaudit) {
                $audits[] = new Users($suppaudit->uid);
            }
        }
        return $audits;
//        return $db->fetch_assoc($db->query("SELECT u.uid,displayName AS employeeName, u.email
//			FROM ".Tprefix."users u
//			JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid)
//			WHERE sa.eid=".$this->report['spid'].""));
    }

    /**
     *
     * @global type $core
     * @param type $strict  Whether the user is strictly the report auditor or has other permissions
     * @return boolean
     */
    public function user_isaudit($strict = false) {
        global $core;
        if($strict == false) {
            if($core->usergroup['canAdminCP'] == 1) {
                return true;
            }

            if(!empty($this->affid)) {
                if(value_exists('affiliatedemployees', 'uid', $core->user['uid'], 'canAudit=1 AND affid='.$this->affid)) {
                    return true;
                }
            }
        }

        $audits = $this->get_report_supplier_audits();
        if(is_array($audits)) {
            foreach($audits as $audit) {
                if($audit->uid == $core->user['uid']) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function get_report_byinfo($reportdata = array()) {
        global $db;

        if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !empty($reportdata['year']) && !empty($reportdata['affid']) && !empty($reportdata['spid']) && !empty($reportdata['quarter'])) {
            $this->report = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."reports WHERE year='".$db->escape_string($reportdata['year'])."' AND affid='".$db->escape_string($reportdata['affid'])."' AND quarter='".$db->escape_string($reportdata['quarter'])."' AND spid='".$db->escape_string($reportdata['spid'])."'"));
            if(is_array($this->report) && !empty($this->report)) {
                return true;
            }
            return false;
        }
        return false;
    }

    protected function get_report_byid($rid) {
        global $db;
        if(!empty($rid)) {
            $this->report = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."reports WHERE rid='".$db->escape_string($rid)."'"));
            if(is_array($this->report) && !empty($this->report)) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function get() {
        return $this->report;
    }

    public function __get($name) {
        if(isset($this->report[$name])) {
            return $this->report[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->report[$name]);
    }

    public function get_budget() {
        return Budgets::get_data(array('affid' => $this->report['affid'], 'year' => $this->report['year'], 'spid' => $this->report['spid']), array('simple' => false, 'operators' => array('affid' => 'IN', 'spid' => 'IN', 'year' => 'IN')));
    }

}
?>