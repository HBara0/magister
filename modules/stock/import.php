<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright Â© 2010 Orkila International Offshore, All Rights Reserved
 * Import Stock
 * $module: Stock
 * Created		@zaher.reda 		September 7, 2012 | 3:41 PM
 * Last Update: 	@zaher.reda 		September 7, 2012 | 3:41 PM
 */
if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if ($core->usergroup['stock_canImport'] == 0) {
    //error($lang->sectionnopermission);
}

$session->start_phpsession();

if (!$core->input['action']) {
    $formatdates = array(1=>'m/d/Y',2=>'m-d-Y',3=>'d/m/Y',4=>'d-m-Y',5=>'Y-m-d');
    asort($formatdates);
    foreach ($formatdates as $key=>$formatvalue) {
        $formatdates[$key] = $formatvalue.' (ex. '.date($formatvalue).')';
    }

    $affiliates = get_specificdata('affiliates',array('affid','name'),'affid','name',array('by'=>'name','sort'=>'ASC'));
    $affiliates_list = parse_selectlist('filteraffiliate',1,$affiliates,$affid,0);

    $dateformats_selectlist = parse_selectlist('dateformat','2',$formatdates,1);
    eval("\$importpage = \"".$template->get('stock_import')."\";");
    output_page($importpage);
}
else {
    if ($core->input['action'] == 'import') {
        $upload = new Uploader('uploadfile',$_FILES,array('application/csv','application/excel','application/octet-stream','application/x-excel','text/csv','text/comma-separated-values','application/vnd.ms-excel','application/vnd.msexcel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),'readonly',5242880,0,1);

        eval("\$headerinc = \"".$template->get('headerinc')."\";");
        echo $headerinc;

        $upload->process_file();
        $filestatus = $upload->parse_status($upload->get_status());
        if ($upload->get_status() != 4) {
            ?>
            <script language="javascript" type="text/javascript">
                $(function() {
                    return window.top.$("#upload_Result").html('<?php echo addslashes($filestatus);?>');
                });
            </script>
            <?php
            exit;
        }
        $filedata = $upload->get_filedata();
        $csv_file = new CSV($filedata,2,true,$core->input['delimiter']);

        $csv_file->readdata_string();

        $csv_header = $csv_file->get_header();
        $stock_data = $csv_file->get_data();
        ?>
        <script language="javascript" type="text/javascript">
            $(function() {
                return window.top.$("#upload_Result").html('<?php echo addslashes(parse_datapreview($csv_header,$stock_data));?>');
            });
        </script>
        <?php
        exit;
    }
    elseif ($core->input['action'] == 'do_perform_import') {
        $error_handler = new ErrorHandler();
		$allowed_headers = array('spid'=>$lang->supplier,'pid'=>$lang->product,'date'=>$lang->date,'amount'=>$lang->amount,'currency'=>$lang->currency,'usdFxrate'=>$lang->fxrate,'quantity'=>$lang->quantity,'quantityUnit'=>$lang->quantityunit,'orderId'=>$lang->orderid,'orderLineId'=>$lang->orderlineid,'TransID'=>$lang->transid,'saleType'=>$lang->saletype);
        $allowed_formatdates = array(1=>'m/d/Y',2=>'m-d-Y',3=>'d/m/Y',4=>'d-m-Y',5=>'Y-m-d');
        /*
		$headers_cache = array();
        for ($i = 0; $i < count($allowed_headers); $i++) {
            if ((empty($core->input['selectheader_'.$i])) || (in_array($core->input['selectheader_'.$i],$headers_cache))) {
                if (empty($core->input['selectheader_'.$i])) {
                    $error_text = $lang->fillallrequiredfields; //clarify names
                    $error_title = $lang->emptyfield;
                }
                if (in_array($core->input['selectheader_'.$i],$headers_cache)) {
                    $error_text = $lang->fieldrepeated; //clarify names
                    $error_title = $lang->repeatedvalue;
                }

                $js = "<script language='javascript' type='text/javascript'>
							$('#errordialog').remove();
							$('body').append(\"<div id='errordialog' title= '{$error_title}'>{$error_text}</div>\");
							$('#errordialog').dialog({bgiframe: true, closeOnEscape: false, modal: true,width: 460, maxWidth: 460, minHeight: 50, zIndex: 1, draggable: false});
						</script>";
                output_xml("<status>false</status><message><![CDATA[{$js}]]></message>");
                exit;
            }
            else {
                $headers_cache[] = $core->input['selectheader_'.$i];
            }
        }
        unset($headers_cache);
		*/

        $stock_data = unserialize($session->get_phpsession('sdata_'.$core->input['identifier']));

        if (is_array($stock_data)) {
			for($i=0;;$i++) {
				if (isset($core->input['selectheader_'.$i])) {
					$headers_ordered[]=$core->input['selectheader_'.$i];
				}
				else
				{
					break;
				}
			}
			foreach($stock_data as $rowkey=>$row) {
				$counter=0;
				foreach($row as $columnkey=>$column) {
					unset($stock_data[$rowkey][$columnkey]);
					$stock_data[$rowkey][$headers_ordered[$counter++]]=$column;
				}
			}
			foreach($stock_data as $rowkey=>$row) {
				$problems=false;
				foreach($row as $columnkey=>$column) {
					if ($columnkey=='spid') {
						if ($core->input['resolvesuppliername']=='on')
						{
							$row['spid']= resolve_entity($column,'s');
						}
						else
						{
							$row['spid']= $column;
						}
					} elseif ($columnkey=='pid') {
						if ($core->input['resolveproductname']=='on')
						{
							$row['pid']= resolve_productname($column,$row['spid']);
						}
						else
						{
							$row['pid']= $column;
						}
					} elseif($columnkey=='date') {
						$row['date']=parse_date($allowed_formatdates[$core->input['dateformat']],$column);
						if (!isset($row['date'])) {
							$problems=true;
							$error_handler->record('errordateformat', $column);
						}
					}
				}
				$row['foreignSystem']=$core->input['foreignSystem'];
				$row['affid']=$core->input['filteraffiliate'];
				$row['saleType']='ski';
				if (!$db->insert_query('integration_mediation_stockpurchases',$row))
				{
					$error_handler->record('database_insert', 'row '.$rowkey);
				}
			}
		}
		$import_errors = $error_handler->get_errors_inline();
        $log->record();
        if ($import_errors!= '') {
            output_xml("<status>false</status><message><![CDATA[$lang->resulterror<br/>{$import_errors}]]></message>");
        }
        else {
            output_xml("<status>true</status><message><![CDATA[$lang->successfullysaved $rowkey entries]]></message>");
        }
    }
    else {
        output_xml("<status>true</status><message><![CDATA[$lang->resulterror<hr>No reason to be here. Something went terribly wrong.]]></message>");
    }
}

function parse_datapreview($csv_header,$data) {
    global $session,$lang,$core;
    $output .= '<span class="subtitle">'.$lang->importstockpreview.'</span><br /><form id="perform_stock/import_Form"><table class="datatable"><tr>'; //Lng file title
	$allowed_headers = array('spid'=>$lang->supplier,'pid'=>$lang->product,'date'=>$lang->date,'amount'=>$lang->amount,'currency'=>$lang->currency,'usdFxrate'=>$lang->fxrate,'quantity'=>$lang->quantity,'quantityUnit'=>$lang->quantityunit,'orderId'=>$lang->orderid,'orderLineId'=>$lang->orderlineid,'TransID'=>$lang->transid,'saleType'=>$lang->saletype);
    foreach ($csv_header as $header_key=>$header_val) {
        $output .= '<td><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
        $output .= '<option value="">&nbsp;</option>';
        foreach ($allowed_headers as $allowed_header_key=>$allowed_header_val) {
            if ((strtolower($header_val) == strtolower($allowed_header_key)) || (strtolower($header_val) == strtolower($allowed_header_val))) {
				$selected_header = ' selected="selected"';
            }
            else {
				$selected_header = '';
            }
            $output .= '<option value="'.$allowed_header_key.'"'.$selected_header.'>'.$allowed_header_val.'</option>';
            $selected_header = '';
        }
        $output .= '</select></td>';
    }
    $output .= '</tr>';

    /* Loop over and display the CSV attendance data - START */
    foreach ($data as $key=>$val) {
        $rowclass = alt_row($rowclass);
        $output .= '<tr class="'.$rowclass.'">';
        foreach ($val as $value) {
            $output .= '<td>'.$value.'</td>';
        }
        $output .='</tr>';
    }
    /* Loop over the CSV stock data - END */
    $identifier = md5(uniqid(microtime()));
    /* serialize and send the stock_data via user session */
    $session->set_phpsession(array('sdata_'.$identifier=>serialize($data)));

    $output .= '<tr><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="dateformat" id="dateformat" value="'.$core->input['dateformat'].'"/></table></form><hr /><input type="button" value="'.$lang->savecaps.'" class="button" id="perform_stock/import_Button" name="perform_stock/import_Button"/></br>';
    $output .= '<div id="perform_stock/import_Results"></div>';
    return trim($output);
}

function resolve_productname($product,$supplier = '') {
    global $db,$options;
    /* Check mediation table */
    $match = $db->fetch_field($db->query('SELECT foreignId FROM '.Tprefix.'integration_mediation_products WHERE foreignName="'.$db->escape_string($product).'" AND affid="'.$db->escape_string($options['affid']).'" AND foreignSystem="'.$db->escape_string($options['foreignSystem']).'"'),'foreignId');
    if (!empty($match)) {
        return $match;
    }
    else {
        /* Check products table */
        $local = $db->fetch_field($db->query('SELECT pid FROM '.Tprefix.'products WHERE name="'.$db->escape_string($product).'"'),'pid');
        $new_product = array(
            'foreignSystem'=>$options['foreignSystem'],
            'foreignId'=>substr(md5(uniqid(microtime())),1,5).bin2hex($product),
            'foreignName'=>$product,
            'affid'=>$options['affid'],
            'localId'=>$local
        );

        if (!empty($supplier)) {
            $new_product['foreignSupplier'] = $supplier;
        }
        $db->insert_query('integration_mediation_products',$new_product);
        return $new_product['foreignId'];
    }
    return false;
}

function resolve_entity($entity,$type) {
    global $db,$options;
    /* Check mediation table */
    $match = $db->fetch_field($db->query('SELECT foreignId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName="'.$db->escape_string($entity).'" AND affid="'.$db->escape_string($options['affid']).'" AND foreignSystem="'.$db->escape_string($options['foreignSystem']).'" AND entityType="'.$db->escape_string($type).'"'),'foreignId');
    if (!empty($match)) {
        return $match;
    }
    else {
        $local = $db->fetch_field($db->query('SELECT eid FROM '.Tprefix.'entities WHERE companyName="'.$db->escape_string($entity).'" AND type="'.$db->escape_string($type).'"'),'eid');

        $new_entity = array(
            'foreignSystem'=>$options['foreignSystem'],
            'foreignId'=>substr(md5(uniqid(microtime())),1,5).bin2hex($entity),
            'foreignName'=>$entity,
            'affid'=>$options['affid'],
            'entityType'=>$type,
            'localId'=>$local
        );
        $db->insert_query('integration_mediation_entities',$new_entity);
        return $new_entity['foreignId'];
    }
    return false;
}

function custom_sort($a,$b) {
    if ($a == $b) {
        return 0;
    }
    if ($a > $b) {
        return 1;
    }
    else {
        return -1;
    }
}

function custom_sort_reverse($a,$b) {
    if ($a == $b) {
        return 0;
    }
    if ($a > $b) {
        return -1;
    }
    else {
        return 1;
    }
}
?>