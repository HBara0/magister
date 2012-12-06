<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: assets.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 * Last Update:    @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 */

if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['assets_canTrack'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!isset($core->input["action"])) {
	$core->input["action"] = "map";
}




$assetslist=Asset::getAllAssets();
if($core->input['action'] == 'map') {
	//echo '<pre>'.print_r($core->input,true).'</pre>';
	$core->input['asid'];
	$core->input['fromDate'];
	$core->input['toDate'];

	$pagecontents = '<form name="assets_filter" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assets&action='.$core->input['action'].'">
					 <table cellspacing=5 cellpadding=5 border=0>
					 <tr><td valign=top align=right>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 0,$assetslist , $selected_options,1).'</td></tr>
					 <tr><td valign=top align=right>'.$lang->from.'</td><td><input type="text" name="fromDate" id="pickDateFrom" tabindex="1"/></td></tr>
					 <tr><td valign=top align=right>'.$lang->to.'</td><td><input type="text" name="toDate" id="pickDateTo" tabindex="2"/></td></tr>
					 <tr><td colspan=2><input type="submit" name="filter_assets" value="Filter" tabindex="3"/></tr></tr></table></form>';
	$pagetitle = $lang->assetstrackmap;
	$asset = new Asset();
	if ($from=='') {
		unset($from);
	}
	if ($to=='') {
		unset($to);
	}
	$data = $asset->get_data_for_users(array($core->user['uid']),$from,$to,10);
	$pagecontents .= $asset->get_map($data, array('infowindow' => 1, 'mapcenter' => '33.89, 35.51', 'zoom' => 14));
}
elseif($core->input['action'] == 'list') {
	$resolve = array('asid' => array('table' => 'assets', 'id' => 'asid', 'name' => 'title'));
	if(isset($core->input['asid'])) {
		$selected_options=array($core->input['asid']);
	} else {
		$selectedasset=get_first($assetslist);
		$selected_options=array($selectedasset['key']);
	}
	$selectedasset=get_first($selected_options);

	$pagecontents = '<form name="assets_filter" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assets&action='.$core->input['action'].'">
					 <table cellspacing=5 cellpadding=5 border=0>
					 <tr><td valign=top align=right>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 1,$assetslist , $selected_options,0,'this.form.submit()').'&nbsp;&nbsp;<a href="'.DOMAIN.'/index.php?module=assets/assets&action=map&asid='.$selectedasset['val'].'">Show on map</a></td></tr>
					 <tr><td valign=top align=right>'.$lang->from.'</td><td><input type="text" name="fromDate" id="pickDateFrom" tabindex="1" value="'.$core->input['fromDate'].'"/></td></tr>
					 <tr><td valign=top align=right>'.$lang->to.'</td><td><input type="text" name="toDate" id="pickDateTo" tabindex="2" value="'.$core->input['toDate'].'"/></td></tr>
					 <tr><td colspan=2><input type="submit" name="filter_assets" value="Filter" tabindex="3"/></td></tr>
					 </table>
					 <form>';

	$asset = new Asset($selectedasset['val']);
	$from = strtotime($core->input['fromDate']);
	$to = strtotime($core->input['toDate']);
	if ($from=='') {
		unset($from);
	}
	if ($to=='') {
		unset($to);
	}

	$data=$asset->get_data_for_assets(array($selectedasset['val']),$from,$to,5);
	$pagetitle = $lang->assetstrackpage;
	$pagecontents .= '<table cellspacing=0 cellpadding=5 border=1>';
	if(isset($data)) {
		$pagecontents .= '<tr>';
		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key => $value) {
				foreach($value as $columnkey => $columnvalue) {
					$pagecontents .= '<th>'.$lang->{$columnkey}.'</th>';
				}
				break;
			}
			break;
		}
		$pagecontents .= '</tr>';



		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key => $value) {
				if (!isset($value['displayName']))
				{
					$value['displayName']=Maps::get_streetname($value['latitude'], $value['longitude']);
					Asset::update_location($value['alid'],$value);
				}
				$pagecontents .='<tr>';
				foreach($value as $columnkey => $columnvalue) {
					if ($columnkey=='timeLine') {
						$pagecontents .= '<td>'.date('Y/m/d G:i',$columnvalue).'</td>';
					} elseif($columnkey=='asid') {
						$pagecontents .= '<td>'.get_name_from_id($columnvalue, $resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
					}
					else {
						$pagecontents .= '<td>'.$columnvalue.'</td>';
					}

				}
				//$pagecontents .='<pre>'.print_r($value,true).'</pre>';
				$pagecontents .='</tr>';
				//$pagecontents .= '('.$value['latitude'].','.$value['longitude'].')->'.$value['displayName'].' direction:'.$value['direction'].' time:'.date('Y-m-d',$value['timeLine']).'<br>';
			}
		}
	}
	$pagecontents .= '</table>';
}



eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;


function get_first ($foo) {
    foreach ($foo as $k=>$v){
     return array('key'=>$k,'val'=>$v);
    }
}

?>
