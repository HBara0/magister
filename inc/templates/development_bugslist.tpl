<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->bugslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->bugslist}</h1>
            <table class='datatable'>
                <thead>
                <th style='width: 2%;'>ID</th>
                <th>Title</th>
                <th style='width: 10%;'>Severity</th>
                <th style='width: 10%;'>Priority</th>
                <th style='width: 15%;'>Reported On</th>
            </thead>
            <tbody>
                {$bugs_list}
            </tbody>
        </table>
    </td>
</tr>
{$footer}
</body>
</html>