<?php
$module['name'] = 'hr';
$module['title'] = $lang->humanresources;
$module['homepage'] = 'employeeslist';
$module['globalpermission'] = 'canUseHR';
$module['menu'] = array('file' => array('employeeslist', 'manageholidays', 'holidayslist', 'managejobopportunity', 'listjobs'),
        'title' => array('employeeslist', 'manageholidays', 'holidayslist', 'managejobopportunity', 'listjobs'),
        'permission' => array('canViewAllEmp', 'canUseHR', 'canUseHR', 'canUseHR', 'canUseHR')
);
?>