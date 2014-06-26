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
            <h3>{$lang->bugslist}</h3>
            <table class='datatable'>
                <thead>
                <th style='width: 2%;'>ID</th>
                <th>Title</th>
                <th style='width: 10%;'>Module</th>
                <th style='width: 10%;'>File</th>
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