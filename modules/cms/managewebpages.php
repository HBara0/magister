<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Create Web Page
 * $module: CMS
 * $id: managewebpages.php
 * Created:			@tony.assaad	August 24, 2012 | 10:53 PM
 * Last Update: 	@tony.assaad	August 24, 2012 | 02:55  PM
 */
 
if($core->usergroup['cms_canAddPage'] == 0) {
	//error($lang->sectionnopermission);
	//exit;
}

if(!$core->input['action']) {	
	if($core->input['type'] == 'edit') {
		$actiontype = 'edit';
		$lang->createwebpage = $lang->editwebpage;
		
		$pageid = $db->escape_string($core->input['id']);
		$cms_page = new CmsPages($pageid);  /* call the page object and the pageid to the constructor to read the single page */
		$page = $cms_page->get(); 
	}
	else
	{
		$actiontype = 'add';
		//$pagecategory = get_specificdata('cms_contentcategories', array('cmsccid', 'title'), 'cmsccid', 'title', 'title');
		//$pagecategories_list = parse_selectlist('page[category]', 5, $pagecategory,$page['category']);		
	}
	
	if($core->usergroup['crm_canPublishPages'] == 1) {
		$publish_page = '<div style="display:table-cell;">'.$lang->publish.'</div><div style="display: table-cell; padding:10px;"><input name="page[isPublished]" type="checkbox" value="1"></div>';
	}
	
	eval("\$createpage =\"".$template->get('cms_webpage_create')."\";");
	output_page($createpage);
}
else 
{
	if($core->input['action'] == 'do_addpage' || $core->input['action'] == 'do_editpage') {
		if($core->input['action'] == 'do_editpage') {
			$options['operationtype'] = 'updateversion';
		}
		$cms_page = new CmsPages();
		$cms_page->create($core->input['page'], $options);

		switch($cms_page->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1: 
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->pageexists}</message>");
				break;
			case 3: 
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				break;
		}
	}
	elseif($core->input['action'] == 'do_uploadtmpimage') {
		$filepath = './tmp/';

		$allowed_types = array('image/jpeg', 'image/gif', 'image/png');
		$upload = new Uploader('file', $_FILES, $allowed_types, 'putfile', 5242880, 0, 1); //5242880 bytes = 5 MB (1024)

		$upload->set_upload_path($filepath);
		$upload->process_file();
		
		$fileinfo = $upload->get_fileinfo();

		echo stripslashes(json_encode(array('filelink' => DOMAIN.'/tmp/'.$fileinfo['name'])));
	}
}	
?>