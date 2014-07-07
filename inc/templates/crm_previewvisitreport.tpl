<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$pagetitle}</h1>
            {$visitreportspages}  
            <p><hr /></p>
        <p>{$prev_visitreports_list}</p>
        <div align="right">{$tools}</div>
    </td>
</tr>
{$footer}
</body>
</html>