<?php
$module['name'] = 'attendance';
$module['title'] = $lang->attendance;
$module['homepage'] = 'requestleave';
$module['globalpermission'] = 'canUseAttendance';
$module['menu'] = array('file' => array('requestleave', 'listleaves', 'leavesstats', 'attendancerecords', 'attendancereport', 'attendancelog', 'holidays', 'importattendance', 'balancesvalidations', 'reinitializebalances', 'generatexpensesreport', 'listaddleavedays'),
        'title' => array('requestleave', 'listleaves', 'viewbalances', 'attendancerecords', 'attendancereport', 'attendancelog', 'holidays', 'importattendance', 'balancesvalidations', 'reinitializebalances', 'generatexpensesreport', 'listaddleavedays'),
        'permission' => array('attendance_canRequestLeave', 'attendance_canListLeaves', 'canUseAttendance', 'attendance_canListAttendance', 'canUseAttendance', 'canUseAttendance', 'attendance_canGenerateReport', 'attendance_canImport', 'canUseHR', 'canUseHR', 'attendance_canGenerateExpReport', 'canUseAttendance')
);
?>