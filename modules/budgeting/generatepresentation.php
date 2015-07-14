<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generatepresentation.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 11:12:30 AM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 11:12:30 AM
 */


ini_set(max_execution_time, 0);
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->input['export']) {
    $year = date('Y');
    if($core->input['affid']) {
        $extra_where = ' AND affid = '.$core->input['affid'];
    }
    for($yearlimits = 3; $yearlimits >= 0; $yearlimits--) {
        $cur_year = $year - $yearlimits;
        for($month = 1; $month < 13; $month++) {
            $times[$cur_year][$month]['start'] = strtotime('01-'.sprintf("%02d", $month).'-'.$cur_year.'');
            $times[$cur_year][$month]['end'] = strtotime(date('t', $times[$cur_year][$month]['start']).'-'.sprintf("%02d", $month).'-'.$cur_year.'');
        }
    }

    if(is_array($times)) {
        if(is_array($times)) {
            foreach($times as $year => $month) {
                foreach($month as $montnum => $timestamps) {
                    $orders[$year][$montnum] = IntegrationMediationSalesOrders::get_orders('date BETWEEN '.$timestamps['start'].' AND '.$timestamps['end'].$extra_where, array('returnarray' => true));
                    if(is_array($orders[$year][$montnum])) {
                        $lines = array();
                        foreach($orders[$year][$montnum] as $order) {
                            $order_lines = $order->get_orderlines();
                            if(is_array($order_lines)) {
                                foreach($order_lines as $key => $val) {
                                    $lines[$key] = $val;
                                }
                            }
                        }
                        $orderlines[$year][$montnum] = $lines;
                    }
                    else {
                        $orderlines[$year][$montnum] = '';
                    }
                }
            }
        }
    }
//parse orderlines according to requireements
    if(is_array($orderlines)) {
//get full fata for all years-Start
        foreach($orderlines as $year => $months) {
            if(is_array($months)) {
                if(!array_filter($months)) {
                    continue;
                }
                foreach($months as $month => $lines) {
                    if(is_array($lines)) {
                        foreach($lines as $line) {
                            $ordercurrency = $line->get_order()->get_currency();
                            if(!isset($line->costCurrency) || empty($line->costCurrency)) {
                                $costcurrency = new Currencies(840);
                            }
                            else {
                                $costcurrency = Currencies::get_data(array('alphaCode' => $line->costCurrency), array('returnarray' => false));
                                if(!is_object($costcurrency)) {
                                    continue;
                                }
                            }
                            if(is_object($ordercurrency)) {
                                $saleexchangerate = $ordercurrency->get_latest_fxrate($ordercurrency->alphaCode, array(), 'USD');
                            }
                            $costexchangerate = $costcurrency->get_latest_fxrate($costcurrency->alphaCode, array(), 'USD');
                            $data[$year][$month]['sales']+=$line->price * $line->quantity * $saleexchangerate;
                            $data[$year][$month]['costs']+=$line->cost * $line->quantity * $costexchangerate;
                            if(isset($line->spid) && !empty($line->spid)) {
                                $suppliers[$line->spid][$year]['sales'] += ($line->price * $line->quantity * $saleexchangerate) / 1000;
                                $suppliers[$line->spid][$year]['costs'] += ($line->cost * $line->quantity * $costexchangerate) / 1000;
                                $suppliers[$line->spid][$year]['income'] = $suppliers[$line->spid][$year]['sales'] - $suppliers[$line->spid][$year]['costs'];
                                if($year == date('Y')) {
                                    $currentyearsups_sales[$line->spid] = $suppliers[$line->spid][$year]['sales'];
                                    $suppliers_customers[$line->spid] ++;
                                    $currentyearsups_costs[$line->spid] = $suppliers[$line->spid][$year]['costs'];
                                    $currentyearsups_income[$line->spid] = $currentyearsups_sales[$line->spid] - $currentyearsups_costs[$line->spid];
                                }
                            }
                            else {
                                if(!isset($line->pid) && empty($line->pid)) {
                                    continue;
                                }
                                $product = IntegrationMediationProducts::get_products(array('foreignId' => $line->pid), array('returnarray' => false));
                                if(!is_object($product)) {
                                    continue;
                                }
                                $localsupplier = $product->get_localsupplier();
                                if(!is_object($localsupplier) || is_empty($localsupplier->eid)) {
                                    continue;
                                }
                                $suppliers[$localsupplier->eid][$year]['sales'] += ($line->price * $line->quantity * $saleexchangerate) / 1000;
                                $suppliers[$localsupplier->eid][$year]['costs'] += ($line->cost * $line->quantity * $costexchangerate) / 1000;
                                $suppliers[$localsupplier->eid][$year]['income'] = $suppliers[$localsupplier->eid][$year]['sales'] - $suppliers[$localsupplier->eid][$year]['costs'];
                                if($year == date('Y')) {
                                    $currentyearsups_sales[$localsupplier->eid] = $suppliers[$localsupplier->eid][$year]['sales'];
                                    $suppliers_customers[$localsupplier->eid] ++;
                                    $currentyearsups_costs[$localsupplier->eid] = $suppliers[$localsupplier->eid][$year]['costs'];
                                    $currentyearsups_income[$localsupplier->eid] = $currentyearsups_sales[$localsupplier->eid] - $currentyearsups_costs[$localsupplier->eid];
                                }
                            }
                        }
                        $data[$year][$month]['income'] = $data[$year][$month]['sales'] - $data[$year][$month]['costs'];
                        $totalyear[$year]['income']+=$data[$year][$month]['income'];
                        $totalyear[$year]['sales']+=$data[$year][$month]['sales'];
                    }
                    else {
                        $data[$year][$month]['income'] = $data[$year][$month]['sales'] = $data[$year][$month]['costs'] = '';
                    }
                }
            }
        }
        //get next year data from budget--START
        if($core->input['affid']) {
            $budgets = Budgets::get_data(array('affid' => $core->input['affid'], 'year' => (date('Y') + 1)), array('returnarray' => true, 'simple' => false));
        }
        else {
            $budgets = Budgets::get_data(array('year' => (date('Y') + 1)), array('returnarray' => true, 'simple' => false));
        }
        if(is_array($budgets)) {
            foreach($budgets as $budget) {
                $lines = $budget->get_budgetlines_objs();
                if(is_array($lines)) {
                    foreach($lines as $line) {
                        $currency = $line->get_currency();
                        if(is_object($currency)) {
                            $exchangerate = $currency->get_latest_fxrate($currency->alphaCode, array(), 'USD');
                        }
                        for($i = 1; $i < 7; $i ++) {
                            $data[(date('Y') + 1)][$i]['sales'] += (($line->amount * $line->s1Perc / 100) / 6 ) * $exchangerate;
                            $data[(date('Y') + 1)][$i]['income'] += (($line->amount * $line->s1Perc / 100) / 6 ) * $exchangerate;
                            $data[(date('Y') + 1)][$i]['costs'] = $data[(date('Y') + 1)][$i]['sales'] - $data[(date('Y') + 1)][$i]['income'];
                        }

                        for($i = 7; $i < 13; $i++) {
                            $data[(date('Y') + 1)][$i]['sales'] += (($line->amount * $line->s2Perc / 100) / 6 ) * $exchangerate;
                            $data[(date('Y') + 1)][$i]['income'] += (($line->amount * $line->s2Perc / 100) / 6 ) * $exchangerate;
                            $data[(date('Y') + 1)][$i]['costs'] = $data[(date('Y') + 1)][$i]['sales'] - $data[(date('Y') + 1)][$i]['income'];
                        }
                    }
                }
            }
        }
        //get next year data from budget--END
//get full fata for all years-End
        if(is_array($data)) {
            foreach($data as $year => $months) {
                $curentsales_total = $currentincome_total = 0;
                if(is_array($months)) {
                    foreach($months as $month => $types) {
                        foreach($types as $type => $number) {
                            //get cumulative income-START
                            if($type == 'income') {
                                $currentincome_total+=intval($number);
                                $final['cumulativeincome'][$year][date("F", mktime(0, 0, 0, $month, 10))] = $currentincome_total;
                                if($year == date('Y')) {
                                    $final['monthlysummary'][$type][date("F", mktime(0, 0, 0, $month, 10))] = intval($number);
                                }
                                continue;
                            }
                            //get cumulative income-END
                            //get monthly summary of current year-START
                            if($year == date('Y')) {
                                $final['monthlysummary'][$type][date("F", mktime(0, 0, 0, $month, 10))] = intval($number);
                            }
                            //get monthly summary of current year-END
                            //get cumulative sales-START
                            if($type == 'sales') {
                                $curentsales_total+= intval($number);
                                $final['cumulativesale'][$year] [date("F", mktime(0, 0, 0, $month, 10))] = $curentsales_total;
                            }
                            //get cumulative sales-END
                        }
                    }
                }
                else {
                    $final['cumulativesale'][$year] [date("F", mktime(0, 0, 0, $month, 10))] = $final['monthlysummary'][$type][date("F", mktime(0, 0, 0, $month, 10))] = $final['cumulativeincome'][$year][date("F", mktime(0, 0, 0, $month, 10))] = '';
                }
            }
        }
        //get top 10 suppliers sales and net=START
        if(is_array($currentyearsups_sales)) {
            asort($currentyearsups_sales);
            $top_salessups = array_reverse(array_slice($currentyearsups_sales, 0, 10, true), true);
        }
        if(is_array($currentyearsups_income)) {
            asort($currentyearsups_income);
            $top_netsups = array_reverse(array_slice($currentyearsups_income, 0, 10, true), true);
            foreach($currentyearsups_income as $eid => $number) {
                $supplierspec_income[$eid] = $number * 100 / $totalyear[date('Y')]['income'];
            }
            if(is_array($supplierspec_income)) {
                asort($supplierspec_income);
                $top_incomeperc = array_reverse(array_slice($supplierspec_income, 0, 10, true), true);
            }
        }
        if(is_array($top_incomeperc)) {
            foreach($top_salessups as $supid => $incomeperc) {
                $customernum = '';
                if(is_array($suppliers_customers)) {
                    $customernum = $suppliers_customers[$supid];
                }
                if(is_array($currentyearsups_sales)) {
                    $salesperc = $currentyearsups_sales[$supid] * 100 / $totalyear[date('Y')]['sales'];
                }
                $supplier = new Entities($supid);
                if(is_object($supplier)) {

                    $supname = str_replace(array(' ', '<', '>', '&', '{', '}', '*'), array('-'), $supplier->get_displayname());
                    $final['topincomepercsup']['%income'][$supname] = $incomeperc.'%';
                    $final['topincomepercsup']['%sales'][$supname] = $salesperc.'%';
                    $final['topincomepercsup']['#customers'][$supname] = $customernum;
                    $supname = '';
                }
            }
        }
        if(is_array($top_salessups)) {
            foreach($top_salessups as $supid => $currentsales) {
                if(is_array($suppliers[$supid])) {
                    foreach($suppliers[$supid] as $year => $type) {
                        if(is_array($type) && isset($type['sales']) && !empty($type['sales'])) {
                            $supplier = new Entities($supid);
                            if(is_object($supplier)) {
                                $final['topsalessuppliers'][$year][str_replace(array(' ', '<', '>', '&', '{', '}', '*'), array('-'), $supplier->get_displayname())] = $type['sales'];
                            }
                        }
                    }
                }
            }
        }
        if(is_array($top_netsups)) {
            foreach($top_netsups as $supid => $currentnet) {
                if(is_array($suppliers[$supid])) {
                    foreach($suppliers[$supid] as $year => $type) {
                        if(is_array($type) && isset($type['income']) && !empty($type['income'])) {
                            $supplier = new Entities($supid);
                            if(is_object($supplier)) {
                                $final['topnetsuppliers'][$year][str_replace(array(' ', '<', '>', '&', '{', '}', '*'), array('-'), $supplier->get_displayname())] = $type['income'];
                            }
                        }
                    }
                }
            }
        }
        //get top 10 suppliers sales and net=END
    }
    $aff = 'All';
    if($core->input['affid']) {
        $affiliate = new Affiliates($core->input['affid']);
        if(is_object($affiliate)) {
            $aff = $affiliate->alias;
        }
    }
//parse contents-START
    if(is_array($final)) {
        $langvariable = $pagedesc = '';
        foreach($final as $langvar => $rowhead) {
            $page = $rows = $tablehead = '';
            $langvariable = $langvar;
            $pagedesc = $langvariable.'desc';
            $colheader = 0;
            if(is_array($rowhead)) {
                foreach($rowhead as $row => $theads) {
                    $rows .= '<tr><td>'.$row.'</td>';
                    if(is_array($theads)) {
                        foreach($theads as $thead => $data) {
                            $rows.= '<td>'.number_format($data, 2, '.', ',').'</td>';
                            if($colheader == 0) {
                                $tablehead.='<th>'.$thead.'</th>';
                            }
                        }
                    }
                    else {
                        $tablehead.='<th>'.$thead.'</th>';
                    }
                    $colheader = 1;
                    $rows.='</tr>';
                }
            }
            $page = '<html xmlns:v = "urn:schemas-microsoft-com:vml" xmlns:o = "urn:schemas-microsoft-com:office:office" xmlns:x = "urn:schemas-microsoft-com:office:excel"
xmlns = "http://www.w3.org/TR/REC-html40">
<head>
   <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
          <meta name=ProgId content=Excel.Sheet>
          <meta name=Generator content="Microsoft Excel 11">
   <!--[if gte mso 9]><xml>
           <x:ExcelWorkbook>
          <x:ExcelWorksheets>
           <x:ExcelWorksheet>
            <x:Name>none</x:Name>
           <x:WorksheetOptions>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
  <x:WindowHeight>9210</x:WindowHeight>
  <x:WindowWidth>19035</x:WindowWidth>
  <x:WindowTopX>0</x:WindowTopX>
  <x:WindowTopY>75</x:WindowTopY>
  <x:ProtectStructure>False</x:ProtectStructure>
  <x:ProtectWindows>False</x:ProtectWindows>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head>
<body><table>
<thead><tr><th>'.$lang->$pagedesc.'</th></tr><tr><th></th>'.$tablehead.'</tr></thead>';
            $page.='<tbody>'.$rows.'</tbody>';
            $page.='</table></body></html>';
            $path = dirname(__FILE__).'\..\..\tmp\\bugetingexport\\'.uniqid($aff.$langvariable).'.html';
            $allpaths[$lang->$langvariable] = $path;
            $handle = fopen($path, 'w') or die('Cannot open file: '.$my_file);
            $writefile = file_put_contents($path, $page);
            continue;
        }
    }
    require dirname(__FILE__).'/../../PHPExcel/Classes/PHPExcel/IOFactory.php';

//Start Excel Convertion
//set excel styles- START
    $style['header'] = array(
            'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3D9140')
            ),
            'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri'
            )
    );
    $style['altrows'] = array(
            'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'C3F5F2')
            ),
    );
