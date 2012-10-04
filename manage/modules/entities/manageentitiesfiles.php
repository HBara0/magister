<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage entities files
 * $id: manageentitiesfiles.php	
 * Created: 	@najwa.kassem   Oct 4, 2010 | 10:10 AM
 * Last Update: @zaher.reda 	Oct 7, 2010 | 11:57 AM
 */
 
if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	$sort_query = 'title ASC';
	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $core->input['sortby'].' '.$core->input['order'];
	}
	$sort_url = sort_url();
			
	$limit_start = 0;
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}
	
	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	$query = $db->query("SELECT f.*, e.companyName
						FROM ".Tprefix."files f JOIN ".Tprefix."entities e ON (e.eid=f.referenceId)
						WHERE f.reference = 'eid'
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
	
	if($db->num_rows($query) > 0) {
		$filters_required = array('category', 'year', 'affid', 'spid');
		$filters_cache = array();
		
		while($file = $db->fetch_array($query))  {
			$file['fvid'] = $db->fetch_field($db->query("SELECT fvid FROM ".Tprefix."fileversions WHERE fid='{$file[fid]}' ORDER BY timeLine DESC LIMIT 0, 1"), 'fvid');
			
			$rowclass = alt_row($rowclass);
			
			switch($file['category']) {
			 case 0: $file['category_output'] = 'Contracts';
					 break;
			 case 1: $file['category_output'] = 'Meeting Minutes';
					 break;
			 case 2: $file['category_output'] = 'Reports';
					 break;
			 case 3: $file['category_output'] = 'Misc';
					 break;
			}
		
			eval("\$files_list .= \"".$template->get('admin_entities_manageentitiesfiles_filerow')."\";");
		}
		$multipages = new Multipages('files', $core->settings['itemsperlist']);
		$files_list .= '<tr><td colspan="5">'.$multipages->parse_multipages().'</td></tr>';
	}
	else
	{
		$files_list = '<tr><td colspan="5" style="text-align:center;">-</td></tr>';
	}
	
	
	$categories_list = parse_selectlist('category', 1, array('0' => 'Contract', '1' => 'Meeting Minutes','2' => 'Auditing', '3' => 'Misc'),0);
	
	$uploadfile_disabled = '';
	eval("\$addedit_form = \"".$template->get('admin_entities_manageentitiesfiles_addeditfields')."\";");
	eval("\$viewfiles_page = \"".$template->get('admin_entities_manageentitiesfiles')."\";");
    output_page($viewfiles_page);
}
else 
{
	if($core->input['action'] == 'do_uploadfile') {
		echo $headerinc;
		
		$allowed_types = array('application/excel', 'application/x-excel' ,'application/vnd.ms-excel', 'application/vnd.msexcel', 'image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/msword','application/vnd.ms-powerpoint', 'text/plain','application/vnd.openxmlformats-officedocument.wordprocessingml.document');

		$upload = new Uploader('uploadfile', $_FILES, $allowed_types,'putfile', 3000000 , 0, 1);
		//$path = $core->settings['rootdir'].'uploads/entitiesfiles/';
		
		$path = '../uploads/entitiesfiles/';
		$upload->set_upload_path($path);
		$upload->process_file();
		$file_data = $upload->get_fileinfo();

		if(!empty($core->input['title'])) {
			$filetitle = $core->input['title'];
		}
		else
		{
			$original_name = explode('.', $file_data['originalname']);
			$filetitle = $original_name[0];
		}
		
		if(value_exists('files', 'title', $filetitle)) {
			?>
			<script language="javascript" type="text/javascript">
				$(function() { 
					window.top.$("#upload_Result").html("<?php echo $lang->filenameexists; ?>");
				}); 
			</script>   
			<?php
			exit;
		}
		
		$newfile = array(
			'title' 		=> $filetitle,
			'category'	 => $core->input['category'],
			'description'  => $core->input['description'],
			'reference'	=> 'eid',
			'referenceId'  => $core->input['eid']
		);
			
		$query = $db->insert_query('files', $newfile);
		if($query) {
			$fid = $db->last_id();
			$newfileversion = array(
				'fid'	 => $fid,
				'name'	=> $file_data['name'],
				'type'	=> $file_data['type'],
				'size'	=> $file_data['size'],
				'timeLine'=> TIME_NOW
				); 
				
			$db->insert_query('fileversions', $newfileversion);
		}	
		?>
        <script language="javascript" type="text/javascript">
			$(function() { 
				window.top.$("#upload_Result").html("<?php echo $upload->parse_status($upload->get_status()); ?>");
			}); 
		</script>   
        <?php				
	}
	elseif($core->input['action'] == 'do_modifyfileinfo') {
		$editfile = array(
			'title' 		=> $core->input['title'],
			'category'	 => $core->input['category'],
			'description'  => $core->input['description'],
			'reference'	=> 'eid',
			'referenceId'  => $core->input['eid']
		); 
			
		$query = $db->update_query('files', $editfile, "fid='".$db->escape_string($core->input['fid'])."'");
		output_xml("<status>true</status><message>{$lang->fileinfomodifiedsuccessfully}</message>");
	}
	elseif($core->input['action'] == 'do_update') {
		echo $headerinc;
	
		$allowed_types = array('application/excel', 'application/x-excel' ,'application/vnd.ms-excel', 'application/vnd.msexcel', 'image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/msword','application/vnd.ms-powerpoint', 'text/plain','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		
		$upload = new Uploader('updatefile', $_FILES, $allowed_types,'putfile', 3000000 , 0, 1);
		$path = '../uploads/entitiesfiles/';

		$upload->set_upload_path($path);
		
		$upload->process_file();
		if($upload->get_status() != 4) {
			?>
				<script language="javascript" type="text/javascript">
					$(function() { 
						window.top.$("#updateUpload_Result").html("<?php echo $upload->parse_status($upload->get_status()); ?>");
					}); 
				</script>   
			<?php	
			exit;
		}
		
		$file_data = $upload->get_fileinfo();
		
		$newversion = array(
			'fid'	    => $core->input['fid'],
			'name'	   => $file_data['name'],
			'type'	   => $file_data['type'],
			'size'	   => $file_data['size'],
			'timeLine'   => TIME_NOW,
			'changes'    => $core->input['changes']
		);  
					
		$query = $db->insert_query('fileversions', $newversion);
		if($query) {
			?>
			<script language="javascript" type="text/javascript">
                $(function() { 
                    window.top.$("#updateUpload_Result").html("<?php echo $lang->newversionuploaded; ?>");
                }); 
            </script>   
			<?php
		}
	}
	elseif($core->input['action'] == 'download') {
		if(!isset($core->input['fvid']) || empty($core->input['fvid'])) {
			redirect($_SERVER['HTTP_REFERER']);
		}
		$path = $core->settings['rootdir'].'/uploads/entitiesfiles/';
	 	$download = new Download('fileversions', 'name', array('fvid' => $core->input['fvid']), $path);
		$download ->download_file();
	}
	elseif($core->input['action'] == 'edit') {
		if(!isset($core->input['fid']) || empty($core->input['fid'])) {
			redirect($_SERVER['HTTP_REFERER']);
		}
		
		$file = $db->fetch_assoc($db->query("SELECT f.*, e.companyName
											FROM ".Tprefix."files f JOIN ".Tprefix."entities e ON (e.eid=f.referenceId)
											WHERE fid='".$db->escape_string($core->input['fid'])."'"));

		$categories_list = parse_selectlist('category', 1, array('0' => 'Contract', '1' => 'Meeting Minutes','2' => 'Auditing', '3' => 'Misc'), $file['category']);
		
		$uploadfile_disabled = ' disabled';
		eval("\$addedit_form = \"".$template->get('admin_entities_manageentitiesfiles_addeditfields')."\";");
		eval("\$edit = \"".$template->get("admin_entities_manageentitiesfiles_editfile")."\";");
		output_page($edit);
	}
	elseif($core->input['action'] == 'get_updateentitiesfiles') {
		eval("\$updatebox = \"".$template->get("popup_admin_entities_manageentitiesfiles_updatefile")."\";");
		echo $updatebox;
	}
}
?>