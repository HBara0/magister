<?php
$module['name'] = 'reporting';
$module['title'] = $lang->reporting;
$module['homepage'] = 'home';
$module['globalpermission'] = 'canUseReporting';
/* $module['menu'] = array('file' 		  => array('home', 'fillreport', 'generatereport', 'list', 'createreports'),
  'title'		 => array('home', 'fillreport', 'generatereport', 'listreports', 'createreports'),
  'permission'	=> array('canUseReporting', 'canFillReports', 'canGenerateReports', 'canGenerateReports', 'canCreateReports')
  ); */
$module['menu'] = array('file' => array('home', 'quarterly' => array('fillreport', 'generatereport', 'generatereport', 'createreports'), 'monthly' => array('fillmreport', 'listmreports')),
        'title' => array('home', 'quarterly' => array('fillreport', 'generatereport', 'listreports', 'createreports'), 'monthly' => array('fillreport', 'listreports')),
        'permission' => array('canUseReporting', array('canUseReporting', 'canFillReports', 'canGenerateReports', 'canUseReporting', 'canCreateReports'), array('canUseReporting', 'canFillReports', 'canGenerateReports')),
);
?>