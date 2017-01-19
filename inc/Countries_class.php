<?php

class Countries extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'coid';
    const TABLE_NAME = 'countries';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name,acronym';
    const CLASSNAME = __CLASS__;

    /**
     *
     * @param Int $id ID of the object
     * @param Boolean $simple Where to read all columns or only basics
     */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    /**
     *
     * @return \Currencies Country main currency
     */
    public function get_maincurrency() {
        if (empty($this->data['mainCurrency'])) {
            return null;
        }
        return new Currencies($this->data['mainCurrency']);
    }

    public static function get_coveredcountries() {
        return self::get_countries('affid !=0');
    }

    /**
     *
     * @param String|Array $filters Filters to apply.
     * @param array $configs Configuration that can be passed to DAL.
     * @return Array    Array of countries.
     */
    public static function get_countries($filters = '', array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    /**
     *
     * @return \Cities|boolean Capital city of the country
     */
    public function get_capitalcity() {
        if (!is_empty($this->data['capitalCity'])) {
            return new Cities($this->data['capitalCity']);
        }
        return false;
    }

    /**
     *
     * @return \Affiliates
     */
    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    /**
     * Get a country object by country name
     *
     * @global type $db DB  Connection resource
     * @param String $name  Name of the country to acquire its object
     * @return \Countries|boolean   Object of country
     */
    public static function get_country_byname($name) {
        global $db;

        if (!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT coid FROM ' . Tprefix . 'countries WHERE name="' . $db->escape_string($name) . '"'), 'coid');
            if (!empty($id)) {
                return new Countries($id);
            }
        }
        return false;
    }

//    public function save(array $data = array()) {
//        global $db;
//        /* Add validations */
//        $db->update_query('countries', $this->data, 'coid='.intval($this->data['coid']));
//    }

    protected function create(array $data) {
        global $db;

        $db->insert_query(self::TABLE_NAME, $data);
    }

    protected function update(array $data) {
        global $db;

        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
    }

    public function get_phonecodes() {
        $countries = self::get_countries('name IS NOT NUll', array('order' => array('by' => Countries::DISPLAY_NAME, 'sort' => 'ASC')));
        if (is_array($countries)) {
            foreach ($countries as $country) {
                $phonecodes[$country->phoneCode] = $country->get_displayname() . ' (+' . $country->phoneCode . ')';
            }
        }
        return $phonecodes;
    }

    public function get_approvedhotels() {
        return TravelManagerHotels::get_data(array('country' => $this->data['coid'], 'isApproved' => 1), array('returnarray' => true));
    }

    public function get_livedata() {
        $url = 'https://restcountries.eu/rest/v1/all';
        $response = file_get_contents($url);
        return $response;
    }

}

?>
