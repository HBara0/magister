<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->attendancelog}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td colspan="2" class="contentContainer">
            <h1>{$lang->attendancelog}</h1>
            <hr>
            <h4>From {$report['fromdate_output']} To {$report['todate_output']}</h4>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} | UL : {$lang->unpaidleave}</span>
            </hr>
            <div>
                {$output}
            </div>
            <div align="right">{$tools}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>