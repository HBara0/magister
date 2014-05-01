<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * System logs
 * $module: admin/maintenance
 * $id: logs.php	
 * Last Update: @zaher.reda 	August 10, 2009 | 10:59 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canReadLogs'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = 'date DESC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();

    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT l.*, l.data AS information, u.username 
						FROM ".Tprefix."logs l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid) 
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
    if($db->num_rows($query) > 0) {
        while($log_entry = $db->fetch_array($query)) {
            $class = alt_row($class);

            if(empty($log_entry['module'])) {
                $log_entry['module'] = $lang->na;
            }

            if(empty($log_entry['username'])) {
                $log_entry['username'] = $lang->guest;
            }
            $logs_list .= "<tr class='{$class}'>";
            $logs_list .= "<td>{$log_entry[username]}</td><td>".date($core->settings['dateformat']." ".$core->settings['timeformat'], $log_entry['date'])."</td><td>{$log_entry['module']}</td><td>".$log->explain($log_entry)."</td><td>{$log_entry[ipaddress]}</td>";
            $logs_list .= '</tr>';
        }

        $multipages = new Multipages('logs', $core->settings['itemsperlist']);
        $logs_list .= "<tr><td colspan='4'>".$multipages->parse_multipages()."</td><td style='text-align:right;'><a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='../images/xls.gif' alt='{$lang->exportexcel}' border='0' /></a></td></tr>";
    }
    else {
        $logs_list = "<tr><td align='center' colspan='5'>{$lang->na}</td></tr>";
    }
    eval("\$logspage = \"".$template->get('admin_maintenance_readlogs')."\";");
    output_page($logspage);
}
else {
    if($core->input['action'] == 'exportexcel') {
        $sort_query = 'date DESC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby'].' '.$core->input['order'];
        }

        $query = $db->query("SELECT u.username, l.date, l.module, l.action, l.data, l.ipAddress 
						FROM ".Tprefix."logs l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid) 
						ORDER BY {$sort_query}");

        if($db->num_rows($query) > 0) {
            $logs[0]['username'] = $lang->username;
            $logs[0]['date'] = $lang->datetime;
            $logs[0]['module'] = $lang->module;
            $logs[0]['data'] = $lang->information;
            $logs[0]['ipAddress'] = $lang->ipaddress;

            $i = 1;
            while($logs[$i] = $db->fetch_assoc($query)) {
                $logs[$i]['date'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $logs[$i]['date']);
                $logs[$i]['data'] = $log->explain($logs[$i]);
                unset($logs[$i]['action']);
                $i++;
            }

            $excelfile = new Excel('array', $logs);
        }
    }
}
/*
  function log_information($log) {
  global $db, $core, $lang;

  $data = unserialize(stripslashes($log['data']));

  list($module, $section) = explode('/', $log['module']);
  $identifier = 'log_'.$module.'_'.$section.'_'.$log['action'];

  switch($identifier) {
  case 'log_reporting_fillreport_save_report':
  case 'log_reporting_fillreport_save_keycustomers':
  case 'log_reporting_fillreport_save_marketreport':
  case 'log_reporting_fillreport_save_productsactivity':
  $report = $db->fetch_assoc($db->query("SELECT a.name AS affiliatename, e.companyName, r.year, r.quarter
  FROM ".Tprefix."affiliates a, ".Tprefix."entities e, ".Tprefix."reports r
  WHERE a.affid=r.affid AND e.eid=r.spid AND r.rid='".$db->escape_string($data[0])."'"));
  $data[0] = 'Q'.$report['quarter'].' '.$report['year'].' - '.$report['companyName'].'/'.$report['affiliatename'];
  break;
  case 'log_reporting_list_do_moderation':
  case 'log_reporting_preview_approve':
  if(is_array($data[0])) {
  if(count($data[0]) > 1) {
  if(isset($data[1])) {
  $identifier .= '_'.$data[1];
  }
  $identifier .= '_multiple';
  unset($data[0]);
  }
  else
  {
  $report = $db->fetch_assoc($db->query("SELECT a.name AS affiliatename, e.companyName, r.year, r.quarter
  FROM ".Tprefix."affiliates a, ".Tprefix."entities e, ".Tprefix."reports r
  WHERE a.affid=r.affid AND e.eid=r.spid AND r.rid='".$db->escape_string($data[0][0])."'"));
  $data[0] = 'Q'.$report['quarter'].' '.$report['year'].' - '.$report['companyName'].'/'.$report['affiliatename'];
  if(isset($data[1])) {
  $identifier .= '_'.$data[1];
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
  $data[0] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($data[0])."'"), 'companyName');
  break;
  case 'log_crm_fillvisitreport_do_add_fillvisitreport':
  $visit_report = $db->fetch_assoc($db->query("SELECT c.companyName, vr.date FROM ".Tprefix."visitreports vr LEFT JOIN ".Tprefix."entities c ON (c.eid=vr.cid) WHERE vr.vrid='".$db->escape_string($data[0])."'"));
  $data[0] = $visit_report['companyName'].' ('.date($core->settings['dateformat'], $visit_report['date']).')';
  break;
  case 'log_N_A_do_login':
  if($data[1] == 0){
  $identifier .= '_failed';
  }
  break;
  }

  if(isset($lang->$identifier)) {
  array_unshift($data, $lang->$identifier);
  $information = call_user_func_array(array($lang, 'sprint'), $data);
  }
  else
  {
  $information = $log['module'].' - '.$log['action'].': '.implode(',', $data);
  }

  return $information;
  } */
?>