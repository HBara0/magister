<?php
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

$session->start_phpsession();
$lang->load('stock_meta');
$performance["--START--"] = microtime();
$perflogcount = 0;
$jq_common = false;
$jq_pie = false;
$jq_chart = false;
$jq_bar = false;

$resolve = array(
		'affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'),
		'spid' => array('table' => 'entities', 'id' => 'eid', 'name' => 'companyName'),
		'pid' => array('table' => 'products', 'id' => 'pid', 'name' => 'name'),
		'currency' => array('table' => 'currencies', 'id' => 'numCode', 'name' => 'alphaCode')
);

if($core->usergroup['stock_canGenerateReports'] == '1') {
	$content = encapsulate_in_fieldset(make_filters($db, $core), 'Filter', false);
	$content .= encapsulate_in_fieldset(make_options(), 'Options', false);
	$content .= encapsulate_in_fieldset(choose_columns(), 'Columns', false);
	if($core->input['action'] == 'generatedummy') {
		$howmany = 100;
		if(isset($core->input['number'])) {
			if(is_numeric($core->input['number'])) {
				$howmany = $core->input['number'];
			}
		}
		random_fill_for_testing($howmany);
	}
	elseif($core->input['action'] == 'getreport') {
		$query = assemble_filter_query($core, $db);
		//$content.=encapsulate_in_fieldset($query, 'Query');
		$allcolumns = array('affid' => 'id', 'spid' => 'id', 'pid' => 'id', 'amount' => 'numeric', 'currency' => 'numeric', 'usdFxrate' => 'numeric', 'quantity' => 'numeric', 'quantityUnit' => 'text', 'date' => 'date', 'saleType' => 'text', 'TRansID' => 'text');
		$rawdata = retrieve_data($query, $allcolumns);
		$trackedcolumns = array();
		$groupingatr = $core->input['groupingattribute'];
		if(!isset($groupingatr))
			$groupingatr = 'pid';
		$rawdata = resolve_names($rawdata, $resolve);
		$trackedcolumns['amount'] = $allcolumns['amount'];
		foreach($allcolumns as $column => $type) {
			if($core->input[$column] == '1') {
				$trackedcolumns[$column] = $allcolumns[$column];
			}
		}
		if($core->input['reporttype'] == 1) {
			$timesliced = time_regroup($rawdata, 'date');
			foreach($timesliced as $year => $yearly) {
				foreach($yearly as $month => $monthly) {
					foreach($monthly as $week => $weekly) {
						unset($timesliced[$year][$month][$week]);
						$timesliced[$year][$month][$week] = sort_by_amount(regroup_and_sum($weekly, $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null));
					}
				}
			}
			$datapresented = encapsulate_in_fieldset(turn_data_into_html($timesliced, true, $trackedcolumns, $groupingatr), 'Grouped',false);
		}
		else {
			$summeddata = regroup_and_sum($rawdata, $groupingatr, $trackedcolumns, ($doresolve) ? $resolve[$groupingatr] : null);
			$datapresented = encapsulate_in_fieldset(turn_data_into_html($summeddata, false, $trackedcolumns, $groupingatr), 'Grouped', false);
		}

		$charts = '<div style="position:relative;"><div id="general_chart" style="margin-left:5px;margin-bottom:10px;">'.make_jqpchart(regroup_by_day(convert_to_dollars($rawdata))).'</div>';
		//$charts.='<div id="product_piechart" style="position:relative;float:left;">'.make_pchart(regroup_by_day($rawdata)).'</div>';
		$charts.='<div id="affiliate_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata), 'affid', array('amount' => 'numeric'), $resolve['affid'])), 'affiliate_pie', 'Top 10 Affiliates').'</div>';
		$charts.='<div id="supplier_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata), 'spid', array('amount' => 'numeric'), $resolve['spid'])), 'supplier_pie', 'Top 10 Suppliers').'</div>';
		$charts.='<div id="product_piechart" style="position:relative;float:left;">'.make_jqppiechart(sort_by_amount(regroup_and_sum(convert_to_dollars($rawdata), 'pid', array('amount' => 'numeric'), $resolve['pid'])), 'product_pie', 'Top 10 Products').'</div></div>';
		$charts =encapsulate_in_fieldset($charts, "Charts", false);
		$performance["--END--"] = microtime();
		if($core->input['isajax'] == 'true') {
			output_xml($charts.$datapresented.get_perf_data());
			exit;
		}
		else {
			$content.='<div id="results_fieldset">'.$charts.$datapresented.get_perf_data().'</div>';
		}
	}
}
else {
	error($lang->sectionnopermission);
}
eval("\$report_template = \"".$template->get('stock_purchasereport')."\";");
output_page($report_template);
//</editor-fold>

