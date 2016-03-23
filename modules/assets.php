<?php
$module['name'] = 'assets';
$module['title'] = $lang->assets;
$module['homepage'] = 'listassets';
$module['globalpermission'] = 'canUseAssets';
$module['menu'] = array('file' => array('assignassets', 'listusers', 'manageassets', 'listassets'),
        'title' => array('assignassets', 'listusers', 'manageassets', 'listasset'),
        'permission' => array('assets_canManageAssets', 'assets_canManageAssets', 'assets_canManageAssets', 'assets_canManageAssets')
);
?>