<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * 
 * Validation for users balances
 * $id: balancesvalidations.php
 * Created:        @zaher.reda    Nov 30, 2012 | 6:38:41 PM
 * Last Update:    @zaher.reda    Nov 30, 2012 | 6:38:41 PM
 */

if(!isset($core->input['identifier'])) {
	$identifier = substr(md5(uniqid(microtime())), 1, 10);
}
else {
	$identifier = $core->input['identifier'];
}

$session->name_phpsession(COOKIE_PREFIX.'fillvisitreport'.$identifier);
$session->start_phpsession();

if(!$core->input['action']) {
	if(!isset($core->input['affid']) || empty($core->input['affid'])) {
		$affid = $core->user['mainaffiliate'];
		if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
			if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
				$affid = $core->user['mainaffiliate'];
				if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
					$affid = $core->user['hraffids'][current($core->user['hraffids'])];
				}
			}
			else {
				error($lang->sectionnopermission);
				exit;
			}
		}
	}
	else {
		$affid = $core->input['affid'];
		if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
			if(!in_array($core->input['affid'], $core->user['hraffids'])) {
				$affid = $core->user['mainaffiliate'];
				if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
					error($lang->sectionnopermission);
					exit;
				}
			}
		}
	}

	/* Parse affiliates select list - START */
	if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
		$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
	}
	else {
		if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 1) {
			$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
		}
	}

	if(is_array($affiliates)) {
		$affid_field = parse_selectlist('affid', 1, $affiliates, $affid, 0);
	}
	else {
		$affid_field = '-';
	}
	/* Parse affiliates select list - END */

	/* Parse periods select list - START */
	$period_years = array();
	$cache = new Cache();
	$periods_query = $db->query('SELECT DISTINCT(CONCAT(CAST(periodStart AS CHAR), "-", CAST(periodEnd AS CHAR))) AS period, periodStart, periodEnd, FROM_UNIXTIME(periodStart, "%Y") AS year FROM '.Tprefix.'leavesstats ORDER BY periodStart ASC, periodEnd ASC');
	while($period = $db->fetch_assoc($periods_query)) {
		if(!$cache->iscached('periodYears', $period['year'])) {
			$cache->add('periodYears', $period['year'], $period['year']);
			$periods[$period['year']] = '&raquo; '.$period['year'];
		}
		$periods[$period['period']] = ' '.date($core->settings['dateformat'], $period['periodStart']).' - '.date($core->settings['dateformat'], $period['periodEnd']);
	}
	$periods_list = parse_selectlist('period', 1, $periods, '', 0, '', array('disabledItems' => $cache->data['periodYears']));
	$periods[0] = '';
	$prevperiods_list = parse_selectlist('prevPeriod', 1, $periods, '', 0, '', array('disabledItems' => $cache->data['periodYears']));
	/* Parse periods select list - END */

	/* Parse types list - START */
	$query = $db->query("SELECT * FROM ".Tprefix."leavetypes WHERE countWith=0 AND noBalance=0 ORDER BY name ASC");
	while($type = $db->fetch_assoc($query)) {
		if(!empty($lang->{$type['name']})) {
			$type['title'] = $lang->{$type['name']};
		}
		if(!empty($type['description'])) {
			$type['description'] = ' ('.$type['description'].')';
		}
		$leave_types[$type['ltid']] = $type['title'].$type['description'];
	}

	$types_list = parse_selectlist('type', 1, $leave_types, $core->input['type'], 0);
	/* Parse types list - END */

	eval("\$validation_page = \"".$template->get('attendance_balancevalidation')."\";");
	output_page($validation_page);
}
else {  //days taken must = actual taken
	if($core->input['action'] == 'preview' || $core->input['action'] == 'fixbalances') {
		
		if($session->isset_phpsession('balancevalidations_'.$identifier)) {
			$options = unserialize($session->get_phpsession('balancevalidations_'.$identifier));
		}
		else {
			$options = $core->input;
		}

		if(is_empty($options['period'])) {
			error($lang->fillrequiredfields);
		}

		if($options['period'] == $options['prevPeriod']) {
			redirect('index.php?module=attendance/balancesvalidations');
		}

		if($core->input['action'] != 'fixbalances') {
			list($options['periodStart'], $options['periodEnd']) = explode('-', $options['period']);
			list($options['prevPeriodStart'], $options['prevPeriodEnd']) = explode('-', $options['prevPeriod']);

			$session->set_phpsession(array('balancevalidations_'.$identifier => serialize($options)));
		}

		/* Validate attribute values at once - START */
		$validation_items = array('periodStart', 'periodEnd', 'prevPeriodStart', 'prevPeriodEnd', 'type');
		foreach($validation_items as $item) {
			$options[$item] = intval($options[$item]);
		}
		/* Validate attribute values at once - END */

		$query = $db->query("SELECT l.*, lt.isWholeDay 
			FROM ".Tprefix."leaves l 
			JOIN leavetypes lt ON (lt.ltid=l.type) 
			WHERE ((fromDate BETWEEN {$options[periodStart]} AND {$options[periodEnd]}) 
			OR (toDate BETWEEN {$options[periodStart]} AND {$options[periodEnd]})) 
			AND (type={$options[type]} || countWith={$options[type]})
			AND l.uid IN (SELECT uid FROM ".Tprefix."affiliatedemployees WHERE isMain=1 AND affid=".$options['affid'].")
			ORDER BY uid ASC, fromDate ASC");

		if($core->input['action'] != 'fixbalances') {
			$columns = array('displayName' => $lang->employeename,
					'daysTaken' => $lang->taken,
					'actualTaken' => $lang->actual,
					'balance' => $lang->balance,
					'actbalance' => $lang->actbalance,
					'entitledFor' => $lang->entitledfor,
					'remainPrevYear' => $lang->remainprevyear,
					'remainPrevYearAct' => $lang->remainprevyearact);

			$tableheader .= '<tr>';
			foreach($columns as $key => $column) {
				if(empty($column)) {
					$column = $key;
				}
				$tableheader .= '<th>'.$column.'</th>';
			}
			$tableheader .= '</tr>';
		}
		while($leave = $db->fetch_assoc($query)) {
			if($leave['toDate'] > $options['periodEnd']) {
				$leave['toDate'] = $options['periodEnd'];
			}

			if($leave['fromDate'] < $options['periodStart']) {
				$leave['fromDate'] = $options['periodStart'];
			}
			
			if($db->fetch_field($db->query("SELECT COUNT(*) AS count FROM leavesapproval WHERE isApproved=0 AND lid={$leave[lid]}"), 'count') == 0) {
				$leaves_counts[$leave['uid']] += count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $leave['isWholeDay']);
			}
		}
		 
		if(!is_array($leaves_counts)) {
			redirect('index.php?module=attendance/balancesvalidations');
		}
		$query2 = $db->query("SELECT lt.*, u.displayName
						FROM ".Tprefix."leavesstats lt 
						JOIN users u ON (u.uid=lt.uid)
						WHERE ltid={$options[type]} AND periodStart={$options[periodStart]} AND periodEnd={$options[periodEnd]} AND lt.uid IN (".implode(', ', array_keys($leaves_counts)).")");

		while($leavestat = $db->fetch_assoc($query2)) {
			$cellstyle = '';
			if($leavestat['daysTaken'] < $leaves_counts[$leavestat['uid']]) {
				$cellstyle['daysTaken'] = ' style="color:red;"';
				if($core->input['fixdaysTaken'] == 1) {
					$db->update_query('leavesstats', array('daysTaken' => $leaves_counts[$leavestat['uid']]), 'lsid='.$leavestat['lsid']);
				}
			}
			elseif($leavestat['daysTaken'] > $leaves_counts[$leavestat['uid']]) {
				$cellstyle['daysTaken'] = ' style="color:orange;"';
				if($core->input['fixdaysTaken'] == 1) {
					$db->update_query('leavesstats', array('daysTaken' => $leaves_counts[$leavestat['uid']]), 'lsid='.$leavestat['lsid']);
				}
			}

			if(!empty($options['prevPeriodStart'])) {
				$prevbalance = $db->fetch_assoc($db->query("SELECT lt.*, u.displayName
									FROM ".Tprefix."leavesstats lt JOIN users u ON (u.uid=lt.uid)
									WHERE ltid={$options[type]} AND periodStart={$options[prevPeriodStart]} AND periodEnd={$options[prevPeriodEnd]} AND lt.uid={$leavestat[uid]}"));

				if(empty($prevbalance) && !isset($prevbalance)) {
					$prevbalance['canTake'] = $prevbalance['daysTaken'] = 0;
				}
			}
			else {
				$prevbalance['canTake'] = $prevbalance['daysTaken'] = 0;
			}
 
			if($leavestat['remainPrevYear'] < ($prevbalance['canTake'] - $prevbalance['daysTaken'])) { 
				$cellstyle['remainPrevYear'] = ' style="color:red;"';
				if($core->input['fixremainPrevYear'] == 1) {
					$db->update_query('leavesstats', array('remainPrevYear' => ($prevbalance['canTake'] - $prevbalance['daysTaken'])), 'lsid='.$leavestat['lsid']);
			
					
				}
			} 
			elseif($leavestat['remainPrevYear'] > ($prevbalance['canTake'] - $prevbalance['daysTaken'])) {
				$cellstyle['remainPrevYear'] = ' style="color:orange;"';
				if($core->input['fixremainPrevYear'] == 1) {
					$db->update_query('leavesstats', array('remainPrevYear' => ($prevbalance['canTake'] - $prevbalance['daysTaken'])), 'lsid='.$leavestat['lsid']);
				}
			}

			if($core->input['action'] != 'fixbalances') {
				$leavestat['actualTaken'] = $leaves_counts[$leavestat['uid']];
				$leavestat['balance'] = ($leavestat['canTake'] - $leavestat['daysTaken']);
				$leavestat['actbalance'] = ($leavestat['canTake'] - $leaves_counts[$leavestat['uid']]);
				$leavestat['remainPrevYearAct'] = ($prevbalance['canTake'] - $prevbalance['daysTaken']);

				$tablerows .= '<tr>';
				foreach($columns as $key => $column) {
					if(!isset($cellstyle[$key])) {
						$cellstyle[$key] = '';
					}
					$tablerows .= '<td'.$cellstyle[$key].'>'.$leavestat[$key].'</td>';
				}

				//$rows .= '<td>'.$leavestat['displayName'].'</td>1
				//<td>'.$leavestat['daysTaken'].'</td>2
				//<td'.$cellstyle['taken'].'>'.$leaves_counts[$leavestat['uid']].'</td>3
				//<td>'.($leavestat['canTake']-$leavestat['daysTaken']).'</td>4
				//
					//<td>'.($leavestat['canTake']-$leaves_counts[$leavestat['uid']]).'</td>5
				//<td>'.$leavestat['entitledFor'].'</td>
				//<td>'.$leavestat['remainPrevYear'].'</td>6
				//<td'.$cellstyle['prevyear'].'>'.($prevbalance['canTake']-$prevbalance['daysTaken']).'</td>7';
				$tablerows .= '</tr>';
			}
			else {
				$session->destroy_phpsession();
				redirect('index.php?module=attendance/balancesvalidations', 5, $lang->leavesuccessfullyapproved);
			}
		}
	}

	eval("\$preview_page = \"".$template->get('attendance_balancevalidation_preview')."\";");
	output_page($preview_page);
}
?>
