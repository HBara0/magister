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
			$query.=$dateto ? ('date<=\''.$dateto.'\'') : '';
			if($dateto && $datefrom) {
				$query.=' AND ';
			}
			$query.=$datefrom ? ('date>=\''.$datefrom.'\'') : '';
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
			foreach($spid as $key => $value) {
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
			foreach($pid as $key => $value) {
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
			foreach($affid as $key => $value) {
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
		$content.=generate_stock_reports_email_data(process_query($query, 'spid', array('amount','affid'), array('table' => 'entities', 'id' => 'eid', 'name' => 'companyName')));
		$content.=generate_stock_reports_email_data(process_query($query, 'pid', array('amount', 'quantity', 'spid','affid'), array('table' => 'products', 'id' => 'pid', 'name' => 'name')));
		$content.="<hr>$query<hr>";
		$purchase_report = $db->query($query);
		if($db->num_rows($purchase_report) > 0) {
			$content.='</td></tr><tr><td colspan=3>';
			$firstrow = true;
			while($purchase = $db->fetch_assoc($purchase_report)) {
				if($firstrow) {
					$firstrow = false;
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
function get_name_from_id($id, $tablename = 'products', $idcolumn = 'pid', $namecolumn = 'name') {
	static $idtonamecache = array();
	global $db;
	try {
		$name = $idtonamecache[$tablename][$idcolumn][$namecolumn][$id];
		if(isset($name)) {
			return $name;
		}
	}
	catch(Exception $e) {
		$msg = 'Exception '.$e->getMessage();
	}
	$name = $db->fetch_field($db->query('SELECT '.$namecolumn.' FROM '.Tprefix.$tablename.' WHERE '.$idcolumn.'="'.$db->escape_string($id).'"'), $namecolumn);
	$idtonamecache[$tablename][$idcolumn][$namecolumn][$id] = $name;
	if(isset($name)) {
		return $name;
	}
	else {
		return $id;
	}
}

function process_query($query, $groupingattribute, $trackedcolumns = array("amount"), $resolve = null) {
	global $db;
	$currency_obj = new Currencies('USD');
	$purchase_report = $db->query($query);
	$dataarray = array();
	if($db->num_rows($purchase_report) > 0) {
		while($purchase = $db->fetch_assoc($purchase_report)) {

			/*
			  if(!isset($dataarray[$purchase[$groupingattribute]])) {
			  foreach($trackedcolumns as $column) {
			  $dataarray[$purchase[$groupingattribute]][$column] = 0;
			  }
			  }

			 */
			if(isset($resolve)) {
				$name = get_name_from_id($purchase[$groupingattribute], $resolve['table'], $resolve['id'], $resolve['name']);
			}
			else {
				$name = $purchase[$groupingattribute];
			}
			foreach($trackedcolumns as $column) {

				if(!isset($dataarray[$name]['count'])) {
					$dataarray[$name]['count'] = 1;
				}
				else {
					$dataarray[$name]['count']++;
				}

				if($column == 'amount') {
					$rate = $purchase['usdFxrate'];
					if(!isset($rate) || $rate == 0) {
						$rate = $currency_obj->get_average_fxrate($purchase['currency'], array('from' => date("-4 days", strtotime($purchase['date'])), 'to' => date("+4 days", strtotime($purchase['date']))));
					}
					$dataarray[$name][$column]+=$purchase[$column] * $rate;
				}
				elseif($column == 'affid') {
					if(isset($resolve)) {
						$dataarray[$name][$column] = get_name_from_id($purchase[$column], 'affiliates', 'affid', 'name');
					}
				}
				elseif($column == 'spid') {
					if(isset($resolve)) {
						$dataarray[$name][$column] = get_name_from_id($purchase[$column], 'entities', 'eid', 'companyName');
					}
				}
				elseif($column == 'pid') {
					if(isset($resolve)) {
						$dataarray[$name][$column] = get_name_from_id($purchase[$column], 'products', 'pid', 'name');
					}
				}
				else {
					$dataarray[$name][$column] = $purchase[$column];
				}
			}
		}
	}
	return $dataarray;
}



function generate_stock_reports_email_data($data) {
	$align="left";
	$oddline = '<tr  style="text-align: '.$align.'; padding: 5px; border-bottom: 1px dashed #888888; background-color:#F7FAFD;">';
	$evenline = '<tr  style="text-align: '.$align.'; padding: 5px; border-bottom: 1px dashed #888888; background-color:#E1E1E1;">';
	$content = '<table style="text-align: '.$align.'; padding:5px;margin:5px;width:100%; font-size: inherit; border-bottom: 1px solid black; border-right: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;"  cellpadding="0" cellspacing="0" ><tr>';
	$th = '<th style="padding: 5px; border-bottom: 1px dashed #888888; background-color:#92D050;">';
	$td = '<td style="border-bottom: 1px dashed #888888;">';
	$toggle = true;

	foreach($data as $key => $row) {
		$content.=$th.'Name</th>';
		foreach($row as $columnid => $value) {
			$content.=$th.$columnid.'</th>';
		}
		$content.='</tr>';
		break;
	}
	foreach($data as $key => $row) {
		if($toggle = !$toggle) {
			$content.=$oddline;
		}
		else {
			$content.=$evenline;
		}
		$content.=$td.$key.'</td>';
		foreach($row as $columnid => $value) {
			$content.=$td.$value.'</td>';
		}
		$content.='</tr>';
	}
	$content.='</table>';
	return $content;
}

?>