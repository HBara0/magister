<tr>
    <td colspan="2">
        <table class="datatable">
            <thead> 
                <tr><th  style="width:50%;padding:8px; margin-top: 10px;"> <div class="ui-state-highlight ui-corner-all" style="padding-left:5px; margin-bottom:10px;"> <a href="index.php?module=meetings/viewmeeting&referrer=list&mtid={$meeting[mtid]}" target="_blank"> {$meeting[title]}</a></div></th></tr>
</thead>
</table>
<div style="height:125px; width: 100%; overflow: visible; border-radius:5px; border: solid 1px  #ffffff;">
    <table class="datatable">
        <div style="display:block; padding:8px;">
            <div style="display:inline-block;padding:0px;"><b>{$lang->fromdate}</b></div>
            <div style="display:inline-block;">{$meeting[fromDate_output]}{$meeting[fromTime_output]}</div>
        </div>
        <div style="padding:8px; display:block;">
            <div style="display:inline-block;padding:0px;"><b>{$lang->todate}</b></div>
            <div style="display:inline-block;">{$meeting[toDate_output]}{$meeting[toTime_output]}</div>
        </div>
        <div style="padding:8px; display:block;" class="smalltext">
            <div style="display:inline-block;padding:0px;"><b>{$lang->createdby}</b></div>
            <div style="display:inline-block;"><a href="{$DOMAIN}users.php?action=profile&uid={$meeting[createdBy]}" target="_blank">{$meeting[businesMgr]}</a>  </div>
        </div>

    </table>
</div>
<hr>  
</td>
</tr>
