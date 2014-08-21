<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'managenews';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('list', 'settings', 'managenews', 'managewebpage', 'listwebpages', 'listmenu', 'eventlist'),
        'title' => array('listnews', 'cmssettings', 'managenews', 'managewebpage', 'listwebpages', 'listmenu', 'eventlist'),
        'permission' => array('canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem')
);
?>