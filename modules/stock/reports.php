<?php

/*
 * Orkila Central Online System (OCOS)
 * Copyright Â© 2010 Orkila International Offshore, All Rights Reserved
 * Import Stock
 * $module: Stock
 * Created		@zaher.reda 		September 7, 2012 | 3:41 PM
 * Last Update: 	@zaher.reda 		September 7, 2012 | 3:41 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

$session->start_phpsession();

$content = '';

if($core->usergroup['stock_canGenerateReports'] == '1') {
	//if(!isset($core->input['action']) || $core->input['action'] == '')
	$headerjs = '<script>$(document).ready(function(){$(".datepicker" ).datepicker();});</script>';
	$content = '<div class="strep_maindiv"><form name="reportfilter" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	$content.='<select name="reporttype"><option value="0">Basic</option><option value="1">Detailed</option></select><div style="margin-left:20%;display:inline;">From:</div></td><td>';
	$content.='<input type="text" name="datefrom" class="datepicker" /><div style="margin-left:28%;display:inline;">To:</div></td><td>';
	$content.='<input type="text" name="dateto" class="datepicker" /></td></tr><tr>';

	$affiliates_query = $db->query('SELECT affid,name from '.Tprefix.'affiliates');
	if($db->num_rows($affiliates_query) > 0) {
		$content.='<td><select name="affiliate[]" multiple="true">';
		while($affiliate = $db->fetch_assoc($affiliates_query)) {
			$content.='<option value="'.$affiliate['affid'].'">'.$affiliate['name'].'</option>';
		}
		$content.='</select>';
	}

	$suppliers_query = $db->query('SELECT eid,companyName from '.Tprefix.'entities WHERE type=\'s\'');
	if($db->num_rows($suppliers_query) > 0) {
		$content.='<td><select name="supplier[]" multiple="true">';
		while($supplier = $db->fetch_assoc($suppliers_query)) {
			$content.='<option value="'.$supplier['eid'].'">'.$supplier['companyName'].'</option>';
		}
		$content.='</select>';
	}

	$products_query = $db->query('SELECT pid,name from '.Tprefix.'products');
	if($db->num_rows($products_query) > 0) {
		$content.='</td><td><select name="product[]" multiple="true">';
		while($product = $db->fetch_assoc($products_query)) {
			$content.='<option value="'.$product['pid'].'">'.$product['name'].'</option>';
		}
		$content.='</select>';
	}

	$content.='</td></tr><td colspan=2><input type=submit value=Generate></td></tr>';

	$content.='</form></div>'.$headerjs;

	if($core->input['action'] == 'getreport') {
		//$content .= '<pre><hr>'.print_r($core->input, true).'</hr></pre>';

		$query = 'SELECT * from '.Tprefix.'integration_mediation_stockpurchases';
		$checkifwherewasadded = false;
		$dateto = parse_date("m/d/Y", $core->input['dateto']);
		$datefrom = parse_date("m/d/Y", $core->input['datefrom']);

		if($dateto || $datefrom) {
			$checkifwherewasadded = true;
			$query.=' WHERE ';
			$query.=$dateto?('date<=\''.$dateto.'\''):'';
			if($dateto && $datefrom) {
				$query.=' AND ';
			}
			$query.=$datefrom?('date>=\''.$datefrom.'\''):'';
		}

		  /*
		  if ($core->input['reporttype']==0) {
		  $query.='AND (spid='.$spid.' OR pid='.$pid.' OR affid='.$affid.')';
		  } else {
		  $query.='AND spid='.$spid.' AND pid='.$pid.' AND affid='.$affid;
		  }
		  */


		if(isset($core->input['supplier'])) {
			$spid = $core->input['supplier'];
			$firstone = true;
			foreach($spid as $key=>$value) {
				$value = $db->escape_string($value);
				if($checkifwherewasadded) {
					if($firstone) {
						$firstone = false;
						$query .=' AND (spid=\''.$value.'\'';
					}
					else {
						$query .=' OR spid=\''.$value.'\'';
					}
				}
				else {
					$firstone = false;
					$checkifwherewasadded = true;
					$query .=' WHERE (spid=\''.$value.'\'';
				}
			}
			$query.=')';
		}
		if(isset($core->input['product'])) {
			$pid = $core->input['product'];
			$firstone = true;
			foreach($pid as $key=>$value) {
				$value = $db->escape_string($value);
				if($checkifwherewasadded) {
					if($firstone) {
						$firstone = false;
						$query .=' AND (pid=\''.$value.'\'';
					}
					else {
						$query .=' OR pid=\''.$value.'\'';
					}
				}
				else {
					$firstone = false;
					$checkifwherewasadded = true;
					$query .=' WHERE (pid=\''.$value.'\'';
				}
			}
			$query.=')';
		}
		if(isset($core->input['affiliate'])) {
			$affid = $core->input['affiliate'];
			$firstone = true;
			foreach($affid as $key=>$value) {
				$value = $db->escape_string($value);
				if($checkifwherewasadded) {
					if($firstone) {
						$firstone = false;
						$query .=' AND (affid=\''.$value.'\'';
					}
					else {
						$query .=' OR affid=\''.$value.'\'';
					}
				}
				else {
					$firstone = false;
					$checkifwherewasadded = true;
					$query .=' WHERE (affid=\''.$value.'\'';
				}
			}
			$query.=')';
		}


		$content.="<hr>$query<hr>";
		$purchase_report = $db->query($query);
		if($db->num_rows($purchase_report) > 0) {
			$content.='</td></tr><tr><td colspan=3>';
			$firstrow=true;
			while($purchase = $db->fetch_assoc($purchase_report)) {
				if ($firstrow) {
					$firstrow=false;
					$content.='<table border=1 cellspacing=0 cellpading=0><tr><td>ID</td><td>supplier</td><td>product</td><td>date("m/d/Y")</td><td>amount</td><td>affiliate</td></tr>';
				}
				$content.='<tr><td>'.$purchase['imspid'].'</td><td>'.$purchase['spid'].'</td><td>'.$purchase['pid'].'</td><td>'.date("m/d/Y", $purchase['date']).'</td><td>'.$purchase['amount'].'</td><td>'.$purchase['affid'].'</td></tr>';
			}
			$content.='</table></td></tr>';
		}
	}
}
else {
	$content = 'You do not have the right to access this page.<br>Contact an administrator for more information.';
}

