<?php
$module['name'] = 'reputation';
$module['title'] = $lang->reputation;
$module['homepage'] = 'reputationslist';
$module['globalpermission'] = 'canUseReputation';
$module['menu'] = array('file' 		  => array('addreputation', 'reputationslist'),
						'title'		 => array('addreputation', 'reputationslist'),
						'permission'	=> array('reputation_canAddLink', 'canUseReputation')
						);
?>