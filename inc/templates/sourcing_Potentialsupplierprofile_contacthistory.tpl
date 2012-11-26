<div>
	<div style="padding:5px; cursor:pointer;" class="{$rowclass}" onClick="$('#content_{$contact_history[sschid]}').slideToggle('slow')">
            <a href="index.php?module=profiles/affiliateprofile&affid={$contact_history[affid]}" target="_blank">{$contact_history[affiliate]}</a>
            <a href="./users.php?action=profile&uid={$contact_history[uid]}">{$contact_history[displayName]}</a> 
            {$contact_history[date_output]}
    
    </div>
<div id="content_{$contact_history[sschid]}" style="display:none; padding:5px; margin-left: 15px; margin-right:15px; background-color:#CCC">
        <div style="display:inline-block;float: left;  margin-bottom: 5px; margin-right:20px;padding: 5px; width:45%;">
            <div>{$lang->chemical}: {$contact_history[chemical]}</div>
            <div>{$lang->origin}:{$contact_history[origincountry]}</div>
            <div>{$lang->application}{$contact_history[application]}</div>
        </div>
        
        <div style="display:inline-block;float:right; margin-bottom: 5px; margin-left:15px; padding: 5px; width:45%;">
            <div>{$lang->grade} {$contact_history[grade]}</div>
            <div>{$lang->market} {$contact_history[market]}</div>
            <div>{$lang->competitors} {$contact_history[competitors]}</div>
        </div>
       
        <div style="padding: 10px;"></hr>{$lang->description}{$contact_history[description]}</div>
    </div>
</div>  