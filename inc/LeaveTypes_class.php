<?php
/*
 * Copyright � 2013 Orkila International Offshore, All Rights Reserved
 *
 * Leave Types Class
 * $id: Leavetypes_class.php
 * Created:        @tony.assaad    May 29, 2013 | 3:39:18 PM
 * Last Update:    @tony.assaad    May 29, 2013 | 3:39:18 PM
 */

class LeaveTypes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ltid';
    const TABLE_NAME = 'leavetypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'ltid, name, title';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function has_expenses($id = '') {
        global $db;

        if(!empty($this->data['ltid']) && empty($id)) {
            $id = $this->data['ltid'];
        }

        if(value_exists('attendance_leavetypes_expenses', 'ltid', $db->escape_string($id))) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_expenses($id = '') {
        global $db;

        if(!empty($this->data['ltid']) && empty($id)) {
            $id = $this->data['ltid'];
        }

        $leavetypeexp_query = $db->query('SELECT *
                                        FROM '.Tprefix.'attendance_leavetypes_expenses alte
                                        JOIN '.Tprefix.'attendance_leaveexptypes alet ON (alet.aletid=alte.aletid)
                                        WHERE ltid='.$db->escape_string($id).' ORDER BY hasComments DESC');
        if($db->num_rows($leavetypeexp_query) > 0) {
            while($leavetype_expense = $db->fetch_assoc($leavetypeexp_query)) {
                $leavetypeexpenses[$leavetype_expense['alteid']] = $leavetype_expense;
            }
            if(is_array($leavetypeexpenses)) {
                return $leavetypeexpenses;
            }
            return false;
        }
        return false;
    }

    public function parse_expensesfield(array $expensestype) {
        global $db, $template;
        if($expensestype['isRequired'] == 1) {
            $expenses_output_required = '<span class="red_text">*</span>';
            $expenses_output_requiredattr = ' required="required"';
        }
        /* parsing comments fields */
        if(isset($lang->{$expensestype['commentsTitleLangVar']})) {
            $expensestype['commentsTitle'] = $lang->{$expensestype['commentsTitleLangVar']};
        }

        if($expensestype['hasComments'] == 1) {
            if($expensestype['requireComments'] == 1) {
                $expenses_output_required_comments = '<span class="red_text">*</span>';
                $expenses_output_comments_requiredattr = ' required="required"';
            }
            $expenses_output_comments_field = '<div style="display:block; padding:5px; text-align:left; width:38%; vertical-align: top;">'.$expensestype['commentsTitle'].$expenses_output_required_comments.'<textarea cols="25" rows="1" id="expenses_['.$expensestype['alteid'].'][description]" name="leaveexpenses['.$expensestype['alteid'].'][description]" '.$expenses_output_comments_requiredattr.'>'.$expensestype['description'].'</textarea></div>';
        }

        if(isset($lang->{$expensestype['name']})) {
            $expensestype['title'] = $lang->{$expensestype['name']};
        }

        eval("\$requestleaveexpenses = \"".$template->get('attendance_requestleave_expsection_fields')."\";");
        return $requestleaveexpenses;
    }

    public static function get_leavetypes($filters = '') {
        global $db;

        if(!empty($filters)) {
            $query_where = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT ltid FROM '.Tprefix.'leavetypes'.$query_where);
        if($db->num_rows($query) > 0) {
            while($leavetype = $db->fetch_assoc($query)) {
                $leavetypes[$leavetype['ltid']] = new LeaveTypes($leavetype['ltid']);
            }

            return $leavetypes;
        }
        return false;
    }

    public function get_additonalfields() {
        return unserialize($this->data['additionalFields']);
    }

    public function parse_additonalfields(array $additional_settings = array()) {
        global $lang;

        $additional_fields = $this->get_additonalfields();
        if(is_array($additional_fields)) {
            foreach($additional_fields as $key => $field) {
                $parsed_fields .= $this->parse_additonalfield($key, $field);
            }
        }

        if(!empty($this->noteLangVar)) {
            $parsed_fields .= '<div class="ui-state-highlight ui-corner-all" style="padding: 5px;"><p>'.$lang->{$this->noteLangVar}.'</p></div><br />';
        }
        return $parsed_fields;
    }

    public function parse_additonalfield($attribute, $field_settings, array $additional_settings = array()) {
        global $db, $core, $lang, $leave;
        $field = '';

        switch($field_settings['type']) {
            case 'inline-search':
                $identifier = uniqid(TIME_NOW);

                if($attribute == 'cid') {
                    $search_for = 'customer';
                }
                elseif($attribute == 'destinationCity') {
                    $search_for = 'destinationcity';
                }
                elseif($attribute == 'sourceCity') {
                    $search_for = 'sourcecity';
                }
                elseif($attribute == 'city') {
                    $search_for = 'cities';
                }
                elseif($attribute == 'spid') {
                    $search_for = 'supplier';
                }
                $field = '<input type="text" id="'.$search_for.'_'.$identifier.'_autocomplete" value="'.$field_settings['value_attribute_value'].'" required="required"/><input type="hidden" size = "3" id = "'.$search_for.'_'.$identifier.'_id_output" value = "'.$field_settings['key_attribute_value'].'" disabled /><input type = "hidden" value="'.$field_settings['key_attribute_value'].'" id="'.$search_for.'_'.$identifier.'_id" name="'.$attribute.'" />';
                break;
            case 'select':
                if($field_settings['datasource'] == 'db') {
                    if(isset($field_settings['table'], $field_settings['attributes'])) {
                        if(isset($field_settings['where'])) {
                            if($field_settings['affid_validation'] == true) {
                                if(empty($field_settings['uid'])) {
                                    $field_settings['uid'] = $core->input['uid'];
                                }
                                $field_settings['affids'] = implode(', ', get_specificdata('affiliatedemployees', array('affid'), 'affid', 'affid', '', 0, 'uid='.$db->escape_string($field_settings['uid'])));
                            }
                            /* The below might not function */
                            if(isset($leave['fromDate_formatted'])) {
                                $leave['fromDate'] = $leave['fromDate_output'];
                            }
                            $leave['fromDate'] = strtotime($leave['fromDate']);
                            if(isset($leave['toDate_formatted'])) {
                                $leave['toDate'] = $leave['toDate_output'];
                            }
                            $leave['toDate'] = strtotime($leave['toDate']);
                            /* The above might not function */
                            eval("\$field_settings[where] = \"".$field_settings['where']."\";");
                        }

                        $data = get_specificdata($field_settings['table'], $field_settings['attributes'], $field_settings['key_attribute'], $field_settings['value_attribute'], array('by' => $field_settings['value_attribute'], 'sort' => 'ASC'), 0, $field_settings['where']);

                        if(is_array($data)) {
                            $field = parse_selectlist($attribute, 0, $data, $field_settings['key_attribute_value'], $field_settings['mulitpleselect'], '', array('required' => true, 'placeholder' => ' '));
                        }
                        else {
                            $field = '<span class="red_text">'.$lang->{$field_settings['errorlang_nodata']}.'</span>';
                        }
                    }
                    else {
                        break;
                    }
                }
                elseif($field_settings['datasource'] == 'function') {
                    unset($field_settings['key_attribute_value'], $field_settings['type'], $field_settings['table'], $field_settings['attributes']);
                    if(method_exists($this, $field_settings['functionname'])) {
                        /* call the sgment function to get  the segment for the on behalf user */
                        $data = $this->{$field_settings['functionname']}(new Users($core->input['uid']));
                    }

                    if(is_array($data)) {
                        $field = parse_selectlist($attribute, 0, $data, $field_settings['key_attribute_value'], $field_settings['mulitpleselect'], '', array('required' => false));
                    }
                }
                break;
            default: break;
        }

        if(!empty($field)) {
            if(isset($field_settings['titlelangvar'])) {
                $field = '<br/><br/>'
                        .'<div style="display:inline-block; width:19%;">'.$lang->{$field_settings['titlelangvar']}.'</div>'
                        .'<div style = "display:inline-block; width:75%;">'.$field.'</div>';
            }
        }
        return $field;
    }

    private function parse_segments_byuser(Users $user_obj = null) {
        global $core;

        if($this->data['isBusiness'] == 1) {
            /* only we get the segments of  selected user (core user) */
            if(!is_object($user_obj)) {
                $user_obj = $core->user_obj;
            }
            $segments = $user_obj->get_segments();
            if(is_array($segments)) {
                foreach($segments as $key => $segment) {
                    $usersegment_data[$segment->get()['psid']] = $segment->get()['title'];
                }
                return $usersegment_data;
            }
            return false;
        }
        return false;
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

}
?>
