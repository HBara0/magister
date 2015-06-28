<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'managenews';
$module['globalpermission'] = 'canUseCms';
$module['menu'] = array(
        'file' => array(
                'settings',
                'managenews' => array('listnews', 'managenews'),
                'managewebpage' => array('managewebpage', 'listwebpages'),
                'listmenu' => array('listmenu'),
                'manageevents' => array('manageevents', 'eventlist'),
        ),
        'title' => array('cmssettings', 'managenews' => array('listnews', 'createnews'), 'managewebpage' => array('createwebpage', 'listwebpages'), 'managemenus' => array('listmenu'), 'manageevents' => array('createevent', 'eventlist')),
        'permission' => array('canAdminCP', array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms')));
?>