<?php
$module['name'] = 'assets';
$module['title'] = $lang->assetsmodule;
$module['homepage'] = 'assets';
$module['globalpermission'] = 'assets_canTrack';
$module['menu'] = array('file' 		  => array('assets','manageassets','managetrackers'),
						'title'		 => array('titletracking','assetspage','trackerspage'),
						'permission'	=> array('assets_canTrack','assets_canManageAssets','assets_canManageAssets')
						);

?>