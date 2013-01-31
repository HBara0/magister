<?php
//<editor-fold defaultstate="collapsed" desc="All">
/*
 * Orkila Central Online System (OCOS)
 * Copyright Â© 2010 Orkila International Offshore, All Rights Reserved
 * Import Stock
 * $module: Stock
 * Created		@zaher.reda 		September 7, 2012 | 3:41 PM
 * Last Update: 	@alain.paulikevitch 		October 27, 2012 | 3:41 PM
 */
//<editor-fold defaultstate="collapsed" desc="main code of the php file">

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

//<editor-fold defaultstate="collapsed" desc="init">
$session->start_phpsession();
$lang->load('stock_meta');
$performance["--START--"] = microtime();
$perflogcount = 0;
$doresolve=true;
$jq_common = false;
$jq_pie = false;
$jq_chart = false;
$jq_bar = false;
$allcolumns = array(
		'affid' => 'id',
		'spid' => 'id',
		'pid' => 'id',
		'currency' => 'text',
		'usdFxrate' => 'numeric',
		'quantity' => 'numeric',
		'quantityUnit' => 'text',
		'date' => 'date',
		'saleType' => 'text',
		'TRansID' => 'text',
		'amount' => 'numeric'
);

$resolve = array(
		'affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'),
		'spid' => array('table' => 'entities', 'id' => 'eid', 'name' => 'companyName'),
		'pid' => array('table' => 'products', 'id' => 'pid', 'name' => 'name'),
		//'currency' => array('table' => 'currencies', 'id' => 'numCode', 'name' => 'alphaCode')
);

//</editor-fold>
if($core->usergroup['stock_canGenerateReports'] == '1') {
	$content = encapsulate_in_fieldset(make_filters($db, $core), 'Filter', false);
	$content .= encapsulate_in_fieldset(make_options(), 'Options', false);
	$content .= encapsulate_in_fieldset(choose_columns(), 'Columns', false);
	if($core->input['action'] == 'generatedummy') {
		//<editor-fold defaultstate="collapsed" desc="populate the database with random entry">
		$howmany = 100;
		if(isset($core->input['number'])) {
			if(is_numeric($core->input['number'])) {
				$howmany = $core->input['number'];
			}
		}
		random_fill_for_testing($howmany);
		//</editor-fold>
	}
	elseif($core->input['action'] == 'emailreport') {
		//<editor-fold defaultstate="collapsed" desc="send a report by email">
		//$affiliate = $core->input['affiliate_id'];
		$query = assemble_filter_query($core, $db);
		//$queryorg = weak_decrypt($core->input['data_filter'], 'orkilakey');
		//echo "<b>Constructed query:</b><br>".$query.'<br><b>Original query</b>s<br>'.$queryorg.'<br>';
		$rawdata = resolve_names(retrieve_data($query, $allcolumns), $resolve);
		$groupingatr = $core->input['grouping_atr'];

		if(!isset($groupingatr))
			$groupingatr = 'pid';
		$rawdata = resolve_names($rawdata, $resolve);
		foreach($allcolumns as $column => $type) {
			if($core->input[$column] == '1') {
				$trackedcolumns[$column] = $allcolumns[$column];
			}
		}
		$trackedcolumns['amount'] = $allcolumns['amount'];
		if($core->input['reporttype'] == 1) {
			$timesliced = time_regroup($rawdata, 'date');
			foreach($timesliced as $year => $yearly) {
				foreach($yearly as $month => $monthly) {
					foreach($monthly as $week => $weekly) {
						unset($timesliced[$year][$month][$week]);
						$timesliced[$year][$month][$week] = sort_by_amount(regroup_and_sum($weekly, $core->input['fxratetype'], $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null));
					}
				}
			}
			$datapresented = turn_data_into_html($timesliced, true, $trackedcolumns, $groupingatr, false);
		}
		else {
			$summeddata = regroup_and_sum($rawdata, $core->input['fxratetype'], $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null);
			$datapresented = turn_data_into_html($summeddata, false, $trackedcolumns, $groupingatr, false);
			$datapresented .= turn_data_into_html(sort_by_amount($summeddata), false, $trackedcolumns, $groupingatr, false);
		}
		send_mail(get_affiliate_gm_email($core->input['affiliate'][0]), $datapresented, get_name_from_id($core->input['affiliate'][0], 'affiliates', 'affid', 'name'));
		//</editor-fold>
	}
	elseif($core->input['action'] == 'getreport') {
		//<editor-fold defaultstate="collapsed" desc="generate a report">
		$query = assemble_filter_query($core, $db);
		//$content.=encapsulate_in_fieldset($query, 'Query');
		if(isset($core->input['affiliate'])) {
			if(count($core->input['affiliate']) == 1) {
				$content .= encapsulate_in_fieldset(email_report($core->input['affiliate'][0], $query), 'Send as e-mail', false);
			}
		}

		$rawdata = retrieve_data($query, $allcolumns);
		$trackedcolumns = array();
		$groupingatr = $core->input['groupingattribute'];
		if(!isset($groupingatr))
			$groupingatr = 'pid';
		$rawdata = resolve_names($rawdata, $resolve);
		foreach($allcolumns as $column => $type) {
			if($core->input[$column] == '1') {
				$trackedcolumns[$column] = $allcolumns[$column];
			}
		}
		$trackedcolumns['amount'] = $allcolumns['amount'];
		if($core->input['reporttype'] == 1) {
			$timesliced = time_regroup($rawdata, 'date');
			foreach($timesliced as $year => $yearly) {
				foreach($yearly as $month => $monthly) {
					foreach($monthly as $week => $weekly) {
						unset($timesliced[$year][$month][$week]);
						$timesliced[$year][$month][$week] = sort_by_amount(regroup_and_sum($weekly, $core->input['fxratetype'], $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null));
					}
				}
			}
			$datapresented = encapsulate_in_fieldset(turn_data_into_html($timesliced, true, $trackedcolumns, $groupingatr), 'Grouped', false);
		}
		else {
			$summeddata = sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'], $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null), 0);
			$datapresented = encapsulate_in_fieldset(turn_data_into_html($summeddata, false, $trackedcolumns, $groupingatr), 'Grouped', false);
		}

		$charts = '<div style="position:relative;"><div id="general_chart" style="margin-left:5px;margin-bottom:10px;">'.make_jqpchart(regroup_by_day(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'])).'</div>';
		$charts.='<div id="affiliate_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'], 'affid', array('amount' => 'numeric'), $resolve['affid'])), 'affiliate_pie', 'Top 10 Affiliates').'</div>';
		$charts.='<div id="supplier_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'], 'spid', array('amount' => 'numeric'), $resolve['spid'])), 'supplier_pie', 'Top 10 Suppliers').'</div>';
		$charts.='<div id="product_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'], 'pid', array('amount' => 'numeric'), $resolve['pid'])), 'product_pie', 'Top 10 Products').'</div></div>';
		$charts = encapsulate_in_fieldset($charts, "Charts", false);
		$performance["--END--"] = microtime();
		if($core->input['isajax'] == 'true') {
			output_xml($datapresented.$charts.get_perf_data());
			exit;
		}
		else {
			$content.='<div id="results_fieldset">'.$datapresented.$charts.get_perf_data().'</div>';
		}
		//</editor-fold>
	}
	elseif($core->input['action'] == 'test') {
		//<editor-fold defaultstate="collapsed" desc="print a full list">
		$query = assemble_filter_query($core, $db)." ORDER BY Date DESC limit 10";
		$trackedcolumns = $allcolumns;
		$trackedcolumns ['imspid'] = "id";
		$rawdata = retrieve_data($query, $allcolumns);
		$groupingatr = "imspid";
		$rawdata = resolve_names($rawdata, $resolve);
		$summeddata = regroup_and_sum(convert_to_dollars($rawdata, $core->input['fxratetype']), $core->input['fxratetype'], $groupingatr, $trackedcolumns, null);

		unset($allcolumns['currency']);
		unset($allcolumns['usdFxrate']);
		unset($allcolumns['saleType']);
		unset($allcolumns['TRansID']);

		$datapresented = encapsulate_in_fieldset(turn_data_into_html($summeddata, false, $allcolumns, $groupingatr), 'Bulk', false);
		//$performance["--END--"] = microtime();
		$content = '<div id="results_fieldset">'.$datapresented.'</div>';
		//</editor-fold>
	}
	elseif($core->input['action'] == 'printlist') {
		//<editor-fold defaultstate="collapsed" desc="print a dynamic list">

		$affiliates = getAffiliatesList(true);
		$suppliers = getSuppliersList(true);
		$products = getProductsList($suppliers, true);

		$filter_where = ' '.Tprefix.'integration_mediation_stockpurchases.affid IN ('.implode(',', $affiliates).')';
		$filter_where.=' AND '.Tprefix.'integration_mediation_stockpurchases.spid IN ('.implode(',', $suppliers).')';
		$filter_where.=' AND '.Tprefix.'integration_mediation_stockpurchases.pid IN ('.implode(',', $products).')';

		$limit_start = 0;
		$sort_query = ' date DESC';
		$multipage_where = $filter_where;

		$sort_url = sort_url();
		if(isset($core->input['start'])) {
			$limit_start = $db->escape_string($core->input['start']);
		}
		if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
			$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
		}
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
		}

		$named_suppliers = getSuppliersList();
		$named_products = getProductsList($named_suppliers);
		$pid_autocomplete = "";
		foreach($named_products as $key => $value) {
			$pid_autocomplete .= '{ label: "'.$value.'", value: "'.$value.'" },';
		}
		$pid_autocomplete = substr($pid_autocomplete, 0, strlen($pid_autocomplete) - 1);
		$filters_config = array(
				'parse' => array(
							'filters' => array('product', 'spid', 'affid', 'date', 'amount'),
							'overwriteField' => array(
								'spid' =>parse_selectlist('filters[spid][]', 2, $named_suppliers, $core->input['filters']['spid'], 1, '', array('multiplesize' => 3, 'id' => 'spid')),
								'product' => '<input id="product_QSearch" type="text" title="" autocomplete="off" value="" tabindex="1" name="filters[product]"/><div id="searchQuickResults_product_product" class="searchQuickResults" style="display:none;"></div>',
							),
						),
				'process' => array(
						'filterKey' => 'imspid',
						'mainTable' => array(
								'name' => 'integration_mediation_stockpurchases',
								'filters' => array('spid' => array('operatorType' => 'multiple', 'name' => 'spid'), 'affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'date' => array('operatorType' => 'date', 'name' => 'date'), 'amount' => array('operatorType' => 'startswith', 'name' => 'amount')),
						),
						'secTables' => array(
								'products' => array(
										'keyAttr' => 'pid',
										'joinKeyAttr' => 'pid',
										'joinWith' => 'integration_mediation_stockpurchases',
										'filters' => array('product' => array('operatorType' => 'startswith', 'name' => 'products.name')),
								),
						),
				),
		);

		$filter = new Inlinefilters($filters_config);
		$filter_where_values = $filter->process_multi_filters();
		$filters_row_display = 'hide';

		if(is_array($filter_where_values)) {
			$filters_row_display = 'show';
			if(count($filter_where_values) > 0) {
				$filter_where .= ' AND '.Tprefix.'integration_mediation_stockpurchases.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
				$multipage_where .= ' AND '.Tprefix.'integration_mediation_stockpurchases.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
			}
		}

		$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

		$query = "SELECT * FROM ".Tprefix."integration_mediation_stockpurchases
					JOIN ".Tprefix."products ON (".Tprefix."products.pid=".Tprefix."integration_mediation_stockpurchases.pid)
					WHERE $filter_where
					ORDER BY {$sort_query}
					LIMIT {$limit_start}, {$core->settings[itemsperlist]}"; // WHERE {$filter_where}
		//$purchases_list = encapsulate_in_fieldset($query);

		$query = $db->query($query);

		if($db->num_rows($query) > 0) {
			while($purchase = $db->fetch_assoc($query)) {
				$purchases_list .= '<tr><td>'.get_name_from_id($purchase['pid'], $resolve["pid"]["table"], $resolve["pid"]["id"], $resolve["pid"]["name"], false)
						.'</td><td>'.get_name_from_id($purchase['spid'], $resolve["spid"]["table"], $resolve["spid"]["id"], $resolve["spid"]["name"], false)
						.'</td><td>'.get_name_from_id($purchase['affid'], $resolve["affid"]["table"], $resolve["affid"]["id"], $resolve["affid"]["name"], false)
						.'</td><td>'.date($settings['dateformat'], $purchase['date'])
						.'</td><td>'.number_format($purchase['amount'], 2, '.', ',')
						.'</td></tr>';
			}
			$multipages = new Multipages('integration_mediation_stockpurchases', $core->settings['itemsperlist'], $multipage_where);
			$purchases_list .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';
		}
		else {
			$purchases_list .= '<tr><td colspan="6">'.$lang->nomatchfound.'</td></tr>';
		}

		if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
			$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
		}
		else {
			if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 1) {
				$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
			}
		}

		if(is_array($affiliates)) {
			$affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0, 'goToURL("index.php?module=hr/holidayslist&amp;affid="+$(this).val())').'';
		}
		eval("\$list = \"".$template->get('stocks_purchaselist')."\";");
		output_page($list);
		die();
		//</editor-fold>
	}
}
else {
	error($lang->sectionnopermission);
}
eval("\$report_template = \"".$template->get('stock_purchasereport')."\";");
output_page($report_template);
//</editor-fold>

