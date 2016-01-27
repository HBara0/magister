<?php
require '../inc/init.php';
$core->input['action'] = 'autosendfxrates';

if($_REQUEST['authkey'] == 'asfasdkjj!h4k23jh4k2_3h4k23jh') {
    $currency_obj = new Currencies('USD');
    $finmanagers = array();
    $fxrates = array();
    $affiliates_currencies = array();
    $from = strtotime('first day of last month');
    $to = strtotime('last day of last month');
    $current_dates = currentquarter_info(true);
    $lastavgs_fields = array();
    $fxrates['EUR']['latest'] = $currency_obj->get_lastmonth_fxrate('EUR', array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
    $fxrates['EUR']['average'] = $currency_obj->get_average_fxrate('EUR', array('from' => $from, 'to' => $to));

    $email_data = array(
            'from_email' => $core->settings['maileremail'],
            'from' => 'OCOS Mailer',
            'subject' => 'FX Rates for '.date('F Y', strtotime('last month'))
    );


    /* Get Fin managers - START */
    $query = $db->query('SELECT finManager FROM '.Tprefix.'affiliates WHERE finManager IS NOT NULL');

    $users_list = array(63 => 63, 3 => 3);
    if($db->num_rows($query) > 0) {
        while($finmanager = $db->fetch_assoc($query)) {
            $users_list[$finmanager['finManager']] = $finmanager['finManager'];
        }
    }
    /* Get Fin managers - END */

    $query = $db->query('SELECT u.uid, displayName, email, name, a.affid
				FROM '.Tprefix.'affiliatedemployees a
				INNER JOIN '.Tprefix.'users u ON (a.uid = u.uid)
				INNER JOIN '.Tprefix.'affiliates aff ON (aff.affid = a.affid)
				WHERE u.uid IN ('.implode(',', $users_list).')');

    if($db->num_rows($query) > 0) {
        while($finmanager = $db->fetch_assoc($query)) {
            if(!isset($finmanagers[$finmanager['uid']])) {
                $finmanagers[$finmanager['uid']]['name'] = $finmanager['displayName'];
                $finmanagers[$finmanager['uid']]['email'] = $finmanager['email'];
            }
            $finmanagers[$finmanager['uid']]['affiliates'][$finmanager['affid']] = $finmanager['name'];
        }
    }

// get affiliates currencies
    $affiliatecurrenciesquery = $db->query('SELECT affid, cur.alphaCode, cur.name
				FROM '.Tprefix.'countries c
				INNER JOIN '.Tprefix.'currencies cur ON (c.mainCurrency = cur.numCode)
				WHERE affid<>0');

    while($country_currency = $db->fetch_assoc($affiliatecurrenciesquery)) {
        $affiliates_currencies[$country_currency[affid]][$country_currency['alphaCode']]['name'] = $country_currency['name'];
        if(!isset($fxrates[$country_currency['alphaCode']]['latest'])) {
            $fxrates[$country_currency['alphaCode']]['latest'] = $currency_obj->get_lastmonth_fxrate($country_currency['alphaCode'], array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
        }
        if(!isset($fxrates[$country_currency['alphaCode']]['average'])) {
            $fxrates[$country_currency['alphaCode']]['average'] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => $from, 'to' => $to));
        }
        $current_quarter = $current_dates['quarter'] - 1;
        while($current_quarter > 0) {
            if(!isset($fxrates[$country_currency['alphaCode']]['pastavgs'][$current_quarter.'/'.date('Y')]) || empty($fxrates[$country_currency['alphaCode']]['pastavgs'][$current_quarter.'/'.date('Y')])) {
                $quarter_extremities = get_quarter_extremities($current_quarter, date('Y'));
                $fxrates[$country_currency['alphaCode']]['pastavgs'][$current_quarter.'/'.date('Y')] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => $quarter_extremities['start'], 'to' => $quarter_extremities['end']));
                $lastavgs_fields[$country_currency['alphaCode']][$current_quarter] = 'Q'.$current_quarter.'/'.date('Y');
            }
            $current_quarter--;
        }
        if(!isset($fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 1]) || empty($fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 1])) {
            $fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 1] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => strtotime('01-Jan-'.(date('Y') - 1)), 'to' => strtotime('31-Dec-'.(date('Y') - 1))));
            $lastavgs_fields[$country_currency['alphaCode']][4] = date('Y') - 1;
        }
        if(!isset($fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 2]) || empty($fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 2])) {
            $fxrates[$country_currency['alphaCode']]['pastavgs'][date('Y') - 2] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => strtotime('01-Jan-'.(date('Y') - 2)), 'to' => strtotime('31-Dec-'.(date('Y') - 2))));
            $lastavgs_fields[$country_currency['alphaCode']][5] = date('Y') - 2;
        }
    }

    foreach($finmanagers as $uid => $user) {
        foreach($user['affiliates'] as $affid => $name) {
            if(isset($affiliates_currencies[$affid])) {
                foreach($affiliates_currencies[$affid] as $code => $cname) {
                    $user['currencies'][$code] = $fxrates[$code];
                }
            }
        }
        if(!array_key_exists('EUR', $user['currencies']) && !empty($fxrates['EUR']['average']) && !empty($fxrates['EUR']['latest'])) {
            $euroline .= '<tr><td style="border: 1px solid black;">EUR</td><td style="border: 1px solid black;">'.formatit($fxrates['EUR']['average']).' </td><td style="border: 1px solid black;">'.trim(formatit(1 / $fxrates['EUR']['average'])).'</td> <td style="border: 1px solid black;">'.trim(formatit($fxrates['EUR']['latest'])).' </td><td style="border: 1px solid black;">'.trim(formatit(1 / $fxrates['EUR']['latest']))."</td>";
            if(is_array($lastavgs_fields['EUR'])) {
                foreach($lastavgs_fields['EUR'] as $key => $lastavgtitle) {
                    if(!empty($fxrates['EUR']['pastavgs'][$lastavgtitle])) {
                        $euroline .='<td style="border: 1px solid black;">'.$fxrates['EUR']['pastavgs'][$lastavgtitle].'</td>';
                    }
                    else {
                        $euroline .='<td style="border: 1px solid black;">N/A</td>';
                    }
                    if(!isset($firstimeheader[$lastavgtitle])) {
                        $lasavgsheaders .= '<th style="border: 1px solid black;">AVG '.$lastavgtitle.'</th>';
                        $firstimeheader[$lastavgtitle] = 1;
                    }
                }
            }
            $euroline .='</tr>';
        }
        foreach($user['currencies'] as $alphacode => $rates) {
            if(is_array($rates['pastavgs']) && is_array($lastavgs_fields[$alphacode])) {
                ksort($lastavgs_fields[$alphacode]);
                foreach($lastavgs_fields[$alphacode] as $key => $lastavgtitle) {
                    if(!empty($rates['pastavgs'][$lastavgtitle])) {
                        $lastavgs[$lastavgtitle] = $rates['pastavgs'][$lastavgtitle];
                    }
                    else {
                        $lastavgs[$lastavgtitle] = 'N/A';
                    }
                    if(!isset($firstimeheader[$lastavgtitle])) {
                        $lasavgsheaders .= '<th style="border: 1px solid black;">AVG '.$lastavgtitle.'</th>';
                        $firstimeheader[$lastavgtitle] = 1;
                    }
                }
            }

            if(empty($rates['average']) && !empty($rates['latest'])) {
                $rates['average'] = $rates['latest'];
            }
            if(!empty($rates['average']) && !empty($rates['latest'])) {
                $actualdata .= '<tr><td style="border: 1px solid black;">'.$alphacode.'</td><td style="border: 1px solid black;">'.formatit($rates['average']).'</td><td style="border: 1px solid black;">'.trim(formatit(1 / $rates['average'])).'</td> <td style="border: 1px solid black;">'.trim(formatit($rates['latest'])).'</td><td style="border: 1px solid black;">'.trim(formatit(1 / $rates['latest'])).'</td>';
            }
            else {
                if(empty($rates['average'])) {
                    continue;
                }
                else {
                    if(empty($rates['latest'])) {
                        $rates['latest'] = $rates['average'];
                    }
                }
                $actualdata .= '<tr><td style="border: 1px solid black;">'.$alphacode.'</td><td style="border: 1px solid black;">'.formatit($rates['average']).'</td><td style="border: 1px solid black;">'.trim(formatit(1 / $rates['average'])).'</td><td style="border: 1px solid black;">'.trim(formatit($rates['latest'])).' </td><td style="border: 1px solid black;">'.trim(formatit(1 / $rates['latest'])).')</td>';
            }
            if(is_array($lastavgs)) {
                foreach($lastavgs as $curcode => $number) {
                    $actualdata .='<td style="border: 1px solid black;">'.$number.'</td>';
                }
            }
            $actualdata .='</tr>';
            unset($lastavgs);
        }
        $email_data['to'] = $user['email'];
        $email_data['message'] = '<pre style="font-size: 13px">Dear '.$user['name'].",<br>";
        $email_data['message'] .= "Please find below the average USD exchange rates for the past month.<br><br><strong><u>Please use the last rate for all your monthly reports</u></strong><br><br>";
        $email_data['message'] .= '<table style="border-collapse:collapse; border-spacing: 2px 2px;"><thead><tr style="background-color:#EAEAEA"><th style="border: 1px solid black;">Currency</th><th style="border: 1px solid black;"> AVG To</th><th style="border: 1px solid black;">AVG From</th><th style="border: 1px solid black;">LAST To</th><th style="border: 1px solid black;">LAST From</th>'.$lasavgsheaders.'</tr></thead><tbody>';
        $email_data['message'] .=$euroline.$actualdata.'</tbody></table><br>';
        $email_data['message'] .= "\nBest Regards,\n</pre>";
        print($email_data['message']);
        $mail = new Mailer($email_data, 'php');
        if($mail->get_status() == true) {
            $log->record($user['name'], 'success');
        }
        else {
            $log->record($user['name'], 'failed');
        }
        unset($actualdata, $lasavgsheaders, $euroline);
    }
}
?>
