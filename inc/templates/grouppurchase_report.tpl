<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->c}</title>
        {$headerinc}
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$reporttitle}</h1>

    <page>
        <div style="page-break-before:always;"></div>
        <table class="datatable" border="0" cellpadding="1" cellspacing="1" width="100%">
            <tbody>
                {$groupurchase[monthead]}
            </tbody>
            {$gpforecat_report}
            {$grouppurchase_report_rows}
        </table>
    </page>
</td>
</tr>
</body>
</html>
