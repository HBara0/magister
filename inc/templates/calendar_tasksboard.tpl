<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->tasksboard}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->tasksboard}</h1>
            <table class="datatable datatable-striped">
                <thead>
                <th>Task</th>
                <th style="width: 10%;">Due Date</th>
                <th style="width: 15%; text-align: center;">To-Do</th>
                <th style="width: 15%; text-align: center;">In Progress</th>
                <th style="width: 15%; text-align: center;">Completed</th>
            </thead>
            <tbody>
                {$calendar_taskboard_rows}
            </tbody>
        </table>
    </td>
</tr>
{$footer}
</body>
</htmt>