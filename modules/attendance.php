<?php
$module['name'] = 'attendance';
$module['title'] = $lang->attendance;
$module['homepage'] = 'requestleave';
$module['globalpermission'] = 'canUseAttendance';
$module['menu'] = array('file' 		  => array('requestleave', 'listleaves', 'leavesstats', 'list', 'generatereport', 'holidays', 'importattendance'),
						'title'		 => array('requestleave', 'listofleaves', 'viewbalances', 'attendancelist', 'attendancereport', 'currentholidays', 'importattendance'),
						'permission'	=> array('attendance_canRequestLeave', 'attendance_canListLeaves', 'canUseAttendance', 'attendance_canListAttendance', 'canUseAttendance', 'attendance_canGenerateReport', 'attendance_canImport')
						);

?>