function convert_to_dollars($rawdata, $mode) {
	log_performance(__METHOD__);
	global $db;
	//$usdcode = $db->fetch_field($db->query('SELECT numCode FROM '.Tprefix.'currencies WHERE alphaCode="USD"'), 'numCode');
	foreach($rawdata as $key => $value) {
		$rate = get_fx_rate($mode, $value['usdFxrate']['value'], $value['currency']['value'], $value['date']['value']);
		$rawdata[$key]['amount']['value'] = (float)$value['amount']['value'] * $rate;
		$rawdata[$key]['usdFxrate']['value'] = 1;
		$rawdata[$key]['currency']['value'] = 'USD';
	}
	return $rawdata;
}

function sort_by_amount($data, $numberofrows = 10) {
	log_performance(__METHOD__);

	if($numberofrows == 0)
		$numberofrows = count($data) + 1;

	for($i = 0; $i < count($data) && ($i < $numberofrows - 1); $i++) {
		$biggest = null;
		foreach($data as $key => $row) {
			if(!isset($processed[$key])) {
				if(isset($biggest)) {
					$rowvalue = 0;
					foreach($row as $rkey => $rvals) {
						if(is_numeric($rkey)) {
							$rowvalue+=(float)$rvals['amount']['value'];
						}
					}
					$bigvalue = 0;
					foreach($data[$biggest] as $bkey => $bvals) {
						if(is_numeric($bkey)) {
							$bigvalue+=(float)$bvals['amount']['value'];
						}
					}

					if($rowvalue > $bigvalue) {
						$biggest = $key;
					}
				}
				else {
					$biggest = $key;
				}
			}
		}
		if(isset($biggest)) {
			$processed[$biggest] = 1;
			$sorted[''.$biggest] = $data[$biggest];
		}
	}
	$label = 'Other';
	$sorted[$label] = array('#name' => 'Other', 0 => array('#StackedRows' => 0, 'amount' => array('value' => 0)));
	$gotfiller = false;
	foreach($data as $key => $row) {
		if(!isset($processed[$key])) {
			foreach($row as $rkey => $rvals) {
				if(is_numeric($rkey)) {
					$sorted[$label][0]['#StackedRows']+=$rvals['#StackedRows'];
					$sorted[$label][0]['amount']['value']+=(float)$rvals['amount']['value'];
				}
			}
			if(!$gotfiller) {
				foreach($row[0] as $key2 => $value) {
					if($key2 != '#StackedRows' && $key2 != 'amount') {
						$sorted[$label][0][$key2]['value'] = '-NA-';
						$sorted[$label][0][$key2]['name'] = '-NA-';
					}
				}
				$gotfiller = true;
			}
		}
	}

	if($sorted[$label][0]['#StackedRows'] == 0)
		unset($sorted[$label]);
	return $sorted;
}

function make_jqlinechart($data) {
	log_performance(__METHOD__);
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);
	global $jq_common, $jq_bar, $jq_pie;
	$includes = '';
	if(!$jq_common) {
		$includes .= '	<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
						<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
						<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />';
		$jq_common = true;
	}
	if(!$jq_chart) {
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.cursor.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.canvasOverlay.min.js"></script>';
		$jq_chart = true;
	}


	$function = '<div id="jqPerfChart" style="width:480px;height:360px;padding:5px;"></div><script>
				$(document).ready(function(){
				var dataPoints=[];';

	$counter = 0;
	foreach($data as $key => $row) {
		if(is_array($row)) {
			$total = 0;
			$timing = 0;
			foreach($row as $key => $value) {
				if(is_array($value)) {
					$total+=(float)$value['amount']['value'];
					$timing+=(float)$value['timing']['value'];
				}
			}
			$function .= 'dataPoints.push(['.$timing.','.number_format($total, 2, '.', '').',"'.$row['#name'].'"]);';
		}
	}

	/*
	  pointLabels:{
	  show:true,
	  location:"n",
	  ypadding:3,
	  labels: ticks
	  },
	 */
	$function.='
	var plot = $.jqplot("jqPerfChart", [dataPoints],
	{
		highlighter: {
			show: true,
			yvalues: 2,
			formatString:"x:%d y:%d   %s",
		},
		cursor: {
			show:true,
			zoom:true,
			dblClickReset: true,
		},
		seriesDefaults: {
			showMarker:true,
			rendererOptions: {smooth: false},
		},
		axes:{
			xaxis:{
				padMin: 0,
				label:"Time",
			},
			yaxis:{
				padMin: 0,
			},
		},
});
});';

	$function.='</script>';

	return $includes.$function;
}

