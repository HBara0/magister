<div class="ch_entryrow {$altrow_class}">
	<div id="historybrief_{$contact_history[sschid]}" class="ch_entryheader" onClick="$('#content_{$contact_history[sschid]}').toggle();"> 
		<div style="display:inline-block; width:30%;"><a href="index.php?module=profiles/affiliateprofile&amp;affid={$contact_history[affid]}" target="_blank">{$contact_history[affiliate]}</a></div>
		<div style="display:inline-block; width:30%;"><a href="./users.php?action=profile&amp;uid={$contact_history[uid]}">{$contact_history[displayName]}</a></div>
		<div style="display:inline-block; width:30%; text-align:right;">{$contact_history[date_output]}</div>
	</div>
	<div id="content_{$contact_history[sschid]}" class="ch_entrycontent"> {$reportcommunication_filled_section} </div>
</div>