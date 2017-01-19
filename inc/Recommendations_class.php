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
        $categories_objs = RecommendationsCategories::get_data(array('isActive' => 1), array('returnarray' => 1));
        if (!is_array($categories_objs)) {
            return false;
        }
        foreach ($categories_objs as $categories_obj) {
            $categories_list[$categories_obj->get_id()] = $categories_obj->get_displayname();
        }
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
        return parse_selectlist2('recommendation[rating]', 1, $ratings, $this->data['rating'], '', '', array('blankstart' => true));
    }

    /**
     *
     * @return boolean|\Cities
     */
    public function get_city() {
        if (!intval($this->data['city'])) {
            return false;
        }
        return new Cities(intval($this->data['city']));
    }

    /**
     * Get name of city
     * @return type
     */
    public function get_cityoutput() {
        $city_obj = $this->get_city();
        if (!is_object($city_obj)) {
            return;
        }
        return $city_obj->get_displayname();
    }

    /**
     *
     * @global type $core
     * @return boolean
     */
    public function canManageRecommendation() {
        global $core;
        if ($core->usergroup['canManageAllRecommendations']) {
            return true;
        }
        if ($core->user['uid'] == $this->data['createdBy']) {
            return true;
        }
        return false;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {

        if (is_array($attributes_param)) {
            foreach ($attributes_param as $attr => $val) {
                $attributes .= $attr . '="' . $val . '"';
            }
        }
        return '<a href="' . $this->get_link() . '" ' . $attributes . '>' . $this->get_displayname() . '</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=travel/recommendationprofile&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=travel/managerecommendation&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    /**
     *
     * @global type $lang
     * @return type
     */
    public function get_categoryutput() {
        global $lang;
        if (!$this->data['category']) {
            return;
        }
        $category_obj = new RecommendationsCategories(intval($this->data['category']));
        return $category_obj->get_displayname();
    }

    /**
     *
     * @return string
     */
    public function get_ratingoutput() {
        if (!intval($this->data['rating'])) {
            return 'N/A';
        }
        $counter = 1;
        $ratingoutput = '';
        $startouput = '<span style="color:green" class="glyphicon glyphicon-star"></span>';
        while ($counter <= intval($this->data['rating'])) {
            $ratingoutput.=$startouput;
            $counter++;
        }
        return $ratingoutput;
    }

    /**
     *
     * @return type
     */
    public function get_compositeDisplayname() {
        return $this->get_displayname() . ' - ' . $this->get_cityoutput() . ' - ' . $this->get_categoryutput();
    }

}
