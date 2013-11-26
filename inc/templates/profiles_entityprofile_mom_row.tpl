<div style="height:170px; width: 100%; overflow: visible;">
    <table class="datatable">
        <div class="subtitle">{$lang->meetingdetails}</div>
        <div style="display:block; padding:5px">
            <div style="font-weight: bold; display:inline-block;padding:5px;" class="altrow">{$lang->discussiondetails}:</div>
            <div style="word-wrap: break-word;display:inline-block;">{$minsofmeeting[meetingDetails]}</div>
        </div>
        <div style="display:block; padding:5px">
            <div style="font-weight: bold;display:inline-block;padding:5px;" class="altrow">{$lang->followup}:</div>
            <div style=" display:inline-block;">{$minsofmeeting[followup]}</div>
        </div>
        <div style="display:block; padding:5px">
            <div style="display:inline-block;padding:5px;"><b>{$lang->fromdate}</b></div>
            <div style="display:inline-block;">{$meeting[fromDate_output]}{$meeting[fromTime_output]}</div>
        </div>
        <div style="padding:5px; display:block;">
            <div style="display:inline-block;padding:5px;"><b>{$lang->todate}</b></div>
            <div style="display:inline-block;">{$meeting[toDate_output]}{$meeting[toTime_output]}</div>
        </div>
    </table>
</div>
 <hr>
        