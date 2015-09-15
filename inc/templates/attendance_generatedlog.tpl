<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->attendancelog}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->attendancelog}</h1>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave}</span>
            <div align="center">
                {$output}
            </div>
            <div align="right">{$tools}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>