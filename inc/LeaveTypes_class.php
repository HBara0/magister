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
            $query_select = 'ltid, name, title,title AS name ,description, toApprove';
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

    public function get() {
        return $this->leavetype;
    }

}
?>
