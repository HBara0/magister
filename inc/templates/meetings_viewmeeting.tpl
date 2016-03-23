<h1>{$meeting[title]}</h1>
<div style="padding: 5px; margin-bottom:20px; word-wrap: break-word;">
    <div>
        <div class="subtitle" style="display:inline-block;">{$lang->meetingdetails}</div>
        <div style="display:inline-block;margin-left:10px;">{$share_meeting}</div>
    </div>
    <div style='padding: 2px; vertical-align: top; display:inline-block; width: 20%; font-weight: bold;'>{$lang->description}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[description]}</div>
    <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->fromdate}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[fromDate_output]} {$meeting[fromTime_output]}</div>
    <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->todate}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[toDate_output]} {$meeting[toTime_output]}</div>
    <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->location}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[locationoutput]}</div>
    <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->attendees}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[attendees_output]}</div>
    <div style='padding: 2px; display:inline-block; width: 20%; font-weight: bold;'>{$lang->createdby}</div><div style='padding: 2px; display:inline-block; width: 75%;'>{$meeting[createdby]}</div>
</div>
{$meeting_attachmentssection}
<div style="padding-left: 5px; width: 100%;">
    {$meetings_viewmeeting_mom}
</div>