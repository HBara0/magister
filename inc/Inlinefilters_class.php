<?php
class Inlinefilters {
	private $filters;
	private $cache;
	private $filters_values;
	private $options = array();
	
	public function __construct(array $filters) {
		$this->options['filters'] = $filters;	
	}
	
 	public function parse_filter($id, $value) {
		global $lang;
		
		if(!in_array($id, $cache['usedids'])) {
			$filter = '<a href="&filterby='.$id.'&filtervalue='.$value.'"><img src="./images/icons/search.gif" border="0" alt="'.$lang->filterby.'"/></a>';	
			$this->cache['usedids'][$id] = $id;
			
			$this->set_filter($id, $value);
			return $filter;
		}
		return false;
	}
	
	public function parse_filters_panel() {
		foreach($this->used_filters as $used_filter_id => $used_filter_value) {
			
			/* Parse delete link - START */
			$used_filters_temp = $this->used_filters;
			unset($used_filter_temp[$used_filter_id]);
			
			$link = base64_encode(serialize($this->used_filters));	
			/* Parse delete link - END */
		}
	}
	
	public function set_filter($filter, $value) {
		$this->filters_values[$filter][] = $value;	
	}
}
?>