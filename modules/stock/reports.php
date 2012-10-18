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
$content = encapsulate_in_fieldset(make_filters($db, $core), 'Filter', false);
$content .= encapsulate_in_fieldset(make_options(), 'Options', false);

if($core->usergroup['stock_canGenerateReports'] == '1') {
	if($core->input['action'] == 'getreport') {
		$query = assemble_filter_query($core, $db);
		$rawdata = retrieve_data($query, array('pid', 'amount', 'affid', 'spid', 'date', 'currency', 'usdFxrate', 'quantity', 'quantityUnit', 'saleType', 'TRansID'));

		$groupingatr = $core->input['groupingattribute'];
		if(!isset($groupingatr))
			$groupingatr = 'pid';

		$doresolve = ($core->input['resolveidintoname'] == 'resolve') ? true : false;


		$resolve = array(
				'affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'),
				'spid' => array('table' => 'entities', 'id' => 'eid', 'name' => 'companyName'),
				'pid' => array('table' => 'products', 'id' => 'pid', 'name' => 'name'),
		);

		if($doresolve)
			$rawdata = resolve_names($rawdata, $resolve);

		if($core->input['reporttype'] == 1) {
			$timesliced = time_regroup($rawdata, 'date');
			foreach($timesliced as $year => $yearly) {
				foreach($yearly as $month => $monthly) {
					foreach($monthly as $week => $weekly) {
						unset($timesliced[$year][$month][$week]);
						$timesliced[$year][$month][$week] = regroup_and_sum($weekly, $groupingatr, array('pid' => 'id', 'amount' => 'numeric', "quantity" => 'numeric'), ($doresolve) ? $resolve[$groupingatr] : null);
					}
				}
			}
			$datapresented = '<pre>'.print_r($timesliced, true).'</pre>';
		}
		else {
			$rawdata = regroup_and_sum($rawdata, $groupingatr, array('pid' => 'id', 'amount' => 'numeric', "quantity" => 'numeric'), ($doresolve) ? $resolve[$groupingatr] : null);
			$datapresented = '<pre>'.print_r($rawdata, true).'</pre>';
		}



		if($core->input['isajax'] == 'true') {
			output_xml($datapresented);
			exit;
		}
		$content .= encapsulate_in_fieldset('<div id="results_fieldset">'.$datapresented.'</div>', 'grouped', false);
		//$content.=generate_stock_reports_email_data(retrieve_data($query, array('amount', 'affid'), array('table' => 'entities', 'id' => 'eid', 'name' => 'companyName'), 'spid'));
		//$content.=generate_stock_reports_email_data(retrieve_data($query, array('amount', 'quantity', 'spid', 'affid'), array('table' => 'products', 'id' => 'pid', 'name' => 'name'), 'pid'));
		//$content.="<hr>$query<hr>";/* */
	}
}
else {
	error($lang->sectionnopermission);
}
eval("\$report_template = \"".$template->get('stock_purchasereport')."\";");
output_page($report_template);
/*
 *
 *
 *
 */
function make_options() {
	$options = '<div class="strep_optdiv"><form name="reportoptions" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	$options.='Group By <select id="groupingattribute" name="group"><option value="pid" selected=true>Product</option><option value="spid">Supplier</option><option value="affid">Affiliate</option>';
	$options.='<input id="resolveidintoname" type="checkbox" name="Resolve" value="resolve">Resolve</input>';
	$options.='<input id="switchdetailed" type="checkbox" name="reporttype">Detailed</input></form></div>';
	$options.='<script type="text/javascript">
					$(document).ready(function() {
						$("#groupingattribute").change(function() {
							post_ajax($(this));						});
						$("#resolveidintoname").change(function() {
							post_ajax($(this));						});
						$("#switchdetailed").change(function() {
							post_ajax($(this));
						});
					});
					function post_ajax(target) {
							var firstform="";
							if ($("#affiliate").val())
								firstform+="&affiliate="+$("#affiliate").val();
							if ($("#supplier").val())
								firstform+="&supplier="+$("#supplier").val();
							if ($("#product").val())
								firstform+="&product="+$("#product").val();
							if ($("#dateto").val())
								firstform+="&dateto="+$("#dateto").val();
							if ($("#datefrom").val())
								firstform+="&datefrom="+$("#datefrom").val();
							//alert(firstform);return;
							var resolveidintoname=0,switchdetailed=0;
							if ($("#switchdetailed").prop("checked")==true)
								switchdetailed=1;
							if ($("#resolveidintoname").prop("checked")==true)
								resolveidintoname="resolve";
							sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&groupingattribute="+$("#groupingattribute").val()+"&resolveidintoname="+resolveidintoname+firstform, "results_fieldset", "results_fieldset", "html");
					}
			   </script>';
	return $options;
}

