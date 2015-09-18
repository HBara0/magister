<?php
$module['name'] = 'attendance';
$module['title'] = $lang->attendance;
$module['homepage'] = 'requestleave';
$module['globalpermission'] = 'canUseAttendance';
$module['menu'] = array('file' => array('requestleave', 'listleaves', 'leavesstats', 'attendancerecords', 'generatereport', 'attendancelog', 'holidays', 'importattendance', 'balancesvalidations', 'reinitializebalances', 'generatexpensesreport'),
        'title' => array('requestleave', 'listofleaves', 'viewbalances', 'attendancerecords', 'attendancereport', 'attendancelog', 'currentholidays', 'importattendance', 'validatebalances', 'reinitializebalances', 'expensesreport'),
        'permission' => array('attendance_canRequestLeave', 'attendance_canListLeaves', 'canUseAttendance', 'attendance_canListAttendance', 'canUseAttendance', 'canUseAttendance', 'attendance_canGenerateReport', 'attendance_canImport', 'canUseHR', 'canUseHR', 'attendance_canGenerateExpReport')
);
?>