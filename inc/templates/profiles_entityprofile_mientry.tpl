<div class="timeline_entry">
    <div class="circle circle_clickable" id="markettimeline_{$mktintldata[cfpid]}" style="top:11px; left:-1%; height:{$height}px; width:{$timedepth}px;"></div>
    <div style="margin-top:10px;">
        <div class="timeline_column" style="width:15%;">{$mktintldata[entity]}<div class="smalltext">{$mktintldata[chemfunction]} - {$mktintldata[application]} - {$mktintldata[segment]}</div></div>
        <div class="timeline_column" style="width:15%;"> {$entity_mrktendproducts}</div>
        <div class="timeline_column">{$mktintldata[potential]}</div>
        <div class="timeline_column">{$mktintldata[unitPrice]}</div>
        <div class="timeline_column">{$mktintldata[mktSharePerc]}</div>
        <div class="timeline_column"> {$mktintldata[mktShareQty]}</div>
        <div class="timeline_column" style="width:2%;"><a style="cursor: pointer;" title="{$lang->viewmrktbox}" id="mktintldetails_{$mktintldata[mibdid]}_profiles/entityprofile_loadpopupbyid" rel="mktdetail_{$mktintldata[mibdid]}"><img src="{$core->settings[rootdir]}/images/icons/search.gif"/></a></div>

    </div>
    
    <div class="timeline_container" id="previoustimelinecontainer_{$mktintldata[cfpid]}">
        {$previoustimelinerows}
    </div>

</div>