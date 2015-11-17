<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: trialbalance.php
 * Created:        @zaher.reda    Dec 16, 2014 | 11:02:48 AM
 * Last Update:    @zaher.reda    Dec 16, 2014 | 11:02:48 AM
 */

require './inc/integration_init.php';

if(!$core->input['action']) {
    foreach($integration_affiliates as $affiliate) {
        if(!in_array($affiliate->affid, $core->user['affiliates'])) {
            continue;
        }
        $selectlists_data['organisations'][$affiliate->integrationOBOrgId] = $affiliate->name;
    }
    $selectlists['organisation'] = parse_selectlist('organisation', 1, $selectlists_data['organisations'], '');

    $curr_objs = Currencies::get_data('numCode IS NOT NULL', array('returnarray' => true));

    foreach($curr_objs as $curr) {
        $currencies[$curr->get_displayname()] = $curr->get_displayname();
    }
    $selectlists['organisation'] .= parse_selectlist('trxcurrency', 3, $currencies, '', 0, '', array('blankstart' => true));

    eval("\$outputpage = \"".$template->get('finance_intgtrialbalance_options')."\";");
    output_page($outputpage);
}
else {
    if($core->input['action'] == 'viewtb') {
        if(!in_array($integration_affiliates_index[$core->input['organisation']], $core->user['affiliates'])) {
            error($lang->sectionnopermission);
            exit;
        }
        $core->input['fromDate'] = strtotime($core->input['fromDate']);
        if(empty($core->input['fromDate'])) {
            $core->input['fromDate'] = strtotime('last month');
        }

        $core->input['toDate'] = strtotime($core->input['toDate']);
        if(empty($core->input['toDate'])) {
            $core->input['toDate'] = TIME_NOW;
        }

        if($core->input['toDate'] < $core->input['fromDate']) {
            error('Mismatched from and to dates.');
            exit;
        }
        $parameters = $core->input;
        $parameters['fromDate'] = date('Y-m-d', $parameters['fromDate']);
        $parameters['toDate'] = date('Y-m-d', $parameters['toDate']);

        // $parameters['trxcurrency'] = 'EUR';
        $parameters['factaccttype'] = 'O';

        $integration->set_organisations(array($parameters['organisation']));

        $organisation = new IntegrationOBOrg($parameters['organisation']);
        $currency = new IntegrationOBCurrency($parameters['trxcurrency'], $intgdb);
        $currency = IntegrationOBCurrency::get_data('iso_code=\''.$parameters['trxcurrency'].'\'');
        $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);
        $amounts_fields = array('saldo_inicial', 'amtacctcr', 'amtacctdr', 'saldo_final');
        $columns = array('currency' => $lang->currency, 'saldo_inicial_source' => $lang->initialbalance, 'amtsourcedr' => $lang->debit, 'amtsourcecr' => $lang->credit, 'saldo_final_source' => $lang->balance, 'saldo_inicial' => $lang->initialbalance, 'amtacctdr' => $lang->debit, 'amtacctcr' => $lang->credit, 'saldo_final' => $lang->balance);
        $columns_cat = array('desc' => 2, 'originalcurrency' => 4, 'legalcurrency' => 4);

        if(isset($parameters['trxcurrency']) && !empty($parameters['trxcurrency'])) {
            $query_where .= " AND F.c_currency_id = '".$currency->c_currency_id."'";
        }

        $sql = "SELECT ID, ACCOUNT_ID, NAME, currency,
            SUM(SALDO_INICIAL) AS SALDO_INICIAL,
            SUM(AMTACCTDR) AS AMTACCTDR,
            SUM(AMTACCTCR) AS AMTACCTCR,
            SUM(SALDO_INICIAL+AMTACCTDR-AMTACCTCR) AS SALDO_FINAL,
            SUM(SALDO_INICIAL_source) AS SALDO_INICIAL_source,
            SUM(amtsourcedr) AS amtsourcedr,
            SUM(amtsourcecr) AS amtsourcecr,
            SUM(SALDO_INICIAL_source+amtsourcedr-amtsourcecr) AS SALDO_FINAL_source,
            groupbyid,
            groupbyname
        FROM
        ((SELECT ID, ACCOUNT_ID, NAME, currency,
            0 AS AMTACCTDR, 0 AS AMTACCTCR,
            COALESCE(SUM(AMTACCTDR-AMTACCTCR), 0) AS SALDO_INICIAL,
            0 AS amtsourcedr, 0 AS amtsourcecr,
            COALESCE(SUM(amtsourcedr-amtsourcecr), 0) AS SALDO_INICIAL_source,
            groupbyname, groupbyid
            FROM
                ((SELECT F.ACCOUNT_ID AS ID, EV.VALUE AS ACCOUNT_ID, EV.NAME AS NAME, C.iso_code AS currency,
                     F.AMTACCTDR, F.AMTACCTCR, F.amtsourcedr, F.amtsourcecr, F.FACTACCTTYPE, F.DATEACCT, c_bpartner.c_bpartner_id AS groupbyid,to_char(c_bpartner.name) AS groupbyname
                FROM C_ELEMENTVALUE EV, FACT_ACCT F
                LEFT JOIN C_BPARTNER ON f.C_BPARTNER_ID = C_BPARTNER.C_BPARTNER_ID
                LEFT JOIN M_PRODUCT ON f.M_PRODUCT_ID = M_PRODUCT.M_PRODUCT_ID
                LEFT JOIN C_PROJECT ON f.C_PROJECT_ID = C_PROJECT.C_PROJECT_ID
                LEFT JOIN C_CURRENCY c ON c.c_currency_id=F.c_currency_id
                WHERE F.ACCOUNT_ID = EV.C_ELEMENTVALUE_ID
                    AND EV.ELEMENTLEVEL = 'S'
                    AND f.AD_ORG_ID IN('".$parameters['organisation']."')
                    AND F.AD_CLIENT_ID IN ('0','C08F137534222BD001345B7B2E8F182D')
                    AND 1=1
                    AND F.DATEACCT < '".$parameters['fromDate']."'
                    AND F.C_ACCTSCHEMA_ID = '".$organisation->c_acctschema_id."'
                    AND F.ISACTIVE = 'Y'
                    ".$query_where."
                )
            UNION ALL
                (SELECT F.ACCOUNT_ID AS ID, EV.VALUE AS ACCOUNT_ID, EV.NAME AS NAME, C.iso_code AS currency,
                F.AMTACCTDR, F.AMTACCTCR, F.amtsourcedr, F.amtsourcecr, F.FACTACCTTYPE, F.DATEACCT, c_bpartner.c_bpartner_id AS groupbyid, to_char(c_bpartner.name) AS groupbyname
                    FROM C_ELEMENTVALUE EV, FACT_ACCT F
                    LEFT JOIN C_BPARTNER ON f.C_BPARTNER_ID = C_BPARTNER.C_BPARTNER_ID
                    LEFT JOIN M_PRODUCT ON f.M_PRODUCT_ID = M_PRODUCT.M_PRODUCT_ID
                    LEFT JOIN C_PROJECT ON f.C_PROJECT_ID = C_PROJECT.C_PROJECT_ID
                    LEFT JOIN C_CURRENCY c  ON c.c_currency_id=F.c_currency_id
                WHERE F.ACCOUNT_ID = EV.C_ELEMENTVALUE_ID
                    AND EV.ELEMENTLEVEL = 'S'
                    AND    f.AD_ORG_ID IN('".$parameters['organisation']."')
                    AND    F.AD_CLIENT_ID IN ('0','C08F137534222BD001345B7B2E8F182D')
                    AND 3=3
                    AND F.DATEACCT = '".$parameters['fromDate']."'
                    AND F.C_ACCTSCHEMA_ID = '".$organisation->c_acctschema_id."'
                    AND F.FACTACCTTYPE = '".$parameters['factaccttype']."'
                    AND F.ISACTIVE = 'Y'
                    ".$query_where."
            )) A
            GROUP BY ACCOUNT_ID, ID, groupbyname, groupbyid, NAME, currency
            HAVING SUM(AMTACCTDR) - SUM(AMTACCTCR) <> 0 )
            UNION
            (SELECT ID, ACCOUNT_ID, NAME, currency,
                SUM(AMTACCTDR) AS AMTACCTDR,
                SUM(AMTACCTCR) AS AMTACCTCR,
                0 AS SALDO_INICIAL,
                SUM(amtsourcedr) AS amtsourcedr,
                SUM(amtsourcecr) AS amtsourcecr,
                0 AS SALDO_INICIAL_source,
                groupbyname, groupbyid
            FROM (SELECT F.ACCOUNT_ID AS ID, EV.VALUE AS ACCOUNT_ID, EV.NAME AS NAME, C.iso_code AS currency,
            F.AMTACCTDR, F.AMTACCTCR, F.amtsourcedr, F.amtsourcecr, F.FACTACCTTYPE, c_bpartner.c_bpartner_id AS groupbyid, to_char(c_bpartner.name) AS groupbyname
            FROM C_ELEMENTVALUE EV, FACT_ACCT F
            LEFT JOIN C_BPARTNER ON f.C_BPARTNER_ID = C_BPARTNER.C_BPARTNER_ID
            LEFT JOIN M_PRODUCT ON f.M_PRODUCT_ID = M_PRODUCT.M_PRODUCT_ID
            LEFT JOIN C_PROJECT ON f.C_PROJECT_ID = C_PROJECT.C_PROJECT_ID
            LEFT JOIN C_CURRENCY c ON c.c_currency_id=F.c_currency_id
            WHERE F.ACCOUNT_ID = EV.C_ELEMENTVALUE_ID
                AND EV.ELEMENTLEVEL = 'S'
                AND  f.AD_ORG_ID IN('".$parameters['organisation']."')
                AND  F.AD_CLIENT_ID IN ('0','C08F137534222BD001345B7B2E8F182D')
                AND 2=2
                AND DATEACCT >= '".$parameters['fromDate']."'
                AND DATEACCT < '".$parameters['toDate']."'
                AND F.C_ACCTSCHEMA_ID = '".$organisation->c_acctschema_id."'
                AND F.FACTACCTTYPE <> '".$parameters['factaccttype']."'
                AND F.FACTACCTTYPE <> 'R'
                AND F.FACTACCTTYPE <> 'C'
                AND F.ISACTIVE = 'Y'
                ".$query_where."
            ) B
            GROUP BY ACCOUNT_ID, ID, groupbyname, groupbyid, NAME, currency
            HAVING SUM(AMTACCTDR) <> 0 OR SUM(AMTACCTCR) <> 0 )) C
        GROUP BY ACCOUNT_ID, ID, groupbyid, groupbyname, NAME, currency
        ORDER BY ACCOUNT_ID, ID, groupbyname, groupbyid, NAME, currency";

        $query = $intgdb->query($sql);
        $last_account = null;

        $styles['border'] = 'border-right: 1px solid #CCC;';
        $styles['altrow'] = 'background-color: #F2FAED;';
        $styles['thead'] = 'background-color: #92D050;';

        $output = '<h1>'.$lang->trialbalance.'<br /><small>'.$organisation->name.' <br />'.$parameters['fromDate'].' - '.$parameters['toDate'].'</small></h1>';

        $output .= '<table width="100%" style="border-spacing: 0px;'.$styles['border'].'">';
        $output .= '<tr style="'.$styles['thead'].'">';
        foreach($columns_cat as $cat => $colspan) {
            if(!isset($lang->$cat)) {
                $lang->$cat = $cat;
            }
            $output .= '<th colspan='.$colspan.'>'.$lang->$cat.'</th>';
        }
        $output .= '</tr>';

        $output .= '<tr style="'.$styles['thead'].'"><th>&nbsp;</th>';
        foreach($columns as $column) {
            $output .= '<th>'.$column.'</th>';
        }
        $output .= '</tr>';

        while($fact = $intgdb->fetch_assoc($query)) {
            $current_account = $fact['account_id'];

            if($current_account != $last_account) {
                if(isset($totals[$last_account])) {
                    foreach($columns as $colum_id => $column_value) {
                        if(isset($totals[$last_account][$colum_id])) {
                            $output_section_head .= '<td style="text-align:right;'.$styles['border'].'"><h3>'.$numfmt->format($totals[$last_account][$colum_id]).'</h3></td>';
                        }
                        else {
                            $output_section_head .= '<td></td>';
                        }
                    }
                }
                $output_section_head .= '</tr>';
                $output .= $output_section_head.$output_section_rows;
                unset($output_section_head, $output_section_rows);
                $output_section_head = '<tr style="'.$styles['altrow'].'"><td><h3>'.$fact['account_id'].' '.$fact['name'].'</h3></td>';
            }

            if(empty($fact['groupbyname'])) {
                $fact['groupbyname'] = 'N/A';
            }
            $output_section_rows .= '<tr><td style="'.$styles['border'].'"> '.$fact['groupbyname'].'</td>';
            foreach($columns as $colum_id => $column_value) {
                $output_section_rows .= parse_datacell($fact[$colum_id]);
            }

            $output_section_rows .= '</tr>';

            foreach($amounts_fields as $field) {
                $totals[$fact['account_id']][$field] += $fact[$field];
                $grandtotal[$field] += $fact[$field];
            }
            $last_account = $fact['account_id'];
        }

        /* Parse Last Entry */
        if(isset($totals[$last_account])) {
            foreach($columns as $colum_id => $column_value) {
                if(isset($totals[$last_account][$colum_id])) {
                    $output_section_head .= '<td style="text-align:right;'.$styles['border'].'"><h3>'.$numfmt->format($totals[$last_account][$colum_id]).'</h3></td>';
                }
                else {
                    $output_section_head .= '<td></td>';
                }
            }
        }
        $output_section_head .= '</tr>';
        $output .= $output_section_head.$output_section_rows;

        /* Parse Grand Total */
        $output .= '<tr><th>Grand Total</th>';
        foreach($columns as $colum_id => $column_value) {
            if(isset($grandtotal[$colum_id])) {
                $output .= '<td style="text-align:right;'.$styles['border'].'"><h3>'.$numfmt->format($grandtotal[$colum_id]).'</h3></td>';
            }
            else {
                $output .= '<td>&nbsp;</td>';
            }
        }
        $output .= '</tr>';
        $output .= '</table>';
        output($output);
    }
}
function parse_datacell($data) {
    global $numfmt, $styles;

    if(is_numeric($data)) {
        return '<td style="text-align:right;'.$styles['border'].'">'.$numfmt->format($data).'</td>';
    }
    else {
        return '<td style="'.$styles['border'].'">'.$data.'</td>';
    }
}

?>