<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'managenews';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('listnews', 'settings', 'managenews', 'managewebpage', 'listwebpages', 'listmenu', 'manageevents', 'eventlist'),
        'title' => array('listnews', 'cmssettings', 'managenews', 'managewebpage', 'listwebpages', 'listmenu', 'manageevents', 'eventlist'),
        'permission' => array('canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem')
);
?>