<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'managenews';
$module['globalpermission'] = 'canUseCms';
$module['menu'] = array(
        'file' => array(
                'settings',
                'contentcategorieslist',
                'extractpages',
                'affdesclist',
                'managenews' => array('listnews', 'managenews'),
                'managewebpage' => array('managewebpage', 'listwebpages'),
                'listmenu' => array('listmenu'),
                'manageevents' => array('manageevents', 'eventlist'),
                'managehighlight' => array('highlightslist', 'managehighlight')
        ),
        'title' => array('cmssettings', 'contentcategorieslist', 'extractpages', 'manageaff', 'managenews' => array('listnews', 'managenews'), 'managewebpage' => array('managewebpage', 'listwebpages'), 'managemenus' => array('listmenu'), 'manageevents' => array('createevent', 'eventlist'), 'managehighlights' => array('highlightslist', 'managehighlights')),
        'permission' => array('canAdminCP', 'canUseCms', 'canUseCms', 'canUseCms', array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms')));
?>