function assemble_filter_query($core, $db) {
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
	if(isset($core->input['supplier']))
		if($core->input['supplier']) {
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
	if(isset($core->input['product']))
		if($core->input['product']) {
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
	if(isset($core->input['affiliate']))
		if($core->input['affiliate']) {
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
	return $query;
}

function make_filters($db, $core) {
	if(isset($core->input['datefrom'])) {
		$datefrom = $core->input['datefrom'];
	}
	else {
		$datefrom = "";
	}
	if(isset($core->input['dateto'])) {
		$dateto = $core->input['dateto'];
	}
	else {
		$dateto = "";
	}

	if(!isset($core->input['affiliate'])) {
		$core->input['affiliate'] = array();
	}
	else {
		if(gettype($core->input['affiliate']) == "string") {
			$core->input['affiliate'] = explode(',', $core->input['affiliate']);
		}
	}

	if(!isset($core->input['supplier'])) {
		$core->input['supplier'] = array();
	}
	else {
		if(gettype($core->input['supplier']) == "string") {
			$core->input['supplier'] = explode(',', $core->input['supplier']);
		}
	}

	if(!isset($core->input['product'])) {
		$core->input['product'] = array();
	}
	else {
		if(gettype($core->input['product']) == "string") {
			$core->input['product'] = explode(',', $core->input['product']);
		}
	}
	$return = '<div class="strep_maindiv"><table cellspacing=0 cellpadding=0 border=0><tr><td><form name="reportfilter" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	$return.='<div style="text-align:right;"><b><u>From:</u></b></font></div></td><td><input type="text" id="datefrom" name="datefrom" class="datepicker" value="'.$datefrom.'" /><div style="margin-left:28%;display:inline;"><b><u>To:</u></b></div></td><td>';
	$return.='<input type="text" id="dateto" name="dateto" class="datepicker" value="'.$dateto.'"/></td></tr><tr><td><b><u>Affiliate:</u></b></td><td><b><u>Supplier:</u></b></td><td><b><u>Product:</u></b></td></tr><tr>';
	$affiliates_query = $db->query('SELECT affid,name from '.Tprefix.'affiliates');
	if($db->num_rows($affiliates_query) > 0) {
		$return.='<td><select id="affiliate" name="affiliate[]" multiple="true">';
		while($affiliate = $db->fetch_assoc($affiliates_query)) {
			$return.='<option '.(in_array($affiliate['affid'], $core->input['affiliate']) ? 'selected="true"' : '').' value="'.$affiliate['affid'].'">'.$affiliate['name'].'</option>';
		}
		$return.='</select>';
	}
	$suppliers_query = $db->query('SELECT eid,companyName from '.Tprefix.'entities WHERE type=\'s\'');
	if($db->num_rows($suppliers_query) > 0) {
		$return.='<td><select id="supplier" name="supplier[]" multiple="true">';
		while($supplier = $db->fetch_assoc($suppliers_query)) {
			$return.='<option '.(in_array($supplier['eid'], $core->input['supplier']) ? 'selected="true"' : '').' value="'.$supplier['eid'].'">'.$supplier['companyName'].'</option>';
		}
		$return.='</select>';
	}
	$products_query = $db->query('SELECT pid,name from '.Tprefix.'products');
	if($db->num_rows($products_query) > 0) {
		$return.='</td><td><select id="product" name="product[]" multiple="true">';
		while($product = $db->fetch_assoc($products_query)) {
			$return.='<option '.(in_array($product['pid'], $core->input['product']) ? 'selected="true"' : '').' value="'.$product['pid'].'">'.$product['name'].'</option>';
		}
		$return.='</select>';
	}
	$return.='</td></tr><td colspan=2><input type=submit value=Generate></td></tr>';
	$return.='</form></div></td></tr></table>'.'<script>$(document).ready(function(){$(".datepicker" ).datepicker();});</script>';
	return $return;
}

function time_regroup($data, $datecolumn) {
	$timesliced = array();
	foreach($data as $key => $row) {
		foreach($row as $column => $value) {
			if($column == $datecolumn) {
				$timesliced[date('Y', $value)][date('F', $value)][date('W', $value)][] = $row;
			}
		}
	}
	return $timesliced;
}

function regroup_and_sum($data, $groupingattribute, $trackedcolumns = array("amount" => 'numeric'), $resolve = null) {
	$currency_obj = new Currencies('USD');
	$grouped = array();
	foreach($data as $key => $purchase) {
		if($groupingattribute != '') {
			if(isset($resolve)) {
				$name = get_name_from_id($purchase[$groupingattribute], $resolve['table'], $resolve['id'], $resolve['name']);
			}
			else {
				$name = $purchase[$groupingattribute];
			}
		}

		if(isset($grouped[$name])) {
			$id = check_for_presence($grouped[$name], $purchase, $trackedcolumns);
			if($id) {
				foreach($id as $key => $value) {
					$break;
				}
				$grouped[$name][$key]['#StackedRows'] = $grouped[$name][$key]['#StackedRows'] + 1;
				foreach($trackedcolumns as $trackname => $columntype) {
					if($columntype == 'numeric') {
						$grouped[$name][$key][$trackname] += $purchase[$trackname];
					}
					else {
						$grouped[$name][$key][$trackname] = $purchase[$trackname];
					}
				}
			}
			else {
				$counter = 0;
				if(isset($grouped[$name])) {
					foreach($grouped[$name] as $tmp) {
						$counter++;
					}
				}
				$grouped[$name][$counter]['#StackedRows'] = 1;
				foreach($trackedcolumns as $trackname => $columntype) {
					$grouped[$name][$counter][$trackname] = $purchase[$trackname];
				}
			}
		}
		else {
			$counter = 0;
			if(isset($grouped[$name])) {
				foreach($grouped[$name] as $tmp) {
					$counter++;
				}
			}
			$grouped[$name][$counter]['#StackedRows'] = 1;
			foreach($trackedcolumns as $trackname => $columntype) {
				$grouped[$name][$counter][$trackname] = $purchase[$trackname];
			}
		}
	}
	return $grouped;
}

function check_for_presence($haystack, $needle, $filter) {
	$results = array();
	foreach($haystack as $key => $row) {
		$present = true;
		foreach($filter as $column => $type) {
			if($type != 'numeric') {
				if($row[$column] != $needle[$column]) {
					$present = false;
				}
			}
		}
		if($present) {
			$results[$key] = 1;
		}
	}
	return $results;
}

function resolve_names($data, $resolverules) {
	foreach($data as $key => $row) {
		foreach($row as $column => $value) {
			if(isset($resolverules[$column])) {
				$data[$key][$column] = get_name_from_id($value, $resolverules[$column]['table'], $resolverules[$column]['id'], $resolverules[$column]['name']);
			}
		}
	}
	return $data;
}

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

function retrieve_data($query, $trackedcolumns = array("amount")) {
	global $db;
	$purchase_report = $db->query($query);
	$dataarray = array();
	if($db->num_rows($purchase_report) > 0) {
		$counter = 0;
		while($purchase = $db->fetch_assoc($purchase_report)) {
			foreach($trackedcolumns as $column) {
				$dataarray[$counter][$column] = $purchase[$column];
			}
			$counter++;
		}
	}
	return $dataarray;
}

function generate_stock_reports_email_data($data) {
	$align = "left";
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
			if($columnid == 'date')
				$value = date('m-d-Y', $value);
			$content.=$td.$value.'</td>';
		}
		$content.='</tr>';
	}
	$content.='</table>';
	return $content;
}

function encapsulate_in_fieldset($html, $legend = "+", $boolStartClosed = false) {
	$id = md5(rand(9, 99999).time());

	$start_js_val = 1;
	$fsstate = "open";
	$content_style = "";

	if($boolStartClosed) {
		$start_js_val = 0;
		$fsstate = "closed";
		$content_style = "display: none;";
	}

	$js = "<script type='text/javascript'>

  var fieldset_state_$id = $start_js_val;

  function toggle_fieldset_$id() {

    var content = document.getElementById('content_$id');
    var fs = document.getElementById('fs_$id');

    if (fieldset_state_$id == 1) {
      // Already open.  Let's close it.
      fieldset_state_$id = 0;
      content.style.display = 'none';
      fs.className = 'c-fieldset-closed-$id';
    }
    else {
      // Was closed.  let's open it.
      fieldset_state_$id = 1;
      content.style.display = '';
      fs.className = 'c-fieldset-open-$id';
    }
  }
  </script><noscript><b>This page contains collapsible fieldsets which require Javascript to function properly.</b></noscript>";

	$rtn = "
    <fieldset class='c-fieldset-$fsstate-$id' id='fs_$id'>
      <legend><a href='javascript: toggle_fieldset_$id();'>$legend</a></legend>
      <div id='content_$id' style='$content_style'>
        $html
      </div>
    </fieldset>
    $js

  <style>
  fieldset.c-fieldset-open-$id {
    border: 1px solid;
  }

  fieldset.c-fieldset-closed-$id {
    border: 2px solid;
    border-bottom-width: 0;
    border-left-width: 0;
    border-right-width: 0;
  }
  </style>

  ";
	return $rtn;
}

?>