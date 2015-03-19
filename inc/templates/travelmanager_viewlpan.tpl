<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->viewplan}</title>
        {$headerinc}
        <script type="text/javascript">

            $(function () {
                $('input[id="confirm_finalize"]').live('click', function () {
                    $('input[type="submit"][id^="perform_travelmanager/viewplan_Button"]').attr("disabled", !this.checked);
                });
                $('button[id="closepage"]').live('click', function () {
                    window.close();
                });
            });

        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->viewplan}: {$plan_name}</h3>
            <div id="container" style="width:100%; margin: 0px auto; display:block;">
                {$leave_details}
                {$segment_details}
                <div style=" width:100%; background-color:white ; display: block;">{$segment_expenses}</div>
                <br/>

                {$transportaion_fields}
                <form name="perform_travelmanager/viewplan_Form" id="perform_travelmanager/viewplan_Form" action="#" method="post">
                    <input type="hidden" name="planid" value="{$planid}"/>

                    {$checkbox['confirm']}
                    {$finalize_button}
                    <button id='closepage'>Close</button>
                </form>
                <div id="perform_travelmanager/viewplan_Results"></div>

        </td>
    </tr>
    {$footer}
</body>
</html>