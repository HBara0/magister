<?php
$module['name'] = 'filesharing';
$module['title'] = $lang->filesharing;
$module['homepage'] = 'fileslist';
$module['globalpermission'] = 'canUseFileSharing';
$module['menu'] = array('file' => array('fileslist', 'uploadfile'),
        'title' => array('fileslist', 'uploadfile'),
        'permission' => array('canUseFileSharing', 'canUseFileSharing')
);
?>