<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->qrperformancereport}</title>
        {$headerinc}
        <link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->qrperformancereport}<br /><small>{$lang->q}{$report_data[quarter]}, {$report_data[year]}</small></h1>

            <table class="datatable"  style="width:70%" border="0" cellspacing="0" cellpadding="2">
                <thead>
                    <tr {$display[charts]}>
                        <td colspan="2">
                            <img src ="{$mkrratingbarchart}" alt=""/>
                            <img src ="{$daystocompletion_bchart[daysfromqstartchart]}" alt=""/>
                            <img src ="{$daystocompletion_bchart[daysfromreportcreationchart]}" alt=""/>
                            <img src ="{$daystocompletion_bchart[daystoimportfromqstartchart]}" alt=""/>
                            <img src ="{$daystocompletion_bchart[daystoimportfromcreationchart]}" alt=""/>
                        </td>
                    </tr>
                    <tr class="thead"><td colspan="2" style="text-align: center;font-size: large">{$lang->overview}</td></tr>
                    <tr class="subtitle"  {$display[allaffiliates]}>
                        <td colspan="2">{$lang->allaffiliates}</td>
                    </tr>
                    <tr {$display[allaffiliates]}>
                        <td>{$lang->avgmkrrating}</td>
                        <td>
                            <div class="ratebar" style="display:inline-block; background-color:#FFF;">
                                <div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="{$avgrating[allaffiliates]}"></div>
                            </div>
                            <strong> {$avgrating[allaffiliates]}</strong>
                        </td>
                    </tr>
                    <tr {$display[allaffiliates]}>
                        <td>{$lang->avg} {$lang->daystocompletion} - {$lang->frombeginingofquarter}</td>
                        <td>{$all_aff_avg[daysfromqstart]}</td>
                    </tr>
                    <tr {$display[allaffiliates]}>
                        <td>{$lang->avg} {$lang->daystocompletion} - {$lang->fromcreationdate}</td>
                        <td>{$all_aff_avg[daysfromreportcreation]}</td>
                    </tr>

                    <tr {$display[allaffiliates]}>
                        <td>{$lang->avg} {$lang->daystoimportdata} - {$lang->frombeginingofquarter}</td>
                        <td>{$all_aff_avg[daystoimportfromqstart]}</td>
                    </tr>
                    <tr {$display[allaffiliates]}>
                        <td>{$lang->avg} {$lang->daystoimportdata} - {$lang->fromcreationdate}</td>
                        <td>{$all_aff_avg[daystoimportfromcreation]}</td>
                    </tr>
                </thead>
                <tbody>

                    {$affiliate_report}
                </tbody>
                <tfoot>

                </tfoot>

            </table>
            <hr />
        </td>
    </tr>
    {$footer}
</body>
</html>