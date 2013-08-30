<?php
$module['name'] = 'assets';
$module['title'] = $lang->assetsmodule;
$module['homepage'] = 'listassets';
$module['globalpermission'] = 'canUseAssets';
$module['menu'] = array('file' 		  => array('assignassets','listusers','addassets','listassets'),
						'title'		 => array('assignassetstitle','listusertitle','addassets','listasset'),
						'permission'	=> array('assets_canManageAssets','assets_canManageAssets','assets_canManageAssets', 'assets_canManageAssets')
						);

?>