<?php

$module['name'] = 'courses';
$module['title'] = $lang->courses;
$module['homepage'] = 'courses';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('courses',),
    'title' => array('courses',),
    'permission' => array('canAccessSystem')
);
?>