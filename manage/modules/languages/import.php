<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Import Language Files
 * $module: Admin/Languages
 * $id: import.php
 * Created: 	@zaher.reda 	July 25, 2012 | 11:43:00 AM	
 * Last Update: @zaher.reda 	July 25, 2012 | 11:43:00 AM	
 */

if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}

$param = array('lang' => 2, 'isFrontEnd' => 0, 'path' => ROOT.INC_ROOT.'languages/french/');
$dir = @opendir($core->sanitize_path($param['path']));
$db->set_charset($lang->settings['charset_db']);

//$lang->rebuild_langfile('attendance_messages', array(1=>'english', 2=>'french'));
$accepted_files = array();
//$rejected_files = array('');

while(false !== ($file = readdir($dir))) {
	$langfile_info = pathinfo($path.'/'.$file);
	if($file != '.' && $file != '..' && $langfile_info['extension'] == 'php') {
		if(!empty($accepted_files)) {
			if(!in_array($file, $accepted_files)) {
				continue;	
			}
		}
		
		$filename = str_replace('.lang.'.$langfile_info['extension'], '', $file);
		$language_file = $param['path'].'/'.$file;
		
		unset($lang);
		require_once $language_file;
		
		if(is_array($lang)) {
			foreach($lang as $key => $val) {
				$query = $db->query('SELECT slvid FROM '.Tprefix.'system_langvariables WHERE name="'.$key.'" AND fileName="'. $filename.'" AND isFrontEnd='.$param['isFrontEnd']);
				if($db->num_rows($query) > 0) {	
					$slvid = $db->fetch_field($query, 'slvid');
				}
				else
				{
					$db->insert_query('system_langvariables', array('name' => $key, 'fileName' => $filename, 'isFrontEnd' => $param['isFrontEnd']));
					echo 'Added variable: '.$filename.'=>'.$key.'<br />';
					$slvid = $db->last_id();
				}
				
				if(value_exists('system_languages_varvalues', 'variable', $slvid, 'lang="'.$param['lang'].'"')) {
					$db->update_query('system_languages_varvalues', array('lang' => $param['lang'], 'value' => utf8_encode($val), 'timeModified' => TIME_NOW, 'modifiedBy' => $core->user['uid']), 'variable='.$slvid.' AND lang="'.$param['lang'].'"');
					echo 'Updated lang: '.$param['lang'].'<br />';
				}
				else
				{
					
					$db->insert_query('system_languages_varvalues', array('lang' => $param['lang'], 'variable' => $slvid, 'value' => utf8_encode($val), 'timeCreated' => TIME_NOW, 'createdBy' => $core->user['uid']));
					echo 'Added lang: '.$param['lang'].'<br />';
				}
			}
		}
	}
}
?>