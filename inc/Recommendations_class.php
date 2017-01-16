<?php

/**
 * Description of Recommendations
 *
 * @author H.B
 */
class Recommendations extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'rid';
    const TABLE_NAME = 'recommendations';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'title,city,category';
    const UNIQUE_ATTRS = 'alias,city,category';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);
        if (!$data['inputChecksum']) {
            $data['inputChecksum'] = generate_checksum();
        }
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);
        if (is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    /**
     *
     * @return \Users|boolean
     */
    public function get_createdBy() {
        return new Users(intval($this->data['createdBy']));
    }

    /**
     *
     * @global type $lang
     * @return type
     */
    public function parse_categories() {
        global $lang;
        $categories_list = array('food' => $lang->food, 'accomodation' => $lang->accomodation, 'entertainment' => $lang->entertainment, 'monument' => $lang->monument);
        return parse_selectlist2('recommendation[category]', 1, $categories_list, $this->data['category']);
    }

    /**
     *
     * @return type
     */
    public function parse_rating() {
        $counter = 1;
        $ratings = array();
        while ($counter < 6) {
            $ratings[$counter] = $counter;
            $counter++;
        }
        return parse_selectlist2('recommendation[rating]', 1, $ratings, $this->data['rating']);
    }

    public function get_city() {
        if (!intval($this->data['city'])) {
            return false;
        }
        return new Cities(intval($this->data['city']));
    }

    public function get_cityoutput() {
        $city_obj = $this->get_city();
        if (!is_object($city_obj)) {
            return;
        }
        return $city_obj->get_displayname();
    }

}
