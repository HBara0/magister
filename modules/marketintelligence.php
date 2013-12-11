<?php
$module['name'] = 'marketintelligence';
$module['title'] = $lang->assets;
$module['homepage'] = 'listassets';
$module['globalpermission'] = 'canUseAssets';
$module['menu'] = array('file' => array('assignassets', 'listusers', 'manageassets', 'listassets'),
		'title' => array('assignassetstitle', 'listusertitle', 'addassets', 'listasset'),
		'permission' => array('assets_canManageAssets', 'assets_canManageAssets', 'assets_canManageAssets', 'assets_canManageAssets')
);
?>