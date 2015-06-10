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
}