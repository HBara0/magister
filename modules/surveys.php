<?php
$module['name'] = 'surveys';
$module['title'] = $lang->surveys;
$module['homepage'] = 'list';
$module['globalpermission'] = 'canUseSurveys';
$module['menu'] = array('file' => array('createsurvey', 'list'),
        'title' => array('createsurvey', 'list'),
        'permission' => array('surveys_canCreateSurvey', 'canUseSurveys')
);
?>