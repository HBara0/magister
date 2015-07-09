<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generatepresentation.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 11:12:30 AM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 11:12:30 AM
 */

//$array['lang']['headrow']['colhead'] = '1';
//if(is_array($array)) {
//    foreach($array as $langvar => $thead) {
//        if(is_array($thead)) {
//            foreach($thead as $header => $row) {
//
//            }
//        }
//    }
//}

$year = date('Y');
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
                $orders[$year][$montnum] = IntegrationMediationSalesOrders::get_orders('date BETWEEN '.$timestamps['start'].' AND '.$timestamps['end'], array('returnarray' => true));
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
            }
        }
    }
}
//parse orderlines according to requireements
if(is_array($orderlines)) {
//get full fata for all years-Start
    foreach($orderlines as $year => $months) {
        if(is_array($months)) {
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
                        $saleexchangerate = $ordercurrency->get_latest_fxrate($ordercurrency->alphaCode, array(), 'USD');
                        $costexchangerate = $costcurrency->get_latest_fxrate($costcurrency->alphaCode, array(), 'USD');
                        $data[$year][$month]['sales']+=$line->price * $line->quantity * $saleexchangerate;
                        $data[$year][$month]['costs']+=$line->cost * $line->quantity * $costexchangerate;
                        if(isset($line->spid) && !empty($line->spid)) {
                            $supplier[$line->spid][$year]['sales'] += $line->price * $line->quantity * $saleexchangerate;
                            $supplier[$line->spid][$year]['costs'] += $line->cost * $line->quantity * $costexchangerate;
                            $supplier[$line->spid][$year]['income'] = $supplier[$line->spid][$year]['sales'] - $supplier[$line->spid][$year]['costs'];
                            if($year == date('Y')) {
                                $currentyearsups_sales[$line->spid] = $supplier[$line->spid][$year]['sales'];
                                $currentyearsups_costs[$line->spid] = $supplier[$line->spid][$year]['costs'];
                                $currentyearsups_income[$line->spid] = $currentyearsups_sales[$line->spid] - $currentyearsups_costs[$line->spid];
                            }
                        }
                    }
                    $data[$year][$month]['income'] = $data[$year][$month]['sales'] - $data[$year][$month]['costs'];
                }
            }
        }
    }
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
                            $final['cumulativeincome'][$year][get_monnthname($month)] = $currentincome_total;
                            continue;
                        }
                        //get cumulative income-END
                        //get monthly summary of current year-START
                        if($year == date('Y')) {
                            $final['monthlysummary'][$type][get_monnthname($month)] = intval($number);
                        }
                        //get monthly summary of current year-END
                        //get cumulative sales-START
                        if($type == 'sales') {
                            $curentsales_total+= intval($number);
                            $final['cumulativesale'][$year] [get_monnthname($month)] = $curentsales_total;
                        }
                        //get cumulative sales-END
                    }
                }
            }
        }
    }
    //get top 10 suppliers sales and net=START
    if(is_array($currentyearsups_sales)) {
        asort($currentyearsups_sales);
        $top_salessups = array_reverse(array_slice($currentyearsups_sales, 0, 10));
    }
    if(is_array($currentyearsups_income)) {
        asort($currentyearsups_income);
        $top_netsups = array_reverse(array_slice($currentyearsups_income, 0, 10));
    }
    if(is_array($top_salessups)) {
        foreach($top_salessups as $supid => $currentsales) {
            if(is_array($suppliers[$supid])) {
                foreach($suppliers[$supid] as $year => $type) {
                    if(is_array($year) && isset($year['sales']) && !empty($year['sales'])) {
                        $supplier = new Entities($supid);
                        if(is_object($supplier)) {
                            $final['topsalessuppliers'][$year][$supplier->get_displayname()] = $year['sales'];
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
                    if(is_array($year) && isset($year['income']) && !empty($year['income'])) {
                        $supplier = new Entities($supid);
                        if(is_object($supplier)) {
                            $final['topnetsuppliers'][$year][$supplier->get_displayname()] = $year['income'];
                        }
                    }
                }
            }
        }
    }
    //get top 10 suppliers sales and net=END
}

//parse contents-START
if(is_array($final)) {
    foreach($final as $langvar => $rowhead) {
        $page = '';
        $colheader = 0;
        if(is_array($rowhead)) {
            foreach($rowhead as $row => $theads) {
                $rows .= '<tr><td>'.$row.'</td>';
                if(is_array($theads)) {
                    foreach($theads as $thead => $data) {
                        $row.= '<td>'.$data.'</td>';
                        if($colheader == 0) {
                            $tablehead.='<th>'.$thead.'</th>';
                        }
                    }
                }
                $colheader = 1;
                $rows.='<tr>';
            }
        }
        $page = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
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
</xml><![endif]--></head>
<body><table>
<thead><tr>'.$tablehead.'</tr></thead>';
        $page.='<tbody>'.$rows.'</tbody>';
        $page.='</table></body></html>';
        header("Content-disposition: attachment; filename=huge_document.html");
        header('Content-Type: text/html; charset=utf-8');
        echo($page);
        exit;
    }
    exit;
}
function get_monnthname($month) {
    switch($month) {
        case 1:
            return 'January';
        case 2:
            return 'February';
        case 3:
            return 'Mars';
        case 4:
            return 'April';
        case 5:
            return 'May';
        case 6:
            return 'June';
        case 7:
            return 'July';
        case 8:
            return 'August';
        case 9:
            return 'September';
        case 10:
            return 'October';
        case 11:
            return 'November';
        case 12:
            return 'December';
        default:
            return null;
    }
}
