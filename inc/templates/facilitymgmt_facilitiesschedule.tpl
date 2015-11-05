<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->list}</title>
        {$headerinc}
        <link href='{$core->settings[rootdir]}/css/fullcalendar.css' rel='stylesheet' />
        <script src='{$core->settings[rootdir]}/js/moment.min.js'></script>
        <script src="{$core->settings[rootdir]}/js/fullcalendar.js" type="text/javascript"></script>

        <script>

            $(document).ready(function() {
                var reservations ={$reserved_data};
                var reservedData = [];
                for(var data in reservations) {
                    reservedData.push(reservations[data]);
                }

                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    //   events: '{$core->settings['rootdir']}/index.php?module=facilitymgmt/facilitiesschedule&action=fetchevents'
                    events: reservedData

                });
                // $('#calendar').fullCalendar('refetchEvents')

            });

        </script>
        <style>

            body {
                margin: 40px 10px;
                padding: 0;
                font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
                font-size: 14px;
            }

            #calendar {
                max-width: 900px;
                margin: 0 auto;
            }

        </style>
    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">



            <div id='calendar'></div>


        </td>
    </tr>

    {$footer}
</body>

</html>