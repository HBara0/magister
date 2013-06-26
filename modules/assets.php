<?php
$module['name'] = 'assets';
$module['title'] = $lang->assetsmodule;
$module['homepage'] = 'assets';
$module['globalpermission'] = 'assets_canTrack';
$module['menu'] = array('file' 		  => array('assets','assignassets','manageassets','listassets','managetrackers','listtrackers'),
						'title'		 => array('titletracking','assignassetstitle','assetspage','assetslist','trackerspage','trackerslist'),
						'permission'	=> array('assets_canTrack','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets')
						);

?>