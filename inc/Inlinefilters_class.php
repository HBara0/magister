<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Filtering/Inline Search Class
 * $id: Filter_class.php
 * Created: 	@zaher.reda		October 11, 2012 | 10:03 AM
 * Last Update: @tony.assaad	December 05, 2012 | 10:03 AM
 */

class Inlinefilters {
    /* IMPORTANT: Config Array Structure,  Explained - START
      $config = array(
      'parse' => array( // Config to parse fields
      'filters' => array('attr1', 'attr2', 'attr3', 'attr4', 'attr5'), // Filter names to be used for filter fields
      'filterTitles' => array('attr1' => 'Attribute Name'), // [Optional] Specific titles for specific fields
      'overwriteField' => array('attr2' => 'SOME HTML'), // [Optional] Overwrite generated field by custom HTML
      'fieldsSequence' => array('attr2' => 1, 'attr1' => 2) // [Optional] By default parse of filter row follows the sequence of attributes in 'filters'; you can overwrite the sequence here. Useful in case where you add a field later.
      ),
      'process' => array( // Config to configure the filtering process
      'filterKey' => 'attr', // The key to which's values will be returned to be user if later queries
      'mainTable' => array( // Configurations for the main filtering table
      'name' => 'tablename', // Table name
      // Below are the filters that are applicable to this main table. The index of the array is the filter name, and the value is the attribute name. Attribute name can have prefixes
      // The value can optionally be an array for more complex filter; where 'operatorType' specifies the operator of the logical operation, and 'name' specified the attribute name
      // 'operatorType' can be 'multiple' to use IN, 'between' to use BETWEEN, and nothing or 'single' to use LIKE
      'filters' => array('attr1' => 'attr1', 'attr2' => 'attr2', 'attr3' => 'attr3', 'attr4' => array('operatorType' => 'between', 'name' => 'attr4'))
      'extraSelect' => 'attr8 AS attr9', // [Optional] Custom addition to the SELECT statement, useful for aggregate functions and CONCAT. To be used jointly with 'havingFilters'
      'havingFilters' => array('attr8' => 'attr9') // [Optional] Some filters require HAVING because they are a result of an aggregate function on CONCAT; these are specified here rather than the 'filter' which relies on WHERE statement
      ),
      'secTables' => array( // In case filters are to be performed on other related tabels (associative entities), their configurations are done here; table by table
      'sectablename' => array( // [Optional] Index of each table entry is the table name
      'keyAttr' => 'secattr', // [Optional] Key attribute of the secondary table
      'joinKeyAttr' => 'secattr', [Optional] In case of join with another table; state the other table key for ON statement
      'joinWith' => 'othertable',// [Optional] In case of join with another table; state the table name
      'joinWord' => 'JOIN', // [Optional] The join keyword; ex LEFT JOIN, RIGHT JOIN etc...
      'filters' => array('attr5' => array('operatorType' => 'multiple', 'name' => 'sectablename.attr5')),
      'filterKeyOverwrite' => '', // [Optional] Overwerite the 'filterKey' in case it doesn't have the same name in this secondary table (ex. eid <> spid)
      'extraWhere' => ''// [Optional] If the table requires another specific filter, this can be set from here. Do not use WHERE statement in this.
      'havingFilters' => array('attr8' => 'attr9') // [Optional] Some filters require HAVING because they are a result of an aggregate function on CONCAT; these are specified here rather than the 'filter' which relies on WHERE statement
      ),
      'sectablename2' => array(// Another secondary table; with more simple filtering (no joins)
      'filters' => array('attr6' => array('operatorType' => 'multiple', 'name' => 'attr6')),
      )
      )
      )
      );
     *
      EXAMPLE:
      $config = array(
      'parse' => array('filters' => array('fullName', 'displayName', 'mainAffid', 'position', 'reportsTo'),
      'filterTitles' => array('fullName' => 'Full Name'),
      'overwriteField' => array('position' => '<select><option>Position 1</option><option>Position 2</option></select>')
      ),
      'process' => array('filterKey' => 'uid',
      'mainTable' => array(
      'name' => 'users',
      'filters' => array('fullName' => 'fullName', 'displayName' => 'displayName', 'reportsTo' => 'reportsTo', 'dateAdded' => array('operatorType' => 'between', 'name' => 'dateAdded'))
      ),
      'secTables' => array(
      'employeessegments' => array(
      'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
      ),
      'assignedemployees' => array(
      'filters' => array('supplier' => array('operatorType' => 'multiple', 'name' => 'eid')),
      )
      )
      )
      );
      Config Array Structure,  Explained - END */
    private $config = array();
    private $matching_rule = 'all';
    private $parsed_fields = array();

