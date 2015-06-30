<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'managenews';
$module['globalpermission'] = 'canUseCms';
$module['menu'] = array(
        'file' => array(
                'settings',
                'contentcategorieslist',
                'managenews' => array('listnews', 'managenews'),
                'managewebpage' => array('managewebpage', 'listwebpages'),
                'listmenu' => array('listmenu'),
                'manageevents' => array('manageevents', 'eventlist'),
                'highlightslist' => array('highlightslist', 'managehighlight'),
        ),
        'title' => array('cmssettings', 'contentcategorieslist', 'managenews' => array('listnews', 'createnews'), 'managewebpage' => array('createwebpage', 'listwebpages'), 'managemenus' => array('listmenu'), 'manageevents' => array('createevent', 'eventlist'), 'highlightslist' => array('highlightslist', 'managehighlights')),
        'permission' => array('canAdminCP', 'canUseCms', array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms'), array('canUseCms', 'canUseCms', 'canUseCms')));
?>