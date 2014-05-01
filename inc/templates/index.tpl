<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->reporting}</title>
        {$headerinc}
        <script src="/js/fillreport.js" type="text/javascript"></script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->reportsoverview}</h3>
            <div style="width:45%; float:left">
                <ul>
                    {$admin_overview}
                    <li>{$lang->overviewcurrentquarter}</li>
                    <li> <em>{$lang->overviewall}</em></li>
                </ul>
            </div>

            <div style="width:45%; float:right">
                <strong>{$lang->duexdays}</strong>
                <ul>
                    {$due_reports_list}
                </ul>

                <strong>{$lang->lastfinalized}</strong>
                <ul>
                    {$last_reports_list}
                </ul>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>