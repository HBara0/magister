<?php
$module['name'] = 'hr';
$module['title'] = $lang->humanresources;
$module['homepage'] = 'employeeslist';
$module['globalpermission'] = 'canUseHR';
$module['menu'] = array('file' 		  => array('employeeslist', 'manageholidays', 'holidayslist'),
						'title'		 => array('employeeslist', 'manageholidays', 'holidayslist'),
						'permission'	=> array('canViewAllEmp', 'canUseHR', 'canUseHR')
						);

?>