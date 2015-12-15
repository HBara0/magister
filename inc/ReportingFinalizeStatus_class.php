<?php
/* -------Definiton-START-------- */

class ReportingFinalizeStatus extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'rfsid';
    const TABLE_NAME = 'reporting_finalizestatuses';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'rfsid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'rid' => $data['rid'],
                'newStatus' => $data['newStatus'],
                'timeModified' => TIME_NOW,
                'actionType' => $data['actionType'],
                'modifiedBy' => $core->user['uid'],
        );
        if($table_array['actionType'] == 'finalize' && $table_array['newStatus'] == 1) {
            $report = new ReportingQReports(intval($table_array['rid']));
            if(is_object($report)) {
                $update_status = $db->update_query('reports', array('timesFinalized' => (($report->timesFinalized) + 1)), "rid='{$table_array[rid]}'");
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {

    }

    /* -------FUNCTIONS-END-------- */
}