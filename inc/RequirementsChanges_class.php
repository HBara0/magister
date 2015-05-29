<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: RequirementsChanges_class.php
 * Created:        @zaher.reda    Feb 25, 2014 | 1:02:03 PM
 * Last Update:    @zaher.reda    Feb 25, 2014 | 1:02:03 PM
 */

class RequirementsChanges extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'drcid';
    const TABLE_NAME = 'development_requirements_changes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'drid, title, refKey, drid, isCompleted';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create($reqchange) {
        global $core, $db;
        /* To expand checks */
        $required_data = array('drid', 'title', 'description');
        foreach($required_data as $attr) {
            if(empty($reqchange[$attr])) {
                $this->errorcode = 2;
                return false;
            }
        }

        if(value_exists('development_requirements_changes', 'title', $reqchange['title'], 'drid='.intval($reqchange['drid']))) {
            $this->errorcode = 602;
            return false;
        }

        unset($reqchange['action'], $reqchange['module']);
        $requirement_obj = new Requirements($reqchange['drid']);
        $reqchange['refKey'] = $requirement_obj->get_lastchangekey() + 1;

        $reqchange['dateRequested'] = strtotime($reqchange['dateRequested']);
        $reqchange['approvedBy'] = $reqchange['createdBy'] = $core->user['uid'];
        $reqchange['dateCreated'] = TIME_NOW;
        $query = $db->insert_query('development_requirements_changes', $reqchange);
        unset($requirement_obj, $reqchange);
        if($query) {
            $this->errorcode = 0;
            return true;
        }
        $this->errorcode = 601;
        return false;
    }

    /**
     *
     * @return \Requirements
     */
    public function get_requirement() {
        return new Requirements($this->drid);
    }

    /**
     *
     * @return \Requirements The requirement which was created due to this change
     */
    public function get_outcomeRequirement() {
        return new Requirements($this->outcomeReq);
    }

    public function get_creator() {
        if(empty($this->data['createdBy'])) {
            return false;
        }
        return new Users($this->data['createdBy']);
    }

    public function get_requester() {
        if(empty($this->data['requestedBy'])) {
            return false;
        }
        return new Users($this->data['requestedBy']);
    }

    protected function update(array $data) {

    }

    public function get_link() {
        return $this->get_requirement()->get_link();
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->data[self::DISPLAY_NAME].'</a>';
    }

}
?>
