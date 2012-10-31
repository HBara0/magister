<div id="leave_{$value[lid]}" style="background-color:{$bgcolor}; height:{$boxsize[height]}px; top:{$boxsize[top]}px; width:{$boxsize[width]}px; left:{$boxsize[left]}px;" class="calendar_hourevent" title="{$lang->dragtoupdate}">
    <span class="smalltext"><span id="fromTime_{$value[lid]}">{$value[fromDate_output]}</span>{$value[toDate_output]}</span>
    <span class="smalltext">
        <a href="index.php?module=profiles/entityprofile&amp;eid={$value[cid]}" target="_blank" title="{$lang->customerprofile}">&nbsp;{$value[customername_prefix]}{$value[customername]}</a>&nbsp;{$value[completesign]}&nbsp;<a href="index.php?{$reportlink_querystring}" target="_blank"><img src="./images/icons/{$image}" border="0" alt="{$image}"/></a>
    </span>
</div>