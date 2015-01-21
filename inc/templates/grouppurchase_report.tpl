<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->c}</title>
        {$headerinc}
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->grouppurchasetabular}</h1>

    <page>
        <div style="page-break-before:always;"></div>
        <table class="datatable" border="0" cellpadding="1" cellspacing="1" width="100%">
            <tbody>
                <tr class="thead">
                    <th style="vertical-align:central; padding:2px;  border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->product}</th>
                    <th style="vertical-align:central; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->saletype}</th>

                    {$groupurchase[monthead]}

                </tr>
            </tbody>
            {$grouppurchase_report_rows}
        </table>
    </page>


</td>
</tr>
</body>
</html>
