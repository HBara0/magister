<div class="timelinedetails"> 
    <div class="circle" id="markettimeline_{$mktintldata[cfpid]}" style="top:30%;left:-25px; height:{$height}px; width:{$timedepth}px;"> </div>
    <div style="margin-bottom:30px;">
    <div class="timelineitem">{$mktintldata[product]} <div>{$mktintldata[chemfunction]}</span>-<span>{$mktintldata[application]}</span> - <span>{$mktintldata[segment]}</div></div>  
    <div class="timelineitem" >{$mktintldata[potential]}</div>
    <div class="timelineitem"> {$mktintldata[mktShareQty]}</div>  
    <div class="timelineitem"><a  style="cursor: pointer;" title="{$lang->viewmrktbox}" id="mktintldetails_{$mktintldata[mibdid]}_profiles/entityprofile_loadpopupbyid" rel="mktdetail_{$mktintldata[mibdid]}"><img  src="{$core->settings[rootdir]}/images/icons/search.gif"/></a></div>
 
    </div>
    <div class="previoustimeline" id="previoustimelinecontainer_{$mktintldata[cfpid]}">
        {$previoustimelinerows}
     
    </div>
</div>

