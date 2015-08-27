<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->tasksboard}</title>
        {$headerinc}
        <script>
            $(function () {
                var tabs = $("#taskstabs").tabs();
                $('#tasksboard_tour').joyride({
                    autoStart: true,
                    'cookieMonster': true, // true/false for whether cookies are used
                    'cookieName': 'tasksboard_tour', // choose your own cookie name
                    'cookieDomain': false,
                });
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
                    <li><a href="#taskstabs-1" id="taskstabs-1_btn">{$lang->assignedtome}</a></li>
                    <li><a href="#taskstabs-2" id="taskstabs_2_btn">{$lang->createdbyme}</a></li>
                    <li><a href="#taskstabs-3" id="taskstabs-3_btn">{$lang->sharedwithme}</a></li>
                </ul>
                <div id="loadindsection"></div>
                <div id="taskstabs-1">{$calendar_taskboard_assigned}</div>
                <div id="taskstabs-2">{$calendar_taskboard_createdby}</div>
                <div id="taskstabs-3">{$calendar_taskboard_shared}</div>
            </div>
        </td>
    </tr>
    {$footer}

    <ol id="tasksboard_tour">
        <li data-id="taskstabs-1_btn"><p>Here you can see all tasks assigned to you. This also include the tasks your created for yourself.</p></li>
        <li data-id="taskstabs_2_btn"><p>Any task that you created shows up here, regardless if assigned to you or someone else.</p></li>
        <li data-id="taskstabs-3_btn" data-button="{$lang->close}Close"><p>Any task that is shared with you shows up here. It is usually created by other users.</p></li>
    </ol>
</body>
</htmt>