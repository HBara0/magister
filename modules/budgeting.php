<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budget;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' 		  => array('create','generatebudget'),
						'title'		 => array('create','generate'),
						'permission'	=> array('budgeting_canFillBudget','budgeting_canFillBudget')
						);

?>