function make_jqbarchart($data) {
	log_performance(__METHOD__);
	global $jq_common, $jq_bar, $jq_pie;
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);
	$includes = '';
	if(!$jq_common) {
		$includes .= '	<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
						<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
						<script type="text/javascript" src="'.$baseurl.'inc/jQplot/excanvas.min.js"></script>
						<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />';
		$jq_common = true;
	}
	if(!$jq_bar) {
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.cursor.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.barRenderer.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.pointLabels.min.js"></script>
				<style>
					.jqplot-cursor-tooltip {
						background-color:#E2F2A2;
						padding:2px;
					}
				</style>
			';
		$jq_bar = true;
	}

	$function = '<div id="bchart1" style="width:400px;sheight:500px;vertical-align:top;overflow: hidden;"></div>
		<script>
		$(document).ready(function(){
			var s1 = [';

	$ticks = 'var ticks = [';
	foreach($data as $key => $row) {
		if(is_array($row)) {
			$total = 0;
			foreach($row as $key => $value) {
				if(is_array($value)) {
					if($value['amount']['value'] < 0) {
						$total-=(float)$value['amount']['value'];
					}
					else {
						$total+=(float)$value['amount']['value'];
					}
				}
			}
			$function .= '["'.$row['#name'].'",'.number_format($total, 2, '.', '').'],';
			$ticks.='["'.$row['#name'].'"],';
		}
	}

	$ticks = substr($ticks, 0, strlen($ticks) - 1);
	$ticks .='];';
	$function = substr($function, 0, strlen($function) - 1);
	$function .='];';

	$function.=$firstvar.$secondvar.'
		plot1 = $.jqplot(\'bchart1\', [s1], {
			animate: !$.jqplot.use_excanvas,
			seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
				shadowAngle: 135,
				showHighlight: true,
				rendererOptions: {
					barWidth: 5,
                    barPadding: 2,
                    barMargin: 2,
		        },

            },
            axes: {
				xaxis: {
					renderer: $.jqplot.CategoryAxisRenderer,
					numberTicks: 3,
					showTicks: 1
				},
				yaxis: {
					padMin: 0,
				}
            },
			cursor:{
                show: true,
                zoom:true,
				clickReset: true,
                showTooltip:true,
                followMouse: false,
				tooltipLocation: "se",
				tooltipAxes: "both",
				formatString: \'%d\',
			},
        });
    });
	</script>';
	return $includes.$function;
}

function make_jqppiechart($data, $id = "jqpieid", $title = '', $margin = 0, $startangle = 0, $divwidth = '230px', $divheight = '230px', $fill = true) {
	log_performance(__METHOD__);
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);
	global $jq_common, $jq_bar, $jq_pie;
	$includes = '';
	if(!$jq_common) {
		$includes .= '	<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
						<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
						<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />';
		$jq_common = true;
	}
	if(!$jq_pie) {
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.pieRenderer.min.js"></script>
					<style>
						.jqplot-highlighter-tooltip {
							top:18px !important;
							left:12px !important;
							background-color:#E2F2A2;
							padding:2px;
						}
					</style>';
		$jq_pie = true;
	}

	$function = '<div id="'.$id.'" style="width:'.$divwidth.';height:'.$divheight.';vertical-align:top;overflow: hidden;"></div><script>
	$(document).ready(function(){
	 plot_'.$id.' = jQuery.jqplot(\''.$id.'\',[[';

	foreach($data as $key => $row) {
		if(is_array($row)) {
			$total = 0;
			foreach($row as $key => $value) {
				if(is_array($value)) {
					if($value['amount']['value'] < 0) {
						$total-=(float)$value['amount']['value'];
					}
					else {
						$total+=(float)$value['amount']['value'];
					}
				}
			}
			if($total < 0) {
				$total = -$total;
			}
			$function.='["'.$row['#name'].'",'.number_format($total, 2, '.', '').'],';
		}
	}
	$function.=']],
                 {
                    title: "'.$title.'",
                    seriesDefaults: {
                        shadow: true,
                        renderer: jQuery.jqplot.PieRenderer,
                        rendererOptions: {
                            showDataLabels: true,
							fill: '.($fill ? 'true' : 'false').',
							sliceMargin: '.$margin.',
							startAngle: '.$startangle.',
							lineWidth: 2
                        }
                    },
                    legend: {
                        show:false
                    },
                    highlighter: {
                        show: true,
                        formatString:\'%s: %P\',
                        tooltipLocation:\'sw\',
                        useAxesFormatters:false
                    }
                }
            );


});
</script>';
	return $includes.$function;
}

function reduce_density_by_grouping($data, $maxtarget) {
	log_performance(__METHOD__);
	$first = true;
	foreach($data as $date => $value) {
		if($first) {
			$min = $date;
			$max = $date;
			$first = false;
		}
		if($date < $min) {
			$min = $date;
		}
		if($date > $max) {
			$max = $date;
		}
	}
	$step = ($max - $min) / $maxtarget;
	$previous = 0;
	for($i = 0; $i < $maxtarget; $i++) {
		$currentstamp = (int)($min + $step * $i);
		$nextstep = (int)($min + $step * ($i + 1));
		$newdata[$currentstamp] = $previous;
		foreach($data as $tdate => $value) {
			if($tdate >= $currentstamp && $tdate < $nextstep) {
				$newdata[$currentstamp]+=(float)$value;
			}
		}
		$previous = $newdata[$currentstamp];
	}
	return $newdata;
}

function make_jqpchart($data) {
	log_performance(__METHOD__);
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);
	global $jq_common, $jq_bar, $jq_pie;
	$includes = '';
	if(!$jq_common) {
		$includes .= '	<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
						<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
						<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />';
		$jq_common = true;
	}
	if(!$jq_chart) {
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.cursor.min.js"></script>
					<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>';
		$jq_chart = true;
	}


	$function = '<div id="jqChart" style="width:670px;height:300px;padding:10px;"></div><script>
				$(document).ready(function(){
				var dataPoints = [];';
	$data = reduce_density_by_grouping($data, 100);
	foreach($data as $date => $value) {
		if(is_numeric($value)) {
			$function.='dataPoints.push(["'.date('Y-m-d', $date).'",'.number_format($value, 2, '.', '').']);';
		}
		else {
			$function.='dataPoints.push(["'.date('Y-m-d', $date).'",'.$value.']);';
		}
	}
	$function.='
	var plot = $.jqplot("jqChart", [dataPoints],
	{
		highlighter: {show: true},
		cursor: {show:true,zoom:true,clickReset: true},
		seriesDefaults: {showMarker:false,rendererOptions: {smooth: false}},
		axes:{
			xaxis:{
				renderer:$.jqplot.DateAxisRenderer,
				rendererOptions:{
                    tickRenderer:$.jqplot.CanvasAxisTickRenderer
                },
				tickOptions:{
                    fontSize:"8pt",
                    fontFamily:"Tahoma",
                    angle:-40
                }
			},
			yaxis:{
				rendererOptions:{
					tickRenderer:$.jqplot.CanvasAxisTickRenderer
				},
                tickOptions:{
					fontSize:"8pt",
                    fontFamily:"Tahoma",
					formatString: ""
                }
		}
	}
});


//jqplotToImg("jqChart");

});';


	/* // function that exports jquery graph to an image
	  $function.='function jqplotToImg(objId) {
	  // first we draw an image with all the chart components
	  var newCanvas = document.createElement("canvas");
	  newCanvas.width = $("#" + objId).width();
	  newCanvas.height = $("#" + objId).height();
	  var baseOffset = $("#" + objId).offset();

	  $("#" + objId).children().each(
	  function() {
	  // for the div\'s with the X and Y axis
	  if ($(this)[0].tagName.toLowerCase() == \'div\') {
	  // X axis is built with canvas
	  $(this).children("canvas").each(
	  function() {
	  var offset = $(this).offset();
	  newCanvas.getContext("2d").drawImage(this,
	  offset.left - baseOffset.left,
	  offset.top - baseOffset.top);
	  });
	  // Y axis got div inside, so we get the text and draw it on
	  // the canvas
	  $(this).children("div").each(
	  function() {
	  var offset = $(this).offset();
	  var context = newCanvas.getContext("2d");
	  context.font = $(this).css(\'font-style\') + " "
	  + $(this).css(\'font-size\') + " "
	  + $(this).css(\'font-family\');
	  context.fillText($(this).html(), offset.left
	  - baseOffset.left, offset.top
	  - baseOffset.top + 10);
	  });
	  }
	  // all other canvas from the chart
	  else if ($(this)[0].tagName.toLowerCase() == \'canvas\') {
	  var offset = $(this).offset();
	  newCanvas.getContext("2d").drawImage(this,
	  offset.left - baseOffset.left,
	  offset.top - baseOffset.top);
	  }
	  });

	  // add the point labels
	  $("#" + objId).children(".jqplot-point-label").each(
	  function() {
	  var offset = $(this).offset();
	  var context = newCanvas.getContext("2d");
	  context.font = $(this).css(\'font-style\') + " "
	  + $(this).css(\'font-size\') + " "
	  + $(this).css(\'font-family\');
	  context.fillText($(this).html(), offset.left - baseOffset.left,
	  offset.top - baseOffset.top + 10);
	  });

	  // add the rectangles
	  $("#" + objId + " *").children(".jqplot-table-legend-swatch").each(
	  function() {
	  var offset = $(this).offset();
	  var context = newCanvas.getContext("2d");
	  context.setFillColor($(this).css(\'background-color\'));
	  context.fillRect(offset.left - baseOffset.left, offset.top
	  - baseOffset.top, 15, 15);
	  });

	  // add the legend
	  $("#" + objId + " *").children(".jqplot-table-legend td:last-child").each(
	  function() {
	  var offset = $(this).offset();
	  var context = newCanvas.getContext("2d");
	  context.font = $(this).css(\'font-style\') + " "
	  + $(this).css(\'font-size\') + " "
	  + $(this).css(\'font-family\');
	  context.fillText($(this).html(), offset.left - baseOffset.left,
	  offset.top - baseOffset.top + 15);
	  });

	  window.open(newCanvas.toDataURL(), "directories=no");
	  }'; */
	$function.='</script>';

	return $includes.$function; //.'<pre>'.print_r($data, true).'</pre>';
}

