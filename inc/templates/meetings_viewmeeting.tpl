<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$meeting[title]}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$meeting[title]}</h3>
            <div style="padding: 5px; margin-bottom:20px; word-wrap: break-word;">
                <div class="subtitle">{$lang->meetingdetails}</div>
                <div style='padding: 2px; vertical-align: top; display:inline-block; width: 20%; font-weight: bold;'>{$lang->description}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[description]}</div>
                <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->fromdate}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[fromDate_output]} {$meeting[fromTime_output]}</div>
                <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->todate}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[toDate_output]} {$meeting[toTime_output]}</div>
                <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->location}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[location]}</div>
                <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->attendees}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[attendees_output]}</div>
                <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->createdby}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[createdby]}</div>
            </div>
            <div style="padding-left: 5px; width: 100%;">
                {$meetings_viewmeeting_mom}
            </div>
            <div class="thead">{$lang->attachements}</div>
            <div style='padding: 2px; display:inline-block; width: 49%; font-weight: bold;'>{$lang->filename}</div>
            <div style='padding: 2px; display:inline-block; width: 49%;; font-weight: bold;'>{$lang->filesize}</div>
            <div>{$meeting_viewmeeting_attach}</div>
        </td>
    </tr>
</body>
</html>