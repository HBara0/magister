<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketIntelligence_class.php
 * Created:        @tony.assaad    Dec 27, 2013 | 3:24:47 PM
 * Last Update:    @tony.assaad    Dec 27, 2013 | 3:24:47 PM
 */

/**
 * Description of MarketIntelligence_class
 *
 * @author tony.assaad
 */
class MarketIntelligence {
    const TABLE_NAME = 'marketintelligence_basicdata';
    const PRIMARY_KEY = 'mibdid';

    private $marketintelligence = array();
    private $customer = null;
    private $brand = null;
    private $endproducttype = null;
    private $miprofiles = array('latestcustomersumbyproduct' => array('groupby' => array('cfpid', 'cfcid', 'mibdid', 'biid'), 'aggregateby' => array('cfpid', 'cfcid', 'biid'), 'displayItem' => ChemFunctionProducts, 'timelevel' => 'latest'), //Main entity profile
            'allprevious' => array('groupby' => array('createdOn', 'eptid', 'mibdid'), 'aggregateby' => array('mibdid'), 'timelevel' => 'allprevious'), //N Level
            'latestaggregatecustomersumbyproduct' => array('groupby' => array('cfpid', 'cfcid', 'biid', 'mibdid'), 'aggregateby' => array('cid', 'cfpid', 'cfcid', 'biid'), 'displayItem' => ChemFunctionProducts, 'timelevel' => 'latest'),
            'latestaggregatebycustomer' => array('groupby' => array('cid', 'eptid'), 'aggregateby' => array('cid', 'cfpid', 'cfcid', 'biid'), 'displayItem' => Customers, 'timelevel' => 'latest'),
            'latestaggregatebyaffiliate' => array('groupby' => array('affid', 'mibdid'), 'aggregateby' => array('affid', 'cfpid', 'cfcid', 'biid'), 'displayItem' => Affiliates, 'timelevel' => 'latest'), //Main affililate profile
            'latestvisitreportdate' => array('groupby' => array('vrid', 'mibdid'), 'aggregateby' => array('vrid', 'cfpid', 'cfcid', 'biid'), 'displayItem' => Customers, 'timelevel' => 'maxvisitreportdate') //Max visit report date
    );