eval("\$report_template = \"".$template->get('stock_purchasereport')."\";");
output_page($report_template);

function dump_templates_to_file_folder() {
	global $db, $template;
	$base_dir = ROOT;
	$base_dir = substr($base_dir, 0, strlen($base_dir) - 1);
	$base_dir.='\templates';
	$content = '<div style="padding:20px;"><form><hr>';
	$templates_query = $db->query('SELECT * FROM '.Tprefix.'templates');
	if($db->num_rows($templates_query) > 0) {
		while($singletemplate = $db->fetch_assoc($templates_query)) {
			$content.='<br>'.$singletemplate['title'];
			try {
				$filename = $base_dir.'\\'.$singletemplate['title'];
				$filehandle = fopen($filename, 'w');
				fwrite($filehandle, $singletemplate['template']);
				fclose($filehandle);
				$content.=' V';
			} catch(Exception $e) {
				$content.=' X '.$e->getMessage();
			}
		}
	}
	$content.='<br><input type=submit value=send id="sendform"/><hr>';
	$content.='</form><div id=resultsdiv></div></div>';

	$script = '<script>
				$(document).ready(function() {
					$("#sendform").click(function(){
						sharedFunctions.requestAjax("post", "index.php?module=stock/migrate&action=do_migrate","", "resultsdiv","resultsdiv", "html");
					});
				});
			  </script>';
	$content.=$script;
	eval("\$debug = \"".$template->get('debug')."\";");
	output_page($debug);
}

?>