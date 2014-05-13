<?php
/*
 * Copyright � 2013 Orkila International Offshore, All Rights Reserved
 *
 * Leave Types Class
 * $id: Leavetypes_class.php
 * Created:        @tony.assaad    May 29, 2013 | 3:39:18 PM
 * Last Update:    @tony.assaad    May 29, 2013 | 3:39:18 PM
 */

class Leavetypes {
    private $leavetype = array();

    public function __construct($ltid = 0, $simple = true) {
        if(isset($ltid) && !empty($ltid)) {
            $this->leavetype = $this->read($ltid, $simple);
        }
    }

    public function has_expenses($id = '') {
        global $db;

        if(!empty($this->leavetype['ltid']) && empty($id)) {
            $id = $this->leavetype['ltid'];
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

        if(!empty($this->leavetype['ltid']) && empty($id)) {
            $id = $this->leavetype['ltid'];
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

    private function read($id, $simple = true) {
        global $db;
        if(empty($id)) {
            return false;
        }
        $query_select = '*';
        if($simple == true) {
            $query_select = 'ltid, name, title,title AS name ,description,additionalFields,isBusiness,toApprove';
        }
        return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'leavetypes WHERE ltid='.$db->escape_string($id)));
    }

    public static function get_leavetypes($filters = '') {
        global $db;

        if(!empty($filters)) {
            $query_where = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT ltid FROM '.Tprefix.'leavetypes'.$query_where);
        if($db->num_rows($query) > 0) {
            while($leavetype = $db->fetch_assoc($query)) {
                $leavetypes[$leavetype['ltid']] = new Leavetypes($leavetype['ltid']);
            }

            return $leavetypes;
        }
        return false;
    }

    public function get_additonalfields() {
        global $db;
        return unserialize($this->leavetype['additionalFields']);
    }

    public function parse_additonalfields(array $additional_settings = array()) {

        $this->additional_fields = $this->get_additonalfields();
        if(is_array($this->additional_fields)) {
            foreach($this->additional_fields as $key => $field) {
                $this->parsed_fields .= $this->parse_additonalfield($key, $field);
            }
            return $this->parsed_fields;
        }
    }

    private function parse_additonalfield($attribute, $field_settings, array $additional_settings = array()) {
        global $db, $core, $lang, $leave;
        $field = '';

        switch($field_settings['type']) {
            case 'inline-search':
                $identifier = uniqid(TIME_NOW);

                if($attribute == 'cid') {
                    $search_for = 'customer';
                }
                elseif($attribute == 'spid') {
                    $search_for = 'supplier';
                }
                $field = '<input type="text" id="'.$search_for.'_'.$identifier.'_QSearch" value="'.$field_settings['value_attribute_value'].'" required="required"/><input type="text" size="3" id="'.$search_for.'_'.$identifier.'_id_output" value="'.$field_settings['key_attribute_value'].'" disabled /><input type="hidden" value="'.$field_settings['key_attribute_value'].'" id="'.$search_for.'_'.$identifier.'_id" name="'.$attribute.'" /><div id="searchQuickResults_'.$identifier.'" class="searchQuickResults" style="display:none;"></div>';

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

                            if(isset($leave['fromDate_formatted'])) {
                                $leave['fromDate'] = $leave['fromDate_output'];
                            }
                            $leave['fromDate'] = strtotime($leave['fromDate']);
                            if(isset($leave['toDate_formatted'])) {
                                $leave['toDate'] = $leave['toDate_output'];
                            }
                            $leave['toDate'] = strtotime($leave['toDate']);
                            eval("\$field_settings[where] = \"".$field_settings['where']."\";");
                        }

                        $data = get_specificdata($field_settings['table'], $field_settings['attributes'], $field_settings['key_attribute'], $field_settings['value_attribute'], array('by' => $field_settings['value_attribute'], 'sort' => 'ASC'), 0, $field_settings['where']);
                        if(is_array($data)) {
                            $field = parse_selectlist($attribute, 0, $data, $field_settings['key_attribute_value'], $field_settings['mulitpleselect'], '', array('required' => true));
                        }
                        else {
                            $field = '<span class="red_text">'.$lang->{$field_settings['errorlang_nodata']}.'</span>';
                        }
                    }
                    else {
                        break;
                    }
                }
                /*  This option will call the parse segment
                 * function based on the funcntion name passed from the
                 * configuration array
                 *
                 * */
                elseif($field_settings['datasource'] == 'function') {
                    unset($field_settings['key_attribute_value'], $field_settings['type'], $field_settings['table'], $field_settings['attributes']);

                    if(($field_settings['functionname'])) {
                        $data = $this->{$field_settings['functionname']}();
                    }
                    if(is_array($data)) {
                        $field = parse_selectlist($attribute, 0, $data, '', $field_settings['mulitpleselect'], '', array('required' => false));
                    }
                }
                break;
            default: break;
        }

        return $field;
    }

    private function parse_segments_byuser(Users $user = null) {
        global $core;
        if($this->leavetype['isBusiness'] == 1) {
            /* only we get the segments of  selected user (core user) */
            if(!is_object($user)) {
                $user_obj = new Users($core->input['uid']);
            }
            $user_segmentsobjs = $user_obj->get_segments();
            if(is_array($user_segmentsobjs)) {
                foreach($user_segmentsobjs as $key => $user_segmentsobj) {

                    $usersegment_data[$user_segmentsobj->get()['psid']] = $user_segmentsobj->get()['title'];
                }
            }
            return $usersegment_data;
        }
    }

    public function get() {
        return $this->leavetype;
    }

}
?>
