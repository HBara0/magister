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
            <h1>{$lang->attendancelog}
                <small><br />{$lang->fromdate} {$report[fromdate_output]} {$lang->todate} {$report[todate_output]}</small>
            </h1>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} |  HD : {$lang->halfday} | UL : {$lang->unpaidleave}</span>
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