function make_pchart($data) {
	log_performance(__METHOD__);
	chdir('inc');
	include('pChart\pChart.class');
	include('pChart\pData.class');
	$DataSet = new pData();
	$formateddata = array();

	foreach($data as $key => $value) {
		$formateddata[str_replace('-', '', $key)] = $value;
	}
	if(count($formateddata) > 0)
		$DataSet->AddPoint($formateddata);
	else
		return '';
	$DataSet->AddSerie('Serie1');
	$DataSet->SetSerieName("Raw data", "Serie1");

	$Test = new pChart(700, 230);
	$Test->setFontProperties('Fonts/tahoma.ttf', 10);
	$Test->setGraphArea(40, 30, 680, 200);
	$Test->drawGraphArea(252, 252, 252);
	$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
	$Test->drawGrid(4, TRUE, 230, 230, 230, 255);

	$Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
	$Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 255, 255, 255);

	$Test->setFontProperties("Fonts/tahoma.ttf", 8);
	$Test->drawLegend(45, 35, $DataSet->GetDataDescription(), 255, 255, 255);
	$Test->setFontProperties("Fonts/tahoma.ttf", 10);
	$Test->drawTitle(60, 22, "Purchases Value", 50, 50, 50, 585);
	$Test->Render("chart.png");
	return '<img src="inc/chart.png"/>'; //.'<hr><pre>'.print_r($formateddata, true).'</pre>';
}

function generate_stock_reports_email_data($data) {
	log_performance(__METHOD__);

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
				$value['value'] = date($core->settings['dateformat'], $value['value']);
			$content.=$td.$value['value'].'</td>';
		}
		$content.='</tr>';
	}
	$content.='</table>';
	return $content;
}

