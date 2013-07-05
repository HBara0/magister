<?php
$module['name'] = 'assets';
$module['title'] = $lang->assetsmodule;
$module['homepage'] = 'assets';
$module['globalpermission'] = 'assets_canTrack';
$module['menu'] = array('file' 		  => array('assets','assignassets','listuser','manageassets','listassets','managetrackers','listtrackers'),
						'title'		 => array('titletracking','assignassetstitle','listusertitle','assetspage','assetslist','trackerspage','trackerslist'),
						'permission'	=> array('assets_canTrack','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets')
						);

?>