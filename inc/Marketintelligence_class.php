<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Marketintelligence_class.php
 * Created:        @tony.assaad    Dec 27, 2013 | 3:24:47 PM
 * Last Update:    @tony.assaad    Dec 27, 2013 | 3:24:47 PM
 */

/**
 * Description of Marketintelligence_class
 *
 * @author tony.assaad
 */
class Marketintelligence {
	private $marketintelligence = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'mibdid,cid';
		}
		$this->marketintelligence = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'marketintelligence_basicdata WHERE mibdid='.intval($id)));
	}

	public function create($data = array()) {
		global $db, $core;
		if(is_array($data)) {
			$this->marketdata = $data;
			if(is_empty($this->marketdata['cfpid'], $this->marketdata['potential'], $this->marketdata['mktSharePerc'], $this->marketdata['mktShareQty'])) {
				$this->errorcode = 1;
				return false;
			}
			/* Santize inputs - START */
			$sanitize_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice', 'ebpid', 'comments');
			foreach($sanitize_fields as $val) {
				$this->marketdata[$val] = $core->sanitize_inputs($this->marketdata[$val], array('removetags' => true));
			}
			/* insert entity Brands */

			$marketintelligence_data = array('cid' => $this->marketdata['cid'],
					'cfpid' => $this->marketdata['cfpid'],
					'ebpid' => $this->marketdata['ebpid'],
					'potential' => $this->marketdata['potential'],
					'mktSharePerc' => $this->marketdata['mktSharePerc'],
					'mktShareQty' => $this->marketdata['mktShareQty'],
					'unitPrice' => $this->marketdata['unitPrice'],
					'comments' => $this->marketdata['comments'],
					'createdBy' => $core->user['uid'],
					'createdOn' => TIME_NOW
			);
			if(is_array($marketintelligence_data)) {
				$query = $db->insert_query('marketintelligence_basicdata', $marketintelligence_data);
			}

			if($query) {
				$this->marketdata['competitor']['mibdid'] = $db->last_id();
				if(is_array($this->marketdata['competitor'])) {
					Marketintelligencecompetitors::save($this->marketdata['competitor']);
				}
				$this->errorcode = 0;
				return true;
			}
		}
	}

	public function get_marketintelligence_byentity($id) {
		global $db;
		$query = $db->query('SELECT mibdid  FROM '.Tprefix.'marketintelligence_basicdata WHERE  YEAR(CURDATE()) <= FROM_UNIXTIME(createdOn, "%Y") AND cid='.$id.' ORDER BY cfpid,createdOn DESC');
		
		while($rows = $db->fetch_assoc($query)) {
			$marketintelligence[$rows['mibdid']] = new Marketintelligence($rows['mibdid']);
		}
		return $marketintelligence;
	}

	public function get_previousmarketintelligence($id) {
		global $db;
		if(!empty($id)) {
			$query = $db->query('SELECT mibdid ,createdOn FROM '.Tprefix.'marketintelligence_basicdata WHERE cfpid="'.$id.'" AND YEAR(CURDATE()) > FROM_UNIXTIME(createdOn, "%Y")  ORDER BY cfpid,createdOn DESC');
			while($rows = $db->fetch_assoc($query)) {
				$prevmarketintelligence[$rows['mibdid']] = new Marketintelligence($rows['mibdid']);
			}
			return $prevmarketintelligence;
		}
	}

	public function get_competitors() {
		global $db;
		$query = $db->query('SELECT micid  FROM '.Tprefix.'marketintelligence_competitors WHERE mibdid='.$this->marketintelligence['mibdid'].'');
		while($rows = $db->fetch_assoc($query)) {
			$marketcomp[$rows['micid']] = new Marketintelligencecompetitors($rows['micid']);
		}
		return $marketcomp;
	}

	public function get_customer() {
		return new Entities($this->marketintelligence['cid']);
	}

	public function get_chemfunctionproducts() {
		return new Chemfunctionproducts($this->marketintelligence['cfpid']);
	}

	public function get_entitiesbrandsproducts() {
		return new Entbrandsproducts($this->marketintelligence['ebpid']);
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

	public function get() {
		return $this->marketintelligence;
	}

	public function apriori() {
		global $db, $core;
		//get cfpid chemical product
		$chemproducts = $this->get_chemfunctionproducts()->get_produt()->get();

//		SELECT DISTINCT store_type FROM stores s1
//		WHERE   EXISTS (
//		SELECT * FROM cities WHERE NOT EXISTS (
//		SELECT * FROM cities_stores
//		WHERE cities_stores.city = cities.city
//		AND cities_stores.store_type = stores.store_type));
//		
		//$query = $db->query('SELECT *  FROM '.Tprefix.'marketintelligence_competitors WHERE cid='.$this->marketintelligence['cid'].'');
		return 'pid '.$chemproducts['pid'].' : '.'<br>';
	}

}

class Marketintelligencecompetitors {
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
			$query_select = 'micid,mibdid';
		}
		$this->mrktintelcompetitors = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.intval($id)));
	}

	public static function save($data = array()) {
		global $db, $core;

		$market_competitors = $data;
		$sanitize_fields = array('eid', 'unitPrice', 'pid', 'unitPrice');
		foreach($sanitize_fields as $val) {
			$market_competitors_data[$val] = $core->sanitize_inputs($market_competitors_data[$val], array('removetags' => true));
		}

		if(is_array($market_competitors)) {
			foreach($market_competitors as $market_competitor) {
				if(empty($market_competitor['pid'])) {
					continue;
				}
				$market_competitors_data = array('mibdid' => $market_competitors[mibdid],
						'eid' => $market_competitor['eid'],
						'unitPrice' => $market_competitor['unitPrice'],
						'pid' => $market_competitor['pid'],
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
			$db->delete_query('marketintelligence_competitors', 'micid='.$this->mrktintelcompetitors['micid']);
		}
	}

	public function get_products() {
		global $db;
		$query = $db->query('SELECT pid  FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
		while($rows = $db->fetch_assoc($query)) {
			$marketcompproducts[$rows['pid']] = new Products($rows['pid']);
		}
		return $marketcompproducts;
	}

	public function get_entities() {
		global $db;
		$query = $db->query('SELECT eid  FROM '.Tprefix.'marketintelligence_competitors WHERE micid='.$this->mrktintelcompetitors['micid'].'');
		while($rows = $db->fetch_assoc($query)) {
			$marketcompsupp[$rows['eid']] = new Entities($rows['eid']);
		}
		return $marketcompsupp;
	}

	public function get() {
		return $this->mrktintelcompetitors;
	}

}
?>
