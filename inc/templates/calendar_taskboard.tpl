<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->addentity}</title>
        {$headerinc}
        <script src="./js/redactor.min.js" type="text/javascript"></script>
        <link href="./css/redactor.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        {$header}
    <tr>
        {$menu}

        <td class="contentContainer">
            <h1>{$lang->taskboard}</h1>

            <table class="datatable datatable-striped">
                <tbody>
                <th>task</th>
                <th>dudate</th>
                <th> to do</th>
                <th> in progress</th>
                <th> Completed </th>
                    {$calendar_taskboard_rows}
            </tbody>
        </table>

    </td>
</tr>
{$footer}
{$taskdetailsbox}
</body>

</htmt>
