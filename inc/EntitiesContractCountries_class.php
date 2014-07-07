<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EntitiesContractCountries_class.php
 * Created:        @tony.assaad    Jun 12, 2014 | 2:30:50 PM
 * Last Update:    @tony.assaad    Jun 12, 2014 | 2:30:50 PM
 */

/**
 * Description of EntitiesContractCountries_class
 *
 * @author tony.assaad
 */
class EntitiesContractCountries {
    private $errorcode = 0;
    private $data = null;

    const PRIMARY_KEY = 'eccid';
    const TABLE_NAME = 'entities_contractcountries';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id = '') {
        global $db;

        if(empty($id)) {
            return false;
        }

        $this->data = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function save(array $data = array()) {
        global $core;

//get object of and the id and set data and save
        if(empty($data)) {
            $data = $this->data;
        }
        if(!empty($this->data['eccid'])) {
            $latest_contractedobjs = EntitiesContractCountries::get_contractcountries(self::PRIMARY_KEY.'='.intval($data['eccid']));
        }

        if(is_object($latest_contractedobjs)) {
            $this->update($data);
        }
        else {
            // $unique_values = array('eid', 'coid');
            /* Validate second level if data not exist for the  eid and coid  coming from the core->input */
            $latest_contractedobjs = EntitiesContractCountries::get_contractcountries(array('eid' => $data['eid'], 'coid' => $data['coid']));

            if(!is_object($latest_contractedobjs)) {
                $this->create($data);
            }
            else {
                $latest_contractedobjs->update($data);
            }
        }

        $this->errorode = 0;
    }

    private function update($data) {
        global $db, $core;
        if(!isset($data['isExclusive'])) {
            $data['isExclusive'] = 0;
        }
        if(!isset($data['selectiveProducts'])) {
            $data['selectiveProducts'] = 0;
        }
        $data['modifiedBy'] = $core->user['uid'];
        $data['modifiedOn'] = TIME_NOW;
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($data[self::PRIMARY_KEY]));
    }

    private function create($countrydata) {
        global $db, $core;
        if(is_array($countrydata)) {
            if(is_empty($countrydata['coid'], $countrydata['eid'])) {
                return;
            }
            $country_data = array(
                    'eid' => $countrydata['eid'],
                    'coid' => $countrydata['coid'],
                    'isExclusive' => $countrydata['isExclusive'],
                    'isAgent ' => $countrydata['Agent'],
                    'isDistributor' => $countrydata['Distributor'],
                    'Exclusivity' => $countrydata['Exclusivity'],
                    'selectiveProducts' => $countrydata['selectiveProducts'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );

            $db->insert_query(self::TABLE_NAME, $country_data);
        }
    }

    public function get_country() {
        return new Countries($this->data['coid']);
    }

    public static function get_contractcountries($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_user() {
        return new Users($this->data['createdBy']);
    }

    public function get() {
        return $this->data;
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
        /* return the  object by itlsef */
        return $this;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

}