function convert_to_dollars($rawdata) {
	log_performance(__METHOD__);
	global $db;
	$usdcode = $db->fetch_field($db->query('SELECT numCode FROM '.Tprefix.'currencies WHERE alphaCode="USD"'), 'numCode');
	$currency_obj = new Currencies('USD');
	foreach($rawdata as $key => $value) {
		if(!isset($value['usdFxrate'])) {
			$rate = (float)$currency_obj->get_average_fxrate($value['currency']['value'], array('from' => strtotime('-7 days', $value['date']['value']), 'to' => strtotime('+7 days', $value['date']['value'])));
		}
		else {
			$rate = (float)$value['usdFxrate']['value'];
		}
		$rawdata[$key]['amount']['value'] = (float)$value['amount']['value'] * $rate;
		$rawdata[$key]['usdFxrate']['value'] = 1;
		$rawdata[$key]['currency']['value'] = $usdcode;
	}
	return $rawdata;
}

function sort_by_amount($data, $numberofrows = 10) {
	log_performance(__METHOD__);
	$itemcount = 0;
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

					//if($row[0]['amount']['value'] > $data[$biggest][0]['amount']['value']) {
					//	$biggest = $key;
					//}
				}
				else {
					$biggest = $key;
				}
			}
		}
		if(isset($biggest)) {
			$processed[$biggest] = 1;
			$sorted[''.$biggest] = $data[$biggest];
			$itemcount++;
		}
	}

	$itemcount = 'Other';
	$sorted[$itemcount] = array('#name' => 'Other', 0 => array('#StackedRows' => 0, 'amount' => array('value' => 0)));
	$gotfiller = false;
	foreach($data as $key => $row) {
		if(!isset($processed[$key])) {
			$sorted[$itemcount][0]['#StackedRows']+=$row[0]['#StackedRows'];
			$sorted[$itemcount][0]['amount']['value']+=(float)$row[0]['amount']['value'];
			if(!$gotfiller) {
				foreach($row[0] as $key2 => $value) {
					if($key2 != '#StackedRows' && $key2 != 'amount') {
						$sorted[$itemcount][0][$key2]['value'] = '-NA-';
						$sorted[$itemcount][0][$key2]['name'] = '-NA-';
					}
				}
				$gotfiller = true;
			}
		}
	}

	if($sorted[$itemcount][0]['#StackedRows'] == 0)
		unset($sorted[$itemcount]);
	return $sorted;
}

function make_jqbarchart($data) {
	log_performance(__METHOD__);
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);
	if(!$jq_common) {
		$includes .= '
			<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
			<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
			<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />
				';
		$jq_common = true;
	}
	if(!$jq_bar) {
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.cursor.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.barRenderer.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>';
		$includes .= '<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>';
		$jq_bar = true;
	}

	$function = '<span id="info1"></span>
		<div id="chart1" style="width:'.$divwidth.';height:'.$divheight.';vertical-align:top;overflow: hidden;"></div><script>
		$(document).ready(function(){
        $.jqplot.config.enablePlugins = true;';

			$firstvar=' var s1 = [';
			$secondvar=' var ticks = [';

		foreach($data as $key => $row) {
		if(is_array($row)) {
			$total = 0;
			foreach($row as $key => $value) {
				if(is_array($value)) {
					$total+=(float)$value['amount']['value'];
				}
			}
			if ($total<0) {
				$total=-$total;
			}
			$firstvar.= number_format($total, 2, '.', '').',';
			$secondvar.='"'.$row['#name'].'",';
		}
	}
	$firstvar.='];';
	$secondvar.='];';


    $function.=$firstvar.$secondvar.'
		plot11 = $.jqplot(\'chart1\', [s1], {
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: true }
        });

        $(\'#chart1\').bind(\'jqplotDataClick\',
            function (ev, seriesIndex, pointIndex, data) {
                $(\'#info1\').html(\'series: \'+seriesIndex+\', point: \'+pointIndex+\', data: \'+data);
            }
        );
    });
	</script>';
	return $includes.$function;
}