    /* Construct the filtering object
     * @param  Array		$config			Configuration array for the filtering operation
     * @param  String		$matching_rule	A match-all (all) or match-any (any) rule for the queries
     */
    public function __construct($config, $matching_rule = 'all') {
        $this->config = $config;
        $this->set_matchingrule($matching_rule);
    }

    /* Parse the search fiedls based on configurations
     * @return Array		$filters			Parsed fields to be used in search
     */
    public function parse_multi_filters(array $exclude = array()) {
        global $core, $db, $lang;
        $tabindex = 1;

        if(is_array($this->config['parse'])) {
            $permissions = $core->user_obj->get_businesspermissions();
            foreach($this->config['parse']['filters'] as $filter) {
                if(in_array($filter, $exclude)) {
                    continue;
                }
                if(!isset($this->config['parse']['overwriteField'][$filter])) {
                    switch($filter) {
                        case 'affid':
                        case 'affiliate':
                        case 'mainAffid':
                        case 'useraffiliates':
                            if($core->usergroup['canViewAllAff'] == 0) {
                                $affiliate_where = 'affid IN ('.implode(',', $core->user['affiliates']).')';
                            }
                            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', '', 0, $affiliate_where);
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $affiliates, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3, 'blankstart' => true));
                            break;
                        case 'allenabledaffiliates':
                        case 'allaffiliates':
                            $affiliate_where = 'isActive = 1';
                            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', '', 0, $affiliate_where);
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $affiliates, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3, 'blankstart' => true));
                            break;
                        case 'ciid':
                            $city = new Cities($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" autocomplete="off" tabindex="1"  id="cities_cache_autocomplete" value="'.$city->get_displayname().'"/><input type="hidden" id="cities_cache_id" name="filters['.$filter.']"/>';
                            break;
                        case 'coid':
                            $countries = Countries::get_data('');
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $countries, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3));
                            break;
                        case 'ciid':
                            $city = new Cities($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" autocomplete="off" tabindex="1"  id="cities_cache_autocomplete" value="'.$city->get_displayname().'"/><input type="hidden" id="cities_cache_id" name="filters['.$filter.']"/>';
                            break;
                        case 'coid':
                            $countries = Countries::get_data('');
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $countries, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3));
                            break;
                        case 'posid':
                        case 'position':
                            $lang->load('positions');
                            $positions = get_specificdata('positions', array('posid', 'name'), 'posid', 'name', 'name');
                            foreach($positions as $key => $val) {
                                if(isset($lang->{$val})) {
                                    $positions[$key] = $lang->{$val};
                                }
                            }
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $positions, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3, 'blankstart' => true));
                            break;
                        case 'usersuppliers':
                            $filters[$filter] = '';
                            break;
                        case 'pid':
                        case 'product':
                        case 'products':
                            $product = new Products($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" autocomplete="off" tabindex="1"  id="product_noexception_cache_autocomplete" value="'.$product->name.'"/><input type="hidden" id="products_cache_id" name="filters['.$filter.']"/><input type="hidden" id="product_noexception_cache_id" name="filters['.$filter.']"/>';
                            break;
                        case 'psid': //all segments
                        case 'segment': //all segments
                        case 'usersegments': //user assigned segments
                            if($filter == 'usersegments' && is_array($permissions['psid'])) {
                                $psegments_query = $db->query("SELECT ps.psid, title FROM ".Tprefix."productsegments ps WHERE ps.psid IN (".implode(',', array_filter($permissions['psid'], 'is_numeric')).")");
                                while($productline = $db->fetch_assoc($psegments_query)) {
                                    $productlines[$productline['psid']] = $productline['title'];
                                }
                            }
                            else {
                                $productlines = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', '');
                            }
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $productlines, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3, 'blankstart' => true));
                            break;
                        case 'reportsTo':
                            $reportsto = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', array('sort' => 'ASC', 'by' => 'displayName'), 0, 'gid!=7 AND uid IN (SELECT reportsTo FROM '.Tprefix.'users WHERE gid!=7)');
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $reportsto, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3, 'blankstart' => true));
                            break;
                        case 'uid':
                            $users = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', array('sort' => 'ASC', 'by' => 'displayName'), 0, '');
                            $filters[$filter] = parse_selectlist('filters['.$filter.'][]', $tabindex, $users, $core->input['filters'][$filter], 1, '', array('multiplesize' => 3));
                            break;
                        case 'fromDate':
                        case 'toDate':
                        case 'date':
                        case 'time':
                        case 'createdOn':
                        case 'effectiveFrom':
                        case 'effectiveTo':
                            $filters[$filter] = '<input type="text" id="pickDate_'.$filter.'_from" autocomplete="off" tabindex="'.$tabindex.'" value="'.$core->input['filters'][$filter]['from'].'" /><input type="hidden" name="filters['.$filter.'][from]" id="altpickDate_'.$filter.'_from" value="'.$core->input['filters'][$filter]['from'].'" /><br />';
                            $filters[$filter] .= '<input type="text" id="pickDate_'.$filter.'_to" autocomplete="off" tabindex="'.($tabindex + 1).'" value="'.$core->input['filters'][$filter]['to'].'" /><input type="hidden" name="filters['.$filter.'][to]" id="altpickDate_'.$filter.'_to" value="'.$core->input['filters'][$filter]['to'].'" />';
                            break;
                        case 'cid':
                        case 'customer':
                            $customer = new Entities($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" id="customer_1_autocomplete" value="'.$customer->get_displayname().'"/><input type="hidden" id="customer_1_id" name="filters['.$filter.']"/>';
                            break;
                        case 'biid':
                            $basicingredient = new BasicIngredients($core->input['filters'][$filter]);
                            $filters[$filter] = '<input placeholder="Basic Ingredient" type="text" id="basicingredients_'.$filter.'_autocomplete" value="'.$basicingredient->get_displayname().'"/><input type="hidden" id="basicingredients_'.$filter.'_id" name="filters['.$filter.']"/>';
                            break;
                        case 'entities':
                            $entity = new Entities($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" id="allentities_1_autocomplete" value="'.$entity->get_displayname().'"/><input type="hidden" id="allentities_1_id" name="filters['.$filter.']"/>';
                            break;
                        case 'userpermentities':
                            $entity = new Entities($core->input['filters'][$filter]);
                            $filters[$filter] = '<input type="text" id="userpermissionentities_1_autocomplete" value="'.$entity->get_displayname().'"/><input type="hidden" id="userpermissionentities_1_id" name="filters['.$filter.']"/>';
                            break;
//                        case 'spid':
//                        case 'supplier':
//                            $supplier = new Entities($core->input['filters'][$filter]);
//                            $filters[$filter] = '<input type="text" id="supplier_1_autocomplete" value="'.$supplier->get_displayname().'"/><input type="hidden" id="supplier_1_id" name="filters['.$filter.']"/>';
//                            break;
//                        case 'csid':
//                        case 'chemical':
//                        case 'chemicals':
//                        case 'chemicalsubstance':
//                            $chemical = new Chemicalsubstances($core->input['filters'][$filter]);
//                            $filters[$filter] = '<input type="text" autocomplete="off" tabindex="1"  id="chemfunctionchecmical_noexception_cache_autocomplete" value="'.$chemical->get_displayname().'"/><input type="hidden" id="chemfunctionchecmical_cache_id" name="filters['.$filter.']"/><input type="hidden" id="chemfunctionchecmical_noexception_cache_id" name="filters['.$filter.']"/>';
//                            break;
                        default:
                            $filters[$filter] = '<input type="text" width="100%" name="filters['.$filter.']" tabindex="'.$tabindex.'" value="'.$core->input['filters'][$filter].'" id="filers_'.$filter.'" title="'.$this->config['parse']['filterTitles'][$filter].'">';
                            break;
                    }
                }
                else {
                    $filters[$filter] = $this->config['parse']['overwriteField'][$filter];
                }
                $tabindex++;
            }
            $this->parsed_fields = $filters;

            return $filters;
        }
        return false;
    }

    public function parse_hidden_filters(array $filters) {
        global $lang;

        if(!empty($filters)) {
            foreach($filters as $filter) {
                if(!isset($this->parsed_fields[$filter])) {
                    continue;
                }

                $hidden_fields .= $lang->{$filter}.' '.$this->parsed_fields[$filter].'<br />';
            }
            if(!empty($hidden_fields)) {
                return '<tr><td><div id="popup_additionalfilters" title="'.$lang->additionalfilters.'">'.$hidden_fields.'</div></td></tr>';
            }
        }
        return false;
    }

    public function prase_filtersrows($options = array('tags' => 'table', 'display' => 'show'), $exclude = array()) {
        global $lang;

        if($options['display'] == 'hide') {
            $options['display'] = 'display: none;';
        }
        else {
            $options['display'] = 'display: table-row;';
        }

        if($options['tags'] == 'div') {
            $rows .= '<div class="tablefilters_row" style="'.$options['display'].'" id="tablefilters">';
        }
        else {
            $rows .= '<tr class="tablefilters_row" style="'.$options['display'].'" id="tablefilters">';
        }

        if(empty($this->parsed_fields)) {
            $this->parse_multi_filters($exclude);
        }

        $count_items = 1;

        $iteration_element = $this->parsed_fields;
        if(isset($this->config['parse']['fieldsSequence']) && !empty($this->config['parse']['fieldsSequence'])) {
            $iteration_element = $this->config['parse']['fieldsSequence'];
        }

        foreach($iteration_element as $filter_item => $filter_value) {
            if(!isset($options['fielddisplay'])) {
                $options['fielddisplay'] = 'display:block';
            }
            if($options['tags'] == 'div') {
                $rows .= '<div>'.$this->parsed_fields[$filter_item].'</div>'; //Will add more CSS for this later
            }
            else {
                //style="'.$options['fielddisplay'].'"
                $rows .= '<th >'.$this->parsed_fields[$filter_item].'</th>';
            }
            $count_items++;
        }
        unset($filter_value);
        if(!isset($options['hidebutton']) || $options['hidebutton'] == false) {
            if($options['tags'] == 'div') {
                $rows .= '<div><input type="image" src="./images/icons/search.gif" border="0" alt="'.$lang->filter.'" value="'.$lang->filter.'"></div></div>';
                $rows .= '<div class="tablefilters_row_toggle" onClick="$(\'#tablefilters\').toggle();">&middot;&middot;&middot;</div>';
            }
            else {
                if(isset($options['overwriteCountItems']) && !empty($options['overwriteCountItems'])) {
                    $count_items = $options['overwriteCountItems'];
                }
                $rows .= '<th style="text-align:right;"><input type="image" src="./images/icons/search.gif" border="0" alt="'.$lang->filter.'" value="'.$lang->filter.'">';
                $rows .= '<br /><a href="'.$_SERVER['REQUEST_URI'].'"><img src="./images/invalid.gif" alt="'.$lang->clear.'" border="0" /></a></th></tr>';
                $rows .= '<tr class="tablefilters_row_toggle"><td colspan="'.$count_items.'" onClick="$(\'#tablefilters\').toggle();"><a title="'.$lang->filter.'">&middot;&middot;&middot;</a></td></tr>';
            }
        }
        return $rows;
    }

    /* Perform advanced multi filtering from DB
     * @return Array $items	A unique array of requested data
     */
    public function process_multi_filters() {
        global $core, $db;

        if(!isset($core->input['filters'])) {
            return false;
        }

        $main_query_where = $this->parse_filterstatements($this->config['process']['mainTable']['filters']);

        if(isset($this->config['process']['mainTable']['havingFilters']) && !empty($this->config['process']['mainTable']['havingFilters'])) {
            $main_query_having = $this->parse_filterstatements($this->config['process']['mainTable']['havingFilters']);
        }

        $this->config['process']['filterKey'] = $db->escape_string($this->config['process']['filterKey']);

        if(!empty($main_query_having) || !empty($main_query_where)) {
            if(!empty($main_query_where)) {
                $main_query_where = ' WHERE '.$main_query_where;
            }

            if(!empty($main_query_having)) {
                $main_query_having = ' HAVING '.$main_query_having;
            }

            if(!empty($this->config['process']['mainTable']['extraSelect'])) {
                $this->config['process']['mainTable']['extraSelect'] = ', '.$this->config['process']['mainTable']['extraSelect'];
            }

            $main_query = $db->query('SELECT '.$this->config['process']['filterKey'].$this->config['process']['mainTable']['extraSelect'].'
								FROM '.Tprefix.$this->config['process']['mainTable']['name'].'
								'.$main_query_where.$main_query_having);


            if($db->num_rows($main_query) > 0) {
                while($item = $db->fetch_assoc($main_query)) {
                    $items[$item[$this->config['process']['filterKey']]] = $item[$this->config['process']['filterKey']];
                }
            }
            else {
                /* If all should match, and main has no results, then stop the process */
                if($this->matching_rule == 'all') {
                    return array(0);
                }
            }
        }

        if(isset($this->config['process']['secTables'])) {
            foreach($this->config['process']['secTables'] as $table => $options) {
                if(isset($options['tablename']) && !empty($options['tablename'])) {
                    $table = $options['tablename'];
                }
                if(!isset($options['filterKeyOverwrite']) || empty($options['filterKeyOverwrite'])) {
                    $options['filterKeyOverwrite'] = $this->config['process']['filterKey'];
                }

                /* Prepare any necessary joins - START */
                $sec_query_join_clause = $sec_query_having = $sec_query_where = '';
                if((isset($options['joinWith']) && !empty($options['joinWith'])) && (isset($options['joinKeyAttr']) && !empty($options['joinKeyAttr']))) {
                    if(!isset($options['joinWord']) && empty($options['joinWord'])) {
                        $options['joinWord'] = 'JOIN';
                    }
                    $sec_query_join_clause = ' '.$options['joinWord'].' '.$options['joinWith'].' ON ('.$table.'.'.$options['keyAttr'].'='.$options['joinWith'].'.'.$options['joinKeyAttr'].')';
                }
                /* Prepare any necessary joins - END */

                /* Prepare WHERE statement filters - START */
                if(isset($options['filters']) && !empty($options['filters'])) {
                    $sec_query_where = $this->parse_filterstatements($options['filters']);
                }
                /* Prepare WHERE statement filters - END */

                if(!empty($sec_query_where)) {
                    $sec_query_where = 'WHERE '.$sec_query_where;
                }

                if(isset($options['havingFilters']) && !empty($options['havingFilters'])) {
                    $sec_query_having = $this->parse_filterstatements($options['havingFilters']);
                }

                if(!empty($sec_query_where) || !empty($sec_query_having)) {

                    if($this->matching_rule == 'all' && !empty($items)) {
                        $sec_query_where .= ' AND '.$options['filterKeyOverwrite'].' IN ("'.implode('", "', $items).'")';
                    }

                    if(isset($options['extraWhere']) && !empty($options['extraWhere'])) {
                        $sec_query_where .= ' AND '.$db->escape_string($options['extraWhere']);
                    }

                    if(!empty($options['extraSelect'])) {
                        $options['extraSelect'] = ', '.$options['extraSelect'];
                    }

                    if(!empty($sec_query_having)) {
                        $sec_query_having = ' HAVING '.$sec_query_having;
                    }

                    $sec_query = $db->query('SELECT DISTINCT('.$options['filterKeyOverwrite'].')'.$options['extraSelect'].'
										FROM '.Tprefix.$table.$sec_query_join_clause.'
										 '.$sec_query_where.$sec_query_having);

                    if($db->num_rows($sec_query) > 0) {
                        while($item = $db->fetch_assoc($sec_query)) {
                            $items_sec[$item[$options['filterKeyOverwrite']]] = $item[$options['filterKeyOverwrite']];
                        }

                        if(is_array($items)) {
                            $items = array_intersect($items, $items_sec);
                        }
                        else {
                            $items = $items_sec;
                        }

                        unset($items_sec);
                    }
                    else {
                        if($this->matching_rule == 'all') {
                            return array(0);
                        }
                    }
                }
            }
        }
        return $items;
    }

    private function parse_filterstatements(array $filters) {
        global $core;

        $query_filter_statement = '';
        foreach($filters as $filteritem => $attr) {
            if((isset($core->input['filters'][$filteritem]) && (!empty($core->input['filters'][$filteritem])) || ($core->input['filters'][$filteritem] == '0'))) {
                $wherestatement = $this->parse_whereentry($attr, $filteritem);
                if(!empty($query_filter_statement)) {
                    if($this->matching_rule == 'all') {
                        $query_operator = ' AND ';
                    }
                    else {
                        $query_operator = ' OR ';
                    }
                }
                if(!empty($wherestatement)) {
                    $query_filter_statement .= $query_operator.$wherestatement;
                }
            }
        }

        if(empty($query_filter_statement)) {
            return false;
        }

        return $query_filter_statement;
    }

    private function parse_whereentry($attr_param, $filteritem) {
        global $db, $core;

        if(!is_array($attr_param)) {
            $attr['name'] = $attr_param;
            $attr['operatorType'] = 'single';
        }
        else {
            $attr = $attr_param;
        }

        if($attr['operatorType'] == 'multiple') {
            $query_where = $sec_query_operator.$attr['name'].' IN ("'.implode('", "', $core->input['filters'][$filteritem]).'")'; //To add $db->escape_string();
        }
        elseif($attr['operatorType'] == 'between') {
            $query_where = $sec_query_operator.'('.$attr['name'].' BETWEEN '.$db->escape_string($core->input['filters'][$filteritem]['start']).' AND '.$db->escape_string($core->input['filters'][$filteritem]['end']).')';
        }
        elseif($attr['operatorType'] == 'date') {
            if(empty($core->input['filters'][$filteritem]['from'])) {
                if(empty($core->input['filters'][$filteritem]['to'])) {
                    return null;
                }
                $core->input['filters'][$filteritem]['from'] = date('Y-M-d', strtotime($core->input['filters'][$filteritem]['from'])).' 00:00:00';
            }
            if(empty($core->input['filters'][$filteritem]['to'])) {
                $core->input['filters'][$filteritem]['to'] = date('Y-m-d', strtotime($core->input['filters'][$filteritem]['from'])).' 23:59:59';
            }
            $query_where = $sec_query_operator.'('.$attr['name'].' BETWEEN '.strtotime($core->input['filters'][$filteritem]['from']).' AND '.(strtotime($core->input['filters'][$filteritem]['to'])).')';
        }
        elseif($attr['operatorType'] == 'equal') {
            $query_where = $sec_query_operator.$attr['name'].'="'.$db->escape_string($core->input['filters'][$filteritem]).'"';
        }
        else {
            /* Check if user using seperator to filter multiple values of same attribute */
            $like_prefix = '%';
            if($attr['operatorType'] === 'startswith') {
                $like_prefix = '';
            }
            if(strstr($core->input['filters'][$filteritem], ';')) {
                $multiple_items = explode(';', $core->input['filters'][$filteritem]);
                foreach($multiple_items as $value) {
                    $query_where .= $query_where_operator.$sec_query_operator.$attr['name'].' LIKE "'.$like_prefix.$db->escape_string($value).'%"';
                    $query_where_operator = ' OR ';
                }

                $query_where = '('.$query_where.')';
            }
            else {
                $query_where = $sec_query_operator.$attr['name'].' LIKE "'.$like_prefix.$db->escape_string($core->input['filters'][$filteritem]).'%"';
            }
        }

        return $query_where;
    }

    public function add_filter($newfilter) {
        if(!isset($newfilter['parse'], $newfilter['process']) && !is_empty($newfilter['parse'], $newfilter['process'])) {
            return false;
        }

        $parse_items = array('filters' => 'filter', 'overwriteField', 'fieldsSequence');
        foreach($parse_items as $realkey => $addkey) {
            if(isset($newfilter['parse'][$addkey])) {
                if(empty($realkey)) {
                    $realkey = $addkey;
                }

                if(isset($this->config['parse'][$realkey])) {
                    if(is_array($newfilter['parse'][$addkey])) {
                        $this->config['parse'][$realkey] = array_merge($this->config['parse'][$realkey], $newfilter['parse'][$addkey]);
                    }
                    else {
                        array_push($this->config['parse'][$realkey], $newfilter['parse'][$addkey]);
                    }
                }
                else {
                    $this->config['parse'][$realkey] = $newfilter['parse'][$addkey];
                }
            }
        }

        if(isset($newfilter['process']['mainTable'])) {
            array_push($this->config['process']['mainTable']['filters'], $newfilter['process']['mainTable']['filter']);
        }
        elseif(isset($newfilter['process']['secTable'])) {
            if(isset($this->config['process']['secTable'][key($newfilter['process']['secTable'])])) {
                $table = key($newfilter['process']['secTable']);
                array_push($this->config['process']['secTable'][$table]['filters'], $newfilter['process']['secTable'][$table]['filter']);
            }
            else {
                array_push($this->config['process']['secTable'], $newfilter['process']['secTable']);
            }
        }
    }

    public function set_matchingrule($rule) {
        global $core;
        $this->matching_rule = $core->sanitize_inputs($rule, array('removetags' => true));
    }

}
?>