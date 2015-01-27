<?php
$module['name'] = 'attendance';
$module['title'] = $lang->attendance;
$module['homepage'] = 'requestleave';
$module['globalpermission'] = 'canUseAttendance';
$module['menu'] = array('file' => array('requestleave', 'listleaves', 'leavesstats', 'attendancerecords', 'generatereport', 'holidays', 'importattendance', 'balancesvalidations', 'generatexpensesreport'),
        'title' => array('requestleave', 'listofleaves', 'viewbalances', 'attendancelist', 'attendancereport', 'currentholidays', 'importattendance', 'validatebalances', 'expensesreport'),
        'permission' => array('attendance_canRequestLeave', 'attendance_canListLeaves', 'canUseAttendance', 'attendance_canListAttendance', 'canUseAttendance', 'attendance_canGenerateReport', 'attendance_canImport', 'canUseHR', 'attendance_canGenerateExpReport')
);
?>