function make_jqppiechart($data, $id = "jqpieid", $title = '', $margin = 0, $startangle = 0, $divwidth = '230px', $divheight = '230px', $fill = true) {
	log_performance(__METHOD__);
	$urlparts = explode('?', get_curent_page_URL());
	$baseurl = substr($urlparts[0], 0, strlen($urlparts[0]) - 9);

	global $jq_common, $jq_pie;
	$includes = '';
	if(!$jq_common) {
		$includes .= '
			<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
			<script type="text/javascript" src="'.$baseurl.'inc/jQplot/plugins/jqplot.highlighter.min.js"></script>
			<link rel="stylesheet" type="text/css" hrf="'.$baseurl.'inc/jQplot/jquery.jqplot.min.css" />
				';
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
					$total+=(float)$value['amount']['value'];
				}
			}
			if ($total<0) {
				$total=-$total;
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
		foreach($data as $date => $value) {
			if($date >= $currentstamp && $date < $nextstep) {
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
	global $jq_common, $jq_chart;
	$includes = '';
	if(!$jq_common) {
		$includes .= '
			<script type="text/javascript" src="'.$baseurl.'inc/jQplot/jquery.jqplot.min.js"></script>
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


	$data = reduce_density_by_grouping($data, 50);


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
		cursor: {show:true,zoom:true},
		series: {showMarker:true},
		seriesDefaults: {rendererOptions: {smooth: false}},
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

function turn_data_into_html($data, $timesliced = false, $trackedcolumns, $groupingcol = "pid") {
	log_performance(__METHOD__);

	global $core, $lang, $resolve;
	$rowcount = 2;
	$head = '<tr align="left"><th>'.$lang->{$groupingcol}.'</th>';
	foreach($trackedcolumns as $colid => $coltype) {
		$head.='<th>'.$lang->{$colid}.'</th>';
		$rowcount+=1;
	}
	$head.='<th>Stacked</th></tr>';

	if(!$timesliced) {
		$html = '<table cellspacing=0 cellpadding=2 border=1>'.$head;
		$totalstack = 0;
		$totalamount = 0;
		foreach($data as $groupingkey => $values) {
			$idneedsadding = true;
			foreach($values as $key => $row) {
				if(is_numeric($key)) {
					$html.='<tr>';
					if($idneedsadding) {
						$name = get_name_from_id($groupingkey, $resolve[$groupingcol]['table'], $resolve[$groupingcol]['id'], $resolve[$groupingcol]['name']);
						if($name == '-NA-') {
							$html.='<td rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
						}
						else {
							$html.='<td rowspan="'.(count($values) - 1).'">'.$name.'</td>';
						}
						$idneedsadding = false;
					}
					foreach($trackedcolumns as $column => $coltype) {
						switch($column) {
							case 'date':
								if(isset($row[$column]['name'])) {
									if(is_numeric($row[$column]['name'])) {
										$html.='<td>'.date($core->settings['dateformat'], $row[$column]['name'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['name']). */'</td>';
									}
									else {
										$html.='<td>'.$row[$column]['name'].'</td>';
									}
								}
								else {
									if(is_numeric($row[$column]['value'])) {
										$html.='<td>'.date($core->settings['dateformat'], $row[$column]['value'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['value']). */'</td>';
									}
									else {
										$html.='<td>'.$row[$column]['value'].'</td>';
									}
								}
								break;
							case 'amount':
								$totalamount+=(float)$row[$column]['value'];
								$html.='<td>'.number_format($row[$column]['value'], 2, '.', ' ').'</td>';
								break;
							default:
								if(isset($row[$column]['name'])) {
									$html.='<td>'.$row[$column]['name'].'</td>';
								}
								else {
									$html.='<td>'.$row[$column]['value'].'</td>';
								}
								break;
						}
					}
					$html.='<td>'.$row['#StackedRows'].'</td></tr>';
					$totalstack+=$row['#StackedRows'];
				}
			}
		}
		$html.='</td></tr><tr>'.($rowcount > 1 ? '<td colspan="'.($rowcount - 1).'" style="text-align:right;">Grand total: <b>'.number_format($totalamount, 2, '.', ' ').'</b></td>' : '').'<td>'.$totalstack.'</td></tr></table>';
	}
	else {
		$html = '<table cellspacing=0 cellpadding=2 border=1>'.$head;
		$grandtotal = 0;
		foreach($data as $year => $yearly) {
			$html .= '<tr><td colspan="'.$rowcount.'"><b>'.$year.'</b></td></tr>';
			$yearlyamount = 0;
			$yearlystack = 0;
			foreach($yearly as $month => $monthly) {
				$monthlyamount = 0;
				$monthlystack = 0;
				$html .= '<tr><td colspan="'.$rowcount.'"><b>'.$month.'</b></td></tr>';
				foreach($monthly as $week => $weekly) {
					$weeklyamount = 0;
					$weeklystack = 0;
					$html .= '<tr><td colspan="'.$rowcount.'"><b>Week '.$week.'</b></td></tr>';
					if(is_array($weekly) && $weekly)
						foreach($weekly as $groupingkey => $values) {
							$idneedsadding = true;
							foreach($values as $key => $row) {
								$html.='<tr>';
								if(is_numeric($key)) {
									if($idneedsadding) {
										$name = get_name_from_id($groupingkey, $resolve[$groupingcol]['table'], $resolve[$groupingcol]['id'], $resolve[$groupingcol]['name']);
										if($name == '-NA-') {
											$html.='<td rowspan="'.(count($values) - 1).'">'.$groupingkey.'</td>';
										}
										else {
											$html.='<td rowspan="'.(count($values) - 1).'">'.$name.'</td>';
										}
										$idneedsadding = false;
									}
									foreach($trackedcolumns as $column => $coltype) {
										switch($column) {
											case 'date':
												if(isset($row[$column]['name'])) {
													if(is_numeric($row[$column]['name'])) {
														$html.='<td>'.date($core->settings['dateformat'], $row[$column]['name'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['name']). */'</td>';
													}
													else {
														$html.='<td>'.$row[$column]['name'].'</td>';
													}
												}
												else {
													if(is_numeric($row[$column]['value'])) {
														$html.='<td>'.date($core->settings['dateformat'], $row[$column]['value'])./* '<br>'.date($core->settings['timeformat'], $row[$column]['value']). */'</td>';
													}
													else {
														$html.='<td>'.$row[$column]['value'].'</td>';
													}
												}
												break;
											case 'amount':
												$weeklyamount+=(float)$row[$column]['value'];
												$html.='<td>'.number_format($row[$column]['value'], 2, '.', ' ').'</td>';
												break;
											default:
												if(isset($row[$column]['name'])) {
													$html.='<td>'.$row[$column]['name'].'</td>';
												}
												else {
													$html.='<td>'.$row[$column]['value'].'</td>';
												}
												break;
										}
									}

									$html.='<td>'.$row['#StackedRows'].'</td></tr>';
									$weeklystack+=$row['#StackedRows'];
								}
							}
						}
					$monthlyamount+=$weeklyamount;
					$monthlystack+=$weeklystack;
					$html.='<tr><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;">Weekly total: <b>'.number_format($weeklyamount, 2, '.', ' ').'</b></td><td>'.$weeklystack.'</td></tr>';
				}
				$yearlyamount+=$monthlyamount;
				$yearlystack+=$monthlystack;
				$html.='<tr><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;">Monthly total: <b>'.number_format($monthlyamount, 2, '.', ' ').'</b></td><td>'.$monthlystack.'</td></tr>';
			}
			$grandtotal+=$yearlyamount;
			$totalstack+=$yearlystack;
			$html.='<tr><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').' style="text-align:right;">Yearly total: <b>'.number_format($yearlyamount, 2, '.', ' ').'</b></td><td>'.$yearlystack.'</td></tr>';
		}
		$html.='</td></tr><tr><td '.(($rowcount > 1) ? 'colspan="'.($rowcount - 1).'"' : '').'" style="text-align:right;">Grand total:<b>'.number_format($grandtotal, 2, '.', ' ').'</b></td><td>'.$totalstack.'</td></tr></table>';
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
				<option value="pid" '.$pid.'>Product</option>
				<option value="spid" '.$spid.'>Supplier</option>
				<option value="affid" '.$affid.'>Affiliate</option></select>';

	if($core->input['resolveidintoname'] == 'resolve') {
		$options.='<input id="resolveidintoname" type="checkbox" name="Resolve" value="resolve" checked="true">Resolve</input>';
	}
	else {
		if(isset($core->input['resolveidintoname'])) {
			//$options.='<input id="resolveidintoname" type="checkbox" name="Resolve" value="resolve">Resolve</input>';
		}
		else {
			//$options.='<input id="resolveidintoname" type="checkbox" name="Resolve" value="resolve" checked="true">Resolve</input>';
		}
	}
	if($core->input['reporttype'] == '1') {
		$options.='<input id="switchdetailed" type="checkbox" name="reporttype" checked="true">'.$lang->detailed.'</input></form></div>';
	}
	else {
		$options.='<input id="switchdetailed" type="checkbox" name="reporttype">'.$lang->detailed.'</input></form></div>';
	}
	$options.='<script type="text/javascript">
					$(document).ready(function() {
						$("#groupingattribute").change(function() {
							$("#column_"+$("#groupingattribute").val()).removeAttr("checked");
								post_col_ajax($(this)); // use post_ajax to remove column filters from post
							});
						//$("#resolveidintoname").change(function() {
						//	post_col_ajax($(this));	// use post_ajax to remove column filters from post
						//	});
						$("#switchdetailed").change(function() {
							post_col_ajax($(this)); // use post_ajax to remove column filters from post
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
							//if ($("#resolveidintoname").prop("checked")==true)
							//	resolveidintoname="resolve";
							sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&groupingattribute="+$("#groupingattribute").val()+"&resolveidintoname="+resolveidintoname+firstform, "results_fieldset", "results_fieldset", "html");
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
					//var input = $("<input>").attr("type", "hidden").attr("name", "resolveidintoname").val(($("#resolveidintoname").prop("checked")==true?"resolve":"0"));
					//$("form").append($(input));
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
				//if ($("#resolveidintoname").prop("checked")==true)
				//	resolveidintoname="resolve";
				sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&groupingattribute="+$("#groupingattribute").val()+firstform+checkboxes, "results_fieldset", "results_fieldset", "html");}</script>';
	//$options.='sharedFunctions.requestAjax("post", "index.php?module=stock/reports&action=getreport", "isajax=true&reporttype="+switchdetailed+"&groupingattribute="+$("#groupingattribute").val()+"&resolveidintoname="+resolveidintoname+firstform+checkboxes, "results_fieldset", "results_fieldset", "html");}</script>';
	return $options;
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
		$affiliates = getAffiliateList(true);
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
	$return.='<div style=""><b><u>'.$lang->from.':</u></b></font></div><input type="text" id="datefrom" name="datefrom" class="datepicker" value="'.$datefrom.'" /></td><td>';
	$return.='<div style=""><b><u>'.$lang->to.':</u></b></div>';
	$return.='<input type="text" id="dateto" name="dateto" class="datepicker" value="'.$dateto.'"/></td></tr><tr><td><b><u>'.$lang->affiliate.':</u></b></td><td><b><u>'.$lang->supplier.':</u></b></td><td><b><u>'.$lang->product.':</u></b></td></tr><tr>';

	$affiliates = getAffiliateList();
	asort($affiliates);
	$return .='<td>'.parse_selectlist('affiliate[]', 1, $affiliates, $core->input['affiliate'], 1, null, array('id' => 'affiliate')).'</td>';

	$suppliers = getSuppliersList();
	asort($suppliers);
	$return .='<td>'.parse_selectlist('supplier[]', 2, $suppliers, $core->input['supplier'], 1, null, array('id' => 'supplier')).'</td>';


	$products = getProductsList($suppliers);
	asort($products);
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

function getAffiliateList($idsonly = false) {
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
	return $products;
}

function time_regroup($data, $datecolumn) {
	log_performance(__METHOD__);

	$timesliced = array();
	foreach($data as $key => $row) {
		foreach($row as $column => $value) {
			if($column == $datecolumn) {
				$timesliced[date('Y', $value['value'])][date('F', $value['value'])][date('W', $value['value'])][] = $row;
			}
		}
	}
	return $timesliced;
}

function regroup_and_sum($data, $groupingattribute = 'pid', $trackedcolumns = array("amount" => 'numeric'), $resolve = null) {
	log_performance(__METHOD__);

	$currency_obj = new Currencies('USD');
	$grouped = array();
	$value = '';
	foreach($data as $rowkey => $purchase) {
		if(!isset($purchase['usdFxrate'])) {
			$rate = (float)$currency_obj->get_average_fxrate($purchase['currency']['value'], array('from' => strtotime('-7 days', $purchase['date']['value']), 'to' => strtotime('+7 days', $purchase['date']['value'])));
		}
		else {
			$rate = (float)$purchase['usdFxrate']['value'];
		}

		$value = $purchase[$groupingattribute]['value'];
		if(isset($resolve)) {
			$name = get_name_from_id($purchase[$groupingattribute]['value'], $resolve['table'], $resolve['id'], $resolve['name']);
			$grouped[$value]['#name'] = $name;
		}
		else {
			$grouped[$value]['#name'] = '-NA-';
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

function regroup_by_day($data) {
	log_performance(__METHOD__);

	$currency_obj = new Currencies('USD');
	$grouped = array();
	$value = '';
	foreach($data as $rowkey => $purchase) {
		if(!isset($purchase['usdFxrate'])) {
			$rate = $currency_obj->get_average_fxrate($purchase['currency']['value'], array('from' => strtotime('-1 day', $purchase['date']['value']), 'to' => strtotime('+1 day', $purchase['date']['value'])));
		}
		else {
			$rate = $purchase['usdFxrate']['value'];
		}

		$date=strtotime(date('Y-m-d',$purchase['date']['value']));
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

function encapsulate_in_fieldset($html, $legend = "+", $boolStartClosed = false) {
	log_performance(__METHOD__);

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
		return '-NA-';
	}
}

function log_performance($name) {
	global $performance, $perflogcount;
	if(isset($performance['FUNCTION_START_'.str_replace(' ', '', $name)])) {
		$performance['FUNCTION_START_'.str_replace(' ', '', $name).'_'.($perflogcount++)] = microtime();
	}
	else {
		$performance['FUNCTION_START_'.str_replace(' ', '', $name)] = microtime();
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
	foreach($performance as $key => $value) {
		$timedate = explode(" ", $value);
		$timing = ($timedate[1] + (float)('0.'.str_replace('0.', '', $timedate[0]))) - (float)$initialtiming;
		$totaltiming+=$timing;
		$tmphtml.='<tr><td>'.$counter++.'</td><td>'.number_format(1000 * $totaltiming, 3, '.', '').'</td><td>'.$key.'</td><td>'.number_format((1000 * $timing), 3, '.', '').'</td></tr>';
		$forpie[] = array('#name' => $key, 0 => array('amount' => array('value' => number_format((1000 * $timing), 3, '.', ''))));
	}
	$tmphtml .= '</table>';

	$html='<table border=1 cellspacing=0 cellpadding=4><tr><td valign=top rowspan=2>'.$tmphtml.'</td><td valign=top>'.make_jqppiechart($forpie, "perfpie", '', 0, -90, 800, 600).'</td></tr><tr><td>'./*make_jqbarchart($forpie)*/''.'</td></tr></table>';
	return encapsulate_in_fieldset($html, "Performance");
}

// <editor-fold defaultstate="collapsed" desc="functions used to randomly populate the purchases table">
function random_fill_for_testing($howmany) {
	for($i = 0; $i < $howmany; $i++) {
		seed_random_gen();
		$gen_product = get_random_entry('products');
		$gen_affiliate = get_random_entry('affiliates');
		$gen_currency = get_random_entry('currencies');
		$dummydata[$i]['pid'] = $gen_product['pid'];
		$dummydata[$i]['spid'] = $gen_product['spid'];
		$dummydata[$i]['affid'] = $gen_affiliate['affid'];
		$dummydata[$i]['amount'] = get_random_float(50000) - 30000;
		$dummydata[$i]['currency'] = $gen_currency['numCode'];
		$dummydata[$i]['usdFxrate'] = get_random_float(3);
		$dummydata[$i]['quantity'] = get_random_integer(50);
		$dummydata[$i]['quantityUnit'] = get_random_value(array("MT", "KG", "L"));
		$dummydata[$i]['date'] = get_random_date(1104559200, 1356847200);
		$dummydata[$i]['saleType'] = 'SKI';
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
?>