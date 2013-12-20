<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: segmentprofile.php
 * Created:        @tony.assaad    Dec 12, 2013 | 10:30:30 AM
 * Last Update:    @tony.assaad    Dec 12, 2013 | 10:30:30 AM
 */

if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSegments'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
$lang->load("products_segments");
if(!$core->input['action']) {
	$psid = $db->escape_string($core->input['id']);
	$segment_obj = new ProductsSegments($psid);
	$segment['title'] = $segment_obj->get()['title'];
	$segmentapp_objs = $segment_obj->get_applications();   /* retunr object of applications of the segments */
	if(is_array($segmentapp_objs)) {
		$segment_applications = '<div style="display:block;padding:5px; vertical-align: left;"> <ul>';
		foreach($segmentapp_objs as $segmentapp_obj) {
			$applications = $segmentapp_obj->get();
			$segment_applications .='<li><strong><span> '.$applications['title'].'</span></strong>';

			/* Get functions for all the segment applications */
			$segmentappfunc_objs = $segmentapp_obj->get_segappfunctions();
			if(is_array($segmentappfunc_objs)) {
				foreach($segmentappfunc_objs as $function_obj) {
					//loop over funtion objects 
					$segment_applications .='<ul><li><span>'.$function_obj->get()['title'].'</span></li></ul>  </li> ';
					//$segment_applicationsfunctions .='<li> '.$function_obj->get()['title'].'</li> ';
				}
			}
			$endproduct_objs = $segmentapp_obj->get_endproduct();
			if(is_array($endproduct_objs)) {
				$endproduct_types = '<div style="display:block;padding:5px; vertical-align: left;"> <ul>';
				foreach($endproduct_objs as $endproduct_obj) {
					$endproduct_types .='<li><span> '.$endproduct_obj->get()['title'].'</span>';
				}
				$endproduct_types .='</ul></div>';
			}
		}
		$segment_applications .='</ul></div>';
		//$segment_applicationsfunctions .='</ul></div>';
	}

	/* segment coordinators */
	$segmentcoord_objs = $segment_obj->get_coordinators();
	if(is_array($segmentcoord_objs)) {
		$segment_coordinators = ' <div style="display:block;padding:5px; vertical-align: left;"> <ul>';
		foreach($segmentcoord_objs as $segmentcoord_obj) { /* get the coordinators detials from the user object through get_coordinator() function */
			$segment_coordinators.='<li> <a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$segmentcoord_obj->get_coordinator()->get()['uid'].'" target="_blank">'.$segmentcoord_obj->get_coordinator()->get()['displayName'].'</a></li> ';
		}
		$segment_coordinators .='</ul></div>';
	}

	$segmentemployees_objs = $segment_obj->get_assignedemployees();
	if(is_array($segmentemployees_objs)) {
 
		$segment_employees = ' <div style="display:block;padding:5px; vertical-align: left;"> <ul>';
		foreach($segmentemployees_objs as $assignedemployee) {
			$segment_employees.='<li> <a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$assignedemployee->get()['uid'].'" target="_blank">'.$assignedemployee->get()['displayName'].'</a></li> ';
		}
		$segment_employees .='</ul></div>';
	}
	$segmentsuppliers_objs = $segment_obj->get_entities();
	if(is_array($segmentsuppliers_objs)) {
		$segment_suppliers = ' <div style="display:block;padding:5px; vertical-align: left;"> <ul>';
		foreach($segmentsuppliers_objs as $segmentsuppliers_obj) {
			$segment_suppliers.='<li> <a href="'.$core->settings['rootdir'].'/index.php?module=profiles/entityprofile&eid='.$segmentsuppliers_obj->get()['eid'].'" target="_blank">'.$segmentsuppliers_obj->get()['companyName'].'</a></li> ';
			print_r($segmentemployees_obj);
		}
		$segment_suppliers .='</ul></div>';
	}

	if($core->usergroup['canViewAllCust'] == 0) {
		$filter = ' AND ase.uid='.$core->user['uid'];
	}
	$segmentcustomer_objs = $segment_obj->get_customers($filter);
	if(is_array($segmentcustomer_objs)) {
		$segment_customers = ' <div style="display:block;padding:5px; vertical-align: left;"> <ul>';
		foreach($segmentcustomer_objs as $segmentcustomer_obj) {
			$segment_customers.='<li> <a href="'.$core->settings['rootdir'].'/index.php?module=profiles/entityprofile&eid='.$segmentcustomer_obj->get()['eid'].'" target="_blank">'.$segmentcustomer_obj->get()['companyName'].'</a></li> ';
		}
		$segment_customers .='</ul></div>';
	}

//	$segmentendproduct_objs = $segment_obj->get_endproduct();
//	if(is_array($segmentendproduct_objs)) {		
//		$segment_customers = ' <div style="display:block;padding:5px; vertical-align: left;"> <ul>';
//		foreach($segmentendproduct_objs as $segmentendproduct_obj) {
//			$segment_endproduct.='<li> '.$segmentendproduct_obj->get()['title'].'</a></li> ';
//		}
//		$segment_endproduct .='</ul></div>';
//	}

	eval("\$segmentsprofilepage = \"".$template->get("admin_products_segmentprofile")."\";");
	output_page($segmentsprofilepage);
}
?>
