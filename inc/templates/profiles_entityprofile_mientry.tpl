<div class="{$classes[entrycontainer]}">
    <input type="hidden" value='{$tlidentifier[value]}' id="{$tlidentifier[id]}"></a>
    <input type="hidden" value='{$profile[next_miprofile]}' id="miprofile-{$tlidentifier[id]}"></a>
    <div class="{$classes[entrybullet]}" id="markettimeline_{$tlidentifier[id]}" style="top:{$top}px; left:{$left}%; height:{$height}px; width:{$timedepth}px;"></div>
    <div>
        <div class="timeline_column" style="width:15%;">{$data[timelineItem][displayName]}<div class="smalltext">{$data[timelineItem][addInfo]}</div></div>
        <div class="timeline_column" style="width:15%;{$depthpaddingfix}">{$entity_mrktendproducts}</div>
        <div class="timeline_column">{$data[potential]}</div>
        <div class="timeline_column">{$data[unitPrice]}</div>
        <div class="timeline_column">{$data[mktSharePerc]}</div>
        <div class="timeline_column">{$data[mktShareQty]}</div>
        {$viewdetails_icon}
    </div>
    {$children_container}
</div>