function turn_data_into_html($data, $timesliced = false, $trackedcolumns, $groupingcol = "pid", $stack = true) {
	log_performance(__METHOD__);
	//<editor-fold defaultstate="collapsed" desc="variables">
	$nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';

	$align = "right";
	$rth = '<th style="padding: 5px;padding-left:10px;padding-right:10px; border-bottom: 1px solid black; background-color:#92D050;text-align:'.$align.';">';
	$rtd0 = '<td style="border-bottom: 1px dashed #888888;background-color:#FFFFFF;padding-'.$align.':10px;text-align:'.$align.';">';
	$rtd1 = '<td style="border-bottom: 1px dashed #888888;background-color:#F7FAFD;padding-'.$align.':10px;text-align:'.$align.';">';
	$rtd2 = '<td style="border-bottom: 1px dashed #888888;background-color:#E1E1E1;padding-'.$align.':10px;text-align:'.$align.';">';


	$align = "left";
	$tr = '<tr  style="text-align: '.$align.'; padding: 5px; border-bottom: 1px dashed #888888;">';
	$th = '<th style="padding: 5px;padding-left:10px;padding-right:10px; border-bottom: 1px solid black; background-color:#92D050;text-align:'.$align.';">';
	$thstack = '<th style="padding: 5px;padding-left:10px;padding-right:10px; border-bottom: 1px solid black; background-color:#92D050;border-left:1px solid black;text-align:right;">';
	$td0 = '<td style="border-bottom: 1px dashed #888888;background-color:#FFFFFF;padding-'.$align.':10px;">';
	$td1 = '<td style="border-bottom: 1px dashed #888888;background-color:#F7FAFD;padding-'.$align.':10px;">';
	$td2 = '<td style="border-bottom: 1px dashed #888888;background-color:#E1E1E1;padding-'.$align.':10px;">';
	$tdstack = '<td style="border-bottom: 1px dashed #888888;border-left:1px solid black;text-align:right;padding-right:10px;">';
	$grouptd1 = '<td style="border-bottom: 1px dashed #888888;background-color:#F7FAFD;padding-'.$align.':10px;" ';
	$grouptd2 = '<td style="border-bottom: 1px dashed #888888;background-color:#E1E1E1;padding-'.$align.':10px;" ';

	$toggle = false;
	$togglegroup = true;
	global $core, $lang, $resolve;
	$rowcount = 2;
	//</editor-fold>
	//<editor-fold defaultstate="collapsed" desc="tablehead">
	$head = '<tr align="left">'.$th.$lang->{$groupingcol}.'</th>';
	foreach($trackedcolumns as $colid => $coltype) {
		if($colid == 'amount') {
			$head.=$rth.$lang->{$colid}.'</th>';
		}
		else {
			$head.=$th.$lang->{$colid}.'</th>';
		}

		$rowcount+=1;
	}

	if($stack) {
		$head.=$thstack.'Stacked</th></tr>';
	}
	else {
		$rowcount-=1;
		$head.='</tr>';
	}
	//</editor-fold>
	if(!$timesliced) {
		//<editor-fold defaultstate="collapsed" desc="not detailed">
		//echo '<pre>'.print_r($data,true).'</pre>';
		$html = '<table style="text-align: '.$align.'; padding:0px;margin:5px;width:100%; font-size: inherit; border-bottom: 1px solid black; border-right: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;"  cellpadding="4" cellspacing="0" >'.$head;
		$totalstack = 0;
		$totalamount = 0;
		foreach($data as $groupingkey => $values) {
			$idneedsadding = true;
			foreach($values as $key => $row) {
				if(is_numeric($key)) {
					$html.=$tr;
					if($idneedsadding) {
						$name = $values['#name']; //get_name_from_id($groupingkey, $resolve[$groupingcol]['table'], $resolve[$groupingcol]['id'], $resolve[$groupingcol]['name']);
						if($name == '-NA-') {
							if($togglegroup) {
								$html.=$grouptd1.' rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
							}
							else {
								$html.=$grouptd2.' rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
							}
						}
						else {
							if($togglegroup) {
								$html.=$grouptd1.' rowspan="'.(count($values) - 1).'">'.$name.'</td>';
							}
							else {
								$html.=$grouptd2.' rowspan="'.(count($values) - 1).'">'.$name.'</td>';
							}
						}
						$togglegroup = !$togglegroup;
						$idneedsadding = false;
						$toggle = false;
					}

					if($togglegroup) {
						if($toggle = !$toggle) {
							$td = $td2;
							$rtd = $rtd2;
						}
						else {
							$td = $td0;
							$rtd = $rtd0;
						}
					}
					else {
						if($toggle = !$toggle) {
							$td = $td1;
							$rtd = $rtd1;
						}
						else {
							$td = $td0;
							$rtd = $rtd0;
						}
					}
					foreach($trackedcolumns as $column => $coltype) {
						switch($column) {
							case 'date':
								if(isset($row[$column]['name'])) {
									if(is_numeric($row[$column]['name'])) {
										$html.=$td.date($core->settings['dateformat'], $row[$column]['name'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['name']). */'</td>';
									}
									else {
										$html.=$td.$row[$column]['name'].'</td>';
									}
								}
								else {
									if(is_numeric($row[$column]['value'])) {
										$html.=$td.date($core->settings['dateformat'], $row[$column]['value'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['value']). */'</td>';
									}
									else {
										$html.=$td.$row[$column]['value'].'</td>';
									}
								}
								break;
							case 'amount':
								$totalamount+=(float)$row[$column]['value'];
								$html.=$rtd.number_format($row[$column]['value'], 2, '.', ',').'</td>';
								break;
							default:
								if(isset($row[$column]['name'])) {
									$html.=$td.$row[$column]['name'].'</td>';
								}
								else {
									$html.=$td.$row[$column]['value'].'</td>';
								}
								break;
						}
					}
					if($stack) {
						$html.=$tdstack.$row['#StackedRows'].'</td>';
					}
					$html.='</tr>';
					$totalstack+=$row['#StackedRows'];
				}
			}
		}
		if($stack) {
			$html.='</td></tr><tr>'.($rowcount > 1 ? '<td colspan="'.($rowcount - 2).'" style="text-align:'.$align.';padding-left:10px;">Total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($totalamount, 2, '.', ',').'</b></td>' : '').'<td style="border-left:1px solid black;text-align:right;padding-right:10px;"><b>'.$totalstack.'</b></td></tr></table>';
		}
		else {
			$html.='</td></tr><tr>'.($rowcount > 1 ? '<td colspan="'.($rowcount - 1).'" style="text-align:'.$align.';padding-left:10px;">Total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($totalamount, 2, '.', ',').'</b></td>' : '').'</tr></table>';
		}
		//</editor-fold>
	}
	else {
		//<editor-fold defaultstate="collapsed" desc="detailed">
		$html = '<table style="text-align: '.$align.'; padding:0px;margin:5px;width:100%; font-size: inherit; border-bottom: 1px solid black; border-right: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;"  cellpadding="4" cellspacing="0" >'.$head;
		$grandtotal = 0;
		foreach($data as $year => $yearly) {
			if($stack) {
				$html .='<tr><td style="padding-left:10px;font-weight:bold;background-color:#AA9A9A;" colspan="'.($rowcount - 1).'"><b>'.$year.'</b></td>'.$tdstack.'</td></tr>';
			}
			else {
				$html .= '<tr><td style="padding-left:10px;font-weight:bold;background-color:#AA9A9A;" colspan="'.$rowcount.'"><b>'.$year.'</b></td></tr>';
			}
			$yearlyamount = 0;
			$yearlystack = 0;
			foreach($yearly as $month => $monthly) {
				$monthlyamount = 0;
				$monthlystack = 0;
				if($stack) {
					$html .= '<tr><td style="padding-left:10px;font-weight:bold;background-color:#AA9A9A;" colspan="'.($rowcount - 1).'"><b>'.$month.'</b></td>'.$tdstack.'</td></tr>';
				}
				else {
					$html .= '<tr"><td style="padding-left:10px;font-weight:bold;background-color:#AA9A9A;" colspan="'.$rowcount.'"><b>'.$month.'</b></td></tr>';
				}
				foreach($monthly as $week => $weekly) {
					$weeklyamount = 0;
					$weeklystack = 0;
					if($stack) {
						$html .= '<tr><td style="padding-left:10px;font-weight:bold;border-top:1px solid black;" colspan="'.($rowcount - 1).'"><b>'.'Week '.$week.'</b></td>'.$tdstack.'</td></tr>';
					}
					else {
						$html .= '<tr><td style="padding-left:10px;font-weight:bold;border-top:1px solid black;" colspan="'.$rowcount.'"><b>'.'Week '.$week.'</b></td></tr>';
					}
					if(is_array($weekly) && $weekly)
						foreach($weekly as $groupingkey => $values) {
							$idneedsadding = true;
							foreach($values as $key => $row) {
								$html.=$tr;
								if(is_numeric($key)) {
									if($idneedsadding) {
										$name = get_name_from_id($groupingkey, $resolve[$groupingcol]['table'], $resolve[$groupingcol]['id'], $resolve[$groupingcol]['name']);
										if($name == '-NA-') {
											if($togglegroup) {
												$html.=$grouptd1.' rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
											}
											else {
												$html.=$grouptd2.' rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
											}
										}
										else {
											if($togglegroup) {
												$html.=$grouptd1.' rowspan="'.(count($values) - 1).'">'.$name.'</td>';
											}
											else {
												$html.=$grouptd2.' rowspan="'.(count($values) - 1).'">'.$name.'</td>';
											}
										}
										$togglegroup = !$togglegroup;
										$idneedsadding = false;
										$toggle = false;
									}

									if($togglegroup) {
										if($toggle = !$toggle) {
											$td = $td2;
											$rtd = $rtd2;
										}
										else {
											$td = $td0;
											$rtd = $rtd0;
										}
									}
									else {
										if($toggle = !$toggle) {
											$td = $td1;
											$rtd = $rtd1;
										}
										else {
											$td = $td0;
											$rtd = $rtd0;
										}
									}
									foreach($trackedcolumns as $column => $coltype) {
										switch($column) {
											case 'date':
												if(isset($row[$column]['name'])) {
													if(is_numeric($row[$column]['name'])) {
														$html.=$td.date($core->settings['dateformat'], $row[$column]['name'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['name']). */'</td>';
													}
													else {
														$html.=$td.$row[$column]['name'].'</td>';
													}
												}
												else {
													if(is_numeric($row[$column]['value'])) {
														$html.=$td.date($core->settings['dateformat'], $row[$column]['value'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['value']). */'</td>';
													}
													else {
														$html.=$td.$row[$column]['value'].'</td>';
													}
												}
												break;
											case 'amount':
												$weeklyamount+=(float)$row[$column]['value'];
												$html.=$rtd.number_format($row[$column]['value'], 2, '.', ',').'</td>';
												break;
											default:
												if(isset($row[$column]['name'])) {
													$html.=$td.$row[$column]['name'].'</td>';
												}
												else {
													$html.=$td.$row[$column]['value'].'</td>';
												}
												break;
										}
									}
									if($stack) {
										$html.=$tdstack.$row['#StackedRows'].'</td></tr>';
									}
									else {
										$html.='</tr>';
									}
									$weeklystack+=$row['#StackedRows'];
								}
							}
						}
					$monthlyamount+=$weeklyamount;
					$monthlystack+=$weeklystack;
					if($stack) {
						$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 2).'"' : '').' style="text-align:right;padding-left:20px;">Weekly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($weeklyamount, 2, '.', ',').'</b></td>'.$tdstack.$weeklystack.'</td></tr>';
					}
					else {
						$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;padding-left:20px;">Weekly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($weeklyamount, 2, '.', ',').'</b></td></tr>';
					}
				}
				$yearlyamount+=$monthlyamount;
				$yearlystack+=$monthlystack;
				if($stack) {
					$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 2).'"' : '').' style="text-align:right;padding-left:20px;">Monthly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($monthlyamount, 2, '.', ',').'</b></td>'.$tdstack.$monthlystack.'</td></tr>';
				}
				else {
					$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;padding-left:20px;">Monthly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($monthlyamount, 2, '.', ',').'</b></td></tr>';
				}
			}
			$grandtotal+=$yearlyamount;
			$totalstack+=$yearlystack;
			if($stack) {
				$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 2).'"' : '').' style="text-align:right;padding-left:20px;">Yearly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($yearlyamount, 2, '.', ',').'</b></td>'.$tdstack.$yearlystack.'</td></tr>';
			}
			else {
				$html.='<tr style="background-color:#F0FFF0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;padding-left:20px;">Yearly total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($yearlyamount, 2, '.', ',').'</b></td></tr>';
			}
		}
		if($stack) {
			$html.='</td></tr><tr style="border-top:1px solid black;background-color:#FFF0F0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 2).'"' : '').'" style="text-align:right;padding-left:20px;">Grand Total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($grandtotal, 2, '.', ',').'</b></td><td style="text-align:right;padding-right:10px;border-left:1px solid black;"><b>'.$totalstack.'</b></td></tr></table>';
		}
		else {
			$html.='</td></tr><tr style="border-top:1px solid black;background-color:#FFF0F0;"><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').'" style="text-align:right;padding-left:20px;">Grand Total</td><td style="text-align:right;padding-right:10px;"><b>'.number_format($grandtotal, 2, '.', ',').'</b></td></tr></table>';
		}
		//</editor-fold>
	}

	return $html;
}

function debug($something, $label = '+') {
	log_performance(__METHOD__);

	global $content, $template, $core;
	if($core->input['isajax'] == 'true') {
		$content = encapsulate_in_fieldset('<pre>'.print_r($something, true).'</pre>', $label, false);
		output_xml($content);
	}
	else {
		$content.=encapsulate_in_fieldset('<pre>'.print_r($something, true).'</pre>', $label, false);
		eval("\$report_template = \"".$template->get('stock_purchasereport')."\";");
		output_page($report_template);
		exit;
	}
}