    public function __construct($id = '', $simple = false) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple = false) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'mibdid, cid';
        }
        $this->marketintelligence = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'marketintelligence_basicdata WHERE mibdid='.intval($id)));
    }

    public static function get_marketintelligence_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public function get_affiliate() {
        return new Affiliates($this->affid);
    }

    public function get_endproducttype() {
        return new EndProducTypes($this->eptid);
    }

    public function create($data = array()) {
        global $db, $core, $log;
        if(is_array($data)) {
            $this->marketdata = $data;

            if(empty($this->marketdata['cfpid']) && empty($this->marketdata['cfcid']) && empty($this->marketdata['biid'])) {
                $this->errorcode = 1;
                return false;
            }
            $options = array('cfcid', 'cfpid', 'biid');
            foreach($options as $option) {
                if(is_array($this->marketdata[$option])) {
                    if(array_filter($this->marketdata[$option])) {
                        $values[$option] = $this->marketdata[$option];
                    }
                    else {
                        unset($this->marketdata[$option]);
                    }
                }
            }
            if(!is_array($values)) {
                $this->errorcode = 2;
                return false;
            }
            foreach($values as $option => $valuesarray) {
                $valuesarray = array_filter($valuesarray);
                foreach($valuesarray as $value) {
                    $this->marketdata[$option] = $value;
                    $required_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice', 'ebpid'); // check cfcid
                    foreach($required_fields as $field) {
                        if(empty($this->marketdata[$field]) && $this->marketdata[$field] != '0') {
                            $this->errorcode = 1;
                            return false;
                        }
                    }

                    if((empty($this->marketdata['cfpid']) && empty($this->marketdata['cfcid']) && empty($this->marketdata['biid']))) {// || is_empty($this->marketdata['potential'], $this->marketdata['mktSharePerc'], $this->marketdata['mktShareQty'])) {
                        $this->errorcode = 1;
                        return false;
                    }

                    /* Santize inputs - START */
                    $sanitize_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice', 'ebpid', 'comments');
                    foreach($sanitize_fields as $val) {
                        $this->marketdata[$val] = $core->sanitize_inputs($this->marketdata[$val], array('removetags' => true));
                    }

                    /* Get end product type if not specified in input */
                    if(!isset($this->marketdata['eptid']) || empty($this->marketdata['eptid'])) {
                        $brand = new EntBrandsProducts($this->marketdata['ebpid']);
                        if($brand->eptid != 0) {
                            $this->marketdata['eptid'] = $brand->get_endproduct()->eptid;
                        }
                        unset($brand);
                    }

                    /* Get affiliate if not specified in input */
                    if(!isset($this->marketdata['affid']) || empty($this->marketdata['affid'])) {
                        $customer = new Customers($this->marketdata['cid']);
                        $customer_country = $customer->get_country();
                        if(is_object($customer_country)) {
                            $cust_ctry_affiliate = $customer_country->get_affiliate();
                            if(is_object($cust_ctry_affiliate)) {
                                $this->marketdata['affid'] = $cust_ctry_affiliate->affid;
                            }
                        }

                        if(empty($this->marketdata['affid'])) {
                            $this->marketdata['affid'] = $core->user['mainaffiliate'];
                        }
                        unset($customer, $customer_country, $cust_ctry_affiliate);
                    }

                    $marketintelligence_data = array('cid' => $this->marketdata['cid'],
                            'cfpid' => $this->marketdata['cfpid'],
                            'affid' => $this->marketdata['affid'],
                            'cfcid' => $this->marketdata['cfcid'],
                            'biid' => $this->marketdata['biid'],
                            'ebpid' => $this->marketdata['ebpid'],
                            'eptid' => $this->marketdata['eptid'],
                            'vrid' => $this->marketdata['vrid'],
                            'vridentifier' => $this->marketdata['vridentifier'],
                            'potential' => $this->marketdata['potential'],
                            'mktSharePerc' => $this->marketdata['mktSharePerc'],
                            'mktShareQty' => $this->marketdata['mktShareQty'],
                            'unitPrice' => $this->marketdata['unitPrice'],
                            'turnover' => $this->marketdata['unitPrice'] * ($this->marketdata['mktShareQty'] * 1000),
                            'comments' => $this->marketdata['comments'],
                            'createdBy' => $core->user['uid'],
                            'createdOn' => $this->marketdata['visitreportdate']
                    );
                    if(empty($this->marketdata['visitreportdate'])) {
                        $marketintelligence_data['createdOn'] = TIME_NOW;
                    }

                    if(is_array($marketintelligence_data)) {
                        $query = $db->insert_query('marketintelligence_basicdata', $marketintelligence_data);
                    }

                    if($query) {
                        $log->record('createmarrketintelligence', $db->last_id());
                        $this->mibdid = $db->last_id();
                        $this->marketdata['competitor']['mibdid'] = $db->last_id();
                        if(is_array($this->marketdata['competitor'])) {
                            MarketIntelligenceCompetitors::save($this->marketdata['competitor']);
                        }
                        $this->errorcode = 0;
                    }
                }
            }
            return true;
        }
    }

