<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->list}</title>
        {$headerinc}
        <link href='{$core->settings[rootdir]}/css/fullcalendar.min.css' rel='stylesheet' />
        <script src='{$core->settings[rootdir]}/js/moment.min.js'></script>
        <script src="{$core->settings[rootdir]}/js/fullcalendar.min.js" type="text/javascript"></script>

        <script>
            $(document).ready(function () {
                //   var reservations ={$reserved_data};
                //    var reservedData = [];
                //    for(var data in reservations) {
                //        reservedData.push(reservations[data]);
                //   }

                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    editable: false,
                    eventLimit: true,
                    // events: reservedData
                    events: {
                        type: 'POST',
                        url: '{$core->settings['rootdir']}/index.php?module=facilitymgmt/facilitiesschedule&action=fetchevents',
                        error: function () {
                            $('#script-warning').show();
                        }

                    },
                    loading: function (bool) {
                        $('#loading').toggle(bool);
                    }

                });

                setInterval(function () {
                    $('#calendar').fullCalendar('refetchEvents')
                }, 30000);

            });

        </script>
        <style>
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