function make_options() {
	log_performance(__METHOD__);

	global $core, $lang;
	$options = '<div class="strep_optdiv"><form name="reportoptions" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	if(isset($core->input['groupingattribute'])) {
		switch($core->input['groupingattribute']) {
			case 'pid':
				$pid = "selected=true";
				break;
			case 'spid':
				$spid = "selected=true";
				break;
			case 'affid':
				$affid = "selected=true";
				break;
		}
	}
	$options.=$lang->groupby.' <select id="groupingattribute" name="group">
				<option value="pid" '.$pid.'>'.$lang->pid.'</option>
				<option value="spid" '.$spid.'>'.$lang->spid.'</option>
				<option value="affid" '.$affid.'>'.$lang->affid.'</option></select>';

	if($core->input['reporttype'] == '1') {
		$options.='<input id="switchdetailed" type="checkbox" name="reporttype" checked="true">'.$lang->detailed.'</input></form></div>';
	}
	else {
		$options.='<input id="switchdetailed" type="checkbox" name="reporttype">'.$lang->detailed.'</input></form></div>';
	}

	if(isset($core->input['fxratetype'])) {
		switch($core->input['fxratetype']) {
			case 'ylast':
				$yearlast = "selected=true";
				break;
			case 'mavg':
				$monthavg = "selected=true";
				break;
			case 'yavg':
				$yearavg = "selected=true";
				break;
			case 'real':
				$realrate = "selected=true";
				break;
		}
	}

	$options.=$lang->fxmethod.' <select id="fxratetype" name="fxratetype">
				<option value="ylast" '.$yearlast.'>'.$lang->ylast.'</option>
				<option value="mavg" '.$monthavg.'>'.$lang->mavg.'</option>
				<option value="yavg" '.$yearavg.'>'.$lang->yavg.'</option>
				<option value="real" '.$realrate.'>'.$lang->real.'</option></select>';


	$options.='<script type="text/javascript">
					$(document).ready(function() {
						$("#groupingattribute").change(function() {
							$("#column_"+$("#groupingattribute").val()).removeAttr("checked");
								post_col_ajax($(this)); // use post_ajax to remove column filters from post
							});
						$("#switchdetailed").change(function() {
							post_col_ajax($(this)); // use post_ajax to remove column filters from post
						});
						$("#fxratetype").change(function() {
							post_col_ajax($(this));
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

							var resolveidintoname=0,switchdetailed=0;
							if ($("#switchdetailed").prop("checked")==true)
								switchdetailed=1;
							sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&fxratetype="+$("#fxratetype").val()+"&groupingattribute="+$("#groupingattribute").val()+"&resolveidintoname="+resolveidintoname+firstform, "results_fieldset", "results_fieldset", "html");
					}
			   </script>';
	return $options;
}

function choose_columns() {
	log_performance(__METHOD__);

	global $core, $lang;
	$columns = array('pid' => 0, 'affid' => 1, 'spid' => 1, 'date' => 1, 'quantity' => 1, 'quantityUnit' => 1, 'currency' => 0, 'usdFxrate' => 0, 'saleType' => 0, 'TRansID' => 0);
	$options = '<div class="strep_coldiv"><form name="datacolumns" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	foreach($columns as $column => $status) {
		if(isset($core->input[$column])) {
			if($core->input[$column] != "1") {
				$status = 0;
			}
		}
		if($status == 1) {
			$options.='<input id="column_'.$column.'" type="checkbox" name="'.$column.'" value="'.$column.'" checked=true>'.$lang->{$column}.'</input>';
		}
		else {
			$options.='<input id="column_'.$column.'" type="checkbox" name="'.$column.'" value="'.$column.'">'.$lang->{$column}.'</input>';
		}
	}
	$options.='</form></div>';
	$options.='<script type="text/javascript">
				$(document).ready(function() {
				$("form").submit( function () {
					input = $("<input>").attr("type", "hidden").attr("name", "fxratetype").val($("#fxratetype").val());
					$("form").append($(input));
					input = $("<input>").attr("type", "hidden").attr("name", "reporttype").val(($("#switchdetailed").prop("checked")==true?"1":"0"));
					$("form").append($(input));
					input = $("<input>").attr("type", "hidden").attr("name", "groupingattribute").val($("#groupingattribute").val());
					$("form").append($(input));';
	foreach($columns as $column => $status) {
		$options.='input = $("<input>").attr("type", "hidden").attr("name", "'.$column.'").val($("#column_'.$column.'").prop("checked")==true?"1":"0");
	$("form").append($(input));';
	}
	$options.='});';

	foreach($columns as $column => $status) {
		$options.='$("#column_'.$column.'").change(function() {
						post_col_ajax($(this));
					});
					';
	}
	$options.='});
					function post_col_ajax(target) {
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
								firstform+="&datefrom="+$("#datefrom").val();';

	$options .='var checkboxes="";
		';
	foreach($columns as $column => $status) {
		$options.='var var_'.$column.'="0";
			if ($("#column_'.$column.'").prop("checked")==true) {
				var_'.$column.'="1";
				checkboxes+="&'.$column.'="+var_'.$column.';
				} else {
				checkboxes+="&'.$column.'="+var_'.$column.';
				}';
	}
	$options.='var switchdetailed=0; //,resolveidintoname=0
				if ($("#switchdetailed").prop("checked")==true)
					switchdetailed=1;
				sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&groupingattribute="+$("#groupingattribute").val()+firstform+checkboxes+"&fxratetype="+$("#fxratetype").val(), "results_fieldset", "results_fieldset", "html");}</script>';
	return $options;
}

function email_report($afid, $query) {
	global $core, $allcolumns;

	$return = '<div class="email_report">
				<form name="email_report" action="index.php?module=stock/reports&action=emailreport" method="POST" enctype="multipart/form-data">
				<input type=hidden name="datefrom" value="'.$core->input['datefrom'].'">
				<input type=hidden name="dateto" value="'.$core->input['dateto'].'">
				<input type=hidden name="report_type" value="'.$core->input['reporttype'].'">
				<input type=hidden name="grouping_atr" value="'.$core->input['groupingattribute'].'">
				<input type=hidden name="fxratetype" value="'.$core->input['fxratetype'].'">';
	//'<input type=hidden name="data_filter" value="'.weak_encrypt($query, 'orkilakey').'">';
	//'<input type=hidden name="affiliate_id" value="'.$afid.'">';

	if(isset($core->input['affiliate'])) {
		if(gettype($core->input['affiliate']) == "array" && count($core->input['affiliate']) > 0) {
			$core->input['affiliate'] = implode(',', $core->input['affiliate']);
			$return .='<input type=hidden name="affiliate" value="'.$core->input['affiliate'].'">';
		}
	}
	if(isset($core->input['supplier'])) {
		if(gettype($core->input['supplier']) == "array" && count($core->input['supplier']) > 0) {
			$core->input['supplier'] = implode(',', $core->input['supplier']);
			$return .='<input type=hidden name="supplier" value="'.$core->input['supplier'].'">';
		}
	}
	if(isset($core->input['product'])) {
		if(gettype($core->input['product']) == "array" && count($core->input['product']) > 0) {
			$core->input['product'] = implode(',', $core->input['product']);
			$return .='<input type=hidden name="product" value="'.$core->input['product'].'">';
		}
	}

	foreach($allcolumns as $column => $type) {
		if($core->input[$column] == '1') {
			$return.='<input type=hidden name="'.$column.'" value="1">';
		}
	}
	$return.='<input type=submit value="Email" id="formsubmit"></form></div>';
	return $return;
}

function assemble_filter_query($core, $db) {
	log_performance(__METHOD__);

	$query = 'SELECT * from '.Tprefix.'integration_mediation_stockpurchases';
	$checkifwherewasadded = false;
	//$dateto = parse_date("m/d/Y", $core->input['dateto'], 1);
	$dateto = strtotime($core->input['dateto']);
	//$datefrom = parse_date("m/d/Y", $core->input['datefrom'], 0);
	$datefrom = strtotime($core->input['datefrom']);
	if($dateto || $datefrom) {
		$checkifwherewasadded = true;
		$query.=' WHERE ';
		$query.=$dateto ? ('date<='.$dateto.'') : '';
		if($dateto && $datefrom) {
			$query.=' AND ';
		}
		$query.=$datefrom ? ('date>='.$datefrom.'') : '';
	}
	if($core->input['supplier']) {
		if($checkifwherewasadded) {
			$query.=' AND spid IN ('.implode(',', $core->input['supplier']).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE spid IN ('.implode(',', $core->input['supplier']).')';
		}
	}
	else {
		$suppliers = getSuppliersList(true);
		if($checkifwherewasadded) {
			$query.=' AND spid IN ('.implode(',', $suppliers).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE spid IN ('.implode(',', $suppliers).')';
		}
	}


	if($core->input['product']) {
		if($checkifwherewasadded) {
			$query.=' AND pid IN ('.implode(',', $core->input['product']).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE pid IN ('.implode(',', $core->input['product']).')';
		}
	}
	else {
		$suppliers = getSuppliersList(true);
		$products = getProductsList($suppliers, true);
		if($checkifwherewasadded) {
			$query.=' AND pid IN ('.implode(',', $products).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE pid IN ('.implode(',', $products).')';
		}
	}

	if($core->input['affiliate']) {
		if($checkifwherewasadded) {
			$query.=' AND affid IN ('.implode(',', $core->input['affiliate']).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE affid IN ('.implode(',', $core->input['affiliate']).')';
		}
	}
	else {
		$affiliates = getAffiliatesList(true);
		if($checkifwherewasadded) {
			$query.=' AND affid IN ('.implode(',', $affiliates).')';
		}
		else {
			$checkifwherewasadded = true;
			$query.=' WHERE affid IN ('.implode(',', $affiliates).')';
		}
	}
	return $query;
}

function make_filters($db, $core) {
	log_performance(__METHOD__);

	global $lang;
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

	$return = '<div class="strep_maindiv"><table cellspacing=2 cellpadding=5 border=0><tr><td><form name="reportfilter" action="index.php?module=stock/reports&action=getreport" method="POST" enctype="multipart/form-data">';
	$return.='<div style=""><b>'.$lang->from.'</b></font></div><input type="text" id="pickDateFrom" name="datefrom" value="'.$datefrom.'" /></td><td>';
	$return.='<div style=""><b>'.$lang->to.'</b></div>';
	$return.='<input type="text" id="pickDateTo" name="dateto" value="'.$dateto.'"/></td></tr><tr><td><b>'.$lang->affiliate.':</b></td><td><b>'.$lang->supplier.'</u></b></td><td><b>'.$lang->product.'</b></td></tr><tr>';

	$affiliates = getAffiliatesList();
	$return .='<td>'.parse_selectlist('affiliate[]', 1, $affiliates, $core->input['affiliate'], 1, null, array('id' => 'affiliate')).'</td>';

	$suppliers = getSuppliersList();
	$return .='<td>'.parse_selectlist('supplier[]', 2, $suppliers, $core->input['supplier'], 1, null, array('id' => 'supplier')).'</td>';

	$products = getProductsList($suppliers);
	$return .='<td>'.parse_selectlist('product[]', 3, $products, $core->input['product'], 1, null, array('id' => 'product')).'</td>';

	$return.='</td></tr><td colspan=2><input type=submit value=Generate id="formsubmit"></td></tr>';
	$return.='</form></div></td></tr></table>'.'
		<script>
			$(document).ready(function(){
				$(".datepicker" ).datepicker();
			});
		</script>';
	return $return;
}

function getAffiliatesList($idsonly = false) {
	log_performance(__METHOD__);
	global $core, $db;
	if($core->usergroup['canViewAllAff'] == 0) {
		$tmpaffiliates = $core->user['affiliates'];
		foreach($tmpaffiliates as $value) {
			if($idsonly) {
				$affiliates[$value] = $value;
			}
			else {
				$affiliates[$value] = get_name_from_id($value, 'affiliates', 'affid', 'name');
			}
		}
	}
	else {
		$affiliates_query = $db->query('SELECT affid,name from '.Tprefix.'affiliates');
		if($db->num_rows($affiliates_query) > 0) {
			while($affiliate = $db->fetch_assoc($affiliates_query)) {
				if($idsonly) {
					$affiliates[$affiliate['affid']] = $affiliate['affid'];
				}
				else {
					$affiliates[$affiliate['affid']] = $affiliate['name'];
				}
			}
		}
	}
	asort($affiliates);
	return $affiliates;
}

function getSuppliersList($idsonly = false) {
	log_performance(__METHOD__);

	global $core, $db;
	if($core->usergroup['canViewAllSupp'] == 0) {
		$tmpsuppliers = $core->user['suppliers'];
		foreach($tmpsuppliers['eid'] as $value) {
			if($idsonly) {
				$suppliers[$value] = $value;
			}
			else {
				$suppliers[$value] = get_name_from_id($value, 'entities', 'eid', 'companyName');
			}
		}
	}
	else {
		$suppliers_query = $db->query('SELECT eid,companyName from '.Tprefix.'entities WHERE type=\'s\'');
		if($db->num_rows($suppliers_query) > 0) {
			while($dbsupplier = $db->fetch_assoc($suppliers_query)) {
				if($idsonly) {
					$suppliers[$dbsupplier['eid']] = $dbsupplier['eid'];
				}
				else {
					$suppliers[$dbsupplier['eid']] = $dbsupplier['companyName'];
				}
			}
		}
	}
	asort($suppliers);
	return $suppliers;
}

function getProductsList($suppliers, $idsonly = false) {
	log_performance(__METHOD__);

	global $core, $db;
	foreach($suppliers as $key => $value) {
		$suppliers[$key] = $key;
	}
	$products_query = $db->query('SELECT pid,name from '.Tprefix.'products WHERE spid IN ('.implode(',', $suppliers).')');
	if($db->num_rows($products_query) > 0) {
		while($product = $db->fetch_assoc($products_query)) {
			if($idsonly) {
				$products[$product['pid']] = $product['pid'];
			}
			else {
				$products[$product['pid']] = $product['name'];
			}
		}
	}
	asort($products);
	return $products;
}

function time_regroup($data, $datecolumn) {
	$monthnames = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
	log_performance(__METHOD__);
	$timesliced = array();
	foreach($data as $key => $row) {
		$timesliced[date('Y', $row['date']['value'])][date('m', $row['date']['value'])][date('W', $row['date']['value'])][] = $row;
	}
	foreach($timesliced as $year => $yearly) {
		$sorted[$year] = $year;
	}
	asort($sorted);
	foreach($sorted as $year) {
		unset($msorted);
		foreach($timesliced[$year] as $month => $monthly) {
			$msorted[$month] = $month;
		}
		asort($msorted);
		foreach($msorted as $month) {
			unset($weeks);
			foreach($timesliced[$year][$month] as $week => $weekly) {
				$weeks[$week] = $week;
			}
			asort($weeks);
			foreach($weeks as $week) {
				$sortedm[$monthnames[(int)$month]][$week] = $timesliced[$year][$month][$week];
			}
		}
		$sorted[$year] = $sortedm;
	}
	return $sorted;
}

function regroup_and_sum($data, $ratemode, $groupingattribute = 'pid', $trackedcolumns = array("amount" => 'numeric'), $resolve = null) {
	log_performance(__METHOD__);
	$grouped = array();
	$value = '';
	foreach($data as $rowkey => $purchase) {
		$rate = get_fx_rate($ratemode, $purchase['usdFxrate'], $purchase['currency']['value'], $purchase['date']['value']);

		$value = $purchase[$groupingattribute]['value'];
		if(isset($resolve)) {
			$name = get_name_from_id($purchase[$groupingattribute]['value'], $resolve['table'], $resolve['id'], $resolve['name']);
			$grouped[$value]['#name'] = $name;
		}
		else {
			$grouped[$value]['#name'] = '-NA-'; // $purchase[$groupingattribute]['value'];
		}

		$doinitialisethisvalue = false;
		if(isset($grouped[$value])) {
			$id = check_for_presence($grouped[$value], $purchase, $trackedcolumns);
			if($id) {
				foreach($id as $key => $v) {
					break;
				}
				$grouped[$value][$key]['#StackedRows'] = $grouped[$value][$key]['#StackedRows'] + 1;
				foreach($trackedcolumns as $trackname => $columntype) {
					if($columntype == 'numeric') {
						if($trackname == 'amount') {
							$grouped[$value][$key][$trackname]['value'] += (float)$purchase[$trackname]['value'] * $rate;
						}
						else {
							$grouped[$value][$key][$trackname] += $purchase[$trackname];
						}
					}
				}
			}
			else {
				$doinitialisethisvalue = true;
			}
		}
		else {
			$doinitialisethisvalue = true;
		}
		if($doinitialisethisvalue) {
			$counter = 0;
			if($grouped[$value]) {
				foreach($grouped[$value] as $tmpk => $tmpv) {
					if(is_numeric($tmpk)) {
						$counter+=1;
					}
				}
			}

			if(!isset($grouped[$value][$counter])) {
				$grouped[$value][$counter]['#StackedRows'] = 1;
				foreach($trackedcolumns as $trackname => $columntype) {
					if($trackname == 'amount') {
						$grouped[$value][$counter][$trackname]['value'] = (float)$purchase[$trackname]['value'] * $rate;
					}
					else {
						$grouped[$value][$counter][$trackname] = $purchase[$trackname];
					}
				}
			}
		}
	}
	return $grouped;
}

function get_fx_rate($ratemode, $indatarate, $currency, $date) {
	$currency_obj = new Currencies('USD');
	if($ratemode == 'real') {
		if(!isset($indatarate)) {
			$rate = (float)$currency_obj->get_average_fxrate($currency, array('from' => strtotime('-7 days', date('d-m-Y', $date)), 'to' => strtotime('+7 days', date('d-m-Y', $date))));
		}
		else {
			$rate = (float)$indatarate;
		}
	}
	else {
		$rate = (float)$currency_obj->get_fxrate_bytype(
						$ratemode, $currency, array('from' => strtotime('-7 days', date('d-m-Y', $date)),
						'to' => strtotime('+7 days', date('d-m-Y', $date)),
						'year' => date('Y', $date),
						'month' => date('m', $date)
						)
		);
	}
	if($rate == 0) {
		$rate = $currency_obj->get_any_rate($currency, array('from' => $date));
	}
	return (float)$rate;
}

function regroup_by_day($data, $fxmode) {
	log_performance(__METHOD__);
	$grouped = array();
	$value = '';
	foreach($data as $rowkey => $purchase) {
		$rate = get_fx_rate($fxmode, $purchase['usdFxrate']['value'], $purchase['currency']['value'], $purchase['date']['value']);
		$date = strtotime(date('Y-m-d', $purchase['date']['value']));
		if(isset($grouped[$date])) {
			$grouped[$date] += (float)$purchase['amount']['value'] * $rate;
		}
		else {
			$grouped[$date] = (float)$purchase['amount']['value'] * $rate;
		}
	}

	return $grouped;
}

function resolve_names($data, $resolverules) {
	log_performance(__METHOD__);
	foreach($data as $key => $row) {
		foreach($row as $column => $value) {
			if(isset($resolverules[$column])) {
				$data[$key][$column]['name'] = get_name_from_id($value['value'], $resolverules[$column]['table'], $resolverules[$column]['id'], $resolverules[$column]['name']);
			}
		}
	}
	return $data;
}

function retrieve_data($query, $trackedcolumns = array("amount" => 'numeric')) {
	log_performance(__METHOD__);

	global $db;
	$purchase_report = $db->query($query);
	$dataarray = array();
	if($db->num_rows($purchase_report) > 0) {
		$counter = 0;
		while($purchase = $db->fetch_assoc($purchase_report)) {
			foreach($trackedcolumns as $column => $type) {
				$dataarray[$counter][$column]['value'] = $purchase[$column];
			}
			$counter++;
		}
	}
	return $dataarray;
}

function check_for_presence($haystack, $needle, $filter) {
	$results = array();
	foreach($haystack as $key => $row) {
		if(is_numeric($key)) {
			$present = true;
			foreach($filter as $column => $type) {
				if($type != 'numeric') {
					if($row[$column]['value'] != $needle[$column]['value']) {
						$present = false;
					}
				}
			}
			if($present) {
				$results[$key] = 1;
			}
		}
	}
	return $results;
}

function log_performance($name) {
	global $performance, $perflogcount;
	$perflogcount++;
	if(isset($performance['_FUNCTION_START_'.str_replace(' ', '', $name)])) {


		for($count = 1; $count < $perflogcount; $count++) {
			if(!isset($performance['_FUNCTION_START_'.str_replace(' ', '', $name).'_'.$count]))
				break;
		}
		$performance['_FUNCTION_START_'.str_replace(' ', '', $name).'_'.$count] = microtime();
	}
	else {
		$performance['_FUNCTION_START_'.str_replace(' ', '', $name)] = microtime();
	}
}

function get_perf_data() {
	global $performance;
	$counter = 0;
	$tmphtml = '<table border=1 cellspacing=0 cellpadding=4><tr style="background-color:lightgrey;"><td>ID</td><td><b>Time</b></td><td><b>Point</b></td><td><b>Delta</b></td></tr>';
	$forpie;
	$timedate = explode(" ", $performance['--START--']);
	$timecomp = explode(":", date('H:i:s', $timedate[1]));
	$initialtiming = $timedate[1] + (float)('0.'.str_replace('0.', '', $timedate[0]));
	$totaltiming = 0;
	$previoustiming = 0;
	foreach($performance as $key => $value) {
		$timedate = explode(" ", $value);
		$timing = ($timedate[1] + (float)('0.'.str_replace('0.', '', $timedate[0]))) - (float)$initialtiming;

		$delta = $timing - $previoustiming;

		$previoustiming = $timing;
		//$totaltiming+=$timing;
		$tmphtml.='<tr><td>'.$counter++.'</td><td>'.number_format(1000 * $timing, 3, '.', '').'</td><td>'.$key.'</td><td>'.number_format((1000 * $delta), 3, '.', '').'</td></tr>';
		$forpie[] = array('#name' => $key, 0 => array('amount' => array('value' => 1000 * $delta), 'timing' => array('value' => (1000 * $timing))));
	}
	$tmphtml .= '</table>';
	//'<td valign=top>'.make_jqppiechart($forpie, "perfpie", '', 0, -90, 800, 600).'</td>'
	//make_jqpchart($forpie);
	//make_jqbarchart($forpie)
	$html = '<table border=0 cellspacing=0 cellpadding=2><tr><td valign=top>'.make_jqlinechart($forpie).'</td></tr></table>';  //<td valign=top>'.$tmphtml.'</td>
	return encapsulate_in_fieldset($html, "Performance", false);
}

function weak_encrypt($string, $key) {
	$result = '';
	for($i = 0; $i < strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) - 1, 1);
		$char = chr(ord($char) + ord($keychar));
		$result.=$char;
	}
	return base64_encode($result);
}

function weak_decrypt($string, $key) {
	$result = '';
	$string = base64_decode($string);
	for($i = 0; $i < strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) - 1, 1);
		$char = chr(ord($char) - ord($keychar));
		$result.=$char;
	}
	return $result;
}

