<?php
$module['name'] = 'hr';
$module['title'] = $lang->humanresources;
$module['homepage'] = 'employeeslist';
$module['globalpermission'] = 'canUseHR';
$module['menu'] = array('file' => array('employeeslist', 'manageholidays', 'holidayslist', 'managejobopportunity', 'listjobopportunities'),
        'title' => array('employeeslist', 'manageholidays', 'holidayslist', 'managejobopportunity', 'listjobopportunities'),
        'permission' => array('canViewAllEmp', 'canUseHR', 'canUseHR', 'hr_canCreateJobOpport', 'hr_canCreateJobOpport')
);
?>