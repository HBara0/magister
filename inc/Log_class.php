<?php

class Log {

    private $lang_loaded = false;

    public function __construct() {
        
    }

    public function record() {
        global $db, $core;

        $data = func_get_args();

        if (count($data) == 1 && is_array($data[0])) {
            $data = $data[0];
        }

        if (!is_array($data)) {
            $data = array($data);
        }

        if (!isset($core->input['module'])) {
            preg_match("/module=([A-Za-z\/0-9]+)/i", $_SERVER['QUERY_STRING'], $query_string);
            $core->input['module'] = $query_string[1];
        }

        if (!isset($core->input['action'])) {
            $query_string = '';
            preg_match("/action=([A-Za-z\/0-9]+)/i", $_SERVER['QUERY_STRING'], $query_string);
            if (is_array($query_string) && !empty($query_string)) {
                $core->input['action'] = $query_string[1];
            }
        }

        $log_entry = array(
            'uid' => $core->user['uid'],
            'ipaddress' => $db->escape_string(userip()),
            'date' => TIME_NOW,
            'module' => $db->escape_string($core->input['module']),
            'action' => $db->escape_string($core->input['action']),
            'data' => $db->escape_string(@serialize($data))
        );

        $db->insert_query('logs', $log_entry);
    }

    public function explain($log) {
        global $db, $core, $lang;

        if ($this->lang_loaded === false) {
            $check_func = create_function('$data', '$found = false; foreach($data as $val) { if(strstr($val, "logs.lang.php")) { $found = true; } } return $found;');
            if (!$check_func(get_included_files())) {
                $lang->load('logs');
                $this->lang_loaded = true;
            }
            else {
                $this->lang_loaded = true;
            }
        }

        $data = unserialize(stripslashes($log['data']));

        list($module, $section) = explode('/', $log['module']);
        $identifier = 'log_' . $module . '_' . $section . '_' . $log['action'];

        switch ($identifier) {
            case 'log_reporting_fillreport_save_report':
            case 'log_reporting_fillreport_save_keycustomers':
            case 'log_reporting_fillreport_save_marketreport':
            case 'log_reporting_fillreport_save_productsactivity':
                $report = $db->fetch_assoc($db->query("SELECT a.name AS affiliatename, e.companyName, r.year, r.quarter 
									FROM " . Tprefix . "affiliates a, " . Tprefix . "entities e, " . Tprefix . "reports r
									WHERE a.affid=r.affid AND e.eid=r.spid AND r.rid='" . $db->escape_string($data[0]) . "'"));
                $data[0] = 'Q' . $report['quarter'] . ' ' . $report['year'] . ' - ' . $report['companyName'] . '/' . $report['affiliatename'];
                break;
            case 'log_reporting_list_do_moderation':
            case 'log_reporting_preview_approve':
                if (is_array($data[0])) {
                    if (count($data[0]) > 1) {
                        if (isset($data[1])) {
                            $identifier .= '_' . $data[1];
                        }
                        $identifier .= '_multiple';
                        unset($data[0]);
                    }
                    else {
                        $report = $db->fetch_assoc($db->query("SELECT a.name AS affiliatename, e.companyName, r.year, r.quarter 
										FROM " . Tprefix . "affiliates a, " . Tprefix . "entities e, " . Tprefix . "reports r
										WHERE a.affid=r.affid AND e.eid=r.spid AND r.rid='" . $db->escape_string($data[0][0]) . "'"));
                        $data[0] = 'Q' . $report['quarter'] . ' ' . $report['year'] . ' - ' . $report['companyName'] . '/' . $report['affiliatename'];
                        if (isset($data[1])) {
                            $identifier .= '_' . $data[1];
                        }
                    }
                    unset($data[1]);
                }
                break;
            case 'log_reporting_fillreport_save_newcustomer':
            case 'log_contents_addentities_do_perform_addentities':
            case 'log_entities_edit_do_perform_edit':
            case 'log_entities_add_do_perform_add':
            case 'log_entities_edit_do_approve':
                $data[0] = $db->fetch_field($db->query("SELECT companyName FROM " . Tprefix . "entities WHERE eid='" . $db->escape_string($data[0]) . "'"), 'companyName');
                break;
            case 'log_crm_fillvisitreport_do_add_fillvisitreport':
                $visit_report = $db->fetch_assoc($db->query("SELECT c.companyName, vr.date FROM " . Tprefix . "visitreports vr LEFT JOIN " . Tprefix . "entities c ON (c.eid=vr.cid) WHERE vr.vrid='" . $db->escape_string($data[0]) . "'"));
                $data[0] = $visit_report['companyName'] . ' (' . date($core->settings['dateformat'], $visit_report['date']) . ')';
                break;
            case 'log_N_A_do_login':
                if ($data[1] == 0) {
                    $identifier .= '_failed';
                }
                break;
        }

        if (isset($lang->$identifier)) {
            array_unshift($data, $lang->$identifier);
            $information = call_user_func_array(array($lang, 'sprint'), $data);
        }
        else {
            $information = $log['module'] . ' - ' . $log['action'] . ': ' . implode(',', $data);
        }

        return $information;
    }

}

?>