function get_affiliate_gm_email($affid) {
	global $db;
	return $db->fetch_field($db->query('SELECT '.Tprefix.'users.email FROM '.Tprefix.'affiliates INNER JOIN '.Tprefix.'users ON '.Tprefix.'affiliates.generalManager='.Tprefix.'users.uid  WHERE '.Tprefix.'affiliates.affid="'.$affid.'"'), 'email');
}

function send_mail($recipient, $content, $subject) {
	global $core;
	$header = $headers = "From: ".$core->settings['maileremail']."\r\nReply-To: ".$core->settings['maileremail']."\r\nX-Mailer: PHP/".phpversion();
	if(mail($recipient, $subject, $content, $headers)) {
		echo("<p>Message successfully sent.</p>");
	}
	else {
		echo("<p>Message delivery failed...</p>");
	}
	die();
}


// <editor-fold defaultstate="collapsed" desc="functions used to randomly populate the purchases table">
function random_fill_for_testing($howmany) {
	global $db;
	for($i = 0; $i < $howmany; $i++) {
		seed_random_gen();
		$gen_product = get_random_entry('products');
		$gen_affiliate = get_random_entry('affiliates');
		$gen_currency = get_random_entry('currencies');
		$dummydata[$i]['pid'] = $gen_product['pid'];
		$dummydata[$i]['spid'] = $gen_product['spid'];
		$dummydata[$i]['affid'] = $gen_affiliate['affid'];
		$dummydata[$i]['amount'] = get_random_float(500000);
		$dummydata[$i]['currency'] = $gen_currency['alphaCode'];
		$dummydata[$i]['usdFxrate'] = get_random_float(3);
		$dummydata[$i]['quantity'] = get_random_integer(50);
		$dummydata[$i]['quantityUnit'] = get_random_value(array("MT", "KG", "L"));
		$dummydata[$i]['date'] = get_random_date(1199167200, 1356847200);
		$dummydata[$i]['saleType'] = get_random_value(array("SKI", "BGP"));
		$dummydata[$i]['TRansID'] = '{NA}';
	}
	foreach($dummydata as $row) {
		$db->insert_query('integration_mediation_stockpurchases', $row);
	}
	echo 'done';
	exit;
}

function seed_random_gen() {
	log_performance(__METHOD__);
	list($usec, $sec) = explode(' ', microtime());
	$seed = (float)$sec + ((float)$usec * 100000);
	srand($seed);
}

function get_random_value($array) {
	log_performance(__METHOD__);
	return $array[rand(0, count($array) - 1)];
}

function get_random_entry($tablename) {
	global $db;
	$result = mysql_query('SELECT * FROM '.Tprefix.$tablename);
	mysql_data_seek($result, rand(0, mysql_num_rows($result) - 1));
	return mysql_fetch_array($result);
}

function get_random_date($from, $to) {
	return $from + rand(0, $to - $from - 1);
}

function get_random_float($max = null) {
	if(!isset($max)) {
		$max = getrandmax();
	}
	return rand(0, getrandmax()) * $max / getrandmax();
}

function get_random_integer($max = null) {
	if(!isset($max)) {
		$max = getrandmax();
	}
	return rand(0, $max);
}

// </editor-fold>
//</editor-fold>
?>
