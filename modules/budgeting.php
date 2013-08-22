<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budget;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' 		  => array('create','fillbudget','generate'),
						'title'		 => array('create','fillbudget','generate'),
						'permission'	=> array('budgeting_canFillBudget ','budgeting_canFillBudget ','budgeting_canFillBudget')
						);

?>