//set excel styles-END
    $outputFile = $aff."-report.xls";

//es are loaded to PHPExcel using the IOFactory load() method
    if(is_array($allpaths)) {
        $count = 0;
        foreach($allpaths as $title => $path) {
            if($count == 0) {
                $main_excel = PHPExcel_IOFactory::load($path);
                $main_excel->getSheet(0)->setTitle($title);

                $main_excel->getSheet(0)
                        ->getStyle('B2:Z2')
                        ->applyFromArray($style['header']);
                for($i = 3; $i <= 8; $i = $i + 2) {
                    $main_excel->getSheet(0)
                            ->getStyle('A'.$i.':Z'.$i)
                            ->applyFromArray($style['altrows']);
                }
                $count = 1;
                continue;
            }
            $tempexcel = PHPExcel_IOFactory::load($path);
            $excels[$title] = $tempexcel->getSheet(0);
            $tempexcel = '';
        }
        if(is_array($excels)) {
            foreach($excels as $title => $sheet) {
                $sheet->setTitle($title);
                $sheet->getStyle('B2:Z2')
                        ->applyFromArray($style['header']);
                for($i = 3; $i <= 9; $i = $i + 2) {
                    $sheet->getStyle('A'.$i.':Z'.$i)
                            ->applyFromArray($style['altrows']);
                }
                $main_excel->addSheet($sheet);
            }
            ob_clean();
            $objWriter = PHPExcel_IOFactory::createWriter($main_excel, "Excel5");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$outputFile");
            $objWriter->save('php://output');
        }
    }
}
if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates('affid IN ('.implode(',', $core->user['affiliates']).')', array('returnarray' => true));
    $affiliates_list = parse_selectlist('affid', 1, $affiliates, '', '', '', array('blankstart' => true));
    eval("\$generatepres = \"".$template->get('budgeting_generatepresentation')."\";");
    output_page($generatepres);
}