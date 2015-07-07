<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->tasksboard}</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        {$headerinc}

        <script>
            $(function () {
                var tabs = $("#taskstabs").tabs();
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->tasksboard}</h1>
            <div id="taskstabs"> <!--template-->
                <ul>
                    <li><a href="#taskstabs-1">{$lang->assignedtome}</a></li>
                    <li><a href="#taskstabs-2">{$lang->createdbyme}</a></li>
                    <li><a href="#taskstabs-3">{$lang->sharedwithme}</a></li>
                </ul>
                <div id="loadindsection"></div>
                <div id="taskstabs-1">{$calendar_taskboard_assigned}</div>
                <div id="taskstabs-2">{$calendar_taskboard_createdby}</div>
                <div id="taskstabs-3">{$calendar_taskboard_shared}</div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</htmt>