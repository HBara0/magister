<?php
/* -------Definiton-START-------- */

class MarketReport extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'mrid';
    const TABLE_NAME = 'marketreport';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
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
                'psid' => $data['psid'],
                'markTrendCompetition' => $data['markTrendCompetition'],
                'quarterlyHighlights' => $data['quarterlyHighlights'],
                'devProjectsNewOp' => $data['devProjectsNewOp'],
                'issues' => $data['issues'],
                'actionPlan' => $data['actionPlan'],
                'remarks' => $data['remarks'],
                'rating' => $data['rating'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['rid'] = $data['rid'];
            $update_array['psid'] = $data['psid'];
            $update_array['markTrendCompetition'] = $data['markTrendCompetition'];
            $update_array['quarterlyHighlights'] = $data['quarterlyHighlights'];
            $update_array['devProjectsNewOp'] = $data['devProjectsNewOp'];
            $update_array['issues'] = $data['issues'];
            $update_array['actionPlan'] = $data['actionPlan'];
            $update_array['remarks'] = $data['remarks'];
            $update_array['rating'] = $data['rating'];
            $update_array['modifiedBy'] = $core->user['uid'];
            $update_array['modifiedOn'] = TIME_NOW;
        }

        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    //
    public function delete() {
        global $db;
        //classes in which we should look for the report id and deleted resulting objects
        $class_names = array(ReportContributors::CLASSNAME);

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

        $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        if($query) {
            return true;
        }
        return false;
    }

}