// convert to dal
    public static function get_marketdata_dal($filters, $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        $configs['returnarray'] = true;
        return $data->get_objects($filters, $configs);
    }

    public static function get_marketdata() {
        global $db;
        $query = $db->query('SELECT mibdid  FROM '.Tprefix.'marketintelligence_basicdata');
        while($rows = $db->fetch_assoc($query)) {
            $marketintelligence[$rows['mibdid']] = new Marketintelligence($rows['mibdid']);
        }
        return $marketintelligence;
    }

    public function get_timelineentry_item($id, $type) {
        switch($type) {
            case ChemFunctionProducts:
                $item = new ChemFunctionProducts($id);
                return array($item->get_produt(), $item->get_chemicalfunction(), $item->get_segmentapplication(), $item->get_segment());
                break;
            case ChemFunctionChemicals:
                $item = new ChemFunctionChemicals($id);
                return array($item->get_chemicalsubstance(), $item->get_chemicalfunction(), $item->get_segmentapplication(), $item->get_segment());
                break;
            case Customers:
                $item = new Customers($id);
                return $item;
                break;
            case Affiliates:
                $item = new Affiliates($id);
                return $item;
                break;
            case BasicIngredients:
                $item = new BasicIngredients($id);
                return $item;
                break;
        }
    }

    public function parse_timelineentry_item($id, $type) {
        $displayitem = $this->get_timelineentry_item($id, $type);
        $output = '';
        if(is_array($displayitem)) {
            foreach($displayitem as $item) {
                if(empty($output['displayName'])) {
                    $output['displayName'] = $item->get_displayname();
                }
                else {
                    $output['addInfo'] .= $sep.$item->get_displayname();
                    $sep = ' - ';
                }
            }
        }
        else {
            $output['displayName'] = $displayitem->get_displayname();
        }
        return $output;
    }

    public function parse_timeline_entry(array $data, array $profile, $depth = 0, $is_last = false, $options = array()) {
        global $core, $template, $lang;
        $timedepth = 25 - ($depth * 5);
        $height = 25 - ($depth * 5);
        $top = 10 + ($depth * 2);
        /* Needs improvement */
        $left = -1.1;
        if($depth > 0) {
            $left = $left + ($depth * 0.1);
        }

        if($is_last == true) {
            $depth = $depth - 1;
        }
        $depthpaddingfix = ' padding-left: '.(30 - $depth * 10).'px;';

        $classes['entrycontainer'] = 'timeline_entry';
        $classes['entrybullet'] = 'circle circle_clickable';
        if($is_last == true) {
            $classes['entrycontainer'] = 'timeline_entry timeline_entry_dependent';
            $classes['entrybullet'] = 'circle';
        }

        $round_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');

        $altrow_class = alt_row($altrow_class);
        foreach($round_fields as $round_field) {
            if($data[$round_field] < 1) {
                $data[$round_field] = round($data[$round_field], 3);
                continue;
            }
            $data[$round_field] = round($data[$round_field]);
        }
        $entity_brdprd_objs = new EntBrandsProducts($data['ebpid']); //$maktintl_obj->get_entitiesbrandsproducts();
        $entity_brandproducts = $entity_brdprd_objs->get();

        $entity_mrktendproducts_objs = new EndProducTypes($entity_brandproducts['eptid']); //$maktintl_obj->get_marketendproducts($entity_brandproducts['eptid']);
        $entity_mrktendproducts = $lang->unspecified;
        if(!empty($entity_brandproducts['eptid'])) {
            $entity_mrktendproducts = $entity_mrktendproducts_objs->title;
        }
        if(!empty($profile['displayItem'])) {
            /* Get chemial substance if no cfpid for the cusomter */
            if(empty($data[$profile['displayItem']::PRIMARY_KEY]) && $profile['displayItem'] == ChemFunctionProducts) {
                $profile['displayItem'] = ChemFunctionChemicals;
            }
            if(empty($data[$profile['displayItem']::PRIMARY_KEY]) && $profile['displayItem'] == ChemFunctionChemicals) {
                $profile['displayItem'] = BasicIngredients;
            }
            $data['timelineItem'] = $this->parse_timelineentry_item($data[$profile['displayItem']::PRIMARY_KEY], $profile['displayItem']);
            $data['timelineItemId'] = $data[$profile['displayItem']::PRIMARY_KEY].'-'.$data['mibdid'].generate_checksum();
            $data['tlidentifier']['value'][$profile['displayItem']::PRIMARY_KEY] = $data['timelineItemId'];
            $tlidentifier['value'] = serialize($data['tlidentifier']['value']);

            if(!empty($data['tlidentifier']['id'])) {
                $tlidentifier['id'] = $data['tlidentifier']['id'].'-'.$data['timelineItemId'];
            }
            else {
                $tlidentifier['id'] = 'tlrelation-'.$data['timelineItemId'];
            }

            if(empty($data['timelineItem'])) {
                return;
            }
        }

        if(empty($data['timelineItem']['addInfo'])) {
            $data['timelineItem']['addInfo'] = date($core->settings['dateformat'], $data['createdOn']);
        }

        if($options['viewonly'] == false) {
            $updatemktintldtls_icon = '<a style="cursor: pointer;" title="'.$lang->update.'" id="updatemktintldtls_'.$data['mibdid'].'_'.$core->input['module'].'_loadpopupbyid" rel="mktdetail_'.$data[mibdid].'"><img src="'.$core->settings[rootdir].'/images/icons/update.png"/></a>';
        }

        if(!empty($data['mibdid'])) {
            eval("\$viewdetails_icon = \"".$template->get('profiles_entityprofile_mientry_viewdetails')."\";");
        }

        if($is_last == false) {
            eval("\$children_container = \"".$template->get('profiles_entityprofile_mientry_childrencontainer')."\";");
        }

        eval("\$detailmarketbox = \"".$template->get('profiles_entityprofile_mientry')."\";");
        return $detailmarketbox;
    }

    public function get_marketintelligence_timeline(array $filterby, $profile) {
        global $db;

        if(empty($profile['timelevel'])) {
            $profile['timelevel'] = 'latest';
        }

        /* Check if user can see the affid, spid, cid etc...
         * Check if user can see the affid, spid, cid etc...
         * Check if user can see the affid, spid, cid etc...
         * Check if user can see the affid, spid, cid etc...
         * Check if user can see the affid, spid, cid etc... */

//Validate market data is less then or equalthe visit report date when exist
        if(isset($filterby[date])) {
            $filterdate = $filterby[date];
            unset($filterby[date]);
            $filterss = ' AND (createdOn)<= '.$filterdate;
        }
        foreach($filterby as $attr => $id) {
            $filters .= $filtersand.$attr.' = '.intval($id);
            $filtersand = ' AND ';
        }

        $is_lastlevel = false;
        if(in_array('mibdid', $profile['groupby'])) {
            $is_lastlevel = true;
        }


        $latestentry_query = 'SELECT MAX(createdOn) FROM '.Tprefix.'marketintelligence_basicdata WHERE '.$filters.$filterss;

        if($profile['timelevel'] == 'latest') {
            $where_query = ' AND createdOn IN ('.$latestentry_query.' GROUP BY '.$db->escape_string(implode(', ', $profile['aggregateby'])).')';
        }
        elseif($profile['timelevel'] == 'allprevious') {
            $where_query = ' AND createdOn IN (SELECT createdOn FROM '.Tprefix.'marketintelligence_basicdata WHERE '.$filters.' AND createdOn < ('.$latestentry_query.' GROUP BY '.implode(', ', array_keys($filterby)).') GROUP BY '.$db->escape_string(implode(', ', $profile['aggregateby'])).')';
        }
        else {
            $where_query = ' AND createdOn IN ('.$latestentry_query.' GROUP BY '.$db->escape_string(implode(', ', $profile['aggregateby'])).')';
        }
        $query = $db->query('SELECT *, SUM(potential) AS potential, SUM(mktShareQty) mktShareQty, (SUM(mktShareQty)/SUM(potential)*100) AS mktSharePerc, AVG(unitPrice) AS unitPrice FROM '.Tprefix.'marketintelligence_basicdata WHERE createdOn!=0 '.$where_query.' AND '.$filters.' GROUP BY '.$db->escape_string(implode(', ', $profile['groupby'])).' ORDER BY cfpid, createdOn DESC');
        if($db->num_rows($query) > 0) {
            while($rows = $db->fetch_assoc($query)) {
                if($is_lastlevel == false) {
                    unset($rows['mibdid']);
                }
                $marketintelligence[] = $rows;
            }
            $db->free_result($query);
            return $marketintelligence;
        }
        return false;
    }

    public function get_marketendproducts($id) {
        return new EndProducTypes($id);
    }

    public function get_competitors() {
        global $db;
        $query = $db->query('SELECT micid FROM '.Tprefix.'marketintelligence_competitors WHERE mibdid='.$this->marketintelligence['mibdid'].'');
        while($rows = $db->fetch_assoc($query)) {
            $marketcomp[$rows['micid']] = new MarketIntelligenceCompetitors($rows['micid']);
        }
        return $marketcomp;
    }

    public function get_visitreport() {
        if(empty($this->marketintelligence['vrid'])) {
            return false;
        }
        return new CrmVisitReports($this->marketintelligence['vrid']);
    }

    public function get_customer() {
        return new Entities($this->marketintelligence['cid']);
    }

    public function get_chemfunctionproducts() {
        if($this->marketintelligence['cfpid'] == 0) {
            return false;
        }
        return new ChemFunctionProducts($this->marketintelligence['cfpid']);
    }

    public function get_basicingredients() {
        if($this->marketintelligence['biid'] != 0) {
            return new BasicIngredients($this->marketintelligence['biid']);
        }
    }

    public function get_chemfunctionschemcials() {
        if($this->marketintelligence['cfcid'] != 0) {
            return new ChemFunctionChemicals($this->marketintelligence['cfcid']);
        }
    }

    public function get_entitiesbrandsproducts() {
        return new EntBrandsProducts($this->marketintelligence['ebpid']);
    }

    public function get_createdby() {
        return new Users($this->marketintelligence['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->marketintelligence['modifiedBy']);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function __get($name) {
        if(isset($this->marketintelligence[$name])) {
            return $this->marketintelligence[$name];
        }
        return false;
    }

    public function get_miprofconfig_byname($name) {
        if(!isset($this->miprofiles[$name])) {
            return $this->miprofiles['latestcustomersumbyproduct']; //Default
        }
        return $this->miprofiles[$name];
    }

    public function get() {
        return $this->marketintelligence;
    }

    public function apriori() {
        global $db, $core;
//get cfpid chemical product

        $chemproducts = $this->get_chemfunctionproducts()->get_produt()->get();
//$query = $db->query('SELECT * FROM '.Tprefix.'marketintelligence_competitors WHERE cid = '.$this->marketintelligence['cid'].'');
        return 'pid '.$chemproducts['pid'].' : '.'<br>';
    }

}

class MarketIntelligenceCompetitors {
    private $mrktintelcompetitors = array();

    public function __construct($id = '', $simple = false) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'micid, mibdid';
        }
        $this->mrktintelcompetitors = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'marketintelligence_competitors WHERE micid = '.intval($id)));
    }

    public static function save($data = array()) {
        global $db, $core;

        $market_competitors = $data;
        $sanitize_fields = array('trader', 'unitPrice', 'pid', 'unitPrice', 'incoterms', 'packaging', 'saletype', 'isSampleacquire');
        foreach($sanitize_fields as $val) {
            $market_competitors_data[$val] = $core->sanitize_inputs
                    ($market_competitors_data[$val], array('removetags' => true));
        }

        if(is_array($market_competitors)) {
            foreach($market_competitors as $market_competitor) {
                if(empty($market_competitor['pid'])) {
                    continue;
                }
                $market_competitors_data = array('mibdid' => $market_competitors[mibdid],
                        // 'eid' => $market_competitor['eid'],
                        'trader' => $market_competitor['trader'],
                        'producer' => $market_competitor['producer'],
                        'unitPrice' => $market_competitor['unitPrice'],
                        'pid' => $market_competitor['pid'],
                        'incoterms' => $market_competitor['incoterms'],
                        'packaging' => $market_competitor['packaging'],
                        'saletype' => $market_competitor['saletype'],
                        'isSampleacquire' => $market_competitor['isSampleacquire'],
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW
                );
                $query = $db->insert_query('marketintelligence_competitors', $market_competitors_data);
            }
        }
    }

    public function delete() {
        global $db;
        if(isset($this->mrktintelcompetitors['micid'])) {
            $db->delete_query('marketintelligence_competitors', 'micid = '.$this->mrktintelcompetitors['micid']);
        }
    }

    public function get_products() {
        global $db;
        $query = $db->query('SELECT pid FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
        while($rows = $db->fetch_assoc($query)) {
            $marketcompproducts[$rows['pid']] = new Products($rows['pid']);
        }
        return $marketcompproducts;
    }

    public function get_entities() {
        global $db;
        $query = $db->query('SELECT eid FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
        while($rows = $db->fetch_assoc($query)) {
            $marketcompsupp[$rows['eid']] = new Entities($rows['eid']);
        }
        return $marketcompsupp;
    }

    public function get_entityproducer() {
        global $db;
        $query = $db->query('SELECT producer FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
        while($rows = $db->fetch_assoc($query)) {
            $marketcomproducer[$rows['producer']] = new Entities($rows['producer']);
        }
        return $marketcomproducer;
    }

    public function get_entitytrader() {
        global $db;
        $query = $db->query('SELECT trader FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
        while($rows = $db->fetch_assoc($query)) {
            $marketcomptrader[$rows['trader']] = new Entities($rows['trader']);
        }
        return $marketcomptrader;
    }

    public function __get($name) {
        if(isset($this->mrktintelcompetitors[$name])) {
            return $this->mrktintelcompetitors[$name];
        }
        return false;
    }

    public function get() {
        return $this->mrktintelcompetitors;
    }

}
?>