<?php
$module['name'] = 'assets';
$module['title'] = $lang->assetsmodule;
$module['homepage'] = 'assets';
$module['globalpermission'] = 'canUseAssets';
$module['menu'] = array('file' 		  => array('assets&action=list','assets&action=map','manageassets','managetrackers','assignassets'),
						'title'		 => array('assetstrackpage','assetstrackmap','assetsmanagepage','trackersmanagepage','assignassetspage'),
						'permission'	=> array('assets_canTrackAssets','assets_canTrackAssets','assets_canManageAssets','assets_canManageAssets','assets_canManageAssets')
						);

?>