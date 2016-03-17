<div class="panel panel-default">
    <div class="jumbotron">
        <h1>{$user_display}</h1>
        <div class="subtitle" style="text-align:center;">From {$report[fromdate_output]} To {$report[todate_output]}</div>
    </div>
    <table style="width: 100%;" align="center" class="datatable">
        <thead>
            <tr>
                <th class="thead" style="width:35%;" colspan="2">{$lang->totaldays} </th>
                <th width="25%" class="thead" style="width:20%;">{$lang->totalhours}</th>
                <th width="13%" class="thead" style="width:15%;">{$lang->average}</th>
                <th width="13%" class="thead" style="width:15%;">{$lang->totaldeviation}</th>
                <th width="18%" class="thead" style="width:15%;">{$lang->percentage}</th>
            </tr>
        </thead>
        <tbody>
            <tr align="center">
                <td width="20%">{$lang->perioddays}</td>
                <td width="11%">{$overall_totals[total_period_days]}</td>
                <td class="altrow border_left">{$overall_totals[actualhours]}</td>
                <td class="altrow">{$overall_totals[average_hour_day]}</td>
                <td class="altrow">{$overall_totals[deviation]}</td>
                <td class="altrow" style="font-weight: bold;">{$overall_totals[workpercentage]}%</td>
            </tr>
            <tr>
                <td>{$lang->holidays}</td>
                <td>{$overall_totals[count_all_holidays]}</td>
                <td colspan="4" rowspan="7" style="width:65%;" class="altrow2 border_left">{$period_workshifts}<br />
                    {$lang->earliestarrival}: {$stats[earliest_arrival_output]} <br /> {$lang->latestdeparture}: {$stats[latest_departure_output]}</td>
            </tr>
            <tr>
                <td>{$lang->leaves}</td>
                <td>{$overall_totals[count_all_leaves]}</td>
            </tr>
            <tr>
                <td>{$lang->weekends}</td>
                <td>{$overall_totals[count_all_weekends]}</td>
            </tr>
            <tr>
                <td>{$lang->requireddays}</td>
                <td>{$required_days}</td>
            </tr>
            <tr>
                <td>{$lang->missingdays}</td>
                <td>{$overall_totals[count_absent_days]}</td>
            </tr>
            <tr>
                <td>{$lang->extraworkeddays}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>{$lang->actualworkingdays}</td>
                <td><u>{$overall_totals[actual_working_days]}</u></td>
        </tr>
        </tbody>
    </table>
    {$attendance_report_user_month}
    </hr>
</div>